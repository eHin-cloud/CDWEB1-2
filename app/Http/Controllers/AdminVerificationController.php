<?php

namespace App\Http\Controllers;

use App\Models\LandlordVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminVerificationController extends Controller
{
    public function index()
    {
        $requests = LandlordVerificationRequest::with(['tenant', 'user', 'documents'])
            ->latest()
            ->paginate(30);

        $stats = [
            'total' => LandlordVerificationRequest::count(),
            'pending' => LandlordVerificationRequest::where('status', 'pending')->count(),
            'approved' => LandlordVerificationRequest::where('status', 'approved')->count(),
            'rejected' => LandlordVerificationRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.verifications.index', compact('requests', 'stats'));
    }

    public function pending(Request $request)
    {
        $requests = LandlordVerificationRequest::with(['tenant', 'user', 'documents'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(30);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Danh sách hồ sơ định danh chủ trọ đang chờ thẩm định',
                'data' => $requests,
            ]);
        }

        $stats = [
            'total' => LandlordVerificationRequest::count(),
            'pending' => LandlordVerificationRequest::where('status', 'pending')->count(),
            'approved' => LandlordVerificationRequest::where('status', 'approved')->count(),
            'rejected' => LandlordVerificationRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.verifications.index', compact('requests', 'stats'));
    }

    public function hasPasskey()
    {
        $hasKey = Auth::user()->webAuthnCredentials()->whereEnabled()->exists();
        return response()->json(['has_passkey' => $hasKey]);
    }

    public function approve(Request $request, $verification)
    {
        if (!($verification instanceof LandlordVerificationRequest)) {
            $verification = LandlordVerificationRequest::findOrFail($verification);
        }

        DB::transaction(function () use ($verification) {
            $verification->loadMissing(['tenant', 'user', 'documents']);
            $tenant = $verification->tenant;

            if ($verification->type === 'kyc') {
                $tenant?->update([
                    'verification_status' => 'kyc_verified',
                    'listing_badge' => 'kyc_verified',
                    'boost_score' => max((int) ($tenant->boost_score ?? 0), 100),
                    'onboarding_step' => max((int) ($tenant->onboarding_step ?? 1), 2),
                    'verified_at' => now(),
                    'kyc_verified_at' => now(),
                ]);

                if ($verification->user?->role === 'unverified_landlord') {
                    $verification->user->update(['role' => 'landlord']);
                }

                if ($verification->user?->landlordProfile) {
                    $verification->user->landlordProfile->update(['status' => 'verified']);
                }
            }

            if ($verification->type === 'premium') {
                $tenant?->update([
                    'verification_status' => 'premium_verified',
                    'listing_badge' => 'premium_verified',
                    'boost_score' => max((int) ($tenant->boost_score ?? 0), 100),
                    'onboarding_step' => max((int) ($tenant->onboarding_step ?? 1), 3),
                    'verified_at' => now(),
                    'premium_verified_at' => now(),
                ]);

                if ($verification->user?->role === 'unverified_landlord') {
                    $verification->user->update(['role' => 'landlord']);
                }

                if ($verification->user?->landlordProfile) {
                    $verification->user->landlordProfile->update(['status' => 'verified']);
                }
            }

            $verification->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'default_document_access_revoked_at' => now(),
                'reject_reason' => null,
                'congratulated_at' => null,
            ]);

            $verification->documents()->update(['status' => 'approved']);

            // Gửi thông báo chúc mừng phê duyệt KYC thành công qua NotificationLog
            try {
                \App\Models\NotificationLog::create([
                    'tenant_id' => $tenant?->id,
                    'type' => 'kyc_verification_approved',
                    'channel' => 'system_email',
                    'recipient_name' => $verification->user?->name ?? 'Chủ cơ sở',
                    'recipient_contact' => $verification->user?->email ?? $verification->user?->phone,
                    'subject' => 'Chúc mừng! Hồ sơ KYC đã được phê duyệt & Cấp Tích Xanh',
                    'message' => 'Hồ sơ xác thực định danh của bạn đã được Superadmin phê duyệt thành công. Cơ sở lưu trú đã được cấp Tích Xanh uy tín trên hệ thống Renty & SmartRoom.',
                    'status' => 'sent',
                    'target_type' => 'landlord_verification_request',
                    'target_id' => $verification->id,
                    'meta' => [
                        'verification_id' => $verification->id,
                        'type' => $verification->type,
                        'badge' => $tenant?->listing_badge,
                    ],
                    'sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Log silently if notification fails
            }
        });

        $message = 'Đã phê duyệt hồ sơ KYC và cấp Tích Xanh uy tín cho cơ sở thành công!';

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'verification_id' => $verification->id,
                    'status' => 'approved',
                    'is_verified' => true,
                    'listing_badge' => $verification->tenant?->listing_badge,
                ],
            ]);
        }

        return back()->with('success', $message);
    }

    public function reject(Request $request, $verification)
    {
        if (!($verification instanceof LandlordVerificationRequest)) {
            $verification = LandlordVerificationRequest::findOrFail($verification);
        }

        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'reject_reason.required' => 'Vui lòng nhập lý do từ chối để chủ cơ sở nắm rõ thông tin bổ sung.',
            'reject_reason.min' => 'Nội dung lý do từ chối phải có độ dài tối thiểu 10 ký tự.',
        ]);

        DB::transaction(function () use ($verification, $validated) {
            $verification->loadMissing(['tenant', 'user', 'documents']);
            $tenant = $verification->tenant;

            if ($verification->type === 'kyc') {
                $tenant?->update([
                    'verification_status' => 'rejected',
                    'listing_badge' => 'unverified',
                    'boost_score' => 0,
                ]);
            }

            if ($verification->type === 'premium') {
                $tenant?->update([
                    'verification_status' => 'kyc_verified',
                    'listing_badge' => 'kyc_verified',
                    'boost_score' => 0,
                ]);
            }

            $verification->update([
                'status' => 'rejected',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'default_document_access_revoked_at' => now(),
                'reject_reason' => $validated['reject_reason'],
            ]);

            $verification->documents()->update(['status' => 'rejected']);

            // Gửi thông báo / email giải thích lý do từ chối cho chủ trọ qua NotificationLog
            try {
                \App\Models\NotificationLog::create([
                    'tenant_id' => $tenant?->id,
                    'type' => 'kyc_verification_rejected',
                    'channel' => 'system_email',
                    'recipient_name' => $verification->user?->name ?? 'Chủ cơ sở',
                    'recipient_contact' => $verification->user?->email ?? $verification->user?->phone,
                    'subject' => 'Thông báo kết quả kiểm duyệt hồ sơ định danh KYC',
                    'message' => 'Hồ sơ KYC của bạn chưa được duyệt với lý do: ' . $validated['reject_reason'] . '. Vui lòng kiểm tra lại thông tin và nộp lại hồ sơ.',
                    'status' => 'sent',
                    'target_type' => 'landlord_verification_request',
                    'target_id' => $verification->id,
                    'meta' => [
                        'verification_id' => $verification->id,
                        'reject_reason' => $validated['reject_reason'],
                    ],
                    'sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Log silently if notification fails
            }
        });

        $message = 'Đã từ chối hồ sơ xác minh và gửi thông báo giải thích cho chủ trọ.';

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'verification_id' => $verification->id,
                    'status' => 'rejected',
                    'reject_reason' => $validated['reject_reason'],
                ],
            ]);
        }

        return back()->with('success', $message);
    }

    public function auditLogs(Request $request)
    {
        $query = \App\Models\AdminAccessLog::with(['admin', 'targetLandlord', 'document'])
            ->latest();

        if ($request->filled('admin_id')) {
            $query->where('admin_user_id', $request->admin_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('document_type', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20);
        
        $admins = \App\Models\User::where('role', 'admin')
            ->orWhereHas('roleRecord', function($q) {
                $q->where('slug', 'admin');
            })->get();

        return view('admin.verifications.audit_logs', compact('logs', 'admins'));
    }



    public function analytics()
    {
        // 1. Tỷ lệ trạng thái phòng trọ toàn hệ thống
        $roomStats = [
            'total' => \App\Models\Room::count(),
            'occupied' => \App\Models\Room::where('status', 'occupied')->count(),
            'empty' => \App\Models\Room::where('status', 'empty')->count(),
            'overdue' => \App\Models\Room::where('status', 'overdue')->count(),
        ];

        // 2. Doanh thu từ hóa đơn đã thanh toán thành công theo tháng
        $billingData = \App\Models\Bill::selectRaw("
            billing_month,
            SUM(total_amount) as total_revenue,
            COUNT(id) as paid_count
        ")
        ->where('status', 'paid')
        ->groupBy('billing_month')
        ->orderBy('billing_month')
        ->get();

        // 3. Số lượng chủ trọ mới gia nhập hệ thống theo tháng (trong vòng 6 tháng qua)
        $landlordsData = \App\Models\User::selectRaw("
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(id) as total_landlords
        ")
        ->where(function($q) {
            $q->where('role', 'landlord')
              ->orWhere('role', 'unverified_landlord')
              ->orWhereHas('roleRecord', function($sq) {
                  $sq->whereIn('slug', ['landlord', 'unverified_landlord']);
              });
        })
        ->groupBy('month')
        ->orderBy('month')
        ->limit(6)
        ->get();

        // 4. Số lượng cư dân mới gia nhập hệ thống theo tháng
        $residentsData = \App\Models\Resident::selectRaw("
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(id) as total_residents
        ")
        ->groupBy('month')
        ->orderBy('month')
        ->limit(6)
        ->get();

        return view('admin.verifications.analytics', compact(
            'roomStats',
            'billingData',
            'landlordsData',
            'residentsData'
        ));
    }
}
