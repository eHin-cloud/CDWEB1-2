<div class="text-center mb-6">
    <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-2xl text-indigo-400 mb-3 shadow-inner">
        <i class="fa-solid fa-user-shield"></i>
    </div>
    <h1 class="text-2xl font-extrabold text-slate-100">{{ $messi->name }}</h1>
    <p class="text-xs text-indigo-400 mt-1 font-mono">@ {{ $messi->username }} &bull; ID: #{{ $messi->id }}</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
    <div>
        <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider">Email liên hệ</label>
        <p class="text-slate-200 mt-1 bg-slate-950/60 px-3.5 py-2.5 rounded-xl border border-slate-800 font-mono">{{ $messi->email ?: 'Chưa cập nhật email' }}</p>
    </div>
    <div>
        <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider">Số điện thoại</label>
        <p class="text-slate-200 mt-1 bg-slate-950/60 px-3.5 py-2.5 rounded-xl border border-slate-800 font-mono">{{ $messi->phone ?: 'Chưa cập nhật SĐT' }}</p>
    </div>
    <div>
        <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider">Vai trò hiện tại</label>
        <p class="text-slate-200 mt-1 bg-slate-950/60 px-3.5 py-2.5 rounded-xl border border-slate-800 font-semibold text-indigo-400">
            {{ $messi->roleName() }}
        </p>
    </div>
    <div>
        <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider">Trạng thái tài khoản</label>
        <div class="mt-1 bg-slate-950/60 px-3.5 py-2.5 rounded-xl border border-slate-800">
            @if($messi->status === 'locked')
                <span class="text-rose-400 font-bold inline-flex items-center gap-1.5"><i class="fa-solid fa-lock text-[10px]"></i> Đã khóa</span>
            @elseif($messi->status === 'pending')
                <span class="text-amber-400 font-bold inline-flex items-center gap-1.5"><i class="fa-solid fa-clock text-[10px]"></i> Chờ duyệt</span>
            @else
                <span class="text-emerald-400 font-bold inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-[10px]"></i> Đang hoạt động</span>
            @endif
        </div>
    </div>
    <div>
        <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider">Cơ sở lưu trú / Tenant</label>
        <p class="text-slate-200 mt-1 bg-slate-950/60 px-3.5 py-2.5 rounded-xl border border-slate-800">
            {{ $messi->tenant?->name ?: 'Toàn hệ thống' }}
        </p>
    </div>
    <div>
        <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider">Ngày đăng ký</label>
        <p class="text-slate-200 mt-1 bg-slate-950/60 px-3.5 py-2.5 rounded-xl border border-slate-800 font-mono">{{ $messi->created_at ? $messi->created_at->format('d/m/Y H:i:s') : 'Chưa rõ' }}</p>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider">Mô tả / Ghi chú</label>
        <p class="text-slate-300 mt-1 bg-slate-950/60 px-3.5 py-2.5 rounded-xl border border-slate-800 italic">{{ $messi->like ?: 'Không có ghi chú' }}</p>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <a href="{{ route('user.list') }}" class="flex-1 py-2.5 px-4 text-center rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-slate-300 transition-all">Quay Lại Danh Sách</a>
    <a href="{{ route('user.updateUser', ['id' => $messi->id]) }}" class="flex-1 py-2.5 px-4 text-center rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-lg shadow-indigo-500/25 transition-all">Chỉnh Sửa</a>
</div>
