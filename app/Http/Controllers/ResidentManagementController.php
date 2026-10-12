<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\AdminActivityLogger;
use App\Support\SensitiveData;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * FEAT_26_RESIDENTS: Quản lý Cư dân & Khách thuê (Resident Management)
 * Phụ trách: Huỳnh Văn Vĩnh Em
 */
class ResidentManagementController extends Controller
{
    /**
     * Lấy Tenant ID hiện tại được phân quyền theo Multi-tenancy
     */
    private function resolveTenantId(Request $request): int
    {
        $tenantId = $request->attributes->get('scoped_tenant_id')
            ?? Auth::user()?->tenant_id;

        if ($tenantId) {
            return (int) $tenantId;
        }

        $fallbackTenantId = Tenant::where('email', 'contact@smartroom-caugiay.vn')->value('id')
            ?? Tenant::query()->orderBy('id')->value('id')
            ?? 1;

        return (int) $fallbackTenantId;
    }

    /**
     * GET /smartroom/admin/residents
     * Hiển thị danh sách cư dân, khách lưu trú và thân nhân theo từng phòng
     */
    public function index(Request $request): View|JsonResponse
    {
        $tenantId = $this->resolveTenantId($request);
        $user = Auth::user();

        $searchQuery = trim((string) $request->query('q', ''));
        $filterRoomId = $request->query('room_id');
        $filterStatus = $request->query('temporary_status');

        // Thống kê cư dân
        $allTenantResidents = Resident::where('tenant_id', $tenantId)->get();
        $residentStats = [
            'total' => $allTenantResidents->count(),
            'registered' => $allTenantResidents->where('temporary_residence_status', 'registered')->count(),
            'absent' => $allTenantResidents->where('temporary_residence_status', 'absent')->count(),
            'unregistered' => $allTenantResidents->where('temporary_residence_status', 'none')->count(),
        ];

        // Lấy danh sách cư dân kèm thông tin phòng
        $residentsQuery = Resident::with(['room.building', 'contract'])
            ->where('tenant_id', $tenantId);

        if ($searchQuery !== '') {
            $residentsQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('phone', 'like', "%{$searchQuery}%")
                    ->orWhere('cccd', 'like', "%{$searchQuery}%")
                    ->orWhereHas('room', function ($rq) use ($searchQuery) {
                        $rq->where('room_number', 'like', "%{$searchQuery}%");
                    });
            });
        }

        if (!empty($filterRoomId)) {
            $residentsQuery->where('room_id', $filterRoomId);
        }

        if (!empty($filterStatus) && in_array($filterStatus, ['registered', 'absent', 'none'], true)) {
            $residentsQuery->where('temporary_residence_status', $filterStatus);
        }

        $residents = $residentsQuery->orderBy('id', 'desc')->paginate(15)->appends($request->query());

        // Lấy danh sách phòng thuộc tenant
        $rooms = Room::where('tenant_id', $tenantId)
            ->withCount('residents')
            ->orderBy('room_number')
            ->get();

        $emptyRooms = $rooms->filter(fn($r) => $r->status === 'empty');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $residentStats,
                    'residents' => $residents,
                    'rooms' => $rooms,
                ]
            ]);
        }

        return view('admin.residents.index', [
            'residents' => $residents,
            'residentStats' => $residentStats,
            'rooms' => $rooms,
            'emptyRooms' => $emptyRooms,
            'searchQuery' => $searchQuery,
            'filterRoomId' => $filterRoomId,
            'filterStatus' => $filterStatus,
            'currentUser' => $user,
        ]);
    }

    /**
     * POST /smartroom/admin/residents/store
     * Thêm mới thông tin cư dân, số điện thoại, CCCD và bàn giao phòng
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $tenantId = $this->resolveTenantId($request);

        // 1. Chuẩn hóa dữ liệu đầu vào (hỗ trợ cả alias name <-> full_name, cccd <-> id_card)
        $fullName = trim((string) ($request->input('full_name') ?? $request->input('name') ?? ''));
        $phone = trim((string) ($request->input('phone') ?? ''));
        $idCard = trim((string) ($request->input('id_card') ?? $request->input('cccd') ?? ''));
        $roomId = $request->input('room_id');
        $tempResRaw = $request->input('temporary_residence');

        $errors = [];
        $errorCode = null;

        // 2. Kiểm tra Họ và tên cư dân (ERR_26_01)
        if ($fullName === '') {
            $errorCode = 'ERR_26_01';
            $errors['full_name'] = ['Họ và tên cư dân là bắt buộc.'];
        } elseif (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 100) {
            $errorCode = $errorCode ?? 'ERR_26_01';
            $errors['full_name'] = ['Họ và tên cư dân phải từ 2 đến 100 ký tự.'];
        }

        // 3. Kiểm tra Số Căn cước công dân (ERR_26_02, format, ERR_26_03)
        if ($idCard === '') {
            $errorCode = $errorCode ?? 'ERR_26_02';
            $errors['id_card'] = ['Số Căn cước công dân là bắt buộc để đăng ký cư trú.'];
        } elseif (!preg_match('/^[0-9]{12}$/', $idCard)) {
            $errorCode = $errorCode ?? 'ERR_26_02';
            $errors['id_card'] = ['Số CCCD phải gồm đúng 12 chữ số'];
        } else {
            // Kiểm tra trùng lặp CCCD trong hệ thống (ERR_26_03)
            $cccdBlindIndex = SensitiveData::blindIndex($idCard);
            $cccdExists = Resident::query()
                ->where(function ($q) use ($idCard, $cccdBlindIndex) {
                    if ($cccdBlindIndex) {
                        $q->where('cccd_blind_index', $cccdBlindIndex);
                    }
                    $q->orWhere('cccd', $idCard);
                })
                ->exists();

            if ($cccdExists) {
                $errorCode = $errorCode ?? 'ERR_26_03';
                $errors['id_card'] = ['Số Căn cước công dân này đã được đăng ký cho một cư dân khác.'];
            }
        }

        // 4. Kiểm tra Phòng lưu trú (ERR_26_04)
        $room = null;
        if (empty($roomId)) {
            $errorCode = $errorCode ?? 'ERR_26_04';
            $errors['room_id'] = ['Vui lòng chọn phòng lưu trú bàn giao cho cư dân ở.'];
        } else {
            $room = Room::where('id', $roomId)
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$room) {
                // Kiểm tra phòng có tồn tại ở tenant khác không (chặn multi-tenancy leak) hoặc không tồn tại
                $errorCode = $errorCode ?? 'ERR_26_04';
                $errors['room_id'] = ['Vui lòng chọn phòng lưu trú bàn giao cho cư dân ở.'];
            }
        }

        // 5. Kiểm tra Số điện thoại (10 chữ số)
        if ($phone === '') {
            $errors['phone'] = ['Số điện thoại là bắt buộc.'];
        } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
            $errors['phone'] = ['Số điện thoại phải gồm đúng 10 chữ số.'];
        }

        // Nếu có lỗi validation
        if (!empty($errors)) {
            $firstErrorMessage = reset($errors)[0] ?? 'Thông tin không hợp lệ.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'code' => $errorCode ?? 'VALIDATION_ERROR',
                    'message' => $firstErrorMessage,
                    'errors' => $errors,
                ], 422);
            }

            return redirect()->back()
                ->withErrors($errors)
                ->withInput()
                ->with('error', $firstErrorMessage)
                ->with('error_code', $errorCode);
        }

        // 6. Xử lý nghiệp vụ: Lưu vào bảng residents & cập nhật trạng thái phòng
        $isTemporaryResidence = filter_var($tempResRaw, FILTER_VALIDATE_BOOLEAN)
            || $tempResRaw === '1'
            || $tempResRaw === 'true'
            || $tempResRaw === 'on';

        $temporaryStatus = $isTemporaryResidence ? 'registered' : 'none';
        if ($request->has('temporary_residence_status') && in_array($request->input('temporary_residence_status'), ['registered', 'absent', 'none'], true)) {
            $temporaryStatus = $request->input('temporary_residence_status');
        }

        $resident = Resident::create([
            'tenant_id' => $room->tenant_id,
            'room_id' => $room->id,
            'name' => $fullName,
            'phone' => $phone,
            'cccd' => $idCard,
            'email' => $request->input('email') ?? (str_replace(' ', '', strtolower($fullName)) . '@gmail.com'),
            'dob' => $request->input('dob'),
            'hometown' => $request->input('hometown'),
            'start_date' => $request->input('start_date') ?? Carbon::now()->toDateString(),
            'status' => 'active',
            'temporary_residence_status' => $temporaryStatus,
            'version' => 1,
        ]);

        // Cập nhật trạng thái phòng sang Đã thuê (occupied)
        $room->update([
            'status' => 'occupied',
        ]);

        // Ghi log hoạt động quản trị viên
        AdminActivityLogger::log(
            'create',
            'residents',
            'Thêm cư dân ' . $resident->name . ' vào phòng ' . $room->room_number,
            $resident,
            ['room_number' => $room->room_number],
            null,
            $resident->only(['room_id', 'name', 'phone', 'email', 'cccd', 'temporary_residence_status'])
        );

        $successMessage = 'Đã lưu hồ sơ cư dân thành công! Phòng đã tự động chuyển sang trạng thái Đã thuê.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'code' => 'ERR_26_05',
                'message' => $successMessage,
                'data' => $resident->load('room'),
            ], 201);
        }

        return redirect()->route('admin.residents.index')
            ->with('success', $successMessage)
            ->with('code', 'ERR_26_05');
    }
}
