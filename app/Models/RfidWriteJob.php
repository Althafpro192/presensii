<?php

namespace App\Models;

use App\Enums\RfidWriteStatus;
use App\Models\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RfidWriteJob model – queue for bulk RFID card writing.
 */
class RfidWriteJob extends Model
{
    use Tenantable;

    protected $fillable = [
        'school_id', 'rfid_device_id', 'student_id', 'card_uid_to_write',
        'status', 'error_message', 'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => RfidWriteStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(RfidDevice::class, 'rfid_device_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
