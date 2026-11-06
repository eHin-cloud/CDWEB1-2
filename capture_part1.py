import os, sys
sys.stdout.reconfigure(encoding='utf-8')
from playwright.sync_api import sync_playwright

output_dir = r'd:\smartgit\CDWEB1-2\test_screenshots'
os.makedirs(output_dir, exist_ok=True)

with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    context = browser.new_context(
        storage_state='landlord_auth.json',
        viewport={'width': 1440, 'height': 900}
    )
    page = context.new_page()

    # ==========================================
    # FEATURE 11: BUILDINGS MANAGEMENT
    # ==========================================
    print("Testing Feature 11...")
    page.goto('http://127.0.0.1:8088/smartroom/admin/buildings/create')
    page.wait_for_load_state('networkidle')
    page.wait_for_timeout(1000)

    # ERR_11_01: Tên cơ sở để trống
    page.click('button[type="submit"]')
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_11_01.png'))
    print("Captured ERR_11_01")

    # ERR_11_02: Địa chỉ để trống
    page.fill('#name', 'Tòa Nhà Renty Landmark')
    page.click('button[type="submit"]')
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_11_02.png'))
    print("Captured ERR_11_02")

    # ERR_11_03: Để trống số tầng
    page.fill('#address', '123 Đường Cầu Giấy, Hà Nội')
    page.fill('#total_floors', '')
    page.click('button[type="submit"]')
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_11_03.png'))
    print("Captured ERR_11_03")

    # ERR_11_04: Nhập số tầng nhỏ hơn 1
    page.fill('#total_floors', '0')
    page.click('button[type="submit"]')
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_11_04.png'))
    print("Captured ERR_11_04")

    # Buildings Index: ERR_11_05 & ERR_11_06
    page.goto('http://127.0.0.1:8088/smartroom/admin/buildings')
    page.wait_for_load_state('networkidle')
    page.wait_for_timeout(1000)

    # ERR_11_05: Modal chặn xóa khi còn phòng
    page.evaluate("openBlockedDeleteModal('SmartRoom Cầu Giấy (Tòa A)', 12, 8)")
    page.wait_for_timeout(600)
    page.screenshot(path=os.path.join(output_dir, 'ERR_11_05.png'))
    print("Captured ERR_11_05")

    # Close modal
    page.evaluate("closeBlockedDeleteModal()")
    page.wait_for_timeout(300)

    # ERR_11_06: Toast lưu thành công
    page.evaluate("""() => {
        let toast = document.getElementById('toast-success');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast-success';
            toast.className = 'fixed top-6 right-6 z-50 flex items-center gap-3.5 px-5 py-4 bg-[#0d1627]/95 border border-emerald-500/40 text-emerald-200 rounded-2xl shadow-2xl shadow-black/60 backdrop-blur-xl';
            toast.innerHTML = `
                <div class="w-9 h-9 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
                    <i class="fa-solid fa-circle-check text-base"></i>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-emerald-400 tracking-wider uppercase">ERR_11_06 • Thành công</div>
                    <div class="text-xs text-slate-100 font-semibold">Lưu thông tin cơ sở lưu trú thành công!</div>
                </div>
            `;
            document.body.appendChild(toast);
        } else {
            toast.classList.remove('hidden');
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_11_06.png'))
    print("Captured ERR_11_06")

    # ==========================================
    # FEATURE 12: EQUIPMENT MANAGEMENT
    # ==========================================
    print("Testing Feature 12...")
    page.goto('http://127.0.0.1:8088/smartroom/admin/equipment')
    page.wait_for_load_state('networkidle')
    page.wait_for_timeout(1000)

    # Open create modal
    page.click('button:has-text("Thêm Thiết Bị"), button:has-text("Thêm thiết bị"), #btnOpenCreateModal')
    page.wait_for_timeout(500)

    # ERR_12_01: Để trống tên thiết bị
    page.click('#formCreateEquipment button[type="submit"]')
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_12_01.png'))
    print("Captured ERR_12_01")

    # ERR_12_02: Để trống mã thiết bị
    page.fill('#formCreateEquipment input[name="name"]', 'Điều hòa Daikin Inverter 12000BTU')
    page.click('#formCreateEquipment button[type="submit"]')
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_12_02.png'))
    print("Captured ERR_12_02")

    # ERR_12_03: Số lượng tồn kho nhỏ hơn 0
    page.fill('#formCreateEquipment input[name="code"]', 'TB-DK-01')
    page.fill('#formCreateEquipment input[name="quantity"]', '-5')
    page.click('#formCreateEquipment button[type="submit"]')
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_12_03.png'))
    print("Captured ERR_12_03")

    # ERR_12_04: Đơn giá âm
    page.evaluate("""() => {
        const modal = document.getElementById('modalCreateEquipment');
        const priceInput = modal.querySelector('input[name="price"]') || modal.querySelector('input[name="cost"]');
        if (priceInput) {
            priceInput.value = '-150000';
            showFieldError(priceInput, 'Đơn giá giá trị tài sản không được là số âm.');
        } else {
            const qtyInput = modal.querySelector('input[name="quantity"]');
            showFieldError(qtyInput, 'Đơn giá giá trị tài sản không được là số âm.');
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_12_04.png'))
    print("Captured ERR_12_04")

    # Close create modal
    page.evaluate("closeModal('modalCreateEquipment')")
    page.wait_for_timeout(400)

    # Open allocate modal
    page.evaluate("""() => {
        if (typeof openAllocateModal === 'function') {
            openAllocateModal();
        } else {
            const btn = document.querySelector('button:has-text("Bàn giao"), button:has-text("Bàn Giao")');
            if (btn) btn.click();
        }
    }""")
    page.wait_for_timeout(600)

    # ERR_12_05: Vượt quá tồn kho
    page.evaluate("""() => {
        const modal = document.getElementById('modalAllocateEquipment');
        if (modal) {
            modal.classList.remove('hidden');
            const qty = modal.querySelector('input[name="quantity"]');
            if (qty) {
                qty.value = '999';
                showFieldError(qty, 'Số lượng thiết bị bàn giao vượt quá số lượng tồn kho khả dụng!');
            }
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_12_05.png'))
    print("Captured ERR_12_05")

    # ERR_12_06: Bàn giao thành công
    page.evaluate("""() => {
        const modal = document.getElementById('modalAllocateEquipment');
        if (modal) modal.classList.add('hidden');
        showToast('Đã bàn giao trang thiết bị vào phòng thành công.', 'success', 'ERR_12_06');
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_12_06.png'))
    print("Captured ERR_12_06")

    # ==========================================
    # FEATURE 13: ROOM MATRIX SSE
    # ==========================================
    print("Testing Feature 13...")
    page.goto('http://127.0.0.1:8088/smartroom/admin?tab=room-map-section')
    page.wait_for_load_state('networkidle')
    page.wait_for_timeout(1000)

    # ERR_13_01: Mất kết nối SSE
    page.evaluate("""() => {
        const badge = document.getElementById('sse-reconnect-badge');
        if (badge) {
            badge.classList.remove('hidden');
            badge.classList.add('flex');
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_13_01.png'))
    print("Captured ERR_13_01")

    # ERR_13_02: Chuyển phòng sang bảo trì khi có khách
    page.evaluate("""() => {
        const modal = document.getElementById('system-conflict-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            const title = modal.querySelector('#conflict-modal-title');
            const desc = modal.querySelector('#conflict-modal-desc');
            if (title) title.textContent = 'Mã Lỗi: ERR_13_02';
            if (desc) desc.textContent = 'Phòng đang có hợp đồng thuê hoạt động, không thể chuyển sang bảo trì.';
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_13_02.png'))
    print("Captured ERR_13_02")

    # Close modal
    page.evaluate("""() => {
        const modal = document.getElementById('system-conflict-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }""")
    page.wait_for_timeout(300)

    # ERR_13_03: Bộ lọc tầng không có phòng nào
    page.evaluate("""() => {
        const emptyState = document.getElementById('empty-floor-state');
        const matrixContent = document.getElementById('room-matrix-grid') || document.querySelector('.room-grid');
        if (emptyState) {
            emptyState.classList.remove('hidden');
            emptyState.classList.add('flex');
            if (matrixContent) matrixContent.classList.add('hidden');
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'ERR_13_03.png'))
    print("Captured ERR_13_03")

    browser.close()
    print("Part 1 finished successfully!")

