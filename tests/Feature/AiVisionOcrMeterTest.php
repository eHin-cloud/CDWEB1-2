<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Kiểm thử Tính năng 16: AI Quét số công tơ điện nước (AI Vision OCR - FEAT_16_AI_OCR)
 * Đặc tả theo Bảng 112 – 117 tài liệu NhomC_baocao_2026102.docx.
 * 
 * Bảng kịch bản lỗi:
 * - ERR_16_01: Chưa chọn tệp ảnh công tơ ("Vui lòng chọn hoặc kéo thả ít nhất một ảnh chụp mặt công tơ.")
 * - ERR_16_02: Ảnh chụp quá mờ hoặc lóa sáng ("Ảnh chụp quá mờ hoặc lóa sáng, AI không nhận diện được chữ số rõ ràng.")
 * - ERR_16_03: Tệp không phải ảnh công tơ đo đếm ("Tệp tải lên không phải là ảnh công tơ đo đếm điện nước hợp lệ.")
 * - ERR_16_04: AI nhận diện chỉ số thành công ("Đã nhận diện thành công chỉ số công tơ và tự khớp vào phòng!")
 * 
 * Bảng 116 Các thành phần UI:
 * - camera_input, img_preview, ocr_result, btnConfirmOCR
 */
class AiVisionOcrMeterTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $landlord;
    protected Building $building;
    protected Room $room1;
    protected Room $room2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Chung Cư Mini Thông Minh Cầu Giấy',
            'email' => 'contact@smartroom-caugiay.vn',
            'phone' => '0987654321',
        ]);

        $roleLandlord = Role::firstOrCreate(
            ['slug' => 'landlord'],
            ['name' => 'Landlord', 'description' => 'Chủ cơ sở lưu trú']
        );

        $this->landlord = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $roleLandlord->id,
        ]);

        $this->building = Building::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Tòa Nhà Tri Thức B',
            'address' => 'Số 10 Dịch Vọng Hậu, Cầu Giấy, Hà Nội',
            'total_floors' => 5,
        ]);

        $this->room1 = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '201',
            'floor' => 2,
            'price' => 4500000,
            'status' => 'occupied',
            'electric_meter_serial' => 'EM-101',
            'water_meter_serial' => 'WM-101',
        ]);

        $this->room2 = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '202',
            'floor' => 2,
            'price' => 4800000,
            'status' => 'occupied',
            'electric_meter_serial' => 'EM-102',
            'water_meter_serial' => 'WM-102',
        ]);
    }

    /**
     * Test ERR_16_01: Chưa chọn tệp ảnh công tơ.
     */
    public function test_err_16_01_empty_image_validation(): void
    {
        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.ai.ocr_meter'), [
                'type' => 'electricity',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_16_01',
                'message' => 'Vui lòng chọn hoặc kéo thả ít nhất một ảnh chụp mặt công tơ.',
            ]);
    }

    /**
     * Test ERR_16_02: Ảnh chụp quá mờ hoặc lóa sáng (độ tin cậy < 85%).
     */
    public function test_err_16_02_blurry_or_glared_image(): void
    {
        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.ai.ocr_meter'), [
                'meter_image' => 'data:image/jpeg;base64,blurry_image_data_sample',
                'type' => 'electricity',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_16_02',
                'message' => 'Ảnh chụp quá mờ hoặc lóa sáng, AI không nhận diện được chữ số rõ ràng.',
            ]);
    }

    /**
     * Test ERR_16_03: Ảnh không phải mặt công tơ đo đếm.
     */
    public function test_err_16_03_invalid_meter_image(): void
    {
        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.ai.ocr_meter'), [
                'meter_image' => 'data:image/jpeg;base64,not_meter_image_data_sample',
                'type' => 'electricity',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_16_03',
                'message' => 'Tệp tải lên không phải là ảnh công tơ đo đếm điện nước hợp lệ.',
            ]);
    }

    /**
     * Test ERR_16_04: AI nhận diện chỉ số thành công với tệp base64 và file upload.
     */
    public function test_err_16_04_successful_single_meter_ocr(): void
    {
        // Kiểm tra với dữ liệu Base64
        $response = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.ai.ocr_meter'), [
                'meter_image' => 'data:image/jpeg;base64,valid_meter_reading_clear',
                'type' => 'electricity',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 'ERR_16_04',
                'message' => 'Đã nhận diện thành công chỉ số công tơ và tự khớp vào phòng!',
                'value' => 120,
                'serial_number' => 'EM-101',
            ]);

        // Kiểm tra với File upload thực tế (dùng create để tránh phụ thuộc GD extension)
        $file = UploadedFile::fake()->create('meter_photo.jpg', 100, 'image/jpeg');
        $responseUpload = $this->actingAs($this->landlord)
            ->post(route('smartroom.admin.ai.ocr_meter'), [
                'meter_image' => $file,
                'type' => 'water',
            ]);

        $responseUpload->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 'ERR_16_04',
                'message' => 'Đã nhận diện thành công chỉ số công tơ và tự khớp vào phòng!',
            ]);
    }

    /**
     * Test Bulk OCR công tơ điện nước: Kiểm tra ERR_16_01, ERR_16_04 và khớp phòng tự động theo Số SX.
     */
    public function test_bulk_meter_ocr_scenarios_and_matching(): void
    {
        // 1. Gửi request rỗng -> ERR_16_01
        $emptyResponse = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.ai.ocr_meter_bulk'), [
                'images' => [],
                'type' => 'electricity',
            ]);

        $emptyResponse->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_16_01',
                'message' => 'Vui lòng chọn hoặc kéo thả ít nhất một ảnh chụp mặt công tơ.',
            ]);

        // 2. Gửi mảng ảnh hỗn hợp: hợp lệ (khớp EM-101), mờ (ERR_16_02), và không phải công tơ (ERR_16_03)
        $bulkResponse = $this->actingAs($this->landlord)
            ->postJson(route('smartroom.admin.ai.ocr_meter_bulk'), [
                'images' => [
                    'data:image/jpeg;base64,valid_meter_photo_for_em_101',
                    'data:image/jpeg;base64,blurry_meter_photo',
                    'data:image/jpeg;base64,not_meter_photo',
                ],
                'type' => 'electricity',
            ]);

        $bulkResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 'ERR_16_04',
                'message' => 'Đã nhận diện thành công chỉ số công tơ và tự khớp vào phòng!',
                'total_scanned' => 3,
                'matched_count' => 1,
                'unmatched_count' => 2,
            ]);

        $data = $bulkResponse->json();
        // Phòng 201 phải được khớp tự động nhờ serial 'EM-101'
        $this->assertEquals($this->room1->id, $data['matched'][0]['room_id']);
        $this->assertEquals('201', $data['matched'][0]['room_number']);
        $this->assertEquals('EM-101', $data['matched'][0]['serial_number']);
        $this->assertEquals(120, $data['matched'][0]['value']);

        // Phần tử không khớp thứ nhất (ảnh mờ)
        $this->assertEquals('ERR_16_02', $data['unmatched'][0]['error_code']);
        // Phần tử không khớp thứ hai (không phải công tơ)
        $this->assertEquals('ERR_16_03', $data['unmatched'][1]['error_code']);
    }

    /**
     * Test Bảng 116: Các phần tử giao diện OCR công tơ hiển thị chuẩn xác trong trang Admin.
     */
    public function test_ui_elements_render_correctly(): void
    {
        $response = $this->actingAs($this->landlord)
            ->get(route('smartroom.admin'));

        $response->assertStatus(200);

        // STT 1: camera_input
        $response->assertSee('id="camera_input"', false);

        // STT 2: img_preview
        $response->assertSee('id="img_preview"', false);

        // STT 3: ocr_result
        $response->assertSee('id="ocr_result"', false);

        // STT 4: btnConfirmOCR (nền xanh lá)
        $response->assertSee('id="btnConfirmOCR"', false);
    }
}
