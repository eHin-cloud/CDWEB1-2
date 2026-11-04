            <!-- SECTION 4: RESIDENT MANAGEMENT (Quản lý khách trọ) -->
            <section id="resident-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- Stat Cards cho Trạng thái tạm trú -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(99,102,241,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Tổng cư dân</p>
                                <h3 class="text-2xl font-extrabold text-white mt-1">{{ $residentStats->count() }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400"><i class="fa-solid fa-users"></i></div>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Đã đăng ký tạm trú</p>
                                <h3 class="text-2xl font-extrabold text-emerald-400 mt-1">{{ $residentStats->where('temporary_residence_status', 'registered')->count() }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400"><i class="fa-solid fa-clipboard-check"></i></div>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(245,158,11,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-amber-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Tạm vắng</p>
                                <h3 class="text-2xl font-extrabold text-amber-400 mt-1">{{ $residentStats->where('temporary_residence_status', 'absent')->count() }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400"><i class="fa-solid fa-user-clock"></i></div>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(239,68,68,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-rose-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Chưa đăng ký</p>
                                <h3 class="text-2xl font-extrabold text-rose-400 mt-1">{{ $residentStats->where('temporary_residence_status', 'none')->count() }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-400"><i class="fa-solid fa-user-xmark"></i></div>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200">Quản Lý Danh Sách Cư Dân</h3>
                            <p class="text-xs text-slate-500">Lưu trữ thông tin cá nhân, đăng ký tạm trú, quản lý người thân tạm trú.</p>
                        </div>
                        <div class="flex gap-2">
                            <form method="GET" action="{{ route('smartroom.admin', [], false) }}" class="flex gap-2">
                                <input type="hidden" name="tab" value="resident-section">
                                <input type="search" name="resident_q" value="{{ $residentFilters['q'] }}" class="px-4 py-2 text-xs rounded-xl bg-slate-900 border border-slate-800 text-slate-200 placeholder-slate-500 focus:border-indigo-500 focus:outline-none" placeholder="Ten / SDT / CCCD">
                                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 rounded-xl text-xs font-bold transition-all">
                                    <i class="fa-solid fa-filter"></i>
                                </button>
                                @if($residentFilters['q'] !== '')
                                    <a href="{{ route('smartroom.admin', ['tab' => 'resident-section']) }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 rounded-xl text-xs font-bold transition-all">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                @endif
                            </form>
                            <button onclick="toggleAddResidentModal(true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                                <i class="fa-solid fa-plus"></i> Thêm Cư Dân
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="tblResidents" class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/50 border-b border-slate-900">
                                <tr>
                                    <th class="px-4 py-4 font-bold">Họ tên</th>
                                    <th class="px-4 py-4 font-bold">Phòng</th>
                                    <th class="px-4 py-4 font-bold">SĐT</th>
                                    <th class="px-4 py-4 font-bold">CCCD</th>
                                    <th class="px-4 py-4 font-bold">Quê quán</th>
                                    <th class="px-4 py-4 font-bold">Tạm trú</th>
                                    <th class="px-4 py-4 font-bold">Ngày vào ở</th>
                                    <th class="px-4 py-4 font-bold text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900" id="resident-table-body">
                                @foreach($residents as $resident)
                                <tr class="hover:bg-slate-900/30 transition-all">
                                    <td class="px-4 py-4 font-bold text-slate-200">{{ $resident->name }}</td>
                                    <td class="px-4 py-4 text-xs font-semibold text-indigo-400">P. {{ $resident->room ? $resident->room->room_number : 'N/A' }}</td>
                                    <td class="px-4 py-4 text-xs text-slate-400 font-mono">{{ $resident->phone }}</td>
                                    <td class="px-4 py-4 text-xs text-slate-400 font-mono">{{ $resident->cccd ?? '—' }}</td>
                                    <td class="px-4 py-4 text-xs text-slate-500 max-w-[120px] truncate" title="{{ $resident->hometown }}">{{ $resident->hometown ?? '—' }}</td>
                                    <td class="px-4 py-4">
                                        @if($resident->temporary_residence_status === 'registered')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Đã đăng ký
                                            </span>
                                        @elseif($resident->temporary_residence_status === 'absent')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Tạm vắng
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Chưa ĐK
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-xs text-slate-500">{{ \Carbon\Carbon::parse($resident->start_date)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex gap-1.5 justify-center flex-wrap">
                                            {{-- Nút Xem chi tiết --}}
                                            <button onclick="openViewResidentModal({{ $resident->id }}, '{{ addslashes($resident->name) }}', '{{ $resident->phone }}', '{{ $resident->email }}', '{{ $resident->room ? $resident->room->room_number : 'N/A' }}', '{{ \Carbon\Carbon::parse($resident->start_date)->format('d/m/Y') }}', '{{ $resident->status === 'active' ? 'Đang hoạt động' : 'Tạm ngưng' }}', '{{ $resident->dob }}', '{{ $resident->cccd }}', '{{ addslashes($resident->hometown) }}', '{{ $resident->temporary_residence_status }}')" class="px-2 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-[10px] font-bold text-emerald-400 transition-all" title="Xem chi tiết">
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                            {{-- Nút Sửa thông tin --}}
                                            <button onclick="openEditResidentModal('{{ $resident->id }}', '{{ addslashes($resident->name) }}', '{{ $resident->phone }}', '{{ $resident->room_id }}', '{{ $resident->start_date }}', '{{ $resident->dob }}', '{{ $resident->cccd }}', '{{ addslashes($resident->hometown) }}', '{{ $resident->temporary_residence_status }}', '{{ $resident->version }}')" class="px-2 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-[10px] font-bold text-indigo-400 transition-all" title="Sửa thông tin">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </button>
                                            {{-- Nút Quản lý người thân --}}
                                            <button onclick="openRelativesModal({{ $resident->id }}, '{{ addslashes($resident->name) }}')" class="px-2 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-[10px] font-bold text-cyan-400 transition-all" title="Quản lý người thân tạm trú">
                                                <i class="fa-solid fa-people-roof"></i>
                                            </button>
                                            @if($isLandlord)
                                                {{-- Nút Xóa cư dân - có chống spam click (disabled sau click) --}}
                                                <form action="{{ route('smartroom.admin.resident.delete', $resident->id, false) }}" method="POST" onsubmit="return confirmAndDisable(this, 'Bạn có chắc chắn muốn xóa cư dân này ra khỏi phòng trọ?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="anti-spam-btn px-2 py-1.5 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 rounded-lg text-[10px] font-bold text-rose-400 transition-all" title="Xóa cư dân">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($residents->hasPages())
                        <div class="mt-6 flex justify-center pb-2">
                            {{ $residents->appends(array_merge(request()->query(), ['tab' => 'resident-section']))->links() }}
                        </div>
                    @endif
                </div>
            </section>

