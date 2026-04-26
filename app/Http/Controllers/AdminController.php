<?php

namespace App\Http\Controllers;

use App\Repositories\PhoneRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    protected PhoneRepository $phones;

    public function __construct(PhoneRepository $phones)
    {
        $this->phones = $phones;
    }

    public function dashboard()
    {
        $all = $this->phones->all();
        usort($all, function ($a, $b) {
            return ($b->created_at ?? '') <=> ($a->created_at ?? '');
        });
        $latestPhones = collect(array_slice($all, 0, 8));
        $totalPhones = count($all);
        $expertRated = count(array_filter($all, fn ($p) => ($p->expert_score ?? 0) > 0));

        return view('admin.dashboard', compact('latestPhones', 'totalPhones', 'expertRated'));
    }

    public function index(Request $request)
    {
        $all = $this->phones->all();
        usort($all, function ($a, $b) {
            return ($b->created_at ?? '') <=> ($a->created_at ?? '');
        });

        if ($search = $request->input('search')) {
            $lower = strtolower($search);
            $all = array_filter($all, function ($phone) use ($lower) {
                return str_contains(strtolower($phone->name), $lower)
                    || str_contains(strtolower($phone->brand), $lower);
            });
        }

        $page = (int) $request->input('page', 1);
        $perPage = 20;
        $total = count($all);
        $items = array_slice($all, ($page - 1) * $perPage, $perPage);

        $phones = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.phones', compact('phones'));
    }

    public function addPhone()
    {
        return view('admin.add-phone');
    }

    public function storePhone(Request $request)
    {
        $request->validate([
            'phone_name' => 'required|string|min:2|max:100',
            'gsmarena_url' => 'nullable|url',
            'price' => 'nullable|numeric|min:0',
            'image_url' => 'nullable|url',
            'amazon_url' => 'nullable|url',
            'flipkart_url' => 'nullable|url',
            'antutu_score' => 'nullable|integer|min:0',
            'dmark_score' => 'nullable|integer|min:0',
            'dmark_stability' => 'nullable|integer|min:0|max:100',
            'geekbench_single' => 'nullable|integer|min:0',
            'geekbench_multi' => 'nullable|integer|min:0',
            'dxomark_score' => 'nullable|integer|min:0',
            'phonearena_score' => 'nullable|integer|min:0',
        ]);

        $jobId = (string) Str::uuid();

        Cache::put("admin_import_{$jobId}", [
            'status' => 'pending',
            'step' => 0,
            'total' => 8,
            'steps' => [],
            'phone_id' => null,
            'error' => null,
        ], 600);

        $overrides = array_filter([
            'price' => $request->input('price'),
            'image_url' => $request->input('image_url'),
            'amazon_url' => $request->input('amazon_url'),
            'flipkart_url' => $request->input('flipkart_url'),
            'antutu_score' => $request->input('antutu_score'),
            'dmark_score' => $request->input('dmark_score'),
            'dmark_stability' => $request->input('dmark_stability'),
            'geekbench_single' => $request->input('geekbench_single'),
            'geekbench_multi' => $request->input('geekbench_multi'),
            'dxomark_score' => $request->input('dxomark_score'),
            'phonearena_score' => $request->input('phonearena_score'),
        ], fn ($v) => $v !== null && $v !== '');

        $importOptions = [
            'skip_image' => $request->boolean('skip_image'),
            'skip_shopping' => $request->boolean('skip_shopping'),
            'force' => $request->boolean('force'),
            'gsmarena_url' => $request->input('gsmarena_url'),
        ];

        $this->runImport($jobId, $request->input('phone_name'), $overrides, $importOptions);

        return redirect()->route('admin.phones.status', ['jobId' => $jobId]);
    }

    public function importStatus(string $jobId)
    {
        $status = Cache::get("admin_import_{$jobId}");

        if (! $status) {
            return response()->json(['error' => 'Job not found or expired'], 404);
        }

        return response()->json($status);
    }

    public function importStatusPage(string $jobId)
    {
        $status = Cache::get("admin_import_{$jobId}");

        if (! $status) {
            return redirect()->route('admin.phones.add')
                ->with('error', 'Import job not found or expired.');
        }

        return view('admin.import-status', compact('jobId', 'status'));
    }

    protected function runImport(string $jobId, string $phoneName, array $overrides, array $options): void
    {
        $progress = fn (int $step, string $stepName, string $state, ?string $msg = null) => Cache::put("admin_import_{$jobId}", array_merge(
            Cache::get("admin_import_{$jobId}", []),
            [
                'status' => $state === 'error' ? 'error' : ($step >= 8 ? 'done' : 'running'),
                'step' => $step,
                'steps' => array_merge(
                    Cache::get("admin_import_{$jobId}", [])['steps'] ?? [],
                    [['step' => $step, 'name' => $stepName, 'state' => $state, 'msg' => $msg]]
                ),
            ]
        ), 600);

        try {
            $progress(1, 'Calling Python aggregator', 'running');

            $pythonBin = env('PYTHON_BIN');
            if (! $pythonBin || ! file_exists($pythonBin)) {
                $candidates = [
                    '/opt/phonefinder-venv/bin/python3',
                    base_path('.venv/bin/python3'),
                    base_path('.venv/bin/python'),
                    'python3',
                ];
                $pythonBin = null;
                foreach ($candidates as $candidate) {
                    if (! str_starts_with($candidate, '/') || file_exists($candidate)) {
                        $pythonBin = $candidate;
                        break;
                    }
                }
            }

            $scriptsPath = env('PYTHON_SCRIPTS_PATH', base_path('python'));
            $pythonScript = $scriptsPath.'/phone_data_aggregator.py';

            if (! file_exists($pythonScript)) {
                $progress(1, 'Calling Python aggregator', 'error', "Script not found: {$pythonScript}");
                Cache::put("admin_import_{$jobId}", array_merge(Cache::get("admin_import_{$jobId}", []), ['status' => 'error', 'error' => 'Python script not found']), 600);

                return;
            }

            $skipSteps = [];
            if ($options['skip_image'] || isset($overrides['image_url'])) {
                $skipSteps[] = 'image';
            }
            if ($options['skip_shopping'] || (isset($overrides['amazon_url']) && isset($overrides['flipkart_url']))) {
                $skipSteps[] = 'shopping';
            }
            $skipArg = ! empty($skipSteps) ? '--skip='.implode(',', $skipSteps) : '';

            $gsmarenaArg = '';
            if (! empty($options['gsmarena_url'])) {
                $gsmarenaArg = '--gsmarena-url='.escapeshellarg($options['gsmarena_url']);
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'admin_import_').'.json';
            $command = "{$pythonBin} {$pythonScript} ".escapeshellarg($phoneName)
                      .' --output '.escapeshellarg($tempFile)
                      ." {$skipArg} {$gsmarenaArg} 2>&1";

            $pythonOutput = [];
            exec($command, $pythonOutput, $exitCode);
            $pythonLogs = implode("\n", $pythonOutput);

            if (! file_exists($tempFile)) {
                $progress(1, 'Calling Python aggregator', 'error', 'Python script produced no output. Output: '.substr($pythonLogs, 0, 500));
                Cache::put("admin_import_{$jobId}", array_merge(Cache::get("admin_import_{$jobId}", []), ['status' => 'error', 'error' => 'Python script produced no output']), 600);

                return;
            }

            $rawJson = file_get_contents($tempFile);
            @unlink($tempFile);
            $data = json_decode($rawJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $progress(1, 'Calling Python aggregator', 'error', 'Failed to parse JSON from Python');
                Cache::put("admin_import_{$jobId}", array_merge(Cache::get("admin_import_{$jobId}", []), ['status' => 'error', 'error' => 'JSON parse error']), 600);

                return;
            }

            $progress(1, 'Calling Python aggregator', 'done', "Data fetched. Steps: {$data['summary']['successful_steps']}/{$data['summary']['total_steps']} ok");

            $progress(2, 'Applying manual overrides', 'running');
            $progress(2, 'Applying manual overrides', 'done', count($overrides).' field(s) will override scraped data');

            $progress(3, 'Saving phone to database', 'running');
            $phoneData = $this->buildPhoneDocument($phoneName, $data, $overrides, $options);

            $existing = null;
            foreach ($this->phones->all() as $p) {
                if (strtolower($p->name) === strtolower($phoneName)) {
                    $existing = $p;
                    break;
                }
            }

            if ($existing && ! ($options['force'] ?? false)) {
                $this->phones->update($existing->id, $phoneData);
                $phone = $this->phones->find($existing->id);
            } elseif ($existing) {
                $this->phones->update($existing->id, $phoneData);
                $phone = $this->phones->find($existing->id);
            } else {
                $phone = $this->phones->create($phoneData);
            }

            $progress(3, 'Saving phone to database', 'done', "Phone ID: {$phone->id}");

            $progress(4, 'Saving specification records', 'done');
            $progress(5, 'Saving benchmark records', 'done');

            $progress(6, 'Calculating scores', 'running');
            $phone->updateScores();
            $this->phones->update($phone->id, $phone->getAttributes());
            $progress(6, 'Calculating scores', 'done',
                "FPI: {$phone->overall_score} | UEPS: {$phone->ueps_score} | Expert: {$phone->expert_score}");

            $progress(7, 'Clearing caches', 'running');
            \Artisan::call('cache:clear');
            $progress(7, 'Clearing caches', 'done');

            $progress(8, 'Import complete!', 'done', "✓ {$phone->name} added successfully. Click below to view.");

            Cache::put("admin_import_{$jobId}", array_merge(
                Cache::get("admin_import_{$jobId}", []),
                ['status' => 'done', 'phone_id' => $phone->id, 'phone_name' => $phone->name]
            ), 600);

        } catch (\Exception $e) {
            $current = Cache::get("admin_import_{$jobId}", []);
            Cache::put("admin_import_{$jobId}", array_merge($current, [
                'status' => 'error',
                'error' => $e->getMessage(),
                'steps' => array_merge($current['steps'] ?? [], [
                    ['step' => 99, 'name' => 'Exception', 'state' => 'error', 'msg' => $e->getMessage()],
                ]),
            ]), 600);
        }
    }

    protected function buildPhoneDocument(string $phoneName, array $data, array $overrides, array $options): array
    {
        $gsmarena = $data['gsmarena']['data'] ?? $data['gsmarena'] ?? null;
        $specs = ($gsmarena['specifications'] ?? $gsmarena) ?? [];
        $shopping = $data['shopping_links']['data'] ?? $data['shopping_links'] ?? null;
        $image = $data['image']['data'] ?? $data['image'] ?? null;

        $scrapedPrice = $this->parsePrice($specs['Misc']['Price'] ?? null);
        $price = isset($overrides['price']) ? (float) $overrides['price'] : $scrapedPrice;

        $releaseDate = $this->parseDate($specs['Launch']['Status'] ?? null);
        $announcedDate = $this->parseDate($specs['Launch']['Announced'] ?? null);
        $brand = $this->parseBrand($phoneName);

        $amazonUrl = $overrides['amazon_url'] ?? null;
        $flipkartUrl = $overrides['flipkart_url'] ?? null;
        $amazonPrice = null;
        $flipkartPrice = null;

        if (! $amazonUrl && $shopping) {
            $amazonItems = $shopping['amazon'] ?? [];
            if (! empty($amazonItems[0])) {
                $amazonUrl = $amazonItems[0]['link'] ?? null;
                $amazonPrice = $this->parsePrice($amazonItems[0]['price'] ?? null);
            }
        }
        if (! $flipkartUrl && $shopping) {
            $flipkartItems = $shopping['flipkart'] ?? [];
            if (! empty($flipkartItems[0])) {
                $flipkartUrl = $flipkartItems[0]['link'] ?? null;
                $flipkartPrice = $this->parsePrice($flipkartItems[0]['price'] ?? null);
            }
        }

        $imageUrl = $overrides['image_url'] ?? null;
        if (! $imageUrl) {
            if ($image && ! empty($image['image_path'])) {
                $imgPath = str_replace('\\', '/', $image['image_path']);
                if (str_contains($imgPath, 'storage/app/public/')) {
                    $imageUrl = '/storage/'.str_replace('storage/app/public/', '', $imgPath);
                } elseif (str_contains($imgPath, 'storage/public/')) {
                    $imageUrl = '/storage/'.str_replace('storage/public/', '', $imgPath);
                } elseif (str_starts_with($imgPath, 'storage/')) {
                    $imageUrl = '/'.$imgPath;
                } else {
                    $imageUrl = '/storage/'.basename($imgPath);
                }
            } elseif ($gsmarena && ! empty($gsmarena['image_url'])) {
                $imageUrl = $gsmarena['image_url'];
            }
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
            'body' => $this->buildBodySpecs($phoneName, $brand, $specs),
            'platform' => $this->buildPlatformSpecs($brand, $specs),
            'camera' => $this->buildCameraSpecs($specs),
            'connectivity' => $this->buildConnectivitySpecs($specs),
            'battery' => $this->buildBatterySpecs($specs),
            'benchmarks' => $this->buildBenchmarks($data, $overrides),
        ];

        return $phoneData;
    }

    protected function buildBodySpecs(string $phoneName, string $brand, array $specs): array
    {
        $body = $specs['Body'] ?? [];
        $display = $specs['Display'] ?? [];
        $misc = $specs['Misc'] ?? [];

        $dimensions = $body['Dimensions'] ?? null;
        $weight = $body['Weight'] ?? null;
        $build = $body['Build'] ?? null;
        $sim = $body['SIM'] ?? null;
        if (is_array($sim)) {
            $sim = implode("\n", $sim);
        }

        $ipRating = $this->parseIpRating($sim) ?? $this->parseIpRating($build);
        $colors = $misc['Colors'] ?? null;
        $displayType = $display['Type'] ?? null;
        $sizeStr = $display['Size'] ?? null;
        $screenArea = $this->parseScreenArea($sizeStr);
        $screenToBody = $this->parseScreenToBody($sizeStr);
        $resStr = $display['Resolution'] ?? null;
        $pixelDensity = $this->parsePixelDensity($resStr);
        $aspectRatio = $this->parseAspectRatio($resStr);
        $displayBright = $this->parseDisplayBrightness($displayType);
        $ourTests = $specs['Our Tests'] ?? [];
        $measuredBright = $ourTests['Display'] ?? null;
        $protection = $display['Protection'] ?? null;
        if (is_array($protection)) {
            $protection = implode(', ', $protection);
        }
        $glassLevel = $this->parseGlassProtectionLevel($protection);
        $pwm = $this->parsePwmDimming($displayType);
        $touchRate = $this->parseTouchSamplingRate($displayType);
        $coolingType = $this->determineCoolingType($brand, $phoneName);

        return [
            'dimensions' => $dimensions,
            'weight' => $weight,
            'build_material' => $build,
            'cooling_type' => $coolingType,
            'sim' => $sim,
            'ip_rating' => $ipRating,
            'colors' => $colors,
            'display_type' => $displayType,
            'display_size' => $sizeStr,
            'display_resolution' => $resStr,
            'display_brightness' => $displayBright,
            'measured_display_brightness' => $measuredBright,
            'display_protection' => $protection,
            'pwm_dimming' => $pwm,
            'screen_to_body_ratio' => $screenToBody,
            'pixel_density' => $pixelDensity,
            'aspect_ratio' => $aspectRatio,
            'screen_area' => $screenArea,
            'touch_sampling_rate' => $touchRate,
            'screen_glass' => $protection,
            'glass_protection_level' => $glassLevel,
            'display_features' => $displayType,
        ];
    }

    protected function buildPlatformSpecs(string $brand, array $specs): array
    {
        $platform = $specs['Platform'] ?? [];
        $memory = $specs['Memory'] ?? [];

        $osRaw = $platform['OS'] ?? null;
        $os = $osRaw;
        $osDetails = null;
        if ($osRaw && str_contains($osRaw, ',')) {
            $parts = explode(',', $osRaw, 2);
            $os = trim($parts[0]).','.trim($parts[1]);
            $osDetails = trim($parts[1]);
        }

        $internalRaw = $memory['Internal'] ?? null;
        if (is_array($internalRaw)) {
            $internalRaw = implode(', ', $internalRaw);
        }
        $ram = $this->parseRamFromInternal($internalRaw);
        $storage = $this->parseStorageFromInternal($internalRaw);
        $storageType = $this->parseStorageType($internalRaw);
        $gpu = $platform['GPU'] ?? '';
        $brandLower = strtolower($brand);

        return [
            'os' => $os,
            'os_details' => $osDetails,
            'chipset' => $platform['Chipset'] ?? null,
            'cpu' => $platform['CPU'] ?? null,
            'gpu' => $gpu,
            'memory_card_slot' => $memory['Card slot'] ?? 'No',
            'internal_storage' => $storage ?? $internalRaw,
            'ram' => $ram ?? $internalRaw,
            'storage_type' => $storageType,
            'bootloader_unlockable' => in_array($brandLower, ['oneplus', 'xiaomi', 'poco', 'nothing', 'google', 'motorola', 'realme']),
            'turnip_support' => $this->determineTurnipSupport($gpu),
            'turnip_support_level' => $this->determineTurnipSupport($gpu) ? $this->determineTurnipLevel($gpu) : null,
            'os_openness' => $this->determineOsOpenness($brandLower, $os),
            'gpu_emulation_tier' => $this->determineGpuEmulationTier($gpu),
            'aosp_aesthetics_score' => $this->determineAospAesthetics($brandLower, $os),
            'custom_rom_support' => $this->determineCustomRomSupport($brandLower),
        ];
    }

    protected function buildCameraSpecs(array $specs): array
    {
        $mainCamera = $specs['Main Camera'] ?? [];
        $selfieCamera = $specs['Selfie camera'] ?? [];

        $mainSpecs = null;
        foreach (['Quad', 'Triple', 'Dual', 'Single', 'Main'] as $key) {
            if (! empty($mainCamera[$key])) {
                $mainSpecs = $mainCamera[$key];
                break;
            }
        }
        if (is_array($mainSpecs)) {
            $mainSpecs = implode("\n", $mainSpecs);
        }
        if ($mainSpecs && ! str_contains($mainSpecs, "\n")) {
            $mainSpecs = preg_replace('/\s+(\d+ MP,)/', "\n$1", $mainSpecs);
        }

        $features = $mainCamera['Features'] ?? null;
        if (is_array($features)) {
            $features = implode(', ', $features);
        }
        $video = $mainCamera['Video'] ?? null;
        if (is_array($video)) {
            $video = implode(', ', $video);
        }

        $selfieSpecs = null;
        foreach (['Dual', 'Single', 'Main'] as $key) {
            if (! empty($selfieCamera[$key])) {
                $selfieSpecs = $selfieCamera[$key];
                break;
            }
        }
        if (is_array($selfieSpecs)) {
            $selfieSpecs = implode("\n", $selfieSpecs);
        }
        $selfieVideo = $selfieCamera['Video'] ?? null;
        if (is_array($selfieVideo)) {
            $selfieVideo = implode(', ', $selfieVideo);
        }
        $selfieFeatures = $selfieCamera['Features'] ?? null;
        if (is_array($selfieFeatures)) {
            $selfieFeatures = implode(', ', $selfieFeatures);
        }

        return [
            'main_camera_specs' => $mainSpecs,
            'main_camera_sensors' => $this->parseCameraSensors($mainSpecs),
            'main_camera_apertures' => $this->parseCameraApertures($mainSpecs),
            'main_camera_focal_lengths' => $this->parseCameraFocalLengths($mainSpecs),
            'main_camera_features' => $features,
            'main_camera_ois' => $this->parseOis($mainSpecs),
            'main_camera_zoom' => $this->parseZoom($mainSpecs),
            'main_camera_pdaf' => $this->parsePdaf($mainSpecs),
            'main_video_capabilities' => $video,
            'video_features' => $video,
            'ultrawide_camera_specs' => $this->parseUltrawide($mainSpecs),
            'telephoto_camera_specs' => $this->parseTelephoto($mainSpecs),
            'selfie_camera_specs' => $selfieSpecs,
            'selfie_camera_sensor' => $this->parseSelfieSensor($selfieSpecs),
            'selfie_camera_aperture' => $this->parseSelfieAperture($selfieSpecs),
            'selfie_camera_features' => $selfieFeatures,
            'selfie_camera_autofocus' => $selfieSpecs && (
                stripos($selfieSpecs, 'AF') !== false ||
                stripos($selfieSpecs, 'PDAF') !== false ||
                stripos($selfieSpecs, 'autofocus') !== false
            ),
            'selfie_video_capabilities' => $selfieVideo,
            'selfie_video_features' => $selfieVideo,
        ];
    }

    protected function buildConnectivitySpecs(array $specs): array
    {
        $comms = $specs['Comms'] ?? [];
        $features = $specs['Features'] ?? [];
        $sound = $specs['Sound'] ?? [];
        $network = $specs['Network'] ?? [];
        $misc = $specs['Misc'] ?? [];
        $s = fn ($v) => is_array($v) ? implode(', ', $v) : ($v ? (string) $v : null);

        $jack = $s($sound['3.5mm jack'] ?? 'No');
        $nfc = $s($comms['NFC'] ?? 'No');
        $infrared = $s($comms['Infrared port'] ?? 'No');
        $wlan = $s($comms['WLAN'] ?? null);
        $bluetooth = $s($comms['Bluetooth'] ?? null);
        $usb = $s($comms['USB'] ?? null);
        $positioning = $s($comms['Positioning'] ?? null);
        $loudspeaker = $s($sound['Loudspeaker '] ?? $sound['Loudspeaker'] ?? null);
        $sensors = $s($features['Sensors'] ?? null);
        $sarValue = $s($misc['SAR'] ?? null);
        $radio = $s($comms['Radio'] ?? 'No');

        return [
            'network_bands' => $s($network['Technology'] ?? null),
            'wlan' => $wlan,
            'wifi_bands' => $this->parseWifiBands($wlan),
            'bluetooth' => $bluetooth,
            'positioning' => $positioning,
            'positioning_details' => $positioning,
            'nfc' => str_contains(strtolower($nfc), 'yes') ? 'Yes' : $nfc,
            'infrared' => str_contains(strtolower($infrared), 'yes') ? 'Yes' : $infrared,
            'radio' => $radio,
            'usb' => $usb,
            'usb_details' => $usb,
            'sensors' => $sensors,
            'loudspeaker' => $loudspeaker,
            'audio_quality' => $s($sound['Quality'] ?? null),
            'loudness_test_result' => $s($sound['Loudness'] ?? null),
            'jack_3_5mm' => $jack,
            'has_3_5mm_jack' => ! str_contains(strtolower($jack), 'no'),
            'sar_value' => $sarValue,
        ];
    }

    protected function buildBatterySpecs(array $specs): array
    {
        $battery = $specs['Battery'] ?? [];
        $batteryType = $battery['Type'] ?? null;
        if (is_array($batteryType)) {
            $batteryType = implode(', ', $batteryType);
        }
        $charging = $battery['Charging'] ?? null;
        if (is_array($charging)) {
            $charging = implode(', ', $charging);
        }

        $wiredCharging = $this->parseWiredCharging($charging);
        $wirelessCharging = $this->parseWirelessCharging($charging);
        $hasReverseWired = str_contains(strtolower($charging ?? ''), 'reverse');
        $hasReverseWireless = str_contains(strtolower($charging ?? ''), 'reverse wireless');
        $chargingReverse = $hasReverseWireless ? 'Reverse wired + wireless' : ($hasReverseWired ? 'Reverse wired' : null);

        return [
            'battery_type' => $batteryType,
            'charging_wired' => $wiredCharging,
            'charging_wireless' => $wirelessCharging,
            'charging_specs_detailed' => $charging,
            'charging_reverse' => $chargingReverse,
            'reverse_wired' => $hasReverseWired ? 'Yes' : null,
            'reverse_wireless' => $hasReverseWireless ? 'Yes' : null,
        ];
    }

    protected function buildBenchmarks(array $data, array $overrides): array
    {
        $gsmarena = $data['gsmarena']['data'] ?? $data['gsmarena'] ?? [];
        $specs = $gsmarena['specifications'] ?? $gsmarena ?? [];
        $ourTests = $specs['Our Tests'] ?? [];
        $euLabel = $specs['EU LABEL'] ?? [];
        $nanoreview = $data['nanoreview_benchmarks']['scores'] ??
                      $data['nanoreview_benchmarks']['data']['scores'] ?? [];
        $gpu = $data['gpu_benchmarks']['gpu_benchmark'] ??
                     $data['gpu_benchmarks']['data']['gpu_benchmark'] ?? [];
        $camera = $data['camera_benchmarks']['camera_benchmark'] ??
                     $data['camera_benchmarks']['data']['camera_benchmark'] ?? [];

        $antutuScore = null;
        $antutuV10 = null;
        if (! empty($nanoreview['antutu_v11'])) {
            $antutuScore = (int) $nanoreview['antutu_v11'];
        }
        if (! empty($nanoreview['antutu_v10'])) {
            $antutuV10 = (int) $nanoreview['antutu_v10'];
        }
        if (isset($overrides['antutu_score']) && $overrides['antutu_score'] !== null) {
            $antutuScore = (int) $overrides['antutu_score'];
        }

        $geekbenchSingle = null;
        $geekbenchMulti = null;
        if (! empty($nanoreview['geekbench_6_single'])) {
            $geekbenchSingle = (int) $nanoreview['geekbench_6_single'];
        }
        if (! empty($nanoreview['geekbench_6_multi'])) {
            $geekbenchMulti = (int) $nanoreview['geekbench_6_multi'];
        }
        $nanoIndividual = $data['nanoreview_benchmarks']['individual_scores'] ??
                          $data['nanoreview_benchmarks']['data']['individual_scores'] ?? [];
        if (! $geekbenchSingle && ! empty($nanoIndividual['geekbench_6_single_values'])) {
            $geekbenchSingle = (int) max($nanoIndividual['geekbench_6_single_values']);
        }
        if (! $geekbenchMulti && ! empty($nanoIndividual['geekbench_6_multi_values'])) {
            $geekbenchMulti = (int) max($nanoIndividual['geekbench_6_multi_values']);
        }
        if (isset($overrides['geekbench_single']) && $overrides['geekbench_single'] !== null) {
            $geekbenchSingle = (int) $overrides['geekbench_single'];
        }
        if (isset($overrides['geekbench_multi']) && $overrides['geekbench_multi'] !== null) {
            $geekbenchMulti = (int) $overrides['geekbench_multi'];
        }

        $dmarkScore = null;
        $stability = null;
        if (! empty($gpu['wildlife_extreme_peak'])) {
            $dmarkScore = (int) $gpu['wildlife_extreme_peak'];
        }
        if (! empty($gpu['wildlife_extreme_stability'])) {
            $stability = (int) round($gpu['wildlife_extreme_stability']);
        }
        if (isset($overrides['dmark_score']) && $overrides['dmark_score'] !== null) {
            $dmarkScore = (int) $overrides['dmark_score'];
        }
        if (isset($overrides['dmark_stability']) && $overrides['dmark_stability'] !== null) {
            $stability = (int) $overrides['dmark_stability'];
        }

        $dxomark = $camera['dxomark'] ?? null;
        $phonearena = $camera['phonearena'] ?? null;
        if (isset($overrides['dxomark_score']) && $overrides['dxomark_score'] !== null) {
            $dxomark = (int) $overrides['dxomark_score'];
        }
        if (isset($overrides['phonearena_score']) && $overrides['phonearena_score'] !== null) {
            $phonearena = (int) $overrides['phonearena_score'];
        }

        $enduranceHours = null;
        $euBattery = $euLabel['Battery'] ?? null;
        if ($euBattery && preg_match('/(\d+):(\d+)h\s*endurance/i', $euBattery, $m)) {
            $enduranceHours = (float) $m[1] + ((float) $m[2] / 60);
        }

        $activeUseScore = null;
        if (! empty($ourTests['Battery'])) {
            $activeUseScore = is_array($ourTests['Battery']) ? implode(', ', $ourTests['Battery']) : $ourTests['Battery'];
        }

        return [
            'antutu_score' => $antutuScore,
            'antutu_v10_score' => $antutuV10,
            'geekbench_single' => $geekbenchSingle,
            'geekbench_multi' => $geekbenchMulti,
            'dmark_wild_life_extreme' => $dmarkScore,
            'dmark_wild_life_stress_stability' => $stability,
            'dmark_test_type' => 'Wild Life Extreme',
            'battery_endurance_hours' => $enduranceHours,
            'battery_active_use_score' => $activeUseScore,
            'dxomark_score' => $dxomark ? (int) $dxomark : null,
            'phonearena_camera_score' => $phonearena ? (int) $phonearena : null,
            'repairability_score' => $euLabel['Repairability'] ?? null,
            'energy_label' => $euLabel['Energy'] ?? null,
            'free_fall_rating' => $euLabel['Free fall'] ?? null,
        ];
    }

    public function editPhone(string $phoneId)
    {
        $phone = $this->phones->findOrFail($phoneId);

        return view('admin.edit-phone', compact('phone'));
    }

    public function updatePhone(Request $request, string $phoneId)
    {
        $phone = $this->phones->findOrFail($phoneId);

        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'price' => 'nullable|numeric|min:0',
        ]);

        $updateData = array_merge(
            $phone->getAttributes(),
            $request->only([
                'name', 'brand', 'model_variant', 'price', 'release_date', 'announced_date',
                'image_url', 'amazon_url', 'flipkart_url', 'amazon_price', 'flipkart_price',
            ])
        );

        $relations = ['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks'];
        foreach ($relations as $relationName) {
            $data = $request->input($relationName, []);
            if (! empty($data)) {
                $updateData[$relationName] = array_merge(
                    (array) ($phone->$relationName ?? []),
                    $data
                );
            }
        }

        $this->phones->update($phone->id, $updateData);

        $phone = $this->phones->find($phone->id);
        $phone->updateScores();
        $this->phones->update($phone->id, $phone->getAttributes());

        return redirect()->route('admin.phones.edit', $phone->id)
            ->with('success', 'Phone updated successfully. Scores recalculated.');
    }

    // ─── Parser helpers ────────────────────────────────────────────────

    protected function parsePrice(?string $priceStr): ?float
    {
        if (! $priceStr) {
            return null;
        }
        if (preg_match('/₹\s*([\d,]+)/u', $priceStr, $m)) {
            return (float) str_replace(',', '', $m[1]);
        }
        if (preg_match('/€\s*([\d,]+)/u', $priceStr, $m)) {
            return (float) str_replace(',', '', $m[1]) * 90;
        }
        if (preg_match('/\$\s*([\d,]+)/u', $priceStr, $m)) {
            return (float) str_replace(',', '', $m[1]) * 83;
        }
        if (preg_match('/([\d,]+)\s*(?:INR|Rs)/i', $priceStr, $m)) {
            return (float) str_replace(',', '', $m[1]);
        }

        return null;
    }

    protected function parseDate(?string $dateStr): ?string
    {
        if (! $dateStr) {
            return null;
        }
        if (preg_match('/(\d{4}),?\s*(\w+)\s*(\d+)?/', $dateStr, $m)) {
            $monthNum = date('m', strtotime("{$m[2]} 1"));

            return "{$m[1]}-{$monthNum}-".str_pad($m[3] ?? '01', 2, '0', STR_PAD_LEFT);
        }
        if (preg_match('/(\d{4})/', $dateStr, $m)) {
            return "{$m[1]}-01-01";
        }

        return null;
    }

    protected function parseBrand(string $phoneName): string
    {
        $lowerName = strtolower($phoneName);
        if (str_starts_with($lowerName, 'vivo iqoo') || str_starts_with($lowerName, 'iqoo')) {
            return 'vivo';
        }

        return explode(' ', $phoneName)[0] ?? 'Unknown';
    }

    protected function parseIpRating(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/(IP\d+[A-Z]*)/i', $str, $m)) {
            return strtoupper($m[1]);
        }

        return null;
    }

    protected function parseScreenArea(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/([\d.]+)\s*cm\s*[²2]/', $str, $m)) {
            return $m[1].' cm²';
        }

        return null;
    }

    protected function parseScreenToBody(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/~([\d.]+%)/', $str, $m)) {
            return '~'.$m[1];
        }

        return null;
    }

    protected function parsePixelDensity(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/~(\d+)\s*ppi/i', $str, $m)) {
            return $m[1].' ppi';
        }

        return null;
    }

    protected function parseAspectRatio(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/(\d+:\d+)\s*ratio/i', $str, $m)) {
            return $m[1];
        }
        if (preg_match('/(\d+:\d+)/', $str, $m)) {
            return $m[1];
        }

        return null;
    }

    protected function parseDisplayBrightness(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        $allNits = [];
        if (preg_match_all('/(\d+)\s*nits/i', $str, $m)) {
            $allNits = array_map('intval', $m[1]);
        }
        if (! empty($allNits)) {
            return max($allNits).' nits (peak)';
        }

        return null;
    }

    protected function parsePwmDimming(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/(\d+)\s*Hz\s*PWM/i', $str, $m)) {
            return $m[1].'Hz PWM';
        }
        if (preg_match('/PWM\s*(\d+)\s*Hz/i', $str, $m)) {
            return $m[1].'Hz PWM';
        }
        if (preg_match('/(\d+)Hz\s*PWM/i', $str, $m)) {
            return $m[1].'Hz PWM';
        }
        if (stripos($str, 'PWM') !== false) {
            return 'Yes';
        }

        return null;
    }

    protected function parseTouchSamplingRate(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/(\d+)\s*Hz\s*touch/i', $str, $m)) {
            return $m[1].'Hz';
        }

        return null;
    }

    protected function parseGlassProtectionLevel(?string $str): ?int
    {
        if (! $str) {
            return null;
        }
        $lower = strtolower($str);
        if (str_contains($lower, 'gorilla glass victus')) {
            return 4;
        }
        if (str_contains($lower, 'gorilla glass 7')) {
            return 3;
        }
        if (str_contains($lower, 'gorilla glass 6') || str_contains($lower, 'gorilla glass 5')) {
            return 2;
        }
        if (str_contains($lower, 'gorilla glass')) {
            return 1;
        }

        return null;
    }

    protected function parseRamFromInternal(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        preg_match_all('/(\d+)\s*GB\s*RAM/i', $str, $m);
        $rams = array_unique($m[1] ?? []);

        return ! empty($rams) ? implode('/', $rams).'GB' : null;
    }

    protected function parseStorageFromInternal(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        preg_match_all('/(\d+)\s*GB(?!\s*RAM)/i', $str, $m);
        $storages = array_unique($m[1] ?? []);

        return ! empty($storages) ? implode('/', $storages).'GB' : null;
    }

    protected function parseStorageType(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/UFS\s*([\d.]+)/i', $str, $m)) {
            return 'UFS '.$m[1];
        }
        if (preg_match('/NVMe/i', $str)) {
            return 'NVMe';
        }
        if (preg_match('/eMMC/i', $str)) {
            return 'eMMC';
        }

        return null;
    }

    protected function parseWiredCharging(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/(\d+)\s*W/i', $str, $m)) {
            return $m[1].'W';
        }

        return null;
    }

    protected function parseWirelessCharging(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/(\d+)\s*W\s*wireless/i', $str, $m)) {
            return $m[1].'W wireless';
        }
        if (stripos($str, 'wireless') !== false) {
            return 'Yes';
        }

        return null;
    }

    protected function parseWifiBands(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        $bands = [];
        if (preg_match('/2\.4\s*GHz/i', $str)) {
            $bands[] = '2.4GHz';
        }
        if (preg_match('/5\s*GHz/i', $str)) {
            $bands[] = '5GHz';
        }
        if (preg_match('/6\s*GHz/i', $str)) {
            $bands[] = '6GHz';
        }

        return ! empty($bands) ? implode(', ', $bands) : null;
    }

    protected function parseOis(?string $str): ?bool
    {
        if (! $str) {
            return null;
        }

        return stripos($str, 'OIS') !== false;
    }

    protected function parseUltrawide(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        $lines = explode("\n", $str);
        foreach ($lines as $line) {
            if (stripos($line, 'ultrawide') !== false || (preg_match('/f\/[2-9]\.[0-9]/i', $line) && preg_match('/(\d+)\s*mm|wide/i', $line))) {
                if (preg_match('/\d+\s*MP.*?(f\/[\d.]+)?.*?(OIS)?/i', $line)) {
                    return trim($line);
                }
            }
        }

        return null;
    }

    protected function parseTelephoto(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        $lines = explode("\n", $str);
        foreach ($lines as $line) {
            if (stripos($line, 'telephoto') !== false || stripos($line, 'tele') !== false || stripos($line, 'zoom') !== false) {
                return trim($line);
            }
        }

        return null;
    }

    protected function parseCameraSensors(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        preg_match_all('/(\d+(?:\.\d+)?)\s*MP/i', $str, $m);

        return ! empty($m[1]) ? implode('MP + ', $m[1]).'MP' : null;
    }

    protected function parseCameraApertures(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        preg_match_all('/f\/([\d.]+)/i', $str, $m);

        return ! empty($m[0]) ? implode(', ', $m[0]) : null;
    }

    protected function parseCameraFocalLengths(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        preg_match_all('/(\d+)\s*mm/i', $str, $m);

        return ! empty($m[1]) ? implode('mm, ', $m[1]).'mm' : null;
    }

    protected function parsePdaf(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/omnidirectional\s*PDAF/i', $str, $m)) {
            return 'Omnidirectional PDAF';
        }
        if (preg_match('/PDAF/i', $str, $m)) {
            return 'PDAF';
        }

        return null;
    }

    protected function parseZoom(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/([\d.]+)x\s*(optical\s*)?zoom/i', $str, $m)) {
            return $m[1].'x optical zoom';
        }

        return null;
    }

    protected function parseSelfieAperture(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/f\/([\d.]+)/i', $str, $m)) {
            return 'f/'.$m[1];
        }

        return null;
    }

    protected function parseSelfieSensor(?string $str): ?string
    {
        if (! $str) {
            return null;
        }
        if (preg_match('/(\d+(?:\.\d+)?)\s*MP/i', $str, $m)) {
            return $m[1].'MP';
        }

        return null;
    }

    protected function determineCoolingType(string $brand, string $phoneName): ?string
    {
        $name = strtolower($phoneName);
        if (str_contains($name, 'pro') || str_contains($name, 'ultra') || str_contains($name, 'plus')) {
            return 'Vapor Chamber';
        }

        return in_array($brand, ['oneplus', 'samsung', 'xiaomi', 'asus']) ? 'Vapor Chamber' : 'Heat Pipe';
    }

    protected function determineOsOpenness(string $brand, ?string $os): int
    {
        if (in_array($brand, ['google', 'oneplus', 'motorola', 'nothing'])) {
            return 5;
        }
        if (in_array($brand, ['xiaomi', 'poco', 'realme'])) {
            return 3;
        }
        if ($brand === 'samsung') {
            return 4;
        }

        return 3;
    }

    protected function determineCustomRomSupport(string $brand): string
    {
        if (in_array($brand, ['google', 'oneplus', 'nothing', 'motorola'])) {
            return 'Major';
        }
        if (in_array($brand, ['xiaomi', 'poco', 'realme', 'asus'])) {
            return 'Limited';
        }

        return 'None';
    }

    protected function determineTurnipSupport(string $gpu): bool
    {
        if (stripos($gpu, 'mali') !== false) {
            return false;
        }
        if (stripos($gpu, 'immortalis') !== false) {
            return false;
        }
        if (stripos($gpu, 'mediatek') !== false) {
            return false;
        }

        return stripos($gpu, 'adreno') !== false;
    }

    protected function determineTurnipLevel(string $gpu): ?string
    {
        if (stripos($gpu, 'mali') !== false || stripos($gpu, 'immortalis') !== false) {
            return null;
        }

        if (preg_match('/adreno\s*(\d+)/i', $gpu, $m)) {
            $gen = (int) $m[1];
            if ($gen >= 740) {
                return 'Full';
            }
            if ($gen >= 700) {
                return 'Full';
            }
            if ($gen >= 600) {
                return 'Moderate';
            }

            return 'Basic';
        }

        return null;
    }

    protected function determineGpuEmulationTier(string $gpu): ?string
    {
        if (stripos($gpu, 'adreno') !== false) {
            if (preg_match('/\d+/', $gpu, $m) && (int) $m[0] >= 740) {
                return 'Tier 1';
            }

            return 'Tier 2';
        }
        if (stripos($gpu, 'immortalis') !== false) {
            return 'Tier 2';
        }

        return 'Tier 3';
    }

    protected function determineAospAesthetics(string $brand, ?string $os): int
    {
        if (in_array($brand, ['google', 'motorola', 'asus', 'nothing'])) {
            return 5;
        }
        if (in_array($brand, ['oneplus', 'realme'])) {
            return 4;
        }
        if ($brand === 'samsung') {
            return 2;
        }

        return 3;
    }
}
