    <!-- ADD RESIDENT MODAL (POPUP) - Mở rộng thêm thông tin cá nhân & tạm trú -->
    <div id="add-resident-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-2xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button onclick="toggleAddResidentModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-indigo-400"></i> Thêm Cư Dân Mới
            </h2>
            <!-- Chống spam click: form submit sẽ disable nút sau lần click đầu tiên -->
            <form action="{{ route('smartroom.admin.resident.store', [], false) }}" method="POST" class="space-y-4" onsubmit="return antiSpamSubmit(this)">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Họ và tên *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="Nguyễn Văn A">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số điện thoại *</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="09xxxxxxxx">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày sinh</label>
                        <input type="date" name="dob" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số CCCD</label>
                        <input type="text" name="cccd" maxlength="12" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="0xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Quê quán</label>
                        <input type="text" name="hometown" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="TP. Hồ Chí Minh">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Chọn phòng *</label>
                        <select name="room_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            @foreach($emptyRoomsList as $room)
                                <option value="{{ $room->id }}">P. {{ $room->room_number }} (Trống)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày vào ở *</label>
                        <input type="date" name="start_date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tạm trú *</label>
                        <select name="temporary_residence_status" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="none">Chưa đăng ký</option>
                            <option value="registered">Đã đăng ký</option>
                            <option value="absent">Tạm vắng</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleAddResidentModal(false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="anti-spam-btn px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Xác Nhận Thêm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT RESIDENT MODAL - Có Optimistic Locking (version) để chặn ghi đè xung đột -->
    <div id="edit-resident-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-2xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button onclick="toggleEditResidentModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-indigo-400"></i> Chỉnh Sửa Thông Tin Cư Dân
            </h2>
            <form id="edit-resident-form" action="" method="POST" class="space-y-4" onsubmit="return antiSpamSubmit(this)">
                @csrf
                @method('PUT')
                <!-- Optimistic Locking: trường version ẩn để server so sánh phiên bản trước khi cập nhật -->
                <input type="hidden" name="version" id="edit-version" value="1">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Họ và tên *</label>
                        <input type="text" name="name" id="edit-name" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số điện thoại *</label>
                        <input type="tel" name="phone" id="edit-phone" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày sinh</label>
                        <input type="date" name="dob" id="edit-dob" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số CCCD</label>
                        <input type="text" name="cccd" id="edit-cccd" maxlength="12" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Quê quán</label>
                        <input type="text" name="hometown" id="edit-hometown" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Phòng trọ *</label>
                        <select name="room_id" id="edit-room-id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}">P. {{ $r->room_number }} ({{ $r->status === 'empty' ? 'Trống' : 'Đang thuê' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày vào ở *</label>
                        <input type="date" name="start_date" id="edit-start-date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tạm trú *</label>
                        <select name="temporary_residence_status" id="edit-temp-status" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="none">Chưa đăng ký</option>
                            <option value="registered">Đã đăng ký</option>
                            <option value="absent">Tạm vắng</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleEditResidentModal(false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="anti-spam-btn px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- VIEW RESIDENT MODAL (POPUP) - Hiển thị chi tiết cư dân & danh sách người thân đi cùng -->
    <div id="view-resident-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-2xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button onclick="toggleViewResidentModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-address-card text-indigo-400"></i> Thông Tin Chi Tiết Cư Dân
            </h2>
            <div class="space-y-6">
                <!-- Thông tin cá nhân -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-user text-indigo-400 text-[10px]"></i> Thông tin cá nhân
                    </h3>
                    <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-900/50 border border-slate-800/40">
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Họ và tên:</span>
                                <strong class="text-slate-200" id="view-name">Nguyễn Văn A</strong>
                            </div>
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Số điện thoại:</span>
                                <strong class="text-slate-200 font-mono" id="view-phone">09xxxxxxxx</strong>
                            </div>
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Email liên hệ:</span>
                                <strong class="text-slate-200" id="view-email">email@gmail.com</strong>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Ngày sinh:</span>
                                <strong class="text-slate-200 font-mono" id="view-dob">—</strong>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Phòng thuê:</span>
                                <strong class="text-indigo-400 font-bold" id="view-room">Phòng 101</strong>
                            </div>
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Số CCCD:</span>
                                <strong class="text-slate-200 font-mono" id="view-cccd">—</strong>
                            </div>
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Quê quán:</span>
                                <strong class="text-slate-200" id="view-hometown">—</strong>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Đăng ký tạm trú:</span>
                                <span id="view-temp-status" class="px-2 py-0.5 rounded text-[10px] font-bold">Chưa ĐK</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách người thân -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-people-group text-cyan-400 text-[10px]"></i> Người thân tạm trú cùng
                        </h3>
                        <button id="view-manage-relatives-btn" onclick="" class="text-[10px] font-bold text-cyan-400 hover:text-cyan-300 transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-cog"></i> Quản lý
                        </button>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900/50 border border-slate-800/40 min-h-[80px]">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-300 hidden" id="view-relatives-table">
                                <thead class="text-slate-500 uppercase font-bold text-[10px] border-b border-slate-800">
                                    <tr>
                                        <th class="pb-2">Họ tên</th>
                                        <th class="pb-2">Quan hệ</th>
                                        <th class="pb-2">CCCD</th>
                                        <th class="pb-2">Trạng thái tạm trú</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/40" id="view-relatives-tbody">
                                    <!-- AJAX insert relatives here -->
                                </tbody>
                            </table>
                            <div class="text-slate-500 text-center py-4" id="view-relatives-empty">
                                <i class="fa-solid fa-folder-open text-lg mb-1 block"></i>
                                Không có thông tin người thân tạm trú.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="toggleViewResidentModal(false)" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MANAGE RELATIVES MODAL (POPUP) - Quản lý người thân tạm trú qua AJAX -->
    <div id="relatives-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-3xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button onclick="toggleRelativesModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-2 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-people-roof text-cyan-400"></i> Quản Lý Người Thân Tạm Trú
            </h2>
            <p class="text-xs text-slate-500 mb-6">Của cư dân: <strong class="text-slate-300 font-bold" id="relatives-modal-resident-name">—</strong></p>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Danh sách người thân hiện có -->
                <div class="lg:col-span-3 space-y-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                        <i class="fa-solid fa-list text-cyan-400"></i> Danh sách người thân trọ cùng
                    </h3>
                    <div class="p-4 rounded-xl bg-slate-900/40 border border-slate-800/60 min-h-[200px] max-h-[350px] overflow-y-auto space-y-2" id="relatives-list-container">
                        <!-- AJAX generated list here -->
                    </div>
                </div>

                <!-- Form Thêm / Sửa người thân -->
                <div class="lg:col-span-2 space-y-4 border-t lg:border-t-0 lg:border-l border-slate-800/80 pt-4 lg:pt-0 lg:pl-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1" id="relative-form-title">
                        <i class="fa-solid fa-user-plus text-indigo-400"></i> Thêm Người Thân Mới
                    </h3>
                    
                    <form id="relative-ajax-form" onsubmit="saveRelative(event)" class="space-y-3">
                        <input type="hidden" id="relative-id" value="">
                        <!-- version của relative cho Optimistic Locking -->
                        <input type="hidden" id="relative-version" value="1">
                        
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Họ và tên *</label>
                            <input type="text" id="relative-name" required class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nguyễn Văn B">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mối quan hệ *</label>
                                <input type="text" id="relative-relationship" required class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Bố, mẹ, vợ, con...">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Ngày sinh</label>
                                <input type="date" id="relative-dob" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Số CCCD</label>
                            <input type="text" id="relative-cccd" maxlength="12" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="12 số">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Quê quán</label>
                            <input type="text" id="relative-hometown" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Địa chỉ quê quán">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Trạng thái đăng ký tạm trú *</label>
                            <select id="relative-temp-status" required class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none">
                                <option value="none">Chưa đăng ký</option>
                                <option value="registered">Đã đăng ký</option>
                                <option value="absent">Tạm vắng</option>
                            </select>
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="button" id="relative-reset-btn" onclick="resetRelativeForm()" class="hidden w-1/2 py-2 rounded-lg text-xs font-bold text-slate-400 bg-transparent hover:bg-slate-900 border border-slate-800 transition-all">
                                Hủy sửa
                            </button>
                            <button type="submit" id="relative-submit-btn" class="w-full py-2 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-save"></i> <span>Lưu Lại</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="pt-6 mt-6 border-t border-slate-900 flex justify-end">
                <button type="button" onclick="toggleRelativesModal(false)" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-300 bg-slate-900 border border-slate-800 hover:border-slate-700 transition-all">
                    Đóng
                </button>
            </div>
        </div>
    </div>

