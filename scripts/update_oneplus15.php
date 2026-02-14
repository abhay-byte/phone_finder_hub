<?php
$phone = App\Models\Phone::find(3);

// 1. Launch
$phone->announced_date = '2025-10-27';
$phone->release_date = '2025-10-28';
$phone->save();

// 2. Platform
if ($phone->platform) {
    $phone->platform->os_details = 'ColorOS 16 (China), OxygenOS 16 (Global)';
    $phone->platform->save();
}

// 3. Body
if ($phone->body) {
    $phone->body->display_brightness = '800 nits (typ), 1800 nits (HBM)';
    $phone->body->measured_display_brightness = '1364 nits';
    $phone->body->pwm_dimming = 'PWM Dimming';
    $phone->body->display_features = '120Hz, Dolby Vision, HDR10+, HDR Vivid, Ultra HDR, 1B colors';
    $phone->body->screen_to_body_ratio = '~90.8%';
    $phone->body->pixel_density = '~450 ppi';
    $phone->body->screen_glass = 'Gorilla Glass 7i or Crystal Shield Glass';
    $phone->body->build_material = 'Glass front (Gorilla Glass Victus 2), aluminum alloy frame, glass back (Gorilla Glass 7i) or fiber-reinforced plastic back. Micro-Arc Oxidation frame (Sand Storm only)';
    $phone->body->touch_sampling_rate = null; // Not specified
    $phone->body->save();
}

// 4. Camera
if ($phone->camera) {
    $phone->camera->main_camera_sensors = 'Main: 1/1.56", Tele: 1/2.76", UW: 1/2.88"';
    $phone->camera->main_camera_apertures = 'Main: f/1.8, Tele: f/2.8, UW: f/2.0';
    $phone->camera->main_camera_focal_lengths = 'Main: 24mm, Tele: 80mm, UW: 16mm';
    $phone->camera->main_camera_ois = 'OIS on Main & Telephoto';
    $phone->camera->video_features = 'Auto HDR, Gyro-EIS, LUT preview, Dolby Vision';
    
    $phone->camera->selfie_camera_aperture = 'f/2.4';
    $phone->camera->selfie_camera_sensor = '1/2.74"';
    $phone->camera->selfie_camera_autofocus = true;
    $phone->camera->selfie_video_capabilities = '4K@30/60fps, 1080p@30/60fps, gyro-EIS';
    $phone->camera->save();
}

// 5. Connectivity
if ($phone->connectivity) {
    $phone->connectivity->audio_quality = '24-bit/192kHz Hi-Res';
    $phone->connectivity->loudspeaker = 'Yes, with stereo speakers';
    $phone->connectivity->loudness_test_result = '-24.8 LUFS (Very good)';
    $phone->connectivity->wifi_bands = 'Dual or Tri-band';
    $phone->connectivity->usb_details = 'USB Type-C 3.2, OTG';
    $phone->connectivity->sar_value = '1.17 W/kg (head), 1.00 W/kg (body)';
    $phone->connectivity->network_bands = 'GSM / HSPA / LTE / 5G';
    $phone->connectivity->save();
}

// 6. Battery
if ($phone->battery) {
    $phone->battery->charging_specs_detailed = '50% in 15 min, 100% in 40 min; UFCS, PPS, PD, QC';
    $phone->battery->reverse_wired = '5W';
    $phone->battery->reverse_wireless = '10W';
    $phone->battery->save();
}

// 7. Benchmarks & Tests
if ($phone->benchmarks) {
    $phone->benchmarks->antutu_v10_score = 2790237;
    $phone->benchmarks->dmark_test_type = 'Wild Life Extreme';
    $phone->benchmarks->battery_active_use_score = '23:07h';
    $phone->benchmarks->energy_label = 'A';
    $phone->benchmarks->repairability_score = 'Class B';
    $phone->benchmarks->free_fall_rating = 'Class C (90 falls)';
    $phone->benchmarks->save();
}

echo "OnePlus 15 data updated successfully!";
