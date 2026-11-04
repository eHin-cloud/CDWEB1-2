<footer class="renty-footer-wrapper border-t border-slate-800/60 bg-[#0e0e11] text-slate-400 mt-20 pt-12 pb-6 px-6 relative z-10 w-full">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-8 mb-12">
            <!-- Brand Info -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-gradient-to-r from-teal-500 to-cyan-500 rounded-xl text-white font-black text-sm tracking-wider shadow-md shadow-teal-500/15">
                        RENTY
                    </span>
                    <span class="text-xs font-bold text-slate-200 tracking-wider">STUDENT HOUSING</span>
                </div>
                <p class="text-xs leading-relaxed text-slate-500">
                    Nền tảng đánh giá và tìm kiếm phòng trọ sinh viên hàng đầu. Tránh bẫy phòng trọ ảo, xem review thực tế từ cộng đồng.
                </p>
                <div class="flex items-center gap-3 mt-2">
                    <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-teal-400 hover:border-teal-500/30 transition-all">
                        <i class="fa-brands fa-facebook-f text-xs"></i>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-teal-400 hover:border-teal-500/30 transition-all">
                        <i class="fa-brands fa-youtube text-xs"></i>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-teal-400 hover:border-teal-500/30 transition-all">
                        <i class="fa-brands fa-tiktok text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Explore Columns -->
            <div>
                <h4 class="text-xs font-extrabold text-slate-200 uppercase tracking-widest mb-4">Khu vực nổi bật</h4>
                <ul class="flex flex-col gap-2.5 text-xs">
                    <li><button type="button" onclick="filterByDistrict('Cầu Giấy')" class="hover:text-teal-400 transition-colors flex items-center gap-1.5 text-left cursor-pointer"><i class="fa-solid fa-location-dot text-[10px] text-teal-400"></i> Phòng trọ Cầu Giấy</button></li>
                    <li><button type="button" onclick="filterByDistrict('Đống Đa')" class="hover:text-teal-400 transition-colors flex items-center gap-1.5 text-left cursor-pointer"><i class="fa-solid fa-location-dot text-[10px] text-teal-400"></i> Phòng trọ Đống Đa</button></li>
                    <li><button type="button" onclick="filterByDistrict('Thanh Xuân')" class="hover:text-teal-400 transition-colors flex items-center gap-1.5 text-left cursor-pointer"><i class="fa-solid fa-location-dot text-[10px] text-teal-400"></i> Phòng trọ Thanh Xuân</button></li>
                    <li><button type="button" onclick="filterByDistrict('Hai Bà Trưng')" class="hover:text-teal-400 transition-colors flex items-center gap-1.5 text-left cursor-pointer"><i class="fa-solid fa-location-dot text-[10px] text-teal-400"></i> Phòng trọ Hai Bà Trưng</button></li>
                </ul>
            </div>

            <!-- Legal Links -->
            <div>
                <h4 class="text-xs font-extrabold text-slate-200 uppercase tracking-widest mb-4">Thông tin & Điều khoản</h4>
                <ul class="flex flex-col gap-2.5 text-xs">
                    <li><button type="button" onclick="openPolicyModal('about')" class="hover:text-teal-400 transition-colors flex items-center gap-2 text-left cursor-pointer"><i class="fa-solid fa-circle-info text-[11px] text-teal-400/80 w-3.5"></i> Về Renty</button></li>
                    <li><button type="button" onclick="openPolicyModal('terms')" class="hover:text-teal-400 transition-colors flex items-center gap-2 text-left cursor-pointer"><i class="fa-solid fa-file-contract text-[11px] text-teal-400/80 w-3.5"></i> Quy chế hoạt động</button></li>
                    <li><button type="button" onclick="openPolicyModal('privacy')" class="hover:text-teal-400 transition-colors flex items-center gap-2 text-left cursor-pointer"><i class="fa-solid fa-shield-halved text-[11px] text-teal-400/80 w-3.5"></i> Chính sách bảo mật</button></li>
                    <li><button type="button" onclick="openPolicyModal('contact')" class="hover:text-teal-400 transition-colors flex items-center gap-2 text-left cursor-pointer"><i class="fa-solid fa-headset text-[11px] text-teal-400/80 w-3.5"></i> Liên hệ hỗ trợ</button></li>
                    <li><button type="button" onclick="openPolicyModal('dispute')" class="hover:text-teal-400 transition-colors flex items-center gap-2 text-left cursor-pointer"><i class="fa-solid fa-scale-balanced text-[11px] text-teal-400/80 w-3.5"></i> Giải quyết khiếu nại</button></li>
                    <li><button type="button" onclick="openPolicyModal('faq')" class="hover:text-teal-400 transition-colors flex items-center gap-2 text-left cursor-pointer"><i class="fa-solid fa-circle-question text-[11px] text-teal-400/80 w-3.5"></i> Câu hỏi thường gặp (FAQ)</button></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="flex flex-col gap-4">
                <h4 class="text-xs font-extrabold text-slate-200 uppercase tracking-widest">Đăng ký nhận tin phòng mới</h4>
                <p class="text-xs leading-relaxed text-slate-500">
                    Nhận thông báo qua email ngay khi có phòng mới đầy đủ tiện ích trong khu vực bạn quan tâm.
                </p>
                <div class="flex gap-2">
                    <input type="email" id="footer-newsletter-email" onkeydown="if(event.key === 'Enter') subscribeNewsletter()" placeholder="Email của bạn..." class="px-3.5 py-2 w-full bg-[#121214] border border-slate-800 rounded-xl text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-teal-500/60 transition-colors">
                    <button type="button" id="btn-footer-newsletter" onclick="subscribeNewsletter()" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-500 hover:to-cyan-500 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-teal-600/20 whitespace-nowrap flex items-center justify-center gap-1.5 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i id="footer-newsletter-spinner" class="fa-solid fa-spinner fa-spin text-xs hidden"></i>
                        <span id="btn-footer-newsletter-text">Đăng ký</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="pt-8 border-t border-slate-800/40 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <span class="text-[10px] text-slate-500 font-bold">
                &copy; 2026 Renty Student Housing. Tất cả các quyền được bảo lưu.
            </span>
            <span class="text-[10px] text-slate-500 flex items-center gap-1">
                Made with <i class="fa-solid fa-heart text-rose-500"></i> for Vietnamese students.
            </span>
        </div>
    </div>
</footer>

<!-- POLICY & INFORMATION POPUP MODAL -->
<div id="renty-policy-modal" class="fixed inset-0 z-[120] hidden items-center justify-center p-3 sm:p-5 bg-slate-950/80 backdrop-blur-md transition-all duration-300" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="policy-modal-dialog relative w-full max-w-3xl bg-[#0d1322] border border-slate-800/80 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh] text-slate-300 animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="policy-modal-header px-6 py-4 border-b border-slate-800/80 flex items-center justify-between bg-slate-900/60 shrink-0">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center text-base shadow-sm">
                    <i id="policy-modal-header-icon" class="fa-solid fa-circle-info"></i>
                </span>
                <div>
                    <h3 id="policy-modal-header-title" class="text-base font-extrabold text-white">Về Renty Student Housing</h3>
                    <p class="policy-modal-subtitle text-[11px] text-slate-400">Nền tảng tìm & thuê phòng trọ sinh viên minh bạch, an toàn</p>
                </div>
            </div>
            <button type="button" onclick="closePolicyModal()" class="policy-modal-close-btn w-9 h-9 rounded-xl bg-slate-900/80 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800 flex items-center justify-center transition-all cursor-pointer" aria-label="Đóng popup">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Quick Switch Tab Bar -->
        <div class="policy-tab-bar px-6 py-2.5 bg-slate-950/40 border-b border-slate-800/60 overflow-x-auto flex items-center gap-2 shrink-0 scrollbar-none">
            <button type="button" onclick="switchPolicyTab('about')" id="tab-btn-about" class="policy-tab-btn active px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 cursor-pointer bg-teal-500/15 text-teal-300 border border-teal-500/30">
                <i class="fa-solid fa-circle-info text-[10px]"></i> Về Renty
            </button>
            <button type="button" onclick="switchPolicyTab('terms')" id="tab-btn-terms" class="policy-tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold transition-all shrink-0 flex items-center gap-1.5 cursor-pointer bg-slate-900/50 text-slate-400 hover:text-slate-200 border border-slate-800">
                <i class="fa-solid fa-file-contract text-[10px]"></i> Quy chế
            </button>
            <button type="button" onclick="switchPolicyTab('privacy')" id="tab-btn-privacy" class="policy-tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold transition-all shrink-0 flex items-center gap-1.5 cursor-pointer bg-slate-900/50 text-slate-400 hover:text-slate-200 border border-slate-800">
                <i class="fa-solid fa-shield-halved text-[10px]"></i> Bảo mật
            </button>
            <button type="button" onclick="switchPolicyTab('contact')" id="tab-btn-contact" class="policy-tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold transition-all shrink-0 flex items-center gap-1.5 cursor-pointer bg-slate-900/50 text-slate-400 hover:text-slate-200 border border-slate-800">
                <i class="fa-solid fa-headset text-[10px]"></i> Liên hệ
            </button>
            <button type="button" onclick="switchPolicyTab('dispute')" id="tab-btn-dispute" class="policy-tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold transition-all shrink-0 flex items-center gap-1.5 cursor-pointer bg-slate-900/50 text-slate-400 hover:text-slate-200 border border-slate-800">
                <i class="fa-solid fa-scale-balanced text-[10px]"></i> Giải quyết khiếu nại
            </button>
            <button type="button" onclick="switchPolicyTab('faq')" id="tab-btn-faq" class="policy-tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold transition-all shrink-0 flex items-center gap-1.5 cursor-pointer bg-slate-900/50 text-slate-400 hover:text-slate-200 border border-slate-800">
                <i class="fa-solid fa-circle-question text-[10px]"></i> FAQ
            </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="p-6 overflow-y-auto space-y-6 text-xs leading-relaxed text-slate-300">
            
            <!-- TAB CONTENT: VỀ RENTY -->
            <div id="policy-content-about" class="policy-tab-pane space-y-4">
                <div class="policy-mission-box p-4 rounded-2xl bg-gradient-to-r from-teal-500/10 via-cyan-500/10 to-transparent border border-teal-500/20">
                    <h4 class="text-sm font-extrabold text-teal-300 mb-1.5 flex items-center gap-2">
                        <i class="fa-solid fa-graduation-cap"></i> Sứ Mệnh Bảo Vệ Sinh Viên
                    </h4>
                    <p class="text-slate-300 text-xs">
                        <strong>Renty Student Housing</strong> ra đời nhằm giải quyết triệt để vấn nạn bẫy phòng trọ ảo, lừa cọc và thổi giá điện nước mà hàng triệu tân sinh viên gặp phải mỗi kỳ nhập học.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <div class="text-teal-400 font-bold mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-map-location-dot"></i> Bản đồ thông minh (Split View)
                        </div>
                        <p class="text-[11px] text-slate-400">Tích hợp tọa độ chính xác các trường ĐH/CĐ (CĐ Công nghệ Thủ Đức, ĐH Sài Gòn, Bách Khoa...), tính toán cự ly và tuyến đường đi bộ/xe máy thực tế.</p>
                    </div>
                    <div class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <div class="text-teal-400 font-bold mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-check"></i> Xác minh KYC 100% Chủ trọ
                        </div>
                        <p class="text-[11px] text-slate-400">Chỉ những chủ trọ được cấp thẩm định CCCD và quyền quản lý phòng thực tế mới được gắn huy hiệu Tích Xanh và mở thanh toán tự động.</p>
                    </div>
                    <div class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <div class="text-teal-400 font-bold mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-comments"></i> Đánh giá Review 3 Tiêu Chí
                        </div>
                        <p class="text-[11px] text-slate-400">Hệ thống chấm điểm thực tế từ cộng đồng sinh viên đã từng ở: An ninh trật tự, Chủ nhà hỗ trợ và Giá cả điện nước minh bạch.</p>
                    </div>
                    <div class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <div class="text-teal-400 font-bold mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-qrcode"></i> Thanh toán VietQR & Hợp đồng OTP
                        </div>
                        <p class="text-[11px] text-slate-400">Ký hợp đồng thuê trọ điện tử xác thực OTP và thanh toán tiền phòng chuẩn xác qua VietQR tự động, không qua trung gian môi giới.</p>
                    </div>
                </div>
            </div>

            <!-- TAB CONTENT: QUY CHẾ HOẠT ĐỘNG -->
            <div id="policy-content-terms" class="policy-tab-pane hidden space-y-4">
                <div class="policy-card p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <h5 class="font-bold text-white mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center text-[10px]">1</span>
                        Nguyên Tắc Đăng Tin &amp; Hình Ảnh Phòng
                    </h5>
                    <ul class="list-disc list-inside space-y-1 text-slate-400 text-[11px]">
                        <li>Hình ảnh phòng phải là <strong>ảnh thực tế 100%</strong>, tối thiểu 3 ảnh chi tiết (không gian phòng, nhà vệ sinh, tiện ích kèm theo).</li>
                        <li>Nghiêm cấm hành vi sử dụng ảnh phòng mẫu của dự án khác hoặc ảnh mạng nhằm đánh lừa người thuê.</li>
                        <li>Giá thuê niêm yết là giá thực tế, chủ trọ không được tự ý tăng giá khi người thuê đến xem trực tiếp.</li>
                    </ul>
                </div>

                <div class="policy-card p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <h5 class="font-bold text-white mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center text-[10px]">2</span>
                        Bảo Vệ Tiền Đặt Cọc Sinh Viên
                    </h5>
                    <p class="text-slate-400 text-[11px] mb-2">
                        Hệ thống áp dụng chính sách <strong>bảo vệ cọc trong 24 giờ đầu</strong> sau khi nhận bàn giao phòng: Nếu phòng thực tế sai khác nghiêm trọng so với thông tin mô tả, người thuê được hoàn 100% tiền đặt cọc giữ chỗ.
                    </p>
                </div>

                <div class="policy-card p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <h5 class="font-bold text-white mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center text-[10px]">3</span>
                        Miễn Phí 100% Cho Sinh Viên
                    </h5>
                    <p class="text-slate-400 text-[11px]">
                        Renty <strong>cam kết không thu bất kỳ khoản phí môi giới nào</strong> từ sinh viên tìm phòng. Mọi trải nghiệm tìm kiếm, liên hệ chủ trọ và đặt lịch xem phòng đều hoàn toàn miễn phí.
                    </p>
                </div>
            </div>

            <!-- TAB CONTENT: CHÍNH SÁCH BẢO MẬT -->
            <div id="policy-content-privacy" class="policy-tab-pane hidden space-y-4">
                <div class="policy-card p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <h5 class="font-bold text-white mb-1.5 flex items-center gap-2">
                        <i class="fa-solid fa-lock text-teal-400"></i> Tuân Thủ Nghị Định 13/2023/NĐ-CP
                    </h5>
                    <p class="text-slate-400 text-[11px]">
                        Renty tuân thủ nghiêm ngặt quy định pháp luật Việt Nam về Bảo vệ dữ liệu cá nhân. Toàn bộ thông tin CCCD, hợp đồng số và số điện thoại của người dùng được mã hóa đầu cuối chuẩn quân sự AES-256.
                    </p>
                </div>

                <div class="space-y-2.5">
                    <div class="policy-card flex items-start gap-2.5 p-3 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <i class="fa-solid fa-circle-check text-emerald-400 mt-1 shrink-0"></i>
                        <div>
                            <strong class="text-slate-200 text-xs">Mục đích thu thập:</strong>
                            <p class="text-slate-400 text-[11px]">Chỉ sử dụng để xác minh danh tính người thuê/chủ trọ, lập hợp đồng điện tử và hỗ trợ xử lý sự cố trong quá trình thuê phòng.</p>
                        </div>
                    </div>
                    <div class="policy-card flex items-start gap-2.5 p-3 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <i class="fa-solid fa-circle-check text-emerald-400 mt-1 shrink-0"></i>
                        <div>
                            <strong class="text-slate-200 text-xs">Không chia sẻ bên thứ ba:</strong>
                            <p class="text-slate-400 text-[11px]">Tuyệt đối không bán hoặc cung cấp số điện thoại sinh viên cho các bên môi giới, tiếp thị quảng cáo ngoài hệ thống.</p>
                        </div>
                    </div>
                    <div class="policy-card flex items-start gap-2.5 p-3 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <i class="fa-solid fa-circle-check text-emerald-400 mt-1 shrink-0"></i>
                        <div>
                            <strong class="text-slate-200 text-xs">Quyền của người dùng:</strong>
                            <p class="text-slate-400 text-[11px]">Bạn có quyền yêu cầu tra cứu, chỉnh sửa hoặc xóa vĩnh viễn dữ liệu tài khoản bất kỳ lúc nào bằng cách liên hệ bộ phận hỗ trợ.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB CONTENT: LIÊN HỆ HỖ TRỢ -->
            <div id="policy-content-contact" class="policy-tab-pane hidden space-y-4">
                <div class="p-4 rounded-2xl bg-gradient-to-r from-teal-500/15 via-blue-500/10 to-transparent border border-teal-500/25 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-teal-300">Tổng đài hỗ trợ sinh viên 24/7</span>
                        <h4 class="text-lg font-black text-white mt-0.5">0396 519 196</h4>
                        <p class="text-[11px] text-slate-400">Tiếp nhận báo cáo khẩn cấp, khiếu nại cọc và hướng dẫn tìm phòng</p>
                    </div>
                    <a href="tel:0396519196" class="px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs transition-all shadow-lg shadow-teal-500/25 inline-flex items-center justify-center gap-2 shrink-0">
                        <i class="fa-solid fa-phone"></i> Gọi Ngay Bây Giờ
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-start gap-3">
                        <span class="w-8 h-8 rounded-xl bg-blue-500/15 text-blue-400 flex items-center justify-center shrink-0 text-sm">
                            <i class="fa-solid fa-location-dot"></i>
                        </span>
                        <div>
                            <strong class="text-white text-xs block mb-0.5">Trụ sở vận hành TP.HCM:</strong>
                            <p class="text-slate-400 text-[11px]">Số 53 Võ Văn Ngân, Phường Linh Chiểu, TP. Thủ Đức, TP. Hồ Chí Minh</p>
                        </div>
                    </div>

                    <div class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-start gap-3">
                        <span class="w-8 h-8 rounded-xl bg-indigo-500/15 text-indigo-400 flex items-center justify-center shrink-0 text-sm">
                            <i class="fa-solid fa-building"></i>
                        </span>
                        <div>
                            <strong class="text-white text-xs block mb-0.5">Văn phòng Hà Nội:</strong>
                            <p class="text-slate-400 text-[11px]">Số 35 Nguyễn Khuyến, Phường Văn Quán, Quận Hà Đông, TP. Hà Nội</p>
                        </div>
                    </div>

                    <div class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-start gap-3">
                        <span class="w-8 h-8 rounded-xl bg-purple-500/15 text-purple-400 flex items-center justify-center shrink-0 text-sm">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <div>
                            <strong class="text-white text-xs block mb-0.5">Email Hỗ Trợ:</strong>
                            <p class="text-slate-400 text-[11px]">support@renty.vn · adminsmartroom@gmail.com</p>
                        </div>
                    </div>

                    <div class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-start gap-3">
                        <span class="w-8 h-8 rounded-xl bg-amber-500/15 text-amber-400 flex items-center justify-center shrink-0 text-sm">
                            <i class="fa-solid fa-clock"></i>
                        </span>
                        <div>
                            <strong class="text-white text-xs block mb-0.5">Khung Giờ Hỗ Trợ:</strong>
                            <p class="text-slate-400 text-[11px]">Thứ 2 – Chủ Nhật: 07:30 – 22:30 (Trực hotline cả ngày lễ)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB CONTENT: GIẢI QUYẾT KHIẾU NẠI -->
            <div id="policy-content-dispute" class="policy-tab-pane hidden space-y-4">
                <div class="policy-dispute-alert p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300">
                    <h5 class="font-bold text-xs mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i> Quy Trình Bảo Vệ Quyền Lợi Sinh Viên
                    </h5>
                    <p class="text-[11px] text-rose-200/90 leading-relaxed">
                        Nếu bạn phát hiện phòng trọ ảo, giá tiền thực tế sai lệch hoặc chủ trọ có hành vi gian lận, hãy bấm nút <strong>"Báo cáo"</strong> trên thẻ phòng hoặc liên hệ ngay hotline.
                    </p>
                </div>

                <div class="space-y-3">
                    <div class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 flex gap-3">
                        <div class="w-6 h-6 rounded-full bg-teal-500/20 text-teal-400 font-extrabold flex items-center justify-center text-xs shrink-0">1</div>
                        <div>
                            <strong class="text-white text-xs">Tiếp nhận thông tin (Dưới 30 phút):</strong>
                            <p class="text-slate-400 text-[11px] mt-0.5">Ban quản trị ghi nhận báo cáo và liên hệ người thuê để lấy bằng chứng (ảnh chụp, tin nhắn trao đổi, biên lai chuyển tiền).</p>
                        </div>
                    </div>

                    <div class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 flex gap-3">
                        <div class="w-6 h-6 rounded-full bg-teal-500/20 text-teal-400 font-extrabold flex items-center justify-center text-xs shrink-0">2</div>
                        <div>
                            <strong class="text-white text-xs">Tạm khóa tin phòng (Trong vòng 2 giờ):</strong>
                            <p class="text-slate-400 text-[11px] mt-0.5">Phòng trọ bị phản ánh sẽ lập tức bị ẩn khỏi kết quả tìm kiếm để tránh sinh viên khác gặp rủi ro.</p>
                        </div>
                    </div>

                    <div class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 flex gap-3">
                        <div class="w-6 h-6 rounded-full bg-teal-500/20 text-teal-400 font-extrabold flex items-center justify-center text-xs shrink-0">3</div>
                        <div>
                            <strong class="text-white text-xs">Phán quyết &amp; Xử lý:</strong>
                            <p class="text-slate-400 text-[11px] mt-0.5">Nếu chủ trọ vi phạm cam kết, Renty hỗ trợ thu hồi 100% tiền cọc và hủy vĩnh viễn quyền đăng tin của cơ sở đó.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB CONTENT: CÂU HỎI THƯỜNG GẶP (FAQ) -->
            <div id="policy-content-faq" class="policy-tab-pane hidden space-y-3">
                <details class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 group" open>
                    <summary class="font-bold text-white text-xs cursor-pointer flex items-center justify-between">
                        <span>1. Tôi xem phòng trên Renty có mất phí môi giới không?</span>
                        <i class="fa-solid fa-chevron-down text-slate-500 text-[10px] group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-slate-400 text-[11px] mt-2.5 leading-relaxed pt-2 border-t border-slate-800/60">
                        Hoàn toàn <strong>KHÔNG</strong>. Sinh viên được xem thông tin, bản đồ cự ly, số điện thoại chủ trọ và đặt lịch xem phòng trực tiếp 100% miễn phí.
                    </p>
                </details>

                <details class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 group">
                    <summary class="font-bold text-white text-xs cursor-pointer flex items-center justify-between">
                        <span>2. Huy hiệu "Đã xác minh" (Tích xanh) có ý nghĩa gì?</span>
                        <i class="fa-solid fa-chevron-down text-slate-500 text-[10px] group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-slate-400 text-[11px] mt-2.5 leading-relaxed pt-2 border-t border-slate-800/60">
                        Huy hiệu Tích xanh chứng nhận chủ trọ đã hoàn thành định danh KYC (CCCD, tài khoản ngân hàng chính chủ và xác nhận quyền sở hữu cơ sở trọ). Thuê các phòng này đảm bảo an toàn tuyệt đối.
                    </p>
                </details>

                <details class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 group">
                    <summary class="font-bold text-white text-xs cursor-pointer flex items-center justify-between">
                        <span>3. Làm sao để tính thời gian và đường đi từ phòng trọ đến trường học?</span>
                        <i class="fa-solid fa-chevron-down text-slate-500 text-[10px] group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-slate-400 text-[11px] mt-2.5 leading-relaxed pt-2 border-t border-slate-800/60">
                        Trên thanh tìm kiếm của bản đồ, bạn chỉ cần bấm chọn trường học (ví dụ: CĐ Công nghệ Thủ Đức). Hệ thống sẽ tự động vẽ lộ trình và tính số phút đi xe máy, đi bộ từ từng phòng đến cổng trường.
                    </p>
                </details>

                <details class="policy-card p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 group">
                    <summary class="font-bold text-white text-xs cursor-pointer flex items-center justify-between">
                        <span>4. Tôi có thể thanh toán tiền phòng bằng những hình thức nào?</span>
                        <i class="fa-solid fa-chevron-down text-slate-500 text-[10px] group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-slate-400 text-[11px] mt-2.5 leading-relaxed pt-2 border-t border-slate-800/60">
                        Hệ thống hỗ trợ tạo mã quét VietQR tự động khớp lệnh chỉ sau 2 giây, cùng các cổng ví điện tử MoMo, VNPay và tiền mặt trực tiếp cho chủ trọ.
                    </p>
                </details>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="policy-modal-footer px-6 py-3.5 border-t border-slate-800/80 bg-slate-900/60 flex items-center justify-between shrink-0">
            <span class="policy-footer-hotline text-[11px] text-slate-400 hidden sm:inline">
                Cần trợ giúp khẩn? Gọi ngay: <strong class="text-teal-400 font-bold">0396 519 196</strong>
            </span>
            <div class="flex items-center gap-2.5 ml-auto">
                <a href="https://zalo.me/0396519196" target="_blank" rel="noopener" class="policy-btn-zalo px-3.5 py-1.5 rounded-xl bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/30 text-xs font-bold transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-message text-[11px]"></i> Nhắn Zalo
                </a>
                <button type="button" onclick="closePolicyModal()" class="policy-btn-confirm px-4 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-extrabold transition-all cursor-pointer">
                    Đã Hiểu &amp; Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* =========================================================================
       POLICY POPUP MODAL STYLES (ĐỘ TƯƠNG PHẢN TUYỆT ĐỐI CHO DARK & LIGHT MODE)
       ========================================================================= */
    /* 1. MẶC ĐỊNH (DARK MODE) */
    #renty-policy-modal .policy-mission-box {
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.18) 0%, rgba(6, 182, 212, 0.12) 100%) !important;
        border: 1px solid rgba(45, 212, 191, 0.35) !important;
    }
    #renty-policy-modal .policy-mission-box h4 {
        color: #5eead4 !important;
    }
    #renty-policy-modal .policy-mission-box p,
    #renty-policy-modal .policy-mission-box strong {
        color: #f1f5f9 !important; /* Luôn sáng rõ, không bao giờ bị màu đen đè */
    }
    #renty-policy-modal .policy-btn-confirm {
        background-color: #1e293b !important;
        color: #ffffff !important;
        border: 1px solid #334155 !important;
    }
    #renty-policy-modal .policy-btn-confirm:hover {
        background-color: #334155 !important;
        color: #ffffff !important;
    }

    /* 2. CHẾ ĐỘ SÁNG (LIGHT MODE) */
    html.theme-light #renty-policy-modal .policy-modal-dialog,
    body.theme-light #renty-policy-modal .policy-modal-dialog {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
    }
    html.theme-light #renty-policy-modal .policy-modal-header,
    body.theme-light #renty-policy-modal .policy-modal-header {
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }
    html.theme-light #policy-modal-header-title,
    body.theme-light #policy-modal-header-title {
        color: #0f172a !important;
    }
    html.theme-light #renty-policy-modal .policy-modal-subtitle,
    body.theme-light #renty-policy-modal .policy-modal-subtitle {
        color: #64748b !important;
    }
    html.theme-light #renty-policy-modal .policy-modal-close-btn,
    body.theme-light #renty-policy-modal .policy-modal-close-btn {
        background-color: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #475569 !important;
    }
    html.theme-light #renty-policy-modal .policy-modal-close-btn:hover,
    body.theme-light #renty-policy-modal .policy-modal-close-btn:hover {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
    }
    html.theme-light #renty-policy-modal .policy-tab-bar,
    body.theme-light #renty-policy-modal .policy-tab-bar {
        background-color: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
    }
    html.theme-light #renty-policy-modal .policy-tab-btn,
    body.theme-light #renty-policy-modal .policy-tab-btn {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #475569 !important;
    }
    html.theme-light #renty-policy-modal .policy-tab-btn:hover,
    body.theme-light #renty-policy-modal .policy-tab-btn:hover {
        color: #0f172a !important;
        border-color: #94a3b8 !important;
    }
    html.theme-light #renty-policy-modal .policy-tab-btn.active,
    body.theme-light #renty-policy-modal .policy-tab-btn.active {
        background-color: #0d9488 !important;
        border-color: #0d9488 !important;
        color: #ffffff !important;
    }

    /* Khung sứ mệnh bảo vệ sinh viên (Light Mode) */
    html.theme-light #renty-policy-modal .policy-mission-box,
    body.theme-light #renty-policy-modal .policy-mission-box {
        background: linear-gradient(135deg, #f0fdfa 0%, #ecfeff 100%) !important;
        border-color: #99f6e4 !important;
    }
    html.theme-light #renty-policy-modal .policy-mission-box h4,
    body.theme-light #renty-policy-modal .policy-mission-box h4 {
        color: #0f766e !important;
    }
    html.theme-light #renty-policy-modal .policy-mission-box p,
    html.theme-light #renty-policy-modal .policy-mission-box strong,
    body.theme-light #renty-policy-modal .policy-mission-box p,
    body.theme-light #renty-policy-modal .policy-mission-box strong {
        color: #134e4a !important; /* Màu xanh đậm dễ đọc, độ tương phản tuyệt đối */
        font-weight: 500 !important;
    }

    /* Các card thông tin & danh sách (Light Mode) */
    html.theme-light #renty-policy-modal .policy-card,
    body.theme-light #renty-policy-modal .policy-card {
        background-color: #ffffff !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
    }
    html.theme-light #renty-policy-modal .policy-card .text-teal-400,
    body.theme-light #renty-policy-modal .policy-card .text-teal-400 {
        color: #0d9488 !important;
    }
    html.theme-light #renty-policy-modal .policy-card h5,
    html.theme-light #renty-policy-modal .policy-card strong,
    body.theme-light #renty-policy-modal .policy-card h5,
    body.theme-light #renty-policy-modal .policy-card strong {
        color: #0f172a !important;
    }
    html.theme-light #renty-policy-modal .policy-card p,
    html.theme-light #renty-policy-modal .policy-card li,
    body.theme-light #renty-policy-modal .policy-card p,
    body.theme-light #renty-policy-modal .policy-card li {
        color: #475569 !important;
    }

    /* Khung khiếu nại & cảnh báo đỏ (Light Mode) */
    html.theme-light #renty-policy-modal .policy-dispute-alert,
    body.theme-light #renty-policy-modal .policy-dispute-alert {
        background-color: #fff1f2 !important;
        border-color: #fecdd3 !important;
        color: #be123c !important;
    }
    html.theme-light #renty-policy-modal .policy-dispute-alert p,
    html.theme-light #renty-policy-modal .policy-dispute-alert strong,
    body.theme-light #renty-policy-modal .policy-dispute-alert p,
    body.theme-light #renty-policy-modal .policy-dispute-alert strong {
        color: #9f1239 !important;
    }

    /* Footer & Nút bấm (Light Mode) */
    html.theme-light #renty-policy-modal .policy-modal-footer,
    body.theme-light #renty-policy-modal .policy-modal-footer {
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }
    html.theme-light #renty-policy-modal .policy-footer-hotline,
    body.theme-light #renty-policy-modal .policy-footer-hotline {
        color: #64748b !important;
    }
    html.theme-light #renty-policy-modal .policy-btn-zalo,
    body.theme-light #renty-policy-modal .policy-btn-zalo {
        background-color: #eff6ff !important;
        border-color: #bfdbfe !important;
        color: #1d4ed8 !important;
    }
    html.theme-light #renty-policy-modal .policy-btn-confirm,
    body.theme-light #renty-policy-modal .policy-btn-confirm {
        background-color: #0f172a !important;
        color: #ffffff !important;
        border: 1px solid #0f172a !important;
        font-weight: 700 !important;
    }
    html.theme-light #renty-policy-modal .policy-btn-confirm:hover,
    body.theme-light #renty-policy-modal .policy-btn-confirm:hover {
        background-color: #1e293b !important;
        color: #ffffff !important;
    }
</style>

<script>
    // Dữ liệu tiêu đề & icon cho từng tab trong Policy Modal
    const policyTabConfig = {
        about: { title: 'Về Renty Student Housing', icon: 'fa-solid fa-circle-info' },
        terms: { title: 'Quy Chế Hoạt Động Nền Tảng', icon: 'fa-solid fa-file-contract' },
        privacy: { title: 'Chính Sách Bảo Mật Dữ Liệu', icon: 'fa-solid fa-shield-halved' },
        contact: { title: 'Liên Hệ & Hỗ Trợ Sinh Viên 24/7', icon: 'fa-solid fa-headset' },
        dispute: { title: 'Quy Trình Giải Quyết Khiếu Nại', icon: 'fa-solid fa-scale-balanced' },
        faq: { title: 'Câu Hỏi Thường Gặp (FAQ)', icon: 'fa-solid fa-circle-question' }
    };

    window.openPolicyModal = function(tabName = 'about') {
        const modal = document.getElementById('renty-policy-modal');
        if (!modal) return;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        switchPolicyTab(tabName);
    };

    window.switchPolicyTab = function(tabName) {
        if (!policyTabConfig[tabName]) tabName = 'about';

        // Cập nhật tiêu đề & icon header
        const titleEl = document.getElementById('policy-modal-header-title');
        const iconEl = document.getElementById('policy-modal-header-icon');
        if (titleEl) titleEl.textContent = policyTabConfig[tabName].title;
        if (iconEl) iconEl.className = policyTabConfig[tabName].icon;

        // Cập nhật active button
        document.querySelectorAll('.policy-tab-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-teal-500/15', 'text-teal-300', 'border-teal-500/30');
            btn.classList.add('bg-slate-900/50', 'text-slate-400', 'border-slate-800');
        });
        const activeBtn = document.getElementById(`tab-btn-${tabName}`);
        if (activeBtn) {
            activeBtn.classList.add('active', 'bg-teal-500/15', 'text-teal-300', 'border-teal-500/30');
            activeBtn.classList.remove('bg-slate-900/50', 'text-slate-400', 'border-slate-800');
        }

        // Cập nhật nội dung tab
        document.querySelectorAll('.policy-tab-pane').forEach(pane => {
            pane.classList.add('hidden');
        });
        const activePane = document.getElementById(`policy-content-${tabName}`);
        if (activePane) {
            activePane.classList.remove('hidden');
        }
    };

    window.closePolicyModal = function() {
        const modal = document.getElementById('renty-policy-modal');
        if (!modal) return;

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    };

    // Đóng khi click ngoài backdrop
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('renty-policy-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closePolicyModal();
                }
            });
        }

        // Đóng khi nhấn phím Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePolicyModal();
            }
        });
    });

    // Lọc theo khu vực nổi bật từ footer lên bản đồ Renty
    window.filterByDistrict = function(districtName) {
        const isRentyPage = window.location.pathname.includes('/renty');
        if (!isRentyPage) {
            window.location.href = `/renty?search=${encodeURIComponent(districtName)}&view=map`;
            return;
        }

        // 1. Chuyển sang chế độ xem bản đồ nếu chưa mở
        if (typeof window.setViewMode === 'function') {
            window.setViewMode('map');
        }

        // 2. Điền từ khóa vào ô tìm kiếm bản đồ
        const mapInput = document.getElementById('gm-search-input');
        if (mapInput) {
            mapInput.value = districtName;
        }

        // 3. Kích hoạt tìm kiếm địa danh và chuyển tâm bản đồ tới quận đó
        if (window.rentyGoogleMap && typeof window.rentyGoogleMap.searchGeocodeOrCampus === 'function') {
            window.rentyGoogleMap.searchGeocodeOrCampus(districtName);
        } else if (typeof window.applySmartSearch === 'function') {
            window.applySmartSearch(districtName);
        }

        // 4. Cuộn mượt mà lên vị trí bản đồ
        const targetEl = document.getElementById('renty-interactive-map') || document.getElementById('room_list_panel') || document.getElementById('rooms-grid');
        if (targetEl) {
            targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };
</script>
