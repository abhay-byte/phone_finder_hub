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
        'selfie_camera_specs',
        'selfie_camera_features',
        'selfie_video_capabilities',
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
