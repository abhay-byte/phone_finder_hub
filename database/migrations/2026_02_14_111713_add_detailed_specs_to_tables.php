<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Launch & Availability
        Schema::table('phones', function (Blueprint $table) {
            $table->date('announced_date')->nullable()->after('release_date');
        });

        Schema::table('spec_platforms', function (Blueprint $table) {
            $table->string('os_details')->nullable()->after('os'); // Global vs China OS
        });

        // 2. Display
        Schema::table('spec_bodies', function (Blueprint $table) {
            $table->string('display_brightness')->nullable()->after('display_features'); // 800 nits typ, 1800 HBM, 4500 peak
            $table->string('pwm_dimming')->nullable()->after('display_brightness');
            $table->string('screen_to_body_ratio')->nullable()->after('display_size');
            $table->string('pixel_density')->nullable()->after('display_resolution');
            $table->string('touch_sampling_rate')->nullable()->after('display_features');
            $table->string('screen_glass')->nullable()->after('display_protection'); // Gorilla Glass 7i etc.
        });

        // 3. Build Variants (already covered by build_material, but let's add specific frame if needed, 
        // strictly following user request for "Micro-Arc Oxidation" which can go in build_material)

        // 4. Camera (Sensors, Aperture, Focal Length)
        Schema::table('spec_cameras', function (Blueprint $table) {
            $table->text('main_camera_sensors')->nullable()->after('main_camera_specs'); // 1/1.56", 1/2.76"...
            $table->text('main_camera_apertures')->nullable()->after('main_camera_sensors'); // f/1.8, f/2.6...
            $table->text('main_camera_focal_lengths')->nullable()->after('main_camera_apertures'); // 24mm, 73mm...
            $table->string('main_camera_ois')->nullable()->after('main_camera_features'); // OIS support details
            
            $table->string('selfie_camera_aperture')->nullable()->after('selfie_camera_specs');
            $table->string('selfie_camera_sensor')->nullable()->after('selfie_camera_aperture');
            $table->boolean('selfie_camera_autofocus')->default(false)->after('selfie_camera_features');
        });

        // 5. Video
        Schema::table('spec_cameras', function (Blueprint $table) {
             $table->text('video_features')->nullable()->after('main_video_capabilities'); // Auto HDR, Gyro-EIS, LUT
        });

        // 6. Audio
        Schema::table('spec_connectivities', function (Blueprint $table) {
            $table->string('audio_quality')->nullable()->after('loudspeaker'); // 24-bit/192kHz Hi-Res
            $table->string('loudness_test_result')->nullable()->after('audio_quality'); // -24.8 LUFS
        });

        // 7. Connectivity & Network
        Schema::table('spec_connectivities', function (Blueprint $table) {
            $table->string('wifi_bands')->nullable()->after('wlan'); // Dual vs Tri-band
            $table->string('usb_details')->nullable()->after('usb'); // USB 3.2, OTG
            $table->string('sar_value')->nullable()->after('sensors');
            $table->string('network_bands')->nullable()->after('positioning'); // Full bands info
        });

        // 8. Battery & Charging
        Schema::table('spec_batteries', function (Blueprint $table) {
            $table->string('charging_specs_detailed')->nullable()->after('charging_wired'); // PPS, PD, QC, 50% in 15 min
            $table->string('reverse_wired')->nullable()->after('charging_reverse');
            $table->string('reverse_wireless')->nullable()->after('reverse_wired');
        });

        // 9. Performance & Tests
        Schema::table('benchmarks', function (Blueprint $table) {
            $table->integer('antutu_v10_score')->nullable()->after('antutu_score');
            $table->string('dmark_test_type')->default('Wild Life Extreme')->after('dmark_wild_life_extreme');
            $table->string('repairability_score')->nullable()->after('battery_endurance_hours');
            $table->string('energy_label')->nullable()->after('repairability_score');
            $table->string('battery_active_use_score')->nullable()->after('battery_endurance_hours'); // 23h 07m
            $table->string('battery_charge_time_100')->nullable()->after('charging_specs_detailed'); // 40 min (wait, this fits in battery table or benchmark? Benchmark is tests)
        });
        
        // Correcting location for charge time to benchmarks as it's a test result
        Schema::table('benchmarks', function (Blueprint $table) {
             $table->string('charge_time_test')->nullable()->after('battery_active_use_score'); // 100% in 40 min
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping columns is tedious, usually we just rollback. 
        // But for completeness:
        Schema::table('phones', function (Blueprint $table) { $table->dropColumn('announced_date'); });
        Schema::table('spec_platforms', function (Blueprint $table) { $table->dropColumn('os_details'); });
        Schema::table('spec_bodies', function (Blueprint $table) { $table->dropColumn(['display_brightness', 'pwm_dimming', 'screen_to_body_ratio', 'pixel_density', 'touch_sampling_rate', 'screen_glass']); });
        Schema::table('spec_cameras', function (Blueprint $table) { $table->dropColumn(['main_camera_sensors', 'main_camera_apertures', 'main_camera_focal_lengths', 'main_camera_ois', 'selfie_camera_aperture', 'selfie_camera_sensor', 'selfie_camera_autofocus', 'video_features']); });
        Schema::table('spec_connectivities', function (Blueprint $table) { $table->dropColumn(['audio_quality', 'loudness_test_result', 'wifi_bands', 'usb_details', 'sar_value', 'network_bands']); });
        Schema::table('spec_batteries', function (Blueprint $table) { $table->dropColumn(['charging_specs_detailed', 'reverse_wired', 'reverse_wireless']); });
        Schema::table('benchmarks', function (Blueprint $table) { $table->dropColumn(['antutu_v10_score', 'dmark_test_type', 'repairability_score', 'energy_label', 'battery_active_use_score', 'charge_time_test']); });
    }
};
