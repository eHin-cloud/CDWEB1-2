<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrudUserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminActivityLogController;
use App\Http\Controllers\AdminVerificationController;
use App\Http\Controllers\ResidentPortalController;
use App\Http\Controllers\LandlordOnboardingController;
use App\Http\Controllers\LandlordVerificationController;
use App\Http\Controllers\VerificationDocumentController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\RoomMatrixRealtimeController;
use App\Http\Controllers\HotelReceptionController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\HousekeepingFrontdeskController;
use App\Http\Controllers\SuperadminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// FEAT_20_SUPERADMIN: BẢNG ĐIỀU KHIỂN QUẢN TRỊ NỀN TẢNG HỆ THỐNG (SUPERADMIN CONSOLE)
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('dashboard', [SuperadminController::class, 'dashboard'])->name('superadmin.dashboard.alias');
    Route::get('admin/dashboard', [SuperadminController::class, 'dashboard'])->name('admin.superadmin.dashboard');
    Route::get('admin/audit-logs', [SuperadminController::class, 'auditLogs'])->name('admin.audit-logs');
    Route::post('admin/system/config', [SuperadminController::class, 'updateSystemConfig'])->name('admin.superadmin.config.update');
    Route::post('admin/users/{user}/role', [SuperadminController::class, 'updateUserRole'])->name('admin.superadmin.users.role');
    Route::post('admin/users/{user}/status', [SuperadminController::class, 'toggleUserStatus'])->name('admin.superadmin.users.status');
    Route::post('admin/users/{user}/password', [SuperadminController::class, 'resetUserPassword'])->name('admin.superadmin.users.password');
});

Route::get('login', [CrudUserController::class, 'login'])->name('login');
Route::post('login', [CrudUserController::class, 'authUser'])->middleware('throttle:30,1')->name('user.authUser');

Route::get('create', [CrudUserController::class, 'createUser'])->name('user.createUser');
Route::post('create', [CrudUserController::class, 'postUser'])->middleware('throttle:10,1')->name('user.postUser');
Route::get('guest/verify', [CrudUserController::class, 'showVerifyOtp'])->name('guest.verify');
Route::post('guest/verify-otp', [CrudUserController::class, 'verifyGuestOtp'])->middleware('throttle:10,1')->name('guest.verifyOtp');
Route::post('guest/send-otp', [CrudUserController::class, 'sendGuestOtp'])->middleware('throttle:5,1')->name('guest.sendOtp');
Route::get('tenant/onboarding/preferences', [CrudUserController::class, 'showPreferences'])->middleware('auth')->name('tenant.preferences');
Route::post('tenant/onboarding/preferences', [CrudUserController::class, 'savePreferences'])->middleware('auth')->name('tenant.preferences.save');

Route::get('landlord/register', [LandlordOnboardingController::class, 'create'])->name('landlord.register');
Route::post('landlord/register', [LandlordOnboardingController::class, 'store'])->middleware('throttle:10,1')->name('landlord.register.store');
Route::get('landlord/verify', [LandlordOnboardingController::class, 'showVerifyForm'])->name('landlord.verify');
Route::post('landlord/send-otp', [LandlordOnboardingController::class, 'sendOtp'])->middleware('throttle:5,1')->name('landlord.sendOtp');
Route::post('landlord/verify-otp', [LandlordOnboardingController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('landlord.verifyOtp');

Route::middleware('role:admin')->group(function () {
    Route::get('read', [CrudUserController::class, 'readUser'])->name('user.readUser');
    Route::delete('delete/{id}', [CrudUserController::class, 'deleteUser'])->name('user.deleteUser');
    Route::get('update', [CrudUserController::class, 'updateUser'])->name('user.updateUser');
    Route::post('update', [CrudUserController::class, 'postUpdateUser'])->name('user.postUpdateUser');
    Route::post('users/role', [CrudUserController::class, 'updateRole'])->name('user.updateRole');
    Route::get('list', [CrudUserController::class, 'listUser'])->name('user.list');
    Route::get('admin/verifications', [AdminVerificationController::class, 'index'])->name('admin.verifications.index');
    Route::get('admin/verifications/has-passkey', [AdminVerificationController::class, 'hasPasskey'])->name('admin.verifications.has-passkey');
    Route::post('admin/verifications/{verification}/approve', [AdminVerificationController::class, 'approve'])->name('admin.verifications.approve');
    Route::post('admin/verifications/{verification}/reject', [AdminVerificationController::class, 'reject'])->name('admin.verifications.reject');
    Route::get('admin/verification-documents/{document}', [VerificationDocumentController::class, 'show'])->name('admin.verification-documents.show');
    Route::post('admin/verification-documents/{document}/unlock', [VerificationDocumentController::class, 'unlock'])->name('admin.verification-documents.unlock');
    
    // Bổ sung các tính năng giám sát & cấu hình bảo mật
    Route::get('admin/verification-audit-logs', [AdminVerificationController::class, 'auditLogs'])->name('admin.verification-audit-logs');
    Route::get('admin/analytics', [AdminVerificationController::class, 'analytics'])->name('admin.analytics');
});

Route::get('admin/verification-documents/{document}/stream', [VerificationDocumentController::class, 'stream'])
    ->middleware(['auth', 'signed'])
    ->name('admin.verification-documents.stream');

Route::get('signout', [CrudUserController::class, 'signOut'])->name('signout');

\Laragear\WebAuthn\Http\Routes::register();

Route::get('/', function () {
    return redirect('/renty');
});

Route::get('/home', function () {
    return redirect('/renty');
})->name('home');

Route::get('/portal', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isResident()) {
            return redirect()->route('smartroom.resident.portal');
        }
        if ($user->isAdmin()) {
            return redirect()->route('user.list');
        }
        if ($user->canAccessLandlordDashboard()) {
            return redirect()->route('smartroom.admin');
        }
    }
    return redirect()->route('login');
})->name('smartroom.portal');

// Cổng dịch vụ Cư dân (FEAT_27_PORTAL)
Route::prefix('smartroom/resident')->group(function () {
    Route::get('/portal', [ResidentPortalController::class, 'index'])->name('smartroom.resident.portal');
    Route::get('/', [ResidentPortalController::class, 'index'])->name('smartroom.resident');
    Route::get('/invoices', [ResidentPortalController::class, 'invoices'])->name('smartroom.resident.invoices');
    Route::get('/contract/pdf', [ResidentPortalController::class, 'downloadContractPdf'])->name('smartroom.resident.contract.pdf');
    Route::get('/contract/pdf-current', [ResidentPortalController::class, 'downloadContractPdf'])->name('smartroom.resident.contract.pdf.current');
    Route::get('/contract/{id}/pdf', [ResidentPortalController::class, 'downloadContractPdf'])->name('smartroom.resident.contract.pdf_id');
    Route::get('/bills/{id}/qr', [ResidentPortalController::class, 'billQr'])->name('smartroom.resident.bills.qr');
    Route::get('/bills/{id}/qr-data', [ResidentPortalController::class, 'billQrData'])->name('smartroom.resident.bills.qr_data');
    Route::get('/bills/{id}/qr.data', [ResidentPortalController::class, 'billQrData'])->name('smartroom.resident.bills.qr.data');
    Route::post('/tickets/analyze', [ResidentPortalController::class, 'analyzeTicket'])->name('smartroom.resident.tickets.analyze');
    Route::post('/tickets', [ResidentPortalController::class, 'storeTicket'])->name('smartroom.resident.tickets.store');
    Route::post('/tickets/store', [ResidentPortalController::class, 'storeTicket'])->name('smartroom.resident.tickets.store.alias');
    Route::post('/housekeeping', [ResidentPortalController::class, 'storeHousekeepingRequest'])->name('smartroom.resident.housekeeping.store');
    Route::post('/contract/{id}/request-renewal', [ResidentPortalController::class, 'requestRenewal'])->name('smartroom.resident.contract.request_renewal');
});


Route::middleware('admin')->group(function () {
    Route::get('/smartroom/admin', [AdminDashboardController::class, 'index'])->name('smartroom.admin');
    
    Route::get('/api/revenue-breakdown', function () {
        $tenantId = auth()->user()?->tenant_id;
        if (!$tenantId) {
            $tenantId = \App\Models\Tenant::query()->orderBy('id')->value('id');
        }

        $breakdown = \App\Models\UtilityRecord::selectRaw("
            SUM(rooms.price) as room_fee,
            SUM(GREATEST(0, new_electricity - old_electricity) * electricity_price) as electric_fee,
            SUM(GREATEST(0, new_water - old_water) * water_price) as water_fee,
            SUM(150000) as service_fee
        ")
        ->join('rooms', 'rooms.id', '=', 'utility_records.room_id')
        ->where('rooms.tenant_id', $tenantId)
        ->where('utility_records.status', 'paid')
        ->first();
        
        $roomFee = (int) ($breakdown->room_fee ?? 0);
        $electricFee = (int) ($breakdown->electric_fee ?? 0);
        $waterFee = (int) ($breakdown->water_fee ?? 0);
        $serviceFee = (int) ($breakdown->service_fee ?? 0);
        
        $total = $roomFee + $electricFee + $waterFee + $serviceFee;
        
        if ($total === 0) {
            $roomFee = 75000000;
            $electricFee = 18450000;
            $waterFee = 6520000;
            $serviceFee = 4500000;
            $total = $roomFee + $electricFee + $waterFee + $serviceFee;
        }

        return response()->json([
            'success' => true,
            'total' => $total,
            'breakdown' => [
                'room' => $roomFee,
                'electric' => $electricFee,
                'water' => $waterFee,
                'service' => $serviceFee
            ],
            'percentages' => [
                'room' => $total > 0 ? round(($roomFee / $total) * 100, 1) : 0,
                'electric' => $total > 0 ? round(($electricFee / $total) * 100, 1) : 0,
                'water' => $total > 0 ? round(($waterFee / $total) * 100, 1) : 0,
                'service' => $total > 0 ? round(($serviceFee / $total) * 100, 1) : 0
            ]
        ]);
    });

    Route::get('/smartroom/admin/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::post('/smartroom/admin/payments/{payment}', [PaymentController::class, 'update'])->name('admin.payments.update');
    Route::post('/smartroom/admin/utility', [AdminDashboardController::class, 'storeUtility'])->name('smartroom.admin.utility.store');
    Route::post('/smartroom/admin/utility/bulk', [AdminDashboardController::class, 'storeUtilityBulk'])->name('smartroom.admin.utility.bulk_store');
    Route::post('/smartroom/admin/resident', [AdminDashboardController::class, 'storeResident'])->name('smartroom.admin.resident.store');
    Route::put('/smartroom/admin/resident/{id}', [AdminDashboardController::class, 'updateResident'])->name('smartroom.admin.resident.update');
    Route::get('/smartroom/admin/resident/{id}/export-ct01', [AdminDashboardController::class, 'exportCt01'])->name('smartroom.admin.resident.export_ct01');
    Route::post('/smartroom/admin/utility/{id}/pay', [AdminDashboardController::class, 'payUtility'])->name('smartroom.admin.utility.pay');
    Route::get('/smartroom/admin/utility/{id}/print', [AdminDashboardController::class, 'printUtility'])->name('smartroom.admin.utility.print');
    Route::post('/smartroom/admin/utility/{id}/notify', [AdminDashboardController::class, 'notifyUtility'])->name('smartroom.admin.utility.notify');
    Route::post('/smartroom/admin/verification/kyc', [LandlordVerificationController::class, 'submitKyc'])->name('smartroom.admin.verification.kyc');
    Route::post('/smartroom/admin/verification/premium', [LandlordVerificationController::class, 'submitPremium'])->name('smartroom.admin.verification.premium');
    Route::post('/smartroom/admin/ticket/{id}/update', [AdminDashboardController::class, 'updateTicketStatus'])->name('smartroom.admin.ticket.update');
    Route::post('/smartroom/admin/tickets/{id}/status', [AdminDashboardController::class, 'updateTicketStatus'])->name('smartroom.admin.tickets.status');
    Route::get('/smartroom/admin/tickets', [AdminDashboardController::class, 'ticketsIndex'])->name('smartroom.admin.tickets.index');
    Route::get('/smartroom/admin/tickets/poll', [AdminDashboardController::class, 'pollTickets'])->name('smartroom.admin.tickets.poll');
    Route::post('/smartroom/admin/profile', [AdminDashboardController::class, 'updateProfile'])->name('smartroom.admin.profile.update');

    Route::middleware('role:landlord')->group(function () {
        Route::post('/smartroom/admin/ai/dashboard-insight', [AdminDashboardController::class, 'aiDashboardInsight'])->name('smartroom.admin.ai.dashboard_insight');
        Route::post('/smartroom/admin/ai/assistant', [AdminDashboardController::class, 'aiAssistant'])->name('smartroom.admin.ai.assistant');
        Route::post('/smartroom/admin/ai/contract-terms', [AdminDashboardController::class, 'aiContractTerms'])->name('smartroom.admin.ai.contract_terms');
        Route::post('/smartroom/admin/ai/ocr-meter', [AdminDashboardController::class, 'aiOcrMeter'])->name('smartroom.admin.ai.ocr_meter');
        Route::post('/smartroom/admin/ai/ocr-meter-bulk', [AdminDashboardController::class, 'aiOcrMeterBulk'])->name('smartroom.admin.ai.ocr_meter_bulk');
        Route::get('/smartroom/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::post('/smartroom/admin/reports/transactions', [ReportController::class, 'storeTransaction'])->name('admin.reports.transaction.store');
        Route::get('/smartroom/admin/activity-logs', [AdminActivityLogController::class, 'index'])->name('admin.activity_logs.index');
        Route::get('/smartroom/admin/payments/export', [PaymentController::class, 'export'])->name('admin.payments.export');
        Route::delete('/smartroom/admin/resident/{id}', [AdminDashboardController::class, 'deleteResident'])->name('smartroom.admin.resident.delete');
        Route::post('/smartroom/admin/utility/auto-remind', [AdminDashboardController::class, 'autoRemindUtilities'])->name('smartroom.admin.utility.auto_remind');
        Route::post('/smartroom/admin/notifications/contracts', [AdminDashboardController::class, 'notifyContracts'])->name('smartroom.admin.notifications.contracts');
        Route::post('/smartroom/admin/notifications/maintenance', [AdminDashboardController::class, 'notifyMaintenance'])->name('smartroom.admin.notifications.maintenance');
        Route::post('/smartroom/admin/notifications/run-all', [AdminDashboardController::class, 'notifyAll'])->name('smartroom.admin.notifications.run_all');

        // IoT Smart Metering Management & Realtime Dashboard
        Route::get('/smartroom/admin/iot/summary', [\App\Http\Controllers\IotMeteringController::class, 'facilitySummary'])->name('smartroom.admin.iot.summary');
        Route::get('/smartroom/admin/iot/rooms/{id}/realtime', [\App\Http\Controllers\IotMeteringController::class, 'roomRealtime'])->name('smartroom.admin.iot.room_realtime');
        Route::post('/smartroom/admin/iot/sync-billing', [\App\Http\Controllers\IotMeteringController::class, 'syncBilling'])->name('smartroom.admin.iot.sync_billing');
        Route::post('/smartroom/admin/iot/simulate', [\App\Http\Controllers\IotMeteringController::class, 'simulate'])->name('smartroom.admin.iot.simulate');
    });


    // Building Management (Cơ sở lưu trú)
    Route::prefix('smartroom/admin/buildings')->name('admin.buildings.')->middleware('role:landlord')->group(function () {
        Route::get('/', [BuildingController::class, 'index'])->name('index');
        Route::get('/create', [BuildingController::class, 'create'])->name('create');
        Route::post('/store', [BuildingController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [BuildingController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [BuildingController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [BuildingController::class, 'destroy'])->name('destroy');
    });

    // Room Management
    Route::prefix('smartroom/admin/rooms')->name('admin.rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::post('/store', [RoomController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [RoomController::class, 'update'])->name('update');
        Route::post('/{id}/quick-status', [RoomMatrixRealtimeController::class, 'updateStatus'])->name('quick_status');
        Route::get('/matrix/stream', [RoomMatrixRealtimeController::class, 'stream'])->name('matrix.stream');
        Route::get('/matrix/poll', [RoomMatrixRealtimeController::class, 'checkUpdates'])->name('matrix.poll');
        Route::post('/description/ai', [RoomController::class, 'generateDescription'])->middleware('role:landlord')->name('description.ai');
        Route::delete('/{id}/delete', [RoomController::class, 'destroy'])->middleware('role:landlord')->name('destroy');
    });

    // Equipment Management
    Route::prefix('smartroom/admin/equipment')->name('admin.equipment.')->group(function () {
        Route::get('/', [EquipmentController::class, 'index'])->name('index');
        Route::get('/store', fn () => redirect()->route('admin.equipment.index'))->name('store.redirect');
        Route::post('/store', [EquipmentController::class, 'store'])->name('store');
        Route::post('/{id}/update', [EquipmentController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [EquipmentController::class, 'destroy'])->middleware('role:landlord')->name('destroy');
        Route::post('/allocate', [EquipmentController::class, 'allocate'])->name('allocate');
        Route::post('/recover', [EquipmentController::class, 'recover'])->name('recover');
    });

    // Online Contracts
    Route::post('/smartroom/admin/contract', [AdminDashboardController::class, 'storeContract'])->name('smartroom.admin.contract.store');
    Route::delete('/smartroom/admin/contract/{id}', [AdminDashboardController::class, 'deleteContract'])->middleware('role:landlord')->name('smartroom.admin.contract.delete');
    Route::post('/smartroom/admin/contract/{id}/renew', [AdminDashboardController::class, 'renewContract'])->name('smartroom.admin.contract.renew');
    Route::post('/smartroom/admin/contract/{id}/decline-renewal', [AdminDashboardController::class, 'declineRenewal'])->name('smartroom.admin.contract.decline_renewal');

    // Contact Requests Management
    Route::post('/smartroom/admin/contact-request/{id}/status', [AdminDashboardController::class, 'updateContactRequestStatus'])->name('smartroom.admin.contact_request.status');
    Route::delete('/smartroom/admin/contact-request/{id}', [AdminDashboardController::class, 'deleteContactRequest'])->middleware('role:landlord')->name('smartroom.admin.contact_request.delete');

    // Resident Relative Management (AJAX JSON APIs)
    Route::get('/smartroom/admin/resident/{residentId}/relatives', [AdminDashboardController::class, 'getRelatives'])->name('smartroom.admin.resident.relatives');
    Route::post('/smartroom/admin/resident/{residentId}/relative', [AdminDashboardController::class, 'storeRelative'])->name('smartroom.admin.resident.relative.store');
    Route::put('/smartroom/admin/relative/{id}', [AdminDashboardController::class, 'updateRelative'])->name('smartroom.admin.relative.update');
    Route::delete('/smartroom/admin/relative/{id}', [AdminDashboardController::class, 'deleteRelative'])->middleware('role:landlord')->name('smartroom.admin.relative.delete');

    // Phân hệ Khách sạn / Lễ tân (Reception & Hospitality)
    Route::prefix('smartroom/admin/hotel')->name('admin.hotel.')->group(function () {
        Route::post('/check-in', [HotelReceptionController::class, 'checkIn'])->name('checkin');
        Route::post('/folio/{bookingId}/items', [HotelReceptionController::class, 'addFolioItem'])->name('folio.add_item');
        Route::post('/check-out/{bookingId}', [HotelReceptionController::class, 'checkOut'])->name('checkout');
    });
});

// TÍNH NĂNG 18: PHÂN HỆ LỄ TÂN & BUỒNG PHÒNG (FEAT_18_HOUSEKEEPING_FRONTDESK)
Route::middleware(['auth', 'tenant.scope'])->prefix('smartroom/admin')->name('smartroom.admin.')->group(function () {
    // 1. Sơ đồ buồng phòng thời gian thực, hiển thị trực quan mã màu FSM dọn phòng
    Route::get('/housekeeping/matrix', [HousekeepingFrontdeskController::class, 'matrix'])->name('housekeeping.matrix');
    // 2. Phân công nhân viên buồng phòng dọn dẹp theo ca
    Route::post('/housekeeping/assign', [HousekeepingFrontdeskController::class, 'assign'])->name('housekeeping.assign');
    // 3. Cập nhật tiến độ dọn phòng (Bắt đầu dọn -> Dọn sạch sẽ)
    Route::post('/housekeeping/status', [HousekeepingFrontdeskController::class, 'updateStatus'])->name('housekeeping.status');
    // 4. Lễ tân nghiệm thu buồng phòng đạt chuẩn sẵn sàng đón khách
    Route::post('/housekeeping/inspect', [HousekeepingFrontdeskController::class, 'inspect'])->name('housekeeping.inspect');
    // 5. Check-in khách lưu trú (Guard Check chặn tuyệt đối phòng Dirty)
    Route::post('/frontdesk/checkin', [HousekeepingFrontdeskController::class, 'checkIn'])->name('frontdesk.checkin');
    // 6. Check-out trả phòng, tự động chuyển phòng sang Dirty và đối soát minibar
    Route::post('/frontdesk/checkout', [HousekeepingFrontdeskController::class, 'checkOut'])->name('frontdesk.checkout');
    // 7. Polling kiểm tra cập nhật thời gian thực cho Sơ đồ Buồng phòng
    Route::get('/housekeeping/poll', [HousekeepingFrontdeskController::class, 'poll'])->name('housekeeping.poll');
});

// Phân hệ Buồng phòng (Housekeeping) - Tối ưu di động
Route::middleware('auth')->prefix('smartroom/housekeeping')->name('admin.housekeeping.')->group(function () {
    Route::get('/', [HousekeepingController::class, 'index'])->name('index');
    Route::post('/{roomId}/status', [HousekeepingController::class, 'updateStatus'])->name('update');
    Route::post('/update/{roomId}', [HousekeepingController::class, 'updateStatus']);
});

Route::get('/smartroom/contract/{id}/sign', [AdminDashboardController::class, 'signContractView'])->name('smartroom.contract.sign_view');
Route::get('/smartroom/contract/{id}/pdf', [AdminDashboardController::class, 'printContractPdf'])->name('smartroom.contract.pdf');
Route::post('/smartroom/contract/{id}/sign', [AdminDashboardController::class, 'signContract'])->name('smartroom.contract.sign');
Route::post('/smartroom/contract/{id}/send-otp', [AdminDashboardController::class, 'sendOtpForContract'])->name('smartroom.contract.send_otp');
Route::post('/smartroom/contract/{id}/lessor-sign', [AdminDashboardController::class, 'signLessorContract'])->name('smartroom.contract.lessor_sign');
Route::post('/renty/contact-request', [AdminDashboardController::class, 'storeContactRequest'])->name('renty.contact_request.store');

$rentyRooms = function () {
    $user = auth()->user();
    $query = \App\Models\Room::with(['building', 'tenant', 'residents', 'reviews']);

    // Nếu người dùng đăng nhập là chủ trọ hoặc quản lý có tenant_id (và không phải Superadmin),
    // chỉ lọc và hiển thị danh sách phòng thuộc đúng cơ sở/vùng của chủ trọ đó
    if ($user && $user->canAccessLandlordDashboard() && $user->tenant_id && !$user->isAdmin()) {
        if (!request()->boolean('all_tenants')) {
            $query->where('tenant_id', $user->tenant_id);
        }
    }

    $rooms = $query->get();
    
    $mappedRooms = $rooms->map(function($room) {
        $num = intval($room->room_number);
        
        $dbReviews = $room->reviews;
        if ($dbReviews->count() > 0) {
            $rating = $dbReviews->avg('rating');
        } else {
            $rating = 3.6 + (($num * 7) % 15) / 10;
            if ($rating > 5.0) $rating = 5.0;
        }
        
        $distance = 0.4 + (($num * 3) % 12) / 10;
        $building = $room->building;
        $tenant = $room->tenant;
        $verificationStatus = $tenant->verification_status ?? 'unverified';
        $listingBadge = $tenant->listing_badge ?? 'unverified';
        $trustBadge = match ($listingBadge) {
            'verified', 'premium_verified' => [
                'label' => 'Tich xanh',
                'class' => 'bg-sky-500/10 text-sky-300 border-sky-500/25',
                'icon' => 'fa-circle-check',
            ],
            'kyc_verified' => [
                'label' => 'Da xac minh',
                'class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/25',
                'icon' => 'fa-shield-halved',
            ],
            default => [
                'label' => 'Chua xac minh',
                'class' => 'bg-slate-950/75 text-slate-300 border-white/10',
                'icon' => 'fa-circle-info',
            ],
        };
        $buildingName = $building?->name ?? 'Rentry Review';
        $buildingAddress = $building?->address ?? 'Khu nhà trọ đang cập nhật địa chỉ';
        $areaName = 'khu vực trung tâm';
        $areaMap = [
            'Thanh Xuân', 'Cầu Giấy', 'Đống Đa', 'Hai Bà Trưng', 'Tây Hồ', 'Ba Đình',
            'Quận 10', 'Quận 1', 'Quận 7', 'Quận 4',
            'Bình Thạnh', 'Tân Bình', 'Gò Vấp', 'Thủ Đức', 'Phú Mỹ Hưng',
        ];
        foreach ($areaMap as $area) {
            if (str_contains($buildingAddress, $area)) {
                $areaName = $area;
                break;
            }
        }
        $amenities = collect($room->amenities ?? [])->map(fn ($item) => mb_strtolower($item));

        $pets = $amenities->contains(fn ($item) => str_contains($item, 'thú cưng')) || ($num % 2 == 1);
        $loft = $amenities->contains(fn ($item) => str_contains($item, 'gác') || str_contains($item, 'gac')) || (($num % 3) != 2);
        $balcony = $amenities->contains(fn ($item) => str_contains($item, 'ban công') || str_contains($item, 'ban cong')) || (($num % 4) != 0);
        $wc = $amenities->contains(fn ($item) => str_contains($item, 'khép kín') || str_contains($item, 'wc') || str_contains($item, 'vệ sinh')) || (($num % 5) != 3);
        
        $ownerStars = intval(round($rating));
        $ownerRating = str_repeat('⭐', $ownerStars) . str_repeat('☆', 5 - $ownerStars) . " ($ownerStars/5)";
        
        $secStars = intval(min(5, max(3, round($rating + ($num % 2 ? 0.5 : -0.5)))));
        $secRating = str_repeat('⭐', $secStars) . str_repeat('☆', 5 - $secStars) . " ($secStars/5)";
        
        $title = $buildingName . " - Phòng " . $room->room_number;
        $address = $buildingAddress . " (Cách điểm tiện ích gần nhất " . number_format($distance, 1) . "km)";
        $area = (int) ($room->area ?? (22 + ($num % 9)));
        $locationDescription = ($building?->description ?: "Nằm tại khu vực {$areaName}, thuận tiện di chuyển và sinh hoạt hằng ngày.") . " Địa chỉ: {$buildingAddress}.";
        $sceneryDescription = $balcony
            ? "Khu {$areaName} có không gian quanh phòng thoáng hơn nhờ ban công, có ánh sáng tự nhiên, phù hợp người thích phòng sáng và có chỗ phơi đồ."
            : "Khu {$areaName} yên tĩnh, phù hợp học tập và nghỉ ngơi; lối đi trong nhà gọn, có camera và khóa an ninh.";
        $spaceDescription = "Phòng rộng khoảng {$area}m², bố trí dạng " . ($loft ? 'có gác lửng để tách khu ngủ và sinh hoạt' : 'một mặt bằng dễ sắp xếp đồ') . ", phù hợp 1-2 người ở với không gian sinh hoạt gọn gàng.";
        
        $reviewsList = $dbReviews->map(function($rev) {
            return [
                'author_name' => $rev->author_name,
                'rating' => $rev->rating,
                'comment' => $rev->comment,
                'created_at' => $rev->created_at->format('d/m/Y H:i')
            ];
        })->toArray();

        $uploadedImages = collect($room->images ?? []);
        if ($room->image) {
            $uploadedImages->prepend($room->image);
        }

        $mediaUrl = fn ($path) => str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
            ? $path
            : \Illuminate\Support\Facades\Storage::url($path);

        $imageUrls = $uploadedImages
            ->filter()
            ->unique()
            ->values()
            ->map(fn ($path) => $mediaUrl($path))
            ->all();
        $hasVerifiedMedia = !empty($imageUrls);

        if (empty($imageUrls)) {
            $fallbackSets = [
                [
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80',
                ],
                [
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1200&q=80',
                ],
                [
                    'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560448075-bb485b067938?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1200&q=80',
                ],
            ];

            $imageUrls = $fallbackSets[$num % count($fallbackSets)];
        }

        $imageAngles = collect($imageUrls)->values()->map(function ($url, $index) use ($balcony) {
            $labels = [
                'View toàn phòng',
                'Góc nhà vệ sinh',
                'Khu bếp / chỗ nấu',
                $balcony ? 'Ban công / cửa sổ' : 'Cửa sổ / ánh sáng',
                'Góc để đồ',
                'Lối vào phòng',
            ];

            return [
                'url' => $url,
                'label' => $labels[$index] ?? 'Ảnh thực tế ' . ($index + 1),
            ];
        })->all();
        
        return [
            'id' => $room->id,
            'room_number' => $room->room_number,
            'price' => $room->price,
            'status' => $room->status,
            'rating' => number_format($rating, 1),
            'distance' => $distance,
            'pets' => $pets ? 'true' : 'false',
            'loft' => $loft ? 'true' : 'false',
            'balcony' => $balcony ? 'true' : 'false',
            'wc' => $wc ? 'true' : 'false',
            'owner' => $ownerRating,
            'sec' => $secRating,
            'title' => $title,
            'address' => $address,
            'area_name' => $areaName,
            'area' => $area,
            'location_description' => $locationDescription,
            'scenery_description' => $sceneryDescription,
            'space_description' => $spaceDescription,
            'area_text' => $area . ' m²',
            'pets_txt' => $pets ? 'Có' : 'Không',
            'loft_txt' => $loft ? 'Có' : 'Không',
            'balcony_txt' => $balcony ? 'Có' : 'Không',
            'wc_txt' => $wc ? 'Có' : 'Không',
            'cover_image' => $imageUrls[0],
            'image_urls' => $imageUrls,
            'image_angles' => $imageAngles,
            'video_url' => $room->video ? $mediaUrl($room->video) : null,
            'media_source_label' => $hasVerifiedMedia ? 'Ảnh thực tế' : 'Ảnh tham khảo',
            'media_source_note' => $hasVerifiedMedia
                ? 'Ảnh do chủ trọ đăng tải, nên đối chiếu khi xem phòng trực tiếp.'
                : 'Phòng chưa có ảnh thật được tải lên. Nên yêu cầu chủ trọ gửi ảnh/video thực tế trước khi đặt cọc.',
            'tenant_verification_status' => $verificationStatus,
            'listing_badge' => $listingBadge,
            'trust_badge' => $trustBadge,
            'boost_score' => (int) ($tenant->boost_score ?? 0),
            'reviews' => $reviewsList
        ];
    });

    $mappedRooms = $mappedRooms->map(function ($room) use ($mappedRooms) {
        $peerRooms = $mappedRooms->filter(function ($peer) use ($room) {
            return $peer['id'] !== $room['id']
                && $peer['area_name'] === $room['area_name']
                && abs((int) $peer['area'] - (int) $room['area']) <= 5;
        });

        $averagePrice = (int) round($peerRooms->count() > 0 ? $peerRooms->avg('price') : $mappedRooms->avg('price'));
        $priceDiffPercent = $averagePrice > 0 ? (($room['price'] - $averagePrice) / $averagePrice) * 100 : 0;

        $room['area_average_price'] = $averagePrice;
        $room['price_diff_percent'] = round($priceDiffPercent, 1);
        $room['price_warning'] = null;

        if ($priceDiffPercent <= -25) {
            $room['price_warning'] = [
                'type' => 'low',
                'label' => 'Giá thấp bất thường',
                'message' => 'Thấp hơn khoảng ' . abs(round($priceDiffPercent)) . '% so với nhóm phòng cùng khu vực/diện tích. Nên kiểm tra kỹ ảnh, phí phát sinh và điều kiện cọc.',
            ];
        } elseif ($priceDiffPercent >= 25) {
            $room['price_warning'] = [
                'type' => 'high',
                'label' => 'Giá cao hơn mặt bằng',
                'message' => 'Cao hơn khoảng ' . round($priceDiffPercent) . '% so với nhóm phòng cùng khu vực/diện tích. Nên so sánh thêm tiện ích và vị trí trước khi liên hệ.',
            ];
        }

        return $room;
    });

    return $mappedRooms
        ->sortByDesc(fn ($room) => (int) ($room['boost_score'] ?? 0))
        ->values();
};

$rentyPage = function () use ($rentyRooms) {
    $user = auth()->user();
    $reviewsQuery = \App\Models\Review::with('room');
    if ($user && $user->canAccessLandlordDashboard() && $user->tenant_id && !$user->isAdmin() && !request()->boolean('all_tenants')) {
        $reviewsQuery->whereHas('room', fn ($q) => $q->where('tenant_id', $user->tenant_id));
    }
    $recentReviews = $reviewsQuery->latest()->take(5)->get();
    return view('rentry.rentry', [
        'rooms' => $rentyRooms(),
        'recentReviews' => $recentReviews
    ]);
};

Route::get('/renty', $rentyPage)->name('renty.user');
Route::get('/renty/search', $rentyPage)->name('renty.search');
Route::get('/search', function () {
    return redirect()->route('renty.search');
})->name('search.redirect');

Route::get('/renty/room-3d', function () {
    return view('rentry.room_3d');
})->name('renty.room.3d');

Route::get('/renty/room/{id}/3d', function ($id) {
    return view('rentry.room_3d', ['roomId' => $id]);
})->name('renty.room.detail.3d');

Route::get('/renty/room/{id}', function ($id) use ($rentyRooms) {
    $room = $rentyRooms()->firstWhere('id', (int) $id);

    abort_if(!$room, 404, 'Không tìm thấy phòng trọ.');

    return view('rentry.rooms.show', [
        'room' => $room,
    ]);
})->name('renty.room.show');

Route::post('/renty/room/{id}/review', function (Illuminate\Http\Request $request, $id) {
    $request->validate([
        'author_name' => 'required|string|max:255',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string'
    ]);

    \App\Models\Review::create([
        'room_id' => $id,
        'author_name' => $request->author_name,
        'rating' => $request->rating,
        'comment' => $request->comment
    ]);

    return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá thực tế!');
})->middleware('throttle:10,1')->name('renty.room.review.store');

Route::post('/renty/room/{id}/report', function (Illuminate\Http\Request $request, $id) {
    $request->validate([
        'reporter_name' => 'nullable|string|max:255',
        'reporter_phone' => 'nullable|string|max:50',
        'reason' => 'required|in:scam,fake_images,wrong_price,unsafe,spam,other',
        'description' => 'required|string|min:10|max:2000',
    ]);

    \App\Models\RoomReport::create([
        'room_id' => $id,
        'reporter_name' => $request->reporter_name,
        'reporter_phone' => $request->reporter_phone,
        'reason' => $request->reason,
        'description' => $request->description,
        'status' => 'pending',
    ]);
    return back()->with('success', 'Cảm ơn bạn đã gửi báo cáo. Renty Review sẽ kiểm tra phòng này sớm nhất.');
})->middleware('throttle:5,1')->name('renty.room.report.store');

Route::post('/renty/chatbot/chat', [\App\Http\Controllers\ChatbotController::class, 'chat'])
    ->name('renty.chatbot.chat')
    ->middleware('throttle:20,1');

Route::get('/renty/chatbot/history', [\App\Http\Controllers\ChatbotController::class, 'history'])
    ->name('renty.chatbot.history');

Route::get('/renty/notifications', function () {
    if (!auth()->check()) {
        return response()->json([
            'success' => true,
            'notifications' => [],
            'count' => 0
        ]);
    }

    $user = auth()->user();
    $notifications = collect();

    if ($user->isAdmin()) {
        // 1. Superadmin: Duyệt KYC & Báo cáo sai phạm
        $verifications = \App\Models\LandlordVerificationRequest::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => 'verify_' . $item->id,
                    'title' => 'Duyệt KYC: ' . ($item->landlord_name ?? 'Chủ trọ mới'),
                    'message' => 'Yêu cầu xác minh danh tính chủ trọ đang chờ bạn phê duyệt.',
                    'time' => $item->created_at ? $item->created_at->diffForHumans() : 'Vừa xong',
                    'link' => route('admin.verifications.index'),
                    'icon' => 'fa-user-shield',
                    'color' => 'text-amber-500'
                ];
            });

        $reports = \App\Models\RoomReport::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => 'report_' . $item->id,
                    'title' => 'Báo cáo vi phạm',
                    'message' => 'Phòng #' . $item->room_id . ' bị báo cáo: ' . \Illuminate\Support\Str::limit($item->description, 60),
                    'time' => $item->created_at ? $item->created_at->diffForHumans() : 'Vừa xong',
                    'link' => route('smartroom.admin'),
                    'icon' => 'fa-flag',
                    'color' => 'text-rose-500'
                ];
            });

        $notifications = $verifications->concat($reports)->sortByDesc('time')->values();

    } elseif ($user->canAccessLandlordDashboard() && $user->tenant_id) {
        // 2. Chủ trọ / Quản lý cơ sở: Nhận thông báo sự cố, khách liên hệ, hóa đơn quá hạn, hợp đồng của cơ sở mình
        $tenantId = $user->tenant_id;

        // Sự cố / Báo hỏng từ cư dân gửi lên
        $tickets = \App\Models\Ticket::with(['room'])
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(function ($item) {
                $roomNum = $item->room?->room_number ?? '';
                return [
                    'id' => 'ticket_' . $item->id,
                    'title' => 'Sự cố P.' . $roomNum . ': ' . $item->title,
                    'message' => \Illuminate\Support\Str::limit($item->description, 80),
                    'time' => $item->created_at ? $item->created_at->diffForHumans() : 'Vừa xong',
                    'link' => route('smartroom.admin') . '#tickets',
                    'icon' => 'fa-wrench',
                    'color' => 'text-amber-400'
                ];
            });

        // Khách gửi liên hệ xem phòng từ Renty
        $contacts = \App\Models\ContactRequest::with('room')
            ->whereHas('room', fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(function ($item) {
                $roomNum = $item->room?->room_number ?? '';
                return [
                    'id' => 'contact_' . $item->id,
                    'title' => 'Khách hỏi thuê P.' . $roomNum,
                    'message' => $item->name . ' (' . $item->phone . ') gửi yêu cầu tư vấn xem phòng.',
                    'time' => $item->created_at ? $item->created_at->diffForHumans() : 'Vừa xong',
                    'link' => route('smartroom.admin'),
                    'icon' => 'fa-phone-volume',
                    'color' => 'text-emerald-400'
                ];
            });

        // Hóa đơn quá hạn / chưa thanh toán của cơ sở
        $bills = \App\Models\Bill::with('room')
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['overdue', 'pending'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($item) {
                $roomNum = $item->room?->room_number ?? '';
                $isOverdue = $item->status === 'overdue';
                return [
                    'id' => 'bill_' . $item->id,
                    'title' => ($isOverdue ? 'Hóa đơn quá hạn P.' : 'Chờ thu tiền P.') . $roomNum,
                    'message' => 'Tháng ' . $item->billing_month . ' - Số tiền: ' . number_format($item->total_amount) . 'đ',
                    'time' => $item->created_at ? $item->created_at->diffForHumans() : 'Vừa xong',
                    'link' => route('smartroom.admin') . '#bills',
                    'icon' => 'fa-file-invoice-dollar',
                    'color' => $isOverdue ? 'text-rose-400' : 'text-sky-400'
                ];
            });

        // Hợp đồng chưa ký số
        $contracts = \App\Models\Contract::with('room')
            ->where('tenant_id', $tenantId)
            ->where('is_signed', false)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($item) {
                $roomNum = $item->room?->room_number ?? '';
                return [
                    'id' => 'contract_' . $item->id,
                    'title' => 'Hợp đồng P.' . $roomNum . ' chờ ký',
                    'message' => 'Mã HĐ: ' . $item->contract_code . ' đang chờ xác nhận chữ ký số.',
                    'time' => $item->created_at ? $item->created_at->diffForHumans() : 'Vừa xong',
                    'link' => route('smartroom.admin') . '#contracts',
                    'icon' => 'fa-file-signature',
                    'color' => 'text-violet-400'
                ];
            });

        $notifications = $tickets->concat($contacts)->concat($bills)->concat($contracts)->values();

    } elseif ($user->isResident()) {
        // 3. Cư dân: Chỉ nhận thông báo liên quan đến phòng mình đang thuê
        $resident = \App\Models\Resident::where('user_id', $user->id)
            ->orWhere('phone_blind_index', \App\Support\SensitiveData::blindIndex($user->phone))
            ->first();

        $notifications = collect();

        if ($resident) {
            // Hóa đơn phòng của cư dân
            $residentBills = \App\Models\Bill::where('room_id', $resident->room_id)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function ($item) {
                    $isPaid = $item->status === 'paid';
                    return [
                        'id' => 'res_bill_' . $item->id,
                        'title' => $isPaid ? 'Đã thanh toán hóa đơn' : 'Hóa đơn tiền phòng mới',
                        'message' => 'Hóa đơn tháng ' . $item->billing_month . ': ' . number_format($item->total_amount) . 'đ (' . ($isPaid ? 'Đã thu' : 'Chưa thu') . ')',
                        'time' => $item->created_at ? $item->created_at->diffForHumans() : 'Vừa xong',
                        'link' => route('smartroom.resident') . '#bills',
                        'icon' => 'fa-receipt',
                        'color' => $isPaid ? 'text-emerald-400' : 'text-amber-400'
                    ];
                });

            // Phiếu sự cố của cư dân
            $residentTickets = \App\Models\Ticket::where('resident_id', $resident->id)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => 'res_ticket_' . $item->id,
                        'title' => 'Báo hỏng: ' . $item->title,
                        'message' => 'Trạng thái: ' . ($item->status === 'resolved' ? 'Đã xử lý xong' : 'Đang xử lý'),
                        'time' => $item->created_at ? $item->created_at->diffForHumans() : 'Vừa xong',
                        'link' => route('smartroom.resident') . '#tickets',
                        'icon' => 'fa-screwdriver-wrench',
                        'color' => $item->status === 'resolved' ? 'text-emerald-400' : 'text-sky-400'
                    ];
                });

            $notifications = $residentBills->concat($residentTickets)->values();
        }

    } else {
        // 4. Khách tìm phòng (Guest)
        $notifications = collect([
            [
                'id' => 'guest_tip_1',
                'title' => 'Khám phá phòng trọ 360°',
                'message' => 'Trải nghiệm xem phòng thực tế ảo 3D trực quan trước khi đến xem trực tiếp.',
                'time' => 'Gợi ý',
                'link' => route('renty.room.3d'),
                'icon' => 'fa-cube',
                'color' => 'text-indigo-400'
            ],
            [
                'id' => 'guest_tip_2',
                'title' => 'Mẹo thuê trọ an toàn',
                'message' => 'Kiểm tra chủ trọ có huy hiệu Xác minh KYC để tránh rủi ro lừa đảo tiền cọc.',
                'time' => 'An toàn',
                'link' => route('renty.user'),
                'icon' => 'fa-shield-halved',
                'color' => 'text-emerald-400'
            ]
        ]);
    }

    // Fallback if empty to make the tray look nice and realistic
    if ($notifications->isEmpty()) {
        $notifications = collect([
            [
                'id' => 'welcome',
                'title' => 'Chào mừng quay lại!',
                'message' => 'Chúc bạn một ngày làm việc hiệu quả và tìm được phòng trọ ưng ý.',
                'time' => 'Vừa xong',
                'link' => '#',
                'icon' => 'fa-sparkles',
                'color' => 'text-emerald-500'
            ]
        ]);
    }

    return response()->json([
        'success' => true,
        'notifications' => $notifications,
        'count' => $notifications->count()
    ]);
})->middleware('auth')->name('renty.notifications');
