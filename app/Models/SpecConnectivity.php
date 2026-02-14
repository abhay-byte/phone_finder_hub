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
        'jack_3_5mm',
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
