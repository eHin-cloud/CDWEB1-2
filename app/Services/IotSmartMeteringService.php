<?php

namespace App\Services;

use App\Models\IotDevice;
use App\Models\IotMeterTelemetry;
use App\Models\Room;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IotSmartMeteringService
{
    /**
     * Tiếp nhận và xử lý dữ liệu đo đạc (Telemetry) từ Gateway/ESP32/LoRaWAN
     */
    public function ingestTelemetry(array $data, ?string $apiKey = null): array
    {
        $device = null;

        // 1. Tìm thiết bị qua API Key nếu có
        if ($apiKey) {
            $device = IotDevice::where('api_key', $apiKey)->first();
        }

        // 2. Tìm theo device_code hoặc meter_serial nếu chưa tìm thấy
        if (!$device && !empty($data['device_code'])) {
            $device = IotDevice::where('device_code', $data['device_code'])->first();
        }

        $meterSerial = $data['meter_serial'] ?? ($device ? $device->meter_serial : null);
        $meterType = $data['meter_type'] ?? ($device ? $device->meter_type : 'electricity');

        if (!$device && $meterSerial) {
            $device = IotDevice::where('meter_serial', $meterSerial)
                ->where('meter_type', $meterType)
                ->first();
        }

        // 3. Tự động liên kết với Phòng thông qua số SX công tơ (electric_meter_serial hoặc water_meter_serial)
        $room = null;
        if ($device && $device->room_id) {
            $room = Room::find($device->room_id);
        } elseif ($meterSerial) {
            $room = Room::where('electric_meter_serial', $meterSerial)
                ->orWhere('water_meter_serial', $meterSerial)
                ->first();
        }

        // Nếu thiết bị chưa tồn tại trong danh mục thiết bị, tự động khởi tạo để sẵn sàng hoạt động
        if (!$device && $meterSerial) {
            $tenantId = $room ? $room->tenant_id : (isset($data['tenant_id']) ? (int) $data['tenant_id'] : null);
            $deviceCode = $data['device_code'] ?? ('METER-' . strtoupper($meterType[0]) . '-' . $meterSerial);

            $device = IotDevice::create([
                'tenant_id' => $tenantId,
                'room_id' => $room ? $room->id : null,
                'device_code' => $deviceCode,
                'meter_serial' => $meterSerial,
                'meter_type' => $meterType,
                'protocol' => $data['protocol'] ?? 'esp32_wifi',
                'api_key' => $apiKey ?? IotDevice::generateApiKey(),
                'status' => 'online',
                'last_reading' => (float) ($data['reading'] ?? 0),
                'last_seen_at' => now(),
                'config' => [
                    'max_voltage' => 250,
                    'min_voltage' => 180,
                    'max_power' => 5000,
                    'night_leak_threshold' => 0.05,
                ],
                'firmware_version' => $data['firmware_version'] ?? 'v1.0-iot',
            ]);
        }

        $reading = (float) ($data['reading'] ?? 0);
        $voltage = isset($data['voltage']) ? (float) $data['voltage'] : null;
        $current = isset($data['current']) ? (float) $data['current'] : null;
        $power = isset($data['power']) ? (float) $data['power'] : null;
        $flowRate = isset($data['flow_rate']) ? (float) $data['flow_rate'] : null;
        $recordedAt = isset($data['recorded_at']) ? Carbon::parse($data['recorded_at']) : now();

        // 4. Kiểm tra phát hiện cảnh báo dị thường (Anomaly Detection)
        $alerts = [];
        $hasAnomaly = false;

        if ($meterType === 'electricity') {
            if ($power !== null && $power > 4500) {
                $hasAnomaly = true;
                $alerts[] = "CẢNH BÁO QUÁ TẢI ĐIỆN: Công suất tức thời đạt {$power}W (Vượt ngưỡng 4500W)!";
            }
            if ($voltage !== null && ($voltage > 250 || $voltage < 175)) {
                $hasAnomaly = true;
                $alerts[] = "CẢNH BÁO ĐIỆN ÁP BẤT THƯỜNG: Điện áp {$voltage}V nằm ngoài dải an toàn (175V - 250V)!";
            }
        } elseif ($meterType === 'water') {
            // Kiểm tra rò rỉ nước đêm (từ 01h - 05h sáng mà có lưu lượng liên tục)
            $hour = $recordedAt->hour;
            if ($hour >= 1 && $hour <= 5 && $flowRate !== null && $flowRate > 0.05) {
                $hasAnomaly = true;
                $alerts[] = "CẢNH BÁO RÒ RỈ NƯỚC: Phát hiện nước chảy liên tục ({$flowRate} L/phút) vào ban đêm ({$recordedAt->format('H:i')})!";
            }
        }

        // 5. Lưu bản ghi dữ liệu đo đạc (Telemetry chu kỳ 15 phút)
        $telemetry = IotMeterTelemetry::create([
            'iot_device_id' => $device ? $device->id : null,
            'room_id' => $room ? $room->id : ($device ? $device->room_id : null),
            'meter_type' => $meterType,
            'meter_serial' => $meterSerial ?? ($device ? $device->meter_serial : 'UNKNOWN'),
            'reading' => $reading,
            'voltage' => $voltage,
            'current' => $current,
            'power' => $power,
            'flow_rate' => $flowRate,
            'signal_quality' => $data['signal_quality'] ?? $data['rssi'] ?? -70,
            'battery_level' => $data['battery_level'] ?? null,
            'raw_payload' => $data,
            'recorded_at' => $recordedAt,
        ]);

        // 6. Cập nhật trạng thái thiết bị
        if ($device) {
            $device->update([
                'last_reading' => $reading,
                'last_seen_at' => now(),
                'status' => $hasAnomaly ? 'warning' : 'online',
            ]);
        }

        return [
            'success' => true,
            'message' => 'Đã tiếp nhận telemetry thành công',
            'telemetry_id' => $telemetry->id,
            'device_code' => $device ? $device->device_code : null,
            'room_number' => $room ? $room->room_number : null,
            'current_reading' => $reading,
            'alerts' => $alerts,
        ];
    }

    /**
     * Lấy dữ liệu chuỗi thời gian Realtime của một phòng để vẽ biểu đồ và giám sát
     */
    public function getRoomRealtimeMetrics(int $roomId, string $range = '1h'): array
    {
        $room = Room::findOrFail($roomId);
        $startTime = match ($range) {
            '15m' => now()->subMinutes(15),
            '1h' => now()->subHour(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            default => now()->subHours(24),
        };

        $telemetries = IotMeterTelemetry::where('room_id', $roomId)
            ->where('recorded_at', '>=', $startTime)
            ->orderBy('recorded_at', 'asc')
            ->get();

        // Nếu phòng chưa có dữ liệu đo đạc nào: Tự động khởi tạo 10 điểm đo chu kỳ 1 phút để người dùng trải nghiệm biểu đồ ngay
        if ($telemetries->isEmpty()) {
            $elecSerial = $room->electric_meter_serial ?: ('EM-IOT-' . preg_replace('/[^A-Za-z0-9]/', '', (string)$room->room_number));
            $waterSerial = $room->water_meter_serial ?: ('WM-IOT-' . preg_replace('/[^A-Za-z0-9]/', '', (string)$room->room_number));
            
            if (!$room->electric_meter_serial || !$room->water_meter_serial) {
                $room->update([
                    'electric_meter_serial' => $elecSerial,
                    'water_meter_serial' => $waterSerial,
                ]);
            }

            $baseElec = 1380.0;
            $baseWater = 35.0;

            for ($i = 9; $i >= 0; $i--) {
                $time = now()->subMinutes($i);
                $power = rand(650, 1150);
                $voltage = 220.0 + (rand(-15, 15) / 10);
                $current = round($power / $voltage, 2);
                $elecVal = $baseElec + (9 - $i) * 0.05;
                $waterVal = $baseWater + (9 - $i) * 0.02;
                $flow = round(rand(8, 25) / 10, 2);

                IotMeterTelemetry::create([
                    'room_id' => $room->id,
                    'meter_type' => 'electricity',
                    'meter_serial' => $elecSerial,
                    'reading' => $elecVal,
                    'voltage' => $voltage,
                    'current' => $current,
                    'power' => $power,
                    'signal_quality' => rand(-68, -55),
                    'recorded_at' => $time,
                ]);

                IotMeterTelemetry::create([
                    'room_id' => $room->id,
                    'meter_type' => 'water',
                    'meter_serial' => $waterSerial,
                    'reading' => $waterVal,
                    'flow_rate' => $flow,
                    'signal_quality' => rand(-68, -55),
                    'recorded_at' => $time,
                ]);
            }

            IotDevice::updateOrCreate(
                ['meter_serial' => $elecSerial, 'meter_type' => 'electricity'],
                [
                    'tenant_id' => $room->tenant_id,
                    'room_id' => $room->id,
                    'device_code' => 'ESP32-ELEC-' . $elecSerial,
                    'protocol' => 'esp32_wifi',
                    'status' => 'online',
                    'last_reading' => $baseElec + 9 * 0.05,
                    'last_seen_at' => now(),
                ]
            );

            IotDevice::updateOrCreate(
                ['meter_serial' => $waterSerial, 'meter_type' => 'water'],
                [
                    'tenant_id' => $room->tenant_id,
                    'room_id' => $room->id,
                    'device_code' => 'LORA-WTR-' . $waterSerial,
                    'protocol' => 'lorawan',
                    'status' => 'online',
                    'last_reading' => $baseWater + 9 * 0.02,
                    'last_seen_at' => now(),
                ]
            );

            $telemetries = IotMeterTelemetry::where('room_id', $roomId)
                ->where('recorded_at', '>=', $startTime)
                ->orderBy('recorded_at', 'asc')
                ->get();
        }

        $electricData = [];

        $waterData = [];

        foreach ($telemetries as $item) {
            $point = [
                'time' => $item->recorded_at->format('H:i:s'),
                'reading' => (float) $item->reading,
                'power' => (float) $item->power,
                'voltage' => (float) $item->voltage,
                'current' => (float) $item->current,
                'flow_rate' => (float) $item->flow_rate,
            ];

            if ($item->meter_type === 'electricity') {
                $electricData[] = $point;
            } else {
                $waterData[] = $point;
            }
        }


        // Lấy chỉ số mới nhất trực tiếp để luôn phản ánh bản ghi vừa cập nhật
        $latestElectric = IotMeterTelemetry::where('room_id', $roomId)
            ->where('meter_type', 'electricity')
            ->orderByDesc('recorded_at')
            ->first();
        $latestWater = IotMeterTelemetry::where('room_id', $roomId)
            ->where('meter_type', 'water')
            ->orderByDesc('recorded_at')
            ->first();

        // Tính lượng tiêu thụ hôm nay
        $todayStart = now()->startOfDay();
        $firstElecToday = IotMeterTelemetry::where('room_id', $roomId)
            ->where('meter_type', 'electricity')
            ->where('recorded_at', '>=', $todayStart)
            ->orderBy('recorded_at', 'asc')
            ->first();

        $elecTodayUsed = ($latestElectric && $firstElecToday) 
            ? max(0, round($latestElectric->reading - $firstElecToday->reading, 2)) 
            : 0;

        $firstWaterToday = IotMeterTelemetry::where('room_id', $roomId)
            ->where('meter_type', 'water')
            ->where('recorded_at', '>=', $todayStart)
            ->orderBy('recorded_at', 'asc')
            ->first();

        $waterTodayUsed = ($latestWater && $firstWaterToday)
            ? max(0, round($latestWater->reading - $firstWaterToday->reading, 2))
            : 0;

        return [
            'room' => [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'electric_serial' => $room->electric_meter_serial,
                'water_serial' => $room->water_meter_serial,
            ],
            'latest' => [
                'electric_reading' => $latestElectric ? (float) $latestElectric->reading : null,
                'electric_power' => $latestElectric ? (float) $latestElectric->power : null,
                'electric_voltage' => $latestElectric ? (float) $latestElectric->voltage : null,
                'water_reading' => $latestWater ? (float) $latestWater->reading : null,
                'water_flow_rate' => $latestWater ? (float) $latestWater->flow_rate : null,
                'last_updated' => $latestElectric?->recorded_at?->diffForHumans() ?? 'Chưa có dữ liệu',
            ],
            'consumption_today' => [
                'electricity_kwh' => $elecTodayUsed,
                'water_m3' => $waterTodayUsed,
                'estimated_cost' => ($elecTodayUsed * 3500) + ($waterTodayUsed * 15000),
            ],
            'series' => [
                'electricity' => $electricData,
                'water' => $waterData,
            ],
        ];
    }

    /**
     * Tự động chốt số điện nước từ IoT cho toàn bộ các phòng vào UtilityRecord tháng hiện tại
     */
    public function autoSyncToUtilityRecords(?int $tenantId = null, ?string $billingMonth = null): array
    {
        $currentMonth = $billingMonth ?: Carbon::now()->format('Y-m');
        $query = Room::where('status', '!=', 'empty');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $rooms = $query->select('id', 'tenant_id', 'room_number', 'status', 'electric_meter_serial', 'water_meter_serial')
            ->with(['latestElectricTelemetry', 'latestWaterTelemetry'])
            ->get();
        $syncedCount = 0;

        $skippedCount = 0;
        $details = [];

        foreach ($rooms as $room) {
            $latestElec = $room->latestElectricTelemetry;
            $latestWater = $room->latestWaterTelemetry;

            // Nếu phòng không có dữ liệu IoT nào thì bỏ qua
            if (!$latestElec && !$latestWater) {
                $skippedCount++;
                continue;
            }

            $existing = UtilityRecord::where('room_id', $room->id)
                ->where('billing_month', $currentMonth)
                ->first();

            $newElec = $latestElec ? (int) round($latestElec->reading) : 0;
            $newWater = $latestWater ? (int) round($latestWater->reading) : 0;

            if ($existing) {
                // Giữ nguyên nếu chỉ số cũ cao hơn (tránh lỗi)
                $updateElec = $newElec >= $existing->old_electricity ? $newElec : $existing->new_electricity;
                $updateWater = $newWater >= $existing->old_water ? $newWater : $existing->new_water;

                $existing->update([
                    'new_electricity' => $updateElec,
                    'new_water' => $updateWater,
                    'status' => 'sent',
                ]);

                $syncedCount++;
                $details[] = [
                    'room' => $room->room_number,
                    'status' => 'updated',
                    'elec' => $updateElec,
                    'water' => $updateWater,
                ];
            } else {
                // Lấy chỉ số tháng trước
                $lastMonthBill = UtilityRecord::where('room_id', $room->id)
                    ->orderBy('billing_month', 'desc')
                    ->first();

                $oldElec = $lastMonthBill ? $lastMonthBill->new_electricity : 0;
                $oldWater = $lastMonthBill ? $lastMonthBill->new_water : 0;

                // Chỉ số mới phải lớn hơn hoặc bằng chỉ số cũ
                $finalElec = max($newElec, $oldElec);
                $finalWater = max($newWater, $oldWater);

                UtilityRecord::create([
                    'tenant_id' => $room->tenant_id,
                    'room_id' => $room->id,
                    'billing_month' => $currentMonth,
                    'old_electricity' => $oldElec,
                    'new_electricity' => $finalElec,
                    'old_water' => $oldWater,
                    'new_water' => $finalWater,
                    'electricity_price' => 3500,
                    'water_price' => 15000,
                    'status' => 'sent',
                ]);

                $syncedCount++;
                $details[] = [
                    'room' => $room->room_number,
                    'status' => 'created',
                    'elec' => $finalElec,
                    'water' => $finalWater,
                ];
            }

            $room->update(['status' => 'overdue']);
        }

        return [
            'success' => true,
            'billing_month' => $currentMonth,
            'synced_count' => $syncedCount,
            'skipped_count' => $skippedCount,
            'details' => $details,
        ];
    }

    /**
     * Thống kê tổng quan tình trạng IoT của toàn cơ sở
     */
    public function getFacilitySummary(?int $tenantId = null): array
    {
        $devicesQuery = IotDevice::query();
        if ($tenantId) {
            $devicesQuery->where('tenant_id', $tenantId);
        }

        $devices = $devicesQuery->get();
        $totalDevices = $devices->count();
        $onlineDevices = $devices->filter(fn($d) => $d->is_online)->count();
        $warningDevices = $devices->where('status', 'warning')->count();
        $offlineDevices = $totalDevices - $onlineDevices;

        // Tính tổng công suất tức thời toàn cơ sở (kW)
        $recentElectricTelemetries = IotMeterTelemetry::where('meter_type', 'electricity')
            ->where('recorded_at', '>=', now()->subMinutes(3))
            ->get();
        $totalPowerWatts = $recentElectricTelemetries->sum('power');

        // Tính tổng lưu lượng nước tức thời (m3/h)
        $recentWaterTelemetries = IotMeterTelemetry::where('meter_type', 'water')
            ->where('recorded_at', '>=', now()->subMinutes(3))
            ->get();
        $totalWaterFlow = $recentWaterTelemetries->sum('flow_rate');


        return [
            'total_devices' => $totalDevices,
            'online_devices' => $onlineDevices,
            'offline_devices' => $offlineDevices,
            'warning_devices' => $warningDevices,
            'total_power_kw' => round($totalPowerWatts / 1000, 2),
            'total_water_flow' => round($totalWaterFlow, 2),
            'devices' => $devices->map(function ($d) {
                return [
                    'id' => $d->id,
                    'device_code' => $d->device_code,
                    'meter_serial' => $d->meter_serial,
                    'meter_type' => $d->meter_type,
                    'protocol' => $d->protocol_label,
                    'room_number' => $d->room ? $d->room->room_number : 'Chưa gán',
                    'last_reading' => (float) $d->last_reading,
                    'is_online' => $d->is_online,
                    'status' => $d->status,
                    'last_seen' => $d->last_seen_at ? $d->last_seen_at->diffForHumans() : 'Chưa có dữ liệu',
                ];
            }),
        ];
    }

    /**
     * Giả lập gửi gói tin telemetry từ thiết bị IoT (Hardware Simulator)
     */
    public function simulatePacket(array $params): array
    {
        $roomId = $params['room_id'] ?? null;
        $room = $roomId ? Room::find($roomId) : null;
        $protocol = $params['protocol'] ?? 'esp32_wifi';
        $meterType = $params['meter_type'] ?? 'electricity';

        $serial = $meterType === 'electricity' 
            ? ($room?->electric_meter_serial ?: ($params['meter_serial'] ?? 'EM-SIM-'.rand(1000, 9999)))
            : ($room?->water_meter_serial ?: ($params['meter_serial'] ?? 'WM-SIM-'.rand(1000, 9999)));

        $reading = isset($params['reading']) ? (float) $params['reading'] : ($meterType === 'electricity' ? rand(1200, 3500) : rand(30, 150));
        $voltage = isset($params['voltage']) ? (float) $params['voltage'] : (220 + rand(-5, 5));
        $current = isset($params['current']) ? (float) $params['current'] : (rand(5, 30) / 10);
        $power = isset($params['power']) ? (float) $params['power'] : round($voltage * $current, 1);
        $flowRate = isset($params['flow_rate']) ? (float) $params['flow_rate'] : (rand(1, 15) / 10);

        $payload = [
            'device_code' => $params['device_code'] ?? ($protocol . '-' . strtoupper($meterType[0]) . '-' . $serial),
            'meter_serial' => $serial,
            'meter_type' => $meterType,
            'protocol' => $protocol,
            'reading' => $reading,
            'voltage' => $voltage,
            'current' => $current,
            'power' => $power,
            'flow_rate' => $flowRate,
            'signal_quality' => rand(-75, -55),
            'battery_level' => rand(85, 100),
            'recorded_at' => now()->toIso8601String(),
        ];

        return $this->ingestTelemetry($payload, $params['api_key'] ?? null);
    }
}
