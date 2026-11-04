            <!-- SECTION 3: UTILITY RECORD INPUT -->
            <section id="utility-section" class="tab-content hidden space-y-8 animate-fade-in">
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200">Chốt Điện Nước Cuối Tháng (FEAT_15_UTILITY_BULK)</h3>
                            <p class="text-xs text-slate-500">Bảng ghi chỉ số điện nước định kỳ. Đơn giá: Điện 3.500đ/kWh, Nước 15.000đ/m3.</p>
                            
                            <!-- Month Selector (cboMonth) theo Bảng 110 -->
                            <div class="mt-3 flex items-center gap-2.5 flex-wrap">
                                <label for="cboMonth" class="text-xs font-bold text-slate-300 flex items-center gap-1.5 whitespace-nowrap">
                                    <i class="fa-solid fa-calendar-days text-indigo-400"></i> Kỳ ghi chỉ số:
                                </label>
                                <div class="relative">
                                    <input type="month" 
                                           id="cboMonth" 
                                           name="billing_month" 
                                           form="bulk-utility-form" 
                                           value="{{ old('billing_month', \Carbon\Carbon::now()->format('Y-m')) }}" 
                                           placeholder="Tháng 10/2026" 
                                           class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none transition-all shadow-inner">
                                </div>
                                <span id="cboMonth-error" class="hidden text-rose-400 text-xs font-semibold items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i> Vui lòng chọn tháng tính cước hóa đơn điện nước.
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="relative">
                                <input type="text" id="utility-search-input" oninput="filterUtilityRows()" placeholder="Tìm số phòng, tên cư dân..." class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none transition-all placeholder-slate-500 w-44 sm:w-52">
                            </div>
                            <button type="button" onclick="openIotDashboardModal()" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:opacity-90 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-tower-broadcast animate-pulse"></i> 📡 IoT Smart Metering
                            </button>
                            <button type="button" onclick="triggerIotAutoSync(this)" class="px-4 py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 hover:opacity-90 text-white rounded-xl text-xs font-bold shadow-lg shadow-cyan-600/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-bolt-lightning"></i> ⚡ Chốt IoT
                            </button>
                            <button type="button" onclick="triggerAutoRemind(this)" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-rose-600/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-bell"></i> Nhắc Zalo
                            </button>
                            <button type="button" onclick="openBulkOcrModal('electricity')" class="px-4 py-2.5 bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500 hover:opacity-90 text-white rounded-xl text-xs font-bold shadow-lg shadow-orange-500/25 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-camera"></i> ⚡ AI Quét OCR
                            </button>
                            <!-- Nút Tạo hóa đơn tháng theo Bảng 118 (Trigger) -->
                            <button type="button" onclick="openInvoiceModal()" class="px-4 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-900/30 transition-all flex items-center gap-2" title="Tính tiền phòng & xuất hóa đơn tự động (FEAT_17_BILLING)">
                                <i class="fa-solid fa-file-invoice-dollar"></i> Tạo hóa đơn tháng
                            </button>
                            <!-- Nút Lưu toàn bộ chỉ số (btnSaveAll) nền xanh lá theo Bảng 110 -->
                            <button type="submit" form="bulk-utility-form" id="btnSaveAll" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i> Lưu toàn bộ chỉ số
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <form id="bulk-utility-form" action="{{ route('smartroom.admin.utility.bulk_store', [], false) }}" method="POST">
                            @csrf
                            <!-- Bảng ghi số tblUtility theo Bảng 110 -->
                            <table id="tblUtility" class="w-full text-left text-sm text-slate-300">
                                <thead class="text-xs text-slate-500 uppercase bg-slate-900/50 border-b border-slate-900">
                                    <tr>
                                        <th class="px-6 py-4 font-bold">Tên phòng</th>
                                        <th class="px-6 py-4 font-bold">Số điện cũ</th>
                                        <th class="px-6 py-4 font-bold">Số điện mới (*)</th>
                                        <th class="px-6 py-4 font-bold">Số nước cũ</th>
                                        <th class="px-6 py-4 font-bold">Số nước mới (*)</th>
                                        <th class="px-6 py-4 font-bold">Tiêu thụ</th>
                                        <th class="px-6 py-4 font-bold">Thành tiền tạm tính</th>
                                        <th class="px-6 py-4 font-bold text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-900" id="utility-table-body">
                                    @foreach($utilityRooms as $room)
                                    @php
                                        $resident = $room->residents->first();
                                        $latestBill = $room->utilityRecords->first();
                                        $currentMonth = \Carbon\Carbon::now()->format('Y-m');
                                        
                                        if ($latestBill && $latestBill->billing_month === $currentMonth) {
                                            $oldElec = $latestBill->old_electricity;
                                            $oldWater = $latestBill->old_water;
                                            $newElec = $latestBill->new_electricity;
                                            $newWater = $latestBill->new_water;
                                        } else {
                                            $oldElec = $latestBill ? $latestBill->new_electricity : 0;
                                            $oldWater = $latestBill ? $latestBill->new_water : 0;
                                            $newElec = '';
                                            $newWater = '';
                                        }
                                        $statusColor = $room->status === 'overdue' ? 'bg-amber-500' : ($room->status === 'empty' ? 'bg-emerald-500' : 'bg-red-500');
                                    @endphp
                                    <tr class="hover:bg-slate-900/10 transition-all" data-room-id="{{ $room->id }}" data-room="{{ $room->room_number }}" data-price="{{ $room->price }}" data-electric-serial="{{ $room->electric_meter_serial ?? '' }}" data-water-serial="{{ $room->water_meter_serial ?? '' }}">
                                        <td class="px-6 py-4 text-slate-200">
                                            <div class="flex items-center gap-2 font-bold">
                                                <span class="w-2.5 h-2.5 rounded-full {{ $statusColor }}"></span> 
                                                <span>{{ $room->room_number }} ({{ $resident ? $resident->name : 'N/A' }})</span>
                                                @if($room->latestElectricTelemetry || $room->latestWaterTelemetry)
                                                    <button type="button" onclick="viewRoomIotChart('{{ $room->id }}', '{{ $room->room_number }}')" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-500/10 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/20 text-[9px] font-semibold transition-all shadow-sm" title="Công tơ thông minh IoT chu kỳ 15 phút. Bấm để xem đồ thị phụ tải">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> IoT Online
                                                    </button>
                                                @endif
                                            </div>
                                            <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-1 font-mono flex-wrap">
                                                @if($room->electric_meter_serial)
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20" title="Số SX Công tơ Điện: {{ $room->electric_meter_serial }}">
                                                        <i class="fa-solid fa-bolt"></i> {{ $room->electric_meter_serial }}
                                                    </span>
                                                @endif
                                                @if($room->water_meter_serial)
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20" title="Số SX Đồng hồ Nước: {{ $room->water_meter_serial }}">
                                                        <i class="fa-solid fa-droplet"></i> {{ $room->water_meter_serial }}
                                                    </span>
                                                @endif
                                                @if(!$room->electric_meter_serial && !$room->water_meter_serial)
                                                    <a href="{{ route('admin.rooms.edit', $room->id) }}" class="text-[9px] text-indigo-400 hover:underline italic" title="Bấm vào đây để cài đặt Số SX công tơ cho phòng">+ Cài Số SX</a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500" data-field="old-elec">{{ $oldElec }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-1.5">
                                                <input type="number" name="utilities[{{ $room->id }}][new_electricity]" value="{{ $newElec }}" oninput="calculateRowCost(this)" class="new-elec-input w-24 px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none transition-colors" placeholder="Số mới">
                                                @if($room->latestElectricTelemetry)
                                                    <button type="button" onclick="applyIotReadingToInput(this, '{{ (int) round($room->latestElectricTelemetry->reading) }}')" class="w-7 h-7 flex items-center justify-center rounded-lg bg-emerald-500/10 hover:bg-emerald-600 text-emerald-400 hover:text-white border border-emerald-500/20 text-xs transition-all shadow-sm" title="Lấy số điện từ IoT Smart Meter: {{ $room->latestElectricTelemetry->reading }} kWh">
                                                        <i class="fa-solid fa-bolt-lightning"></i>
                                                    </button>
                                                @endif
                                                <button type="button" onclick="openMeterOcrModal('{{ $room->id }}', '{{ $room->room_number }}', 'electricity', this)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-indigo-500/10 hover:bg-indigo-600 text-indigo-400 hover:text-white border border-indigo-500/20 text-xs transition-all shadow-sm" title="Quét số điện bằng AI OCR Camera">
                                                    <i class="fa-solid fa-camera"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500" data-field="old-water">{{ $oldWater }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-1.5">
                                                <input type="number" name="utilities[{{ $room->id }}][new_water]" value="{{ $newWater }}" oninput="calculateRowCost(this)" class="new-water-input w-24 px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none transition-colors" placeholder="Số mới">
                                                @if($room->latestWaterTelemetry)
                                                    <button type="button" onclick="applyIotReadingToInput(this, '{{ (int) round($room->latestWaterTelemetry->reading) }}')" class="w-7 h-7 flex items-center justify-center rounded-lg bg-cyan-500/10 hover:bg-cyan-600 text-cyan-400 hover:text-white border border-cyan-500/20 text-xs transition-all shadow-sm" title="Lấy số nước từ IoT Smart Meter: {{ $room->latestWaterTelemetry->reading }} m3">
                                                        <i class="fa-solid fa-droplet"></i>
                                                    </button>
                                                @endif
                                                <button type="button" onclick="openMeterOcrModal('{{ $room->id }}', '{{ $room->room_number }}', 'water', this)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-cyan-500/10 hover:bg-cyan-600 text-cyan-400 hover:text-white border border-cyan-500/20 text-xs transition-all shadow-sm" title="Quét số nước bằng AI OCR Camera">
                                                    <i class="fa-solid fa-camera"></i>
                                                </button>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-xs text-slate-400">
                                            <div class="flex items-center gap-1.5">
                                                <span>⚡ Điện: <strong data-field="used-elec">0</strong> kWh</span>
                                                <span class="abnormal-elec-warning hidden text-amber-400 font-bold text-[10px] items-center gap-1 bg-amber-500/10 border border-amber-500/20 px-1.5 py-0.5 rounded" title="Lượng điện tiêu thụ tăng đột biến (> 1000 kWh)!">
                                                    <i class="fa-solid fa-triangle-exclamation animate-bounce"></i> Bất thường
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span>💧 Nước: <strong data-field="used-water">0</strong> m3</span>
                                                <span class="abnormal-water-warning hidden text-amber-400 font-bold text-[10px] items-center gap-1 bg-amber-500/10 border border-amber-500/20 px-1.5 py-0.5 rounded" title="Lượng nước tiêu thụ tăng đột biến (> 100 m3)!">
                                                    <i class="fa-solid fa-triangle-exclamation animate-bounce"></i> Bất thường
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-indigo-400 font-bold text-xs" data-field="cost-total">0đ</td>
                                        <td class="px-6 py-4 text-center">
                                             <div class="flex items-center justify-center gap-2">
                                                 <button type="button" onclick="openInvoiceModal('{{ $room->id }}', '{{ $room->room_number }}', '{{ $resident ? $resident->name : '' }}', '{{ $room->price }}')" class="px-2.5 py-1.5 bg-blue-900/30 hover:bg-blue-900 text-blue-300 hover:text-white rounded-lg text-xs font-bold border border-blue-800/40 transition-all flex items-center gap-1" title="Tạo hóa đơn & VietQR (FEAT_17_BILLING)">
                                                     <i class="fa-solid fa-file-invoice-dollar"></i> Hóa đơn
                                                 </button>
                                                 @if($room->status === 'overdue' && $latestBill && $latestBill->status !== 'paid')
                                                     <form action="{{ route('smartroom.admin.utility.pay', $latestBill->id, false) }}" method="POST" class="inline">
                                                         @csrf
                                                         <button type="submit" class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white rounded-lg text-xs font-bold border border-emerald-500/20 transition-all flex items-center gap-1">
                                                             <i class="fa-solid fa-check"></i> Xác nhận đóng
                                                         </button>
                                                     </form>
                                                     <a href="{{ route('smartroom.admin.utility.print', $latestBill->id) }}" target="_blank" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-bold border border-slate-700 transition-all flex items-center gap-1" title="In hóa đơn">
                                                         <i class="fa-solid fa-print"></i> In
                                                     </a>
                                                 @else
                                                     <button type="button" onclick="saveSingleUtility('{{ $room->id }}', this)" class="px-3 py-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-400 hover:text-white rounded-lg text-xs font-bold border border-indigo-500/20 transition-all">
                                                         <i class="fa-solid fa-save"></i> Lưu số
                                                     </button>
                                                 @endif
                                             </div>
                                         </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>

            </section>

