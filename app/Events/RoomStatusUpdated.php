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
        $this->roomData = [
            'id' => $room->id,
            'room_number' => $room->room_number,
            'floor' => $room->floor,
            'status' => $room->status,
            'status_label' => $room->status_label,
            'badge_class' => $room->badge_class,
            'status_class' => $room->status_class,
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
