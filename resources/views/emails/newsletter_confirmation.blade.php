<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Đăng Ký Nhận Tin Phòng Mới - SmartRoom &amp; Renty</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif; background-color: #060911; color: #f1f5f9;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #060911; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 560px; background-color: #0d1322; border-radius: 20px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);">
                    <!-- Header with Gradient Accent -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px; text-align: center; background: linear-gradient(135deg, rgba(16, 185, 129, 0.25) 0%, rgba(99, 102, 241, 0.25) 100%); border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                            <div style="display: inline-block; width: 52px; height: 52px; line-height: 52px; border-radius: 16px; background: linear-gradient(135deg, #10b981, #06b6d4); color: #ffffff; font-size: 24px; font-weight: bold; margin-bottom: 12px; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);">
                                &#9993;
                            </div>
                            <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                SmartRoom <span style="color: #34d399;">&amp; Renty</span>
                            </h1>
                            <p style="margin: 6px 0 0 0; font-size: 13px; color: #94a3b8; font-weight: 500;">
                                Cổng Thông Tin &amp; Đăng Ký Nhận Tin Phòng Mới
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 32px 28px 32px;">
                            <p style="margin: 0 0 16px 0; font-size: 15px; color: #e2e8f0; line-height: 1.6;">
                                Xin chào <strong>{{ $email }}</strong>,
                            </p>
                            <p style="margin: 0 0 20px 0; font-size: 14px; color: #94a3b8; line-height: 1.6;">
                                Cảm ơn bạn đã đăng ký nhận bản tin trên nền tảng <strong>SmartRoom &amp; Renty</strong>! Từ bây giờ, bạn sẽ là người đầu tiên nhận được thông báo khi có các căn phòng trọ mới, tiện nghi và giá tốt nhất tại khu vực bạn mong muốn.
                            </p>

                            <!-- Highlights Box -->
                            <div style="background: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 16px; padding: 20px; margin: 24px 0;">
                                <div style="font-size: 12px; font-weight: 800; color: #34d399; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">
                                    ĐẶC QUYỀN DÀNH CHO BẠN
                                </div>
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #cbd5e1; line-height: 1.5;">
                                            <span style="color: #34d399; font-weight: bold; margin-right: 8px;">✓</span>
                                            <strong>Ưu tiên nhận tin tức thời:</strong> Nhận thông báo sớm nhất khi có phòng trống vừa nạp vào hệ thống.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #cbd5e1; line-height: 1.5;">
                                            <span style="color: #34d399; font-weight: bold; margin-right: 8px;">✓</span>
                                            <strong>100% Chủ trọ đã KYC:</strong> Đề xuất các cơ sở trọ uy tín, có giấy phép PCCC và huy hiệu Tích Xanh.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #cbd5e1; line-height: 1.5;">
                                            <span style="color: #34d399; font-weight: bold; margin-right: 8px;">✓</span>
                                            <strong>AI Cảnh báo giá thông minh:</strong> So sánh giá mặt bằng xung quanh, chống bẫy giá ảo và lừa cọc.
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin: 32px 0 16px 0;">
                                <a href="{{ url('/renty') }}" target="_blank" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #059669 0%, #0d9488 100%); color: #ffffff; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 12px; box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);">
                                    Khám Phá Sàn Phòng Trọ Renty Ngay &rarr;
                                </a>
                            </div>

                            <p style="margin: 24px 0 0 0; font-size: 12px; color: #64748b; line-height: 1.5; text-align: center;">
                                Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email. Chúng tôi cam kết bảo mật thông tin và không gửi thư rác.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 32px; text-align: center; background-color: #060911; border-top: 1px solid rgba(255, 255, 255, 0.05); font-size: 12px; color: #64748b;">
                            &copy; 2026 SmartRoom &amp; Renty. Nền tảng kết nối và quản lý lưu trú số 1 cho sinh viên &amp; người đi làm.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
