<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔔 TIN NÓNG: Phòng bạn theo dõi vừa chuyển sang trạng thái TRỐNG - Renty</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif; background-color: #060911; color: #f1f5f9;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #060911; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 580px; background-color: #0d1322; border-radius: 20px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);">
                    <!-- Header with Emerald Green Accent -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px; text-align: center; background: linear-gradient(135deg, rgba(16, 185, 129, 0.3) 0%, rgba(245, 158, 11, 0.25) 100%); border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                            <div style="display: inline-block; width: 56px; height: 56px; line-height: 56px; border-radius: 18px; background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; font-size: 26px; font-weight: bold; margin-bottom: 12px; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);">
                                &#127881;
                            </div>
                            <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                Phòng Đang Theo Dõi <span style="color: #34d399;">VỪA TRỐNG!</span>
                            </h1>
                            <p style="margin: 6px 0 0 0; font-size: 13px; color: #cbd5e1; font-weight: 500;">
                                Chuông Báo Phòng Trống Thông Minh &ndash; SmartRoom &amp; Renty
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
                                Căn phòng trọ bạn đang bật chuông theo dõi tại <strong>{{ $room->building?->name ?? 'SmartRoom' }}</strong> vừa được cập nhật sang trạng thái <strong>SẴN SÀNG CHO THUÊ</strong>!
                            </p>

                            <!-- Room Details Card -->
                            <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 16px; padding: 22px; margin: 24px 0;">
                                <div style="display: inline-block; padding: 4px 10px; background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); border-radius: 8px; font-size: 10px; font-weight: 800; color: #34d399; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">
                                    &bull; SẴN SÀNG CHO THUÊ NGAY
                                </div>
                                <h3 style="margin: 0 0 10px 0; font-size: 18px; color: #ffffff; font-weight: 800;">
                                    Phòng {{ $room->room_number }} &ndash; {{ $room->building?->name }}
                                </h3>
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #94a3b8; width: 110px;">Địa chỉ:</td>
                                        <td style="padding: 6px 0; font-size: 13px; color: #cbd5e1;">
                                            {{ $room->building?->address }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #94a3b8;">Giá thuê:</td>
                                        <td style="padding: 6px 0; font-size: 16px; color: #34d399; font-weight: 800;">
                                            {{ number_format($room->price) }} VNĐ / tháng
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #94a3b8;">Diện tích:</td>
                                        <td style="padding: 6px 0; font-size: 13px; color: #f8fafc; font-weight: 600;">
                                            {{ $room->area ?? 25 }} m² (Tầng {{ $room->floor ?? 1 }})
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <p style="margin: 0 0 24px 0; font-size: 13px; color: #f59e0b; font-weight: 600; line-height: 1.5; background: rgba(245, 158, 11, 0.1); border-left: 3px solid #f59e0b; padding: 12px 14px; border-radius: 0 8px 8px 0;">
                                &#9888; Lưu ý: Phòng đẹp tại khu vực này thường có khách thuê rất nhanh. Bạn nên liên hệ chủ trọ hoặc đặt lịch xem phòng ngay hôm nay!
                            </p>

                            <!-- Button Call to Action -->
                            <div style="text-align: center; margin: 30px 0 10px 0;">
                                <a href="{{ url('/renty/room/' . $room->id) }}" style="display: inline-block; padding: 14px 34px; background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 800; border-radius: 12px; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);">
                                    Xem Chi Tiết &amp; Đặt Phòng Ngay &rarr;
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
