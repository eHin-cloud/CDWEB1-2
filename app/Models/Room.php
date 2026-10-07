<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    public const MAX_OCCUPANTS = 5;
    public const ROOM_TYPES = ['standard', 'deluxe', 'vip', 'studio'];
    public const RENTAL_TYPES = ['month', 'day', 'hour'];
    public const HOUSEKEEPING_STATUSES = ['dirty', 'cleaning', 'clean', 'inspected', 'out_of_service'];
    public const HOUSEKEEPING_PRIORITIES = ['urgent', 'high', 'normal', 'low'];

    protected $fillable = [
        'building_id',
        'tenant_id',
        'room_number',
        'floor',
        'status',
        'room_type',
        'rental_type',
        'price',
        'deposit',
        'price_per_day',
        'price_per_hour',
        'price_extra_hour',
        'cleaning_status',
        'housekeeping_status',
        'assigned_staff_id',
        'priority',
        'inspection_notes',
        'inspected_by',
        'inspected_at',
        'area',
        'electric_meter_serial',
        'water_meter_serial',
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
        'deposit' => 'integer',
        'price' => 'integer',
        'inspected_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(function ($room) {
            if ($room->isDirty('status') && $room->status === 'empty') {
                \App\Services\RoomAlertService::notifySubscribersForRoom($room);
            }
        });
    }

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

    public function iotDevices(): HasMany
    {
        return $this->hasMany(IotDevice::class);
    }

    public function iotTelemetries(): HasMany
    {
        return $this->hasMany(IotMeterTelemetry::class);
    }

    public function latestElectricTelemetry()
    {
        return $this->hasOne(IotMeterTelemetry::class)->ofMany(
            ['recorded_at' => 'max', 'id' => 'max'],
            function ($query) {
                $query->where('meter_type', 'electricity');
            }
        );
    }

    public function latestWaterTelemetry()
    {
        return $this->hasOne(IotMeterTelemetry::class)->ofMany(
            ['recorded_at' => 'max', 'id' => 'max'],
            function ($query) {
                $query->where('meter_type', 'water');
            }
        );
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function housekeepingLogs(): HasMany
    {
        return $this->hasMany(HousekeepingLog::class)->latest('id');
    }

    public function isHousekeepingDirty(): bool
    {
        return in_array($this->housekeeping_status, ['dirty'], true) || in_array($this->cleaning_status, ['dirty'], true);
    }

    public function canCheckIn(): bool
    {
        if ($this->status === 'occupied') {
            return false;
        }

        // Tuyệt đối không cho phép Check-in nếu phòng đang ở trạng thái Dirty hoặc chưa hoàn tất nghiệm thu
        if ($this->isHousekeepingDirty()) {
            return false;
        }

        // Phòng phải ở trạng thái Clean hoặc Inspected
        return in_array($this->housekeeping_status, ['clean', 'inspected'], true);
    }

    public function getHousekeepingStatusLabelAttribute(): string
    {
        return match ($this->housekeeping_status) {
            'dirty' => 'Cần dọn (Dirty)',
            'cleaning' => 'Đang dọn (Cleaning)',
            'clean' => 'Đã dọn xong (Clean)',
            'inspected' => 'Đã kiểm tra (Inspected)',
            'out_of_service' => 'Tạm dừng (Out of service)',
            default => 'Cần dọn (Dirty)',
        };
    }

    public function getHousekeepingColorHexAttribute(): string
    {
        return match ($this->housekeeping_status) {
            'dirty' => '#EF4444',        // Đỏ
            'cleaning' => '#F59E0B',     // Vàng cam
            'clean' => '#10B981',        // Xanh lục
            'inspected' => '#0D9488',    // Xanh ngọc
            'out_of_service' => '#64748B', // Xám
            default => '#EF4444',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'Khẩn cấp đón khách',
            'high' => 'Ưu tiên cao',
            'normal' => 'Bình thường',
            'low' => 'Ưu tiên thấp',
            default => 'Bình thường',
        };
    }
}


