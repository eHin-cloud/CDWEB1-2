        <!-- TOP NAVBAR -->
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div class="flex items-center gap-2">
                <h2 id="section-title" class="text-lg font-bold text-slate-100">Tổng Quan Hệ Thống</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Bộ chọn Double Màu (Cặp Màu Kép) giao diện Admin -->
                @include('admin.partials.accent-picker')

                <!-- Nút chuyển Light / Dark -->
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition-all shadow-sm focus:outline-none" aria-label="Chuyển chế độ sáng tối" title="Chuyển chế độ Sáng ☀️ / Tối 🌙">
                    <i class="fa-solid fa-moon text-sm" data-theme-icon></i>
                </button>
                <!-- Notifications Cho Chủ Trọ -->
                <div class="relative" id="admin-notification-container">
                    @php
                        $pendingContactCount = $contactRequestStats['pending'] ?? 0;
                        $expiringContractCount = isset($expiringContracts) ? $expiringContracts->count() : 0;
                        $billAlertCount = isset($billAlerts) ? $billAlerts->count() : 0;
                        $equipmentAlertCount = isset($equipmentAlerts) ? $equipmentAlerts->count() : 0;
                        $emptyRoomAlertCount = isset($emptyRoomAlerts) ? $emptyRoomAlerts->count() : 0;
                        $totalNotices = $pendingContactCount + ($smartAlertTotal ?? 0);
                    @endphp
                    <button type="button" 
                            id="admin-notification-btn"
                            onclick="toggleAdminNotifications()" 
                            class="relative w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                            aria-expanded="false"
                            title="Thông báo & Việc cần xử lý">
                        <i class="fa-regular fa-bell text-[15px]"></i>
                        @if($totalNotices > 0)
                            <span id="admin-notification-badge" class="absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] font-extrabold px-1.5 min-w-[18px] h-[18px] rounded-full flex items-center justify-center shadow-md shadow-rose-500/40 animate-pulse">
                                {{ $totalNotices > 99 ? '99+' : $totalNotices }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Panel Thông Báo Cho Chủ Trọ -->
                    <div id="admin-notification-dropdown" 
                         class="hidden absolute right-0 mt-3 w-80 sm:w-96 rounded-2xl bg-[#0d121f]/95 border border-slate-800 shadow-2xl backdrop-blur-xl z-50 overflow-hidden">
                        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-bell text-indigo-400 text-sm"></i>
                                <h3 class="text-sm font-bold text-slate-100">Thông Báo & Nhắc Việc</h3>
                                @if($totalNotices > 0)
                                    <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 text-[10px] font-extrabold border border-rose-500/20">
                                        {{ $totalNotices }}
                                    </span>
                                @endif
                            </div>
                            @if($totalNotices > 0)
                                <button type="button" onclick="markAllNotificationsAsRead()" class="text-[11px] font-semibold text-slate-400 hover:text-indigo-400 transition-colors">
                                    Đánh dấu đã đọc
                                </button>
                            @endif
                        </div>

                        <div class="max-h-[380px] overflow-y-auto divide-y divide-slate-800/60 p-2 space-y-1">
                            @if($pendingContactCount > 0)
                                <a href="javascript:void(0)" onclick="switchTab('contact-section'); toggleAdminNotifications(false);" class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-800/50 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-phone-volume text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-200 group-hover:text-rose-300 transition-colors">
                                            {{ $pendingContactCount }} yêu cầu tư vấn mới
                                        </p>
                                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">Khách thuê đang chờ tư vấn hoặc đăng ký xem phòng</p>
                                        <span class="inline-block mt-1 text-[10px] text-rose-400 font-semibold">Ưu tiên cao • Xử lý ngay &rarr;</span>
                                    </div>
                                </a>
                            @endif

                            @if($expiringContractCount > 0)
                                <a href="javascript:void(0)" onclick="switchTab('contract-section'); toggleAdminNotifications(false);" class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-800/50 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-file-signature text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-200 group-hover:text-indigo-300 transition-colors">
                                            {{ $expiringContractCount }} hợp đồng sắp hết hạn
                                        </p>
                                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">Hết hạn trong vòng 30 ngày tới cần gia hạn</p>
                                        <span class="inline-block mt-1 text-[10px] text-indigo-400 font-semibold">Quản lý hợp đồng &rarr;</span>
                                    </div>
                                </a>
                            @endif

                            @if($billAlertCount > 0)
                                <a href="javascript:void(0)" onclick="switchTab('utility-section'); toggleAdminNotifications(false);" class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-800/50 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-receipt text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-200 group-hover:text-amber-300 transition-colors">
                                            {{ $billAlertCount }} khoản hóa đơn quá hạn
                                        </p>
                                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">Tiền phòng, điện nước chưa được thanh toán</p>
                                        <span class="inline-block mt-1 text-[10px] text-amber-400 font-semibold">Nhắc nợ cư dân &rarr;</span>
                                    </div>
                                </a>
                            @endif

                            @if($emptyRoomAlertCount > 0)
                                <a href="javascript:void(0)" onclick="switchTab('room-map-section'); toggleAdminNotifications(false);" class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-800/50 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-door-open text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-200 group-hover:text-emerald-300 transition-colors">
                                            {{ $emptyRoomAlertCount }} phòng trống lâu ngày
                                        </p>
                                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">Phòng trống trên 30 ngày cần tìm khách thuê</p>
                                        <span class="inline-block mt-1 text-[10px] text-emerald-400 font-semibold">Xem sơ đồ phòng &rarr;</span>
                                    </div>
                                </a>
                            @endif

                            @if($equipmentAlertCount > 0)
                                <a href="{{ route('admin.equipment.index') }}" class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-800/50 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-screwdriver-wrench text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-200 group-hover:text-rose-300 transition-colors">
                                            {{ $equipmentAlertCount }} sự cố thiết bị cần chú ý
                                        </p>
                                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">Tồn kho thiết bị sắp hết hoặc có báo hỏng mở</p>
                                        <span class="inline-block mt-1 text-[10px] text-rose-400 font-semibold">Kiểm tra vật tư &rarr;</span>
                                    </div>
                                </a>
                            @endif

                            @if($totalNotices == 0)
                                <div class="p-6 text-center">
                                    <div class="w-12 h-12 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto mb-3">
                                        <i class="fa-solid fa-check text-base"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-200">Không có cảnh báo tồn đọng</p>
                                    <p class="text-[11px] text-slate-500 mt-1">Mọi hoạt động quản lý phòng trọ đang vận hành ổn định.</p>
                                </div>
                            @endif
                        </div>

                        <div class="p-3 bg-slate-950/60 border-t border-slate-800 text-center">
                            <a href="javascript:void(0)" onclick="switchTab('dashboard-section'); toggleAdminNotifications(false);" class="text-[11px] font-bold text-indigo-400 hover:text-indigo-300 transition-colors inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-chart-pie"></i> Xem toàn bộ cảnh báo trên Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Date: Ngày Hiện Tại -->
                <div class="text-sm font-semibold text-slate-400 bg-slate-900 border border-slate-800 px-4 py-2 rounded-xl flex items-center gap-2" title="Ngày hiện tại">
                    <i class="fa-regular fa-calendar text-indigo-400"></i>
                    <span>{{ date('d/m/Y') }}</span>
                </div>
            </div>
        </header>

