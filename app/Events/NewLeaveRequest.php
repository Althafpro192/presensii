<?php

namespace App\Events;

use App\Models\LeaveRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewLeaveRequest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leaveRequest;

    /**
     * Create a new event instance.
     */
    public function __construct(LeaveRequest $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast to a specific school's admin/teacher channel
        return [
            new PrivateChannel('school.' . $this->leaveRequest->school_id . '.leaves'),
        ];
    }
    
    public function broadcastWith(): array
    {
        return [
            'id' => $this->leaveRequest->id,
            'student_name' => $this->leaveRequest->student->name ?? 'Unknown',
            'type' => $this->leaveRequest->type,
            'date' => $this->leaveRequest->date->format('Y-m-d'),
            'status' => $this->leaveRequest->status,
        ];
    }
}
