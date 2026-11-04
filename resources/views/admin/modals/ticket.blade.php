    <!-- TICKET UPDATE MODAL -->
    <div id="ticket-update-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-lg bg-[#0a0f1d] border border-slate-800 p-6 sm:p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button type="button" onclick="closeTicketUpdateModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-screwdriver-wrench text-indigo-400"></i> Xử Lý Sự Cố Báo Hỏng
            </h2>
            <div class="mb-4 p-4 rounded-2xl bg-slate-900/60 border border-slate-800 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-400">Mã sự cố:</span>
                    <span id="modal-ticket-id" class="font-mono font-bold text-slate-200">#</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Phòng & Vị trí:</span>
                    <span id="modal-ticket-room-location" class="font-bold text-indigo-400">P. -</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Cư dân báo:</span>
                    <span id="modal-ticket-resident" class="font-semibold text-slate-300">-</span>
                </div>
                <div class="pt-1 text-slate-300 font-medium" id="modal-ticket-title">-</div>
            </div>

            <form id="ticket-update-form" method="POST" action="" onsubmit="return validateTicketUpdateForm()" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Trạng thái sự cố</label>
                    <select name="status" id="modal-ticket-status" onchange="handleTicketModalStatusChange(this.value)" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                        <option value="pending">Chờ tiếp nhận (Pending)</option>
                        <option value="processing">Đang xử lý / Đã giao việc (Processing)</option>
                        <option value="resolved">Đã giải quyết xong (Resolved)</option>
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">
                            Kỹ thuật viên / Thợ phụ trách <span id="modal-assigned-required-star" class="hidden text-rose-400 font-black">*</span>
                        </label>
                        <span id="modal-assigned-hint" class="text-[11px] text-slate-500 italic">Tùy chọn khi chờ tiếp nhận</span>
                    </div>
                    <div class="relative">
                        <input type="text" name="assigned_to" id="modal-ticket-assigned-to" maxlength="100" oninput="clearTicketAssignedError()" placeholder="VD: Thợ điện Tuấn, KTV Nam..." class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                    </div>
                    <p id="modal-assigned-error" class="hidden text-[11px] text-rose-400 mt-1.5 flex items-center gap-1 font-medium">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        <span id="modal-assigned-error-text"></span>
                    </p>
                </div>

                <div class="p-3 rounded-xl bg-indigo-500/5 border border-indigo-500/10">
                    <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="send_telegram" value="1" checked class="rounded bg-slate-900 border-slate-800 text-indigo-600 focus:ring-0">
                        <span>Gửi thông báo tiến độ qua Telegram Bot cho BQL</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeTicketUpdateModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-300 transition-all">
                        Hủy
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white transition-all shadow-lg shadow-indigo-600/30">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TICKET IMAGE VIEWER MODAL -->
    <div id="ticket-image-modal" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md hidden flex items-center justify-center p-4" onclick="closeTicketImageModal()">
        <div class="relative max-w-4xl max-h-[90vh]" onclick="event.stopPropagation()">
            <button type="button" onclick="closeTicketImageModal()" class="absolute -top-12 right-0 text-white hover:text-rose-400 text-2xl font-bold transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <img id="ticket-image-modal-src" src="" alt="Ảnh lỗi phóng to" class="max-h-[85vh] max-w-full rounded-2xl shadow-2xl object-contain border border-slate-800">
        </div>
    </div>

