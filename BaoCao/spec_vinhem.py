# -*- coding: utf-8 -*-
"""
Đặc tả chi tiết 12 chức năng của Huỳnh Văn Vĩnh Em (Thành viên)
Chuẩn hóa: 10 người đọc đều code giống nhau, coder đọc vô code được ngay.
"""

SPEC_VINHEM = """
## C. CÁC MODULE & ĐẶC TẢ KỸ THUẬT DO HUỲNH VĂN VĨNH EM (THÀNH VIÊN) PHỤ TRÁCH

---

### [FEAT-VEM-01] Cổng tìm kiếm lưu trú công cộng Renty Portal (Glassmorphism & Responsive)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Route & View**: `GET /renty` (hoặc `/home`, `/`), View `resources/views/renty/index.blade.php`
- **Middleware**: `web`

#### 1. Input Specification (Tham số Query String)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `q` | String | Không | `nullable\|string\|max:100` | "Từ khóa tìm kiếm tối đa 100 ký tự." |
| `city` | String | Không | `nullable\|string` | "Tỉnh/Thành phố không hợp lệ." |
| `district` | String | Không | `nullable\|string` | "Quận/Huyện không hợp lệ." |
| `sort` | String | Không | `nullable\|in:price_asc,price_desc,rating_desc,newest` | "Tiêu chí sắp xếp không hợp lệ." |

#### 2. Business Logic Flow
1. **Truy vấn danh sách phòng**:
   - Truy vấn `Room::with(['building', 'tenant', 'reviews'])` với điều kiện mặc định `status = 'empty'`.
   - Tính toán điểm đánh giá sao trung bình (`rating = reviews->avg('rating') ?? 4.5`).
   - Lấy huy hiệu uy tín chủ trọ (`trustBadge`):
     - `premium_verified` / `verified`: Huy hiệu "Tích xanh" uy tín (Xanh ngọc `bg-sky-500/10 text-sky-300`).
     - `kyc_verified`: Huy hiệu "Đã xác minh KYC" (Xanh lá `bg-emerald-500/10 text-emerald-300`).
     - `unverified`: Chưa xác minh (Xám).
2. **Khởi tạo dữ liệu giao diện**: Nạp danh sách các cơ sở, ảnh đại diện, khoảng cách tiện ích, mức giá theo tháng/ngày/giờ.
3. **Render View**: Kết xuất giao diện Glassmorphism với hiệu ứng nền mờ `backdrop-blur-md bg-slate-900/80 border border-white/10`.

#### 3. Database Operation
- **Đọc**: `rooms`, `buildings`, `tenants`, `reviews`. Không sửa đổi dữ liệu.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Render HTML trang chủ Renty hoàn chỉnh kèm danh sách thẻ phòng dạng Grid (1 cột trên di động, 2 cột trên tablet, 3 cột trên desktop).

#### 5. UI/UX Specification & Xử lý lỗi
- **Layout**: Header với logo Renty phát sáng, thanh tìm kiếm lớn ở Banner Hero, nút chuyển chế độ xem Bản đồ / Danh sách.

---

### [FEAT-VEM-02] Bộ lọc tìm kiếm thông minh đa tiêu chí (Smart Search Filter)
- **Git Branch**: `feat(smart-search)`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Service & Controller**: `App\Services\SmartSearchService`, `App\Http\Controllers\Api\SmartSearchController`
- **Endpoint**: `GET /api/renty/rooms`
- **Middleware**: `web`

#### 1. Input Specification (Request Query Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `price_min` | Numeric | Không | `nullable\|numeric\|min:0` | "Giá tối thiểu phải lớn hơn hoặc bằng 0." |
| `price_max` | Numeric | Không | `nullable\|numeric\|gte:price_min` | "Giá tối đa phải lớn hơn hoặc bằng giá tối thiểu." |
| `rental_type` | String | Không | `nullable\|in:month,day,hour` | "Hình thức thuê phải là month, day hoặc hour." |
| `room_type` | String | Không | `nullable\|in:standard,deluxe,vip,studio` | "Hạng phòng không hợp lệ." |
| `amenities` | Array | Không | `nullable\|array` | "Danh sách tiện ích lọc không hợp lệ." |
| `only_empty` | Boolean | Không | `nullable\|boolean` | "Trạng thái chỉ phòng trống phải là true/false." |

#### 2. Business Logic Flow
1. **Xây dựng truy vấn động (Dynamic Query Builder)**:
   - Áp dụng các điều kiện lọc: `whereBetween('price', [$min, $max])`.
   - Nếu `only_empty == true`: Thêm điều kiện `where('status', 'empty')`.
   - Lọc theo tiện ích (JSON column): Duyệt qua mảng `amenities` (ví dụ: `wc_rieng`, `gac_lung`, `ban_cong`, `thu_cung`, `thang_may`):
     `whereJsonContains('amenities', $amenity)`.
2. **Sắp xếp & Phân trang**: Mặc định sắp xếp theo ngày đăng mới nhất hoặc theo giá tăng/giảm dần; phân trang 12 phòng/trang.
3. **Phản hồi**: Trả về dữ liệu JSON kèm metadata phân trang để giao diện cập nhật AJAX không cần tải lại trang.

#### 3. Database Operation
- **Đọc**: `rooms`, `buildings`, `reviews`. Lập chỉ mục trên các cột `price`, `status`, `room_type` để truy vấn dưới 50ms.

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "data": [...], "current_page": 1, "total_rooms": 28}`.
- **Thất bại (HTTP 422)**: Báo lỗi nếu khoảng giá `price_min > price_max`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Nhập khoảng giá tối thiểu lớn hơn giá tối đa | Ô giá bôi đỏ viền: "Khoảng giá tìm kiếm không hợp lệ (Giá tối thiểu phải nhỏ hơn giá tối đa)". |
| Không tìm thấy phòng nào phù hợp | Hiển thị Empty State với hình minh họa: "Không tìm thấy phòng phù hợp với tiêu chí của bạn. Hãy thử nới lỏng bộ lọc!". |

---

### [FEAT-VEM-03] Thanh công cụ so sánh phòng nổi song song (Compare tối đa 3 phòng)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Endpoint**: `POST /api/renty/rooms/compare`
- **Middleware**: `web`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_ids` | Array | Có | `required\|array\|min:2\|max:3` | "Vui lòng chọn từ 2 đến tối đa 3 phòng để thực hiện so sánh." |
| `room_ids.*`| Integer | Có | `integer\|exists:rooms,id` | "Mã phòng so sánh không tồn tại." |

#### 2. Business Logic Flow
1. **Kiểm tra số lượng phòng**:
   - Nếu `count(room_ids) < 2`: Báo lỗi yêu cầu chọn ít nhất 2 phòng.
   - Nếu `count(room_ids) > 3`: Chặn và thông báo chỉ được so sánh tối đa 3 phòng.
2. **Nạp & Đối chiếu thông số kỹ thuật song song**:
   - Truy vấn chi tiết các phòng được chọn kèm thông tin tòa nhà.
   - Chuẩn hóa ma trận đối chiếu gồm các tiêu chí: Giá thuê theo tháng, tiền đặt cọc, diện tích sử dụng, đơn giá điện/nước, danh mục tiện ích có/không (Checkmark xanh / Dấu x đỏ), khoảng cách tiện ích và điểm sao đánh giá uy tín.
3. **Phản hồi**: Trả về cấu trúc bảng so sánh chi tiết dạng JSON hoặc render Partial Blade View.

#### 3. Database Operation
- **Đọc**: `rooms`, `buildings`, `utility_records`.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Trả về ma trận so sánh song song 3 cột để hiển thị trên Modal đối chiếu.

#### 5. UI/UX Specification & Xử lý lỗi
- **Thanh so sánh nổi (Sticky Floating Bar)**: Cố định ở đáy màn hình `fixed bottom-4 left-1/2 -translate-x-1/2 z-40 bg-slate-900/90 border border-sky-500/30 rounded-2xl px-6 py-3 shadow-2xl`, hiển thị thumbnail các phòng đã chọn kèm nút "So sánh ngay (2/3)".
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Chọn thêm phòng thứ 4 vào thanh so sánh | Toast cảnh báo vàng: "Bạn chỉ có thể so sánh tối đa 3 phòng cùng một lúc". |
| Bấm So sánh khi chỉ chọn 1 phòng | Toast thông báo: "Vui lòng chọn ít nhất 2 phòng để tiến hành so sánh đối chiếu". |

---

### [FEAT-VEM-04] Màn hình Chi tiết phòng lưu trú (Room Detail & Media Gallery)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Route & View**: `GET /renty/room/{id}`, View `resources/views/renty/detail.blade.php`
- **Middleware**: `web`

#### 1. Input Specification (Tham số Route)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `id` | Integer (Route) | Có | `exists:rooms,id` | "Phòng lưu trú không tồn tại hoặc đã ngừng cho thuê." |

#### 2. Business Logic Flow
1. **Truy vấn thông tin chi tiết**: `Room::with(['building', 'tenant', 'reviews', 'equipment'])->findOrFail($id)`.
2. **Tổng hợp đa phương tiện (Media Showcase)**:
   - Slide ảnh chất lượng cao (Lightbox xem ảnh phóng to).
   - Trình phát Video thực tế không gian phòng (hỗ trợ MP4, WebM).
3. **Tọa độ địa lý & Bản đồ GIS**: Nạp tọa độ GPS của tòa nhà để hiển thị vị trí trên OpenStreetMap / Google Maps.
4. **Phát hiện giá dị biệt (Price Anomaly Detection)**:
   - So sánh đơn giá phòng với mức giá trung bình của các phòng cùng khu vực. Nếu giá rẻ hơn hoặc cao hơn 30% bất thường, hiển thị nhãn cảnh báo để khách hàng lưu ý.

#### 3. Database Operation
- **Đọc**: `rooms`, `buildings`, `reviews`, `equipment`.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Render HTML trang chi tiết phòng đầy đủ tiện ích và form liên hệ đặt hẹn xem phòng.

#### 5. UI/UX Specification & Xử lý lỗi
- **Gallery**: Carousel trình chiếu ảnh mượt mà, nút "Đặt phòng ngay", nút "Đăng ký xem phòng", nút "Gửi báo cáo vi phạm".

---

### [FEAT-VEM-05] Hệ thống Đánh giá Review có xác thực hợp đồng lưu trú thực tế
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: Route `POST /renty/room/{id}/review`
- **Middleware**: `auth`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `rating` | Integer | Có | `required\|integer\|min:1\|max:5` | "Điểm đánh giá phải từ 1 đến 5 sao." |
| `comment` | String | Có | `required\|string\|min:10\|max:1000` | "Nội dung nhận xét phải từ 10 đến 1000 ký tự." |

#### 2. Business Logic Flow
1. **Cơ chế Xác thực Khách thuê Thực tế (Verified Tenant Guard)**:
   - Kiểm tra xem người dùng đang đăng nhập (`auth()->id()`) đã từng có Hợp đồng thuê (`Contract`) hoặc Phiếu đặt phòng (`HotelBooking`) hợp lệ đối với căn phòng này chưa.
   - **Guard Check**: Nếu chưa từng thuê, **CHẶN ĐÁNH GIÁ** (HTTP 403): *"Bạn chỉ có thể đánh giá phòng này sau khi đã ký hợp đồng hoặc lưu trú thực tế tại đây"*.
2. **Lưu đánh giá**:
   - Tạo bản ghi mới trong bảng `reviews` với `user_id = auth()->id()`, `room_id = :id`, số sao và bình luận.
   - Gắn nhãn chứng thực `"Đã xác thực cư dân thuê trọ"`.
3. **Phản hồi**: Redirect back kèm thông báo cảm ơn đã gửi đánh giá.

#### 3. Database Operation
- **Bảng tác động**: `reviews` (INSERT bản ghi mới).

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về trang chi tiết phòng kèm toast xanh: "Cảm ơn bạn đã gửi đánh giá trải nghiệm lưu trú!".

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Người dùng chưa từng thuê phòng cố tình gửi review | Chặn gửi đánh giá kèm thông báo đỏ: "Bạn chỉ có thể đánh giá phòng này sau khi đã hoàn tất hợp đồng thuê tại đây". |
| Bỏ trống nội dung hoặc nhận xét dưới 10 ký tự | Ô nhận xét bôi đỏ viền: "Vui lòng nhập nội dung đánh giá chi tiết (tối thiểu 10 ký tự)". |

---

### [FEAT-VEM-06] Tiếp nhận Báo cáo phòng vi phạm / lừa cọc (RoomReport)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: Route `POST /renty/room/{id}/report`
- **Middleware**: `web`, `throttle:5,1`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `reason` | String | Có | `required\|in:scam,fake_images,wrong_price,unsafe,other` | "Lý do báo cáo vi phạm không hợp lệ." |
| `description` | String | Có | `required\|string\|min:10\|max:1000` | "Vui lòng cung cấp mô tả chi tiết bằng chứng sai phạm (10 - 1000 ký tự)." |
| `reporter_name`| String | Không | `nullable\|string\|max:255` | "Họ tên người báo cáo tối đa 255 ký tự." |
| `reporter_phone`| String | Không | `nullable\|regex:/^[0-9]{10}$/` | "Số điện thoại liên lạc phải gồm đúng 10 chữ số." |

#### 2. Business Logic Flow
1. **Tiếp nhận khiếu nại**: Ghi nhận báo cáo vào bảng `room_reports` với `status = 'pending'`.
2. **Cơ chế Cảnh báo Tự động (Auto-flagging)**:
   - Đếm số lượng báo cáo `pending` của phòng đó.
   - Nếu một phòng nhận quá 3 báo cáo lừa đảo (`scam`), hệ thống tự động gắn cờ cảnh báo màu vàng `"Phòng đang bị người dùng báo cáo sai phạm"` trên cổng tìm kiếm Renty để bảo vệ người thuê khác.
3. **Phản hồi**: Thông báo đã tiếp nhận và sẽ đối soát xử lý trong vòng 12 giờ.

#### 3. Database Operation
- **Bảng tác động**: `room_reports`.

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "message": "Báo cáo của bạn đã được gửi đến Ban Quản Trị để xử lý."}`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống phần mô tả chi tiết vi phạm | Ô mô tả bôi đỏ viền: "Vui lòng nhập mô tả chi tiết hành vi sai phạm để ban quản trị đối soát xử lý". |

---

### [FEAT-VEM-07] Trợ lý ảo AI Renty Chatbot tư vấn thuê phòng theo mô hình RAG (Google Gemini API)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: `App\Http\Controllers\ChatbotController@chat`
- **Endpoint & Method**: `POST /renty/chatbot/chat`
- **Middleware**: `web`, `throttle:60,1`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `message` | String | Có | `required\|string\|min:2\|max:300` | "Câu hỏi của bạn phải từ 2 đến tối đa 300 ký tự." |
| `conversation_history`| Array | Không | `nullable\|array\|max:10` | "Lịch sử hội thoại không hợp lệ." |

#### 2. Business Logic Flow
1. **Giai đoạn Truy xuất dữ liệu (Retrieval Phase - RAG)**:
   - Phân tích ngữ nghĩa câu hỏi để trích xuất các thực thể: Khu vực (Quận/Đường), Mức giá tối đa, Tiện ích yêu cầu (máy lạnh, gác lửng, thú cưng).
   - Truy vấn CSDL bảng `rooms` kết hợp `buildings` lấy tối đa 5 phòng trống (`status = 'empty'`) phù hợp nhất.
   - Nạp thông tin phòng vào đoạn văn bản ngữ cảnh Context.
2. **Giai đoạn Tăng cường & Sinh câu trả lời (Augmented Generation Phase)**:
   - Gửi Context phòng và câu hỏi người dùng đến Google Gemini API (`gemini-2.5-flash`).
   - Ràng buộc AI tuân thủ nguyên tắc: *"Chỉ trả lời dựa trên danh sách phòng trong Context. Không tự bịa đặt phòng không có thật."*
3. **Phản hồi**: Nhận văn bản trả lời từ AI và bóc tách danh sách phòng đính kèm (Card Preview) hiển thị trực tiếp trong khung chat.

#### 3. Database Operation
- **Đọc**: `rooms`, `buildings` (lọc phòng trống).

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "reply": "...", "suggested_rooms": [{"id": 8, "name": "...", "price": "...", "image": "..."}]}`.

#### 5. UI/UX Specification & Xử lý lỗi
- **Khung chat**: Cửa sổ nổi góc phải `#renty-chatbot-modal`, bong bóng chat người dùng và AI, hiệu ứng gõ phím 3 chấm nhảy.
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Gửi tin nhắn rỗng hoặc toàn khoảng trắng | Nút gửi bị vô hiệu hóa (disabled). Nếu cố tình gửi hiển thị: "Nội dung tin nhắn không được để trống". |
| Nhập câu hỏi quá dài (> 300 ký tự) | Ô chat bôi đỏ viền: "Câu hỏi quá dài (tối đa 300 ký tự). Vui lòng rút ngắn tiêu chí tìm kiếm của bạn". |
| Không tìm thấy phòng phù hợp trong CSDL | Chatbot trả lời thân thiện: "Renty chưa tìm thấy phòng trọ nào phù hợp với yêu cầu của bạn. Bạn thử nới rộng khoảng giá hoặc chọn khu vực lân cận nhé!". |

---

### [FEAT-VEM-08] Cổng dịch vụ Cư dân & Khách lưu trú (Guest Portal: Hóa đơn & Mã VietQR)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: `App\Http\Controllers\ResidentPortalController@index`, `billQr`, `requestRenewal`
- **Endpoint**:
  - `GET /smartroom/resident` (`smartroom.resident`)
  - `GET /smartroom/resident/bills/{id}/qr` (`smartroom.resident.bills.qr`)
  - `POST /smartroom/resident/contract/{id}/request-renewal` (`smartroom.resident.contract.request_renewal`)
- **Middleware**: `auth`

#### 1. Input Specification (Xin gia hạn hợp đồng POST /request-renewal)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `extension_months`| Integer | Có | `required\|integer\|min:1\|max:36` | "Thời gian xin gia hạn phải từ 1 đến 36 tháng." |
| `note` | String | Không | `nullable\|string\|max:500` | "Ghi chú gia hạn tối đa 500 ký tự." |

#### 2. Business Logic Flow
1. **Xác thực quyền Cư dân**: Lấy thông tin cư dân liên kết với tài khoản người dùng (`Resident::where('user_id', auth()->id())->first()`).
2. **Tổng hợp dữ liệu Dashboard Cư dân**:
   - Phòng đang thuê, thời hạn hợp đồng, danh sách người ở cùng phòng.
   - Danh sách hóa đơn chưa thanh toán kèm nút "Quét mã VietQR thanh toán nhanh".
3. **Hiển thị mã VietQR chuyển khoản**:
   - Khi bấm xem mã QR hóa đơn: Hệ thống sinh mã VietQR chứa đúng số tiền và cú pháp chuyển tiền để cư dân mở app ngân hàng quét thanh toán tức thì.

#### 3. Database Operation
- **Đọc**: `residents`, `rooms`, `contracts`, `bills`, `resident_relatives`.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Render view `resident.dashboard` hoặc trả về ảnh mã VietQR.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bấm mở VietQR khi hóa đơn đã được thanh toán | Toast xanh: "Hóa đơn tháng này của bạn đã được thanh toán đầy đủ. Cảm ơn bạn!". |
| Gửi xin gia hạn khi hợp đồng còn hạn trên 60 ngày | Toast cảnh báo: "Hợp đồng của bạn vẫn còn thời hạn dài (> 60 ngày). Hệ thống chỉ mở tính năng xin gia hạn trước khi hết hạn 30 ngày". |

---

### [FEAT-VEM-09] Tiếp nhận & Xử lý sự cố kỹ thuật và buồng phòng (Smart Tickets & AI NLP)
- **Git Branch**: `VinhEm/8-XuLyBaoHongDangDonPhong`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: `App\Http\Controllers\ResidentPortalController@storeTicket`, `analyzeTicket`
- **Endpoint**:
  - `POST /smartroom/resident/tickets` (`smartroom.resident.tickets.store`)
  - `POST /smartroom/resident/tickets/analyze` (`smartroom.resident.tickets.analyze`)
- **Middleware**: `auth`

#### 1. Input Specification (Gửi báo hỏng POST /tickets)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `title` | String | Có | `required\|string\|max:255` | "Tiêu đề sự cố không được để trống." |
| `description` | String | Có | `required\|string\|min:5\|max:1000` | "Vui lòng mô tả chi tiết sự cố hỏng hóc (5 - 1000 ký tự)." |
| `category` | String | Có | `required\|in:electric,water,furniture,maintenance,other` | "Danh mục phân loại sự cố không hợp lệ." |
| `image` | File | Không | `nullable\|image\|mimes:jpeg,png,jpg,webp\|max:10240` | "Ảnh chụp hiện trạng sự cố tối đa 10MB." |

#### 2. Business Logic Flow
1. **AI NLP Phân tích độ khẩn cấp (analyzeTicket)**:
   - Sử dụng Google Gemini AI phân tích nội dung mô tả: Tự động đánh giá mức độ nghiêm trọng (`priority`: `low`, `medium`, `high`) và gợi ý biện pháp xử lý tạm thời cho cư dân (ví dụ: *"Khóa van nước tổng ngay lập tức để tránh ngập phòng"*).
2. **Lưu phiếu sự cố**: Tạo bản ghi trong bảng `tickets` với `status = 'pending'`, gán `resident_id`, `room_id`, `priority` từ AI.
3. **Thông báo Ban Quản Lý**: Tự động gửi thông báo đến chủ trọ / nhân viên kỹ thuật để điều phối sửa chữa.

#### 3. Database Operation
- **Bảng tác động**: `tickets`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về danh sách ticket kèm toast: "Đã gửi phiếu báo hỏng thành công. Kỹ thuật viên sẽ xử lý sớm nhất!".

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Cư dân gửi ticket nhưng để trống mô tả | Ô mô tả bôi đỏ: "Vui lòng nhập mô tả sự cố để ban quản lý nắm được nguyên nhân hư hỏng". |
| Tải ảnh sự cố vượt quá 10MB | Thông báo lỗi: "Kích thước ảnh chụp sự cố quá lớn. Vui lòng chọn ảnh dung lượng dưới 10MB". |

---

### [FEAT-VEM-10] Quản lý thông tin Cư dân & Thân nhân lưu trú theo phòng (Residents & Relatives)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: `AdminDashboardController@storeResident`, `updateResident`, `deleteResident`, `getRelatives`, `storeRelative`, `updateRelative`, `deleteRelative`
- **Endpoint**:
  - `POST /smartroom/admin/resident` (`smartroom.admin.resident.store`)
  - `PUT /smartroom/admin/resident/{id}` (`smartroom.admin.resident.update`)
  - `DELETE /smartroom/admin/resident/{id}` (`smartroom.admin.resident.delete`)
  - `GET /smartroom/admin/resident/{residentId}/relatives` (`smartroom.admin.resident.relatives`)
  - `POST /smartroom/admin/resident/{residentId}/relative` (`smartroom.admin.resident.relative.store`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Thêm người ở cùng POST /relative)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `name` | String | Có | `required\|string\|max:255` | "Vui lòng nhập họ và tên người ở cùng." |
| `phone` | String | Không | `nullable\|regex:/^[0-9]{10}$/` | "Số điện thoại người ở cùng không hợp lệ." |
| `cccd` | String | Có | `required\|regex:/^[0-9]{12}$/` | "Số CCCD người ở cùng phải đủ 12 chữ số." |
| `relationship` | String | Có | `required\|string\|max:100` | "Vui lòng ghi rõ quan hệ nhân thân (Bạn bè, Vợ/Chồng, Anh em)." |

#### 2. Business Logic Flow
1. **Kiểm tra sức chứa của phòng**:
   - Đếm tổng số người đang ở phòng đó (1 cư dân đại diện + số thân nhân hiện tại).
   - Nếu vượt quá sức chứa tối đa quy định của phòng, ném lỗi HTTP 422: *"Số lượng người ở cùng vượt quá sức chứa tối đa của phòng"*.
2. **Mã hóa PII**: Dữ liệu SĐT và CCCD của người ở cùng được mã hóa AES-256-GCM trước khi lưu vào bảng `resident_relatives`.
3. **Phản hồi**: Trả về danh sách thân nhân cập nhật dưới dạng JSON.

#### 3. Database Operation
- **Bảng tác động**: `residents`, `resident_relatives`.

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "message": "Thêm người ở cùng phòng thành công!"}`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Thêm người ở cùng nhưng bỏ trống CCCD | Ô CCCD bôi đỏ: "Vui lòng điền họ tên và số CCCD hợp lệ của người ở cùng". |
| Số người ở cùng vượt quá sức chứa phòng | Cảnh báo quá tải: "Phòng này chỉ có sức chứa tối đa 2 người. Vui lòng kiểm tra lại quy định phòng". |

---

### [FEAT-VEM-11] Tự động kết xuất tờ khai đăng ký tạm trú Mẫu CT01 (Bộ Công an)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: `App\Http\Controllers\AdminDashboardController@exportCt01`
- **Endpoint**: `GET /smartroom/admin/resident/{id}/export-ct01` (`smartroom.admin.resident.export_ct01`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Tham số Route)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `id` | Integer (Route) | Có | `exists:residents,id` | "Hồ sơ cư dân không tồn tại trên hệ thống." |

#### 2. Business Logic Flow
1. **Tổng hợp dữ liệu nhân thân**:
   - Giải mã số CCCD, SĐT của cư dân đại diện và danh sách người ở cùng phòng từ bảng `residents` và `resident_relatives`.
   - Lấy địa chỉ cơ sở lưu trú từ bảng `buildings`, thông tin chủ hộ/chủ trọ từ bảng `landlord_profiles`.
2. **Kiểm tra tính đầy đủ của hồ sơ (Completeness Guard)**:
   - Nếu cư dân còn thiếu Quê quán (`hometown`), Ngày sinh (`dob`) hoặc Số CCCD: Chặn xuất và yêu cầu cập nhật hồ sơ trước.
3. **Điền biểu mẫu Mẫu CT01 tự động (DomPDF)**:
   - Tự động điền đúng các mục hành chính theo chuẩn Bộ Công an: Họ tên, Ngày tháng năm sinh, Giới tính, Số định danh cá nhân CCCD, Nơi thường trú, Nơi tạm trú, Ý kiến của chủ hộ/chủ cơ sở lưu trú.
4. **Xuất file**: Kết xuất file PDF Mẫu CT01 sẵn sàng in để nộp Công an phường/xã.

#### 3. Database Operation
- **Đọc**: `residents`, `resident_relatives`, `buildings`, `landlord_profiles`. Không ghi CSDL.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Tải về file PDF tên `Mau_CT01_TamTru_{TenCuDan}.pdf`.
- **Thất bại (HTTP 422)**: Báo lỗi hồ sơ cư trú chưa đầy đủ thông tin.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Xuất CT01 khi thiếu thông tin quê quán / ngày sinh | Modal cảnh báo: "Hồ sơ cư trú chưa đầy đủ thông tin (thiếu Quê quán / Ngày sinh). Vui lòng cập nhật đầy đủ trước khi xuất mẫu CT01". |

---

### [FEAT-VEM-12] Tiện ích Đăng ký nhận chuông báo khi phòng chuyển sang trống (Empty Room Alert)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: `AdminDashboardController@storeContactRequest`, `updateContactRequestStatus`
- **Endpoint**: `POST /renty/contact-request` (`renty.contact_request.store`)
- **Middleware**: `web`, `throttle:5,1`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_id` | Integer | Có | `required\|integer\|exists:rooms,id` | "Phòng đăng ký theo dõi không tồn tại." |
| `name` | String | Có | `required\|string\|max:255` | "Vui lòng nhập họ và tên của bạn." |
| `phone` | String | Có | `required\|regex:/^(0[3\|5\|7\|8\|9])[0-9]{8}$/` | "Số điện thoại nhận chuông báo không hợp lệ." |
| `note` | String | Không | `nullable\|string\|max:500` | "Ghi chú tối đa 500 ký tự." |

#### 2. Business Logic Flow
1. **Kiểm tra trùng lặp đăng ký**:
   Kiểm tra trong bảng `contact_requests` xem SĐT này đã đăng ký theo dõi phòng này trong 30 ngày qua chưa. Nếu đã có, trả về thông báo đã ghi nhận.
2. **Lưu phiếu đăng ký**: Tạo bản ghi trong `contact_requests` với `type = 'empty_room_alert'`, `status = 'pending'`.
3. **Cơ chế kích hoạt chuông báo tự động**:
   Khi một hợp đồng thuê kết thúc và phòng chuyển trạng thái từ `occupied` sang `empty` (hoặc nhân viên dọn phòng xong), hệ thống tự động quét danh sách `contact_requests` của phòng đó và gửi tin nhắn SMS/Zalo thông báo cho khách: *"Phòng [Số_Phòng] tại [Địa_Chỉ] bạn đang theo dõi hiện đã trống! Bấm vào đây để đặt phòng ngay: [Link]"*.

#### 3. Database Operation
- **Bảng tác động**: `contact_requests`, `notification_logs`.

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "message": "Đăng ký nhận chuông báo thành công! Renty sẽ nhắn tin ngay khi phòng này có người trả."}`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống số điện thoại nhận thông báo | Viền đỏ ô SĐT: "Vui lòng nhập số điện thoại để hệ thống gửi thông báo". |
| Số điện thoại đã đăng ký nhận thông báo phòng này | Toast thông báo: "Bạn đã đăng ký nhận chuông báo cho phòng này rồi. Hệ thống sẽ nhắn tin ngay khi phòng trống!". |
"""
