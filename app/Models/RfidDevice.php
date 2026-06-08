<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfidDevice extends Model
{
    protected $fillable = ['school_id', 'device_name', 'api_key', 'ip_address', 'status', 'last_activity_at'];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}