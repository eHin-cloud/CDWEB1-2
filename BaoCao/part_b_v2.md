## B. PHÂN HỆ DO HUỲNH VĂN VĨNH EM (NHÓM PHÓ) PHỤ TRÁCH

### 1. ĐẶC TẢ KỸ THUẬT: CỔNG RENTY PORTAL, BỘ LỌC TIỆN ÍCH & SO SÁNH PHÒNG ĐA TIÊU CHÍ

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-VINHEM-01` |
| **Loại tác vụ (Type)** | Public Portal / UI-UX / Multi-criteria Algorithm |
| **Nhánh Git (Branches)** | `main`, `feature/renty-portal-filters-compare` |
| **Người thực hiện (Assignee)** | Huỳnh Văn Vĩnh Em (Nhóm Phó) |
| **Package / Standards** | Tailwind CSS 3.4 (Glassmorphism), Vanilla JS (ES6+), Redis Cache Tags |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| GET | `/renty` | `web` | Trang chủ tìm kiếm phòng phong cách Glassmorphism |
| GET | `/api/renty/rooms` | `api`, `throttle:60,1` | API lấy danh sách phòng công cộng kèm bộ lọc đa tiêu chí |
| POST | `/api/renty/rooms/compare` | `api`, `throttle:30,1` | So sánh đối chiếu song song từ 2 đến 3 phòng được chọn |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- **Truy vấn danh sách phòng công cộng:** Lọc theo khoảng giá, quận/huyện, tiện ích (gác lửng, ban công, thú cưng). Kết quả được lưu Redis Cache theo key `renty:rooms:filter:{md5_query}` với TTL 300 giây.
- **Động cơ Tính điểm So sánh Đa tiêu chí (Thang điểm 10):**
  1. $Score_{price} = \max\left(0, \min\left(10, \frac{5.000.000 - Price}{300.000} + 2\right)\right)$
  2. $Score_{dist} = \max\left(0, \min\left(10, (2.0 - Distance) \times 6\right)\right)$
  3. $Score_{sec} = Stars_{sec} \times 2$
  4. $Score_{owner} = Stars_{owner} \times 2$

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi So sánh Thành công
- **HTTP Status Code:** `200 OK`
```json
{
  "success": true,
  "data": [
    {
      "id": 101,
      "room_number": "P.101",
      "price": 3200000,
      "price_formatted": "3.200.000đ",
      "distance": 0.5,
      "scores": {
        "price": 8.0,
        "distance": 9.0,
        "security": 10.0,
        "owner": 10.0
      },
      "amenities": ["Gác lửng", "Ban công", "Thú cưng"]
    }
  ]
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /api/renty/rooms/compare`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `ids` | Array | Có | `required\|array\|min:2\|max:3` | Danh sách phòng so sánh bắt buộc từ 2 đến 3 phòng. |
| `ids.*` | Integer | Có | `integer\|exists:rooms,id` | Một trong các phòng được chọn không tồn tại trong CSDL. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Truy vấn bảng `rooms`:** Tìm các phòng có `status == 'empty'`.
- **Cache Invalidation:** Xóa toàn bộ Redis tag `renty_rooms` khi có sự kiện `RoomSavedEvent` hoặc `RoomDeletedEvent`.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Người dùng chọn quá 3 phòng để so sánh | Khóa checkbox của các phòng còn lại, hiển thị Toast cảnh báo: "Bạn chỉ có thể so sánh đối đầu tối đa 3 phòng." |
| Nhấn nút "So sánh ngay" khi mới chọn 1 phòng | Nút bấm bị mờ (disabled), hiển thị Tooltip nhắc nhở: "Vui lòng chọn thêm ít nhất 1 phòng nữa để so sánh." |
| Bộ lọc tìm kiếm không có kết quả phù hợp (Empty State) | Hiển thị hình minh họa kính lúp rỗng kèm thông báo: "Không tìm thấy phòng phù hợp" và nút "Đặt lại bộ lọc". |
| Giá thuê phòng hiển thị chuỗi số dính liền không phân cách | Tự động format định dạng tiền tệ Việt Nam `3.500.000đ` có dấu chấm phân cách hàng nghìn rõ ràng. |
| Chuyển trang danh sách phòng khi đang cuộn ở cuối trang | Tự động cuộn mượt (Smooth Scroll) lên đầu danh sách sau khi dữ liệu trang mới tải xong. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Gửi mảng `ids` rỗng hoặc sai số lượng | 422 | `COMPARE_INVALID_COUNT` | Trả về lỗi: "Danh sách phòng cần so sánh bắt buộc phải từ 2 đến 3 phòng." |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Giao diện Renty Portal tải nhanh dưới 1.5 giây, chuẩn Responsive 100% không lỗi vỡ khung.
- [ ] So sánh đối đầu 2-3 phòng trả về bảng điểm và biểu đồ Radar trực quan.
- [ ] Danh sách phòng được cache trên Redis với tag `renty_rooms`, tự động xóa khi có thay đổi giá.

---

### 2. ĐẶC TẢ KỸ THUẬT: CHI TIẾT PHÒNG, CẢNH BÁO GIÁ ẢO & REVIEW CƯ DÂN CÓ XÁC THỰC

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-VINHEM-02` |
| **Loại tác vụ (Type)** | Anti-Fraud / Rating & Review System / Room Details |
| **Nhánh Git (Branches)** | `main`, `feature/room-detail-reviews-fraud-detection` |
| **Người thực hiện (Assignee)** | Huỳnh Văn Vĩnh Em (Nhóm Phó) |
| **Package / Standards** | Swiper Carousel, Anti-Fraud Mathematical Bounds, Blade Components |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| GET | `/renty/room/{id}` | `web` | Trang chi tiết phòng lưu trú, tiện ích, ảnh 360 và hồ sơ chủ trọ |
| POST | `/renty/room/{id}/review` | `auth`, `throttle:5,1` | Gửi đánh giá chấm sao và bình luận thực tế (có kiểm tra hợp đồng) |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- **Phát hiện Giá ảo (Price Anomaly Detection):**
  - Tính giá trung bình quận: $\overline{P}_{\text{district}}$.
  - Nếu giá niêm yết $P < 0.5 \times \overline{P}_{\text{district}}$: Gắn huy hiệu vàng cảnh báo lừa cọc.
- **Xác minh Quyền Review (Verified Reviews Guard):**
  - Kiểm tra `Contract::where('room_id', $id)->where('user_id', auth()->id())->whereIn('status', ['active', 'completed'])->exists()`.
  - Nếu không tồn tại: Chặn ngay lập tức với mã lỗi `AUTH_UNAUTHORIZED_REVIEW`.

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Gửi Đánh giá Thành công
- **HTTP Status Code:** `201 Created`
```json
{
  "success": true,
  "message": "Cảm ơn bạn đã gửi đánh giá! Đánh giá đã được ghi nhận và hiển thị công khai.",
  "new_average_rating": 4.8
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /renty/room/{id}/review`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `rating` | Integer | Có | `required\|integer\|min:1\|max:5` | Điểm đánh giá chất lượng phải từ 1 đến 5 sao. |
| `comment` | String | Có | `required\|string\|min:10\|max:500` | Nội dung nhận xét phải từ 10 đến 500 ký tự. |
| `cleanliness_rating` | Integer | Không | `nullable\|integer\|min:1\|max:5` | Đánh giá độ sạch sẽ buồng phòng từ 1 đến 5 sao. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Bảng `reviews`:** `INSERT` bản ghi mới với `is_verified_tenant = true`.
- **Bảng `rooms`:** Tự động tính lại và cập nhật điểm trung bình `average_rating`.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Giá niêm yết thấp bất thường (< 50% mặt bằng quận) | Hiển thị dải băng cảnh báo màu vàng nổi bật: "Cảnh báo giá ảo - Vui lòng kiểm tra kỹ trước khi đặt cọc!" |
| Khách chưa từng thuê phòng bấm gửi đánh giá | Chặn form đánh giá, hiển thị thông báo: "Chỉ cư dân có hợp đồng hoàn tất tại phòng này mới có thể gửi đánh giá." |
| Gửi đánh giá nhưng chưa chọn số sao | Viền đỏ khu vực chấm sao và rung lắc nhẹ: "Vui lòng chọn từ 1 đến 5 sao trước khi gửi." |
| Album ảnh phòng độ phân giải cao tải chậm | Hiển thị ảnh xem trước mờ (Blur-up Placeholder) trước khi nạp xong ảnh sắc nét gốc. |
| Đánh giá chứa từ ngữ thô tục hoặc spam link | Bộ lọc ngôn từ phía client tự động phát hiện và cảnh báo: "Nội dung đánh giá chứa từ ngữ chưa chuẩn mực." |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Người dùng vãng lai cố tình gửi review qua Postman | 403 | `AUTH_UNAUTHORIZED_REVIEW` | Trả về HTTP 403: "Chỉ cư dân từng lưu trú mới có quyền gửi đánh giá phòng này." |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Phòng có giá thấp bất thường hiển thị cảnh báo giá ảo nổi bật.
- [ ] Chặn đứng 100% đánh giá ảo từ các tài khoản chưa có hợp đồng hợp lệ.
- [ ] Điểm trung bình sao của phòng được cập nhật tự động ngay khi có đánh giá mới.

---

### 3. ĐẶC TẢ KỸ THUẬT: BÁO CÁO PHÒNG SAI PHẠM & XỬ LÝ KHIẾU NẠI LỪA ĐẢO

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-VINHEM-03` |
| **Loại tác vụ (Type)** | Moderation / Consumer Protection / Trust & Safety |
| **Nhánh Git (Branches)** | `main`, `feature/room-violation-reports` |
| **Người thực hiện (Assignee)** | Huỳnh Văn Vĩnh Em (Nhóm Phó) |
| **Package / Standards** | Laravel Storage API, Image Validation, Moderation Workflow |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| POST | `/renty/room/{id}/report` | `web`, `throttle:5,1` | Khách gửi báo cáo phòng sai phạm kèm bằng chứng hình ảnh |
| POST | `/admin/reports/{id}/resolve` | `auth`, `role:admin` | Superadmin xử lý khiếu nại (Gỡ phòng, cảnh cáo, bác bỏ) |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- Khách gửi báo cáo $\to$ Lưu vào `room_reports` với `status = 'pending'`.
- Superadmin xem xét:
  - Nếu vi phạm nghiêm trọng (lừa cọc, ảnh giả mạo): Chuyển `rooms.status = 'inactive'`, gỡ khỏi Cổng Renty.

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Gửi Báo cáo Thành công
- **HTTP Status Code:** `201 Created`
```json
{
  "success": true,
  "message": "Cảm ơn bạn đã gửi báo cáo. Ban quản trị Renty sẽ tiến hành thẩm tra và xử lý trong vòng 24 giờ!"
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /renty/room/{id}/report`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `reason` | String | Có | `required\|in:fake_price,fake_photo,scam_deposit,wrong_address,other` | Lý do báo cáo vi phạm không hợp lệ. |
| `description` | String | Có | `required\|string\|min:20\|max:1000` | Mô tả chi tiết sự việc phải có ít nhất 20 ký tự. |
| `evidence_file` | File | Không | `nullable\|image\|mimes:jpeg,jpg,png,webp\|max:5120` | Ảnh bằng chứng không được vượt quá 5MB. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Bảng `room_reports`:** Tạo bản ghi mới.
- **Bảng `rooms`:** Cập nhật `status = 'inactive'` nếu Superadmin duyệt gỡ phòng.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Khách bấm nút "Hủy giao dịch" trên cổng thanh toán VNPAY | Chuyển hướng về trang phòng kèm thông báo vàng: "Giao dịch cọc đã hủy. Phòng vẫn giữ chỗ cho bạn trong 15 phút." |
| Tiền đã trừ ở ngân hàng nhưng Webhook chưa kịp phản hồi | Hiển thị màn hình chờ: "Hệ thống đang đồng bộ với cổng VNPAY..." kèm Spinner xoay tròn và polling kiểm tra trạng thái. |
| Số dư thẻ ngân hàng không đủ thanh toán | Cổng hiển thị thông báo chi tiết: "Giao dịch không thành công do số dư không đủ. Vui lòng chọn phương thức khác." |
| Người dùng bấm nút "Thanh toán cọc" 2 lần liên tiếp | Nút bấm lập tức bị khóa sau cú click đầu tiên, hiển thị text "Đang tạo giao dịch..." để tránh trùng lặp. |
| Hết thời gian giữ chỗ 15 phút mà chưa thanh toán | Đồng hồ đếm ngược về 00:00, phòng tự động hủy giữ chỗ và mở lại trạng thái `empty` cho khách khác đặt. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Spam gửi báo cáo phá hoại đối thủ | 429 | `REPORT_RATE_LIMIT_EXCEEDED` | Giới hạn tối đa 3 báo cáo / 1 giờ trên mỗi IP/User. Trả về HTTP 429. |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Gửi báo cáo kèm ảnh bằng chứng lưu thành công vào CSDL.
- [ ] Superadmin có giao diện duyệt báo cáo và thực hiện gỡ phòng sai phạm 1-click.
- [ ] Phòng bị gỡ lập tức biến mất khỏi Cổng Renty Portal.

---

### 4. ĐẶC TẢ KỸ THUẬT: TRỢ LÝ ẢO AI RENTY CHATBOT THEO MÔ HÌNH RAG

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-VINHEM-04` |
| **Loại tác vụ (Type)** | Generative AI / RAG Chatbot / Natural Language Processing |
| **Nhánh Git (Branches)** | `main`, `feature/ai-rag-renty-chatbot` |
| **Người thực hiện (Assignee)** | Huỳnh Văn Vĩnh Em (Nhóm Phó) |
| **Package / Standards** | Google Gemini 2.5 Flash, Entity Extraction, Full-text Search, Redis Throttle |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| POST | `/renty/chatbot/chat` | `web`, `throttle:60,1` | Hỏi đáp tự nhiên với trợ lý ảo RAG dựa trên CSDL phòng thực tế |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
```text
Guest: "Tìm phòng dưới 4 triệu ở Thủ Đức có ban công"
                    |
                    v
         [POST /renty/chatbot/chat]
                    |
                    v
    [Entity Extraction: searchRooms(prompt)]
    Khoảng giá: <= 4.000.000đ | Khu vực: Thủ Đức | Tiện ích: Ban công
                    |
                    v
[Query MySQL: Tìm tối đa 5 phòng trống (status == 'empty') phù hợp]
                    |
                    v
[Nạp CSDL phòng thực tế vào System Prompt của Google Gemini 2.5 Flash]
"BẠN LÀ TRỢ LÝ RENTY AI. CHỈ DÙNG DỮ LIỆU ĐƯỢC CẤP Ở ĐÂY, KHÔNG TỰ BỊA ĐẶT PHÒNG."
                    |
                    v
[Gemini phản hồi trong 5 giây? (Timeout: 5s)]
        |                              |
       Có                             Không (hoặc Lỗi)
        |                              |
        v                              v
[Lời khuyên tự nhiên]           [Fallback Template câu trả lời tĩnh]
        |                              |
        +--------------+---------------+
                       |
                       v
   [Trả về: Văn bản trả lời + Carousel Rich Room Cards]
```

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Chatbot Phản hồi Thành công
- **HTTP Status Code:** `200 OK`
```json
{
  "success": true,
  "response": "Renty tìm thấy phòng P.201 tại Thủ Đức rất phù hợp với ngân sách dưới 4 triệu của bạn!",
  "rooms": [
    {
      "id": 201,
      "room_number": "201",
      "price": 3500000,
      "address": "45 Lê Văn Việt, Tăng Nhơn Phú A, TP. Thủ Đức",
      "cover_image": "/storage/rooms/p201.jpg"
    }
  ],
  "used_ai": true
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /renty/chatbot/chat`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `prompt` | String | Có | `required\|string\|min:2\|max:300` | Câu hỏi tìm phòng bắt buộc từ 2 đến 300 ký tự. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Truy vấn bảng `rooms`:** Tìm kiếm kết hợp `price <= ?` và `address LIKE ?` và `amenities LIKE ?`.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Trợ lý ảo AI đang suy nghĩ và soạn câu trả lời | Hiển thị hiệu ứng 3 dấu chấm nhấp nháy (Typing Indicator) kèm text "Renty AI đang tìm phòng phù hợp...". |
| Khách hỏi những câu hỏi ngoài nghiệp vụ thuê phòng | Chatbot lịch sự phản hồi: "Em là trợ lý tìm phòng Renty, em chỉ hỗ trợ các câu hỏi về phòng trọ và tiện ích ạ!" |
| Khách nhập tin nhắn quá dài vượt quá 500 ký tự | Hiển thị số đếm ký tự màu đỏ ở góc ô chat: "Đã vượt quá 500 ký tự quy định." |
| Mất kết nối WebSocket khi đang nhắn tin với bot | Góc trên hộp thoại chat hiển thị chấm đỏ "Mất kết nối", hệ thống tự động kết nối lại sau mỗi 3 giây. |
| Bấm vào thẻ phòng trong câu trả lời của Chatbot | Mở tab mới dẫn thẳng đến trang chi tiết phòng lưu trú với đầy đủ ảnh 360 và mức giá. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Mất mạng hoặc Gemini API timeout > 5s | 200 | `FALLBACK_CHATBOT_TEMPLATE` | Tự động kích hoạt câu trả lời tĩnh từ Template, không bao giờ để lộ lỗi 500 ra giao diện. |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Chatbot phản hồi trung bình dưới 2.5 giây.
- [ ] 100% phòng được gợi ý là phòng có thật trong CSDL và đang ở trạng thái trống (`empty`).
- [ ] Giao diện trả về kèm Rich Room Cards có thể click xem chi tiết ngay lập tức.

---

### 5. ĐẶC TẢ KỸ THUẬT: CỔNG CƯ DÂN & THEO DÕI HÓA ĐƠN THANH TOÁN VIETQR

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-VINHEM-05` |
| **Loại tác vụ (Type)** | Resident Portal / Payment Integration / VietQR NAPAS247 |
| **Nhánh Git (Branches)** | `main`, `feature/resident-guest-portal-vietqr` |
| **Người thực hiện (Assignee)** | Huỳnh Văn Vĩnh Em (Nhóm Phó) |
| **Package / Standards** | Mobile-first Portal, NAPAS247 VietQR Standard, Session Auth |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| GET | `/smartroom/resident` | `auth`, `role:resident` | Cổng thông tin cá nhân dành riêng cho Cư dân thuê phòng |
| GET | `/smartroom/resident/bills` | `auth`, `role:resident` | Xem danh sách hóa đơn tiền phòng và mở mã VietQR thanh toán |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- Cư dân đăng nhập $\to$ Hệ thống nạp danh sách hóa đơn thuộc `resident_id`.
- Với hóa đơn chưa thanh toán (`status IN ('sent', 'overdue')`), tự động tạo URL VietQR chuẩn NAPAS247:
  `https://img.vietqr.io/image/{bank_bin}-{bank_account_no}-compact.png?amount={amount}&addInfo={addInfo}&accountName={accountName}`

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Xem Hóa đơn Thành công
- **HTTP Status Code:** `200 OK`
```json
{
  "success": true,
  "unpaid_bills_count": 1,
  "bills": [
    {
      "id": 142,
      "billing_month": "2026-09",
      "room_number": "P.201",
      "total_amount": 4250000,
      "status": "sent",
      "vietqr_url": "https://img.vietqr.io/image/970422-0123456789-compact.png?amount=4250000&addInfo=TT%20TIEN%20PHONG%20P201%20THANG%2009/2026&accountName=NGUYEN%20VAN%20A"
    }
  ]
}
```
##### 3.2. Quy tắc Xác thực Phiên làm việc
- Bắt buộc đăng nhập với vai trò `role:resident`, ràng buộc `resident_id = auth()->user()->resident_id`.

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Truy vấn bảng `utility_records` & `bills`:** Lọc theo `resident_id` của phiên hiện tại.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Cư dân chưa có hóa đơn cước phí nào cần thanh toán | Thẻ hóa đơn hiển thị huy hiệu xanh lá cây: "Bạn đã hoàn thành toàn bộ nghĩa vụ tài chính tháng này." |
| Ngày đến hạn đóng tiền hiển thị sai múi giờ UTC | Tự động chuyển đổi hiển thị chuẩn múi giờ Việt Nam: `Hạn đóng: 23:59 ngày 05/10/2026`. |
| Hồ sơ cư dân chưa được xác thực thông tin CCCD | Banner màu cam trên đầu trang nhắc nhở: "Vui lòng cập nhật CCCD để ban quản lý hoàn tất đăng ký tạm trú." |
| Ảnh mã VietQR thanh toán tải chậm do mạng yếu | Hiển thị khung chờ Skeleton hình vuông kích thước 250x250px tránh vỡ layout trang. |
| Nhấn nút "Gọi Hotline Ban quản lý" trên điện thoại | Tự động kích hoạt ứng dụng cuộc gọi điện thoại di động với số hotline đã lưu của tòa nhà. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Cố tình truy vấn ID hóa đơn của phòng khác | 403 | `TENANT_DATA_MISMATCH` | Scope tự động chặn và ném lỗi 403: "Bạn không có quyền truy cập vào hóa đơn của phòng này." |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Cư dân xem đúng thông tin phòng và hóa đơn của chính mình.
- [ ] Quét mã VietQR trên App ngân hàng tự động điền đúng 100% Số tài khoản, Số tiền và Cú pháp.
- [ ] Giao diện tối ưu hoàn hảo cho màn hình điện thoại di động (Mobile-first).

---

### 6. ĐẶC TẢ KỸ THUẬT: TIẾP NHẬN SỰ CỐ KỸ THUẬT SMART TICKETS & AI PHÂN LOẠI KHẨN CẤP

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-VINHEM-06` |
| **Loại tác vụ (Type)** | Maintenance / AI Text Classification / Incident Management |
| **Nhánh Git (Branches)** | `main`, `feature/smart-tickets-ai-priority` |
| **Người thực hiện (Assignee)** | Huỳnh Văn Vĩnh Em (Nhóm Phó) |
| **Package / Standards** | Google Gemini Text Classifier, Storage API, Laravel Reverb WebSockets |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| POST | `/smartroom/resident/tickets` | `auth`, `role:resident` | Cư dân gửi báo hỏng hóc thiết bị kèm ảnh hiện trạng |
| POST | `/smartroom/resident/tickets/analyze` | `auth`, `role:resident` | AI phân tích độ khẩn cấp (High/Med/Low) và gợi ý an toàn |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- Tiếp nhận mô tả sự cố $\to$ Nạp vào Gemini AI:
  - `high`: Chập điện, bốc khói, nhảy aptomat, vỡ ống nước chính (nguy cơ cháy nổ, ngập lụt).
  - `medium`: Tắc bồn cầu, máy lạnh chảy nước.
  - `low`: Cháy bóng đèn, kẹt khóa tủ.
- Ticket mức `high` lập tức phát WebSockets qua Laravel Reverb, làm nhấp nháy thẻ cảnh báo đỏ `animate-pulse` trên Dashboard của Ban quản lý tòa nhà.

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Gửi Ticket Thành công
- **HTTP Status Code:** `201 Created`
```json
{
  "success": true,
  "message": "Gửi yêu cầu hỗ trợ sửa chữa thành công!",
  "ticket": {
    "id": 142,
    "title": "Chập điện ổ cắm khu vực bếp",
    "priority": "high",
    "status": "pending",
    "ai_suggestion": "Ngắt ngay aptomat khu vực bếp để đảm bảo an toàn PCCC!"
  }
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /smartroom/resident/tickets`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `title` | String | Có | `required\|string\|max:150` | Tiêu đề sự cố không được để trống. |
| `description` | String | Có | `required\|string\|min:10\|max:1000` | Mô tả hiện trạng hư hỏng bắt buộc từ 10 đến 1000 ký tự. |
| `category` | String | Có | `required\|in:electric,water,furniture,maintenance,other` | Hạng mục sự cố không hợp lệ. |
| `image` | File | Không | `nullable\|image\|mimes:jpeg,jpg,png,webp\|max:3072` | Ảnh chụp hiện trạng không vượt quá 3MB. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Bảng `tickets`:** Tạo bản ghi mới với `status = 'pending'`, `priority` do AI phân loại.
- **Sự kiện WebSockets:** Bắn `CriticalTicketCreatedEvent` qua Reverb khi `priority === 'high'`.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Tải ảnh hiện trạng sự cố hỏng hóc vượt quá 3MB | Client kiểm tra kích thước trước khi tải lên, báo lỗi: "Kích thước ảnh tối đa 3MB. Vui lòng chọn ảnh nhẹ hơn!" |
| Chưa chọn mức độ khẩn cấp của sự cố (Gấp / Thường) | Tự động gán mức "Thường" làm mặc định để không gây gián đoạn quá trình gửi phản ánh. |
| Sự cố đã được kỹ thuật viên xử lý và thay thế vật tư | Thẻ ticket chuyển sang màu xanh lá "Đã khắc phục", hiển thị nút "Đánh giá chất lượng phục vụ". |
| Kỹ thuật viên chưa đính kèm ảnh nghiệm thu sau sửa | Chặn thao tác bấm "Hoàn tất ticket", yêu cầu tải lên ít nhất 1 ảnh chụp hiện trạng đã sửa xong. |
| Danh sách phản ánh sự cố trống (Empty State) | Hiển thị hình minh họa cờ lê và thông báo: "Không có sự cố kỹ thuật nào đang chờ xử lý." |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Lỗi dịch vụ AI phân tích độ khẩn cấp | 200 | `TICKET_AI_FALLBACK` | Tự động gán mặc định `priority = 'medium'`, lưu ticket bình thường, không làm gián đoạn việc gửi báo cáo. |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Gửi ticket kèm ảnh thành công, lưu bản ghi vào CSDL.
- [ ] AI tự động phân loại đúng mức `high` đối với các từ khóa chập điện, vỡ ống nước.
- [ ] Ticket khẩn cấp hiển thị huy hiệu đỏ nhấp nháy trên màn hình Ban quản lý.

---

### 7. ĐẶC TẢ KỸ THUẬT: QUẢN LÝ HỒ SƠ CƯ DÂN & THÂN NHÂN LƯU TRÚ (RESIDENT RELATIVES)

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-VINHEM-07` |
| **Loại tác vụ (Type)** | Resident Demographics / Relatives Management / Privacy Protection |
| **Nhánh Git (Branches)** | `main`, `feature/resident-relatives-management` |
| **Người thực hiện (Assignee)** | Huỳnh Văn Vĩnh Em (Nhóm Phó) |
| **Package / Standards** | Eloquent One-to-Many, OpenSSL AES-256-GCM |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| GET | `/smartroom/admin/resident/{id}/relatives` | `auth`, `role:landlord` | Lấy danh sách thân nhân, người ở cùng của cư dân chính |
| POST | `/smartroom/admin/resident/{id}/relatives` | `auth`, `role:landlord` | Thêm mới người ở cùng phòng (mã hóa thông tin CCCD) |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- Thêm thân nhân $\to$ Mã hóa số CCCD và SĐT bằng AES-256-GCM.
- Tính toán tổng nhân khẩu: $N_{\text{total}} = 1 + \text{count}(\text{relatives})$.
- Cập nhật số lượng nhân khẩu lên thẻ phòng trên Sơ đồ Ma trận.

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Thêm Thân nhân Thành công
- **HTTP Status Code:** `201 Created`
```json
{
  "success": true,
  "message": "Thêm thân nhân ở cùng phòng thành công!",
  "total_occupants": 3
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /smartroom/admin/resident/{id}/relatives`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `name` | String | Có | `required\|string\|max:255` | Họ và tên người ở cùng không được để trống. |
| `relationship` | String | Có | `required\|string\|max:50` | Mối quan hệ với người thuê chính không được để trống. |
| `cccd_number` | String | Không | `nullable\|string\|regex:/^[0-9]{12}$/` | Số CCCD phải gồm đúng 12 chữ số. |
| `phone` | String | Không | `nullable\|string\|regex:/^(0[3\|5\|7\|8\|9])[0-9]{8}$/` | Số điện thoại di động không đúng định dạng. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Bảng `resident_relatives`:** `INSERT` bản ghi mới liên kết `resident_id`.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Đăng ký thân nhân với số CCCD đã tồn tại trong CSDL | Ô nhập CCCD viền đỏ, hiển thị lỗi: "Số CCCD này đã được đăng ký cho một cư dân khác trong hệ thống." |
| Bỏ trống trường Mối quan hệ với chủ hộ thuê | Bắt buộc chọn từ Dropdown (Bố mẹ, Vợ/Chồng, Con cái, Bạn cùng phòng) trước khi bấm Lưu. |
| Danh sách cư dân dài hàng trăm người | Cung cấp ô tìm kiếm tức thời (Instant Search) lọc nhanh theo Tên, Số phòng hoặc SĐT mà không tải lại trang. |
| Cư dân đã hết hạn hợp đồng nhưng chưa làm thủ tục trả phòng | Tên cư dân gắn huy hiệu màu vàng cảnh báo: "Hợp đồng đã quá hạn - Chờ thanh lý." |
| Bấm nút "Xuất danh sách cư dân ra Excel" | Nút bấm hiển thị trạng thái "Đang xuất..." và tự động kích hoạt tải tệp `.xlsx` có dấu tiếng Việt chuẩn UTF-8. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Số nhân khẩu vượt quá `rooms.max_occupants` | 422 | `ROOM_CAPACITY_EXCEEDED` | Trả về HTTP 422: "Số lượng người ở vượt quá giới hạn sức chứa tối đa của phòng." |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Thêm, sửa, xóa thông tin thân nhân ở cùng mượt mà.
- [ ] Dữ liệu CCCD thân nhân được mã hóa AES-256 an toàn trong CSDL.
- [ ] Tổng số người ở hiển thị chính xác trên Ma trận phòng.

---

### 8. ĐẶC TẢ KỸ THUẬT: TỰ ĐỘNG ĐIỀN & KẾT XUẤT TỜ KHAI TẠM TRÚ MẪU CT01 (BỘ CÔNG AN)

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-VINHEM-08` |
| **Loại tác vụ (Type)** | Legal Automation / Public Administration / PDF Generation |
| **Nhánh Git (Branches)** | `main`, `feature/export-ct01-police-form` |
| **Người thực hiện (Assignee)** | Huỳnh Văn Vĩnh Em (Nhóm Phó) |
| **Package / Standards** | Barryvdh Laravel-DomPDF, Thông tư 56/2021/TT-BCA, UTF-8 Diacritics Engine |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| GET | `/smartroom/admin/resident/{id}/export-ct01` | `auth`, `role:landlord,manager` | Tự động điền dữ liệu và xuất tờ khai tạm trú Mẫu CT01 file PDF |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- Ánh xạ tự động 4 mục theo Thông tư 56/2021/TT-BCA:
  - Mục 1: Người kê khai (`name`, `dob`, `gender`, `cccd` giải mã AES-256, `phone`).
  - Mục 2: Nơi thường trú (`hometown`).
  - Mục 3: Nơi tạm trú (`building.address` + " - Phòng " + `room_number`).
  - Mục 4: Chủ cơ sở (`landlord_profile.full_name`, `cccd_number`).
- Thiết lập DomPDF: Khổ A4 đứng, căn lề 15mm, nhúng font `DejaVu Sans` chống lỗi dấu tiếng Việt.

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Kết xuất Thành công
- **HTTP Status Code:** `200 OK`
- **Headers:** `Content-Type: application/pdf`, `Content-Disposition: attachment; filename="CT01_{name}_{date}.pdf"`.

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Truy vấn bảng `residents`, `buildings`, `rooms`, `landlord_profiles`:** Nạp dữ liệu giải mã PII phục vụ in ấn.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Hồ sơ cư dân bị thiếu Quê quán hoặc Dân tộc | Bôi đỏ các ô dữ liệu bị thiếu và hiện cảnh báo: "Vui lòng cập nhật đủ thông tin trước khi in tờ khai CT01." |
| Bấm xem trước tờ khai Mẫu CT01 | Mở cửa sổ Modal Preview khổ A4 chuẩn biểu mẫu của Bộ Công an kèm thông tin đã điền sẵn. |
| Bấm nút "In tờ khai CT01" | Gọi lệnh `window.print()` với CSS ẩn toàn bộ menu và thanh công cụ website, chỉ in nội dung tờ khai. |
| Chọn nhiều cư dân để kết xuất tờ khai hàng loạt | Hệ thống tự động ghép nối thành một tệp PDF duy nhất gồm nhiều trang (mỗi người 1 trang A4 chuẩn). |
| Số định danh cá nhân CCCD không đủ 12 chữ số | Báo lỗi ngay dưới ô nhập liệu: "Số định danh cá nhân bắt buộc phải có đúng 12 chữ số." |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Thiếu trường bắt buộc trong hồ sơ cư dân | 422 | `CT01_INCOMPLETE_PROFILE` | Trả về HTTP 422: "Hồ sơ cư dân chưa đầy đủ thông tin pháp lý để xuất tờ khai CT01." |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] 1-Click xuất file PDF Mẫu CT01 hoàn chỉnh trong dưới 1.5 giây.
- [ ] Khớp 100% bố cục các trường theo quy định của Bộ Công An.
- [ ] Dấu tiếng Việt hiển thị sắc nét, in ra giấy đạt chuẩn pháp lý.

---

### 9. ĐẶC TẢ KỸ THUẬT: PHÂN HỆ LỄ TÂN KHÁCH SẠN & QUẢN LÝ BẢNG KÊ MINIBAR FOLIO

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-VINHEM-09` |
| **Loại tác vụ (Type)** | Hospitality Management / Dynamic Pricing / Folio Billing |
| **Nhánh Git (Branches)** | `main`, `feature/hotel-reception-minibar-folio` |
| **Người thực hiện (Assignee)** | Huỳnh Văn Vĩnh Em (Nhóm Phó) |
| **Package / Standards** | Hospitality Pricing Engine, Eloquent Relations (`HotelBooking`, `HotelFolioItem`) |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| POST | `/smartroom/admin/hotel/check-in` | `auth`, `role:landlord,manager` | Tiếp nhận khách thuê theo giờ hoặc theo ngày, nhận tiền cọc |
| POST | `/smartroom/admin/hotel/folio/{bookingId}/items` | `auth`, `role:landlord,manager` | Thêm dịch vụ tiêu hao minibar, giặt ủi vào bảng kê Folio |
| POST | `/smartroom/admin/hotel/check-out/{bookingId}` | `auth`, `role:landlord,manager` | Quyết toán cước, in hóa đơn và chuyển phòng sang `dirty` |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- **Thuật toán Động cơ Tính cước Phòng (Hospitality Pricing Engine):**
  - $\Delta T = \text{ceil}((\text{now}() - \text{check\_in\_at}) / 3600)$.
  - Nếu `rental_type === 'hour'`:
    - $\Delta T \le 2$: Tiền phòng = $100.000$đ.
    - $2 < \Delta T < 8$: Tiền phòng = $100.000 + (\Delta T - 2) \times 30.000$đ.
    - $\Delta T \ge 8$: Áp dụng giá ngày = $350.000$đ.
  - Nếu `rental_type === 'day'`: Tiền phòng = $\max(1, \text{ceil}((\text{now}() - \text{check\_in\_at}) / 86400)) \times 350.000$đ.
  - $\text{Phải thanh toán} = \text{Tiền phòng} + \sum \text{Dịch vụ Minibar} - \text{Tiền cọc}$.

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Check-out Thành công
- **HTTP Status Code:** `200 OK`
```json
{
  "success": true,
  "booking_id": 482,
  "duration_hours": 4,
  "room_charge": 160000,
  "minibar_total": 45000,
  "deposit_deducted": 100000,
  "total_due": 105000,
  "room_status": "dirty",
  "message": "Check-out hoàn tất. Phòng đã chuyển sang trạng thái chờ dọn buồng!"
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /smartroom/admin/hotel/check-in`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `room_id` | Integer | Có | `required\|integer\|exists:rooms,id` | Phòng khách sạn cần check-in không hợp lệ. |
| `guest_name` | String | Có | `required\|string\|max:255` | Họ và tên khách lưu trú không được để trống. |
| `rental_type` | String | Có | `required\|in:hour,day` | Hình thức thuê phòng phải là theo Giờ (hour) hoặc theo Ngày (day). |
| `deposit_amount` | Integer | Có | `required\|integer\|min:0` | Tiền cọc tạm ứng không được là số âm. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Bảng `hotel_bookings`:** `status = 'completed'`, `check_out_at = now()`.
- **Bảng `rooms`:** `status = 'empty'`, `cleaning_status = 'dirty'`.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Lễ tân bấm Check-in vào phòng đang có khách hoặc bẩn | Khóa nút Check-in, hiển thị cảnh báo đỏ: "Phòng P.102 hiện đang có khách hoặc chưa được dọn dẹp sạch sẽ!" |
| Nhập số lượng Minibar tiêu thụ lớn hơn số có trong tủ | Báo lỗi: "Số lượng sử dụng không được lớn hơn số lượng tồn thực tế trong Minibar phòng." |
| Thanh toán hóa đơn Folio tổng hợp khi trả phòng | Tự động cộng tiền phòng, tiền nước ngọt/bia Minibar và trừ tiền cọc; làm tròn số tiền về số nguyên. |
| In phiếu thanh toán cho khách lưu trú | Kết xuất phiếu hóa đơn định dạng in nhiệt khổ K80 gọn gàng, rõ nét tên phòng và chi tiết đồ dùng. |
| Bấm Check-out khi khách còn nợ tiền dịch vụ | Mở Modal cảnh báo đỏ: "Khách còn nợ 350.000đ. Bạn có chắc chắn muốn cho khách check-out không?" |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Check-in vào phòng đang có khách hoặc bẩn | 422 | `HOTEL_ROOM_NOT_EMPTY` | Ném lỗi: "Không thể check-in. Phòng hiện đang có khách ở hoặc chưa được dọn dẹp sạch sẽ!" |
| Cố tình check-out booking đã hoàn tất | 400 | `HOTEL_BOOKING_CLOSED` | Chặn lại: "Lượt đặt phòng này đã được quyết toán trước đó." |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Tính toán cước theo giờ/ngày lũy tiến chính xác 100% theo công thức toán.
- [ ] Bảng kê Minibar cộng dồn chuẩn xác, tự động trừ tiền cọc đã thu trước.
- [ ] Phòng tự động chuyển sang trạng thái chờ dọn dẹp (`dirty`) ngay sau khi check-out.

---

### 10. ĐẶC TẢ KỸ THUẬT: PHÂN HỆ BUỒNG PHÒNG & MÁY TRẠNG THÁI DỌN DẸP (FSM)

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-VINHEM-10` |
| **Loại tác vụ (Type)** | Housekeeping / Finite State Machine / Realtime Dispatching |
| **Nhánh Git (Branches)** | `main`, `feature/housekeeping-fsm-workflow` |
| **Người thực hiện (Assignee)** | Huỳnh Văn Vĩnh Em (Nhóm Phó) |
| **Package / Standards** | Mobile Web UI, Finite State Machine, Laravel Reverb WebSockets |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| GET | `/smartroom/housekeeping` | `auth`, `role:landlord,manager` | Giao diện di động danh sách buồng phòng cần dọn dẹp |
| POST | `/smartroom/housekeeping/{roomId}/status` | `auth`, `role:landlord,manager` | Cập nhật trạng thái dọn buồng theo ma trận FSM |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- **Ma trận Chuyển trạng thái Buồng phòng (FSM):**
  $$\text{dirty} \xrightarrow{\text{Bấm "Bắt đầu dọn"}} \text{cleaning} \xrightarrow{\text{Bấm "Hoàn tất dọn"}} \text{clean}$$
- Nghiêm cấm mọi hành vi nhảy cóc trạng thái (từ `dirty` nhảy thẳng sang `occupied`).
- Khi phòng chuyển sang `clean`: Lập tức phát WebSockets thông báo cho Lễ tân và kích hoạt hiển thị phòng trống trên Cổng Renty.

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Cập nhật Trạng thái Thành công
- **HTTP Status Code:** `200 OK`
```json
{
  "success": true,
  "room_id": 105,
  "cleaning_status": "clean",
  "message": "Phòng P.105 đã dọn sạch sẽ và sẵn sàng đón khách mới!"
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /smartroom/housekeeping/{roomId}/status`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `status` | String | Có | `required\|in:dirty,cleaning,clean` | Trạng thái buồng phòng không hợp lệ. |
| `notes` | String | Không | `nullable\|string\|max:255` | Ghi chú dọn dẹp tối đa 255 ký tự. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Bảng `rooms`:** Cập nhật `cleaning_status = $status`, `updated_at = now()`.
- **Sự kiện WebSockets:** Bắn `RoomCleaningStatusChangedEvent` qua Reverb.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Bấm nút "Hoàn tất dọn" khi phòng chưa chuyển sang "Đang dọn" | Chặn thao tác trái quy trình FSM, yêu cầu nhân viên bấm nút "Bắt đầu dọn dẹp" trước. |
| Hai nhân viên buồng phòng cùng bấm nhận dọn 1 phòng | Hệ thống khóa phòng cho người bấm trước, người sau nhận thông báo: "Phòng này đã được tiếp nhận bởi nhân viên khác." |
| Thao tác trên điện thoại di động bằng một tay | Nút bấm "Bắt đầu" và "Hoàn tất" thiết kế kích thước lớn tối thiểu 50px, đặt ở vị trí thuận tiện ngón tay cái. |
| Mất sóng Internet khi đang bấm chuyển trạng thái buồng phòng | Lưu trạng thái vào bộ nhớ cục bộ (Offline storage) và tự động đồng bộ lên máy chủ ngay khi có mạng trở lại. |
| Phòng có ghi chú dọn dẹp đặc biệt (Khách dị ứng phấn hoa) | Thẻ phòng hiển thị icon chuông cảnh báo màu cam nhấp nháy để nhân viên lưu ý thay ga gối riêng. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Chuyển trạng thái trái quy tắc FSM | 422 | `FSM_INVALID_TRANSITION` | Trả về HTTP 422: "Không thể chuyển trực tiếp trạng thái. Vui lòng tuân thủ quy trình dọn dẹp!" |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Thao tác chuyển trạng thái phản hồi tức thì dưới 300ms.
- [ ] Thẻ phòng trên màn hình Lễ tân và Dashboard tự động đổi màu không cần reload trang.
- [ ] Ghi nhận thời gian bắt đầu và kết thúc dọn dẹp để đo lường KPI nhân viên.

---

### 11. ĐẶC TẢ KỸ THUẬT: TIỆN ÍCH ĐĂNG KÝ NHẬN CHUÔNG BÁO PHÒNG TRỐNG

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-VINHEM-11` |
| **Loại tác vụ (Type)** | Customer Engagement / Automated Notifications / Waitlist Queue |
| **Nhánh Git (Branches)** | `main`, `feature/room-availability-bell-alerts` |
| **Người thực hiện (Assignee)** | Huỳnh Văn Vĩnh Em (Nhóm Phó) |
| **Package / Standards** | Async Redis Queue (`notifications`), Zalo ZNS / SMS Gateway |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| POST | `/renty/room/{id}/subscribe-alert` | `web`, `throttle:5,1` | Khách đăng ký nhận chuông báo khi phòng đang ở chuyển sang trống |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- Khách để lại SĐT/Email $\to$ Lưu vào `room_availability_subscribers`.
- Khi phòng chuyển sang `clean` $\to$ Hệ thống kích hoạt Event, đẩy Job vào hàng đợi `notifications` gửi tin nhắn Zalo/SMS đến khách đăng ký kèm link xem phòng.

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Đăng ký Thành công
- **HTTP Status Code:** `200 OK`
```json
{
  "success": true,
  "message": "Đăng ký nhận chuông báo thành công! Renty sẽ thông báo ngay khi phòng sẵn sàng."
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /renty/room/{id}/subscribe-alert`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `phone` | String | Có | `required\|string\|regex:/^(0[3\|5\|7\|8\|9])[0-9]{8}$/` | Số điện thoại nhận tin nhắn không đúng định dạng. |
| `email` | String | Không | `nullable\|email\|max:255` | Địa chỉ email không đúng định dạng. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Bảng `room_availability_subscribers`:** `INSERT` bản ghi mới kèm `subscribed_at = now()`.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Khách đăng ký nhận chuông báo cho phòng đang còn trống | Báo Toast thông báo: "Phòng này hiện đang trống sẵn! Bạn có thể liên hệ đặt phòng ngay mà không cần chờ chuông." |
| Nhập số điện thoại nhận tin nhắn không đúng 10 số | Viền đỏ ô số điện thoại, báo lỗi: "Số điện thoại không hợp lệ (bắt buộc gồm 10 chữ số)." |
| Một số điện thoại đăng ký nhận tin 2 lần cho cùng 1 phòng | Hiển thị thông báo: "Số điện thoại của bạn đã nằm trong danh sách nhận thông báo phòng này trước đó." |
| Khách muốn hủy nhận tin nhắn chuông báo phòng trống | Cung cấp đường link "Hủy đăng ký" trực tiếp trong tin nhắn Zalo/SMS gửi đến khách hàng. |
| Đăng ký chuông báo thành công | Mở Modal hiển thị icon chuông rung kèm thông điệp cảm ơn và cam kết thông báo ngay khi phòng sẵn sàng. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Phòng đang trống nhưng vẫn gọi API đăng ký | 422 | `ROOM_ALREADY_AVAILABLE` | Trả về HTTP 422: "Phòng hiện đang có sẵn, không cần đăng ký chuông báo." |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Đăng ký chuông báo mượt mà, lưu vào CSDL.
- [ ] Khi phòng chuyển sang `clean`, tin nhắn thông báo được dispatch vào hàng đợi tự động.
- [ ] Khách nhận được tin nhắn Zalo kèm đường link trực tiếp đến chi tiết phòng.

---

### 12. ĐẶC TẢ KỸ THUẬT: KHÔNG GIAN THỰC TẾ ẢO 3D TRỰC QUAN (RENTY 3D ROOM TOUR)

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-VINHEM-12` |
| **Loại tác vụ (Type)** | Frontend 3D Graphics / Virtual Tour / WebGL |
| **Nhánh Git (Branches)** | `main`, `feature/renty-3d-room-tour` |
| **Người thực hiện (Assignee)** | Huỳnh Văn Vĩnh Em (Nhóm Phó) |
| **Package / Standards** | Three.js / Panolens.js, HTML5 WebGL, Equirectangular 360 Panorama |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| GET | `/renty/room/{id}/3d` | `web` | Giao diện xem phòng thực tế ảo 3D 360 độ tương tác không gian |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- Nạp ảnh toàn cảnh Panorama 360 vào khối cầu `SphereGeometry` của Three.js.
- Hỗ trợ xoay tự do qua chuột hoặc cảm biến con quay hồi chuyển (Gyroscope) trên smartphone.
- Gắn các Interactive Hotspots (Bấm để xem góc bếp, WC, ban công).

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Tải Phòng 3D Thành công
- **HTTP Status Code:** `200 OK`
```json
{
  "success": true,
  "room_id": 102,
  "panorama_url": "/storage/rooms/3d/p102_equirectangular.jpg",
  "hotspots": [
    {"pitch": 10.5, "yaw": -45.2, "title": "Gác lửng đúc cao 2m"},
    {"pitch": -15.0, "yaw": 110.8, "title": "Bếp từ và bồn rửa inox"}
  ]
}
```

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Bảng `rooms`:** Đọc đường dẫn `panorama_3d_path`.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Trình duyệt hoặc thiết bị cũ không hỗ trợ WebGL | Tự động chuyển đổi về chế độ xem bộ sưu tập ảnh góc rộng thông thường kèm thông báo: "Thiết bị không hỗ trợ 3D Viewer." |
| Ảnh toàn cảnh Panorama 360 dung lượng lớn tải chậm | Hiển thị thanh đo phần trăm nạp dữ liệu (Loading 0% - 100%) và hiển thị ảnh mờ trước trong khi chờ tải ảnh HD. |
| Bấm vào các điểm tương tác (Hotspot) trong phòng | Mở Popup nhỏ giới thiệu thông tin chi tiết: "Gác lửng đúc cao 2m", "Bếp từ và bồn rửa inox". |
| Xoay màn hình cảm ứng bị giật hoặc trễ khung hình | Áp dụng cơ chế làm mượt quán tính (Inertia Damping) giúp chuyển động xoay 360 độ đạt tốc độ mượt mà 60fps. |
| Nút chuyển đổi qua lại giữa chế độ 3D và xem ảnh phẳng | Thiết kế nút gạt Toggle trực quan ở góc màn hình, chuyển đổi giao diện tức thời không cần tải lại trang. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Phòng chưa có dữ liệu ảnh 360 | 404 | `PANORAMA_NOT_FOUND` | Điều hướng về trang chi tiết phòng thông thường kèm thông báo: "Phòng này chưa cập nhật ảnh 3D." |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Quả cầu 3D tương tác mượt mà đạt tốc độ 60fps trên Chrome và Safari mobile.
- [ ] Xoay mượt mà theo cảm ứng vuốt tay hoặc con quay hồi chuyển trên điện thoại.
- [ ] Chuyển đổi qua lại trơn tru giữa chế độ xem 3D và xem ảnh phẳng thông thường.
