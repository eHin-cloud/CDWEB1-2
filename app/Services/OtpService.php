<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Facades\Log;

class OtpService
{
    const COOLDOWN_SECONDS = 60;
    const EXPIRE_MINUTES = 5;
    const MAX_ATTEMPTS = 5;

    /**
     * Tạo mã OTP mới có kiểm tra cooldown 60 giây và thời hạn 5 phút
     */
    public static function generateOtp(string $target, string $type = 'register', bool $force = false): array
    {
        $target = trim($target);

        // Tìm bản ghi OTP mới nhất của target + type
        $latest = OtpCode::where('target', $target)
            ->where('type', $type)
            ->latest('id')
            ->first();

        // Kiểm tra Cooldown 60s
        if ($latest && !$force && $latest->last_sent_at) {
            $secondsPassed = (int) now()->diffInSeconds($latest->last_sent_at);
            if ($secondsPassed < self::COOLDOWN_SECONDS) {
                $retryAfter = self::COOLDOWN_SECONDS - $secondsPassed;
                return [
                    'success' => false,
                    'cooldown' => true,
                    'retry_after' => $retryAfter,
                    'message' => "Vui lòng đợi {$retryAfter} giây trước khi yêu cầu mã OTP mới."
                ];
            }
        }

        // Sinh mã ngẫu nhiên 6 chữ số
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(self::EXPIRE_MINUTES);

        // Lưu vào DB
        $record = OtpCode::create([
            'target' => $target,
            'code' => $otp,
            'type' => $type,
            'attempts' => 0,
            'max_attempts' => self::MAX_ATTEMPTS,
            'last_sent_at' => now(),
            'expires_at' => $expiresAt,
            'is_verified' => false,
        ]);

        return [
            'success' => true,
            'code' => $otp,
            'expires_at' => $expiresAt,
            'expires_in' => self::EXPIRE_MINUTES * 60,
            'retry_after' => self::COOLDOWN_SECONDS,
            'record_id' => $record->id
        ];
    }

    /**
     * Xác minh mã OTP, kiểm soát tối đa 5 lần thử sai và thời hạn 5 phút
     */
    public static function verifyOtp(string $target, string $type, string $inputCode): array
    {
        $target = trim($target);
        $inputCode = trim($inputCode);

        $otpRecord = OtpCode::where('target', $target)
            ->where('type', $type)
            ->latest('id')
            ->first();

        if (!$otpRecord) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy yêu cầu gửi mã OTP. Vui lòng nhấn gửi mã trước.'
            ];
        }

        // Kiểm tra đã xác minh chưa
        if ($otpRecord->is_verified) {
            return [
                'success' => false,
                'message' => 'Mã OTP này đã được xác minh trước đó.'
            ];
        }

        // Kiểm tra hết hạn 5 phút
        if (now()->isAfter($otpRecord->expires_at)) {
            return [
                'success' => false,
                'expired' => true,
                'message' => 'Mã OTP đã hết hạn (quá 5 phút). Vui lòng yêu cầu gửi mã mới.'
            ];
        }

        // Kiểm tra số lần thử sai vượt quá giới hạn
        if ($otpRecord->attempts >= $otpRecord->max_attempts) {
            return [
                'success' => false,
                'locked' => true,
                'message' => 'Bạn đã nhập sai mã OTP quá 5 lần. Mã này đã bị khóa, vui lòng yêu cầu gửi lại mã mới.'
            ];
        }

        // Kiểm tra khớp mã
        if ($inputCode !== (string) $otpRecord->code) {
            $otpRecord->increment('attempts');
            $remaining = $otpRecord->max_attempts - $otpRecord->attempts;

            if ($remaining <= 0) {
                return [
                    'success' => false,
                    'locked' => true,
                    'message' => 'Bạn đã nhập sai mã OTP 5 lần liên tiếp. Mã đã bị vô hiệu hóa, vui lòng gửi lại mã mới.'
                ];
            }

            return [
                'success' => false,
                'message' => "Mã OTP không chính xác. Bạn còn {$remaining} lần thử."
            ];
        }

        // Khớp mã thành công
        $otpRecord->update(['is_verified' => true]);

        return [
            'success' => true,
            'message' => 'Xác minh OTP thành công!'
        ];
    }
}
