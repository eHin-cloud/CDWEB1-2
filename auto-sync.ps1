# =====================================================================
# SmartRoom & Renty - Auto Sync Git Daemon [SAFE MODE - AN TOAN TUYET DOI]
# Tu dong lang nghe, cat code do dang (Stash), keo code moi va phuc hoi
# =====================================================================
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
[Console]::InputEncoding  = [System.Text.Encoding]::UTF8
chcp 65001 >$null 2>&1
$Host.UI.RawUI.WindowTitle = "SmartRoom & Renty - Auto Sync Git Daemon [Safe Mode]"

# 1. Kiem tra xem may da cai dat Git chua
$gitCheck = Get-Command git -ErrorAction SilentlyContinue
if (-not $gitCheck) {
    Write-Host "[LOI] Khong tim thay Git trong PATH! Vui long cai dat Git tren may." -ForegroundColor Red
    Read-Host "Nhan Enter de thoat..."
    exit 1
}

# 2. Xac dinh nhanh hien tai
$CURRENT_BRANCH = (git rev-parse --abbrev-ref HEAD 2>$null)
if (-not $CURRENT_BRANCH) {
    $CURRENT_BRANCH = "main"
}
$INTERVAL = 20 # Kiem tra moi 20 giay

Clear-Host
Write-Host "====================================================================" -ForegroundColor Cyan
Write-Host "   SMARTROOM & RENTY - AUTO SYNC GIT DAEMON [CHE DO AN TOAN - STASH]   " -ForegroundColor Green
Write-Host "====================================================================" -ForegroundColor Cyan
Write-Host "• Nhanh theo doi:   " -NoNewline; Write-Host "$CURRENT_BRANCH" -ForegroundColor Yellow
Write-Host "• Chu ky quet:      " -NoNewline; Write-Host "$INTERVAL giay / lan" -ForegroundColor Yellow
Write-Host "• Che do an toan:   " -NoNewline; Write-Host "Tu dong cat code do dang -> Keo -> Khoi phuc code" -ForegroundColor Green
Write-Host "• Nhan Ctrl + C de dung tien trinh bat cu luc nao." -ForegroundColor Gray
Write-Host "--------------------------------------------------------------------" -ForegroundColor DarkGray
Write-Host "[!] Dang lang nghe thay doi tu Git Server... (Vui long giu cua so nay)" -ForegroundColor Cyan
Write-Host ""

while ($true) {
    try {
        # 1. Fetch metadata moi nhat tu remote origin
        git fetch origin $CURRENT_BRANCH 2>$null

        $LOCAL = (git rev-parse HEAD 2>$null).Trim()
        $REMOTE = (git rev-parse "origin/$CURRENT_BRANCH" 2>$null).Trim()

        # Neu ton tai commit hash hop le va khac nhau
        if ($LOCAL -and $REMOTE -and ($LOCAL -ne $REMOTE)) {
            $TIME = Get-Date -Format "HH:mm:ss dd/MM/yyyy"
            Write-Host "[$TIME] ========================================================" -ForegroundColor Magenta
            Write-Host "[$TIME] [!] Phat hien commit moi tren nhanh '$CURRENT_BRANCH'!" -ForegroundColor Green
            Write-Host "[$TIME] Local:  $LOCAL" -ForegroundColor Gray
            Write-Host "[$TIME] Remote: $REMOTE" -ForegroundColor Gray

            # 2. Kiem tra xem may cuc bo co code dang sua do dang chua commit khong
            $localStatus = git status --porcelain 2>$null
            $hasLocalChanges = [bool]($localStatus)

            if ($hasLocalChanges) {
                $stashTag = "auto-sync-stash-" + (Get-Date -Format "yyyyMMdd-HHmmss")
                Write-Host "[$TIME] [BAO VE] Phat hien ban co code dang sua do dang chua commit!" -ForegroundColor Yellow
                Write-Host "[$TIME] [BAO VE] Dang tu dong cat code vao ngan tam an toan (git stash)..." -ForegroundColor Yellow
                git stash push -u -m "$stashTag" 2>$null | Out-Null
            }

            # Lay danh sach file thay doi tu remote
            $CHANGED_FILES = git diff --name-only $LOCAL $REMOTE 2>$null

            # 3. Keo code moi ve
            Write-Host "[$TIME] [+] Dang keo code moi ve (git pull origin $CURRENT_BRANCH)..." -ForegroundColor Cyan
            $pullOutput = git pull origin $CURRENT_BRANCH 2>&1
            Write-Host $pullOutput -ForegroundColor Gray

            # 4. Khoi phuc lai phan code dang go do cua ban
            if ($hasLocalChanges) {
                Write-Host "[$TIME] [BAO VE] Dang dap lai phan code ban dang viet do dang..." -ForegroundColor Cyan
                $popOutput = git stash pop 2>&1

                # Kiem tra neu xay ra Conflict that su
                if ($LASTEXITCODE -ne 0 -or ($popOutput -match "CONFLICT")) {
                    try { [Console]::Beep(1000, 300) } catch {}
                    Write-Host "[$TIME] [CANH BAO CONFLICT] Xung dot ma nguon da duoc phat hien!" -ForegroundColor White -BackgroundColor DarkRed
                    Write-Host "[$TIME] Code moi keo ve bi trung dong voi doan code ban vua sua." -ForegroundColor Yellow
                    Write-Host "[$TIME] Git da giu nguyen ca hai va danh dau trong file bang: <<<<<<<" -ForegroundColor Yellow
                    Write-Host "[$TIME] >> Hay mo VS Code / IDE de chon giu code phu hop! <<" -ForegroundColor Magenta
                } else {
                    Write-Host "[$TIME] [OK] Da khoi phuc thanh cong code dang go, hoan toan khong bi mat mat!" -ForegroundColor Green
                }
            }

            # 5. Kiem tra xem co Migration moi khong
            $hasMigration = $CHANGED_FILES | Where-Object { $_ -match "^database/migrations" }
            if ($hasMigration) {
                Write-Host "[$TIME] [->] Phat hien Migration CSDL moi! Dang chay 'php artisan migrate --force'..." -ForegroundColor Yellow
                php artisan migrate --force
            }

            # 6. Kiem tra xem co thu vien Composer moi khong
            $hasComposer = $CHANGED_FILES | Where-Object { $_ -match "composer\.lock" }
            if ($hasComposer) {
                Write-Host "[$TIME] [->] Phat hien composer.lock thay doi! Dang chay 'composer install'..." -ForegroundColor Yellow
                composer install --no-interaction --prefer-dist
            }

            # 7. Kiem tra xem co thay doi Frontend khong
            $hasFrontend = $CHANGED_FILES | Where-Object { $_ -match "^resources/|^package\.json|^vite\.config\.js" }
            if ($hasFrontend) {
                Write-Host "[$TIME] [->] Phat hien thay doi Frontend! Dang chay 'npm run build'..." -ForegroundColor Yellow
                npm run build
            }

            # 8. Lam sach cache he thong
            php artisan optimize:clear > $null 2>&1
            Write-Host "[$TIME] [OK] >> DONG BO HOAN TAT! He thong da cap nhat ban moi nhat. <<" -ForegroundColor Green
            Write-Host "[$TIME] ========================================================" -ForegroundColor Magenta
            Write-Host ""
        }
    }
    catch {
        Write-Host "[CANH BAO] Tam thoi mat ket noi Git hoac xay ra loi nhe: $_" -ForegroundColor DarkYellow
    }

    Start-Sleep -Seconds $INTERVAL
}
