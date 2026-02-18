<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecBody extends Model
{
    protected $fillable = [
        'phone_id',
        'dimensions',
        'weight',
        'build_material',
        'cooling_type',
        'sim',
        'ip_rating',
        'colors',
        'display_type',
        'display_size',
        'display_resolution',
        'display_protection',
        'display_brightness',
        'measured_display_brightness',
        'pwm_dimming',
        'screen_to_body_ratio',
        'pixel_density',
        'touch_sampling_rate',
        'screen_glass',
        'screen_area',
        'aspect_ratio',
        'glass_protection_level',
        'display_features',
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
