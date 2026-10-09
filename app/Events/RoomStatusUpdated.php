<?php

namespace App\Events;

use App\Models\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Room $room;
    public array $roomData;

    /**
     * Create a new event instance.
     */
    public function __construct(Room $room)
    {
        $this->room = $room;
        $staff = $room->assignedStaff;
        $inspector = $room->inspector;

        $this->roomData = [
            'id' => $room->id,
            'room_number' => $room->room_number,
            'floor' => $room->floor,
            'status' => $room->status,
            'status_label' => $room->status_label,
            'badge_class' => $room->badge_class,
            'status_class' => $room->status_class,
            'housekeeping_status' => $room->housekeeping_status ?: 'dirty',
            'cleaning_status' => $room->cleaning_status ?: 'dirty',
            'priority' => $room->priority ?: 'normal',
            'version' => (int) $room->version,
            'assigned_staff_id' => $room->assigned_staff_id,
            'assigned_staff_name' => $staff?->name ?? 'Chưa phân công',
            'inspected_by' => $room->inspected_by,
            'inspector_name' => $inspector?->name,
            'inspection_notes' => $room->inspection_notes,
            'tenant_id' => $room->tenant_id,
            'updated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Kênh phát sóng công khai hoặc private cho từng tenant
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('tenant.' . $this->room->tenant_id . '.room-matrix'),
            new Channel('room-matrix'),
        ];
    }

    /**
     * Tên sự kiện broadcast
     */
    public function broadcastAs(): string
    {
        return 'room.status.updated';
    }

    /**
     * Dữ liệu gửi kèm qua WebSocket / Event
     */
    public function broadcastWith(): array
    {
        return $this->roomData;
    }
}
