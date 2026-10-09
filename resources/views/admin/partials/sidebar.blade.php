@php
    $currentTab = request()->query('tab', 'dashboard-section');
    $isDashboardRoute = request()->routeIs('smartroom.admin');
    $user = Auth::user();
    $isLandlord = $user?->isLandlord();
    $isAdmin = $user?->isAdmin();
    $isSuperAdmin = $user?->isSuperAdmin();
    $isReceptionist = $user?->isReceptionist();
    $isHousekeeper = $user?->isHousekeeper();
    $isManager = $user?->isManager();
    $tenantId = $user?->tenant_id;
    if (!$tenantId && $user && !$isAdmin && !$isSuperAdmin) {
        $tenantId = \App\Models\Tenant::where('email', 'contact@smartroom-caugiay.vn')->value('id')
            ?? \App\Models\Tenant::query()->orderBy('id')->value('id');
    }

    if (isset($contactRequestStats['pending'])) {
        $sidebarContactCount = (int) $contactRequestStats['pending'];
    } elseif ($tenantId) {
        $sidebarContactCount = \App\Models\ContactRequest::whereHas('room', function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        })->where('status', 'pending')->count();
    } elseif ($user && ($user->isAdmin() || $user->isSuperAdmin())) {
        $sidebarContactCount = \App\Models\ContactRequest::where('status', 'pending')->count();
    } else {
        $sidebarContactCount = 0;
    }

    $sidebarTicketQuery = \App\Models\Ticket::where('status', 'pending');
    if ($tenantId && !$user?->isAdmin() && !$user?->isSuperAdmin()) {
        $sidebarTicketQuery->where('tenant_id', $tenantId);
    }
    $sidebarPendingTickets = $sidebarTicketQuery->count();

    $sidebarDirtyRoomsCount = \App\Models\Room::when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
        ->where(function ($query) {
            $query->whereIn('status', ['cleaning'])
                  ->orWhereIn('cleaning_status', ['dirty', 'cleaning']);
        })->count();
    $userInitials = '';
    $userName = '';
    $userRoleLabel = 'Quản trị viên';
    
    if (Auth::check()) {
        $userName = $user->name;
        if ($user->isSuperAdmin()) {
            $userRoleLabel = 'Quản trị viên tối cao (Superadmin)';
        } elseif ($isLandlord) {
            $userRoleLabel = 'Chủ chung cư mini';
        } elseif ($isAdmin) {
            $userRoleLabel = 'Quản trị viên';
        } elseif ($isReceptionist) {
            $userRoleLabel = 'Nhân viên lễ tân';
        } elseif ($isHousekeeper) {
            $userRoleLabel = 'Nhân viên buồng phòng';
        } elseif ($isManager) {
            $userRoleLabel = 'Quản lý vận hành';
        } else {
            $userRoleLabel = 'Nhân viên vận hành';
        }
        $words = explode(' ', trim($userName));
        if (count($words) >= 2) {
            $userInitials = mb_substr($words[count($words) - 2], 0, 1) . mb_substr($words[count($words) - 1], 0, 1);
        } else {
            $userInitials = mb_substr($userName, 0, 2);
        }
        $userInitials = mb_strtoupper($userInitials);
    }
@endphp

<!-- SIDEBAR -->
<aside id="admin-sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#0d121f] border-r border-slate-900 flex flex-col h-screen z-30 transition-[width] duration-200 shrink-0 select-none">
    <!-- Sidebar Header (Cố định ở đỉnh) -->
    <div class="px-5 py-4 border-b border-slate-900 flex items-center justify-between shrink-0">
        <a href="{{ route('home') }}" class="sidebar-brand flex items-center gap-3 min-w-0 group" title="Về trang chủ">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-hotel text-white text-sm"></i>
            </div>
            <span class="text-base font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-400 bg-clip-text text-transparent">SmartRoom</span>
        </a>
        <button type="button" id="sidebar-toggle" class="w-7 h-7 rounded-lg border border-slate-800/80 text-slate-400 hover:text-slate-100 hover:bg-slate-800/60 flex items-center justify-center transition-all" title="Thu gọn/mở rộng menu">
            <i class="fa-solid fa-angles-left text-xs transition-transform"></i>
        </button>
    </div>
    
    <!-- Navigation Links (Cuộn độc lập, mượt mà, phân nhóm chuyên nghiệp) -->
    <nav id="admin-sidebar-nav" class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-3 space-y-4">
        
        @if($isAdmin || $user?->isSuperAdmin())
            <!-- ========================================================================= -->
            <!-- DÀNH RIÊNG CHO QUẢN TRỊ VIÊN HỆ THỐNG (SUPERADMIN & ADMIN)                -->
            <!-- ẨN HOÀN TOÀN CÁC MỤC VẬN HÀNH PHÒNG TRỌ KHÔNG LIÊN QUAN                   -->
            <!-- ========================================================================= -->

            <!-- NHÓM 1: ĐIỀU HÀNH NỀN TẢNG -->
            <div class="sidebar-group">
                <div class="sidebar-group-title px-3 mb-1 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    Điều Hành Nền Tảng
                </div>
                <div class="sidebar-group-divider hidden my-2 border-t border-slate-800/60"></div>
                <div class="space-y-0.5">
                    @if($user?->isSuperAdmin())
                        <a href="{{ route('admin.superadmin.dashboard') }}" 
                           class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ (request()->routeIs('admin.superadmin.dashboard') || request()->routeIs('superadmin.dashboard.alias')) ? 'text-amber-400 bg-amber-500/10 border-amber-500/20' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                            <i class="fa-solid fa-crown w-5 text-center text-[15px] text-amber-400"></i>
                            <span class="truncate">Superadmin Console</span>
                        </a>

                        <a href="{{ route('admin.audit-logs') }}" 
                           class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ request()->routeIs('admin.audit-logs') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                            <i class="fa-solid fa-shield-halved w-5 text-center text-[15px] text-indigo-400"></i>
                            <span class="truncate">Nhật Ký Kiểm Toán</span>
                        </a>
                    @endif

                    <a href="{{ route('user.list') }}" 
                       class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ (request()->routeIs('user.list') || request()->routeIs('user.readUser') || request()->routeIs('user.updateUser') || request()->routeIs('user.createUser')) ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-users-gear w-5 text-center text-[15px]"></i>
                        <span class="truncate">Quản Trị Tài Khoản</span>
                    </a>

                    <a href="{{ route('admin.verifications.index') }}" 
                       class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ (request()->routeIs('admin.verifications.*') || request()->routeIs('admin.verification-audit-logs') || request()->routeIs('admin.settings')) ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-user-check w-5 text-center text-[15px]"></i>
                        <span class="truncate">Duyệt KYC Chủ Trọ</span>
                    </a>

                    <a href="{{ route('admin.analytics') }}" 
                       class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ request()->routeIs('admin.analytics') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-[15px]"></i>
                        <span class="truncate">Phân Tích & Báo Cáo Sàn</span>
                    </a>
                </div>
            </div>

        @else
            <!-- ========================================================================= -->
            <!-- DÀNH CHO CHỦ TRỌ, QUẢN LÝ TÒA NHÀ & NHÂN VIÊN VẬN HÀNH                    -->
            <!-- ========================================================================= -->

            <!-- NHÓM 1: TỔNG QUAN -->
            @if(!$isHousekeeper)
            <div class="sidebar-group">
                <div class="sidebar-group-title px-3 mb-1 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    Tổng Quan
                </div>
                <div class="sidebar-group-divider hidden my-2 border-t border-slate-800/60"></div>
                <div class="space-y-0.5">
                    <a href="{{ route('smartroom.admin') }}?tab=dashboard-section" 
                       data-section="dashboard-section" 
                       class="sidebar-nav-link w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ ($isDashboardRoute && $currentTab === 'dashboard-section') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-[15px]"></i>
                        <span class="truncate">Tổng Quan</span>
                    </a>
                    
                    @if($isLandlord)
                        <a href="{{ route('admin.reports.index') }}" 
                           class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ request()->routeIs('admin.reports.*') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                            <i class="fa-solid fa-chart-column w-5 text-center text-[15px]"></i>
                            <span class="truncate">Báo Cáo</span>
                        </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- NHÓM 2: QUẢN LÝ PHÒNG & TÒA NHÀ -->
            <div class="sidebar-group">
                <div class="sidebar-group-title px-3 mb-1 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    {{ $isHousekeeper ? 'Buồng Phòng' : 'Quản Lý Phòng' }}
                </div>
                <div class="sidebar-group-divider hidden my-2 border-t border-slate-800/60"></div>
                <div class="space-y-0.5">
                    @if(!$isHousekeeper)
                    <a href="{{ route('smartroom.admin') }}?tab=room-map-section" 
                       data-section="room-map-section" 
                       class="sidebar-nav-link w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ ($isDashboardRoute && $currentTab === 'room-map-section') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-cubes w-5 text-center text-[15px]"></i>
                        <span class="truncate">Sơ Đồ Phòng</span>
                    </a>
                    @endif

                    <a href="{{ route('smartroom.admin.housekeeping.matrix') }}" 
                       class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ request()->routeIs('smartroom.admin.housekeeping.*') ? 'text-teal-400 bg-teal-500/10 border-teal-500/20' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-broom-ball w-5 text-center text-[15px] text-teal-400"></i>
                        <span class="truncate">{{ $isHousekeeper ? 'Sơ Đồ Buồng Phòng' : 'Sơ Đồ Buồng Phòng & Lễ Tân' }}</span>
                        @if($sidebarDirtyRoomsCount > 0)
                            <span id="sidebar-housekeeping-badge" class="sidebar-badge ml-auto bg-rose-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-sm shadow-rose-500/30">
                                {{ $sidebarDirtyRoomsCount }}
                            </span>
                        @endif
                    </a>

                    @if(!$isHousekeeper)
                    <a href="{{ route('smartroom.admin') }}?tab=housekeeping-section" 
                       data-section="housekeeping-section" 
                       class="sidebar-nav-link w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ ($isDashboardRoute && $currentTab === 'housekeeping-section') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-paintbrush w-5 text-center text-[15px]"></i>
                        <span class="truncate">Nhiệm Vụ Dọn Phòng</span>
                    </a>
                    @endif
                    
                    @if($isLandlord)
                        <a href="{{ route('admin.buildings.index') }}" 
                           class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ request()->routeIs('admin.buildings.*') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                            <i class="fa-solid fa-city w-5 text-center text-[15px]"></i>
                            <span class="truncate">Cơ Sở Lưu Trú</span>
                        </a>
                    @endif

                    @if(!$isReceptionist && !$isHousekeeper)
                        <a href="{{ route('admin.rooms.index') }}" 
                           class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ request()->routeIs('admin.rooms.*') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                            <i class="fa-solid fa-door-open w-5 text-center text-[15px]"></i>
                            <span class="truncate">Cấu Hình Phòng</span>
                        </a>
                        
                        <a href="{{ route('admin.equipment.index') }}" 
                           class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ request()->routeIs('admin.equipment.*') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                            <i class="fa-solid fa-screwdriver-wrench w-5 text-center text-[15px]"></i>
                            <span class="truncate">Thiết Bị</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- NHÓM 3: KHÁCH THUÊ & HỢP ĐỒNG -->
            @if(!$isHousekeeper)
            <div class="sidebar-group">
                <div class="sidebar-group-title px-3 mb-1 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    Cư Dân & Khách Thuê
                </div>
                <div class="sidebar-group-divider hidden my-2 border-t border-slate-800/60"></div>
                <div class="space-y-0.5">
                    <a href="{{ route('smartroom.admin') }}?tab=contact-section" 
                       data-section="contact-section" 
                       class="sidebar-nav-link w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ ($isDashboardRoute && $currentTab === 'contact-section') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-phone-volume w-5 text-center text-[15px]"></i>
                        <span class="truncate">Yêu Cầu Tư Vấn</span>
                        @if($sidebarContactCount > 0)
                            <span class="sidebar-badge ml-auto bg-rose-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-sm shadow-rose-500/30">
                                {{ $sidebarContactCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('smartroom.admin') }}?tab=contract-section" 
                       data-section="contract-section" 
                       class="sidebar-nav-link w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ ($isDashboardRoute && $currentTab === 'contract-section') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-file-signature w-5 text-center text-[15px]"></i>
                        <span class="truncate">Hợp Đồng Online</span>
                    </a>

                    <a href="{{ route('smartroom.admin') }}?tab=resident-section" 
                       data-section="resident-section" 
                       class="sidebar-nav-link w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ ($isDashboardRoute && $currentTab === 'resident-section') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-users w-5 text-center text-[15px]"></i>
                        <span class="truncate">Quản Lý Cư Dân</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- NHÓM 4: DỊCH VỤ & TÀI CHÍNH -->
            @if(!$isHousekeeper)
            <div class="sidebar-group">
                <div class="sidebar-group-title px-3 mb-1 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    Vận Hành & Tài Chính
                </div>
                <div class="sidebar-group-divider hidden my-2 border-t border-slate-800/60"></div>
                <div class="space-y-0.5">
                    @if(!$isReceptionist && !$isHousekeeper)
                        <a href="{{ route('smartroom.admin') }}?tab=utility-section" 
                           data-section="utility-section" 
                           class="sidebar-nav-link w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ ($isDashboardRoute && $currentTab === 'utility-section') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                            <i class="fa-solid fa-bolt w-5 text-center text-[15px]"></i>
                            <span class="truncate">Chốt Điện Nước</span>
                        </a>
                    @endif

                    <a href="{{ route('smartroom.admin') }}?tab=ticket-section" 
                       data-section="ticket-section" 
                       class="sidebar-nav-link w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ ($isDashboardRoute && $currentTab === 'ticket-section') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-triangle-exclamation w-5 text-center text-[15px]"></i>
                        <span class="truncate">Sự Cố & Báo Hỏng</span>
                        <span id="sidebar-ticket-badge" class="sidebar-badge ml-auto bg-rose-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-sm shadow-rose-500/30 {{ $sidebarPendingTickets > 0 ? '' : 'hidden' }}">
                            {{ $sidebarPendingTickets }}
                        </span>
                    </a>

                    <a href="{{ route('admin.payments.index') }}" 
                       class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ request()->routeIs('admin.payments.*') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-money-check-dollar w-5 text-center text-[15px]"></i>
                        <span class="truncate">Thanh Toán</span>
                    </a>

                    @if($isLandlord)
                        <a href="{{ route('admin.activity_logs.index') }}" 
                           class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ request()->routeIs('admin.activity_logs.*') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                            <i class="fa-solid fa-clock-rotate-left w-5 text-center text-[15px]"></i>
                            <span class="truncate">Lịch Sử Vận Hành</span>
                        </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- NHÓM 5: TÀI KHOẢN & HỆ THỐNG -->
            @if(!$isHousekeeper)
            <div class="sidebar-group">
                <div class="sidebar-group-title px-3 mb-1 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    Hệ Thống
                </div>
                <div class="sidebar-group-divider hidden my-2 border-t border-slate-800/60"></div>
                <div class="space-y-0.5">
                    <a href="{{ route('smartroom.admin') }}?tab=profile-section" 
                       data-section="profile-section" 
                       class="sidebar-nav-link w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border {{ ($isDashboardRoute && $currentTab === 'profile-section') ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border-transparent hover:border-slate-800' }}">
                        <i class="fa-solid fa-address-card w-5 text-center text-[15px]"></i>
                        <span class="truncate">Hồ Sơ</span>
                    </a>
                </div>
            </div>
            @endif
        @endif
    </nav>

    <!-- Sidebar Footer (Cố định ở đáy) -->
    <div class="sidebar-footer p-3 border-t border-slate-900 shrink-0 bg-[#0d121f]">
        <div class="sidebar-user flex items-center gap-3 p-2 rounded-xl bg-slate-900/60 border border-slate-800/60">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-indigo-900/80 to-violet-900/80 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-300 text-xs shrink-0 shadow-sm">
                {{ $userInitials ?: 'AD' }}
            </div>
            <div class="sidebar-profile overflow-hidden min-w-0">
                <h4 class="text-xs font-bold text-slate-200 truncate leading-snug">{{ $userName ?: 'Quản Trị Viên' }}</h4>
                <p class="text-[10px] text-slate-400 truncate leading-tight">{{ $userRoleLabel }}</p>
            </div>
        </div>
        <a href="{{ route('signout') }}" class="sidebar-logout mt-2 w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/25 transition-all duration-200">
            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i> <span>Đăng Xuất (Thoát Admin)</span>
        </a>
    </div>
</aside>

<script>
(function () {
    const key = 'smartroom.sidebar.collapsed';

    function setCollapsed(collapsed) {
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        document.documentElement.classList.toggle('sidebar-collapsed', collapsed);
        try {
            localStorage.setItem(key, collapsed ? '1' : '0');
        } catch (e) {}
    }

    function initSidebarToggle() {
        const toggle = document.getElementById('sidebar-toggle');
        if (!toggle) return;

        try {
            if (localStorage.getItem(key) === '1') {
                setCollapsed(true);
            }
        } catch (e) {}

        if (!toggle.dataset.bound) {
            toggle.dataset.bound = 'true';
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const isCollapsed = document.body.classList.contains('sidebar-collapsed') ||
                                    document.documentElement.classList.contains('sidebar-collapsed');
                setCollapsed(!isCollapsed);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebarToggle);
    } else {
        initSidebarToggle();
    }
})();
</script>
