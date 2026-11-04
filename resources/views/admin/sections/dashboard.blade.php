            <!-- SECTION 1: DASHBOARD OVERVIEW -->
            <section id="dashboard-section" class="tab-content space-y-8 animate-fade-in">
                @php
                    $hasProfileUpdated = !empty(trim((string)($landlordProfile?->national_id ?? '')))
                        || !empty(trim((string)($landlordProfile?->business_license ?? '')));

                    $hasVerifiedBadge = in_array($tenant->verification_status ?? '', ['premium_verified', 'verified'], true)
                        || in_array($tenant->listing_badge ?? '', ['premium_verified', 'verified'], true)
                        || (($premiumRequest?->status ?? '') === 'approved');

                    $hasLegal = !empty($landlordProfile?->national_id) && !empty($landlordProfile?->business_license);
                    $isApproved = in_array($landlordProfile?->status, ['approved', 'verified'], true)
                        || in_array($landlordProfile?->verification_status, ['approved', 'verified', 'kyc_verified', 'premium_verified'], true)
                        || in_array($tenant->verification_status ?? '', ['kyc_verified', 'premium_verified', 'premium_pending'], true)
                        || in_array($tenant->listing_badge ?? '', ['kyc_verified', 'premium_verified', 'verified'], true)
                        || (($kycRequest?->status ?? '') === 'approved')
                        || (($premiumRequest?->status ?? '') === 'approved');
                    $isPending = ($landlordProfile?->status === 'pending_approval')
                        || (($kycRequest?->status ?? '') === 'pending')
                        || (($tenant->verification_status ?? '') === 'kyc_pending');
                @endphp

                <!-- BANNER ĐỊNH DANH / NÂNG CẤP TÍCH XANH THEO TRẠNG THÁI HỒ SƠ -->
                @if(!$hasVerifiedBadge)
                    @if(!$hasProfileUpdated)
                        <!-- 1. Khi chủ trọ CHƯA cập nhật hồ sơ -> Hiện mục Cập nhật hồ sơ -->
                        <div class="glass-card rounded-2xl p-5 border border-amber-500/30 bg-gradient-to-r from-amber-500/15 via-indigo-950/40 to-slate-900/80 mb-6 shadow-lg shadow-amber-500/5">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-300 flex items-center justify-center shrink-0 mt-0.5 shadow-md shadow-amber-500/20">
                                        <i class="fa-solid fa-address-card text-xl text-amber-400"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-base font-extrabold text-white">Cập nhật hồ sơ định danh chủ trọ</h4>
                                            <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30 uppercase tracking-wider flex items-center gap-1">
                                                <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Yêu cầu cập nhật
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-300 mt-1.5 leading-relaxed max-w-3xl">
                                            Vui lòng cập nhật số CCCD và thông tin giấy tờ để hoàn thiện hồ sơ chủ trọ, mở khóa quyền quản lý cơ sở lưu trú và đủ điều kiện nâng cấp huy hiệu Tích Xanh chính chủ.
                                        </p>
                                    </div>
                                </div>

                                <a href="{{ route('smartroom.admin', ['tab' => 'profile-section']) }}" onclick="if(typeof switchTab === 'function'){ switchTab('profile-section'); return false; }" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-400 hover:to-indigo-500 text-white font-bold text-xs transition-all flex items-center justify-center gap-2 shrink-0 shadow-lg shadow-amber-500/25 cursor-pointer">
                                    <i class="fa-solid fa-user-pen text-xs"></i>
                                    <span>Cập nhật hồ sơ</span>
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- 2. Khi chủ trọ ĐÃ cập nhật hồ sơ -> Hiện mục Nâng cấp lên Tích Xanh -->
                        <div class="glass-card rounded-2xl p-5 border border-sky-500/30 bg-gradient-to-r from-sky-500/15 via-indigo-950/40 to-slate-900/80 mb-6 shadow-lg shadow-sky-500/5">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-12 h-12 rounded-2xl bg-sky-500/20 border border-sky-400/40 text-sky-300 flex items-center justify-center shrink-0 mt-0.5 shadow-md shadow-sky-500/20">
                                        <i class="fa-solid fa-certificate text-xl text-sky-400"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-base font-extrabold text-white">Nâng cấp Tích Xanh uy tín cho nhà trọ của bạn</h4>
                                            <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full bg-sky-500/20 text-sky-300 border border-sky-400/30 uppercase tracking-wider flex items-center gap-1">
                                                <i class="fa-solid fa-bolt text-[9px] text-amber-400"></i> Ưu tiên Top 1 Renty
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-300 mt-1.5 leading-relaxed max-w-3xl">
                                            Hồ sơ định danh của bạn đã được ghi nhận. Nâng cấp để tin đăng được gắn huy hiệu <strong class="text-sky-300">Tích Xanh chính chủ</strong>, tự động mở khóa nhận tiền VietQR và tăng đến 300% tỷ lệ khách thuê liên hệ.
                                        </p>
                                    </div>
                                </div>

                                <a href="{{ route('smartroom.admin', ['tab' => 'profile-section']) }}" onclick="if(typeof switchTab === 'function'){ switchTab('profile-section'); return false; }" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white font-bold text-xs transition-all flex items-center justify-center gap-2 shrink-0 shadow-lg shadow-sky-500/25 cursor-pointer">
                                    <i class="fa-solid fa-certificate text-xs text-sky-300"></i>
                                    <span>Nâng cấp lên Tích Xanh</span>
                                </a>
                            </div>
                        </div>
                    @endif
                @endif

                @if(!$isApproved)
                    <div class="glass-card rounded-2xl p-5 border border-amber-500/30 bg-gradient-to-r from-amber-500/10 via-slate-900/60 to-indigo-500/10 mb-6">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-bold text-white">
                                            @if($isPending)
                                                Hồ sơ đang chờ Admin phê duyệt
                                            @else
                                                Hồ sơ chủ trọ chưa hoàn thiện
                                            @endif
                                        </h4>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isPending ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                            {{ $isPending ? 'Chờ duyệt' : 'Chưa đủ MST/CCCD' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                                        Bạn có thể quản lý phòng nội bộ bình thường. Tuy nhiên để <strong class="text-amber-300">đăng tin công khai lên Renty</strong>, bạn cần <a href="{{ route('smartroom.admin') }}?tab=profile-section" class="text-indigo-400 underline hover:text-indigo-300 font-bold">hoàn thiện hồ sơ (MST/CCCD)</a> và chờ Admin duyệt.
                                    </p>
                                </div>
                            </div>

                            <!-- Stepper 3 Bước -->
                            <div class="flex items-center gap-2 shrink-0 bg-slate-950/70 border border-slate-800 rounded-xl px-4 py-2.5">
                                <!-- Bước 1: Đã xong (Tick xanh lá nhạt) -->
                                <div class="flex items-center gap-1.5">
                                    <div class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 flex items-center justify-center text-[10px]">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-400">B1: ĐK</span>
                                </div>
                                <div class="w-3 h-0.5 bg-emerald-500/40"></div>
                                <!-- Bước 2: Đã xong (Tick xanh lá nhạt) -->
                                <div class="flex items-center gap-1.5">
                                    <div class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 flex items-center justify-center text-[10px]">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-400">B2: OTP</span>
                                </div>
                                <div class="w-3 h-0.5 bg-indigo-500/40"></div>
                                <!-- Bước 3: Đang làm (Active - Màu tím) -->
                                <div class="flex items-center gap-1.5">
                                    <div class="w-5 h-5 rounded-full bg-indigo-600 border border-indigo-400 text-white flex items-center justify-center text-[10px] font-black shadow-sm">
                                        3
                                    </div>
                                    <span class="text-[10px] font-bold text-indigo-300">B3: Hồ Sơ</span>
                                </div>
                            </div>

                            <a href="{{ route('smartroom.admin') }}?tab=profile-section" class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs transition-all flex items-center justify-center gap-1.5 shrink-0 shadow-md shadow-amber-500/10">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                <span>Hoàn thiện ngay</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Stats ribbon -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tổng số phòng</span>
                            <h3 class="text-3xl font-extrabold text-slate-100 mt-2">{{ $totalRooms }}</h3>
                            <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 mt-1">
                                <i class="fa-solid fa-arrow-up"></i> 100% Khai thác
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center">
                            <i class="fa-solid fa-door-open text-xl"></i>
                        </div>
                    </div>
                    <!-- Occupied Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-red-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Đang thuê</span>
                            <h3 class="text-3xl font-extrabold text-red-400 mt-2">{{ $occupiedRooms }}</h3>
                            <span class="text-[10px] text-slate-400 font-semibold flex items-center gap-1 mt-1">
                                Tỉ lệ lấp đầy: {{ $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0 }}%
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center">
                            <i class="fa-solid fa-user-check text-xl"></i>
                        </div>
                    </div>
                    <!-- Empty Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Phòng trống</span>
                            <h3 class="text-3xl font-extrabold text-emerald-400 mt-2">{{ $emptyRooms }}</h3>
                            <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 mt-1">
                                Sẵn sàng đón khách
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <i class="fa-solid fa-circle-plus text-xl"></i>
                        </div>
                    </div>
                    <!-- Overdue Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-amber-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Chưa đóng tiền</span>
                            <h3 class="text-3xl font-extrabold text-amber-400 mt-2">{{ $overdueRooms }}</h3>
                            <span class="text-[10px] text-amber-400 font-semibold flex items-center gap-1 mt-1">
                                Cần nhắc nhở đóng phí
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                        </div>
                    </div>
                </div>

                @if($isLandlord)
                <!-- AI insights and assistant -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <div class="glass-card rounded-2xl p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-200 flex items-center gap-2">
                                    <i class="fa-solid fa-wand-magic-sparkles text-indigo-400"></i> Nhận xét AI
                                </h3>
                                <p class="text-xs text-slate-500 mt-1">Phân tích nhanh doanh thu, công nợ, phòng trống và rủi ro vận hành.</p>
                            </div>
                            <button type="button" onclick="loadAiDashboardInsight(this)" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold flex items-center gap-2">
                                <i class="fa-solid fa-rotate"></i> Tạo nhận xét
                            </button>
                        </div>
                        <div id="ai-dashboard-insight" class="mt-5 rounded-xl bg-slate-950/40 border border-slate-800 p-4 text-sm text-slate-300 leading-relaxed">
                            Bấm "Tạo nhận xét" để AI phân tích dữ liệu dashboard hiện tại.
                        </div>
                    </div>

                    <!-- FEAT_14_AI_ASSISTANT_HUB: HỆ SINH THÁI TRỢ LÝ AI HỖ TRỢ TOÀN DIỆN -->
                    <div id="ai-assistant-hub-container" class="glass-card rounded-3xl p-6 sm:p-7 border border-indigo-500/20 relative overflow-hidden">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-5 border-b border-slate-800/80 pb-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-gradient-to-r from-purple-500/20 to-pink-500/20 text-purple-300 border border-purple-500/30">
                                        FEAT_14 AI ASSISTANT HUB
                                    </span>
                                </div>
                                <h3 class="text-lg font-black text-white mt-1 flex items-center gap-2.5">
                                    <i class="fa-solid fa-wand-magic-sparkles text-purple-400"></i>
                                    Trợ Lý AI Hỗ Trợ Toàn Diện
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">Trung tâm điều phối Trợ lý AI dành riêng cho Chủ trọ: Cố vấn kinh doanh & Soạn bài SEO, Trợ lý Hỏi - Đáp & Tư vấn nghiệp vụ quản lý.</p>
                            </div>
                        </div>

                        <!-- 1. Tab Selector (tabMode dành riêng cho Chủ trọ) -->
                        <div class="flex flex-wrap items-center gap-2 mb-4 bg-slate-950/60 p-1.5 rounded-2xl border border-slate-800/90 text-xs">
                            <button type="button" onclick="switchAiHubMode('landlord_advisor', this)" class="ai-hub-tab-btn flex-1 min-w-[150px] py-2 px-3 rounded-xl font-bold bg-indigo-600 text-white transition-all flex items-center justify-center gap-2" data-mode="landlord_advisor">
                                <i class="fa-solid fa-bullhorn text-amber-300"></i> Cố vấn & Viết bài SEO
                            </button>
                            <button type="button" onclick="switchAiHubMode('landlord_qa', this)" class="ai-hub-tab-btn flex-1 min-w-[150px] py-2 px-3 rounded-xl font-semibold text-slate-400 hover:text-slate-200 transition-all flex items-center justify-center gap-2" data-mode="landlord_qa">
                                <i class="fa-solid fa-comments text-cyan-400"></i> Hỏi - Đáp & Trợ Lý Chủ Trọ
                            </button>
                        </div>

                        <!-- 2. Controls & Prompt input (cboTone, txtPrompt) -->
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <label for="ai-hub-prompt" class="text-xs font-bold text-slate-300 flex items-center gap-1.5">
                                    <i class="fa-solid fa-message text-indigo-400"></i> Câu hỏi / Yêu cầu <span class="text-rose-400">*</span>
                                </label>
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="text-slate-400 text-[11px] font-semibold">Văn phong:</span>
                                    <select id="ai-hub-tone" class="bg-slate-900 border border-slate-800 text-slate-300 rounded-xl px-2.5 py-1 text-xs font-medium focus:outline-none focus:border-purple-500">
                                        <option value="professional" selected>Chuyên nghiệp, thu hút</option>
                                        <option value="friendly">Thân thiện, gần gũi</option>
                                        <option value="urgent">Khẩn cấp, an toàn</option>
                                        <option value="creative_seo">Sáng tạo Marketing SEO</option>
                                    </select>
                                </div>
                            </div>

                            <div class="relative">
                                <textarea id="ai-hub-prompt" 
                                          rows="3" 
                                          placeholder="Bạn cần AI hỗ trợ điều gì? Nhập yêu cầu tại đây... (Nhấn Enter để gửi)" 
                                          class="w-full px-4 py-3 rounded-2xl bg-slate-950/70 border border-slate-800 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-purple-500 transition-all resize-none"></textarea>
                                <div id="ai-hub-prompt-error" class="hidden text-xs text-rose-400 font-semibold mt-1 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span id="ai-hub-prompt-error-text"></span>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                <div class="flex items-center gap-2">
                                    <span id="ai-hub-char-count" class="text-[11px] text-slate-500">0/2000 ký tự</span>
                                    <span id="ai-hub-countdown-badge" class="hidden text-[11px] font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-lg animate-pulse">
                                        <i class="fa-solid fa-clock-rotate-left"></i> Đang chờ: <span id="ai-hub-countdown-seconds">30</span>s
                                    </span>
                                </div>

                                <button type="button" 
                                        id="btn-send-ai-hub" 
                                        onclick="submitAiAssistantHub()" 
                                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:opacity-95 text-white font-extrabold text-xs shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all">
                                    <i class="fa-solid fa-bolt text-amber-300"></i>
                                    <span>Gửi yêu cầu đến AI</span>
                                </button>
                            </div>
                        </div>

                        <!-- 3. Response Area (responseBox) -->
                        <div id="ai-hub-response-container" class="mt-5 pt-4 border-t border-slate-800/80">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-robot text-purple-400"></i> Phản hồi của Trợ lý AI
                                </span>
                                <div id="ai-hub-status-badge" class="hidden text-[10px] font-bold px-2 py-0.5 rounded border"></div>
                            </div>

                            <div id="ai-hub-response-box" class="rounded-2xl bg-slate-950/60 border border-slate-800/80 p-4 text-sm text-slate-300 leading-relaxed min-h-[90px] whitespace-pre-line relative">
                                <span class="text-slate-500 italic">Chọn tab tác vụ phía trên và nhập câu hỏi/yêu cầu để nhận hỗ trợ tức thời từ Trợ lý AI SmartRoom.</span>
                            </div>

                            <!-- 4. Quick Action Buttons (btnQuickAction) -->
                            <div id="ai-hub-actions-bar" class="hidden flex flex-wrap items-center gap-2.5 mt-3 pt-2">
                                <button type="button" id="btn-copy-ai-post" onclick="copyAiHubContent()" class="hidden px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                                    <i class="fa-solid fa-copy"></i> Sao chép bài viết marketing SEO
                                </button>
                                <button type="button" id="btn-booking-viewing" onclick="openBookingFromAi()" class="hidden px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-md shadow-indigo-600/20 flex items-center gap-1.5">
                                    <i class="fa-solid fa-calendar-check"></i> Đặt lịch hẹn xem phòng
                                </button>
                                <button type="button" id="btn-view-emergency-ticket" onclick="openEmergencyTicketModal()" class="hidden px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition-all shadow-md shadow-rose-600/20 flex items-center gap-1.5">
                                    <i class="fa-solid fa-ticket"></i> Xem ticket bảo trì khẩn cấp
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Revenue Line & Doughnut Split Chart -->
                    <div class="glass-card rounded-2xl p-6 lg:col-span-2 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4 border-b border-slate-900/60 pb-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-200">Doanh Thu 3 Tháng Gần Nhất</h3>
                                <p class="text-xs text-slate-500">Báo cáo xu hướng cột và cơ cấu nguồn thu từ điện, nước, phòng</p>
                            </div>
                            <span class="text-[10px] px-2.5 py-1 rounded bg-indigo-500/10 text-indigo-400 font-bold border border-indigo-500/20 uppercase tracking-wider">Doanh thu tháng 06</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                            <!-- Left: Trend Bar Chart -->
                            <div class="md:col-span-3 flex flex-col justify-between">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Xu hướng 3 tháng</span>
                                <div class="h-56 w-full">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                            
                            <!-- Right: Doughnut Breakdown Chart -->
                            <div class="md:col-span-2 flex flex-col justify-between border-t md:border-t-0 md:border-l border-slate-800/50 pt-4 md:pt-0 md:pl-6">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Cơ Cấu Nguồn Thu</span>
                                <div class="h-36 w-full flex items-center justify-center relative">
                                    <canvas id="revenueBreakdownChart"></canvas>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
                                        <span class="text-[8px] text-slate-500 font-bold uppercase tracking-wider">Tổng thu</span>
                                        <span class="text-xs font-black text-indigo-400" id="breakdown-total-txt">--M</span>
                                    </div>
                                </div>
                                <div class="space-y-1.5 mt-3 text-[10px]">
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-slate-450"><span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>Tiền phòng</span>
                                        <strong class="text-slate-200" id="breakdown-room-pct">--%</strong>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-slate-450"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Tiền điện</span>
                                        <strong class="text-slate-200" id="breakdown-elec-pct">--%</strong>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-slate-450"><span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>Tiền nước</span>
                                        <strong class="text-slate-200" id="breakdown-water-pct">--%</strong>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-slate-450"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Dịch vụ</span>
                                        <strong class="text-slate-200" id="breakdown-service-pct">--%</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Doughnut Room Status Chart -->
                    <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                        <div class="mb-4">
                            <h3 class="text-base font-bold text-slate-200">Tỷ Lệ Phòng Trống / Đầy</h3>
                            <p class="text-xs text-slate-500">Tổng quan nhanh tình trạng khai thác phòng</p>
                        </div>
                        <div class="h-48 w-full flex items-center justify-center relative">
                            <canvas id="statusChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Tổng phòng</span>
                                <strong class="text-2xl font-black text-slate-100">{{ $totalRooms }}</strong>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                            <div class="p-2 rounded-xl bg-emerald-500/5 border border-emerald-500/10">
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">Trống</span>
                                <strong class="text-sm text-emerald-400">{{ $totalRooms > 0 ? round(($emptyRooms / $totalRooms) * 100, 1) : 0 }}%</strong>
                            </div>
                            <div class="p-2 rounded-xl bg-red-500/5 border border-red-500/10">
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">Thuê</span>
                                <strong class="text-sm text-red-400">{{ $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0 }}%</strong>
                            </div>
                            <div class="p-2 rounded-xl bg-amber-500/5 border border-amber-500/10">
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">Nợ</span>
                                <strong class="text-sm text-amber-400">{{ $totalRooms > 0 ? round(($overdueRooms / $totalRooms) * 100, 1) : 0 }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Smart Alerts Dashboard -->
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-brain text-indigo-400"></i>
                                Dashboard cảnh báo thông minh
                            </h3>
                            <p class="text-xs text-slate-500">Tự động quét hợp đồng, hóa đơn, phòng trống và thiết bị cần xử lý</p>
                        </div>
                        <div class="px-4 py-2 rounded-xl bg-slate-900/70 border border-slate-800 text-xs font-bold text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-bell text-amber-400"></i>
                            {{ $smartAlertTotal }} cảnh báo cần chú ý
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                        @foreach($smartAlertGroups as $group)
                            @php
                                $alertColorClasses = [
                                    'indigo' => [
                                        'box' => 'bg-indigo-500/5 border-indigo-500/20',
                                        'icon' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                                        'count' => 'text-indigo-400',
                                        'badge' => 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20',
                                    ],
                                    'amber' => [
                                        'box' => 'bg-amber-500/5 border-amber-500/20',
                                        'icon' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        'count' => 'text-amber-400',
                                        'badge' => 'bg-amber-500/10 text-amber-300 border-amber-500/20',
                                    ],
                                    'emerald' => [
                                        'box' => 'bg-emerald-500/5 border-emerald-500/20',
                                        'icon' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'count' => 'text-emerald-400',
                                        'badge' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
                                    ],
                                    'rose' => [
                                        'box' => 'bg-rose-500/5 border-rose-500/20',
                                        'icon' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                        'count' => 'text-rose-400',
                                        'badge' => 'bg-rose-500/10 text-rose-300 border-rose-500/20',
                                    ],
                                ][$group['color']];
                            @endphp

                            <div class="rounded-2xl border {{ $alertColorClasses['box'] }} p-4 flex flex-col min-h-[320px]">
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl border {{ $alertColorClasses['icon'] }} flex items-center justify-center shrink-0">
                                            <i class="fa-solid {{ $group['icon'] }}"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-extrabold text-slate-200 leading-snug">{{ $group['label'] }}</h4>
                                            <p class="text-[10px] text-slate-500 mt-0.5">Cập nhật theo dữ liệu hiện tại</p>
                                        </div>
                                    </div>
                                    <span class="text-2xl font-black {{ $alertColorClasses['count'] }}">{{ $group['count'] }}</span>
                                </div>

                                <div class="space-y-2 flex-1">
                                    @forelse($group['items'] as $item)
                                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3">
                                            <div class="flex items-start justify-between gap-2">
                                                <strong class="text-xs text-slate-200 leading-snug">{{ $item['title'] }}</strong>
                                                <span class="shrink-0 text-[9px] font-bold px-2 py-0.5 rounded-full border {{ $alertColorClasses['badge'] }}">{{ $item['meta'] }}</span>
                                            </div>
                                            <p class="text-[10px] text-slate-500 mt-1 leading-relaxed">{{ $item['detail'] }}</p>
                                        </div>
                                    @empty
                                        <div class="h-full min-h-[170px] rounded-xl bg-slate-950/30 border border-slate-800/50 flex flex-col items-center justify-center text-center p-4">
                                            <i class="fa-solid fa-circle-check text-emerald-400 mb-2"></i>
                                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ $group['empty'] }}</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($isLandlord)
                <!-- Notification Center -->
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-6 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-paper-plane text-cyan-400"></i>
                                Notification Center
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">Gui gia lap Email, Zalo, SMS va luu log tat ca thong bao.</p>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="px-4 py-3 rounded-xl bg-slate-950/50 border border-slate-800">
                                <div class="text-lg font-black text-emerald-300">{{ $notificationSummary['sent'] }}</div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase">Sent</div>
                            </div>
                            <div class="px-4 py-3 rounded-xl bg-slate-950/50 border border-slate-800">
                                <div class="text-lg font-black text-amber-300">{{ $notificationSummary['skipped'] }}</div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase">Skipped</div>
                            </div>
                            <div class="px-4 py-3 rounded-xl bg-slate-950/50 border border-slate-800">
                                <div class="text-lg font-black text-indigo-300">{{ $notificationSummary['today'] }}</div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase">Today</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-3 mb-6">
                        <form method="POST" action="{{ route('smartroom.admin.notifications.run_all', [], false) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold flex items-center justify-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Run all
                            </button>
                        </form>
                        <button type="button" onclick="triggerAutoRemind(this)" class="w-full px-4 py-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold flex items-center justify-center gap-2">
                            <i class="fa-solid fa-money-bill-wave"></i> Payment
                        </button>
                        <form method="POST" action="{{ route('smartroom.admin.notifications.contracts', [], false) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold flex items-center justify-center gap-2">
                                <i class="fa-solid fa-file-signature"></i> Contracts
                            </button>
                        </form>
                        <form method="POST" action="{{ route('smartroom.admin.notifications.maintenance', [], false) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center justify-center gap-2">
                                <i class="fa-solid fa-screwdriver-wrench"></i> Maintenance
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-900">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/70 border-b border-slate-900">
                                <tr>
                                    <th class="px-4 py-3 font-bold">Time</th>
                                    <th class="px-4 py-3 font-bold">Type</th>
                                    <th class="px-4 py-3 font-bold">Channel</th>
                                    <th class="px-4 py-3 font-bold">Recipient</th>
                                    <th class="px-4 py-3 font-bold">Subject</th>
                                    <th class="px-4 py-3 font-bold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                                @forelse($notificationLogs as $log)
                                    <tr class="hover:bg-slate-900/30 transition-all">
                                        <td class="px-4 py-3 text-xs text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3 text-xs font-bold text-slate-200">{{ str_replace('_', ' ', $log->type) }}</td>
                                        <td class="px-4 py-3 text-xs font-bold text-cyan-300 uppercase">{{ $log->channel }}</td>
                                        <td class="px-4 py-3 text-xs text-slate-400">
                                            <div class="font-bold text-slate-300">{{ $log->recipient_name ?? '-' }}</div>
                                            <div class="text-[10px] text-slate-500">{{ $log->recipient_contact ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-slate-400 max-w-[320px] truncate" title="{{ $log->message }}">{{ $log->subject }}</td>
                                        <td class="px-4 py-3">
                                            @if($log->status === 'sent')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">sent</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-300 border border-amber-500/20">{{ $log->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-xs text-slate-500">No notification logs yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Recent Events Table -->
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200">Hoạt Động Gần Đây</h3>
                            <p class="text-xs text-slate-500">Các giao dịch và cập nhật mới nhất trong tháng</p>
                        </div>
                        <button class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                            Xem tất cả <i class="fa-solid fa-angle-right"></i>
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/50 border-b border-slate-900">
                                <tr>
                                    <th class="px-6 py-4 font-bold">Cư dân / Phòng</th>
                                    <th class="px-6 py-4 font-bold">Loại giao dịch</th>
                                    <th class="px-6 py-4 font-bold">Số tiền</th>
                                    <th class="px-6 py-4 font-bold">Thời gian</th>
                                    <th class="px-6 py-4 font-bold">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900">
                                @foreach($rooms->where('status', '!=', 'empty')->take(3) as $r)
                                @php
                                    $latestRecord = $r->utilityRecords->first();
                                    $resident = $r->residents->first();
                                @endphp
                                @if($resident)
                                <tr class="hover:bg-slate-900/30 transition-all">
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 font-bold text-xs">
                                            {{ $r->room_number }}
                                        </div>
                                        <div>
                                            <strong class="text-slate-200 text-xs block">{{ $resident->name }}</strong>
                                            <span class="text-[10px] text-slate-500">Phòng {{ $r->room_number }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-xs text-slate-400">
                                        {{ $r->status === 'overdue' ? 'Gửi hóa đơn tháng ' . ($latestRecord ? explode('-', $latestRecord->billing_month)[1] : '06') : 'Đã đóng hóa đơn tháng ' . ($latestRecord ? explode('-', $latestRecord->billing_month)[1] : '05') }}
                                    </td>
                                    <td class="px-6 py-4 {{ $r->status === 'overdue' ? 'text-amber-400' : 'text-emerald-400' }} font-bold text-xs">
                                        @if($latestRecord)
                                            {{ number_format($r->price + ($latestRecord->new_electricity - $latestRecord->old_electricity) * $latestRecord->electricity_price + ($latestRecord->new_water - $latestRecord->old_water) * $latestRecord->water_price + 150000) }}đ
                                        @else
                                            {{ number_format($r->price + 150000) }}đ
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">
                                        {{ $latestRecord ? $latestRecord->updated_at->diffForHumans() : 'Hôm nay' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($r->status === 'overdue')
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Chưa đóng</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Hoàn tất</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

