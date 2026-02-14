<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecCamera extends Model
{
    protected $fillable = [
        'phone_id',
        'main_camera_specs',
        'main_camera_features',
        'main_video_capabilities',
        'main_camera_sensors',
        'main_camera_apertures',
        'main_camera_focal_lengths',
        'main_camera_ois',
        'video_features',
        'selfie_camera_specs',
        'selfie_camera_features',
        'selfie_video_capabilities',
        'selfie_camera_aperture',
        'selfie_camera_sensor',
        'selfie_camera_autofocus',
        'main_camera_zoom',
        'main_camera_pdaf',
        'selfie_video_features',
        'ultrawide_camera_specs',
        'telephoto_camera_specs',
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
