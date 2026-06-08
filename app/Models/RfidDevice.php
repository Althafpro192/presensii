<?php

namespace App\Models;

use App\Enums\DeviceStatus;
use App\Models\Traits\Tenantable;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * RfidDevice model – ESP32 device per school.
 */
class RfidDevice extends Model
{
    use Tenantable, Auditable;

    protected $fillable = [
        'school_id', 'device_name', 'api_key', 'ip_address',
        'status', 'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => DeviceStatus::class,
            'last_activity_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (RfidDevice $device) {
            if (empty($device->api_key)) {
                $device->api_key = Str::random(64);
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function cardAssignments(): HasMany
    {
        return $this->hasMany(RfidCardAssignment::class);
    }

    public function writeJobs(): HasMany
    {
        return $this->hasMany(RfidWriteJob::class);
    }
}