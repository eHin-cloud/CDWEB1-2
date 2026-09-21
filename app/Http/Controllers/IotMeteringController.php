<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLogger;
use App\Models\Room;
use App\Services\IotSmartMeteringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IotMeteringController extends Controller
{
    public function __construct(
        protected IotSmartMeteringService $iotService
    ) {}

    /**
     * API Webhook/Endpoint tiếp nhận dữ liệu telemetry từ Gateway/ESP32/LoRaWAN
     * Hỗ trợ xác thực qua Bearer Token, X-Device-Token hoặc X-API-Key
     */
    public function ingest(Request $request): JsonResponse
    {
        $apiKey = $request->header('X-API-Key') 
            ?: $request->header('X-Device-Token') 
            ?: $request->bearerToken();

        $validated = $request->validate([
            'meter_serial' => 'nullable|string|max:100',
            'device_code' => 'nullable|string|max:100',
            'meter_type' => 'nullable|in:electricity,water',
            'protocol' => 'nullable|in:esp32_wifi,lorawan,modbus_rs485,zigbee,mqtt',
            'reading' => 'required|numeric|min:0',
            'voltage' => 'nullable|numeric',
            'current' => 'nullable|numeric',
            'power' => 'nullable|numeric',
            'flow_rate' => 'nullable|numeric',
            'signal_quality' => 'nullable|integer',
            'battery_level' => 'nullable|integer|between:0,100',
            'recorded_at' => 'nullable|date',
        ]);

        $result = $this->iotService->ingestTelemetry($validated, $apiKey);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Lấy dữ liệu Realtime phụ tải và sản lượng của một phòng cụ thể
     */
    public function roomRealtime(Request $request, $roomId): JsonResponse
    {
        $range = $request->query('range', '24h');
        $metrics = $this->iotService->getRoomRealtimeMetrics((int) $roomId, $range);

        return response()->json([
            'success' => true,
            'data' => $metrics,
        ]);
    }

    /**
     * Lấy thống kê tổng quan toàn bộ hệ thống IoT Smart Metering
     */
    public function facilitySummary(Request $request): JsonResponse
    {
        $tenantId = $request->user()?->tenant_id;
        $summary = $this->iotService->getFacilitySummary($tenantId);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Thực hiện tự động chốt số từ công tơ IoT vào hóa đơn tháng của các phòng
     */
    public function syncBilling(Request $request): JsonResponse
    {
        $tenantId = $request->user()?->tenant_id;
        $billingMonth = $request->input('billing_month');

        $result = $this->iotService->autoSyncToUtilityRecords($tenantId, $billingMonth);

        // Ghi nhật ký kiểm toán hệ thống
        AdminActivityLogger::log(
            'create',
            'utilities',
            "Chốt số điện nước tự động qua IoT Smart Metering ({$result['synced_count']} phòng)",
            null,
            ['billing_month' => $result['billing_month'], 'synced_count' => $result['synced_count']],
            null,
            $result
        );

        return response()->json([
            'success' => true,
            'message' => "Đã đồng bộ và chốt số tự động thành công cho {$result['synced_count']} phòng!",
            'data' => $result,
        ]);
    }

    /**
     * Giả lập gửi gói tin telemetry từ trình duyệt để test và demo
     */
    public function simulate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'meter_type' => 'required|in:electricity,water',
            'protocol' => 'nullable|in:esp32_wifi,lorawan,modbus_rs485,zigbee,mqtt',
            'reading' => 'nullable|numeric|min:0',
            'voltage' => 'nullable|numeric',
            'current' => 'nullable|numeric',
            'power' => 'nullable|numeric',
            'flow_rate' => 'nullable|numeric',
        ]);

        $result = $this->iotService->simulatePacket($validated);

        return response()->json([
            'success' => true,
            'message' => 'Đã phát gói tin telemetry mô phỏng thành công!',
            'data' => $result,
        ]);
    }
}
