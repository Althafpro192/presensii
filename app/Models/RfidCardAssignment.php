<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfidCardAssignment extends Model
{
    protected $fillable = ['student_id', 'rfid_device_id', 'card_uid', 'status', 'lost_reported_at', 'lost_note'];

    protected $casts = [
        'assigned_at' => 'datetime',
        'lost_reported_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}