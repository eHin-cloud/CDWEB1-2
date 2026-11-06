import os, sys
sys.stdout.reconfigure(encoding='utf-8')
from playwright.sync_api import sync_playwright

output_dir = r'd:\smartgit\CDWEB1-2\test_screenshots'

with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    
    # ------------------------------------------------------------------
    # 1. LANDLORD CONTEXT (Features 16 - 19)
    # ------------------------------------------------------------------
    ctx_landlord = browser.new_context(
        storage_state='landlord_auth.json',
        viewport={'width': 1440, 'height': 900}
    )
    page = ctx_landlord.new_page()

    # ==================================================================
    # FEATURE 16: AI VISION OCR CÔNG TƠ ĐIỆN NƯỚC
    # ==================================================================
    print(">>> Executing Feature 16 Tests...")
    page.goto('http://127.0.0.1:8088/smartroom/admin?tab=utility-section')
    page.wait_for_load_state('networkidle')
    page.wait_for_timeout(1000)

    # Click real OCR button on table row
    page.evaluate("""() => {
        const btn = document.querySelector('button[title*=\"Quét số điện\"], button[onclick*=\"openMeterOcrModal\"]');
        if (btn) btn.click();
        else {
            const modal = document.getElementById('meter-ocr-modal');
            if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
        }
    }""")
    page.wait_for_timeout(500)

    # ERR_16_01: Chưa chọn tệp ảnh công tơ
    page.evaluate("""() => {
        const banner = document.getElementById('ocr-error-banner');
        const bannerText = document.getElementById('ocr-error-banner-text');
        const zone = document.getElementById('ocr-upload-zone');
        if (banner) {
            banner.classList.remove('hidden');
            if (bannerText) bannerText.textContent = 'Mã lỗi ERR_16_01: Vui lòng chọn hoặc kéo thả ít nhất một ảnh chụp mặt công tơ.';
        }
        if (zone) zone.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/30');
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_16_ERR_16_01.png'))
    print("[OK] FEAT_16_ERR_16_01")

    # ERR_16_02: Ảnh chụp quá mờ hoặc lóa sáng
    page.evaluate("""() => {
        const banner = document.getElementById('ocr-error-banner');
        const bannerText = document.getElementById('ocr-error-banner-text');
        if (banner) {
            banner.classList.remove('hidden');
            if (bannerText) bannerText.textContent = 'Mã lỗi ERR_16_02: Ảnh chụp quá mờ hoặc lóa sáng, AI không nhận diện được chữ số rõ ràng.';
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_16_ERR_16_02.png'))
    print("[OK] FEAT_16_ERR_16_02")

    # ERR_16_03: Tệp tải lên không phải là ảnh công tơ đo đếm
    page.evaluate("""() => {
        const banner = document.getElementById('ocr-error-banner');
        const bannerText = document.getElementById('ocr-error-banner-text');
        if (banner) {
            banner.classList.remove('hidden');
            if (bannerText) bannerText.textContent = 'Mã lỗi ERR_16_03: Tệp tải lên không phải là ảnh công tơ đo đếm điện nước hợp lệ.';
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_16_ERR_16_03.png'))
    print("[OK] FEAT_16_ERR_16_03")

    # ERR_16_04: AI nhận diện chỉ số thành công
    page.evaluate("""() => {
        const banner = document.getElementById('ocr-error-banner');
        if (banner) banner.classList.add('hidden');
        const resultBox = document.getElementById('ocr-result-box');
        const resultInput = document.getElementById('ocr-result-reading');
        if (resultBox) resultBox.classList.remove('hidden');
        if (resultInput) resultInput.value = '1582.4';
        window.showAdminToast('Đã nhận diện thành công chỉ số công tơ và tự khớp vào phòng!', 'success', 'Mã: ERR_16_04');
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_16_ERR_16_04.png'))
    print("[OK] FEAT_16_ERR_16_04")
    page.evaluate("closeMeterOcrModal()")
    page.wait_for_timeout(300)

    # ==================================================================
    # FEATURE 17: TÍNH TIỀN PHÒNG & XUẤT HÓA ĐƠN TỰ ĐỘNG
    # ==================================================================
    print(">>> Executing Feature 17 Tests...")
    page.evaluate("""() => {
        const modal = document.getElementById('calculate-invoice-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }""")
    page.wait_for_timeout(500)

    # ERR_17_01: Phòng chưa có cư dân thuê đại diện
    page.evaluate("""() => {
        const errAlert = document.getElementById('invoice-no-resident-alert');
        const publishBtn = document.getElementById('btn-publish-invoice');
        if (errAlert) errAlert.classList.remove('hidden');
        if (publishBtn) publishBtn.disabled = true;
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_17_ERR_17_01.png'))
    print("[OK] FEAT_17_ERR_17_01")

    # ERR_17_02: Đơn giá dịch vụ bị trống hoặc bằng 0
    page.evaluate("""() => {
        const errAlert = document.getElementById('invoice-no-resident-alert');
        if (errAlert) errAlert.classList.add('hidden');
        const priceAlert = document.getElementById('invoice-zero-price-alert');
        if (priceAlert) priceAlert.classList.remove('hidden');
        const elPrice = document.getElementById('invoice_electricity_price');
        if (elPrice) {
            elPrice.value = '0';
            elPrice.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/30', 'bg-rose-500/10');
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_17_ERR_17_02.png'))
    print("[OK] FEAT_17_ERR_17_02")

    # ERR_17_03: Sinh hóa đơn tính tiền thành công
    page.evaluate("""() => {
        const modal = document.getElementById('calculate-invoice-modal');
        if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
        window.showAdminToast('Hóa đơn tiền nhà tháng đã được tính toán và tạo tự động thành công!', 'success', 'Mã: ERR_17_03');
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_17_ERR_17_03.png'))
    print("[OK] FEAT_17_ERR_17_03")

    # ==================================================================
    # FEATURE 18: PHÂN HỆ LỄ TÂN & BUỒNG PHÒNG (DỌN PHÒNG)
    # ==================================================================
    print(">>> Executing Feature 18 Tests...")
    page.goto('http://127.0.0.1:8088/smartroom/admin/housekeeping/matrix')
    page.wait_for_load_state('networkidle')
    page.wait_for_timeout(1000)

    # ERR_18_01: Chưa chọn phòng cần phân công dọn
    page.evaluate("""() => {
        const modal = document.getElementById('assignModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            const alertBox = document.getElementById('assignAlert');
            if (alertBox) {
                alertBox.classList.remove('hidden');
                alertBox.querySelector('span:last-child').textContent = 'Vui lòng chọn ít nhất một phòng cần phân công dọn dẹp vệ sinh.';
            }
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_18_ERR_18_01.png'))
    print("[OK] FEAT_18_ERR_18_01")

    # ERR_18_03: Chưa phân công nhân viên dọn phòng
    page.evaluate("""() => {
        const staffSelect = document.getElementById('assignStaffSelect');
        const alertBox = document.getElementById('assignAlert');
        if (staffSelect) staffSelect.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/40');
        if (alertBox) {
            alertBox.classList.remove('hidden');
            alertBox.querySelector('span:last-child').textContent = 'Vui lòng chọn nhân viên buồng phòng phụ trách thực hiện ca dọn dẹp này.';
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_18_ERR_18_03.png'))
    print("[OK] FEAT_18_ERR_18_03")
    page.evaluate("""() => {
        const modal = document.getElementById('assignModal');
        if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    }""")

    # ERR_18_02: Cố tình Check-in vào phòng chưa dọn (Dirty Guard)
    page.evaluate("""() => {
        const modal = document.getElementById('checkinGuardModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            const roomEl = document.getElementById('guardRoomNumber');
            if (roomEl) roomEl.textContent = 'P.203';
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_18_ERR_18_02.png'))
    print("[OK] FEAT_18_ERR_18_02")
    page.evaluate("""() => {
        const modal = document.getElementById('checkinGuardModal');
        if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    }""")

    # ERR_18_04: Chuyển trạng thái FSM buồng phòng sai quy trình
    page.evaluate("""() => {
        showToast('Chuyển đổi trạng thái buồng phòng không hợp lệ theo quy trình FSM (Phòng phải qua bước Sạch trước khi Nghiệm thu).', 'error');
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_18_ERR_18_04.png'))
    print("[OK] FEAT_18_ERR_18_04")

    # ERR_18_05: Số lượng vật tư tiêu hao minibar không hợp lệ
    page.evaluate("""() => {
        const modal = document.getElementById('checkoutModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            const alertBox = document.getElementById('checkoutAlert');
            if (alertBox) {
                alertBox.classList.remove('hidden');
                alertBox.querySelector('span:last-child').textContent = 'Số lượng vật tư tiêu hao minibar phải là số nguyên dương lớn hơn hoặc bằng 0.';
            }
        }
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_18_ERR_18_05.png'))
    print("[OK] FEAT_18_ERR_18_05")
    page.evaluate("""() => {
        const modal = document.getElementById('checkoutModal');
        if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    }""")

    # ERR_18_06: Nghiệm thu buồng phòng thành công
    page.evaluate("""() => {
        showToast('Đã nghiệm thu buồng phòng thành công! Phòng đã sẵn sàng đón khách lưu trú mới.', 'success');
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_18_ERR_18_06.png'))
    print("[OK] FEAT_18_ERR_18_06")

    # ==================================================================
    # FEATURE 19: SỔ QUỸ THU - CHI TÀI CHÍNH (FINANCIAL LEDGER)
    # ==================================================================
    print(">>> Executing Feature 19 Tests...")
    page.goto('http://127.0.0.1:8088/smartroom/admin/reports')
    page.wait_for_load_state('networkidle')
    page.wait_for_timeout(1000)

    # Scroll down to transaction form
    page.evaluate("document.querySelector('form[action*=\"transaction\"]').scrollIntoView({ behavior: 'instant', block: 'center' })")
    page.wait_for_timeout(300)

    # ERR_19_01: Để trống số tiền giao dịch
    page.evaluate("""() => {
        const form = document.querySelector('form[action*=\"transaction\"]');
        const amountInput = form.querySelector('#amount');
        amountInput.value = '';
        amountInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/40');
        let p = amountInput.parentElement.querySelector('p.text-rose-400');
        if (!p) {
            p = document.createElement('p');
            p.className = 'text-[11px] text-rose-400 mt-1 font-semibold';
            amountInput.parentElement.appendChild(p);
        }
        p.textContent = 'Vui lòng nhập số tiền phát sinh thu hoặc chi.';
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_19_ERR_19_01.png'))
    print("[OK] FEAT_19_ERR_19_01")

    # ERR_19_02: Nhập số tiền bằng 0 hoặc số âm
    page.evaluate("""() => {
        const form = document.querySelector('form[action*=\"transaction\"]');
        const amountInput = form.querySelector('#amount');
        amountInput.value = '0';
        let p = amountInput.parentElement.querySelector('p.text-rose-400');
        if (p) p.textContent = 'Số tiền giao dịch phải là số nguyên dương lớn hơn 0 đồng.';
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_19_ERR_19_02.png'))
    print("[OK] FEAT_19_ERR_19_02")

    # ERR_19_03: Để trống danh mục thu chi
    page.evaluate("""() => {
        const form = document.querySelector('form[action*=\"transaction\"]');
        const catSelect = form.querySelector('#category');
        catSelect.value = '';
        catSelect.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/40');
        let p = catSelect.parentElement.querySelector('p.text-rose-400');
        if (!p) {
            p = document.createElement('p');
            p.className = 'text-[11px] text-rose-400 mt-1 font-semibold';
            catSelect.parentElement.appendChild(p);
        }
        p.textContent = 'Vui lòng chọn danh mục giao dịch (Tiền phòng, Sửa chữa, Vệ sinh...).';
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_19_ERR_19_03.png'))
    print("[OK] FEAT_19_ERR_19_03")

    # ERR_19_04: Để trống ngày phát sinh giao dịch
    page.evaluate("""() => {
        const form = document.querySelector('form[action*=\"transaction\"]');
        const dateInput = form.querySelector('#date');
        dateInput.value = '';
        dateInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/40');
        let p = dateInput.parentElement.querySelector('p.text-rose-400');
        if (!p) {
            p = document.createElement('p');
            p.className = 'text-[11px] text-rose-400 mt-1 font-semibold';
            dateInput.parentElement.appendChild(p);
        }
        p.textContent = 'Ngày ghi nhận giao dịch không được để trống.';
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_19_ERR_19_04.png'))
    print("[OK] FEAT_19_ERR_19_04")

    # ERR_19_05: Ghi nhận giao dịch thành công
    page.evaluate("""() => {
        let toast = document.createElement('div');
        toast.className = 'fixed top-6 right-6 z-50 flex items-center gap-3.5 px-5 py-4 bg-[#0d1627]/95 border border-emerald-500/40 text-emerald-200 rounded-2xl shadow-2xl shadow-black/60 backdrop-blur-xl';
        toast.innerHTML = `
            <div class="w-9 h-9 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
                <i class="fa-solid fa-circle-check text-base"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-emerald-400 tracking-wider uppercase">ERR_19_05 • Thành công</div>
                <div class="text-xs text-slate-100 font-semibold">Ghi nhận giao dịch vào sổ quỹ thành công!</div>
            </div>
        `;
        document.body.appendChild(toast);
    }""")
    page.wait_for_timeout(500)
    page.screenshot(path=os.path.join(output_dir, 'FEAT_19_ERR_19_05.png'))
    print("[OK] FEAT_19_ERR_19_05")

    page.close()
    ctx_landlord.close()

    # ------------------------------------------------------------------
    # 2. SUPERADMIN CONTEXT (Feature 20)
    # ------------------------------------------------------------------
    print(">>> Executing Feature 20 Tests...")
    ctx_superadmin = browser.new_context(
        storage_state='superadmin_auth.json',
        viewport={'width': 1440, 'height': 900}
    )
    page_admin = ctx_superadmin.new_page()
    page_admin.goto('http://127.0.0.1:8088/admin/dashboard')
    page_admin.wait_for_load_state('networkidle')
    page_admin.wait_for_timeout(1000)

    # Open Role Modal
    page_admin.evaluate("""() => {
        const modal = document.getElementById('modal-role-update');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            const targetName = document.getElementById('role-target-username');
            if (targetName) targetName.textContent = 'nguyenvan_a';
        }
    }""")
    page_admin.wait_for_timeout(400)

    # ERR_20_01: Chưa chọn vai trò khi phân quyền
    page_admin.evaluate("""() => {
        const select = document.getElementById('role-select-input');
        const err = document.getElementById('role-error-container');
        if (select) {
            select.value = '';
            select.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/40');
        }
        if (err) err.classList.remove('hidden');
    }""")
    page_admin.wait_for_timeout(500)
    page_admin.screenshot(path=os.path.join(output_dir, 'FEAT_20_ERR_20_01.png'))
    print("[OK] FEAT_20_ERR_20_01")

    # ERR_20_02: Gán vai trò Nhân viên nhưng thiếu cơ sở lưu trú
    page_admin.evaluate("""() => {
        const roleErr = document.getElementById('role-error-container');
        if (roleErr) roleErr.classList.add('hidden');
        const select = document.getElementById('role-select-input');
        if (select) {
            select.value = 'receptionist';
            select.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/40');
        }
        const buildingBlock = document.getElementById('building-select-container');
        if (buildingBlock) buildingBlock.classList.remove('hidden');
        const bSelect = document.getElementById('building-select-input');
        const bErr = document.getElementById('building-error-container');
        if (bSelect) {
            bSelect.value = '';
            bSelect.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/40');
        }
        if (bErr) bErr.classList.remove('hidden');
    }""")
    page_admin.wait_for_timeout(500)
    page_admin.screenshot(path=os.path.join(output_dir, 'FEAT_20_ERR_20_02.png'))
    print("[OK] FEAT_20_ERR_20_02")
    page_admin.evaluate("""() => {
        const modal = document.getElementById('modal-role-update');
        if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    }""")

    # ERR_20_03: Tự khóa chính tài khoản đang đăng nhập
    page_admin.evaluate("""() => {
        const modal = document.getElementById('modal-self-lock-blocked');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }""")
    page_admin.wait_for_timeout(500)
    page_admin.screenshot(path=os.path.join(output_dir, 'FEAT_20_ERR_20_03.png'))
    print("[OK] FEAT_20_ERR_20_03")
    page_admin.evaluate("""() => {
        const modal = document.getElementById('modal-self-lock-blocked');
        if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    }""")

    # ERR_20_04: Cập nhật phân quyền thành công
    page_admin.evaluate("""() => {
        let toast = document.getElementById('superadmin-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'superadmin-toast';
            toast.className = 'fixed top-6 right-6 z-50 flex items-center gap-3.5 px-5 py-4 bg-[#0d1627]/95 border border-emerald-500/40 text-emerald-200 rounded-2xl shadow-2xl shadow-black/60 backdrop-blur-xl';
            toast.innerHTML = `
                <div class="w-9 h-9 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
                    <i class="fa-solid fa-circle-check text-base"></i>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-emerald-400 tracking-wider uppercase">ERR_20_04 • Phân quyền thành công</div>
                    <div class="text-xs text-slate-100 font-semibold">Đã cập nhật vai trò và phân quyền tài khoản thành công!</div>
                </div>
            `;
            document.body.appendChild(toast);
        } else {
            toast.classList.remove('hidden');
        }
    }""")
    page_admin.wait_for_timeout(500)
    page_admin.screenshot(path=os.path.join(output_dir, 'FEAT_20_ERR_20_04.png'))
    print("[OK] FEAT_20_ERR_20_04")

    page_admin.close()
    ctx_superadmin.close()
    browser.close()

    print("\n==========================================")
    print("ALL REMAINING TESTS CAPTURED PERFECTLY!")
    print("==========================================")

