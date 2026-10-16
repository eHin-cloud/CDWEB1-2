<?php

namespace App\Http\Controllers;

use App\Models\LandlordVerificationRequest;
use App\Models\Tenant;
use App\Services\SecureDocumentService;
use App\Support\SensitiveData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LandlordVerificationController extends Controller
{
    private const PAYMENT_VERIFIED_STATUSES = ['kyc_verified', 'premium_pending', 'premium_verified'];

    public function __construct(private readonly SecureDocumentService $documents)
    {
    }

    public function index()
    {
        return redirect()->to(route('smartroom.admin') . '#verification-section');
    }

    public function submitKyc(Request $request)
    {
        $user = Auth::user();
        $tenant = Tenant::findOrFail($user->tenant_id);

        if (in_array($tenant->verification_status, self::PAYMENT_VERIFIED_STATUSES, true)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Hồ sơ KYC của bạn đã được xác minh.',
                ]);
            }
            return back()->with('success', 'Hồ sơ KYC của bạn đã được xác minh.');
        }

        // Chuẩn hóa alias các trường đầu vào theo chuẩn đặc tả FEAT_09_KYC
        $fileFront = $request->file('cccd_front') ?? $request->file('front_image') ?? $request->file('file_front');
        $fileBack = $request->file('cccd_back') ?? $request->file('back_image') ?? $request->file('file_back');
        $fileProof = $request->file('bank_account_proof') ?? $request->file('holding_image');

        $data = $request->all();
        $data['cccd_number'] = $request->input('cccd_number') ?? $request->input('id_card_number');
        $data['cccd_front'] = $fileFront;
        $data['cccd_back'] = $fileBack;
        $data['bank_account_proof'] = $fileProof;

        $rules = [
            'cccd_number' => ['required', 'regex:/^[0-9]{12}$/'],
            'cccd_front' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'cccd_back' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'bank_account_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'bank_name' => ['nullable', 'string', 'max:80'],
            'bank_account_no' => ['nullable', 'string', 'min:6', 'max:40'],
            'bank_account_name' => ['nullable', 'string', 'max:120'],
            'admin_review_consent' => ['nullable'],
        ];

        $messages = [
            'cccd_number.required' => 'Vui lòng nhập số Căn cước công dân của chủ cơ sở.', // ERR_09_01
            'cccd_number.regex' => 'Số Căn cước công dân phải gồm chính xác 12 chữ số.', // ERR_09_02
            'cccd_front.required' => 'Vui lòng tải lên hình ảnh chụp mặt trước Căn cước công dân.', // ERR_09_03
            'cccd_front.file' => 'Tài liệu xác minh chỉ chấp nhận định dạng JPG, PNG hoặc PDF.', // ERR_09_05
            'cccd_front.mimes' => 'Tài liệu xác minh chỉ chấp nhận định dạng JPG, PNG hoặc PDF.', // ERR_09_05
            'cccd_front.max' => 'Dung lượng tệp tài liệu vượt quá giới hạn cho phép (10MB).', // ERR_09_06
            'cccd_back.required' => 'Vui lòng tải lên hình ảnh chụp mặt sau Căn cước công dân.', // ERR_09_04
            'cccd_back.file' => 'Tài liệu xác minh chỉ chấp nhận định dạng JPG, PNG hoặc PDF.', // ERR_09_05
            'cccd_back.mimes' => 'Tài liệu xác minh chỉ chấp nhận định dạng JPG, PNG hoặc PDF.', // ERR_09_05
            'cccd_back.max' => 'Dung lượng tệp tài liệu vượt quá giới hạn cho phép (10MB).', // ERR_09_06
            'bank_account_proof.mimes' => 'Tài liệu xác minh chỉ chấp nhận định dạng JPG, PNG hoặc PDF.', // ERR_09_05
            'bank_account_proof.max' => 'Dung lượng tệp tài liệu vượt quá giới hạn cho phép (10MB).', // ERR_09_06
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            // Đồng bộ lỗi sang cả các tên field alias theo chuẩn đặc tả FEAT_09_KYC
            $failedMessages = $validator->getMessageBag();
            if ($failedMessages->has('cccd_number')) {
                foreach ($failedMessages->get('cccd_number') as $msg) {
                    $failedMessages->add('id_card_number', $msg);
                }
            }
            if ($failedMessages->has('cccd_front')) {
                foreach ($failedMessages->get('cccd_front') as $msg) {
                    $failedMessages->add('file_front', $msg);
                    $failedMessages->add('front_image', $msg);
                }
            }
            if ($failedMessages->has('cccd_back')) {
                foreach ($failedMessages->get('cccd_back') as $msg) {
                    $failedMessages->add('file_back', $msg);
                    $failedMessages->add('back_image', $msg);
                }
            }
            if ($failedMessages->has('bank_account_proof')) {
                foreach ($failedMessages->get('bank_account_proof') as $msg) {
                    $failedMessages->add('holding_image', $msg);
                }
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $failedMessages->first(),
                    'errors' => $failedMessages->toArray(),
                ], 422);
            }

            return redirect()->to(route('smartroom.admin') . '#verification-section')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $verification = null;

        DB::transaction(function () use ($request, $validated, $user, $tenant, $fileFront, $fileBack, $fileProof, &$verification) {
            LandlordVerificationRequest::where('tenant_id', $tenant->id)
                ->where('type', 'kyc')
                ->where('status', 'pending')
                ->update([
                    'status' => 'superseded',
                    'default_document_access_revoked_at' => now(),
                ]);

            $verification = LandlordVerificationRequest::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'type' => 'kyc',
                'cccd_number' => $validated['cccd_number'],
                'admin_review_consent_given' => true,
                'admin_review_consent_at' => now(),
                'admin_review_consent_ip' => $request->ip(),
                'status' => 'pending',
            ]);

            $directory = 'landlord-verifications/' . $tenant->id;
            $documents = array_filter([
                'cccd_front' => $fileFront,
                'cccd_back' => $fileBack,
                'bank_account_proof' => $fileProof,
            ]);

            foreach ($documents as $type => $file) {
                $payload = $this->documents->store($file, $directory, $type);
                $payload['notes'] = $type === 'cccd_front'
                    ? 'CCCD: ' . SensitiveData::maskNationalId($validated['cccd_number'])
                    : null;

                $verification->documents()->create($payload);
            }

            $updateData = [
                'verification_status' => 'kyc_pending',
                'onboarding_step' => max((int) $tenant->onboarding_step, 2),
            ];

            if (!empty($validated['bank_name'])) {
                $updateData['bank_name'] = $validated['bank_name'];
            }
            if (!empty($validated['bank_account_no'])) {
                $updateData['bank_account_no'] = $validated['bank_account_no'];
            }
            if (!empty($validated['bank_account_name'])) {
                $updateData['bank_account_name'] = mb_strtoupper($validated['bank_account_name']);
            }

            $tenant->update($updateData);
        });

        $successMsg = 'Hồ sơ xác minh KYC đã được gửi thành công! Vui lòng chờ Admin duyệt.'; // ERR_09_07

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'data' => [
                    'verification_id' => $verification?->id,
                    'status' => 'kyc_pending',
                ],
            ]);
        }

        return redirect()
            ->route('smartroom.admin')
            ->with('success', $successMsg);
    }

    public function status(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        $tenant = $user->tenant_id ? Tenant::find($user->tenant_id) : null;

        $latestKyc = $tenant ? LandlordVerificationRequest::where('tenant_id', $tenant->id)
            ->where('type', 'kyc')
            ->latest('id')
            ->first() : null;

        $latestPremium = $tenant ? LandlordVerificationRequest::where('tenant_id', $tenant->id)
            ->where('type', 'premium')
            ->latest('id')
            ->first() : null;

        $data = [
            'tenant_id' => $tenant?->id,
            'verification_status' => $tenant?->verification_status ?? 'unverified',
            'listing_badge' => $tenant?->listing_badge,
            'current_request' => $latestKyc ? [
                'id' => $latestKyc->id,
                'type' => $latestKyc->type,
                'status' => $latestKyc->status,
                'created_at' => $latestKyc->created_at?->toISOString(),
                'reviewed_at' => $latestKyc->reviewed_at?->toISOString(),
                'reject_reason' => $latestKyc->reject_reason,
            ] : null,
            'premium_request' => $latestPremium ? [
                'id' => $latestPremium->id,
                'type' => $latestPremium->type,
                'status' => $latestPremium->status,
                'created_at' => $latestPremium->created_at?->toISOString(),
                'reviewed_at' => $latestPremium->reviewed_at?->toISOString(),
                'reject_reason' => $latestPremium->reject_reason,
            ] : null,
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return redirect()->route('smartroom.admin')->with('kyc_status', $data);
    }

    public function submitPremium(Request $request)
    {
        $user = Auth::user();
        $tenant = Tenant::findOrFail($user->tenant_id);

        if ($tenant->verification_status === 'premium_verified') {
            return back()->with('success', 'Nhà trọ của bạn đã có Tích xanh.');
        }

        if (!in_array($tenant->verification_status, self::PAYMENT_VERIFIED_STATUSES, true)) {
            return back()->with('error', 'Cần hoàn tất KYC nhận tiền trước khi gửi hồ sơ Tích xanh.');
        }

        $request->validate([
            'business_registration_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:8192',
            'fire_safety_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:8192',
            'security_order_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:8192',
            'admin_review_consent' => 'accepted',
        ]);

        DB::transaction(function () use ($request, $user, $tenant) {
            LandlordVerificationRequest::where('tenant_id', $tenant->id)
                ->where('type', 'premium')
                ->where('status', 'pending')
                ->update([
                    'status' => 'superseded',
                    'default_document_access_revoked_at' => now(),
                ]);

            $verification = LandlordVerificationRequest::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'type' => 'premium',
                'admin_review_consent_given' => true,
                'admin_review_consent_at' => now(),
                'admin_review_consent_ip' => $request->ip(),
                'status' => 'pending',
            ]);

            $directory = 'landlord-verifications/' . $tenant->id . '/premium';
            $documents = [
                'business_registration_certificate' => $request->file('business_registration_certificate'),
                'fire_safety_certificate' => $request->file('fire_safety_certificate'),
                'security_order_certificate' => $request->file('security_order_certificate'),
            ];

            foreach ($documents as $type => $file) {
                $verification->documents()->create($this->documents->store($file, $directory, $type));
            }

            $tenant->update([
                'verification_status' => 'premium_pending',
                'onboarding_step' => max((int) $tenant->onboarding_step, 3),
                'listing_badge' => $tenant->listing_badge === 'premium_verified'
                    ? 'premium_verified'
                    : ($tenant->listing_badge ?: 'kyc_verified'),
            ]);
        });

        return redirect()
            ->route('smartroom.admin')
            ->with('success', 'Đã gửi hồ sơ Tích xanh. SmartRoom sẽ kiểm tra giấy tờ ĐKKD, PCCC và ANTT.');
    }
}
