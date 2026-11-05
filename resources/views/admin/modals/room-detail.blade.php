    <!-- ROOM DETAIL MODAL (CENTERED & COMPACT BALANCED 2-COL) -->
    <div id="room-detail-modal" onclick="closeRoomDetail()" class="fixed inset-0 z-[100] bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-5 transition-all duration-200">
        <div id="room-detail-modal-box" onclick="event.stopPropagation()" class="w-full max-w-2xl lg:max-w-3xl bg-[#0a0f1d] border border-slate-800/80 rounded-3xl p-5 sm:p-6 shadow-2xl relative my-auto max-h-[92vh] overflow-y-auto animate-fade-in border-t border-t-indigo-500/30">
            <!-- Close button -->
            <button type="button" onclick="closeRoomDetail()" class="absolute top-4 right-4 sm:top-5 sm:right-5 w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all shadow-sm z-10" title="Đóng">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
            
            <!-- Room Head -->
            <div class="flex items-center gap-3 pr-10 mb-4 pb-3 border-b border-slate-800/60">
                <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shrink-0 text-lg">
                    <i class="fa-solid fa-door-open"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2.5 py-0.5 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 font-bold uppercase" id="modal-room-status-badge">Đã thuê</span>
                        <span class="text-xs text-slate-400 font-semibold" id="modal-room-price-display"></span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-100 tracking-tight" id="modal-room-title">Phòng 101</h2>
                </div>
            </div>

            <!-- Content Grid: Dynamic 1 or 2 columns based on occupancy -->
            <div id="modal-content-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- CỘT 1: VỊ TRÍ, HOUSEKEEPING & CƯ DÂN -->
                <div class="space-y-3 flex flex-col justify-between">
                    <div class="space-y-3">
                        <!-- Vị trí cơ sở lưu trú cụ thể của phòng -->
                        <div class="p-3 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-1">
                            <div class="flex items-center justify-between text-[11px] font-bold text-slate-400">
                                <span class="flex items-center gap-1.5 uppercase tracking-wider text-indigo-400">
                                    <i class="fa-solid fa-building"></i> Cơ sở &amp; Vị trí
                                </span>
                                <a id="modal-room-maps-link" href="#" target="_blank" class="text-blue-400 hover:text-blue-300 flex items-center gap-1 text-[11px] font-bold transition-colors">
                                    <i class="fa-brands fa-google text-[10px]"></i> Bản đồ <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                </a>
                            </div>
                            <div class="text-xs font-bold text-slate-200 truncate" id="modal-room-building-name">-</div>
                            <div class="text-xs text-slate-400 flex items-start gap-1.5">
                                <i class="fa-solid fa-location-dot text-rose-400 text-xs shrink-0 mt-0.5"></i>
                                <span id="modal-room-building-address" class="line-clamp-2">-</span>
                            </div>
                        </div>

                        <!-- Resident details (chỉ hiện khi phòng có người ở) -->
                        <div class="space-y-1.5 hidden" id="modal-resident-details">
                            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-user text-indigo-400"></i> Thông tin cư dân
                            </h3>
                            <div class="p-3 rounded-2xl bg-slate-900/50 border border-slate-800/60 space-y-1.5 text-xs">
                                <div class="flex justify-between"><span class="text-slate-400">Họ tên:</span> <strong class="text-slate-200 font-bold" id="modal-resident-name">-</strong></div>
                                <div class="flex justify-between"><span class="text-slate-400">Số điện thoại:</span> <strong class="text-slate-200 font-bold" id="modal-resident-phone">-</strong></div>
                                <div class="flex justify-between"><span class="text-slate-400">Bắt đầu ở:</span> <strong class="text-slate-300">01/03/2025</strong></div>
                            </div>
                        </div>

                        <!-- Housekeeping Quick Action Selector -->
                        <div class="p-3 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-2">
                            <div class="flex items-center justify-between text-[11px] font-bold text-slate-400">
                                <span class="flex items-center gap-1.5 uppercase tracking-wider text-slate-300">
                                    <i class="fa-solid fa-broom text-amber-400"></i> Housekeeping / Dọn buồng
                                </span>
                                <span id="quick-status-loading" class="text-indigo-400 hidden">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i> Đang lưu...
                                </span>
                            </div>
                            <div class="grid grid-cols-3 gap-1.5 text-xs">
                                <button type="button" onclick="setQuickRoomStatus('empty')" id="btn-quick-empty" class="py-1.5 px-2 rounded-xl font-bold border transition-all text-center flex flex-col items-center gap-0.5 hover:border-emerald-500 hover:bg-emerald-500/10 text-emerald-400 border-slate-800 bg-slate-900/50">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></span>
                                    <span class="text-[10px]">Đã dọn</span>
                                </button>
                                <button type="button" onclick="setQuickRoomStatus('cleaning')" id="btn-quick-cleaning" class="py-1.5 px-2 rounded-xl font-bold border transition-all text-center flex flex-col items-center gap-0.5 hover:border-orange-500 hover:bg-orange-500/10 text-orange-400 border-slate-800 bg-slate-900/50">
                                    <span class="w-2 h-2 rounded-full bg-orange-500 shadow-sm shadow-orange-500/50"></span>
                                    <span class="text-[10px]">Cần dọn</span>
                                </button>
                                <button type="button" onclick="setQuickRoomStatus('maintenance')" id="btn-quick-maintenance" class="py-1.5 px-2 rounded-xl font-bold border transition-all text-center flex flex-col items-center gap-0.5 hover:border-slate-500 hover:bg-slate-500/10 text-slate-400 border-slate-800 bg-slate-900/50">
                                    <span class="w-2 h-2 rounded-full bg-slate-500 shadow-sm shadow-slate-500/50"></span>
                                    <span class="text-[10px]">Bảo trì</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Nút Đóng khi ở chế độ 1 cột (phòng trống) -->
                    <div id="modal-col1-close-btn" class="hidden pt-1">
                        <button type="button" onclick="closeRoomDetail()" class="w-full py-2 px-4 rounded-xl text-xs font-semibold text-slate-400 bg-slate-900/60 hover:bg-slate-900 border border-slate-800/80 hover:border-slate-700 transition-all">
                            Đóng lại
                        </button>
                    </div>
                </div>

                <!-- CỘT 2: HÓA ĐƠN & THAO TÁC NGHIỆP VỤ -->
                <div id="modal-col-billing-actions" class="space-y-3 flex flex-col justify-between">
                    <!-- Billing summary (chỉ hiện khi phòng có hóa đơn) -->
                    <div class="space-y-1.5 hidden" id="modal-billing-details">
                        <div class="flex justify-between items-center">
                            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-file-invoice-dollar text-amber-400"></i> Hóa đơn gần nhất
                            </h3>
                            <span class="text-[10px] text-amber-400 font-bold px-2 py-0.5 rounded bg-amber-500/10 border border-amber-500/20" id="modal-bill-status">Chưa thanh toán</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-900/50 border border-slate-800/60 space-y-1.5 text-xs">
                            <div class="flex justify-between"><span class="text-slate-400">Tiền thuê phòng:</span> <strong class="text-slate-200" id="modal-bill-rent">0đ</strong></div>
                            <div class="flex justify-between"><span class="text-slate-400">Tiền điện:</span> <strong class="text-slate-200" id="modal-bill-electric">0đ</strong></div>
                            <div class="flex justify-between"><span class="text-slate-400">Tiền nước:</span> <strong class="text-slate-200" id="modal-bill-water">0đ</strong></div>
                            <div class="flex justify-between"><span class="text-slate-400">Dịch vụ (Mạng, rác):</span> <strong class="text-slate-200">150.000đ</strong></div>
                            <hr class="border-slate-800/60 my-1">
                            <div class="flex justify-between text-xs items-baseline"><strong class="text-indigo-400">Tổng thanh toán:</strong> <strong class="text-indigo-400 font-black text-sm" id="modal-bill-total">0đ</strong></div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="space-y-2 pt-1 border-t border-slate-800/60">
                        <form id="modal-pay-form" action="" method="POST" class="hidden">
                            @csrf
                        </form>
                        <form id="modal-notify-form" action="" method="POST" class="hidden">
                            @csrf
                        </form>
                        <button id="modal-btn-pay" onclick="submitModalPay()" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 transition-all hidden">
                            <i class="fa-solid fa-circle-check"></i> Xác nhận đã đóng tiền
                        </button>
                        <button id="modal-btn-zalo-personal" type="button" onclick="openRoomZaloPersonalChat()" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-md shadow-blue-600/20 transition-all hidden" title="Mở khung chat Zalo giữa bạn và khách trọ">
                            <i class="fa-solid fa-comment-dots"></i> Nhắn Zalo Cá Nhân Cho Khách (zalo.me)
                        </button>
                        <button id="modal-btn-action" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all hidden">
                            <i class="fa-solid fa-bell-slash"></i> Gửi nhắc nợ qua Zalo/Mail
                        </button>
                        <div class="grid grid-cols-2 gap-2">
                            <button id="modal-btn-qr" class="w-full flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-xl text-[11px] font-bold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all hidden">
                                <i class="fa-solid fa-qrcode text-indigo-400"></i> Xem VietQR
                            </button>
                            <button id="modal-btn-print" onclick="printModalInvoice()" class="w-full flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-xl text-[11px] font-bold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all hidden">
                                <i class="fa-solid fa-print text-indigo-400"></i> In hóa đơn
                            </button>
                        </div>
                        <button type="button" onclick="closeRoomDetail()" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-semibold text-slate-400 bg-slate-900/40 hover:bg-slate-900 border border-slate-800/60 hover:border-slate-700 transition-all">
                            Đóng lại
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- VIETQR POPUP MODAL -->
    <div id="vietqr-modal" onclick="closeVietQRModal()" class="fixed inset-0 z-[110] bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div onclick="event.stopPropagation()" class="w-full max-w-md bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 shadow-2xl relative animate-fade-in border-t border-t-indigo-500/30">
            <button type="button" onclick="closeVietQRModal()" class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <div class="text-center mb-4">
                <span class="text-xs px-2.5 py-1 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 font-bold uppercase tracking-wider">Mã VietQR Hóa Đơn</span>
                <h3 class="text-lg font-bold text-slate-100 mt-2" id="qr-modal-room">Phòng 101</h3>
            </div>

            <!-- Loading Spinner -->
            <div id="qr-modal-loading" class="flex flex-col items-center justify-center py-12 gap-3">
                <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs text-slate-400 font-semibold">Đang tạo mã thanh toán...</span>
            </div>

            <!-- Content -->
            <div id="qr-modal-content" class="space-y-4 hidden">
                <!-- QR Image Display -->
                <div class="flex justify-center p-4 bg-white rounded-2xl relative overflow-hidden group">
                    <img id="qr-modal-image" class="w-60 h-60 object-contain transition-transform group-hover:scale-105 duration-300" src="" alt="Mã VietQR">
                </div>

                <!-- Payment Details -->
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 space-y-2.5 text-xs">
                    <div class="flex justify-between"><span class="text-slate-500">Khách thuê:</span> <strong class="text-slate-200" id="qr-modal-tenant">N/A</strong></div>
                    <div class="flex justify-between"><span class="text-slate-500">Số tiền cần đóng:</span> <strong class="text-emerald-400 font-bold text-sm" id="qr-modal-amount">0đ</strong></div>
                    <div class="flex justify-between"><span class="text-slate-500">Ngân hàng:</span> <strong class="text-slate-200" id="qr-modal-bank">MB - 9999888889999</strong></div>
                    <div class="flex justify-between"><span class="text-slate-500">Chủ tài khoản:</span> <strong class="text-slate-200" id="qr-modal-name">NGUYEN VAN CHU NHA</strong></div>
                    <div class="flex justify-between items-start"><span class="text-slate-500">Nội dung chuyển:</span> <strong class="text-slate-200 text-right shrink-0 max-w-[180px] break-words" id="qr-modal-desc">N/A</strong></div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button id="qr-modal-copy" onclick="copyBankAccount()" class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all">
                        <i class="fa-solid fa-copy text-indigo-400"></i> Sao chép STK
                    </button>
                    <a id="qr-modal-download" download="VietQR_Payment.png" target="_blank" class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        <i class="fa-solid fa-download"></i> Tải ảnh QR
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- ZALO / SMS SEND MESSAGE PHONE MODAL -->
    <div id="send-msg-modal" class="fixed inset-0 z-[110] bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-3xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 md:p-8 shadow-2xl relative animate-fade-in">
            <button onclick="closeSendMsgModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all z-30">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="flex flex-col md:flex-row gap-8 items-stretch justify-center">
                <!-- LEFT SIDE: PHONE SIMULATOR -->
                <div class="flex justify-center items-center shrink-0">
                    <div class="w-[280px] h-[480px] bg-slate-950 rounded-[36px] border-[5px] border-slate-800 relative shadow-2xl flex flex-col overflow-hidden">
                        <!-- iPhone Notch & Status Bar -->
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-28 h-4 bg-slate-950 rounded-b-xl z-20 flex justify-between items-center px-4 text-[7px] text-slate-500 font-bold">
                            <span>16:40</span>
                            <div class="w-8 h-1 bg-slate-800 rounded-full"></div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-wifi"></i>
                                <i class="fa-solid fa-battery-three-quarters"></i>
                            </div>
                        </div>

                        <!-- Chat Header -->
                        <div id="phone-header" class="flex items-center justify-between px-3 py-2 bg-[#1e293b] border-b border-slate-800 rounded-t-2xl text-slate-200 mt-3 shrink-0">
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-full bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-[10px] text-indigo-400 font-bold">
                                    <i class="fa-solid fa-user text-[9px]"></i>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold" id="phone-screen-title">Khách thuê</div>
                                    <div class="text-[7px] text-emerald-400 font-bold" id="phone-app-name">Zalo Messenger</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-ellipsis-vertical text-[10px] text-slate-500"></i>
                        </div>

                        <!-- Chat Area -->
                        <div id="msg-preview-container" class="flex-1 p-3 overflow-y-auto space-y-3 bg-[#0f172a] flex flex-col justify-end">
                            <div class="max-w-[85%] rounded-2xl px-3 py-1.5 text-[10px] bg-slate-900 text-slate-400 border border-slate-800 self-start">
                                Xin chào ban quản lý. Tôi cần nhận thông báo phòng.
                            </div>
                            <!-- Live Preview Bubble -->
                            <div id="preview-bubble" class="max-w-[85%] rounded-2xl px-3 py-1.5 text-[10px] bg-indigo-600 text-white self-end ml-auto shadow-md">
                                <p id="preview-bubble-text" class="break-words leading-relaxed">(Trống)</p>
                            </div>
                        </div>

                        <!-- Message Input Mock -->
                        <div class="p-2 bg-slate-900/60 border-t border-slate-800 shrink-0 flex items-center gap-2">
                            <div class="flex-1 bg-slate-950 border border-slate-800 rounded-full px-3 py-1 text-[8px] text-slate-500">
                                Tin nhắn...
                            </div>
                            <div class="w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-center text-white text-[8px]">
                                <i class="fa-solid fa-microphone"></i>
                            </div>
                        </div>

                        <!-- Success Broadcast Overlay -->
                        <div id="msg-success-overlay" class="absolute inset-0 bg-slate-950/95 flex flex-col items-center justify-center text-center space-y-4 p-4 z-10 hidden">
                            <div class="relative flex items-center justify-center">
                                <div class="absolute w-16 h-16 bg-emerald-500/10 rounded-full animate-ping"></div>
                                <div class="absolute w-24 h-24 bg-emerald-500/5 rounded-full animate-pulse"></div>
                                <div class="w-12 h-12 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-lg">
                                    <i class="fa-solid fa-paper-plane animate-bounce"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-200">ĐANG TRUYỀN TIN</h4>
                                <p class="text-[9px] text-slate-500 mt-1">Kết nối cổng Zalo & SMS API thành công...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE: MESSAGE EDITING & CONTROL -->
                <div class="flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-envelope-open-text text-indigo-400 animate-pulse"></i> Trình Gửi Tin Nhắn Điện Tử
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Hệ thống tích hợp cổng API Zalo ZNS và SMS Brandname để tự động gửi thông báo đến cư dân.</p>
                    </div>

                    <!-- Channel Switch Tabs -->
                    <div class="flex border-b border-slate-800">
                        <div id="msg-tab-zalo" onclick="switchMsgTypeTab('zalo')" class="flex-1 py-2 text-center text-xs font-bold text-indigo-400 bg-indigo-500/10 border-b-2 border-indigo-500 transition-all cursor-pointer">
                            <i class="fa-solid fa-message mr-1.5"></i> Cổng Zalo ZNS
                        </div>
                        <div id="msg-tab-sms" onclick="switchMsgTypeTab('sms')" class="flex-1 py-2 text-center text-xs font-medium text-slate-500 hover:text-slate-300 transition-all cursor-pointer">
                            <i class="fa-solid fa-comment-sms mr-1.5"></i> Cổng SMS Brandname
                        </div>
                    </div>

                    <!-- Recipient details -->
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Số điện thoại nhận tin</label>
                        <input type="text" id="msg-phone-input" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold" placeholder="Nhập số điện thoại...">
                    </div>

                    <!-- Template Selectors -->
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Mẫu tin nhắn nhanh</label>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="selectMsgTemplate(1)" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] text-slate-300 hover:text-slate-100 hover:border-slate-700 transition-all">
                                <i class="fa-solid fa-paste text-slate-500 mr-1"></i> Mẫu 1
                            </button>
                            <button onclick="selectMsgTemplate(2)" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] text-slate-300 hover:text-slate-100 hover:border-slate-700 transition-all">
                                <i class="fa-solid fa-paste text-slate-500 mr-1"></i> Mẫu 2
                            </button>
                            <button onclick="selectMsgTemplate(3)" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] text-slate-300 hover:text-slate-100 hover:border-slate-700 transition-all">
                                <i class="fa-solid fa-paste text-slate-500 mr-1"></i> Mẫu 3
                            </button>
                        </div>
                    </div>

                    <!-- Message text input -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Nội dung tin nhắn</label>
                        <textarea id="msg-text-input" oninput="updateMessagePreview()" rows="4" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-xs text-slate-300 focus:outline-none focus:border-indigo-500 resize-none leading-relaxed" placeholder="Soạn nội dung tin nhắn..."></textarea>
                    </div>

                    <!-- Direct Personal Zalo Action (Chu tro nhan cho khach tro) -->
                    <div class="p-3.5 rounded-2xl bg-blue-950/40 border border-blue-500/30 space-y-2">
                        <div class="flex items-center justify-between text-[11px] font-bold text-blue-300">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-user-check text-blue-400"></i> Nhắn từ Zalo Cá Nhân của bạn (Chủ trọ):
                            </span>
                            <span class="text-[10px] text-emerald-400 font-semibold bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">Miễn phí 100%</span>
                        </div>
                        <button type="button" onclick="openPersonalZaloChat()" class="w-full py-2.5 px-3 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-md shadow-blue-600/25 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-comment-dots text-sm"></i>
                            <span>Mở Khung Chat Zalo Với Khách Trọ (zalo.me)</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </button>
                        <p class="text-[10px] text-slate-400 leading-normal">
                            💡 Khi bấm, hệ thống sẽ <strong>tự động sao chép</strong> tin nhắn ở trên vào clipboard. Khi Zalo mở ra, bạn chỉ cần nhấn <strong class="text-white">Ctrl + V</strong> (Dán) rồi bấm gửi.
                        </p>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center gap-3 pt-1">
                        <button onclick="closeSendMsgModal()" class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 rounded-xl text-xs font-bold transition-all">
                            Hủy bỏ
                        </button>
                        <button id="msg-send-btn" onclick="triggerSendMessage()" class="flex-1 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/25 transition-all flex items-center justify-center">
                            <i class="fa-solid fa-paper-plane mr-2"></i> Gửi Tin Cổng ZNS/SMS
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
