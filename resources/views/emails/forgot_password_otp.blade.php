<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã OTP Khôi Phục Mật Khẩu - SmartRoom &amp; Renty</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background-color: #0b0f19; color: #f1f5f9;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #0b0f19; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 540px; background-color: #131d31; border-radius: 20px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px; text-align: center; background: linear-gradient(135deg, rgba(99, 102, 241, 0.25), rgba(239, 68, 68, 0.2)); border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                            <div style="display: inline-block; width: 48px; height: 48px; line-height: 48px; border-radius: 14px; background: linear-gradient(135deg, #6366f1, #ec4899); color: #ffffff; font-size: 22px; font-weight: bold; margin-bottom: 12px; box-shadow: 0 10px 20px rgba(99, 102, 241, 0.35);">
                                &#128274;
                            </div>
                            <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                SmartRoom <span style="color: #818cf8;">&amp; Renty</span>
                            </h1>
                            <p style="margin: 6px 0 0 0; font-size: 13px; color: #94a3b8; font-weight: 500;">
                                Yêu Cầu Khôi Phục Mật Khẩu Tài Khoản
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 32px 32px 28px 32px;">
                            <p style="margin: 0 0 16px 0; font-size: 15px; color: #e2e8f0; line-height: 1.6;">
                                Xin chào <strong>{{ $fullName ?? 'Bạn' }}</strong>,
                            </p>
                            <p style="margin: 0 0 24px 0; font-size: 14px; color: #94a3b8; line-height: 1.6;">
                                Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản liên kết với email <strong>{{ $email }}</strong>. Dưới đây là mã xác thực OTP 6 chữ số của bạn:
                            </p>

                            <!-- OTP Box -->
                            <div style="text-align: center; margin: 28px 0; padding: 24px; background: #070b13; border: 2px dashed #818cf8; border-radius: 16px;">
                                <div style="font-size: 11px; font-weight: 700; color: #a5b4fc; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">
                                    MÃ OTP KHÔI PHỤC MẬT KHẨU
                                </div>
                                <div style="font-size: 38px; font-weight: 900; letter-spacing: 12px; color: #818cf8; font-family: 'Courier New', monospace;">
                                    {{ $otp }}
                                </div>
                                <div style="font-size: 12px; color: #64748b; margin-top: 10px;">
                                    Mã này có hiệu lực trong vòng <strong style="color: #f87171;">5 phút</strong>
                                </div>
                            </div>

                            <!-- Security Alert -->
                            <div style="background: rgba(239, 68, 68, 0.08); border-left: 4px solid #ef4444; padding: 12px 16px; border-radius: 8px; margin: 24px 0;">
                                <p style="margin: 0; font-size: 12px; color: #fca5a5; line-height: 1.5;">
                                    <strong>Cảnh báo bảo mật:</strong> Tuyệt đối không chia sẻ mã này cho bất kỳ ai. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này hoặc liên hệ ngay với bộ phận hỗ trợ của SmartRoom.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 32px; background-color: #0c1220; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.05);">
                            <p style="margin: 0; font-size: 12px; color: #64748b;">
                                &copy; 2026 SmartRoom &amp; Renty. Nền tảng quản lý nhà trọ và kết nối khách thuê số 1.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
