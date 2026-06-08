<?php

namespace App\Models;

use App\Enums\CardStatus;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RfidCardAssignment model – tracks RFID cards assigned to students.
 * One student can have multiple cards but only one active at a time.
 */
class RfidCardAssignment extends Model
{
    use Auditable;

    protected $fillable = [
        'student_id', 'rfid_device_id', 'card_uid', 'status',
        'assigned_at', 'lost_reported_at', 'lost_note',
    ];

    protected function casts(): array
    {
        return [
            'status' => CardStatus::class,
            'assigned_at' => 'datetime',
            'lost_reported_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(RfidDevice::class, 'rfid_device_id');
    }

    /**
     * Mark this card as lost.
     */
    public function markAsLost(?string $note = null): void
    {
        $this->update([
            'status' => CardStatus::Lost,
            'lost_reported_at' => now(),
            'lost_note' => $note,
        ]);
    }
}