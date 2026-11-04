            <!-- SECTION 6: CONTACT REQUESTS -->
            <section id="contact-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Tổng số yêu cầu</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $contactRequestStats['total'] }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                                <i class="fa-solid fa-phone-volume text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(245,158,11,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-amber-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Chưa xử lý</p>
                                <h3 class="text-3xl font-extrabold text-amber-400 mt-2 tracking-tight">{{ $contactRequestStats['pending'] }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400">
                                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(99,102,241,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Đã liên hệ</p>
                                <h3 class="text-3xl font-extrabold text-indigo-400 mt-2 tracking-tight">{{ $contactRequestStats['processed'] }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                                <i class="fa-solid fa-square-check text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consultation Requests Table -->
                <div class="glass-card rounded-3xl border border-slate-900 overflow-hidden shadow-2xl">
                    <div class="p-6 border-b border-slate-900 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-200">Danh sách đăng ký tư vấn</h2>
                            <p class="text-xs text-slate-500 mt-1">Các yêu cầu xem phòng và đăng ký tư vấn từ khách thuê trên Renty Hub</p>
                        </div>
                        <div class="flex gap-2">
                            <form method="GET" action="{{ route('smartroom.admin', [], false) }}" class="flex gap-2">
                                <input type="hidden" name="tab" value="contact-section">
                                <input type="search" name="contact_q" value="{{ $contactFilters['q'] ?? '' }}" class="px-4 py-2 text-xs rounded-xl bg-slate-900 border border-slate-800 text-slate-200 placeholder-slate-500 focus:border-indigo-500 focus:outline-none" placeholder="Tên khách / SĐT / Phòng / Lời nhắn">
                                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 rounded-xl text-xs font-bold transition-all" title="Tìm kiếm">
                                    <i class="fa-solid fa-filter"></i>
                                </button>
                                @if(!empty($contactFilters['q']))
                                    <a href="{{ route('smartroom.admin', ['tab' => 'contact-section']) }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 rounded-xl text-xs font-bold transition-all" title="Xóa lọc">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-900">
                                <tr>
                                    <th class="px-6 py-4">Khách hàng</th>
                                    <th class="px-6 py-4">Số điện thoại</th>
                                    <th class="px-6 py-4">Phòng quan tâm</th>
                                    <th class="px-6 py-4">Lịch hẹn xem</th>
                                    <th class="px-6 py-4">Lời nhắn</th>
                                    <th class="px-6 py-4">Ngày đăng ký</th>
                                    <th class="px-6 py-4">Trạng thái</th>
                                    <th class="px-6 py-4 text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900">
                                @forelse($contactRequests as $req)
                                <tr class="hover:bg-slate-900/40 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-250">{{ $req->name }}</td>
                                    <td class="px-6 py-4 font-mono text-xs text-indigo-300">{{ $req->phone }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-900 border border-slate-800 text-slate-300">
                                            Phòng {{ $req->room->room_number }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-semibold">
                                        @if($req->appointment_time)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">
                                                <i class="fa-regular fa-calendar-check text-[10px]"></i>
                                                {{ \Carbon\Carbon::parse($req->appointment_time)->format('H:i d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-slate-500 italic">Tư vấn trực tiếp</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs max-w-xs truncate" title="{{ $req->message }}">
                                        {{ $req->message ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4">
                                        @if($req->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                Chờ xử lý
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                Đã liên hệ
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="tel:{{ $req->phone }}" class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all text-xs" title="Gọi điện">
                                                <i class="fa-solid fa-phone"></i>
                                            </a>
                                            <a href="https://zalo.me/{{ $req->phone }}" target="_blank" class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all text-xs" title="Chat Zalo">
                                                <i class="fa-solid fa-comment-sms"></i>
                                            </a>
                                            <form action="{{ route('smartroom.admin.contact_request.status', $req->id, false) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $req->status === 'pending' ? 'processed' : 'pending' }}">
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-all text-xs" title="Đổi trạng thái">
                                                    <i class="fa-solid {{ $req->status === 'pending' ? 'fa-check' : 'fa-rotate-left' }}"></i>
                                                </button>
                                            </form>
                                            @if($isLandlord)
                                                <form action="{{ route('smartroom.admin.contact_request.delete', $req->id, false) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa yêu cầu tư vấn này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all text-xs" title="Xóa">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-xs text-slate-500">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <i class="fa-solid fa-phone-slash text-2xl text-slate-700"></i>
                                            <span>Chưa có yêu cầu tư vấn hay đăng ký xem phòng nào.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($contactRequests->hasPages())
                        <div class="mt-6 flex justify-center pb-2">
                            {{ $contactRequests->appends(array_merge(request()->query(), ['tab' => 'contact-section']))->links() }}
                        </div>
                    @endif
                </div>
            </section>

