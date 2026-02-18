<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Phone;
use App\Models\SpecBody;
use App\Models\SpecPlatform;
use App\Models\SpecCamera;
use App\Models\SpecConnectivity;
use App\Models\SpecBattery;
use App\Models\Benchmark;
use Illuminate\Support\Str;

class ImportPhone extends Command
{
    protected $signature = 'phone:import {phone_name : Name of the phone to import} 
                            {--skip-image : Skip image download}
                            {--skip-shopping : Skip shopping links}
                            {--force : Overwrite existing phone data}
                            {--json= : Path to JSON file instead of fetching}';

    protected $description = 'Import phone data from Python aggregator script and populate all database tables';

    protected $data;
    protected $phone;

    public function handle()
    {
        $phoneName = $this->argument('phone_name');
        $jsonFile = $this->option('json');

        $this->info("=== Importing Phone: {$phoneName} ===");
        $this->newLine();

        // Step 1: Get data
        if ($jsonFile && file_exists($jsonFile)) {
            $this->info('Loading data from JSON file...');
            $this->data = json_decode(file_get_contents($jsonFile), true);
        } else {
            $this->info('Fetching data from Python aggregator...');
            $this->data = $this->fetchPhoneData($phoneName);
        }

        if (!$this->data) {
            $this->error('Failed to fetch phone data');
            return 1;
        }

        // Step 2: Check if phone exists
        $existingPhone = Phone::where('name', $phoneName)->first();
        if ($existingPhone && !$this->option('force')) {
            if (!$this->confirm("Phone '{$phoneName}' already exists. Overwrite?")) {
                $this->info('Import cancelled.');
                return 0;
            }
        }

        // Step 3: Create/update phone record
        $this->info('Creating phone record...');
        $this->createPhoneRecord($phoneName, $existingPhone);

        // Step 4: Create/update spec records
        $this->info('Creating specification records...');
        $this->createSpecRecords();

        // Step 5: Create/update benchmark records
        $this->info('Creating benchmark records...');
        $this->createBenchmarkRecords();

        // Step 6: Calculate scores
        $this->info('Calculating scores...');
        $this->phone->load(['benchmarks', 'body', 'platform', 'camera', 'connectivity', 'battery']);
        $this->phone->updateScores();

        // Step 7: Recalculate all rankings
        $this->info('Recalculating all phone rankings...');
        $this->call('phone:recalculate-scores');
        $this->call('cache:clear');

        // Summary
        $this->phone->refresh();
        $this->newLine();
        $this->info('✓ Phone imported successfully!');
        $this->table(['Metric', 'Value'], [
            ['ID', $this->phone->id],
            ['Name', $this->phone->name],
            ['Brand', $this->phone->brand],
            ['Price', '₹' . number_format($this->phone->price, 0)],
            ['FPI', $this->phone->overall_score],
            ['UEPS', $this->phone->ueps_score],
            ['GPX', $this->phone->gpx_score],
            ['CMS', $this->phone->cms_score],
            ['Value', $this->phone->value_score],
            ['Endurance', $this->phone->endurance_score],
            ['Expert', $this->phone->expert_score],
        ]);

        return 0;
    }

    // ─── Data Fetching ───────────────────────────────────────────────

    protected function fetchPhoneData(string $phoneName): ?array
    {
        $pythonScript = base_path('python/phone_data_aggregator.py');
        $venvPython = base_path('.venv/bin/python');

        if (!file_exists($venvPython)) {
            $this->error("Python venv not found at: {$venvPython}");
            $this->line("Run: python -m venv .venv && source .venv/bin/activate && pip install -r python/requirements.txt");
            return null;
        }

        // Build skip options
        $skipSteps = [];
        if ($this->option('skip-image')) {
            $skipSteps[] = 'image';
        }
        if ($this->option('skip-shopping')) {
            $skipSteps[] = 'shopping';
        }
        $skipArg = !empty($skipSteps) ? '--skip=' . implode(',', $skipSteps) : '';

        // Use temp file for output to avoid stdout parsing issues
        $tempFile = tempnam(sys_get_temp_dir(), 'phone_import_') . '.json';
        
        $command = "{$venvPython} {$pythonScript} " . escapeshellarg($phoneName) . " --output " . escapeshellarg($tempFile) . " {$skipArg} 2>&1";
        $this->line("Running: {$command}");

        // Use proc_open to stream output in real-time
        $process = proc_open($command, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes, base_path());

        if (!is_resource($process)) {
            $this->error('Failed to start Python script');
            return null;
        }

        fclose($pipes[0]);

        // Stream output
        while (!feof($pipes[1])) {
            $line = fgets($pipes[1]);
            if ($line !== false) {
                $line = trim($line);
                if ($line) {
                    $this->line("  [py] {$line}");
                }
            }
        }
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);
        
        if (!file_exists($tempFile)) {
            $this->error('Python script did not produce output file');
            return null;
        }

        $output = file_get_contents($tempFile);
        unlink($tempFile);
        
        if (!$output) {
            $this->error('Empty output file from Python script');
            return null;
        }

        $data = json_decode($output, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Failed to parse JSON: ' . json_last_error_msg());
            return null;
        }

        $this->info('✓ Data fetched successfully');
        $summary = $data['summary'] ?? [];
        $this->line("  Steps: " . ($summary['successful_steps'] ?? '?') . "/" . ($summary['total_steps'] ?? '?') . " successful");
        
        if (!empty($data['errors'])) {
            $this->warn('  Warnings: ' . implode(', ', $data['errors']));
        }

        return $data;
    }

    // ─── Phone Record ────────────────────────────────────────────────

    protected function createPhoneRecord(string $phoneName, ?Phone $existingPhone): void
    {
        $gsmarena = $this->data['gsmarena']['data'] ?? $this->data['gsmarena'] ?? null;
        $specs = $gsmarena['specifications'] ?? $gsmarena ?? [];
        $shopping = $this->data['shopping_links']['data'] ?? $this->data['shopping_links'] ?? null;
        $image = $this->data['image']['data'] ?? $this->data['image'] ?? null;

        // Parse price
        $price = $this->parsePrice($specs['Misc']['Price'] ?? null);

        // Parse dates
        $releaseDate = $this->parseDate($specs['Launch']['Status'] ?? null);
        $announcedDate = $this->parseDate($specs['Launch']['Announced'] ?? null);

        // Parse brand
        $brand = $this->parseBrand($phoneName);

        // Shopping links
        $amazonUrl = null;
        $amazonPrice = null;
        $flipkartUrl = null;
        $flipkartPrice = null;

        if ($shopping) {
            $amazonItems = $shopping['amazon'] ?? [];
            if (!empty($amazonItems[0])) {
                $amazonUrl = $amazonItems[0]['link'] ?? null;
                $amazonPrice = $this->parsePrice($amazonItems[0]['price'] ?? null);
            }
            $flipkartItems = $shopping['flipkart'] ?? [];
            if (!empty($flipkartItems[0])) {
                $flipkartUrl = $flipkartItems[0]['link'] ?? null;
                $flipkartPrice = $this->parsePrice($flipkartItems[0]['price'] ?? null);
            }
        }

        // Image path
        $imageUrl = null;
        if ($image && !empty($image['image_path'])) {
            $imgPath = $image['image_path'];
            if (str_starts_with($imgPath, 'storage/')) {
                $imageUrl = '/' . $imgPath;
            } elseif (str_starts_with($imgPath, '/storage')) {
                $imageUrl = $imgPath;
            } else {
                $imageUrl = '/storage/phones/' . basename($imgPath);
            }
        } elseif ($gsmarena && !empty($gsmarena['image_url'])) {
            $imageUrl = $gsmarena['image_url'];
        }

        $phoneData = [
            'name' => $phoneName,
            'brand' => $brand,
            'model_variant' => $specs['Misc']['Models'] ?? null,
            'price' => $price ?? $amazonPrice ?? $flipkartPrice ?? 0,
            'overall_score' => 0,
            'release_date' => $releaseDate,
            'announced_date' => $announcedDate,
            'image_url' => $imageUrl,
            'amazon_url' => $amazonUrl,
            'amazon_price' => $amazonPrice,
            'flipkart_url' => $flipkartUrl,
            'flipkart_price' => $flipkartPrice,
        ];

        if ($existingPhone) {
            $existingPhone->update($phoneData);
            $this->phone = $existingPhone;
        } else {
            $this->phone = Phone::create($phoneData);
        }

        $this->line("  Phone ID: {$this->phone->id}, Price: ₹" . number_format($this->phone->price, 0));
    }

    // ─── Spec Records ────────────────────────────────────────────────

    protected function createSpecRecords(): void
    {
        $gsmarena = $this->data['gsmarena']['data'] ?? $this->data['gsmarena'] ?? [];
        $specs = $gsmarena['specifications'] ?? $gsmarena ?? [];

        $this->createBodySpecs($specs);
        $this->createPlatformSpecs($specs);
        $this->createCameraSpecs($specs);
        $this->createConnectivitySpecs($specs);
        $this->createBatterySpecs($specs);
    }

    // ─── Body Specs ──────────────────────────────────────────────────

    protected function createBodySpecs(array $specs): void
    {
        $body = $specs['Body'] ?? [];
        $display = $specs['Display'] ?? [];
        $misc = $specs['Misc'] ?? [];

        // Dimensions
        $dimensions = $body['Dimensions'] ?? null;

        // Weight
        $weight = $body['Weight'] ?? null;

        // Build
        $build = $body['Build'] ?? null;

        // SIM - handle array
        $sim = $body['SIM'] ?? null;
        if (is_array($sim)) {
            $sim = implode("\n", $sim);
        }

        // IP rating - parse from SIM or Build
        $ipRating = $this->parseIpRating($sim) ?? $this->parseIpRating($build);

        // Colors
        $colors = $misc['Colors'] ?? null;

        // Display Type
        $displayType = $display['Type'] ?? null;

        // Display Size (contains area and STB ratio)
        $sizeStr = $display['Size'] ?? null;
        $displaySize = $sizeStr;
        $screenArea = $this->parseScreenArea($sizeStr);
        $screenToBody = $this->parseScreenToBody($sizeStr);

        // Display Resolution (contains PPI and aspect ratio)
        $resStr = $display['Resolution'] ?? null;
        $pixelDensity = $this->parsePixelDensity($resStr);
        $aspectRatio = $this->parseAspectRatio($resStr);

        // Display Brightness - parse from Type field
        $displayBrightness = $this->parseDisplayBrightness($displayType);

        // Measured brightness from Our Tests
        $ourTests = $specs['Our Tests'] ?? [];
        $measuredBrightness = $ourTests['Display'] ?? null;

        // Display protection
        $protection = $display['Protection'] ?? null;
        if (is_array($protection)) {
            $protection = implode(', ', $protection);
        }

        // Glass protection level
        $glassProtectionLevel = $this->parseGlassProtectionLevel($protection);
        $screenGlass = $protection;

        // PWM
        $pwmDimming = $this->parsePwmDimming($displayType);

        // Touch sampling rate - look in display features
        $touchSamplingRate = $this->parseTouchSamplingRate($displayType);

        // Cooling type - determine by brand/chipset heuristic
        $brand = strtolower($this->phone->brand);
        $coolingType = $this->determineCoolingType($brand, $this->phone->name);

        // Display features - combine useful display info
        $displayFeatures = $displayType;

        SpecBody::updateOrCreate(
            ['phone_id' => $this->phone->id],
            [
                'dimensions' => $dimensions,
                'weight' => $weight,
                'build_material' => $build,
                'cooling_type' => $coolingType,
                'sim' => $sim,
                'ip_rating' => $ipRating,
                'colors' => $colors,
                'display_type' => $displayType,
                'display_size' => $displaySize,
                'display_resolution' => $resStr,
                'display_brightness' => $displayBrightness,
                'measured_display_brightness' => $measuredBrightness,
                'display_protection' => $protection,
                'pwm_dimming' => $pwmDimming,
                'screen_to_body_ratio' => $screenToBody,
                'pixel_density' => $pixelDensity,
                'aspect_ratio' => $aspectRatio,
                'screen_area' => $screenArea,
                'touch_sampling_rate' => $touchSamplingRate,
                'screen_glass' => $screenGlass,
                'glass_protection_level' => $glassProtectionLevel,
                'display_features' => $displayFeatures,
            ]
        );
        $this->line('  ✓ Body specs');
    }

    // ─── Platform Specs ──────────────────────────────────────────────

    protected function createPlatformSpecs(array $specs): void
    {
        $platform = $specs['Platform'] ?? [];
        $memory = $specs['Memory'] ?? [];

        // OS parsing - split "Android 15, OxygenOS 15" into os + os_details
        $osRaw = $platform['OS'] ?? null;
        $os = $osRaw;
        $osDetails = null;
        if ($osRaw && str_contains($osRaw, ',')) {
            $parts = explode(',', $osRaw, 2);
            $os = trim($parts[0]) . ',' . trim($parts[1]);
            $osDetails = trim($parts[1]);
        }

        // Memory Internal field: can be string or array ["256GB 12GB RAM, 512GB 12GB RAM", "UFS 4.0"]
        $internalRaw = $memory['Internal'] ?? null;
        if (is_array($internalRaw)) {
            $internalRaw = implode(', ', $internalRaw);
        }

        // Parse RAM variants — look for all "XGB RAM" patterns
        $ram = $this->parseRamFromInternal($internalRaw);
        
        // Parse storage variants
        $storage = $this->parseStorageFromInternal($internalRaw);

        // Storage type
        $storageType = $this->parseStorageType($internalRaw);

        // GPU
        $gpu = $platform['GPU'] ?? '';

        // Brand-based heuristics
        $brand = strtolower($this->phone->brand);
        $bootloaderUnlockable = in_array($brand, ['oneplus', 'xiaomi', 'poco', 'nothing', 'google', 'motorola', 'realme']);
        $osOpenness = $this->determineOsOpenness($brand, $os);
        $customRomSupport = $this->determineCustomRomSupport($brand);
        $turnipSupport = $this->determineTurnipSupport($gpu);
        $turnipSupportLevel = $turnipSupport ? $this->determineTurnipLevel($gpu) : null;
        $gpuEmulationTier = $this->determineGpuEmulationTier($gpu);
        $aospAestheticsScore = $this->determineAospAesthetics($brand, $os);

        SpecPlatform::updateOrCreate(
            ['phone_id' => $this->phone->id],
            [
                'os' => $os,
                'os_details' => $osDetails,
                'chipset' => $platform['Chipset'] ?? null,
                'cpu' => $platform['CPU'] ?? null,
                'gpu' => $gpu,
                'memory_card_slot' => $memory['Card slot'] ?? 'No',
                'internal_storage' => $storage ?? $internalRaw,
                'ram' => $ram ?? $internalRaw,
                'storage_type' => $storageType,
                'bootloader_unlockable' => $bootloaderUnlockable,
                'turnip_support' => $turnipSupport,
                'turnip_support_level' => $turnipSupportLevel,
                'os_openness' => $osOpenness,
                'gpu_emulation_tier' => $gpuEmulationTier,
                'aosp_aesthetics_score' => $aospAestheticsScore,
                'custom_rom_support' => $customRomSupport,
            ]
        );
        $this->line('  ✓ Platform specs');
    }

    // ─── Camera Specs ────────────────────────────────────────────────

    protected function createCameraSpecs(array $specs): void
    {
        $mainCamera = $specs['Main Camera'] ?? [];
        $selfieCamera = $specs['Selfie camera'] ?? [];

        // Get main camera specs — can be under Triple, Quad, Dual, Single, or other keys
        $mainSpecs = null;
        foreach (['Quad', 'Triple', 'Dual', 'Single', 'Main'] as $key) {
            if (!empty($mainCamera[$key])) {
                $mainSpecs = $mainCamera[$key];
                break;
            }
        }
        // GSMArena sometimes returns this as a single string with specs separated by spaces
        if (is_array($mainSpecs)) {
            $mainSpecs = implode("\n", $mainSpecs);
        }
        // Split concatenated camera lines: "50 MP, f/1.8, ... OIS 50 MP, f/2.0, ..."
        // by looking for " XX MP" pattern mid-string
        if ($mainSpecs && !str_contains($mainSpecs, "\n")) {
            $mainSpecs = preg_replace('/\s+(\d+ MP,)/', "\n$1", $mainSpecs);
        }

        // Features
        $features = $mainCamera['Features'] ?? null;
        if (is_array($features)) {
            $features = implode(', ', $features);
        }

        // Video
        $video = $mainCamera['Video'] ?? null;
        if (is_array($video)) {
            $video = implode(', ', $video);
        }

        // Selfie camera specs
        $selfieSpecs = null;
        foreach (['Dual', 'Single', 'Main'] as $key) {
            if (!empty($selfieCamera[$key])) {
                $selfieSpecs = $selfieCamera[$key];
                break;
            }
        }
        if (is_array($selfieSpecs)) {
            $selfieSpecs = implode("\n", $selfieSpecs);
        }

        // Selfie video
        $selfieVideo = $selfieCamera['Video'] ?? null;
        if (is_array($selfieVideo)) {
            $selfieVideo = implode(', ', $selfieVideo);
        }

        // Selfie features
        $selfieFeatures = $selfieCamera['Features'] ?? null;
        if (is_array($selfieFeatures)) {
            $selfieFeatures = implode(', ', $selfieFeatures);
        }

        // Parse granular camera fields from main specs
        $ois = $this->parseOis($mainSpecs);
        $ultrawide = $this->parseUltrawide($mainSpecs);
        $telephoto = $this->parseTelephoto($mainSpecs);
        $sensors = $this->parseCameraSensors($mainSpecs);
        $apertures = $this->parseCameraApertures($mainSpecs);
        $focalLengths = $this->parseCameraFocalLengths($mainSpecs);
        $pdaf = $this->parsePdaf($mainSpecs);
        $zoom = $this->parseZoom($mainSpecs);

        // Selfie granular
        $selfieAperture = $this->parseSelfieAperture($selfieSpecs);
        $selfieSensor = $this->parseSelfieSensor($selfieSpecs);
        $selfieAutofocus = $selfieSpecs ? (
            stripos($selfieSpecs, 'AF') !== false ||
            stripos($selfieSpecs, 'PDAF') !== false ||
            stripos($selfieSpecs, 'autofocus') !== false
        ) : false;

        SpecCamera::updateOrCreate(
            ['phone_id' => $this->phone->id],
            [
                'main_camera_specs' => $mainSpecs,
                'main_camera_sensors' => $sensors,
                'main_camera_apertures' => $apertures,
                'main_camera_focal_lengths' => $focalLengths,
                'main_camera_features' => $features,
                'main_camera_ois' => $ois,
                'main_camera_zoom' => $zoom,
                'main_camera_pdaf' => $pdaf,
                'main_video_capabilities' => $video,
                'video_features' => $video,
                'ultrawide_camera_specs' => $ultrawide,
                'telephoto_camera_specs' => $telephoto,
                'selfie_camera_specs' => $selfieSpecs,
                'selfie_camera_sensor' => $selfieSensor,
                'selfie_camera_aperture' => $selfieAperture,
                'selfie_camera_features' => $selfieFeatures,
                'selfie_camera_autofocus' => $selfieAutofocus,
                'selfie_video_capabilities' => $selfieVideo,
                'selfie_video_features' => $selfieVideo,
            ]
        );
        $this->line('  ✓ Camera specs');
    }

    // ─── Connectivity Specs ──────────────────────────────────────────

    protected function createConnectivitySpecs(array $specs): void
    {
        $comms = $specs['Comms'] ?? [];
        $features = $specs['Features'] ?? [];
        $sound = $specs['Sound'] ?? [];
        $network = $specs['Network'] ?? [];
        $misc = $specs['Misc'] ?? [];

        // Helper to safely convert any value to string
        $s = fn($v) => is_array($v) ? implode(', ', $v) : ($v ? (string)$v : null);

        // 3.5mm jack
        $jack = $s($sound['3.5mm jack'] ?? 'No');
        $hasJack = !str_contains(strtolower($jack), 'no');

        // NFC
        $nfc = $s($comms['NFC'] ?? 'No');
        $nfcValue = str_contains(strtolower($nfc), 'yes') ? 'Yes' : $nfc;

        // Infrared
        $infrared = $s($comms['Infrared port'] ?? 'No');
        $infraredValue = str_contains(strtolower($infrared), 'yes') ? 'Yes' : $infrared;

        // Network bands
        $networkBands = $s($network['Technology'] ?? null);

        // Positioning
        $positioning = $s($comms['Positioning'] ?? null);

        // USB
        $usb = $s($comms['USB'] ?? null);

        // WLAN
        $wlan = $s($comms['WLAN'] ?? null);
        $wifiBands = $this->parseWifiBands($wlan);

        // Loudspeaker - GSMArena uses trailing space on key
        $loudspeaker = $s($sound['Loudspeaker '] ?? $sound['Loudspeaker'] ?? null);

        // Audio quality from tests
        $audioQuality = $s($sound['Quality'] ?? null);
        
        // Loudness 
        $loudnessTest = $s($sound['Loudness'] ?? null);

        // SAR value
        $sarValue = $s($misc['SAR'] ?? null);

        // Sensors
        $sensors = $s($features['Sensors'] ?? null);

        // Bluetooth
        $bluetooth = $s($comms['Bluetooth'] ?? null);

        // Radio
        $radio = $s($comms['Radio'] ?? 'No');

        SpecConnectivity::updateOrCreate(
            ['phone_id' => $this->phone->id],
            [
                'network_bands' => $networkBands,
                'wlan' => $wlan,
                'wifi_bands' => $wifiBands,
                'bluetooth' => $bluetooth,
                'positioning' => $positioning,
                'positioning_details' => $positioning,
                'nfc' => $nfcValue,
                'infrared' => $infraredValue,
                'radio' => $radio,
                'usb' => $usb,
                'usb_details' => $usb,
                'sensors' => $sensors,
                'loudspeaker' => $loudspeaker,
                'audio_quality' => $audioQuality,
                'loudness_test_result' => $loudnessTest,
                'jack_3_5mm' => $jack,
                'has_3_5mm_jack' => $hasJack,
                'sar_value' => $sarValue,
            ]
        );
        $this->line('  ✓ Connectivity specs');
    }

    // ─── Battery Specs ───────────────────────────────────────────────

    protected function createBatterySpecs(array $specs): void
    {
        $battery = $specs['Battery'] ?? [];

        // Battery type - can be array
        $batteryType = $battery['Type'] ?? null;
        if (is_array($batteryType)) {
            $batteryType = implode(', ', $batteryType);
        }

        // Charging - can be a string or array
        $charging = $battery['Charging'] ?? null;
        if (is_array($charging)) {
            $charging = implode(', ', $charging);
        }

        $wiredCharging = $this->parseWiredCharging($charging);
        $wirelessCharging = $this->parseWirelessCharging($charging);

        // Reverse charging
        $hasReverseWired = str_contains(strtolower($charging ?? ''), 'reverse wired') ||
                           str_contains(strtolower($charging ?? ''), 'reverse');
        $hasReverseWireless = str_contains(strtolower($charging ?? ''), 'reverse wireless');

        // Charging reverse text
        $chargingReverse = null;
        if ($hasReverseWireless) {
            $chargingReverse = 'Reverse wired + wireless';
        } elseif ($hasReverseWired) {
            $chargingReverse = 'Reverse wired';
        }

        SpecBattery::updateOrCreate(
            ['phone_id' => $this->phone->id],
            [
                'battery_type' => $batteryType,
                'charging_wired' => $wiredCharging,
                'charging_wireless' => $wirelessCharging,
                'charging_specs_detailed' => $charging,
                'charging_reverse' => $chargingReverse,
                'reverse_wired' => $hasReverseWired ? 'Yes' : null,
                'reverse_wireless' => $hasReverseWireless ? 'Yes' : null,
            ]
        );
        $this->line('  ✓ Battery specs');
    }

    // ─── Benchmark Records ───────────────────────────────────────────

    protected function createBenchmarkRecords(): void
    {
        $gsmarena = $this->data['gsmarena']['data'] ?? $this->data['gsmarena'] ?? [];
        $specs = $gsmarena['specifications'] ?? $gsmarena ?? [];
        $ourTests = $specs['Our Tests'] ?? [];
        $euLabel = $specs['EU LABEL'] ?? [];
        
        $nanoreview = $this->data['nanoreview_benchmarks']['scores'] ?? 
                      $this->data['nanoreview_benchmarks']['data']['scores'] ?? [];
        $gpu = $this->data['gpu_benchmarks']['gpu_benchmark'] ?? 
               $this->data['gpu_benchmarks']['data']['gpu_benchmark'] ?? [];
        $camera = $this->data['camera_benchmarks']['camera_benchmark'] ?? 
                  $this->data['camera_benchmarks']['data']['camera_benchmark'] ?? [];

        // AnTuTu
        $antutuScore = null;
        $antutuV10 = null;

        // Try Our Tests first
        $antutuStr = $ourTests['Performance']['AnTuTu'] ?? $ourTests['Performance']['Antutu'] ?? null;
        if ($antutuStr) {
            if (preg_match('/(\d+)\s*\(v11\)/i', $antutuStr, $m)) {
                $antutuScore = (int)$m[1];
            }
            if (preg_match('/(\d+)\s*\(v10\)/i', $antutuStr, $m)) {
                $antutuV10 = (int)$m[1];
            }
            if (!$antutuScore && preg_match('/(\d+)/', $antutuStr, $m)) {
                $antutuScore = (int)$m[1];
            }
        }

        // Override with nanoreview (more reliable)
        if (!empty($nanoreview['antutu_v11'])) $antutuScore = (int)$nanoreview['antutu_v11'];
        if (!empty($nanoreview['antutu_v10'])) $antutuV10 = (int)$nanoreview['antutu_v10'];

        // Geekbench
        $geekbenchSingle = null;
        $geekbenchMulti = null;

        $gbStr = $ourTests['Performance']['GeekBench'] ?? $ourTests['Performance']['Geekbench'] ?? null;
        if ($gbStr) {
            // May contain "single: 3200 / multi: 10200" or just a number
            if (preg_match('/single[:\s]*(\d+)/i', $gbStr, $m)) {
                $geekbenchSingle = (int)$m[1];
            }
            if (preg_match('/multi[:\s]*(\d+)/i', $gbStr, $m)) {
                $geekbenchMulti = (int)$m[1];
            }
        }

        // Override with nanoreview (top-level keys or individual_scores arrays)
        if (!empty($nanoreview['geekbench_6_single'])) $geekbenchSingle = (int)$nanoreview['geekbench_6_single'];
        if (!empty($nanoreview['geekbench_6_multi'])) $geekbenchMulti = (int)$nanoreview['geekbench_6_multi'];

        // Also check individual_scores from nanoreview (contains arrays of values)
        $nanoIndividual = $this->data['nanoreview_benchmarks']['individual_scores'] ??
                          $this->data['nanoreview_benchmarks']['data']['individual_scores'] ?? [];
        if (!$geekbenchSingle && !empty($nanoIndividual['geekbench_6_single_values'])) {
            $geekbenchSingle = (int)max($nanoIndividual['geekbench_6_single_values']);
        }
        if (!$geekbenchMulti && !empty($nanoIndividual['geekbench_6_multi_values'])) {
            $geekbenchMulti = (int)max($nanoIndividual['geekbench_6_multi_values']);
        }

        // 3DMark
        $dmarkScore = null;
        $dmarkStr = $ourTests['Performance']['3DMark'] ?? null;
        if ($dmarkStr && preg_match('/(\d+)/', $dmarkStr, $m)) {
            $dmarkScore = (int)$m[1];
        }
        
        // Override with GPU benchmark data
        if (!empty($gpu['wildlife_extreme_peak'])) {
            $dmarkScore = (int)$gpu['wildlife_extreme_peak'];
        }

        // Stability
        $stability = null;
        if (!empty($gpu['wildlife_extreme_stability'])) {
            $stability = (int)round($gpu['wildlife_extreme_stability']);
        }

        // Battery endurance
        $enduranceHours = null;
        $activeUseScore = null;
        $chargeTimeTest = null;

        // EU Label battery
        $euBattery = $euLabel['Battery'] ?? null;
        if ($euBattery && preg_match('/(\d+):(\d+)h\s*endurance/i', $euBattery, $m)) {
            $enduranceHours = (float)$m[1] + ((float)$m[2] / 60);
        }

        // Active use score
        if (!empty($ourTests['Battery'])) {
            $activeUseScore = is_array($ourTests['Battery']) ? implode(', ', $ourTests['Battery']) : $ourTests['Battery'];
        }

        // Charge time from battery charging spec
        $chargingSpec = $specs['Battery']['Charging'] ?? null;
        if (is_array($chargingSpec)) $chargingSpec = implode(', ', $chargingSpec);
        if ($chargingSpec && preg_match('/(\d+)\s*min/i', $chargingSpec, $m)) {
            $chargeTimeTest = $m[0];
        }

        // Camera benchmarks
        $dxomark = $camera['dxomark'] ?? null;
        $phonearena = $camera['phonearena'] ?? null;
        $otherBenchmark = $camera['mobile91'] ?? $camera['gsmarena'] ?? null;
        if ($otherBenchmark && $otherBenchmark <= 10) {
            $otherBenchmark = (int)($otherBenchmark * 10); // Convert 9.0/10 to 90
        }

        Benchmark::updateOrCreate(
            ['phone_id' => $this->phone->id],
            [
                'antutu_score' => $antutuScore,
                'antutu_v10_score' => $antutuV10,
                'geekbench_single' => $geekbenchSingle,
                'geekbench_multi' => $geekbenchMulti,
                'dmark_wild_life_extreme' => $dmarkScore,
                'dmark_wild_life_stress_stability' => $stability,
                'dmark_test_type' => 'Wild Life Extreme',
                'battery_endurance_hours' => $enduranceHours,
                'battery_active_use_score' => $activeUseScore,
                'charge_time_test' => $chargeTimeTest,
                'battery_charge_time_100' => $chargeTimeTest,
                'dxomark_score' => $dxomark ? (int)$dxomark : null,
                'phonearena_camera_score' => $phonearena ? (int)$phonearena : null,
                'other_benchmark_score' => $otherBenchmark ? (int)$otherBenchmark : null,
                'repairability_score' => $euLabel['Repairability'] ?? null,
                'energy_label' => $euLabel['Energy'] ?? null,
                'free_fall_rating' => $euLabel['Free fall'] ?? null,
            ]
        );

        $this->line("  ✓ Benchmarks (AnTuTu: " . ($antutuScore ?? 'N/A') .
            ", GB: " . ($geekbenchSingle ?? '?') . "/" . ($geekbenchMulti ?? '?') .
            ", 3DMark: " . ($dmarkScore ?? 'N/A') .
            ", Stability: " . ($stability ?? 'N/A') . "%)");
    }

    // ═══════════════════════════════════════════════════════════════
    // PARSER HELPERS
    // ═══════════════════════════════════════════════════════════════

    protected function parsePrice(?string $priceStr): ?float
    {
        if (!$priceStr) return null;
        if (preg_match('/₹\s*([\d,]+)/u', $priceStr, $m)) {
            return (float)str_replace(',', '', $m[1]);
        }
        if (preg_match('/€\s*([\d,]+)/u', $priceStr, $m)) {
            return (float)str_replace(',', '', $m[1]) * 90;
        }
        if (preg_match('/\$\s*([\d,]+)/u', $priceStr, $m)) {
            return (float)str_replace(',', '', $m[1]) * 83;
        }
        if (preg_match('/([\d,]+)\s*(?:INR|Rs)/i', $priceStr, $m)) {
            return (float)str_replace(',', '', $m[1]);
        }
        return null;
    }

    protected function parseDate(?string $dateStr): ?string
    {
        if (!$dateStr) return null;
        // "Released 2025, January 20" or "2025, January"
        if (preg_match('/(\d{4}),?\s*(\w+)\s*(\d+)?/', $dateStr, $m)) {
            $year = $m[1];
            $month = $m[2];
            $day = $m[3] ?? '01';
            $monthNum = date('m', strtotime("{$month} 1"));
            return "{$year}-{$monthNum}-" . str_pad($day, 2, '0', STR_PAD_LEFT);
        }
        if (preg_match('/(\d{4})/', $dateStr, $m)) {
            return "{$m[1]}-01-01";
        }
        return null;
    }

    protected function parseBrand(string $phoneName): string
    {
        // Handle "vivo iQOO" brand prefix
        $lowerName = strtolower($phoneName);
        if (str_starts_with($lowerName, 'vivo iqoo')) return 'vivo';
        if (str_starts_with($lowerName, 'iqoo')) return 'vivo';
        
        $parts = explode(' ', $phoneName);
        return $parts[0] ?? 'Unknown';
    }

    // ─── Display parsers ─────────────────────────────────────────────

    protected function parseScreenArea(?string $sizeStr): ?string
    {
        if (!$sizeStr) return null;
        // Match "97.9 cm²" or "97.9 cm 2" or "97.9 cm2"
        if (preg_match('/([\d.]+)\s*cm\s*[²2]/', $sizeStr, $m)) {
            return $m[1] . ' cm²';
        }
        return null;
    }

    protected function parseDisplayBrightness(?string $typeStr): ?string
    {
        if (!$typeStr) return null;
        // Match highest nits value — "1600 nits (HBM), 4500 nits (peak)"
        $allNits = [];
        if (preg_match_all('/(\d+)\s*nits/i', $typeStr, $matches)) {
            $allNits = array_map('intval', $matches[1]);
        }
        if (!empty($allNits)) {
            $maxNits = max($allNits);
            return $maxNits . ' nits (peak)';
        }
        return null;
    }

    protected function parseIpRating(?string $str): ?string
    {
        if (!$str) return null;
        if (preg_match('/(IP\d+[A-Z]*)/i', $str, $m)) {
            return strtoupper($m[1]);
        }
        return null;
    }

    protected function parsePwmDimming(?string $str): ?string
    {
        if (!$str) return null;
        // Match "2160Hz PWM" or "2160 Hz PWM" or "PWM 2160Hz"
        if (preg_match('/(\d+)\s*Hz\s*PWM/i', $str, $m)) {
            return $m[1] . 'Hz PWM';
        }
        if (preg_match('/PWM\s*(\d+)\s*Hz/i', $str, $m)) {
            return $m[1] . 'Hz PWM';
        }
        // Also match comma-separated format: "120Hz, 2160Hz PWM"
        if (preg_match('/(\d+)Hz\s*PWM/i', $str, $m)) {
            return $m[1] . 'Hz PWM';
        }
        if (stripos($str, 'PWM') !== false) {
            return 'Yes';
        }
        return null;
    }

    protected function parseTouchSamplingRate(?string $str): ?string
    {
        if (!$str) return null;
        if (preg_match('/(\d+)\s*Hz\s*touch/i', $str, $m)) {
            return $m[1] . 'Hz';
        }
        return null;
    }

    protected function parseScreenToBody(?string $str): ?string
    {
        if (!$str) return null;
        if (preg_match('/~([\d.]+%)/', $str, $m)) {
            return '~' . $m[1];
        }
        return null;
    }

    protected function parsePixelDensity(?string $str): ?string
    {
        if (!$str) return null;
        if (preg_match('/~(\d+)\s*ppi/i', $str, $m)) {
            return '~' . $m[1] . ' ppi';
        }
        return null;
    }

    protected function parseAspectRatio(?string $str): ?string
    {
        if (!$str) return null;
        if (preg_match('/(\d+:\d+)/', $str, $m)) {
            return $m[1];
        }
        return null;
    }

    protected function parseGlassProtectionLevel(?string $protection): ?string
    {
        if (!$protection) return null;
        $p = strtolower($protection);
        if (str_contains($p, 'victus 2')) return 'Gorilla Glass Victus 2';
        if (str_contains($p, 'victus')) return 'Gorilla Glass Victus';
        if (str_contains($p, 'gorilla glass 7')) return 'Gorilla Glass 7';
        if (str_contains($p, 'gorilla glass 6')) return 'Gorilla Glass 6';
        if (str_contains($p, 'gorilla glass 5')) return 'Gorilla Glass 5';
        if (str_contains($p, 'ceramic shield')) return 'Ceramic Shield';
        return $protection;
    }

    // ─── Platform parsers ────────────────────────────────────────────

    protected function parseRamFromInternal(?string $str): ?string
    {
        if (!$str) return null;
        // "256GB 12GB RAM, 512GB 16GB RAM" → "12GB, 16GB"
        preg_match_all('/(\d+)\s*GB\s*RAM/i', $str, $m);
        if (!empty($m[1])) {
            $unique = array_unique($m[1]);
            sort($unique, SORT_NUMERIC);
            return implode(', ', array_map(fn($v) => $v . 'GB', $unique));
        }
        return null;
    }

    protected function parseStorageFromInternal(?string $str): ?string
    {
        if (!$str) return null;
        // "256GB 12GB RAM, 512GB 16GB RAM, 1TB 16GB RAM" → "256GB, 512GB, 1TB"
        $storages = [];
        // Match TB
        preg_match_all('/(\d+)\s*TB/i', $str, $tbM);
        foreach ($tbM[0] as $tb) {
            $storages[] = trim($tb);
        }
        // Match GB (but not "GB RAM")
        preg_match_all('/(\d+)\s*GB(?!\s*RAM)/i', $str, $gbM);
        foreach ($gbM[0] as $gb) {
            $storages[] = trim($gb);
        }
        if (!empty($storages)) {
            return implode(', ', array_unique($storages));
        }
        return null;
    }

    protected function parseStorageType(?string $str): ?string
    {
        if (!$str) return null;
        if (preg_match('/(UFS\s*[\d.]+)/i', $str, $m)) {
            return strtoupper(str_replace(' ', ' ', $m[1]));
        }
        if (stripos($str, 'NVMe') !== false) return 'NVMe';
        return null;
    }

    protected function determineOsOpenness(string $brand, ?string $os): string
    {
        $brand = strtolower($brand);
        if (in_array($brand, ['google', 'motorola'])) return 'Near-AOSP';
        if (in_array($brand, ['oneplus', 'nothing'])) return 'Moderately restricted';
        if (in_array($brand, ['xiaomi', 'poco'])) return 'Moderately restricted';
        return 'Restricted OEM skin';
    }

    protected function determineCustomRomSupport(string $brand): string
    {
        $brand = strtolower($brand);
        if (in_array($brand, ['oneplus', 'xiaomi', 'poco', 'google', 'nothing'])) return 'Major';
        if (in_array($brand, ['motorola', 'samsung', 'realme'])) return 'Limited';
        return 'None';
    }

    protected function determineTurnipSupport(string $gpu): bool
    {
        return str_contains(strtolower($gpu), 'adreno');
    }

    protected function determineTurnipLevel(string $gpu): string
    {
        $gpu = strtolower($gpu);
        if (str_contains($gpu, 'adreno 830') || str_contains($gpu, 'adreno 750')) return 'Full';
        if (str_contains($gpu, 'adreno 8')) return 'Full';
        if (str_contains($gpu, 'adreno 7')) return 'Stable';
        if (str_contains($gpu, 'adreno 6')) return 'Partial';
        return 'Stable';
    }

    protected function determineGpuEmulationTier(string $gpu): ?string
    {
        $gpu = strtolower($gpu);
        if (str_contains($gpu, 'adreno 830') || str_contains($gpu, 'adreno 750')) return 'Adreno 8xx High-tier';
        if (str_contains($gpu, 'adreno 8')) return 'Adreno 8xx High-tier';
        if (str_contains($gpu, 'adreno 7')) return 'Adreno 7xx Mid-tier';
        if (str_contains($gpu, 'adreno 6')) return 'Adreno 6xx Entry-tier';
        if (str_contains($gpu, 'immortalis')) return 'Mali Immortalis High-tier';
        if (str_contains($gpu, 'mali-g')) return 'Mali Mid-tier';
        return null;
    }

    protected function determineAospAesthetics(string $brand, ?string $os): int
    {
        $brand = strtolower($brand);
        $os = strtolower($os ?? '');
        if (str_contains($os, 'pixel') || $brand === 'google') return 10;
        if ($brand === 'nothing') return 9;
        if ($brand === 'motorola') return 8;
        if ($brand === 'oneplus') return 7;
        if (in_array($brand, ['xiaomi', 'poco'])) return 5;
        if (in_array($brand, ['samsung', 'oppo', 'vivo', 'realme'])) return 4;
        return 5;
    }

    protected function determineCoolingType(string $brand, string $name): ?string
    {
        $name = strtolower($name);
        $brand = strtolower($brand);
        // Gaming phones with active fans
        if (str_contains($name, 'rog') || str_contains($name, 'redmagic')) return 'Active Fan';
        // Flagships with vapor chambers
        if (str_contains($name, 'iqoo') && (str_contains($name, 'pro') || str_contains($name, 'ultra'))) return 'Vapor Chamber';
        if ($brand === 'oneplus' && (str_contains($name, '13') || str_contains($name, '15'))) return 'Vapor Chamber';
        if (str_contains($name, 'find x')) return 'Vapor Chamber';
        if (str_contains($name, 'galaxy s2') && str_contains($name, 'ultra')) return 'Vapor Chamber';
        if (str_contains($name, 'xiaomi 15') || str_contains($name, 'xiaomi 14')) return 'Vapor Chamber';
        if (str_contains($name, 'pixel 9 pro')) return 'Vapor Chamber';
        if (str_contains($name, 'gt 7 pro') || str_contains($name, 'gt 6')) return 'Vapor Chamber';
        // Mid-range with graphite
        if (str_contains($name, 'poco') || str_contains($name, 'nord') || str_contains($name, 'nothing phone')) return 'Graphite';
        if (str_contains($name, 'gt 7') && !str_contains($name, 'pro')) return 'Graphite';
        return null;
    }

    // ─── Camera parsers ──────────────────────────────────────────────

    protected function parseOis(?string $str): ?string
    {
        if (!$str) return null;
        return stripos($str, 'OIS') !== false ? 'Yes' : 'No';
    }

    protected function parseUltrawide(?string $str): ?string
    {
        if (!$str) return null;
        $lines = preg_split('/[\n;]+/', $str);
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/ultrawide|120˚|114˚|119˚|112˚|ultra-wide/i', $line)) {
                return $line;
            }
        }
        return null;
    }

    protected function parseTelephoto(?string $str): ?string
    {
        if (!$str) return null;
        $lines = preg_split('/[\n;]+/', $str);
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/telephoto|periscope|optical zoom|3x|5x|10x/i', $line)) {
                return $line;
            }
        }
        return null;
    }

    protected function parseCameraSensors(?string $str): ?string
    {
        if (!$str) return null;
        $sensors = [];
        $lines = preg_split('/[\n;]+/', $str);
        foreach ($lines as $line) {
            if (preg_match('/(\d+)\s*MP/i', $line, $m)) {
                $label = 'Main';
                if (preg_match('/ultrawide|ultra-wide/i', $line)) $label = 'Ultrawide';
                elseif (preg_match('/telephoto|periscope/i', $line)) $label = 'Telephoto';
                
                // Extract sensor size if present
                $sensorSize = '';
                if (preg_match('/(1\/[\d.]+")[,\s]/i', $line, $sm)) {
                    $sensorSize = ' (' . $sm[1] . ')';
                }
                $sensors[] = $label . ': ' . $m[1] . 'MP' . $sensorSize;
            }
        }
        return !empty($sensors) ? implode(', ', $sensors) : null;
    }

    protected function parseCameraApertures(?string $str): ?string
    {
        if (!$str) return null;
        $apertures = [];
        $lines = preg_split('/[\n;]+/', $str);
        foreach ($lines as $line) {
            if (preg_match('/(f\/[\d.]+)/i', $line, $m)) {
                $label = 'main';
                if (preg_match('/ultrawide|ultra-wide/i', $line)) $label = 'ultrawide';
                elseif (preg_match('/telephoto|periscope/i', $line)) $label = 'telephoto';
                $apertures[] = $m[1] . ' (' . $label . ')';
            }
        }
        return !empty($apertures) ? implode(', ', $apertures) : null;
    }

    protected function parseCameraFocalLengths(?string $str): ?string
    {
        if (!$str) return null;
        $lengths = [];
        $lines = preg_split('/[\n;]+/', $str);
        foreach ($lines as $line) {
            if (preg_match('/(\d+)mm/i', $line, $m)) {
                $label = 'main';
                if (preg_match('/ultrawide|ultra-wide/i', $line)) $label = 'ultrawide';
                elseif (preg_match('/telephoto|periscope/i', $line)) $label = 'telephoto';
                $lengths[] = $m[1] . 'mm (' . $label . ')';
            }
        }
        return !empty($lengths) ? implode(', ', $lengths) : null;
    }

    protected function parsePdaf(?string $str): ?string
    {
        if (!$str) return null;
        if (stripos($str, 'multi-directional PDAF') !== false) return 'Multi-directional PDAF';
        if (stripos($str, 'Dual Pixel') !== false || stripos($str, 'DPAF') !== false) return 'Dual Pixel AF';
        if (stripos($str, 'PDAF') !== false) return 'PDAF';
        if (stripos($str, 'Laser') !== false) return 'Laser AF';
        return null;
    }

    protected function parseZoom(?string $str): ?string
    {
        if (!$str) return null;
        if (preg_match('/(\d+x)\s*optical\s*zoom/i', $str, $m)) {
            return $m[1] . ' optical zoom';
        }
        return null;
    }

    protected function parseSelfieAperture(?string $str): ?string
    {
        if (!$str) return null;
        if (preg_match('/(f\/[\d.]+)/i', $str, $m)) {
            return $m[1];
        }
        return null;
    }

    protected function parseSelfieSensor(?string $str): ?string
    {
        if (!$str) return null;
        if (preg_match('/(\d+)\s*MP/i', $str, $m)) {
            return $m[1] . 'MP';
        }
        return null;
    }

    // ─── Other parsers ───────────────────────────────────────────────

    protected function parseWifiBands(?string $str): ?string
    {
        if (!$str) return null;
        if (stripos($str, 'tri-band') !== false) return 'Tri-band';
        if (stripos($str, 'dual-band') !== false) return 'Dual-band';
        return null;
    }

    protected function parseWiredCharging(?string $str): ?string
    {
        if (!$str) return null;
        if (preg_match('/(\d+)\s*W\s*(?:wired|,)/i', $str, $m)) {
            return $m[1] . 'W wired';
        }
        // First number with W is often wired
        if (preg_match('/(\d+)\s*W/i', $str, $m)) {
            return $m[1] . 'W wired';
        }
        return null;
    }

    protected function parseWirelessCharging(?string $str): ?string
    {
        if (!$str) return null;
        if (preg_match('/(\d+)\s*W\s*wireless/i', $str, $m)) {
            return $m[1] . 'W wireless';
        }
        if (preg_match('/(\d+)\s*W\s*Qi/i', $str, $m)) {
            return $m[1] . 'W wireless';
        }
        return null;
    }
}
