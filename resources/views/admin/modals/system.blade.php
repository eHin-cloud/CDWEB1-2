    <!-- CELEBRATION MODAL FOR APPROVED VERIFICATION / TICH XANH -->
    @include('admin.partials.celebration_modal')


    <!-- MODAL CẢNH BÁO XUNG ĐỘT NGHIỆP VỤ (ERR_13_02) -->
    <div id="modal-conflict-warning" class="fixed inset-0 z-[100] bg-slate-950/80 backdrop-blur-md hidden items-center justify-center p-4">
        <div class="w-full max-w-md bg-[#0a0f1d] border border-amber-500/40 rounded-3xl p-6 sm:p-7 shadow-2xl relative my-auto animate-fade-in space-y-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 text-2xl mx-auto mb-1">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="text-center space-y-2">
                <h3 class="text-lg font-black text-white">Cảnh Báo Xung Đột Nghiệp Vụ</h3>
                <p id="modal-conflict-message" class="text-sm font-medium text-slate-300 leading-relaxed">
                    Phòng đang có hợp đồng thuê hoạt động, không thể chuyển sang bảo trì.
                </p>
            </div>
            <div class="pt-3 flex justify-center">
                <button type="button" onclick="closeConflictModal()" class="w-full py-3 px-5 rounded-xl text-sm font-bold text-white bg-amber-600 hover:bg-amber-500 transition-all shadow-lg shadow-amber-600/30">
                    Đã hiểu và Đóng
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL SỰ CỐ KHẨN CẤP AI (ERR_14_06) -->
    <div id="modal-ai-emergency-ticket" class="fixed inset-0 z-[100] bg-slate-950/80 backdrop-blur-md hidden items-center justify-center p-4">
        <div class="w-full max-w-md bg-[#13070a] border border-rose-500/50 rounded-3xl p-6 sm:p-7 shadow-2xl relative my-auto animate-fade-in space-y-4">
            <div class="w-14 h-14 rounded-2xl bg-rose-500/20 border border-rose-500/40 flex items-center justify-center text-rose-400 text-2xl mx-auto mb-1 animate-bounce">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="text-center space-y-2">
                <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30">
                    Ưu tiên xử lý khẩn cấp (Emergency)
                </span>
                <h3 class="text-lg font-black text-white">Cảnh Báo Sự Cố Kỹ Thuật Khẩn Cấp</h3>
                <p id="modal-ai-emergency-message" class="text-sm font-semibold text-rose-200 leading-relaxed">
                    Sự cố kỹ thuật khẩn cấp đã được AI ghi nhận và chuyển ngay đến đội ngũ bảo trì tòa nhà!
                </p>
                <p class="text-xs text-slate-400 mt-2">Đội ngũ kỹ thuật viên tòa nhà đang được điều động ưu tiên đến hiện trường. Vui lòng ngắt nguồn điện/khóa van nước an toàn.</p>
            </div>
            <div class="pt-3 flex justify-center gap-2">
                <button type="button" onclick="closeAiEmergencyModal()" class="w-full py-3 px-5 rounded-xl text-sm font-bold text-white bg-rose-600 hover:bg-rose-500 transition-all shadow-lg shadow-rose-600/30">
                    Đã hiểu & Xác nhận
                </button>
            </div>
        </div>
    </div>


    <!-- KYC TERMS MODAL -->
    <div id="kyc-terms-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeKycTermsModal()"></div>
        <!-- Modal Content -->
        <div class="relative w-full max-w-3xl max-h-[85vh] mx-4 bg-[#0f1422] border border-slate-700/60 rounded-3xl shadow-2xl shadow-black/50 flex flex-col animate-fade-in">
            <!-- Header -->
            <div class="flex items-center justify-between px-8 py-5 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/15 border border-indigo-500/25 flex items-center justify-center">
                        <i class="fa-solid fa-shield-halved text-indigo-400"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-100">Điều Khoản Bảo Mật & KYC</h3>
                        <p class="text-[10px] text-slate-500 mt-0.5">Vui lòng đọc kỹ trước khi đồng ý</p>
                    </div>
                </div>
                <button onclick="closeKycTermsModal()" class="w-9 h-9 rounded-xl border border-slate-800 hover:border-slate-600 bg-slate-900/50 hover:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <!-- Body -->
            <div class="overflow-y-auto flex-1 px-8 py-6 space-y-6 text-[12px] leading-relaxed text-slate-300 custom-scrollbar">
                <div class="rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-4">
                    <h4 class="text-base font-extrabold text-indigo-300 mb-2">ĐIỀU KHOẢN BẢO MẬT THÔNG TIN VÀ XÁC THỰC TÀI KHOẢN (KYC)</h4>
                    <p class="text-slate-400 text-[11px] leading-6">Bằng việc tích chọn đồng ý, Quý khách (sau đây gọi là <strong class="text-slate-200">"Chủ trọ"</strong>) chấp thuận các điều khoản nghiêm ngặt dưới đây từ Ban quản trị hệ thống SmartRoom (sau đây gọi là <strong class="text-slate-200">"SmartRoom"</strong>) liên quan đến việc thu thập, xử lý và bảo vệ dữ liệu cá nhân.</p>
                </div>

                <!-- Section I -->
                <div>
                    <h5 class="text-sm font-extrabold text-emerald-300 mb-3 flex items-center gap-2"><i class="fa-solid fa-scale-balanced text-emerald-400/70"></i> I. Căn Cứ Pháp Lý (Luật Việt Nam Bản Địa)</h5>
                    <p class="mb-3 text-slate-400">SmartRoom cam kết hoạt động tuân thủ nghiêm ngặt hệ thống pháp luật Việt Nam về an toàn thông tin và bảo vệ dữ liệu cá nhân, bao gồm nhưng không giới hạn ở:</p>
                    <ul class="space-y-2 text-slate-300">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-check-circle text-emerald-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Nghị định 13/2023/NĐ-CP</strong> về Bảo vệ dữ liệu cá nhân (Đặc biệt là quy định về Dữ liệu cá nhân cơ bản và Dữ liệu cá nhân nhạy cảm).</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-check-circle text-emerald-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Luật An toàn thông tin mạng 2015</strong> về trách nhiệm bảo vệ thông tin cá nhân trên mạng của tổ chức, cá nhân.</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-check-circle text-emerald-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Bộ luật Dân sự 2015 (Điều 38)</strong> về Quyền bí mật đời tư.</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-check-circle text-emerald-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Luật An ninh mạng 2018</strong> về phòng ngừa, xử lý hành vi xâm phạm an ninh mạng.</span></li>
                    </ul>
                </div>

                <!-- Section II -->
                <div>
                    <h5 class="text-sm font-extrabold text-sky-300 mb-3 flex items-center gap-2"><i class="fa-solid fa-bullseye text-sky-400/70"></i> II. Mục Đích Sử Dụng Dữ Liệu Duy Nhất</h5>
                    <ul class="space-y-2 text-slate-300">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-dot text-sky-400/60 mt-0.5 shrink-0 text-[9px]"></i><span><strong class="text-slate-200">Phạm vi thu thập:</strong> Hình ảnh Căn cước công dân (CCCD) hai mặt, số tài khoản ngân hàng, tên chủ tài khoản và ảnh xác minh tài khoản.</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-dot text-sky-400/60 mt-0.5 shrink-0 text-[9px]"></i><span><strong class="text-slate-200">Mục đích:</strong> Chỉ sử dụng duy nhất cho việc xác thực danh tính chủ sở hữu (KYC) để kích hoạt tính năng nhận tiền tự động/rút tiền trên hệ thống và phòng chống các hành vi gian lận tài chính.</span></li>
                        <li class="flex items-start gap-2 rounded-xl border border-rose-500/20 bg-rose-500/5 p-3"><i class="fa-solid fa-ban text-rose-400 mt-0.5 shrink-0"></i><span><strong class="text-rose-300">Tuyệt đối nghiêm cấm:</strong> SmartRoom không được phép sử dụng dữ liệu này cho mục đích quảng cáo, chia sẻ, bán hoặc chuyển giao cho bất kỳ bên thứ ba nào khác khi chưa có sự đồng ý bằng văn bản của Chủ trọ.</span></li>
                    </ul>
                </div>

                <!-- Section III -->
                <div>
                    <h5 class="text-sm font-extrabold text-amber-300 mb-3 flex items-center gap-2"><i class="fa-solid fa-lock text-amber-400/70"></i> III. Quy Trình Giới Hạn Quyền Truy Cập Và Xóa Dữ Liệu</h5>
                    <ul class="space-y-2 text-slate-300">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-hourglass-half text-amber-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Giới hạn quyền xem tạm thời:</strong> Quyền xem hình ảnh CCCD và tài liệu ngân hàng gốc chỉ được cấp cho nhân sự kiểm duyệt được chỉ định của SmartRoom trong thời gian hồ sơ đang chờ duyệt (tối đa không quá <strong class="text-amber-200">24 giờ làm việc</strong> kể từ khi gửi).</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-key text-amber-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Mã hóa và Thu hồi quyền:</strong> Ngay sau khi hồ sơ được "Duyệt" hoặc "Từ chối", hệ thống tự động thu hồi hoàn toàn quyền tiếp cận của nhân sự kiểm duyệt.</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-trash-can text-amber-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Cam kết xóa dữ liệu (Hard Delete):</strong> Toàn bộ tệp ảnh gốc (CCCD, Xác nhận tài khoản) sẽ được xóa vĩnh viễn khỏi bộ nhớ đệm của nhân sự trong vòng <strong class="text-amber-200">24 giờ</strong>. Hệ thống chỉ lưu trữ chuỗi hash đã được mã hóa theo tiêu chuẩn quân đội <strong class="text-amber-200">AES-256</strong> trên máy chủ để phục vụ đối chiếu lịch sử khi có yêu cầu từ cơ quan chức năng.</span></li>
                    </ul>
                </div>

                <!-- Section IV -->
                <div>
                    <h5 class="text-sm font-extrabold text-rose-300 mb-3 flex items-center gap-2"><i class="fa-solid fa-gavel text-rose-400/70"></i> IV. Cam Kết Trách Nhiệm Chi Tiết Và Chế Tài Xử Phạt Khi Lộ Thông Tin</h5>
                    <p class="mb-3 text-slate-400"><strong class="text-slate-200">Tuyên bố chịu trách nhiệm:</strong> SmartRoom khẳng định việc bảo vệ dữ liệu của Chủ trọ là nghĩa vụ pháp lý tối cao. Nếu xảy ra bất kỳ sự cố rò rỉ, phát tán hoặc làm lộ thông tin cá nhân, thông tin tài khoản của Chủ trọ xuất phát từ lỗi hệ thống, lỗ hổng bảo mật hoặc do hành vi cố ý/vô ý của nhân viên SmartRoom, chúng tôi cam kết chịu trách nhiệm như sau:</p>
                    <ul class="space-y-3 text-slate-300">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-shield-halved text-rose-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Trách nhiệm hình sự và hành chính:</strong> SmartRoom hoàn toàn chịu trách nhiệm trước pháp luật Việt Nam, phối hợp với Cục An ninh mạng và phòng, chống tội phạm sử dụng công nghệ cao <strong class="text-rose-200">(A05)</strong> để điều tra và xử lý cá nhân vi phạm theo quy định của pháp luật.</span></li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-coins text-rose-400/60 mt-0.5 shrink-0"></i>
                            <div>
                                <p><strong class="text-slate-200">Trách nhiệm dân sự và bồi thường thiệt hại:</strong></p>
                                <ul class="mt-2 space-y-2 pl-4">
                                    <li class="flex items-start gap-2"><span class="text-rose-400 mt-0.5 shrink-0">•</span><span>SmartRoom cam kết bồi thường toàn bộ thiệt hại về vật chất và tinh thần thực tế phát sinh cho Chủ trọ nếu thông tin bị lộ dẫn đến việc Chủ trọ bị lợi dụng danh tính (Ví dụ: bị kẻ xấu dùng CCCD vay tín dụng đen, lừa đảo chiếm đoạt tài sản...).</span></li>
                                    <li class="flex items-start gap-2"><span class="text-rose-400 mt-0.5 shrink-0">•</span><span>Mức bồi thường tối thiểu cho một sự cố lộ thông tin được ấn định sẵn là <strong class="text-rose-200 bg-rose-500/10 px-2 py-0.5 rounded-lg">50.000.000 VNĐ</strong> cho một tài khoản bị ảnh hưởng, chưa bao gồm các chi phí khắc phục thiệt hại phát sinh thực tế được chứng minh bằng hóa đơn, chứng từ hoặc phán quyết của Tòa án.</span></li>
                                </ul>
                            </div>
                        </li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-bell text-rose-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Khắc phục sự cố:</strong> Trong vòng <strong class="text-rose-200">02 giờ</strong> kể từ khi phát hiện sự cố, SmartRoom có trách nhiệm thông báo bằng văn bản/email cho Chủ trọ, đồng thời áp dụng mọi biện pháp kỹ thuật để ngăn chặn và giảm thiểu tối đa thiệt hại.</span></li>
                    </ul>
                </div>

                <!-- Section V -->
                <div>
                    <h5 class="text-sm font-extrabold text-violet-300 mb-3 flex items-center gap-2"><i class="fa-solid fa-handshake text-violet-400/70"></i> V. Phương Thức Giải Quyết Tranh Chấp</h5>
                    <div class="rounded-xl border border-violet-500/20 bg-violet-500/5 p-4 text-slate-300">
                        <p>Mọi tranh chấp phát sinh từ hoặc liên quan đến việc bảo mật thông tin này sẽ được ưu tiên giải quyết bằng <strong class="text-violet-200">thương lượng</strong>. Trong trường hợp không đạt được thỏa thuận, vụ việc sẽ được đưa ra giải quyết tại <strong class="text-violet-200">Tòa án nhân dân có thẩm quyền tại Việt Nam</strong> theo quy định của pháp luật.</p>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <div class="px-8 py-4 border-t border-slate-800 flex items-center justify-between">
                <p class="text-[10px] text-slate-500"><i class="fa-solid fa-info-circle mr-1"></i> Cập nhật lần cuối: Tháng 06/2026</p>
                <button onclick="closeKycTermsModal()" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-lg shadow-indigo-600/25">
                    <i class="fa-solid fa-check mr-1.5"></i> Đã đọc và hiểu
                </button>
            </div>
        </div>
    </div>

