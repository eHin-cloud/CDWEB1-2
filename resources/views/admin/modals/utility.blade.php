    <!-- UTILITY MODALS (MOVED OUT OF TRANSFORMED SECTIONS TO ROOT VIEWPORT) -->
                <!-- MODAL IOT SMART METERING & REALTIME DASHBOARD -->
                <div id="iot-metering-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/85 backdrop-blur-md p-4 overflow-y-auto">
                    <div class="relative w-full max-w-5xl bg-slate-900/95 border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-6 my-auto max-h-[92vh] overflow-y-auto text-slate-200">
                        <!-- Header -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-5">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 via-teal-500 to-cyan-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/25">
                                    <i class="fa-solid fa-tower-broadcast text-xl animate-pulse"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-base font-bold text-slate-100">IoT Smart Metering & Giám Sát Thời Gian Thực</h3>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Chu kỳ 1 phút/lần (Chế độ Test)
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">Tích hợp công tơ điện tử thông minh truyền không dây qua LoRaWAN / ESP32 WiFi / Modbus RS485 / Zigbee (Chu kỳ test: 1 phút)</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="loadIotSummary()" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold border border-slate-700 transition-all flex items-center gap-1.5" title="Tải lại dữ liệu">
                                    <i class="fa-solid fa-arrows-rotate"></i> Làm mới
                                </button>
                                <button type="button" onclick="closeIotDashboardModal()" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-slate-200 border border-slate-700 flex items-center justify-center transition-all">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- KPI Summary Cards -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5">
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                                <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                                    <span>Công Suất Tức Thời</span>
                                    <i class="fa-solid fa-bolt text-amber-400"></i>
                                </div>
                                <div class="text-2xl font-black text-amber-300 font-mono" id="iot-kpi-power">0.00 <span class="text-xs font-bold text-slate-400">kW</span></div>
                                <div class="text-[10px] text-slate-500 mt-1">Toàn bộ phòng trọ</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                                <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                                    <span>Lưu Lượng Nước</span>
                                    <i class="fa-solid fa-droplet text-cyan-400"></i>
                                </div>
                                <div class="text-2xl font-black text-cyan-300 font-mono" id="iot-kpi-water">0.00 <span class="text-xs font-bold text-slate-400">m3/h</span></div>
                                <div class="text-[10px] text-slate-500 mt-1">Lưu lượng tức thời</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                                <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                                    <span>Công Tơ Đang Kết Nối</span>
                                    <i class="fa-solid fa-wifi text-emerald-400"></i>
                                </div>
                                <div class="text-2xl font-black text-emerald-300 font-mono" id="iot-kpi-devices">0 / 0</div>
                                <div class="text-[10px] text-emerald-400/80 mt-1" id="iot-kpi-status-hint">Trạng thái Online</div>
                            </div>
                            <div onclick="switchIotTab('anomalies')" class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80 hover:border-rose-500/60 hover:bg-rose-950/20 cursor-pointer transition-all group relative overflow-hidden" title="Bấm vào để xem chi tiết lý do cảnh báo, phòng xảy ra và thời gian phát sinh">
                                <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                                    <span class="group-hover:text-rose-300 font-semibold transition-colors">Cảnh Báo Dị Thường</span>
                                    <i class="fa-solid fa-triangle-exclamation text-rose-400 group-hover:scale-110 transition-transform"></i>
                                </div>
                                <div class="flex items-baseline justify-between">
                                    <div class="text-2xl font-black text-rose-400 font-mono" id="iot-kpi-warnings">0</div>
                                    <span class="text-[10px] text-slate-500 group-hover:text-rose-400/90 transition-colors flex items-center gap-1 font-semibold">
                                        Xem chi tiết <i class="fa-solid fa-chevron-right text-[8px]"></i>
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-500 mt-1">Quá tải / Rò rỉ nước / Điện áp</div>
                            </div>
                        </div>

                        <!-- Tab Navigation -->
                        <div class="flex items-center gap-2 border-b border-slate-800 pb-1 flex-wrap">
                            <button type="button" onclick="switchIotTab('realtime')" id="iot-tab-btn-realtime" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-emerald-500/10 text-emerald-300 border border-emerald-500/30">
                                <i class="fa-solid fa-chart-line"></i> Biểu Đồ Phụ Tải (Realtime)
                            </button>
                            <button type="button" onclick="switchIotTab('devices')" id="iot-tab-btn-devices" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-slate-200 border border-transparent">
                                <i class="fa-solid fa-microchip"></i> Danh Sách Công Tơ IoT
                            </button>
                            <button type="button" onclick="switchIotTab('simulator')" id="iot-tab-btn-simulator" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-slate-200 border border-transparent">
                                <i class="fa-solid fa-flask-vial"></i> Trình Giả Lập 1 Phút (Simulator)
                            </button>
                            <button type="button" onclick="switchIotTab('anomalies')" id="iot-tab-btn-anomalies" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-slate-200 border border-transparent">
                                <i class="fa-solid fa-triangle-exclamation text-rose-400"></i> Cảnh Báo Dị Thường
                                <span id="iot-tab-anomalies-badge" class="hidden px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30">0</span>
                            </button>
                        </div>

                        <!-- Tab 1: Realtime Load Curves -->
                        <div id="iot-tab-realtime" class="space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-950/50 p-3 rounded-2xl border border-slate-800/80">
                                <div class="flex items-center gap-2">
                                    <label class="text-xs text-slate-400 font-semibold">Chọn phòng:</label>
                                    <select id="iot-room-filter-select" onchange="onIotRoomFilterChange(this.value)" class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none">
                                        <option value="">-- Chọn phòng xem biểu đồ --</option>
                                        @foreach($utilityRooms as $r)
                                            <option value="{{ $r->id }}">Phòng {{ $r->room_number }} ({{ $r->residents->first()?->name ?? 'Trống' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs text-slate-400">Khung thời gian:</span>
                                    <button type="button" onclick="changeIotChartRange('15m')" id="btn-range-15m" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-900 text-slate-400 hover:bg-slate-800 border border-slate-800">15 Phút</button>
                                    <button type="button" onclick="changeIotChartRange('1h')" id="btn-range-1h" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">1 Giờ (Từng phút)</button>
                                    <button type="button" onclick="changeIotChartRange('24h')" id="btn-range-24h" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-900 text-slate-400 hover:bg-slate-800 border border-slate-800">24 Giờ</button>
                                    <button type="button" onclick="changeIotChartRange('7d')" id="btn-range-7d" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-900 text-slate-400 hover:bg-slate-800 border border-slate-800">7 Ngày</button>
                                </div>
                            </div>


                            <!-- Interactive Chart Canvas -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                        <h4 class="text-xs font-bold text-slate-300" id="iot-chart-title">Đường Cong Phụ Tải Điện (W) & Lưu Lượng Nước (L/phút) Theo Chu Kỳ 15 Phút</h4>
                                    </div>
                                    <div class="flex items-center gap-3 text-[11px]">
                                        <span class="inline-flex items-center gap-1 text-amber-400"><span class="w-2.5 h-0.5 bg-amber-400"></span> Công suất điện (W)</span>
                                        <span class="inline-flex items-center gap-1 text-cyan-400"><span class="w-2.5 h-0.5 bg-cyan-400"></span> Nước (L/m)</span>
                                        <button type="button" onclick="quickInjectDemoData()" class="ml-2 px-2.5 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 text-[11px] font-bold flex items-center gap-1.5 transition-all shadow-sm" title="Phát ngay 1 gói tin mẫu cho phòng đang chọn">
                                            <i class="fa-solid fa-bolt text-amber-400"></i> Bắn Gói Tin Mẫu
                                        </button>
                                    </div>
                                </div>

                                <div class="relative w-full h-64 bg-slate-900/40 rounded-xl border border-slate-800/50 p-2 overflow-hidden">
                                    <canvas id="iot-realtime-chart-canvas" class="w-full h-full block cursor-crosshair"></canvas>

                                    <!-- Interactive Hover Tooltip Box -->
                                    <div id="iot-chart-tooltip" class="absolute pointer-events-none hidden z-20 px-3.5 py-2.5 bg-slate-950/95 border border-slate-700/90 rounded-2xl shadow-2xl shadow-black text-xs backdrop-blur-md transition-all duration-75 space-y-1.5 min-w-[185px]">
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-1 text-[11px] font-mono">
                                            <span class="flex items-center gap-1.5 text-slate-200 font-bold"><i class="fa-regular fa-clock text-emerald-400"></i> <span id="iot-tt-time">--:--:--</span></span>
                                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-800 text-emerald-400 font-bold">1 Phút</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-4 text-xs font-mono">
                                            <span class="text-amber-400 flex items-center gap-1.5 font-sans"><i class="fa-solid fa-bolt text-[10px]"></i> Công suất:</span>
                                            <strong id="iot-tt-power" class="text-amber-300 font-bold">0 W</strong>
                                        </div>
                                        <div class="flex items-center justify-between gap-4 text-xs font-mono">
                                            <span class="text-cyan-400 flex items-center gap-1.5 font-sans"><i class="fa-solid fa-droplet text-[10px]"></i> Lưu lượng:</span>
                                            <strong id="iot-tt-water" class="text-cyan-300 font-bold">0.0 L/m</strong>
                                        </div>
                                        <div id="iot-tt-warning-banner" class="hidden py-1 px-2 rounded-lg bg-rose-500/20 border border-rose-500/30 text-rose-300 text-[10px] font-bold flex items-center gap-1">
                                            <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                                            <span id="iot-tt-warning-text">Cảnh báo dị thường</span>
                                        </div>
                                        <div class="pt-1 border-t border-slate-800/80 space-y-1 text-[10px] font-mono">
                                            <div class="flex items-center justify-between text-slate-400">
                                                <span class="font-sans">Chỉ số điện:</span>
                                                <span id="iot-tt-reading" class="text-slate-200 font-bold">0.00 kWh</span>
                                            </div>
                                            <div class="flex items-center justify-between text-slate-400">
                                                <span class="font-sans">Chỉ số nước:</span>
                                                <span id="iot-tt-water-reading" class="text-cyan-300 font-bold">0.00 m3</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="iot-chart-empty-state" class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-slate-500 text-xs bg-slate-950/90 rounded-xl">
                                        <i class="fa-solid fa-chart-area text-3xl text-slate-600"></i>
                                        <span class="text-slate-400">Phòng này chưa có dữ liệu đo đạc nào</span>
                                        <button type="button" onclick="quickInjectDemoData()" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-2 shadow-lg shadow-emerald-600/30 transition-all">
                                            <i class="fa-solid fa-bolt"></i> Tạo Ngay Điểm Đo Mẫu
                                        </button>
                                    </div>
                                </div>

                                <!-- Current Room Stats Summary -->
                                <div id="iot-room-stats-banner" class="hidden grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-xs">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 block text-[10px]">Chỉ số điện tích lũy:</span>
                                        <strong id="iot-stat-elec-val" class="text-emerald-400 font-mono text-sm">0 kWh</strong>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 block text-[10px]">Chỉ số nước tích lũy:</span>
                                        <strong id="iot-stat-water-val" class="text-cyan-400 font-mono text-sm">0 m3</strong>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 block text-[10px]">Lưu lượng nước tức thời:</span>
                                        <strong id="iot-stat-water-flow" class="text-sky-300 font-mono text-sm">0.0 L/m</strong>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 block text-[10px]">Ước tính tiêu thụ hôm nay:</span>
                                        <strong id="iot-stat-cost-val" class="text-indigo-400 font-mono text-sm">0đ</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Devices Fleet -->
                        <div id="iot-tab-devices" class="hidden space-y-4">
                            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                                <table class="w-full text-left text-xs text-slate-300">
                                    <thead class="bg-slate-950/70 text-[11px] text-slate-400 uppercase border-b border-slate-800">
                                        <tr>
                                            <th class="px-4 py-3">Mã Thiết Bị</th>
                                            <th class="px-4 py-3">Giao Thức</th>
                                            <th class="px-4 py-3">Phòng Gán</th>
                                            <th class="px-4 py-3">Số SX Công Tơ</th>
                                            <th class="px-4 py-3">Loại</th>
                                            <th class="px-4 py-3">Chỉ Số Gần Nhất</th>
                                            <th class="px-4 py-3">Trạng Thái</th>
                                            <th class="px-4 py-3">Lần Cuối Nhận Tin</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60" id="iot-devices-table-body">
                                        <tr>
                                            <td colspan="8" class="text-center py-6 text-slate-500">Đang tải danh sách thiết bị...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: Hardware Simulator -->
                        <div id="iot-tab-simulator" class="hidden space-y-4">
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-4">
                                <div class="flex items-center gap-2 text-xs font-bold text-amber-300">
                                    <i class="fa-solid fa-flask text-sm"></i>
                                    <span>Bộ Giả Lập Phát Sóng Gói Tin IoT (Hardware Telemetry Simulator)</span>
                                </div>
                                <p class="text-xs text-slate-400">Tính năng này cho phép bạn giả lập một vi điều khiển (ESP32 / LoRaWAN Node) gửi gói tin đo đạc về máy chủ theo chu kỳ 15 phút để kiểm thử hệ thống ngay trên trình duyệt mà không cần phần cứng thật.</p>

                                <form id="iot-simulator-form" onsubmit="sendIotSimulation(event)" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                    <div>
                                        <label class="block text-slate-400 mb-1 font-semibold">Phòng nhận dữ liệu <span class="text-rose-400">*</span></label>
                                        <select name="room_id" id="sim-room-id" required class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-emerald-500 focus:outline-none">
                                            @foreach($utilityRooms as $r)
                                                <option value="{{ $r->id }}">Phòng {{ $r->room_number }} (Số SX Điện: {{ $r->electric_meter_serial ?: 'Chưa có' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 mb-1 font-semibold">Loại công tơ</label>
                                        <select name="meter_type" id="sim-meter-type" onchange="onSimMeterTypeChange(this.value)" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-emerald-500 focus:outline-none">
                                            <option value="electricity">⚡ Điện (PZEM-004T / Modbus)</option>
                                            <option value="water">💧 Nước (Pulse Counter / Ultrasonic)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 mb-1 font-semibold">Giao thức truyền thông</label>
                                        <select name="protocol" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-emerald-500 focus:outline-none">
                                            <option value="esp32_wifi">ESP32 (WiFi / HTTP Webhook)</option>
                                            <option value="lorawan">LoRaWAN Long-Range 868MHz</option>
                                            <option value="modbus_rs485">Modbus RS485 Bus RTU</option>
                                            <option value="zigbee">Zigbee 3.0 Mesh</option>
                                            <option value="mqtt">MQTT Broker Ingestion</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 mb-1 font-semibold">Chỉ số tích lũy mới (kWh hoặc m3)</label>
                                        <input type="number" step="0.01" name="reading" id="sim-reading" value="1420.5" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-emerald-500 focus:outline-none font-mono" placeholder="Chỉ số công tơ">
                                    </div>
                                    <div id="sim-elec-inputs" class="contents">
                                        <div>
                                            <label class="block text-slate-400 mb-1 font-semibold">Điện áp tức thời (V)</label>
                                            <input type="number" step="0.1" name="voltage" value="221.8" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-emerald-500 focus:outline-none font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-400 mb-1 font-semibold">Công suất tức thời (W)</label>
                                            <input type="number" step="1" name="power" value="780" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-emerald-500 focus:outline-none font-mono">
                                        </div>
                                    </div>
                                    <div id="sim-water-inputs" class="hidden">
                                        <label class="block text-slate-400 mb-1 font-semibold">Lưu lượng nước tức thời (L/phút)</label>
                                        <input type="number" step="0.1" name="flow_rate" value="1.8" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-cyan-500 focus:outline-none font-mono">
                                    </div>

                                    <div class="md:col-span-3 flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-800">
                                        <div class="flex items-center gap-2.5">
                                            <button type="button" id="btn-auto-sim" onclick="toggleAutoSimulation()" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-amber-300 border border-amber-500/30 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                                                <i class="fa-solid fa-play text-amber-400" id="auto-sim-icon"></i> <span id="auto-sim-text">Bật Tự Động Bắn Gói Tin (Mỗi 1 Phút)</span>
                                            </button>
                                            <span id="auto-sim-counter" class="hidden px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                Đã gửi: <span id="auto-sim-count">0</span> gói
                                            </span>
                                        </div>
                                        <button type="submit" id="btn-send-sim" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:opacity-90 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/25 transition-all flex items-center gap-2">
                                            <i class="fa-solid fa-satellite-dish"></i> 🚀 Bắn 1 Gói Tin Thử Ngay
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>

                        <!-- Tab 4: Cảnh Báo Dị Thường (Anomalies & Warnings Detail) -->
                        <div id="iot-tab-anomalies" class="hidden space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 text-lg shrink-0">
                                        <i class="fa-solid fa-triangle-exclamation animate-pulse"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-100 flex items-center gap-2 flex-wrap">
                                            <span>Nhật Ký Cảnh Báo Dị Thường & An Toàn Điện Nước</span>
                                            <span id="anomalies-total-badge" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30">0 Cảnh báo</span>
                                        </h4>
                                        <p class="text-xs text-slate-400 mt-0.5">Phân tích chuyên sâu từng điểm đo: xác định chính xác thời gian phát sinh, nguyên nhân kỹ thuật và khuyến nghị hành động.</p>
                                    </div>
                                </div>
                                <button type="button" onclick="loadIotSummary()" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-700 text-xs font-semibold flex items-center gap-1.5 transition-all self-start sm:self-auto shrink-0 shadow-sm" title="Tải lại cảnh báo mới nhất">
                                    <i class="fa-solid fa-rotate"></i> Cập nhật
                                </button>
                            </div>

                            <!-- Danh sách các cảnh báo (Render động qua JavaScript) -->
                            <div id="iot-anomalies-container" class="space-y-3.5">
                                <div class="p-8 text-center text-slate-500 text-xs rounded-2xl bg-slate-950/40 border border-slate-800">
                                    <i class="fa-solid fa-circle-notch fa-spin text-xl text-slate-400 mb-2"></i>
                                    <p>Đang tải dữ liệu cảnh báo dị thường từ hệ thống...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-800">
                            <span class="text-xs text-slate-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-400"></i> Máy chủ Ingestion sẵn sàng nhận gói tin tại: <code class="bg-slate-950 px-2 py-0.5 rounded font-mono text-[11px] text-emerald-300">/api/v1/iot/telemetry</code>
                            </span>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <button type="button" onclick="closeIotDashboardModal()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all">
                                    Đóng lại
                                </button>
                                <button type="button" onclick="triggerIotAutoSync(this)" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:opacity-90 text-white text-xs font-bold shadow-lg shadow-cyan-600/20 transition-all flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-bolt-lightning"></i> ⚡ Chốt Số Tự Động Sang Hóa Đơn
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL AI OCR CAMERA QUÉT CÔNG TƠ ĐIỆN NƯỚC -->

                <div id="meter-ocr-modal" class="fixed inset-0 z-[100] bg-[#04060b]/80 backdrop-blur-md hidden items-center justify-center p-4 transition-all duration-300 overflow-y-auto">
                    <div class="glass-card w-full max-w-lg rounded-3xl border border-slate-800 p-6 space-y-5 shadow-2xl relative my-auto max-h-[92vh] overflow-y-auto animate-fade-in bg-[#0a0f1d]/95">
                        <!-- Header -->
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500/20 via-purple-500/20 to-pink-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 shadow-inner">
                                    <i class="fa-solid fa-wand-magic-sparkles text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                                        AI OCR Quét Công Tơ
                                        <span id="ocr-meter-type-badge" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Điện</span>
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Phòng <strong id="ocr-target-room-text" class="text-slate-200">101</strong> — Google Gemini Vision AI bóc tách chỉ số</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeMeterOcrModal()" class="w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Source Switch Tabs -->
                        <div class="grid grid-cols-2 gap-2 p-1 rounded-xl bg-slate-950 border border-slate-900">
                            <button type="button" id="tab-btn-camera" onclick="switchOcrSource('camera')" class="py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 bg-indigo-600 text-white shadow-sm">
                                <i class="fa-solid fa-camera"></i> Camera Trực Tiếp
                            </button>
                            <button type="button" id="tab-btn-upload" onclick="switchOcrSource('upload')" class="py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 text-slate-400 hover:text-slate-200">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Tải Ảnh Có Sẵn
                            </button>
                        </div>

                        <!-- Viewport Box: Camera Stream & Image Preview -->
                        <div class="relative w-full rounded-2xl bg-slate-950 border border-slate-800/80 overflow-hidden flex items-center justify-center min-h-[220px]">
                            <!-- Video Stream -->
                            <video id="ocr-camera-video" autoplay playsinline class="w-full h-56 object-cover hidden"></video>

                            <!-- Image Preview (img_preview - Bảng 116) -->
                            <img id="img_preview" src="" alt="Ảnh công tơ" class="w-full h-56 object-contain hidden">
                            <img id="ocr-image-preview" src="" alt="Meter Preview" class="hidden">

                            <!-- Upload Zone -->
                            <div id="ocr-upload-zone" onclick="document.getElementById('camera_input').click()" class="p-8 text-center cursor-pointer hover:bg-slate-900/30 transition-all flex flex-col items-center justify-center gap-2 hidden w-full h-56 border-2 border-dashed border-slate-700/60 rounded-xl">
                                <div class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-image text-xl"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-300">Chụp ảnh / Tải ảnh * (Bấm để chọn file)</p>
                                <p class="text-[10px] text-slate-500">Hỗ trợ định dạng JPG, PNG, WEBP (Tối đa 10MB)</p>
                            </div>

                            <!-- Laser Scanning Animation -->
                            <div id="ocr-laser-line" class="hidden absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent shadow-[0_0_20px_#22d3ee] animate-pulse"></div>

                            <!-- AI Processing Overlay -->
                            <div id="ocr-loading-overlay" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm hidden flex flex-col items-center justify-center gap-3">
                                <div class="w-10 h-10 border-3 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                                <span class="text-xs font-bold text-slate-200">Gemini Vision AI đang đọc chỉ số công tơ...</span>
                                <span class="text-[10px] text-indigo-400">Quét số nguyên từ mặt đồng hồ</span>
                            </div>

                            <!-- Camera Permission / Error Warning -->
                            <div id="ocr-camera-error" class="p-6 text-center hidden flex flex-col items-center justify-center gap-2">
                                <i class="fa-solid fa-video-slash text-2xl text-rose-400 mb-1"></i>
                                <p class="text-xs font-bold text-slate-300">Không thể mở Camera thiết bị</p>
                                <p class="text-[10px] text-slate-500 max-w-xs">Vui lòng cấp quyền truy cập camera trên trình duyệt hoặc chuyển sang tab <strong>Tải Ảnh Có Sẵn</strong>.</p>
                            </div>
                        </div>

                        <!-- Cảnh báo lỗi OCR động (ERR_16_01, ERR_16_02, ERR_16_03) -->
                        <div id="ocr-error-banner" class="hidden p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-xs text-rose-400 flex items-center gap-2 animate-fade-in">
                            <i class="fa-solid fa-circle-exclamation text-rose-500 shrink-0"></i>
                            <span id="ocr-error-banner-text">Vui lòng chọn hoặc kéo thả ít nhất một ảnh chụp mặt công tơ.</span>
                        </div>

                        <!-- Hidden Form Elements: camera_input theo Bảng 116 -->
                        <canvas id="ocr-capture-canvas" class="hidden"></canvas>
                        <input type="file" id="camera_input" name="meter_image" accept="image/*" class="hidden" onchange="handleOcrFileSelected(this)">
                        <input type="file" id="ocr-file-input" accept="image/*" class="hidden" onchange="handleOcrFileSelected(this)">

                        <!-- Intermediate Actions: Capture & Retake -->
                        <div class="flex gap-2">
                            <button type="button" id="btn-ocr-capture" onclick="captureOcrSnapshot()" class="flex-1 py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/25">
                                <i class="fa-solid fa-camera"></i> Chụp Ảnh Mặt Đồng Hồ
                            </button>
                            <button type="button" id="btn-ocr-retake" onclick="resetOcrCapture()" class="hidden py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-bold transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-rotate-left"></i> Chụp Lại
                            </button>
                            <button type="button" id="btn-ocr-analyze" onclick="submitOcrAnalysis()" class="hidden flex-1 py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:opacity-90 text-white text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-purple-600/25">
                                <i class="fa-solid fa-sparkles"></i> Phân Tích Với AI Gemini
                            </button>
                        </div>

                        <!-- Result Display Box: ocr_result theo Bảng 116 -->
                        <div id="ocr-result-box" class="hidden p-4 rounded-2xl bg-slate-950/80 border border-emerald-500/30 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Số nhận diện được</span>
                                <span id="ocr-confidence-badge" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    Độ tin cậy: 95%
                                </span>
                            </div>
                            <div class="flex items-baseline justify-between">
                                <span class="text-xs text-slate-500">Chỉ số công tơ đọc được:</span>
                                <div class="flex items-baseline gap-1">
                                    <input type="text" id="ocr_result" placeholder="VD: 14852" class="text-3xl font-extrabold text-emerald-400 font-mono tracking-tight bg-transparent border-0 focus:outline-none w-36 text-right" readonly value="0">
                                    <span id="ocr-result-value" class="hidden">0</span>
                                    <span id="ocr-result-unit" class="text-xs font-bold text-slate-400">kWh</span>
                                </div>
                            </div>
                            <div id="ocr-fallback-hint" class="hidden p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-[10px] text-amber-300">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                <span>AI nhận diện độ nét chưa tối ưu. Vui lòng kiểm tra lại ảnh chụp hoặc chỉnh tay nếu cần.</span>
                            </div>
                        </div>

                        <!-- Footer Actions: btnConfirmOCR nền xanh lá theo Bảng 116 -->
                        <div class="flex gap-2 pt-2 border-t border-slate-900">
                            <button type="button" onclick="closeMeterOcrModal()" class="w-1/3 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 text-xs font-bold transition-all">
                                Đóng lại
                            </button>
                            <button type="button" id="btnConfirmOCR" onclick="applyOcrResultToInput()" disabled class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 disabled:bg-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed hover:bg-emerald-500 text-white text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20">
                                <i class="fa-solid fa-check"></i> Xác nhận áp dụng số
                            </button>
                            <button type="button" id="btn-ocr-apply" onclick="applyOcrResultToInput()" class="hidden"></button>
                        </div>
                    </div>
                </div>

                <!-- Modal: AI Bulk OCR Scanning (Quét Hàng Loạt & Khớp Phòng) -->
                <div id="bulk-meter-ocr-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 overflow-y-auto">
                    <div class="relative w-full max-w-4xl bg-slate-900/95 border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-5 my-auto max-h-[92vh] overflow-y-auto">
                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 flex items-center justify-center text-white shadow-lg shadow-orange-500/25">
                                    <i class="fa-solid fa-bolt-lightning text-lg"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-slate-100">AI Quét Hàng Loạt & Khớp Phòng Tự Động</h3>
                                        <span id="bulk-ocr-type-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Điện (kWh)</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">Tải lên nhiều ảnh công tơ một lượt. Gemini Vision AI sẽ bóc tách Số SX và Chỉ số để tự động điền vào từng phòng.</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeBulkOcrModal()" class="w-8 h-8 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Loại công tơ: Điện hoặc Nước -->
                        <div class="flex items-center justify-between gap-4 p-3 rounded-2xl bg-slate-950 border border-slate-800/60">
                            <div class="text-xs font-semibold text-slate-300 flex items-center gap-2">
                                <i class="fa-solid fa-filter text-indigo-400"></i> Loại công tơ đang quét:
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" id="bulk-type-btn-electricity" onclick="setBulkOcrType('electricity')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm">
                                    <i class="fa-solid fa-bolt"></i> Công Tơ Điện
                                </button>
                                <button type="button" id="bulk-type-btn-water" onclick="setBulkOcrType('water')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-slate-900 text-slate-400 border border-slate-800 hover:text-slate-200">
                                    <i class="fa-solid fa-droplet"></i> Đồng Hồ Nước
                                </button>
                            </div>
                        </div>

                        <!-- Khu vực tải nhiều ảnh (Dropzone) -->
                        <input type="file" id="bulk-ocr-file-input" multiple accept="image/*" class="hidden" onchange="handleBulkOcrFilesSelected(this)">
                        
                        <div id="bulk-ocr-dropzone" onclick="document.getElementById('bulk-ocr-file-input').click()" class="border-2 border-dashed border-slate-700 hover:border-indigo-500/60 rounded-2xl p-6 text-center cursor-pointer transition-all bg-slate-950/50 hover:bg-slate-950/80 group">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 group-hover:bg-indigo-500/20 border border-indigo-500/20 flex items-center justify-center text-indigo-400 transition-all shadow-inner">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl group-hover:scale-110 transition-transform"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-200">Nhấn vào đây hoặc kéo thả nhiều ảnh công tơ vào đây</p>
                                <p class="text-xs text-slate-400 max-w-md">Hỗ trợ định dạng JPG, PNG, WEBP (chụp từ điện thoại, tối đa 30 ảnh một lượt). AI sẽ tự xoay, phóng to mặt số và trích xuất.</p>
                            </div>
                        </div>

                        <!-- Trạng thái ảnh đã chọn & Nút kích hoạt AI -->
                        <div id="bulk-ocr-files-bar" class="hidden flex items-center justify-between p-3 rounded-2xl bg-slate-950 border border-slate-800">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                <span class="text-slate-300 font-semibold">Đã nạp <strong id="bulk-files-count" class="text-emerald-400 font-mono text-sm">0</strong> ảnh công tơ</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="clearBulkOcrFiles()" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 text-xs font-semibold transition-all">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Chọn lại
                                </button>
                                <button type="button" id="btn-start-bulk-ocr" onclick="startBulkOcrAnalysis()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:opacity-90 text-white text-xs font-bold transition-all shadow-lg shadow-purple-600/25 flex items-center gap-1.5">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Bắt Đầu Quét AI Tự Động
                                </button>
                            </div>
                        </div>

                        <!-- Hiệu ứng quét và thanh tiến trình -->
                        <div id="bulk-ocr-progress-box" class="hidden p-4 rounded-2xl bg-slate-950 border border-indigo-500/30 space-y-3">
                            <div class="flex items-center justify-between text-xs font-bold">
                                <span class="text-indigo-300 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i> Đang phân tích qua Google Gemini Vision AI...
                                </span>
                                <span id="bulk-ocr-progress-text" class="text-slate-400 font-mono">0%</span>
                            </div>
                            <div class="w-full bg-slate-900 rounded-full h-2.5 overflow-hidden border border-slate-800">
                                <div id="bulk-ocr-progress-bar" class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p class="text-[11px] text-slate-400 italic">Hệ thống đang bóc tách Số SX và Chỉ số từng ảnh, đồng thời đối chiếu với các phòng trong nhà trọ của bạn...</p>
                        </div>

                        <!-- Bảng kết quả phân tích & đối chiếu (Review Table) -->
                        <div id="bulk-ocr-results-wrapper" class="hidden space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-200 uppercase tracking-wider">Kết Quả Khớp Dữ Liệu</span>
                                    <span id="bulk-match-stats-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Khớp thành công 0/0
                                    </span>
                                </div>
                                <span class="text-[11px] text-slate-400">Bạn có thể chỉnh sửa lại phòng hoặc chỉ số trước khi chốt</span>
                            </div>

                            <div class="max-h-72 overflow-y-auto rounded-2xl border border-slate-800 bg-slate-950/60 divide-y divide-slate-800/60">
                                <table class="w-full text-left text-xs text-slate-300">
                                    <thead class="text-[11px] text-slate-400 uppercase bg-slate-900/80 sticky top-0 backdrop-blur-sm z-10 border-b border-slate-800">
                                        <tr>
                                            <th class="px-4 py-3">Ảnh</th>
                                            <th class="px-4 py-3">Số SX AI Đọc</th>
                                            <th class="px-4 py-3">Phòng Khớp Được</th>
                                            <th class="px-4 py-3">Chỉ Số Mới</th>
                                            <th class="px-4 py-3">Trạng Thái</th>
                                            <th class="px-4 py-3 text-center">Bỏ qua</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bulk-ocr-results-body" class="divide-y divide-slate-900">
                                        <!-- Rows rendered dynamically by JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex items-center justify-between pt-3 border-t border-slate-800/80">
                            <button type="button" onclick="closeBulkOcrModal()" class="py-2.5 px-5 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 text-xs font-bold transition-all">
                                Đóng lại
                            </button>
                            <button type="button" id="btn-bulk-ocr-apply" onclick="applyBulkOcrToTable()" disabled class="py-2.5 px-6 rounded-xl bg-emerald-600 disabled:bg-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed hover:bg-emerald-500 text-white text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-emerald-600/20">
                                <i class="fa-solid fa-check-double"></i> Áp Dụng Tất Cả Vào Bảng Chốt Số
                            </button>
                        </div>
                    </div>
                </div>

                <!-- MODAL TÍNH TIỀN PHÒNG & XUẤT HÓA ĐƠN TỰ ĐỘNG (FEAT_17_BILLING - BẢNG 118 ĐẾN 123) -->
                <div id="calculate-invoice-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/85 backdrop-blur-md p-4 overflow-y-auto">
                    <div class="relative w-full max-w-3xl bg-slate-900/95 border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-5 my-auto max-h-[92vh] overflow-y-auto text-slate-200">
                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-900/40">
                                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-slate-100">Tính Tiền Phòng & Xuất Hóa Đơn Tự Động (FEAT_17_BILLING)</h3>
                                        <span id="invoice-status-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Chờ tính toán</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">Tự động tổng hợp chiết tính tiền phòng, điện nước tiêu thụ, phí phát sinh và tạo mã QR VietQR NAPAS247 chuẩn ngân hàng.</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeInvoiceModal()" class="w-8 h-8 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Cảnh báo lỗi theo Bảng 121 -->
                        <!-- ERR_17_01: Phòng chưa có cư dân thuê đại diện (Cảnh báo vàng chặn xuất hóa đơn) -->
                        <div id="invoice-err-17-01" class="hidden p-4 rounded-2xl bg-amber-500/15 border border-amber-500/40 text-amber-300 text-xs font-semibold flex items-center gap-3">
                            <i class="fa-solid fa-triangle-exclamation text-amber-400 text-lg"></i>
                            <div>
                                <strong class="block font-bold">Cảnh báo không thể lập hóa đơn:</strong>
                                <span id="invoice-err-17-01-msg">Phòng này chưa có cư dân đại diện thuê, không thể sinh hóa đơn thu tiền.</span>
                            </div>
                        </div>

                        <!-- ERR_17_02: Đơn giá dịch vụ bị trống hoặc bằng 0 (Bôi đỏ nhắc nhở) -->
                        <div id="invoice-err-17-02" class="hidden p-4 rounded-2xl bg-rose-500/15 border border-rose-500/40 text-rose-300 text-xs font-semibold flex items-center gap-3">
                            <i class="fa-solid fa-circle-exclamation text-rose-400 text-lg"></i>
                            <div>
                                <strong class="block font-bold">Lỗi cấu hình bảng giá:</strong>
                                <span id="invoice-err-17-02-msg">Đơn giá điện nước chưa được cấu hình trong bảng giá cơ sở.</span>
                            </div>
                        </div>

                        <!-- Form nhập liệu theo Bảng 120 -->
                        <form id="invoice-calculate-form" onsubmit="event.preventDefault(); submitPublishInvoice();" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- room_id: ID phòng hợp lệ -->
                                <div>
                                    <label for="invoice_room_id" class="block text-xs font-bold text-slate-300 mb-1.5">
                                        Phòng tính tiền <span class="text-rose-400">*</span>
                                    </label>
                                    <select id="invoice_room_id" name="room_id" onchange="handleInvoiceRoomChange(this.value)" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none transition-all">
                                        <option value="">-- Chọn phòng cần lập hóa đơn --</option>
                                        @foreach($utilityRooms as $room)
                                            @php $res = $room->residents->first(); @endphp
                                            <option value="{{ $room->id }}" data-room="{{ $room->room_number }}" data-price="{{ $room->price }}" data-resident="{{ $res ? $res->name : '' }}">
                                                Phòng {{ $room->room_number }} - {{ $res ? $res->name : '(Chưa có cư dân)' }} ({{ number_format($room->price) }}đ)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- invoice_date: Ngày lập hóa đơn (YYYY-MM-DD) -->
                                <div>
                                    <label for="invoice_date" class="block text-xs font-bold text-slate-300 mb-1.5">
                                        Ngày lập hóa đơn <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="date" id="invoice_date" name="invoice_date" value="{{ date('Y-m-d') }}" onchange="calculateModalPreview()" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none transition-all">
                                </div>

                                <!-- STT 2: due_date - DatePicker theo Bảng 122 -->
                                <div>
                                    <label for="due_date" class="block text-xs font-bold text-slate-300 mb-1.5">
                                        Hạn thanh toán * <span class="text-slate-400 text-[10px] font-normal">(Sau ngày lập ít nhất 3 ngày)</span>
                                    </label>
                                    <input type="date" id="due_date" name="due_date" placeholder="Chọn ngày..." value="{{ \Carbon\Carbon::now()->addDays(5)->format('Y-m-d') }}" onchange="validateDueDateRange()" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none transition-all">
                                    <span id="due_date_error" class="hidden text-rose-400 text-[11px] font-semibold mt-1"></span>
                                </div>

                                <!-- other_fees: Phí phát sinh (nếu có) -->
                                <div>
                                    <label for="invoice_other_fees" class="block text-xs font-bold text-slate-300 mb-1.5">
                                        Phí phát sinh (nếu có) <span class="text-slate-400 text-[10px] font-normal">(Đền bù, dọn thêm...)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" id="invoice_other_fees" name="other_fees" value="0" min="0" step="1000" oninput="calculateModalPreview()" placeholder="0" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none transition-all pr-8">
                                        <span class="absolute right-3 top-2 text-xs text-slate-500">đ</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Đơn giá cấu hình kiểm tra ERR_17_02 -->
                            <div class="grid grid-cols-2 gap-4 p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                                <div>
                                    <label for="invoice_electricity_price" class="block text-[11px] font-bold text-slate-400 mb-1">
                                        ⚡ Đơn giá điện (đ/kWh)
                                    </label>
                                    <input type="number" id="invoice_electricity_price" name="electricity_price" value="3500" oninput="calculateModalPreview()" class="w-full px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none font-mono">
                                </div>
                                <div>
                                    <label for="invoice_water_price" class="block text-[11px] font-bold text-slate-400 mb-1">
                                        💧 Đơn giá nước (đ/m3)
                                    </label>
                                    <input type="number" id="invoice_water_price" name="water_price" value="15000" oninput="calculateModalPreview()" class="w-full px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none font-mono">
                                </div>
                            </div>

                            <!-- STT 1: summary_card - Tổng tiền thanh toán (Chữ to nổi bật trên nền trắng theo Bảng 122) -->
                            <div id="summary_card" class="p-5 rounded-2xl bg-white text-slate-900 shadow-xl border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all">
                                <div>
                                    <span class="text-xs uppercase font-extrabold tracking-wider text-slate-500">Tổng tiền thanh toán</span>
                                    <div id="summary_card_amount" class="text-3xl md:text-4xl font-black text-blue-900 font-mono tracking-tight mt-0.5">
                                        4.850.000 đ
                                    </div>
                                    <div class="text-[11px] text-slate-600 mt-1 font-medium flex items-center gap-2">
                                        <span id="summary_room_tag" class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-bold">Phòng: 201</span>
                                        <span id="summary_resident_tag">Cư dân: Nguyễn Văn A</span>
                                    </div>
                                </div>

                                <div class="text-xs text-slate-600 space-y-1 text-right font-medium">
                                    <div>Tiền phòng: <strong id="summary_room_price" class="text-slate-900 font-mono">4.500.000 đ</strong></div>
                                    <div>Điện + Nước: <strong id="summary_utility_price" class="text-slate-900 font-mono">200.000 đ</strong></div>
                                    <div>Dịch vụ cố định + Phí thêm: <strong id="summary_extra_price" class="text-slate-900 font-mono">150.000 đ</strong></div>
                                </div>
                            </div>

                            <!-- STT 3: qr_code - QR VietQR NAPAS247 theo Bảng 122 -->
                            <div id="vietqr-preview-box" class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <!-- STT 3: qr_code -->
                                    <img id="qr_code" src="https://img.vietqr.io/image/VCB-1051572297-compact.png?amount=4850000&addInfo=Thanh%20toan%20Phong%20201" alt="Mã thanh toán ngân hàng" class="w-32 h-32 object-contain rounded-xl p-1.5 bg-white border border-slate-200 shadow-md">
                                    <div class="space-y-1">
                                        <span class="text-[10px] uppercase font-bold text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded-full inline-block">VietQR NAPAS247 Động</span>
                                        <h4 class="text-xs font-bold text-slate-200">Quét mã chuyển khoản ngân hàng</h4>
                                        <p class="text-[11px] text-slate-400">Ngân hàng: <strong>Vietcombank (VCB)</strong></p>
                                        <p class="text-[11px] text-slate-400">Số tài khoản: <strong class="font-mono text-slate-200">1051572297</strong></p>
                                        <p class="text-[11px] text-slate-400">Chủ tài khoản: <strong>CHỦ TRỌ</strong></p>
                                    </div>
                                </div>
                                <div class="text-right space-y-2">
                                    <a id="btnDownloadInvoicePdf" href="javascript:void(0)" onclick="downloadInvoicePdfCurrent()" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 text-xs font-bold border border-slate-700 transition-all inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-file-pdf text-rose-400"></i> Xuất PDF
                                    </a>
                                </div>
                            </div>

                            <!-- Footer Actions: STT 4: btnPublish theo Bảng 122 -->
                            <div class="flex items-center justify-between pt-3 border-t border-slate-800/80 gap-3">
                                <button type="button" onclick="closeInvoiceModal()" class="py-2.5 px-5 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 text-xs font-bold transition-all">
                                    Hủy bỏ
                                </button>
                                <!-- STT 4: btnPublish nền xanh đậm theo Bảng 122 -->
                                <button type="submit" id="btnPublish" class="flex-1 py-3 px-6 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-extrabold uppercase tracking-wider transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-950/40">
                                    <i class="fa-solid fa-paper-plane"></i> Phát hành hóa đơn
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


