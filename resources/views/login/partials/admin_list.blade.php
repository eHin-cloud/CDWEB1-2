


<main class="container mx-auto px-6 py-12 flex-grow relative z-10">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Quản Trị Hệ Thống</h1>
                <p class="text-xs text-slate-400 mt-1">Danh sách người dùng và phân quyền quản lý trong hệ thống SmartRoom</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.verifications.index') }}" class="px-4 py-2.5 bg-sky-600/90 hover:bg-sky-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-sky-500/20 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved"></i> Giám Sát & Bảo Mật
                </a>
                <a href="{{ route('user.createUser') }}" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-emerald-500/20 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-1.5">
                    <i class="fa-solid fa-user-plus"></i> Thêm Admin Mới
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
            <div class="bg-slate-900/30 backdrop-blur-md border border-slate-800/80 rounded-2xl p-5 flex items-center justify-between shadow-md">
                <div>
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Tổng thành viên</span>
                    <h3 class="text-2xl font-black text-slate-100 mt-1">{{ number_format($stats['total'] ?? $users->total()) }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-users text-lg"></i>
                </div>
            </div>
            <div class="bg-slate-900/30 backdrop-blur-md border border-slate-800/80 rounded-2xl p-5 flex items-center justify-between shadow-md">
                <div>
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Quản trị viên</span>
                    <h3 class="text-2xl font-black text-rose-400 mt-1">{{ number_format($stats['admin'] ?? 0) }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-user-shield text-lg"></i>
                </div>
            </div>
            <div class="bg-slate-900/30 backdrop-blur-md border border-slate-800/80 rounded-2xl p-5 flex items-center justify-between shadow-md">
                <div>
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Chủ nhà trọ</span>
                    <h3 class="text-2xl font-black text-sky-400 mt-1">{{ number_format($stats['landlord'] ?? 0) }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-house-user text-lg"></i>
                </div>
            </div>
            <div class="bg-slate-900/30 backdrop-blur-md border border-slate-800/80 rounded-2xl p-5 flex items-center justify-between shadow-md">
                <div>
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Cư dân thuê trọ</span>
                    <h3 class="text-2xl font-black text-emerald-400 mt-1">{{ number_format($stats['resident'] ?? 0) }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-door-closed text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Filters & Search Form -->
        <form method="GET" action="{{ route('user.list', [], false) }}" class="flex flex-col md:flex-row gap-3 justify-between items-center mb-6">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" 
                       name="search" 
                       id="user-search" 
                       value="{{ request('search') }}" 
                       onkeyup="filterUserList()" 
                       placeholder="Tìm tên, username, email, SĐT..." 
                       class="w-full bg-slate-950/60 border border-slate-800 hover:border-slate-700/60 focus:border-indigo-500 rounded-xl pl-10 pr-4 py-2.5 text-xs focus:outline-none transition-all text-slate-200 placeholder-slate-500">
            </div>
            <div class="flex flex-wrap gap-2 w-full md:w-auto justify-end items-center">
                <select name="role" id="role-filter" onchange="this.form.submit()" class="bg-slate-950/60 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-semibold focus:outline-none focus:border-indigo-500 transition-all text-slate-300">
                    <option value="all">-- Tất cả vai trò --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->slug }}" {{ request('role') === $r->slug ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
                <select name="status" id="status-filter" onchange="this.form.submit()" class="bg-slate-950/60 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-semibold focus:outline-none focus:border-indigo-500 transition-all text-slate-300">
                    <option value="all">-- Tất cả trạng thái --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>Đã khóa</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                </select>
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-indigo-500/20 flex items-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Lọc
                </button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('user.list') }}" class="px-3 py-2.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-white rounded-xl text-xs font-semibold transition-all" title="Làm mới bộ lọc">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- Table Content -->
        <div class="bg-slate-900/35 backdrop-blur-xl border border-slate-800 rounded-3xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.35)]">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="users-table">
                    <thead>
                        <tr class="border-b border-slate-800 bg-slate-900/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-5 py-4">ID & Ngày Tạo</th>
                            <th class="px-5 py-4">Thành Viên</th>
                            <th class="px-5 py-4">Liên Hệ</th>
                            <th class="px-5 py-4">Vai Trò &amp; Đối Tác</th>
                            <th class="px-5 py-4 text-center">Tình Trạng</th>
                            <th class="px-5 py-4">Ghi Chú</th>
                            <th class="px-5 py-4 text-center">Phân Quyền</th>
                            <th class="px-5 py-4 text-right">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 text-slate-200">
                        @forelse($users as $userItem)
                            <tr class="hover:bg-slate-800/15 transition-colors user-row {{ $userItem->id === auth()->id() ? 'bg-amber-500/5' : '' }}"
                                data-name="{{ strtolower($userItem->name) }}"
                                data-email="{{ strtolower($userItem->email ?? '') }}"
                                data-phone="{{ strtolower($userItem->phone ?? '') }}"
                                data-role="{{ $userItem->roleSlug() }}">
                                
                                <td class="px-5 py-4">
                                    <span class="text-xs font-bold text-slate-300 block">#{{ $userItem->id }}</span>
                                    <span class="text-[10px] text-slate-500 font-mono block mt-0.5">
                                        {{ $userItem->created_at ? $userItem->created_at->format('d/m/Y') : '—' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-xs font-extrabold text-white shadow-md shadow-indigo-500/10 shrink-0">
                                            {{ mb_strtoupper(mb_substr($userItem->name ?: 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-100 text-xs block leading-tight flex items-center gap-1.5">
                                                {{ $userItem->name }}
                                                @if($userItem->id === auth()->id())
                                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-amber-500/20 text-amber-300 border border-amber-500/30">Bạn</span>
                                                @endif
                                            </span>
                                            <span class="text-[11px] text-slate-400 font-mono">@<span>{{ $userItem->username }}</span></span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="text-xs text-slate-300 block font-mono">{{ $userItem->email ?: '—' }}</span>
                                    <span class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5 font-mono">
                                        <i class="fa-solid fa-phone text-[9px] text-slate-500"></i>
                                        {{ $userItem->phone ?: 'Chưa cập nhật' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    @php
                                        $slug = $userItem->roleSlug();
                                        $roleBadge = match ($slug) {
                                            'superadmin' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                            'admin' => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
                                            'landlord' => 'bg-blue-500/15 text-blue-300 border-blue-500/30',
                                            'unverified_landlord' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                            'manager' => 'bg-teal-500/15 text-teal-300 border-teal-500/30',
                                            'receptionist' => 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30',
                                            'housekeeper' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
                                            'resident' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
                                            default => 'bg-slate-700/30 text-slate-300 border-slate-700',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border {{ $roleBadge }} inline-flex items-center gap-1 uppercase tracking-wider">
                                        @if($slug === 'superadmin')
                                            <i class="fa-solid fa-crown text-[9px]"></i>
                                        @elseif($slug === 'admin')
                                            <i class="fa-solid fa-user-shield text-[9px]"></i>
                                        @elseif(in_array($slug, ['landlord', 'unverified_landlord']))
                                            <i class="fa-solid fa-hotel text-[9px]"></i>
                                        @endif
                                        {{ $userItem->roleName() }}
                                    </span>
                                    @if($userItem->tenant)
                                        <span class="text-[10px] text-slate-400 block mt-1 flex items-center gap-1 font-medium">
                                            <i class="fa-solid fa-building text-[9px] text-indigo-400"></i>
                                            {{ $userItem->tenant->name }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-500 block mt-1 italic">
                                            {{ in_array($slug, ['superadmin', 'admin', 'guest']) ? 'Toàn hệ thống' : 'Chưa gắn nhà trọ' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if($userItem->status === 'locked')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Đã khóa
                                        </span>
                                    @elseif($userItem->status === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Chờ duyệt
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hoạt động
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-slate-400 text-xs italic max-w-[150px] truncate" title="{{ $userItem->like }}">
                                    {{ $userItem->like ?: '—' }}
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <button type="button"
                                            onclick="openAssignRoleModal({{ $userItem->id }}, '{{ addslashes($userItem->name) }}', '{{ $userItem->roleSlug() }}', '{{ $userItem->tenant_id }}')"
                                            class="mx-auto px-3 py-1.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 rounded-xl text-xs font-semibold border border-indigo-500/20 transition-all flex items-center justify-center gap-1.5 shadow-md shadow-indigo-500/5 active:scale-[0.97]">
                                        <i class="fa-solid fa-user-gear"></i> Phân quyền
                                    </button>
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex gap-1.5 justify-end">
                                        <a href="{{ route('user.readUser', ['id' => $userItem->id]) }}"
                                           class="p-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-xl text-slate-300 hover:text-white transition-all flex items-center justify-center shadow"
                                           title="Xem chi tiết tài khoản">
                                            <i class="fa-regular fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('user.updateUser', ['id' => $userItem->id]) }}"
                                           class="p-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-indigo-500 rounded-xl text-indigo-400 hover:text-indigo-300 transition-all flex items-center justify-center shadow"
                                           title="Chỉnh sửa thông tin">
                                            <i class="fa-regular fa-pen-to-square text-xs"></i>
                                        </a>
                                        <a href="{{ route('user.deleteUser', ['id' => $userItem->id]) }}"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');"
                                           class="p-2 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 hover:border-rose-500/40 rounded-xl text-rose-400 transition-all flex items-center justify-center shadow"
                                           title="Xóa tài khoản">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-slate-500">
                                    <i class="fa-solid fa-user-slash text-2xl mb-2 block text-slate-600"></i>
                                    Không tìm thấy tài khoản người dùng nào phù hợp với bộ lọc hiện tại.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="p-4 border-t border-slate-800/60">
                    {{ $users->links() }}
                </div>
            @else
                <div class="p-4 border-t border-slate-800/60 flex items-center justify-between text-xs text-slate-400">
                    <span>Hiển thị <strong class="text-slate-200">{{ $users->total() }}</strong> thành viên</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Phân Quyền Tài Khoản -->
    <div id="assign-role-modal" class="fixed inset-0 z-50 items-center justify-center hidden transition-opacity duration-300">
        <div class="absolute inset-0 bg-slate-950/85 backdrop-blur-sm" onclick="closeAssignRoleModal()"></div>
        <div class="bg-[#0f172a] border border-slate-800/80 rounded-3xl p-6 w-full max-w-md relative z-10 mx-4 shadow-2xl transition-all transform scale-95 duration-350">
            <div class="flex items-center justify-between mb-5 border-b border-slate-800/60 pb-3">
                <h3 class="text-base font-bold text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-user-shield text-indigo-400"></i> Phân Quyền Thành Viên
                </h3>
                <button onclick="closeAssignRoleModal()" class="w-8 h-8 rounded-lg hover:bg-slate-800/60 text-slate-400 hover:text-slate-100 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <form id="assign-role-form" method="POST" action="{{ route('user.updateRole', [], false) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="user_id" id="modal-user-id">
                
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Tên thành viên</label>
                    <input type="text" id="modal-user-name" readonly class="w-full rounded-xl border border-slate-850 bg-slate-950 px-3.5 py-2.5 text-xs text-slate-400 outline-none cursor-not-allowed font-semibold">
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Vai Trò</label>
                    <select name="role_slug" id="modal-role-select" onchange="toggleTenantSelect()" class="w-full rounded-xl border border-slate-850 bg-slate-950 px-3.5 py-2.5 text-xs text-slate-200 outline-none focus:border-indigo-500/80 transition-all font-semibold font-sans">
                        @foreach($roles as $role)
                            <option value="{{ $role->slug }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="modal-tenant-container">
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Gán nhà trọ / Tenant</label>
                    <select name="tenant_id" id="modal-tenant-select" class="w-full rounded-xl border border-slate-850 bg-slate-950 px-3.5 py-2.5 text-xs text-slate-200 outline-none focus:border-indigo-500/80 transition-all font-semibold font-sans">
                        <option value="">Không gán nhà trọ</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-500 mt-2 leading-relaxed italic">
                        * Chủ trọ hoặc Nhân viên quản lý bắt buộc phải được gán nhà trọ để quản trị đúng phạm vi.
                    </p>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-800/60">
                    <button type="button" onclick="closeAssignRoleModal()" class="flex-1 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold text-slate-350 hover:bg-slate-850 transition-all text-center">
                        Hủy Bỏ
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-550 text-xs font-semibold text-white shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 transition-all text-center active:scale-[0.98]">
                        Cập Nhật
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAssignRoleModal(id, name, roleSlug, tenantId) {
            document.getElementById('modal-user-id').value = id;
            document.getElementById('modal-user-name').value = name;
            document.getElementById('modal-role-select').value = roleSlug;
            document.getElementById('modal-tenant-select').value = tenantId || '';
            
            toggleTenantSelect();
            
            const modal = document.getElementById('assign-role-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modal.querySelector('.bg-\\[\\#0f172a\\]').classList.remove('scale-95');
                modal.querySelector('.bg-\\[\\#0f172a\\]').classList.add('scale-100');
            }, 10);
        }

        function closeAssignRoleModal() {
            const modal = document.getElementById('assign-role-modal');
            modal.querySelector('.bg-\\[\\#0f172a\\]').classList.remove('scale-100');
            modal.querySelector('.bg-\\[\\#0f172a\\]').classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 150);
        }

        function toggleTenantSelect() {
            const roleSelect = document.getElementById('modal-role-select').value;
            const tenantContainer = document.getElementById('modal-tenant-container');
            if (['landlord', 'unverified_landlord', 'manager', 'receptionist', 'housekeeper'].includes(roleSelect)) {
                tenantContainer.style.display = 'block';
            } else {
                tenantContainer.style.display = 'none';
            }
        }

        function filterUserList() {
            const searchQuery = document.getElementById('user-search').value.toLowerCase().trim();
            const roleFilter = document.getElementById('role-filter').value;
            const rows = document.querySelectorAll('.user-row');

            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const email = row.getAttribute('data-email');
                const phone = row.getAttribute('data-phone');
                const role = row.getAttribute('data-role');

                const matchesSearch = name.includes(searchQuery) || email.includes(searchQuery) || phone.includes(searchQuery);
                const matchesRole = roleFilter === 'all' || role === roleFilter;

                if (matchesSearch && matchesRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</main>
