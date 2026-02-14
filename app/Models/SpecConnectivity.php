<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecConnectivity extends Model
{
    protected $fillable = [
        'phone_id',
        'wlan',
        'bluetooth',
        'positioning',
        'nfc',
        'infrared',
        'radio',
        'usb',
        'sensors',
        'loudspeaker',
        'audio_quality',
        'loudness_test_result',
        'wifi_bands',
        'usb_details',
        'sar_value',
        'network_bands',
        'positioning_details',
        'has_3_5mm_jack',
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
