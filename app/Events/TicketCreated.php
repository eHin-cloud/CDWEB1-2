<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ticket $ticket;
    public array $ticketData;

    /**
     * Create a new event instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $room = $ticket->room;
        $resident = $ticket->resident;

        $this->ticketData = [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'category' => $ticket->category,
            'specific_location' => $ticket->specific_location,
            'description' => $ticket->description,
            'image_path' => $ticket->image_path,
            'room_id' => $ticket->room_id,
            'room_number' => $room ? $room->room_number : 'N/A',
            'building_name' => $room && $room->building ? $room->building->name : 'N/A',
            'floor' => $room ? $room->floor : 'N/A',
            'resident_name' => $resident ? $resident->name : 'N/A',
            'resident_phone' => $resident ? $resident->phone : 'N/A',
            'tenant_id' => $ticket->tenant_id,
            'status' => $ticket->status,
            'assigned_to' => $ticket->assigned_to,
            'created_at' => $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
        ];
    }

    /**
     * Kênh phát sóng công khai cho tenant dashboard
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('tenant.' . $this->ticket->tenant_id . '.dashboard'),
        ];
    }

    /**
     * Tên sự kiện broadcast
     */
    public function broadcastAs(): string
    {
        return 'ticket.created';
    }

    /**
     * Dữ liệu gửi kèm
     */
    public function broadcastWith(): array
    {
        return $this->ticketData;
    }
}
