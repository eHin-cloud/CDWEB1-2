<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Building;
use Illuminate\Database\Seeder;

class RoomAmenitiesSeeder extends Seeder
{
    /**
     * Seed dữ liệu 4 tiện ích cốt lõi: Máy lạnh, Máy giặt, Tủ lạnh, Wifi
     * cho các phòng để hỗ trợ người dùng lọc dữ liệu chính xác trên giao diện Renty.
     */
    public function run(): void
    {
        $rooms = Room::query()->with('building')->get();

        if ($rooms->isEmpty()) {
            $this->command?->warn('Không có phòng nào trong cơ sở dữ liệu để cập nhật tiện ích.');
            return;
        }

        // Định nghĩa các gói tiện ích mẫu
        $fullAmenitiesPackage = [
            'ac', 'washer', 'fridge', 'wifi',
            'Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi',
            'water_heater', 'smart_lock', 'balcony'
        ];

        $acWasherWifiPackage = [
            'ac', 'washer', 'wifi',
            'Máy lạnh', 'Máy giặt', 'Wifi',
            'water_heater'
        ];

        $acFridgeWifiPackage = [
            'ac', 'fridge', 'wifi',
            'Máy lạnh', 'Tủ lạnh', 'Wifi',
            'water_heater'
        ];

        $washerFridgeWifiPackage = [
            'washer', 'fridge', 'wifi',
            'Máy giặt', 'Tủ lạnh', 'Wifi'
        ];

        $acWifiPackage = [
            'ac', 'wifi',
            'Máy lạnh', 'Wifi'
        ];

        $updatedCount = 0;
        $full4Count = 0;

        foreach ($rooms as $index => $room) {
            $buildingName = $room->building?->name ?? 'Tòa nhà Renty';
            $roomNum = $room->room_number;

            // Nhóm 1: Các phòng có ĐẦY ĐỦ CẢ 4 MÓN (Máy lạnh, Máy giặt, Tủ lạnh, Wifi)
            // Cứ mỗi 3 phòng thì có 1 phòng full 4 món để đảm bảo lọc luôn có nhiều kết quả
            if ($index % 3 === 0 || in_array($index, [0, 1, 3, 6, 9, 12, 15, 18, 21, 24, 27, 30], true)) {
                $assignedAmenities = $fullAmenitiesPackage;
                $descSuffix = "Phòng cao cấp Full nội thất: Đã trang bị sẵn Máy lạnh (điều hòa Inverter), Máy giặt riêng, Tủ lạnh 2 cánh và Wifi tốc độ cao. Giờ giấc tự do, an ninh camera 24/7.";
                $full4Count++;
            } elseif ($index % 4 === 1) {
                // Nhóm 2: Máy lạnh + Máy giặt + Wifi
                $assignedAmenities = $acWasherWifiPackage;
                $descSuffix = "Phòng hiện đại tiện nghi: Có sẵn Máy lạnh và Máy giặt riêng trong phòng, Wifi cáp quang miễn phí. Phù hợp sinh viên và người đi làm.";
            } elseif ($index % 4 === 2) {
                // Nhóm 3: Máy lạnh + Tủ lạnh + Wifi
                $assignedAmenities = $acFridgeWifiPackage;
                $descSuffix = "Phòng thoáng mát: Đầy đủ Máy lạnh, Tủ lạnh mới và Wifi cực mạnh. Không chung chủ, ban công đón gió.";
            } elseif ($index % 4 === 3) {
                // Nhóm 4: Máy giặt + Tủ lạnh + Wifi
                $assignedAmenities = $washerFridgeWifiPackage;
                $descSuffix = "Phòng trọ đầy đủ tiện nghi sinh hoạt: Có Máy giặt, Tủ lạnh tiết kiệm điện, Wifi dùng riêng.";
            } else {
                // Nhóm 5: Máy lạnh + Wifi
                $assignedAmenities = $acWifiPackage;
                $descSuffix = "Phòng trang bị Máy lạnh Inverter mát lạnh và mạng Wifi ổn định.";
            }

            // Gộp với tiện ích ban đầu nếu có (không làm mất các tiện ích khác)
            $existing = is_array($room->amenities) ? $room->amenities : [];
            $mergedAmenities = array_values(array_unique(array_merge($existing, $assignedAmenities)));

            // Cập nhật description rõ ràng để text search và DOM search tìm thấy ngay lập tức
            $baseDesc = trim(preg_replace('/Phòng (cao cấp|hiện đại|thoáng mát|trang bị).*$/ui', '', (string) ($room->description ?? '')));
            $newDescription = trim(($baseDesc !== '' ? $baseDesc . ". " : '') . $descSuffix);

            $room->update([
                'amenities' => $mergedAmenities,
                'description' => $newDescription,
            ]);

            $updatedCount++;
        }

        $this->command?->info("Đã cập nhật tiện ích thành công cho {$updatedCount} phòng (trong đó có {$full4Count} phòng sở hữu đầy đủ cả 4 tiện ích: Máy lạnh, Máy giặt, Tủ lạnh, Wifi).");
    }
}
