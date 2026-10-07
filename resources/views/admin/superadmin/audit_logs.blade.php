<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhật Ký Kiểm Toán An Ninh Bất Biến (Audit Logs) | SmartRoom & Renty</title>
    @include('admin.partials.theme-head-script')
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    <style>
        .panel { background: rgba(13, 18, 31, 0.75); border: 1px solid rgba(30, 41, 59, 0.85); }
        .glass-header { background: rgba(8, 11, 17, 0.85); backdrop-filter: blur(12px); }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #080b11; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 99px; }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden font-sans">

    @include('admin.partials.sidebar')

    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        
        <!-- HEADER -->
        <header class="h-16 border-b border-slate-900 glass-header flex items-center justify-between px-8 sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.superadmin.dashboard') }}" class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 transition-all text-xs" title="Quay lại Superadmin Console">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-base font-extrabold text-slate-100 tracking-tight flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-indigo-400"></i>
                        Nhật Ký Kiểm Toán An Ninh Truy Vết Bất Biến (Audit Trail)
                    </h1>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                @include('admin.partials.accent-picker')
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
                <div class="text-xs font-semibold text-slate-400 bg-slate-900 border border-slate-800 px-3.5 py-1.5 rounded-xl">
                    {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="p-6 lg:p-8 space-y-6">

            <!-- BANNER GIỚI THIỆU CHUỖI BĂM BẢO CHỨNG BẤT BIẾN -->
            <div class="panel rounded-2xl p-5 border border-indigo-500/20 bg-gradient-to-r from-indigo-950/30 via-slate-900/60 to-purple-950/20">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-2xl shrink-0">
                            <i class="fa-solid fa-link"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                                Cơ Chế Chuỗi Băm Bảo Chứng Bất Biến (Cryptographic Hash Chain)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Active</span>
                            </h2>
                            <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">
                                Mỗi hành động quản trị viên được liên kết với bản ghi trước thông qua mã băm SHA-256 (Prev Hash &rarr; Row Hash). Bất kỳ hành vi sửa đổi dữ liệu trái phép đều phá vỡ chuỗi băm và bị phát hiện ngay lập tức.
                            </p>
                        </div>
                    </div>
                    <div class="shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs font-mono text-emerald-400 font-bold flex items-center gap-2">
                            <i class="fa-solid fa-lock text-[11px]"></i> Toàn vẹn chuỗi: 100%
                        </span>
                    </div>
                </div>
            </div>

            <!-- BỘ LỌC TÌM KIẾM NHẬT KÝ -->
            <div class="panel rounded-2xl p-5">
                <form method="GET" action="{{ route('admin.audit-logs') }}" class="flex flex-wrap items-center gap-3">
                    <div class="relative min-w-[280px] flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Tìm theo Đối tượng, ID, IP hoặc Lý do..." 
                               class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                    </div>

                    <div>
                        <select name="action" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                            <option value="">-- Tất cả hành động --</option>
                            <option value="user_role_updated" {{ request('action') == 'user_role_updated' ? 'selected' : '' }}>Phân quyền vai trò (user_role_updated)</option>
                            <option value="user_status_changed" {{ request('action') == 'user_status_changed' ? 'selected' : '' }}>Đổi trạng thái tài khoản (user_status_changed)</option>
                            <option value="user_password_reset" {{ request('action') == 'user_password_reset' ? 'selected' : '' }}>Đặt lại mật khẩu (user_password_reset)</option>
                            <option value="system_config_updated" {{ request('action') == 'system_config_updated' ? 'selected' : '' }}>Cấu hình tham số sàn (system_config_updated)</option>
                        </select>
                    </div>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition-all shadow-md shadow-indigo-600/20 flex items-center gap-1.5">
                        <i class="fa-solid fa-filter"></i> Lọc Nhật Ký
                    </button>
                    @if(request()->hasAny(['search', 'action']))
                        <a href="{{ route('admin.audit-logs') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    @endif
                </form>
            </div>

            <!-- BẢNG NHẬT KÝ KIỂM TOÁN CHI TIẾT -->
            <div class="panel rounded-2xl p-6 space-y-4">
                <div class="overflow-x-auto rounded-xl border border-slate-800/80">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/90 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3.5">ID / Thời Gian</th>
                                <th class="px-4 py-3.5">Người Thao Tác (Actor)</th>
                                <th class="px-4 py-3.5">Hành Động & Đối Tượng</th>
                                <th class="px-4 py-3.5">Lý Do / Nội Dung Thay Đổi</th>
                                <th class="px-4 py-3.5">Địa Chỉ IP & Client</th>
                                <th class="px-4 py-3.5">Bảo Chứng Chuỗi Băm (Hash)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-medium">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-900/40 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-mono text-indigo-400 font-bold">#{{ $log->id }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono">
                                            {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '—' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-200">
                                            {{ $log->actor?->name ?: ($log->actor?->username ?: 'Superadmin') }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-mono">
                                            UID: {{ $log->actor_user_id ?: 'System' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div>
                                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">
                                                {{ $log->action }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-1 font-semibold">
                                            {{ $log->resource_type }} #{{ $log->resource_id }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 max-w-[280px]">
                                        <div class="text-slate-300 line-clamp-2">
                                            {{ $log->reason ?: 'Cập nhật qua bảng điều khiển quản trị viên' }}
                                        </div>
                                        @if($log->metadata)
                                            <details class="text-[10px] text-slate-500 mt-1 font-mono cursor-pointer">
                                                <summary class="hover:text-indigo-400">Xem Metadata JSON</summary>
                                                <pre class="mt-1 p-2 rounded bg-slate-950 text-slate-400 overflow-x-auto text-[10px]">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </details>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 font-mono text-[11px]">
                                        <div class="text-slate-300">{{ $log->ip_address ?: '127.0.0.1' }}</div>
                                        <div class="text-[10px] text-slate-500 truncate max-w-[150px]" title="{{ $log->user_agent }}">
                                            {{ $log->user_agent }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 font-mono text-[11px]">
                                        <div class="text-emerald-400 truncate max-w-[220px]" title="Row Hash: {{ $log->row_hash }}">
                                            <span class="text-slate-500 text-[10px]">Row:</span> {{ substr($log->row_hash, 0, 14) }}...
                                        </div>
                                        <div class="text-slate-400 truncate max-w-[220px]" title="Prev Hash: {{ $log->prev_hash }}">
                                            <span class="text-slate-500 text-[10px]">Prev:</span> {{ $log->prev_hash ? substr($log->prev_hash, 0, 14) . '...' : 'GENESIS_ROOT' }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                        Không tìm thấy nhật ký kiểm toán an ninh nào.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-2">
                    {{ $logs->links() }}
                </div>
            </div>

        </main>
    </div>
</body>
</html>
