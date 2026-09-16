<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    public const MAX_OCCUPANTS = 5;

    protected $fillable = [
        'building_id',
        'tenant_id',
        'room_number',
        'floor',
        'status',
        'room_type',
        'rental_type',
        'price',
        'price_per_day',
        'price_per_hour',
        'price_extra_hour',
        'cleaning_status',
        'area',
        'amenities',
        'description',
        'image',
        'images',
        'video',
        'version'
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
    ];

    protected $appends = [
        'status_label',
        'badge_class',
        'status_class',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'empty' => 'Trống',
            'occupied' => 'Đã thuê',
            'overdue' => 'Nợ phí',
            'cleaning' => 'Cần dọn dẹp',
            'maintenance' => 'Bảo trì',
            default => 'Không xác định',
        };
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'empty' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            'occupied' => 'bg-red-500/10 text-red-400 border-red-500/20',
            'overdue' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            'cleaning' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
            'maintenance' => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
            default => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'empty' => 'room-empty border-emerald-500/20',
            'occupied' => 'room-occupied border-red-500/20',
            'overdue' => 'room-overdue border-amber-500/20',
            'cleaning' => 'room-cleaning border-orange-500/20',
            'maintenance' => 'room-maintenance border-slate-500/20',
            default => 'border-slate-800/40',
        };
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function electricWaterLogs(): HasMany
    {
        return $this->hasMany(ElectricWaterLog::class);
    }

    public function utilityRecords(): HasMany
    {
        return $this->hasMany(UtilityRecord::class)->orderBy('billing_month', 'desc');
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function contactRequests()
    {
        return $this->hasMany(ContactRequest::class);
    }

    public function equipmentAllocations(): HasMany
    {
        return $this->hasMany(RoomEquipment::class);
    }

    public function hotelBookings(): HasMany
    {
        return $this->hasMany(HotelBooking::class);
    }

    public function activeHotelBooking(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(HotelBooking::class)->where('status', 'checked_in')->latest('id');
    }

    public function isHourly(): bool
    {
        return $this->rental_type === 'hour';
    }

    public function isDaily(): bool
    {
        return $this->rental_type === 'day';
    }

    public function isMonthly(): bool
    {
        return $this->rental_type === 'month' || empty($this->rental_type);
    }

    public function activeResidents(): HasMany
    {
        return $this->hasMany(Resident::class)->where('status', 'active');
    }

    public function occupancyCount(?int $excludeResidentId = null): int
    {
        $residentQuery = $this->activeResidents();

        if ($excludeResidentId) {
            $residentQuery->where('id', '!=', $excludeResidentId);
        }

        $residentIds = $residentQuery->pluck('id');

        return $residentIds->count()
            + ResidentRelative::whereIn('resident_id', $residentIds)->count();
    }

    public function availableOccupancySlots(?int $excludeResidentId = null): int
    {
        return max(0, self::MAX_OCCUPANTS - $this->occupancyCount($excludeResidentId));
    }

    public function canAcceptOccupants(int $additionalOccupants, ?int $excludeResidentId = null): bool
    {
        return $this->availableOccupancySlots($excludeResidentId) >= $additionalOccupants;
    }

    public function syncOccupancyStatus(): void
    {
        if ($this->status === 'maintenance') {
            return;
        }

        $hasActiveResidents = $this->activeResidents()->exists();

        if (!$hasActiveResidents) {
            $this->update(['status' => 'empty']);
            return;
        }

        if ($this->status === 'empty') {
            $this->update(['status' => 'occupied']);
        }
    }

    public static function syncOccupancyStatusById($roomId): void
    {
        if (!$roomId) {
            return;
        }

        $room = self::find($roomId);
        if ($room) {
            $room->syncOccupancyStatus();
        }
    }
}
