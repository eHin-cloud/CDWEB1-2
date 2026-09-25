# =====================================================================
# SmartRoom & Renty - Auto Sync Git Daemon [SAFE MODE - AN TOÀN TUYỆT ĐỐI]
# Tự động lắng nghe, cất code dở dang (Stash), kéo code mới và phục hồi
# =====================================================================
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$Host.UI.RawUI.WindowTitle = "SmartRoom & Renty - Auto Sync Git Daemon [Safe Mode]"

# 1. Kiểm tra xem máy đã cài đặt Git chưa
$gitCheck = Get-Command git -ErrorAction SilentlyContinue
if (-not $gitCheck) {
    Write-Host "[LỖI] Không tìm thấy Git trong PATH! Vui lòng cài đặt Git trên máy." -ForegroundColor Red
    Read-Host "Nhấn Enter để thoát..."
    exit 1
}

# 2. Xác định nhánh hiện tại
$CURRENT_BRANCH = (git rev-parse --abbrev-ref HEAD 2>$null)
if (-not $CURRENT_BRANCH) {
    $CURRENT_BRANCH = "main"
}
$INTERVAL = 20 # Kiểm tra mỗi 20 giây

Clear-Host
Write-Host "====================================================================" -ForegroundColor Cyan
Write-Host "   SMARTROOM & RENTY - AUTO SYNC GIT DAEMON [CHẾ ĐỘ AN TOÀN - STASH]   " -ForegroundColor Green
Write-Host "====================================================================" -ForegroundColor Cyan
Write-Host "• Nhánh theo dõi:   " -NoNewline; Write-Host "$CURRENT_BRANCH" -ForegroundColor Yellow
Write-Host "• Chu kỳ quét:      " -NoNewline; Write-Host "$INTERVAL giây / lần" -ForegroundColor Yellow
Write-Host "• Chế độ an toàn:   " -NoNewline; Write-Host "Tự động cất code dở dang -> Kéo -> Khôi phục code" -ForegroundColor Green
Write-Host "• Nhấn Ctrl + C để dừng tiến trình bất cứ lúc nào." -ForegroundColor Gray
Write-Host "--------------------------------------------------------------------" -ForegroundColor DarkGray
Write-Host "[!] Đang lắng nghe thay đổi từ Git Server... (Vui lòng giữ cửa sổ này)" -ForegroundColor Cyan
Write-Host ""

while ($true) {
    try {
        # 1. Fetch metadata mới nhất từ remote origin
        git fetch origin $CURRENT_BRANCH 2>$null

        $LOCAL = (git rev-parse HEAD 2>$null).Trim()
        $REMOTE = (git rev-parse "origin/$CURRENT_BRANCH" 2>$null).Trim()

        # Nếu tồn tại commit hash hợp lệ và khác nhau
        if ($LOCAL -and $REMOTE -and ($LOCAL -ne $REMOTE)) {
            $TIME = Get-Date -Format "HH:mm:ss dd/MM/yyyy"
            Write-Host "[$TIME] ========================================================" -ForegroundColor Magenta
            Write-Host "[$TIME] [!] Phát hiện commit mới trên nhánh '$CURRENT_BRANCH'!" -ForegroundColor Green
            Write-Host "[$TIME] Local:  $LOCAL" -ForegroundColor Gray
            Write-Host "[$TIME] Remote: $REMOTE" -ForegroundColor Gray

            # 2. Kiểm tra xem máy cục bộ có code đang gõ dở chưa commit không
            $localStatus = git status --porcelain 2>$null
            $hasLocalChanges = [bool]($localStatus)

            if ($hasLocalChanges) {
                $stashTag = "auto-sync-stash-" + (Get-Date -Format "yyyyMMdd-HHmmss")
                Write-Host "[$TIME] [🛡️ BẢO VỆ] Phát hiện bạn có code đang sửa dở dang chưa commit!" -ForegroundColor Yellow
                Write-Host "[$TIME] [🛡️ BẢO VỆ] Đang tự động cất code vào ngăn tạm an toàn (git stash)..." -ForegroundColor Yellow
                git stash push -u -m "$stashTag" 2>$null | Out-Null
            }

            # Lấy danh sách file thay đổi từ remote
            $CHANGED_FILES = git diff --name-only $LOCAL $REMOTE 2>$null

            # 3. Kéo code mới về
            Write-Host "[$TIME] [+] Đang kéo code mới về (git pull origin $CURRENT_BRANCH)..." -ForegroundColor Cyan
            $pullOutput = git pull origin $CURRENT_BRANCH 2>&1
            Write-Host $pullOutput -ForegroundColor Gray

            # 4. Khôi phục lại phần code đang gõ dở của bạn
            if ($hasLocalChanges) {
                Write-Host "[$TIME] [🛡️ BẢO VỆ] Đang đắp lại phần code bạn đang viết dở dang..." -ForegroundColor Cyan
                $popOutput = git stash pop 2>&1

                # Kiểm tra nếu xảy ra Conflict thật sự
                if ($LASTEXITCODE -ne 0 -or ($popOutput -match "CONFLICT")) {
                    try { [Console]::Beep(1000, 300) } catch {}
                    Write-Host "[$TIME] [⚠️ CẢNH BÁO CONFLICT] Xung đột mã nguồn đã được phát hiện!" -ForegroundColor White -BackgroundColor DarkRed
                    Write-Host "[$TIME] Code mới kéo về bị trùng dòng với đoạn code bạn vừa sửa." -ForegroundColor Yellow
                    Write-Host "[$TIME] Git đã giữ nguyên cả hai và đánh dấu trong file bằng: <<<<<<<" -ForegroundColor Yellow
                    Write-Host "[$TIME] >> Hãy mở VS Code / IDE để chọn giữ code phù hợp! <<" -ForegroundColor Magenta
                } else {
                    Write-Host "[$TIME] [OK] Đã khôi phục thành công code đang gõ, hoàn toàn không bị mất mát!" -ForegroundColor Green
                }
            }

            # 5. Kiểm tra xem có Migration mới không
            $hasMigration = $CHANGED_FILES | Where-Object { $_ -match "^database/migrations" }
            if ($hasMigration) {
                Write-Host "[$TIME] [->] Phát hiện Migration CSDL mới! Đang chạy 'php artisan migrate --force'..." -ForegroundColor Yellow
                php artisan migrate --force
            }

            # 6. Kiểm tra xem có thư viện Composer mới không
            $hasComposer = $CHANGED_FILES | Where-Object { $_ -match "composer\.lock" }
            if ($hasComposer) {
                Write-Host "[$TIME] [->] Phát hiện composer.lock thay đổi! Đang chạy 'composer install'..." -ForegroundColor Yellow
                composer install --no-interaction --prefer-dist
            }

            # 7. Kiểm tra xem có thay đổi Frontend không
            $hasFrontend = $CHANGED_FILES | Where-Object { $_ -match "^resources/|^package\.json|^vite\.config\.js" }
            if ($hasFrontend) {
                Write-Host "[$TIME] [->] Phát hiện thay đổi Frontend! Đang chạy 'npm run build'..." -ForegroundColor Yellow
                npm run build
            }

            # 8. Làm sạch cache hệ thống
            php artisan optimize:clear > $null 2>&1
            Write-Host "[$TIME] [OK] >> ĐỒNG BỘ HOÀN TẤT! Hệ thống đã cập nhật bản mới nhất. <<" -ForegroundColor Green
            Write-Host "[$TIME] ========================================================" -ForegroundColor Magenta
            Write-Host ""
        }
    }
    catch {
        Write-Host "[CẢNH BÁO] Tạm thời mất kết nối Git hoặc xảy ra lỗi nhẹ: $_" -ForegroundColor DarkYellow
    }

    Start-Sleep -Seconds $INTERVAL
}
