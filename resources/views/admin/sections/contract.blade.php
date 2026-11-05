            <!-- SECTION 5: CONTRACT MANAGEMENT -->
            <section id="contract-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- Premium Stat Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(99,102,241,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Tổng số hợp đồng</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $contractStats['total'] }}</h3>
                                <span class="text-[10px] text-indigo-400 font-semibold mt-1 block">Tất cả bản ghi</span>
                            </div>
                            <span class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-file-contract text-lg"></i>
                            </span>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Hợp đồng hiệu lực</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $contractStats['active'] }}</h3>
                                <span class="text-[10px] text-emerald-400 font-semibold mt-1 block">Đã có chữ ký</span>
                            </div>
                            <span class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-circle-check text-lg"></i>
                            </span>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(245,158,11,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-amber-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Chờ cư dân ký</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $contractStats['pending'] }}</h3>
                                <span class="text-[10px] text-amber-400 font-semibold mt-1 block">Yêu cầu chữ ký</span>
                            </div>
                            <span class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-signature text-lg"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Contract List Glass Card -->
                <div class="glass-card rounded-2xl p-6 border border-slate-800/40 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-file-invoice-dollar text-indigo-400"></i> Danh Sách Hợp Đồng Online
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Khởi tạo hợp đồng thuê nhà điện tử và theo dõi trạng thái ký trực tuyến.</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <form method="GET" action="{{ route('smartroom.admin', [], false) }}" class="flex gap-2">
                                <input type="hidden" name="tab" value="contract-section">
                                <input type="search" name="contract_q" value="{{ $contractFilters['q'] ?? '' }}" class="w-64 sm:w-80 md:w-96 px-4 py-2 text-xs rounded-xl bg-slate-900 border border-slate-800 text-slate-200 placeholder-slate-500 focus:border-indigo-500 focus:outline-none transition-all shadow-inner" placeholder="Mã HĐ / Phòng / Cư dân / SĐT">
                                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 rounded-xl text-xs font-bold transition-all shrink-0" title="Tìm kiếm">
                                    <i class="fa-solid fa-filter"></i>
                                </button>
                                @if(!empty($contractFilters['q']))
                                    <a href="{{ route('smartroom.admin', ['tab' => 'contract-section']) }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 rounded-xl text-xs font-bold transition-all shrink-0" title="Xóa lọc">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                @endif
                            </form>
                            <button onclick="toggleAddContractModal(true)" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-indigo-600/25 hover:-translate-y-0.5 shrink-0">
                                <i class="fa-solid fa-plus-circle"></i> Tạo Hợp Đồng Mới
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-900">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/80 border-b border-slate-900">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider">Mã Hợp Đồng</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Phòng Trọ</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Cư Dân Đại Diện</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Tiền Cọc</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Thời Hạn Thuê</th>
                                    <th class="px-6 py-4 font-bold text-center tracking-wider">Trạng Thái</th>
                                    <th class="px-6 py-4 font-bold text-center tracking-wider">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                                @forelse($contracts as $c)
                                <tr class="hover:bg-slate-900/40 transition-all group">
                                    <td class="px-6 py-4 font-bold text-slate-200 group-hover:text-indigo-400 transition-colors">
                                        <div>{{ $c->contract_code }}</div>
                                        <div class="mt-1">
                                            @if(($c->signing_method ?? 'online') === 'offline')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                    <i class="fa-solid fa-file-signature text-[9px]"></i> Ký trực tiếp (Giấy)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                                    <i class="fa-solid fa-globe text-[9px]"></i> Ký số Online
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded bg-indigo-500/10 text-indigo-400 text-xs font-bold border border-indigo-500/20">
                                            Phòng {{ $c->room ? $c->room->room_number : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-200 text-xs">{{ $c->resident ? $c->resident->name : 'N/A' }}</div>
                                        <div class="text-[10px] text-slate-500 mt-0.5"><i class="fa-solid fa-phone text-[8px] mr-1"></i>{{ $c->resident ? $c->resident->phone : '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-bold text-emerald-400">{{ number_format($c->deposit) }}đ</td>
                                    <td class="px-6 py-4 text-xs text-slate-400">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-calendar-alt text-[10px] text-slate-500"></i>
                                            <span>{{ date('d/m/Y', strtotime($c->start_date)) }} - {{ date('d/m/Y', strtotime($c->end_date)) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($c->status === 'active')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                Đã ký hiệu lực
                                            </span>
                                        @else
                                            @if(($c->signing_method ?? 'online') === 'offline')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                    Chờ ký giấy
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                                                    Chờ ký online
                                                </span>
                                            @endif
                                        @endif

                                        @if($c->renewal_status === 'requested')
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 animate-pulse" title="Ghi chú cư dân: {{ $c->renewal_note }}">
                                                    Cần gia hạn ({{ $c->renewal_months }} thg)
                                                </span>
                                            </div>
                                        @elseif($c->renewal_status === 'approved')
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                                                    Đã duyệt gia hạn
                                                </span>
                                            </div>
                                        @elseif($c->renewal_status === 'declined')
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-500/10 text-rose-300 border border-rose-500/20">
                                                    Đã từ chối gia hạn
                                                </span>
                                            </div>
                                        @elseif($c->renewal_status === 'renewed')
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                                    Đã gia hạn/tái ký
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            @if($c->status === 'pending')
                                                @if(($c->signing_method ?? 'online') === 'offline')
                                                    <button type="button" onclick="openConfirmOfflineSignModal({{ $c->toJson() }})" class="px-3 py-2 bg-amber-600/20 hover:bg-amber-600 text-amber-300 hover:text-white rounded-xl text-xs font-bold border border-amber-500/30 transition-all flex items-center gap-1.5 shadow-sm">
                                                        <i class="fa-solid fa-check-double text-amber-400"></i> Xác nhận đã ký
                                                    </button>
                                                    <a href="{{ route('smartroom.contract.pdf', $c->id) }}" target="_blank" class="px-3 py-2 bg-cyan-600/20 hover:bg-cyan-600 text-cyan-300 hover:text-white rounded-xl text-xs font-bold border border-cyan-500/25 transition-all flex items-center gap-1.5">
                                                        <i class="fa-solid fa-print"></i> In ký giấy (PDF)
                                                    </a>
                                                @else
                                                    <button onclick="copySignLink('{{ route('smartroom.contract.sign_view', $c->id, false) }}', this)" class="px-3 py-2 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-400 hover:text-white rounded-xl text-xs font-bold border border-indigo-500/20 transition-all flex items-center gap-1.5">
                                                        <i class="fa-solid fa-link"></i> Link ký
                                                    </button>
                                                    <button onclick="openSendMsgModal('{{ $c->resident ? $c->resident->phone : '' }}', '{{ $c->resident ? $c->resident->name : '' }}', 'Kính gửi anh/chị {{ $c->resident ? $c->resident->name : '' }}, vui lòng truy cập đường link sau để hoàn tất ký kết hợp đồng thuê phòng {{ $c->room ? $c->room->room_number : '' }}: ' + window.location.origin + '{{ route('smartroom.contract.sign_view', $c->id, false) }}', 'contract')" class="px-3 py-2 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white rounded-xl text-xs font-bold border border-emerald-500/20 transition-all flex items-center gap-1.5">
                                                        <i class="fa-solid fa-paper-plane"></i> Gửi link ký
                                                    </button>
                                                @endif
                                            @endif
                                            
                                            @if($c->status === 'active' || $c->renewal_status === 'requested')
                                                <button onclick="openRenewContractModal({{ $c->toJson() }}, {{ $c->resident ? $c->resident->toJson() : 'null' }}, {{ $c->room ? $c->room->toJson() : 'null' }})" class="px-3 py-2 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white rounded-xl text-xs font-bold border border-emerald-500/20 transition-all flex items-center gap-1.5">
                                                    <i class="fa-solid fa-clock-rotate-left"></i> Gia hạn / Tái ký
                                                </button>
                                            @endif

                                            <a href="{{ route('smartroom.contract.sign_view', $c->id) }}" target="_blank" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold border border-slate-750 transition-all flex items-center gap-1.5">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem HĐ
                                            </a>
                                            <a href="{{ route('smartroom.contract.pdf', $c->id) }}" target="_blank" class="px-3 py-2 bg-cyan-600/20 hover:bg-cyan-600 text-cyan-300 hover:text-white rounded-xl text-xs font-bold border border-cyan-500/25 transition-all flex items-center gap-1.5">
                                                <i class="fa-solid fa-file-pdf"></i> In/PDF
                                            </a>

                                            @if($c->offline_contract_scan)
                                                <a href="/storage/{{ ltrim($c->offline_contract_scan, '/') }}" target="_blank" class="px-3 py-2 bg-amber-500/10 hover:bg-amber-500 text-amber-300 hover:text-white rounded-xl text-xs font-bold border border-amber-500/20 transition-all flex items-center gap-1.5" title="Xem bản scan/ảnh chụp hợp đồng giấy đã ký">
                                                    <i class="fa-solid fa-paperclip"></i> Bản scan
                                                </a>
                                            @endif

                                            @if($isLandlord)
                                                <form action="{{ route('smartroom.admin.contract.delete', $c->id, false) }}" method="POST" onsubmit="return confirmDelete(event, this, 'Bạn có chắc chắn muốn xóa hợp đồng này không?', 'Xóa Hợp Đồng')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-2 bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white rounded-xl text-xs font-bold border border-rose-500/25 transition-all">
                                                        <i class="fa-solid fa-trash-can"></i> Xóa
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-500">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <i class="fa-solid fa-folder-open text-2xl text-slate-700"></i>
                                            <span>Không tìm thấy hợp đồng nào. Hãy tạo hợp đồng mới!</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($contracts->hasPages())
                        <div class="mt-6 flex justify-center pb-2">
                            {{ $contracts->appends(array_merge(request()->query(), ['tab' => 'contract-section']))->links() }}
                        </div>
                    @endif
                </div>
            </section>

