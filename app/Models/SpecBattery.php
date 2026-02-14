<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecBattery extends Model
{
    protected $fillable = [
        'phone_id',
        'battery_type',
        'charging_wired',
        'charging_wireless',
        'charging_reverse',
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
