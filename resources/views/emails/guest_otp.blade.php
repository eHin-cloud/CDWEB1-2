<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã OTP Xác Minh Tài Khoản Khách Thuê - SmartRoom &amp; Renty</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background-color: #0b0f19; color: #f1f5f9;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #0b0f19; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 540px; background-color: #131d31; border-radius: 20px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px; text-align: center; background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(16, 185, 129, 0.2)); border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                            <div style="display: inline-block; width: 48px; height: 48px; line-height: 48px; border-radius: 14px; background: linear-gradient(135deg, #10b981, #6366f1); color: #ffffff; font-size: 22px; font-weight: bold; margin-bottom: 12px; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);">
                                &#10003;
                            </div>
                            <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                SmartRoom <span style="color: #34d399;">&amp; Renty</span>
                            </h1>
                            <p style="margin: 6px 0 0 0; font-size: 13px; color: #94a3b8; font-weight: 500;">
                                Cổng Xác Thực Đăng Ký Tài Khoản Khách Thuê
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
                                Bạn vừa tạo tài khoản khách thuê trên hệ sinh thái <strong>SmartRoom &amp; Renty</strong>. Dưới đây là mã xác thực OTP 6 số để kích hoạt tài khoản và đăng nhập vào Renty:
                            </p>

                            <!-- OTP Box -->
                            <div style="text-align: center; margin: 28px 0; padding: 24px; background: #070b13; border: 2px dashed #10b981; border-radius: 16px;">
                                <div style="font-size: 11px; font-weight: 700; color: #6ee7b7; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">
                                    MÃ XÁC THỰC OTP (6 SỐ)
                                </div>
                                <div style="font-size: 38px; font-weight: 900; letter-spacing: 12px; color: #34d399; font-family: 'Courier New', monospace;">
                                    {{ $otp }}
                                </div>
                                <div style="font-size: 12px; color: #64748b; margin-top: 10px;">
                                    Mã này có hiệu lực trong vòng <strong style="color: #f87171;">5 phút</strong>
                                </div>
                            </div>

                            <!-- Security Alert -->
                            <div style="background: rgba(16, 185, 129, 0.08); border-left: 4px solid #10b981; padding: 12px 16px; border-radius: 8px; margin: 24px 0;">
                                <p style="margin: 0; font-size: 12px; color: #a7f3d0; line-height: 1.5;">
                                    <strong>Lưu ý:</strong> Vui lòng không chia sẻ mã này cho bất kỳ ai. Sau khi xác thực thành công, bạn sẽ được tự động chuyển vào trang Renty để trải nghiệm tìm phòng trọ tốt nhất.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 32px; text-align: center; background-color: #0b0f19; border-top: 1px solid rgba(255, 255, 255, 0.05); font-size: 12px; color: #64748b;">
                            &copy; 2026 SmartRoom &amp; Renty. Nền tảng kết nối và quản lý nhà trọ hiện đại.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
