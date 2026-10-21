<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã OTP &amp; Liên Kết Đăng Nhập Siêu Tốc - SmartRoom &amp; Renty</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background-color: #0b0f19; color: #f1f5f9;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #0b0f19; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 540px; background-color: #131d31; border-radius: 20px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px; text-align: center; background: linear-gradient(135deg, rgba(14, 165, 233, 0.2), rgba(147, 51, 234, 0.2)); border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                            <div style="display: inline-block; width: 50px; height: 50px; line-height: 50px; border-radius: 16px; background: linear-gradient(135deg, #0ea5e9, #9333ea); color: #ffffff; font-size: 24px; font-weight: bold; margin-bottom: 12px; box-shadow: 0 10px 25px rgba(99, 102, 241, 0.35);">
                                &#9889;
                            </div>
                            <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                SmartRoom <span style="color: #38bdf8;">&amp; Renty</span>
                            </h1>
                            <p style="margin: 6px 0 0 0; font-size: 13px; color: #94a3b8; font-weight: 500;">
                                Cổng Xác Thực &amp; Đăng Nhập Siêu Tốc 1 Chạm
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
                                Bạn vừa yêu cầu đăng nhập vào hệ sinh thái <strong>SmartRoom &amp; Renty</strong>. Để vào hệ thống nhanh nhất, bạn chỉ cần bấm vào nút bên dưới:
                            </p>

                            @if(!empty($magicLink))
                            <!-- NÚT ĐĂNG NHẬP NHANH 1 CHẠM (XANH & TÍM) -->
                            <div style="text-align: center; margin: 26px 0 24px 0;">
                                <a href="{{ $magicLink }}" 
                                   target="_blank"
                                   style="display: inline-block; padding: 15px 36px; background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 50%, #9333ea 100%); color: #ffffff; text-decoration: none; font-weight: 800; font-size: 15px; border-radius: 14px; box-shadow: 0 10px 25px rgba(99, 102, 241, 0.45); letter-spacing: 0.5px; border: 1px solid rgba(255, 255, 255, 0.25);">
                                    &#9889; Đăng Nhập Nhanh Bằng 1 Chạm
                                </a>
                                <p style="margin: 8px 0 0 0; font-size: 12px; color: #94a3b8;">
                                    (Bấm nút trên để vào hệ thống ngay lập tức mà không cần nhập mã)
                                </p>
                            </div>

                            <!-- Phân cách -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin: 20px 0;">
                                <tr>
                                    <td style="border-bottom: 1px dashed rgba(255, 255, 255, 0.15);"></td>
                                    <td style="padding: 0 12px; white-space: nowrap; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1.5px; text-align: center;">HOẶC NHẬP MÃ OTP THỦ CÔNG</td>
                                    <td style="border-bottom: 1px dashed rgba(255, 255, 255, 0.15);"></td>
                                </tr>
                            </table>
                            @endif

                            <!-- OTP Box -->
                            <div style="text-align: center; margin: 20px 0; padding: 22px; background: #070b13; border: 2px dashed #38bdf8; border-radius: 16px;">
                                <div style="font-size: 11px; font-weight: 700; color: #7dd3fc; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">
                                    MÃ XÁC THỰC OTP (6 SỐ)
                                </div>
                                <div style="font-size: 38px; font-weight: 900; letter-spacing: 12px; color: #38bdf8; font-family: 'Courier New', monospace;">
                                    {{ $otp }}
                                </div>
                                <div style="font-size: 12px; color: #64748b; margin-top: 10px;">
                                    Mã &amp; liên kết có hiệu lực trong vòng <strong style="color: #f87171;">5 phút</strong>
                                </div>
                            </div>

                            @if(!empty($magicLink))
                            <div style="margin-top: 16px; padding: 10px 14px; background: rgba(15, 23, 42, 0.6); border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.05); font-size: 11px; color: #64748b; word-break: break-all; line-height: 1.5;">
                                Nếu nút bấm không hoạt động, bạn có thể copy liên kết sau dán vào trình duyệt:<br>
                                <a href="{{ $magicLink }}" style="color: #38bdf8; text-decoration: underline;">{{ $magicLink }}</a>
                            </div>
                            @endif

                            <!-- Security Alert -->
                            <div style="background: rgba(59, 130, 246, 0.08); border-left: 4px solid #3b82f6; padding: 12px 16px; border-radius: 8px; margin: 24px 0 0 0;">
                                <p style="margin: 0; font-size: 12px; color: #93c5fd; line-height: 1.5;">
                                    <strong>Lưu ý bảo mật:</strong> Tuyệt đối không chia sẻ mã hoặc liên kết này cho bất kỳ ai. Nếu không phải bạn gửi yêu cầu, vui lòng bỏ qua email này để bảo vệ tài khoản.
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
