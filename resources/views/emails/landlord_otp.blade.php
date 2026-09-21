<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã OTP Xác Minh Tài Khoản - SmartRoom</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background-color: #0f172a; color: #f1f5f9;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #0f172a; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 540px; background-color: #1e293b; border-radius: 16px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px; text-align: center; background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(16, 185, 129, 0.15)); border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                            <div style="display: inline-block; width: 48px; height: 48px; line-height: 48px; border-radius: 12px; background: linear-gradient(135deg, #6366f1, #10b981); color: #ffffff; font-size: 24px; font-weight: bold; margin-bottom: 12px;">
                                &#9733;
                            </div>
                            <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                SmartRoom <span style="color: #818cf8;">&amp; Renty</span>
                            </h1>
                            <p style="margin: 6px 0 0 0; font-size: 13px; color: #94a3b8; font-weight: 500;">
                                Cổng Xác Thực &amp; Đăng Ký Chủ Trọ
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 32px 32px 28px 32px;">
                            <p style="margin: 0 0 16px 0; font-size: 15px; color: #e2e8f0; line-height: 1.6;">
                                Xin chào <strong>{{ $fullName ?? 'Quý chủ trọ' }}</strong>,
                            </p>
                            <p style="margin: 0 0 24px 0; font-size: 14px; color: #94a3b8; line-height: 1.6;">
                                Bạn vừa đăng ký tài khoản chủ trọ trên hệ thống <strong>SmartRoom</strong>. Dưới đây là mã xác thực OTP dùng để kích hoạt tài khoản và mở Dashboard quản lý:
                            </p>

                            <!-- OTP Box -->
                            <div style="text-align: center; margin: 28px 0; padding: 24px; background: #0b0f19; border: 2px dashed #6366f1; border-radius: 12px;">
                                <div style="font-size: 11px; font-weight: 700; color: #a5b4fc; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">
                                    MÃ XÁC THỰC OTP (6 SỐ)
                                </div>
                                <div style="font-size: 36px; font-weight: 900; letter-spacing: 12px; color: #fbbf24; font-family: 'Courier New', monospace;">
                                    {{ $otp }}
                                </div>
                                <div style="font-size: 12px; color: #64748b; margin-top: 8px;">
                                    Mã này có hiệu lực trong vòng <strong style="color: #f87171;">5 phút</strong>
                                </div>
                            </div>

                            <!-- Security Alert -->
                            <div style="background: rgba(245, 158, 11, 0.1); border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 6px; margin: 24px 0;">
                                <p style="margin: 0; font-size: 12px; color: #fde68a; line-height: 1.5;">
                                    <strong>Lưu ý bảo mật:</strong> Không chia sẻ mã OTP này cho bất kỳ ai, kể cả nhân viên SmartRoom. Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 32px; background-color: #0b0f19; border-top: 1px solid rgba(255, 255, 255, 0.05); text-align: center;">
                            <p style="margin: 0; font-size: 11px; color: #64748b; line-height: 1.5;">
                                &copy; 2026 SmartRoom &amp; Renty. Hệ thống quản lý vận hành phòng trọ thông minh.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
