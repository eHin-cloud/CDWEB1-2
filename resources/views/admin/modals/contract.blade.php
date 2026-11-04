    <!-- STYLE HỖ TRỢ GIAO DIỆN SÁNG (LIGHT MODE) & CHỐNG LỖI FONT NỀN XUYÊN THẤU CHO MODAL HỢP ĐỒNG -->
    <style>
        #add-contract-modal .modal-box {
            background-color: #0b1120;
            color: #f1f5f9;
            border: 1px solid #1e293b;
        }
        #contract-room-results {
            background-color: #0b1120 !important;
            border: 1px solid #334155 !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8) !important;
            opacity: 1 !important;
            backdrop-filter: none !important;
        }
        #contract-room-results .contract-room-option {
            background: transparent;
        }
        #contract-room-results .contract-room-option:hover {
            background-color: rgba(99, 102, 241, 0.2) !important;
        }

        /* TRẠNG THÁI BÔI ĐỎ VIỀN KHI VALIDATE SAI (CẢ DARK & LIGHT MODE) */
        #add-contract-modal .input-error,
        #add-contract-modal input.input-error,
        #add-contract-modal textarea.input-error,
        #add-contract-modal #contract-room-search-wrapper.input-error,
        html.theme-light #add-contract-modal .input-error,
        html.theme-light #add-contract-modal input.input-error,
        html.theme-light #add-contract-modal textarea.input-error,
        html.theme-light #add-contract-modal #contract-room-search-wrapper.input-error,
        body.theme-light #add-contract-modal .input-error,
        body.theme-light #add-contract-modal input.input-error,
        body.theme-light #add-contract-modal textarea.input-error,
        body.theme-light #add-contract-modal #contract-room-search-wrapper.input-error {
            border: 2px solid #ef4444 !important;
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.25) !important;
            background-color: rgba(239, 68, 68, 0.08) !important;
            outline: none !important;
        }

        #add-contract-modal .error-message {
            color: #ef4444 !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            margin-top: 0.375rem !important;
        }
        #add-contract-modal .error-message.hidden {
            display: none !important;
        }
        #add-contract-modal .error-message:not(.hidden) {
            display: flex !important;
            align-items: center !important;
            gap: 0.375rem !important;
        }

        /* GIAO DIỆN SÁNG (LIGHT MODE) */
        html.theme-light #add-contract-modal .modal-box,
        body.theme-light #add-contract-modal .modal-box {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.25) !important;
        }
        html.theme-light #add-contract-modal .contract-section-card,
        body.theme-light #add-contract-modal .contract-section-card {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }
        html.theme-light #add-contract-modal h2,
        html.theme-light #add-contract-modal h3,
        body.theme-light #add-contract-modal h2,
        body.theme-light #add-contract-modal h3 {
            color: #0f172a !important;
        }
        html.theme-light #add-contract-modal label,
        body.theme-light #add-contract-modal label {
            color: #475569 !important;
            font-weight: 700 !important;
        }
        html.theme-light #add-contract-modal input:not(.input-error),
        html.theme-light #add-contract-modal textarea:not(.input-error),
        body.theme-light #add-contract-modal input:not(.input-error),
        body.theme-light #add-contract-modal textarea:not(.input-error) {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        html.theme-light #add-contract-modal input::placeholder,
        html.theme-light #add-contract-modal textarea::placeholder,
        body.theme-light #add-contract-modal input::placeholder,
        body.theme-light #add-contract-modal textarea::placeholder {
            color: #94a3b8 !important;
        }
        html.theme-light #contract-room-search-wrapper:not(.input-error),
        body.theme-light #contract-room-search-wrapper:not(.input-error) {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
        }
        html.theme-light #contract-room-search,
        body.theme-light #contract-room-search {
            background-color: transparent !important;
            border: none !important;
            color: #0f172a !important;
        }
        html.theme-light #contract-room-results,
        body.theme-light #contract-room-results {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 25px 60px -10px rgba(15, 23, 42, 0.3) !important;
            opacity: 1 !important;
            backdrop-filter: none !important;
        }
        html.theme-light #contract-room-results .contract-room-option span.text-slate-100,
        body.theme-light #contract-room-results .contract-room-option span.text-slate-100 {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        html.theme-light #contract-room-results .contract-room-option span.text-slate-500,
        body.theme-light #contract-room-results .contract-room-option span.text-slate-500 {
            color: #64748b !important;
        }
        html.theme-light #contract-room-results .contract-room-option:hover,
        body.theme-light #contract-room-results .contract-room-option:hover {
            background-color: #eef2ff !important;
        }
        /* Khắc phục lỗi autofill trắng đục của Chrome */
        #add-contract-modal input:-webkit-autofill,
        #add-contract-modal input:-webkit-autofill:hover,
        #add-contract-modal input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #0b1120 inset !important;
            -webkit-text-fill-color: #f8fafc !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        html.theme-light #add-contract-modal input:-webkit-autofill,
        html.theme-light #add-contract-modal input:-webkit-autofill:hover,
        html.theme-light #add-contract-modal input:-webkit-autofill:focus,
        body.theme-light #add-contract-modal input:-webkit-autofill,
        body.theme-light #add-contract-modal input:-webkit-autofill:hover,
        body.theme-light #add-contract-modal input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #ffffff inset !important;
            -webkit-text-fill-color: #0f172a !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>

    <!-- ADD CONTRACT MODAL (POPUP) -->
    <div id="add-contract-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="modal-box w-full max-w-5xl border p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[92vh] overflow-y-auto">
            <button onclick="toggleAddContractModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-indigo-400"></i> Tạo Hợp Đồng Thuê Nhà Mới
            </h2>
            <form id="create-contract-form" action="{{ route('smartroom.admin.contract.store', [], false) }}" method="POST" onsubmit="return validateCreateContractForm(event)" novalidate class="space-y-6">
                @csrf

                <!-- Phương thức ký kết hợp đồng -->
                <div class="contract-section-card rounded-2xl border border-slate-800 bg-slate-950/40 p-4 space-y-3">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-pen-ruler text-indigo-400"></i> Phương thức ký kết hợp đồng *
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="relative flex items-start gap-3 p-3.5 rounded-xl border border-indigo-500/40 bg-indigo-500/5 hover:bg-indigo-500/10 cursor-pointer transition-all has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-600/10">
                            <input type="radio" name="signing_method" value="online" checked class="mt-1 text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-100 flex items-center gap-1.5">
                                        <i class="fa-solid fa-globe text-indigo-400"></i> Ký số Online (Khuyến nghị)
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Nhanh gọn</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                                    Sinh liên kết ký số, cư dân vẽ chữ ký tay Canvas trên điện thoại và xác thực mã OTP qua Gmail.
                                </p>
                            </div>
                        </label>

                        <label class="relative flex items-start gap-3 p-3.5 rounded-xl border border-slate-800 bg-slate-900/60 hover:bg-slate-900 cursor-pointer transition-all has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500/10">
                            <input type="radio" name="signing_method" value="offline" class="mt-1 text-amber-500 focus:ring-amber-500">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-100 flex items-center gap-1.5">
                                        <i class="fa-solid fa-file-signature text-amber-400"></i> Ký bản cứng trực tiếp (In giấy)
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/30">Truyền thống</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                                    Tải/In file PDF chuẩn A4 ra giấy để 2 bên ký bút mực. Sau đó xác nhận hoặc tải ảnh chụp hợp đồng lên để kích hoạt phòng.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nơi ký</label>
                        <input type="text" name="contract_place" value="Hà Nội" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày</label>
                        <input type="number" name="sign_day" min="1" max="31" value="{{ date('d') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tháng</label>
                        <input type="number" name="sign_month" min="1" max="12" value="{{ date('m') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Năm</label>
                        <input type="number" name="sign_year" min="1900" max="2100" value="{{ date('Y') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Chọn Phòng Trọ *</label>
                        <div class="relative">
                            <div id="contract-room-search-wrapper" class="flex items-center gap-2 rounded-xl bg-slate-900 border border-slate-800 focus-within:border-indigo-500 px-4 transition-all">
                                <i class="fa-solid fa-magnifying-glass text-slate-500 text-xs"></i>
                                <input type="text" id="contract-room-search" oninput="filterContractRooms(this.value)" onfocus="filterContractRooms(this.value)" placeholder="Tìm số phòng, tên tòa, địa chỉ..." autocomplete="off" class="w-full py-2.5 bg-transparent text-slate-200 text-sm focus:outline-none">
                            </div>
                            <input type="hidden" name="room_id" id="contract-room-id">
                            <p id="err-contract-room" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Vui lòng chọn phòng lưu trú để thiết lập hợp đồng.</span>
                            </p>
                            <div id="contract-room-results" class="hidden absolute left-0 right-0 top-[calc(100%+8px)] z-50 max-h-72 overflow-y-auto rounded-2xl border border-slate-800 bg-[#0b1120] shadow-2xl p-1">
                                @php
                                    $availableRooms = $rooms->filter(function($r) {
                                        return $r->status === 'empty';
                                    });
                                @endphp
                                @forelse($availableRooms as $r)
                                    @php
                                        $buildingName = $r->building?->name ?? 'Chưa rõ tòa';
                                        $buildingAddress = $r->building?->address ?? '';
                                        $roomLabel = 'Phòng ' . $r->room_number . ' - ' . $buildingName;
                                        $roomSearchText = $roomLabel . ' Phòng' . $r->room_number . ' ' . $buildingAddress;
                                    @endphp
                                    <button type="button" onclick="selectContractRoom(this)" data-id="{{ $r->id }}" data-price="{{ $r->price }}" data-area="{{ $r->area }}" data-room="{{ $r->room_number }}" data-address="{{ $buildingAddress }}" data-label="{{ $roomLabel }}" data-search="{{ mb_strtolower($roomSearchText) }}" class="contract-room-option w-full text-left px-3 py-2.5 rounded-xl hover:bg-indigo-600/20 transition-all flex items-center justify-between group">
                                        <div>
                                            <span class="block text-sm font-bold text-slate-100 group-hover:text-indigo-300">Phòng {{ $r->room_number }}</span>
                                            <span class="block text-[11px] text-slate-500">{{ $buildingName }} • {{ number_format($r->price) }} đ/tháng • {{ $r->area }}m²</span>
                                        </div>
                                        <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-md flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Trống
                                        </span>
                                    </button>
                                @empty
                                    <div class="px-3 py-4 text-center text-xs font-semibold text-amber-400">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Hiện không còn phòng trống nào khả dụng để tạo hợp đồng mới.
                                    </div>
                                @endforelse
                                <div id="contract-room-empty" class="hidden px-3 py-4 text-center text-xs font-semibold text-slate-500">
                                    Không tìm thấy phòng phù hợp
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contract-section-card rounded-2xl border border-slate-800 bg-slate-950/30 p-4">
                    <h3 class="text-sm font-extrabold text-slate-200 mb-3">I. Bên cho thuê nhà (Bên A)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Họ và tên bên A *</label>
                            <input type="text" name="lessor_name" id="lessor-name" value="{{ Auth::user()->name ?? 'Chủ trọ SmartRoom' }}" placeholder="Họ và tên bên A" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                            <p id="err-lessor-name" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Họ và tên bên cho thuê không được để trống.</span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">CMTND / CCCD bên A</label>
                            <input type="text" name="lessor_id_number" id="lessor-id-number" placeholder="Số CMTND / CCCD bên A" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số điện thoại bên A</label>
                            <input type="text" name="lessor_phone" id="lessor-phone" value="{{ Auth::user()->phone ?? '' }}" placeholder="Số điện thoại bên A" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">HKTT / Chỗ ở hiện tại bên A</label>
                            <input type="text" name="lessor_address" id="lessor-address" placeholder="Địa chỉ thường trú bên A" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="contract-section-card rounded-2xl border border-slate-800 bg-slate-950/30 p-4">
                    <h3 class="text-sm font-extrabold text-slate-200 mb-3">II. Bên thuê nhà ở (Bên B)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tên bên thuê (Cư dân đại diện) *</label>
                            <input type="text" name="lessee_name" id="lessee-name" placeholder="Nhập họ tên bên thuê" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                            <input type="hidden" name="resident_id" id="contract-resident-id">
                            <p id="err-lessee-name" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Vui lòng chọn cư dân đại diện đứng tên trên hợp đồng.</span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">CMND / CCCD bên B *</label>
                            <input type="text" name="lessee_id_number" id="lessee-id-number" placeholder="Nhập 9 hoặc 12 số CCCD" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                            <p id="err-lessee-id-number" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Số CMND/CCCD bên thuê phải gồm 9 đến 12 chữ số.</span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số điện thoại bên B *</label>
                            <div class="relative">
                                <input type="text" name="lessee_phone" id="lessee-phone" oninput="lookupCustomerByPhone(this.value)" placeholder="VD: 0988123456" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                                <span id="lookup-customer-spinner" class="hidden absolute right-3 top-3 text-indigo-400">
                                    <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                                </span>
                            </div>
                            <p id="err-lessee-phone" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Số điện thoại phải gồm 10 chữ số và bắt đầu bằng số 0 (VD: 0988123456).</span>
                            </p>
                            <!-- Huy hiệu nhận dạng tài khoản khách -->
                            <div id="customer-lookup-badge" class="hidden mt-2 p-2.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 text-xs flex items-center justify-between gap-2 transition-all">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                                    <div>
                                        <div class="font-bold text-slate-100 flex items-center gap-1.5">
                                            <span id="lookup-customer-name">Khách thuê</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Đã có tài khoản</span>
                                        </div>
                                        <div id="lookup-customer-email" class="text-[11px] text-slate-400">email@example.com</div>
                                    </div>
                                </div>
                                <span class="text-[10px] text-emerald-300 font-semibold bg-emerald-500/20 px-2 py-0.5 rounded">Tự điền ✓</span>
                            </div>
                            <!-- Cảnh báo nếu khách đang có hợp đồng chưa kết thúc -->
                            <div id="customer-active-contract-warning" class="hidden mt-2 p-3 rounded-xl border border-amber-500/30 bg-amber-500/10 text-amber-300 text-xs transition-all space-y-1">
                                <div class="flex items-center gap-1.5 font-bold text-amber-200">
                                    <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
                                    <span>Khách này đang có hợp đồng chưa kết thúc!</span>
                                </div>
                                <div id="customer-active-contract-desc" class="text-[11px] text-amber-300/90 leading-relaxed font-sans">
                                    Hiện đang thuê phòng...
                                </div>
                                <div class="text-[10px] text-slate-400 italic">
                                    💡 Xác nhận nếu khách thuê thêm phòng mới, hoặc dùng chức năng <strong>"Gia hạn"</strong> nếu khách ở tiếp phòng cũ.
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email bên B (Nhận link ký &amp; OTP)</label>
                            <input type="email" name="lessee_email" id="lessee-email" placeholder="VD: nguyenvana@gmail.com" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hộ khẩu thường trú (HKTT) bên B *</label>
                            <input type="text" name="lessee_permanent_address" id="lessee-permanent-address" placeholder="Địa chỉ theo CCCD bên B" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                            <p id="err-lessee-permanent-address" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Vui lòng nhập hộ khẩu thường trú của bên thuê.</span>
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Chỗ ở hiện tại bên B *</label>
                            <input type="text" name="lessee_current_address" id="lessee-current-address" placeholder="Nơi cư trú hoặc tạm trú hiện tại bên B" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                            <p id="err-lessee-current-address" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Vui lòng nhập chỗ ở hiện tại của bên thuê.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày Bắt Đầu *</label>
                        <input type="date" name="start_date" id="contract-start-date" onchange="calculateContractEndDate()" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                        <p id="err-start-date" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Ngày bắt đầu hợp đồng là bắt buộc.</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Thời Hạn (Tháng) *</label>
                        <input type="number" name="duration_months" id="contract-duration-months" min="1" max="60" value="6" oninput="calculateContractEndDate()" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="6">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày Kết Thúc *</label>
                        <input type="date" name="end_date" id="contract-end-date" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                        <p id="err-end-date" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Thời hạn kết thúc hợp đồng phải sau ngày bắt đầu hiệu lực.</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tiền Đặt Cọc (VNĐ) *</label>
                        <input type="number" name="deposit" id="contract-deposit" min="0" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all" placeholder="3000000">
                        <p id="err-deposit" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Số tiền đặt cọc hợp đồng phải lớn hơn hoặc bằng 0 đồng.</span>
                        </p>
                    </div>
                </div>
                <div class="contract-section-card rounded-2xl border border-slate-800 bg-slate-950/30 p-4 space-y-4">
                    <h3 class="text-sm font-extrabold text-slate-200">Nội dung thuê và thanh toán</h3>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Địa chỉ nhà/phòng cho thuê *</label>
                        <input type="text" name="rental_address" id="rental-address" placeholder="Địa chỉ nhà/phòng cho thuê *" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                        <p id="err-rental-address" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                            <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Địa chỉ nhà/phòng cho thuê là bắt buộc.</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mô tả diện tích cho thuê</label>
                        <textarea name="rental_area_description" id="rental-area-description" rows="2" placeholder="Diện tích cho thuê" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Trang thiết bị kèm theo</label>
                        <textarea name="equipment_list" rows="4" placeholder="Trang thiết bị kèm theo..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none resize-none"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mục đích thuê</label>
                            <input type="text" name="rental_purpose" value="ở" placeholder="Mục đích thuê" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số người ở</label>
                            <input type="number" name="occupant_count" min="1" max="20" value="1" placeholder="Số người ở" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Giá thuê/tháng *</label>
                            <input type="number" name="rent_price" id="contract-rent-price" placeholder="Giá thuê/tháng *" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                            <p id="err-rent-price" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Giá thuê là bắt buộc và phải lớn hơn 0 đồng.</span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Chu kỳ TT (tháng)</label>
                            <input type="number" name="payment_cycle_months" min="1" max="12" value="3" placeholder="Chu kỳ thanh toán tháng" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Thời điểm TT lần đầu *</label>
                            <input type="text" name="first_payment_date" id="first-payment-date" placeholder="Thời điểm thanh toán lần đầu" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                            <p id="err-first-payment-date" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Thời điểm thanh toán lần đầu là bắt buộc.</span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hình thức thanh toán *</label>
                            <input type="text" name="payment_method" id="payment-method" placeholder="Hình thức thanh toán" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all">
                            <p id="err-payment-method" class="error-message text-xs text-rose-500 mt-1.5 hidden items-center gap-1.5 font-semibold">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> <span>Hình thức thanh toán là bắt buộc.</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleAddContractModal(false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                        Hủy Bỏ
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Tạo Hợp Đồng
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Xác Nhận Ký Hợp Đồng Giấy Trực Tiếp -->
    <div id="modal-confirm-offline-sign" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm hidden p-4">
        <div class="modal-box w-full max-w-lg border border-slate-800 bg-[#0f172a] p-6 rounded-3xl shadow-2xl relative animate-fade-in space-y-5">
            <button type="button" onclick="toggleConfirmOfflineSignModal(false)" class="absolute top-5 right-5 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-100">Xác Nhận Ký Hợp Đồng Trực Tiếp</h3>
                    <p class="text-xs text-slate-400">Ký bằng bản in giấy truyền thống &amp; kích hoạt phòng</p>
                </div>
            </div>

            <form id="form-confirm-offline-sign" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800 space-y-1.5 text-xs text-slate-300">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Mã hợp đồng:</span>
                        <span id="offline-modal-code" class="font-mono font-bold text-indigo-400">HĐ-001</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Cư dân đại diện:</span>
                        <span id="offline-modal-resident" class="font-bold text-slate-200">Nguyễn Văn A</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Phòng lưu trú:</span>
                        <span id="offline-modal-room" class="font-bold text-emerald-400">Phòng 101</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1.5 flex items-center justify-between">
                        <span><i class="fa-solid fa-camera mr-1 text-slate-400"></i> Ảnh chụp / Bản scan HĐ đã ký (Tùy chọn)</span>
                        <span class="text-[10px] text-slate-500 font-normal">PNG, JPG, PDF (Tối đa 5MB)</span>
                    </label>
                    <input type="file" name="scan_file" accept="image/png,image/jpeg,image/webp,application/pdf" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600/20 file:text-indigo-400 hover:file:bg-indigo-600/30 cursor-pointer bg-slate-900 border border-slate-800 rounded-xl p-1.5 focus:outline-none">
                    <p class="text-[11px] text-slate-500 mt-1">Đính kèm ảnh chụp giúp lưu trữ hồ sơ pháp lý đối soát an toàn trên Cloud.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1.5">Ghi chú xác nhận (Tùy chọn):</label>
                    <textarea name="offline_note" rows="2" placeholder="Ghi chú thêm (VD: Hai bên đã ký biên bản bàn giao phòng và nhận đủ tiền cọc)..." class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none resize-none"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" onclick="toggleConfirmOfflineSignModal(false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                        Hủy
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 shadow-lg shadow-amber-600/20 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-check-double"></i>
                        <span>Xác Nhận Đã Ký &amp; Kích Hoạt</span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- RENEW CONTRACT MODAL -->
    <div id="renew-contract-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-3xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 md:p-8 shadow-2xl relative animate-fade-in overflow-y-auto max-h-[90vh]">
            <button onclick="closeRenewContractModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all z-30">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-lg">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                        Xử Lý Gia Hạn & Tái Ký Hợp Đồng
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Duyệt gia hạn trực tiếp thời hạn thuê hoặc khởi tạo hợp đồng tái ký mới.</p>
                </div>
            </div>

            <!-- Resident Renewal Request Info -->
            <div id="renew-request-info-box" class="hidden rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-4 mb-6">
                <h4 class="text-xs font-bold text-indigo-400 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info"></i> Yêu cầu từ Cư dân
                </h4>
                <p class="text-xs text-slate-300 leading-relaxed whitespace-pre-line" id="renew-request-info-text"></p>
            </div>

            <form id="renew-contract-form" method="POST" action="" class="space-y-6" onsubmit="return disableSubmit(this)">
                @csrf
                <!-- Hidden fields containing old contract details -->
                <input type="hidden" name="room_id" id="renew-room-id">
                <input type="hidden" name="resident_id" id="renew-resident-id">
                <input type="hidden" name="lessor_name" id="renew-lessor-name">
                <input type="hidden" name="lessor_phone" id="renew-lessor-phone">
                <input type="hidden" name="lessor_id_number" id="renew-lessor-id-number">
                <input type="hidden" name="lessor_address" id="renew-lessor-address">
                <input type="hidden" name="lessee_name" id="renew-lessee-name">
                <input type="hidden" name="lessee_phone" id="renew-lessee-phone">
                <input type="hidden" name="lessee_id_number" id="renew-lessee-id-number">
                <input type="hidden" name="lessee_permanent_address" id="renew-lessee-permanent-address">
                <input type="hidden" name="lessee_current_address" id="renew-lessee-current-address">
                <input type="hidden" name="rental_address" id="renew-rental-address">
                <input type="hidden" name="rental_area_description" id="renew-rental-area-description">
                <input type="hidden" name="equipment_list" id="renew-equipment-list">
                <input type="hidden" name="rental_purpose" id="renew-rental-purpose">
                <input type="hidden" name="occupant_count" id="renew-occupant-count">
                <input type="hidden" name="first_payment_date" id="renew-first-payment-date">
                <input type="hidden" name="payment_method" id="renew-payment-method">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Phòng trọ</label>
                        <input type="text" id="renew-room-display" readonly class="w-full bg-slate-900/50 border border-slate-800/80 rounded-xl px-4 py-2.5 text-xs text-slate-400 font-bold focus:outline-none">
                    </div>
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Cư dân đại diện</label>
                        <input type="text" id="renew-resident-display" readonly class="w-full bg-slate-900/50 border border-slate-800/80 rounded-xl px-4 py-2.5 text-xs text-slate-400 font-bold focus:outline-none">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Hình thức xử lý</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex flex-col p-4 rounded-2xl bg-slate-950 border border-slate-850 hover:border-slate-700 cursor-pointer select-none group transition-all">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-200">Gia hạn trực tiếp</span>
                                <input type="radio" name="renewal_mode" value="extend" checked onchange="toggleRenewalModeFields('extend')" class="accent-indigo-600">
                            </div>
                            <span class="text-[10px] text-slate-500 mt-2 leading-relaxed">Cập nhật trực tiếp ngày hết hạn của hợp đồng hiện tại. Không cần cư dân ký lại chữ ký số.</span>
                        </label>
                        <label class="relative flex flex-col p-4 rounded-2xl bg-slate-950 border border-slate-850 hover:border-slate-700 cursor-pointer select-none group transition-all">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-200">Tái ký hợp đồng mới</span>
                                <input type="radio" name="renewal_mode" value="new_contract" onchange="toggleRenewalModeFields('new_contract')" class="accent-indigo-600">
                            </div>
                            <span class="text-[10px] text-slate-500 mt-2 leading-relaxed">Kết thúc hợp đồng cũ và tạo một bản hợp đồng điện tử hoàn toàn mới. Cư dân cần ký kết online để kích hoạt.</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div id="renew-start-date-group" class="hidden">
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Ngày bắt đầu mới</label>
                        <input type="date" name="start_date" id="renew-start-date" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold">
                    </div>
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2" id="renew-end-date-label">Ngày kết thúc gia hạn mới</label>
                        <input type="date" name="end_date" id="renew-end-date" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Giá thuê mới (đ/tháng)</label>
                        <input type="number" name="rent_price" id="renew-rent-price" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold">
                    </div>
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Tiền cọc mới (đ)</label>
                        <input type="number" name="deposit" id="renew-deposit" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold">
                    </div>
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Chu kỳ đóng tiền (tháng)</label>
                        <select name="payment_cycle_months" id="renew-payment-cycle-months" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold">
                            <option value="1">1 tháng</option>
                            <option value="2">2 tháng</option>
                            <option value="3">3 tháng</option>
                            <option value="6">6 tháng</option>
                            <option value="12">12 tháng</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 pt-4 border-t border-slate-850">
                    <button type="button" id="renew-decline-btn" class="w-full sm:w-auto px-5 py-3 bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white rounded-xl text-xs font-bold border border-rose-500/20 transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-ban"></i> Từ Chối Yêu Cầu
                    </button>
                    <div class="flex-1"></div>
                    <button type="button" onclick="closeRenewContractModal()" class="w-full sm:w-auto px-5 py-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 rounded-xl text-xs font-bold transition-all">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="submit-btn w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-extrabold shadow-lg shadow-emerald-600/25 transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Xác Nhận Duyệt
                    </button>
                </div>
            </form>
        </div>
    </div>
