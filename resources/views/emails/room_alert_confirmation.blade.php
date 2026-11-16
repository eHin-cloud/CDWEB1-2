<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Kích Hoạt Chuông Báo Phòng Trống - SmartRoom &amp; Renty</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif; background-color: #060911; color: #f1f5f9;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #060911; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 580px; background-color: #0d1322; border-radius: 20px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);">
                    <!-- Header with Amber Accent -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px; text-align: center; background: linear-gradient(135deg, rgba(245, 158, 11, 0.25) 0%, rgba(99, 102, 241, 0.25) 100%); border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                            <div style="display: inline-block; width: 56px; height: 56px; line-height: 56px; border-radius: 18px; background: linear-gradient(135deg, #f59e0b, #d97706); color: #ffffff; font-size: 26px; font-weight: bold; margin-bottom: 12px; box-shadow: 0 10px 25px rgba(245, 158, 11, 0.35);">
                                &#128276;
                            </div>
                            <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                SmartRoom <span style="color: #fbbf24;">&amp; Renty</span>
                            </h1>
                            <p style="margin: 6px 0 0 0; font-size: 13px; color: #cbd5e1; font-weight: 500;">
                                Chuông Báo Phòng Trống Thông Minh (Room Alert)
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
                                Bạn vừa kích hoạt thành công <strong>Chuông báo phòng trống thông minh</strong> trên sàn phòng trọ <strong>Renty</strong>. Hệ thống AI sẽ tự động giám sát 24/7 và thông báo ngay tức thì cho bạn khi có phòng trống phù hợp.
                            </p>

                            <!-- Thông tin tiêu chí đăng ký -->
                            <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 16px; padding: 20px; margin: 24px 0;">
                                <div style="font-size: 12px; font-weight: 800; color: #fbbf24; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px;">
                                    TIÊU CHÍ BẠN ĐÃ THIẾT LẬP
                                </div>
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    @if(!empty($room))
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #94a3b8; width: 140px;">Phòng theo dõi:</td>
                                        <td style="padding: 6px 0; font-size: 13px; color: #f8fafc; font-weight: 700;">
                                            Phòng {{ $room->room_number }} &ndash; {{ $room->building?->name ?? 'SmartRoom' }}
                                        </td>
                                    </tr>
                                    @if($room->building?->address)
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #94a3b8;">Địa chỉ:</td>
                                        <td style="padding: 6px 0; font-size: 13px; color: #cbd5e1;">
                                            {{ $room->building?->address }}
                                        </td>
                                    </tr>
                                    @endif
                                    @endif
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #94a3b8; width: 140px;">Khu vực mong muốn:</td>
                                        <td style="padding: 6px 0; font-size: 13px; color: #f8fafc; font-weight: 700;">
                                            {{ $district }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #94a3b8;">Ngân sách tối đa:</td>
                                        <td style="padding: 6px 0; font-size: 14px; color: #34d399; font-weight: 800;">
                                            {{ number_format($budget) }} VNĐ / tháng
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #94a3b8;">Thời gian đăng ký:</td>
                                        <td style="padding: 6px 0; font-size: 13px; color: #cbd5e1;">
                                            {{ now()->format('d/m/Y H:i:s') }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Cách thức hoạt động -->
                            <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px; padding: 16px 20px; margin-bottom: 24px;">
                                <div style="font-size: 12px; font-weight: 700; color: #94a3b8; margin-bottom: 8px;">CƠ CHẾ BẮN CHUÔNG TỰ ĐỘNG:</div>
                                <p style="margin: 0; font-size: 13px; color: #cbd5e1; line-height: 1.5;">
                                    Khi khách thuê cũ trả phòng hoặc khi có phòng mới tại <strong>{{ $district }}</strong> với giá dưới <strong>{{ number_format($budget) }} VNĐ</strong> chuyển sang trạng thái <strong>Trống</strong>, hệ thống sẽ tự động gửi email thông báo kèm đường dẫn đặt phòng ngay cho bạn.
                                </p>
                            </div>

                            <!-- Button Call to Action -->
                            <div style="text-align: center; margin: 30px 0 10px 0;">
                                <a href="{{ url('/renty') }}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #f59e0b, #d97706); color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; border-radius: 12px; box-shadow: 0 10px 25px rgba(245, 158, 11, 0.35);">
                                    Xem các phòng trống khác trên Renty &rarr;
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 32px; background-color: #080c16; border-top: 1px solid rgba(255, 255, 255, 0.06); text-align: center; font-size: 12px; color: #64748b; line-height: 1.6;">
                            <p style="margin: 0 0 4px 0;">
                                Bạn nhận được email này vì đã đăng ký chuông báo phòng trống trên nền tảng <strong style="color: #94a3b8;">SmartRoom &amp; Renty</strong>.
                            </p>
                            <p style="margin: 0;">
                                Hotline hỗ trợ: <strong style="color: #94a3b8;">0396.519.196</strong> &bull; Email: <strong style="color: #94a3b8;">support@smartroom.vn</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
