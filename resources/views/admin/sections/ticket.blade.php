            <!-- SECTION TICKET: QUẢN LÝ SỰ CỐ & BÁO HỎNG -->
            <section id="ticket-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(99,102,241,0.15)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Tổng số sự cố</p>
                                <h3 id="ticket-stat-total" class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $ticketStats['total'] }}</h3>
                                <span class="text-[11px] text-slate-500 mt-1 block">Tất cả báo hỏng đã ghi nhận</span>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(244,63,94,0.15)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-rose-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Chờ xử lý (Mới)</p>
                                <h3 id="ticket-stat-pending" class="text-3xl font-extrabold text-rose-400 mt-2 tracking-tight">{{ $ticketStats['pending'] }}</h3>
                                <span class="text-[11px] text-rose-400/80 mt-1 block font-medium">Cần điều phối thợ sửa</span>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-400">
                                <i class="fa-solid fa-hourglass-start text-xl animate-pulse"></i>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(245,158,11,0.15)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-amber-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Đang khắc phục</p>
                                <h3 id="ticket-stat-processing" class="text-3xl font-extrabold text-amber-400 mt-2 tracking-tight">{{ $ticketStats['processing'] }}</h3>
                                <span class="text-[11px] text-amber-400/80 mt-1 block font-medium">Thợ đang kiểm tra & sửa</span>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400">
                                <i class="fa-solid fa-screwdriver-wrench text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(16,185,129,0.15)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Đã hoàn thành</p>
                                <h3 id="ticket-stat-resolved" class="text-3xl font-extrabold text-emerald-400 mt-2 tracking-tight">{{ $ticketStats['resolved'] }}</h3>
                                <span class="text-[11px] text-emerald-400/80 mt-1 block font-medium">Đã giải quyết dứt điểm</span>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                                <i class="fa-solid fa-circle-check text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tickets Main Table Panel -->
                <div class="glass-card rounded-3xl border border-slate-900 overflow-hidden shadow-2xl">
                    <div class="p-6 border-b border-slate-900 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <h2 class="text-lg font-bold text-slate-200">Danh sách báo hỏng & sự cố</h2>
                                <span id="ticket-count-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                    {{ count($tickets) }} sự cố
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Theo dõi chi tiết số phòng, vị trí hư hỏng, ảnh chụp thực tế và phân công thợ sửa chữa</p>
                        </div>

                        <!-- Filter Controls -->
                        <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
                            <!-- Status Filter -->
                            <select id="ticket-filter-status" onchange="filterAdminTickets()" class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-indigo-500">
                                <option value="all">Tất cả trạng thái</option>
                                <option value="pending">Chờ xử lý</option>
                                <option value="processing">Đang khắc phục</option>
                                <option value="resolved">Đã hoàn thành</option>
                            </select>

                            <!-- Category Filter -->
                            <select id="ticket-filter-category" onchange="filterAdminTickets()" class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-indigo-500">
                                <option value="all">Tất cả danh mục</option>
                                <option value="electric">Điện</option>
                                <option value="water">Nước</option>
                                <option value="furniture">Nội thất</option>
                                <option value="maintenance">Bảo trì</option>
                                <option value="housekeeping">Dọn phòng (Housekeeping)</option>
                                <option value="other">Khác</option>
                            </select>

                            <!-- Search Input -->
                            <div class="relative flex-1 sm:w-80">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                                    <i class="fa-solid fa-magnifying-glass text-xs leading-none"></i>
                                </div>
                                <input type="text" id="ticket-search-input" oninput="filterAdminTickets()" placeholder="Tìm phòng, vị trí, cư dân..." class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-3 py-2 text-xs leading-normal text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all shadow-inner">
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-900">
                                <tr>
                                    <th class="px-6 py-4">Mã & Ngày</th>
                                    <th class="px-6 py-4">Phòng & Vị trí</th>
                                    <th class="px-6 py-4">Cư dân báo</th>
                                    <th class="px-6 py-4">Nội dung & Hình ảnh</th>
                                    <th class="px-6 py-4">Phụ trách</th>
                                    <th class="px-6 py-4">Trạng thái</th>
                                    <th class="px-6 py-4 text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="admin-ticket-table-body" class="divide-y divide-slate-900">
                                @forelse($tickets as $ticket)
                                    @php
                                        $categoryLabels = [
                                            'electric' => ['Điện', 'bg-amber-500/10 text-amber-300 border-amber-500/20', 'fa-bolt'],
                                            'water' => ['Nước', 'bg-cyan-500/10 text-cyan-300 border-cyan-500/20', 'fa-droplet'],
                                            'furniture' => ['Nội thất', 'bg-purple-500/10 text-purple-300 border-purple-500/20', 'fa-couch'],
                                            'maintenance' => ['Bảo trì', 'bg-blue-500/10 text-blue-300 border-blue-500/20', 'fa-wrench'],
                                            'housekeeping' => ['Dọn phòng', 'bg-teal-500/10 text-teal-300 border-teal-500/20', 'fa-broom'],
                                            'other' => ['Khác', 'bg-slate-500/10 text-slate-300 border-slate-500/20', 'fa-circle-exclamation'],
                                        ];
                                        $catInfo = $categoryLabels[$ticket->category] ?? [$ticket->category, 'bg-slate-500/10 text-slate-300 border-slate-500/20', 'fa-info-circle'];

                                        $statusClass = match($ticket->status) {
                                            'pending' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                            'processing' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                            'resolved' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                            default => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                        };
                                        $statusText = match($ticket->status) {
                                            'pending' => 'Chờ tiếp nhận',
                                            'processing' => 'Đang xử lý',
                                            'resolved' => 'Đã hoàn thành',
                                            default => $ticket->status,
                                        };
                                    @endphp
                                    <tr class="ticket-row hover:bg-slate-900/40 transition-colors"
                                        data-ticket-id="{{ $ticket->id }}"
                                        data-ticket-status="{{ $ticket->status }}"
                                        data-ticket-category="{{ $ticket->category }}"
                                        data-ticket-search="{{ mb_strtolower(($ticket->room->room_number ?? '') . ' ' . ($ticket->specific_location ?? '') . ' ' . ($ticket->resident->name ?? '') . ' ' . ($ticket->resident->phone ?? '') . ' ' . $ticket->title . ' ' . ($ticket->room->building->name ?? '')) }}">
                                        
                                        <!-- ID & Date -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono text-xs font-bold text-slate-400">#{{ $ticket->id }}</span>
                                            <div class="text-[11px] text-slate-500 mt-0.5">
                                                <i class="fa-regular fa-clock mr-1"></i>{{ $ticket->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </td>

                                        <!-- Room & Location -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-xs font-black text-indigo-400">
                                                    P.{{ $ticket->room->room_number ?? 'N/A' }}
                                                </span>
                                                @if(optional($ticket->room)->floor)
                                                    <span class="text-[11px] text-slate-500">Tầng {{ $ticket->room->floor }}</span>
                                                @endif
                                                @if(optional($ticket->room)->building)
                                                    <span class="text-[10px] text-slate-500">({{ $ticket->room->building->name }})</span>
                                                @endif
                                            </div>
                                            <div class="mt-1.5 flex items-center gap-1.5">
                                                @if($ticket->specific_location)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-900 border border-slate-800 text-[11px] font-semibold text-rose-300">
                                                        <i class="fa-solid fa-location-dot text-rose-400 text-[10px]"></i>
                                                        <span>{{ $ticket->specific_location }}</span>
                                                    </span>
                                                @else
                                                    <span class="inline-block px-2 py-0.5 rounded-md bg-slate-900/50 text-[11px] text-slate-500 italic">
                                                        Toàn bộ phòng
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Resident Info -->
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-slate-200 text-xs">
                                                {{ $ticket->resident->name ?? 'Cư dân hệ thống' }}
                                            </div>
                                            @if(optional($ticket->resident)->phone)
                                                <a href="tel:{{ $ticket->resident->phone }}" class="inline-flex items-center gap-1 text-[11px] text-indigo-400 hover:text-indigo-300 font-mono mt-0.5">
                                                    <i class="fa-solid fa-phone text-[9px]"></i> {{ $ticket->resident->phone }}
                                                </a>
                                            @endif
                                        </td>

                                        <!-- Content & Image -->
                                        <td class="px-6 py-4 max-w-xs">
                                            <div class="flex items-center gap-1.5 mb-1">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold border {{ $catInfo[1] }}">
                                                    <i class="fa-solid {{ $catInfo[2] }}"></i> {{ $catInfo[0] }}
                                                </span>
                                            </div>
                                            <div class="font-bold text-xs text-slate-200 line-clamp-1">{{ $ticket->title }}</div>
                                            <div class="text-[11px] text-slate-400 line-clamp-2 mt-0.5 leading-relaxed">{{ $ticket->description }}</div>
                                            @if($ticket->image_path)
                                                <div class="mt-2 flex items-center gap-2">
                                                    <img src="{{ $ticket->image_path }}" onclick="viewTicketImage('{{ $ticket->image_path }}')" alt="Ảnh lỗi" class="w-10 h-10 object-cover rounded-lg border border-slate-700 hover:border-indigo-500 cursor-pointer transition-all hover:scale-105 shadow">
                                                    <button type="button" onclick="viewTicketImage('{{ $ticket->image_path }}')" class="text-[11px] text-indigo-400 hover:underline flex items-center gap-1">
                                                        <i class="fa-regular fa-image"></i> Xem ảnh
                                                    </button>
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Assigned Technician -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($ticket->assigned_to)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-300 font-medium">
                                                    <i class="fa-solid fa-user-gear text-indigo-400"></i> {{ $ticket->assigned_to }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-500 italic">Chưa chỉ định</span>
                                            @endif
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide border {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <button type="button" 
                                                    onclick="openTicketUpdateModal({
                                                        id: {{ $ticket->id }},
                                                        room: '{{ $ticket->room->room_number ?? 'N/A' }}',
                                                        location: '{{ addslashes($ticket->specific_location ?? 'Toàn phòng') }}',
                                                        title: '{{ addslashes($ticket->title) }}',
                                                        resident: '{{ addslashes($ticket->resident->name ?? 'Cư dân') }}',
                                                        status: '{{ $ticket->status }}',
                                                        assigned_to: '{{ addslashes($ticket->assigned_to ?? '') }}',
                                                        image_path: '{{ $ticket->image_path ?? '' }}'
                                                    })" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600/10 hover:bg-indigo-600/20 border border-indigo-500/20 text-indigo-400 text-xs font-bold transition-all">
                                                <i class="fa-solid fa-pen-to-square"></i> Cập nhật
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="ticket-empty-row">
                                        <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-500">
                                            <div class="flex flex-col items-center justify-center gap-3">
                                                <i class="fa-solid fa-clipboard-check text-3xl text-slate-700"></i>
                                                <span class="text-slate-400 font-medium">Không có sự cố nào cần xử lý.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr id="ticket-no-search-match" class="hidden">
                                    <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-500">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <i class="fa-solid fa-filter-circle-xmark text-3xl text-slate-700"></i>
                                            <span class="text-slate-400 font-medium">Không tìm thấy sự cố nào phù hợp với bộ lọc hiện tại.</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Ticket Pagination Bar -->
                    <div id="ticket-pagination-container" class="px-6 py-4 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-950/40">
                        <div class="flex items-center gap-3 text-xs text-slate-400">
                            <span>Hiển thị <span id="ticket-page-start" class="font-bold text-slate-200">1</span> - <span id="ticket-page-end" class="font-bold text-slate-200">10</span> trên tổng số <span id="ticket-page-total" class="font-bold text-indigo-400">{{ count($tickets) }}</span> sự cố</span>
                            <div class="flex items-center gap-1.5 ml-2">
                                <span class="text-slate-500 text-[11px]">Mỗi trang:</span>
                                <select id="ticket-page-size" onchange="changeTicketPageSize(this.value)" class="bg-slate-900 border border-slate-800 rounded-lg px-2 py-1 text-xs text-slate-300 focus:outline-none focus:border-indigo-500">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <button type="button" id="ticket-prev-page" onclick="changeTicketPage(currentTicketPage - 1)" class="px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900/80 hover:bg-slate-800 text-slate-300 disabled:opacity-30 disabled:cursor-not-allowed text-xs font-semibold flex items-center gap-1 transition">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i> Trước
                            </button>
                            <div id="ticket-pagination-numbers" class="flex items-center gap-1"></div>
                            <button type="button" id="ticket-next-page" onclick="changeTicketPage(currentTicketPage + 1)" class="px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900/80 hover:bg-slate-800 text-slate-300 disabled:opacity-30 disabled:cursor-not-allowed text-xs font-semibold flex items-center gap-1 transition">
                                Sau <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

