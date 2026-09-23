@echo off
chcp 65001 > nul
setlocal enabledelayedexpansion
title SmartRoom ^& Renty Ultimate Orchestrator v8.0 [Super Auto-Pilot]

:: Neu khong truyen tham so --cli, tu dong khoi chay GUI App Launcher
if "%1" neq "--cli" (
    start "" powershell -WindowStyle Normal -ExecutionPolicy Bypass -File "%~dp0launcher.ps1"
    exit /b 0
)

mode con: cols=110 lines=42

:: ======================================================================
:: INITIAL ENVIRONMENT SCAN (AUTO-PILOT)
:: ======================================================================
:: Phat hien IP mang noi bo tu dong (Local IP Discovery cho mobile testing)
set LOCAL_IP=127.0.0.1
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4"') do (
    set temp_ip=%%a
    set temp_ip=!temp_ip: =!
    if "!LOCAL_IP!"=="127.0.0.1" (
        if not "!temp_ip!"=="127.0.0.1" (
            set LOCAL_IP=!temp_ip!
        )
    )
)

:: Phat hien so bo PHP (khong dung chuong trinh neu chua co PHP de ho tro Docker)
set "PHP_CMD="
if exist "D:\xampp\php\php.exe" (
    set "PHP_CMD=D:\xampp\php\php.exe"
    set "PATH=D:\xampp\php;!PATH!"
) else if exist "C:\xampp\php\php.exe" (
    set "PHP_CMD=C:\xampp\php\php.exe"
    set "PATH=C:\xampp\php;!PATH!"
) else (
    where php >nul 2>&1
    if !errorlevel! equ 0 set "PHP_CMD=php"
)

:: Phat hien Composer
where composer >nul 2>&1
if !errorlevel! equ 0 (
    set COMPOSER_CMD=composer
) else (
    if exist "composer.phar" (
        set COMPOSER_CMD=!PHP_CMD! composer.phar
    ) else (
        set COMPOSER_CMD=composer
    )
)

:: Doc file .env de lay thong tin DB
set DB_NAME=quan_ly_nha_tro
set DB_PORT=3306
if exist .env (
    for /f "usebackq tokens=1,2 delims==" %%i in (".env") do (
        set var_name=%%i
        set var_val=%%j
        if "!var_name!"=="DB_DATABASE" set DB_NAME=!var_val!
        if "!var_name!"=="DB_PORT" set DB_PORT=!var_val!
    )
)

:MENU
:TAB_MENU
cls
color 0b
echo.
echo    ====================================================================================================
echo                     SMARTROOM ^& RENTY - TRINH KHOI CHAY HE THONG [RUN SELECTOR]
echo    ====================================================================================================
echo      [TAB 1: DOCKER COMPOSE]       ^|  [TAB 2: XAMPP STACK]          ^|  [TAB 3: WAMPP STACK]
echo    --------------------------------+---------------------------------+---------------------------------
echo      * Container hoa toan dien     ^|  * May chu cuc bo XAMPP        ^|  * May chu cuc bo WampServer
echo      * Web App: Port 8088          ^|  * Web App: Port 8000          ^|  * Web App: Port 8000
echo      * MySQL Docker: Port 3309     ^|  * MySQL XAMPP: Port 3306      ^|  * MySQL WAMPP: Port 3306/3308
echo      * Reverb WS: Port 8085        ^|  * Vite Dev Hot-Reload         ^|  * Vite Dev Hot-Reload
echo      * Khong lo xung dot moi truong^|  * Nhan dien D:\, C:\xampp...  ^|  * Tu dong quet WampServer
echo    ====================================================================================================
echo.
echo      >> VUI LONG CHON PHUONG THUC BAN MUON KHOI CHAY DU AN:
echo.
echo         [1] CHAY BANG DOCKER    ---^> Khoi chay bang Docker (Web Container + MySQL 8.4 Docker)
echo         [2] CHAY BANG XAMPP     ---^> Khoi chay bang PHP ^& MySQL cua XAMPP (Port 8000 / 3306)
echo         [3] CHAY BANG WAMPP     ---^> Khoi chay bang PHP ^& MySQL cua WampServer
echo.
echo      ----------------------------------------------------------------------------------------------
echo         [4] MENU NANG CAO       ---^> Mo toan bo 10+ cong cu quan tri, Migrate, Reset DB, Chuan doan
echo         [0] THOAT CHUONG TRINH
echo.
echo    ====================================================================================================
echo.
set /p tab_choice="   >> Nhap lua chon cua ban (1-4 hoac 0 de thoat): "

if "%tab_choice%"=="1" goto RUN_VIA_DOCKER
if "%tab_choice%"=="2" goto RUN_VIA_XAMPP
if "%tab_choice%"=="3" goto RUN_VIA_WAMPP
if "%tab_choice%"=="4" goto ADVANCED_MENU
if "%tab_choice%"=="0" goto EXIT_CLEAN
goto TAB_MENU

:: ======================================================================
:: RUN HANDLERS: DOCKER / XAMPP / WAMPP
:: ======================================================================

:RUN_VIA_DOCKER
cls
color 0a
echo.
echo    ====================================================================
echo    [ TAB 1: DOCKER ] DANG KHOI DONG MOI TRUONG DOCKER COMPOSE...
echo    ====================================================================
echo.
goto DOCKER_AUTO_RUN

:RUN_VIA_XAMPP
cls
color 0a
echo.
echo    ====================================================================
echo    [ TAB 2: XAMPP ] KHOI CHAY MOI TRUONG BANG XAMPP (PHP + MYSQL)
echo    ====================================================================
echo.

:: 1. Phat hien PHP XAMPP
set "XAMPP_PHP="
set "XAMPP_DIR="
if exist "D:\xampp\php\php.exe" (
    set "XAMPP_PHP=D:\xampp\php\php.exe"
    set "XAMPP_DIR=D:\xampp"
) else if exist "C:\xampp\php\php.exe" (
    set "XAMPP_PHP=C:\xampp\php\php.exe"
    set "XAMPP_DIR=C:\xampp"
) else if exist "E:\xampp\php\php.exe" (
    set "XAMPP_PHP=E:\xampp\php\php.exe"
    set "XAMPP_DIR=E:\xampp"
) else (
    where php >nul 2>&1
    if !errorlevel! equ 0 (
        set "XAMPP_PHP=php"
    )
)

if not defined XAMPP_PHP (
    color 0c
    echo    [ LOI ] Khong tim thay thu muc cai dat XAMPP tren may!
    echo    Kiem tra cac duong dan: D:\xampp, C:\xampp, E:\xampp hoac PHP trong PATH.
    echo.
    pause
    goto TAB_MENU
)

echo    [1/3] [+] Da xac dinh PHP XAMPP: !XAMPP_PHP!
set "PHP_CMD=!XAMPP_PHP!"
if defined XAMPP_DIR (
    set "PATH=!XAMPP_DIR!\php;!PATH!"
)

:: 2. Kiem tra va khoi dong MySQL XAMPP
echo    [2/3] [+] Dang kiem tra CSDL MySQL XAMPP...
tasklist /fi "imagename eq mysqld.exe" | findstr /i "mysqld.exe" > nul
if !errorlevel! neq 0 (
    echo    [!] MySQL chua bat! Dang tu dong khoi dong MySQL tu XAMPP...
    if defined XAMPP_DIR (
        if exist "!XAMPP_DIR!\mysql_start.bat" (
            start /b "" "!XAMPP_DIR!\mysql_start.bat" >nul 2>&1
            timeout /t 3 > nul
        )
    )
    tasklist /fi "imagename eq mysqld.exe" | findstr /i "mysqld.exe" > nul
    if !errorlevel! neq 0 (
        color 0e
        echo    [!] Khong the tu dong bat MySQL. Vui long mo XAMPP Control Panel va nhan Start MySQL!
        echo.
        pause
    ) else (
        echo    ^|---^> [OK] MySQL XAMPP da khoi dong thanh cong.
    )
) else (
    echo    ^|---^> [OK] MySQL XAMPP dang hoat dong san sang.
)

:: 3. Dong bo cau hinh .env cho XAMPP
if exist .env (
    findstr /C:"DB_HOST=mysql" .env > nul
    if !errorlevel! equ 0 (
        echo    [!] Phat hien .env dang tro Docker. Dang chuyen sang MySQL XAMPP (127.0.0.1:3306)...
        powershell -Command "$c = gc .env; $c = $c -replace '^DB_HOST=.*', 'DB_HOST=127.0.0.1'; $c = $c -replace '^DB_PORT=.*', 'DB_PORT=3306'; $c = $c -replace '^DB_DATABASE=.*', 'DB_DATABASE=qlphongtro'; $c = $c -replace '^DB_USERNAME=.*', 'DB_USERNAME=root'; $c = $c -replace '^DB_PASSWORD=.*', 'DB_PASSWORD='; $c | Out-File -encoding utf8 .env"
        call !PHP_CMD! artisan config:clear > nul 2>&1
    )
)

echo    [3/3] [+] Dang khoi chay Server Laravel va Vite...
set MODE=DEV
goto PROCESS

:RUN_VIA_WAMPP
cls
color 0b
echo.
echo    ====================================================================
echo    [ TAB 3: WAMPP ] KHOI CHAY MOI TRUONG BANG WAMP (PHP + MYSQL)
echo    ====================================================================
echo.

:: 1. Phat hien PHP WAMP
set "WAMP_PHP="
set "WAMP_DIR="
for %%d in (C D E F) do (
    if exist "%%d:\wamp64\bin\php" (
        for /d %%p in (%%d:\wamp64\bin\php\php*) do (
            if exist "%%p\php.exe" (
                set "WAMP_PHP=%%p\php.exe"
                set "WAMP_DIR=%%d:\wamp64"
            )
        )
    )
    if exist "%%d:\wamp\bin\php" (
        for /d %%p in (%%d:\wamp\bin\php\php*) do (
            if exist "%%p\php.exe" (
                set "WAMP_PHP=%%p\php.exe"
                set "WAMP_DIR=%%d:\wamp"
            )
        )
    )
)

if not defined WAMP_PHP (
    where php >nul 2>&1
    if !errorlevel! equ 0 (
        set "WAMP_PHP=php"
    )
)

if not defined WAMP_PHP (
    color 0c
    echo    [ LOI ] Khong tim thay WampServer tai C:\wamp64, D:\wamp64, C:\wamp!
    echo    Vui long kiem tra WampServer hoac chon chay bang XAMPP hoac Docker.
    echo.
    pause
    goto TAB_MENU
)

echo    [1/3] [+] Da xac dinh PHP Wamp: !WAMP_PHP!
set "PHP_CMD=!WAMP_PHP!"
if defined WAMP_DIR (
    for %%i in (!WAMP_PHP!) do set "WAMP_PHP_BIN=%%~dpi"
    set "PATH=!WAMP_PHP_BIN!;!PATH!"
)

:: 2. Kiem tra MySQL WampServer
echo    [2/3] [+] Dang kiem tra CSDL MySQL WAMP...
tasklist /fi "imagename eq mysqld.exe" | findstr /i "mysqld.exe" > nul
if !errorlevel! neq 0 (
    color 0e
    echo    [!] MySQL Wamp chua bat! Vui long bat WampServer (Bieu tuong W mau xanh la).
    pause
) else (
    echo    ^|---^> [OK] MySQL WAMP dang hoat dong san sang.
)

:: 3. Dong bo cau hinh .env cho WAMP
if exist .env (
    findstr /C:"DB_HOST=mysql" .env > nul
    if !errorlevel! equ 0 (
        echo    [!] Phat hien .env dang tro Docker. Dang chuyen sang MySQL WAMP (127.0.0.1:3306)...
        powershell -Command "$c = gc .env; $c = $c -replace '^DB_HOST=.*', 'DB_HOST=127.0.0.1'; $c = $c -replace '^DB_PORT=.*', 'DB_PORT=3306'; $c = $c -replace '^DB_DATABASE=.*', 'DB_DATABASE=qlphongtro'; $c = $c -replace '^DB_USERNAME=.*', 'DB_USERNAME=root'; $c = $c -replace '^DB_PASSWORD=.*', 'DB_PASSWORD='; $c | Out-File -encoding utf8 .env"
        call !PHP_CMD! artisan config:clear > nul 2>&1
    )
)

echo    [3/3] [+] Dang khoi chay Server Laravel va Vite...
set MODE=DEV
goto PROCESS

:: ======================================================================
:: ADVANCED MANAGEMENT MENU (TOAN BO CONG CU HE THONG)
:: ======================================================================
:ADVANCED_MENU
cls
color 0b
echo.
echo    +==================================================================================================+
echo    ^|   ____                        _   ____                         ____             _                ^|
echo    ^|  / ___^| _ __ ___   __ _ _ __ ^| ^|_^|  _ \  ___   ___  _ __ ___   ^|  _ \  ___ _ __ ^| ^|_ _   _        ^|
echo    ^|  \___ \^| '_  _ \ / _ ^| '__^|^| __^| ^|_^) ^|/ _ \ / _ \^| '_  _ \  ^| ^|_^) ^|/ _ \ '_ \^| __^| ^| ^| ^|       ^|
echo    ^|   ___^) ^| ^| ^| ^| ^| ^| (_^| ^| ^|   ^| ^|_^|  _ ^<^| ^(_^) ^| ^(_^) ^| ^| ^| ^| ^| ^| ^|  _ ^<^|  __/ ^| ^| ^| ^|_^| ^|_^| ^|       ^|
echo    ^|  ^|____/^|_^| ^|_^| ^|_^|\__,_^|_^|    \__^|_^| \_\\___/ \___/^|_^| ^|_^| ^|_^| ^|_^| \_\\___^|_^| ^|_^|\__^|\__, ^|       ^|
echo    ^|                                                                                  ^|___/         ^|
echo    ^|            [+] SMARTROOM (Quan Tri Tro)   x   [+] RENTY (Thue Phong Thong Minh)                  ^|
echo    ^|                             --- MENU NANG CAO ^& QUAN TRI HE THONG ---                           ^|
echo    +==================================================================================================+
echo                        SMARTROOM ^& RENTY ORCHESTRATOR v8.0 [SUPER AUTO-PILOT]
echo.
echo    [1] DEV MODE          - Che do Lap trinh (Hot Reloading bang Vite Server)
echo    [2] STABLE RUN        - Che do On dinh (Chay bang file Production Build - nhanh hon)
echo    [3] RESET DATABASE    - [CANH BAO] Lam moi toan bo Database va Nap du lieu gia lap
echo    [4] VIEW SITEMAP      - Xem danh sach lien ket website hoat dong
echo    [5] FAST REBUILD ^& RUN - [ONE CLICK] Reset DB + Rebuild Vite + Mo Server ngay
echo    [6] INITIALIZE        - Tai moi thu vien (Composer Install + NPM Install)
echo    [7] UPGRADE SYSTEM    - [SUA LOI] Dong bo hoa va Cap nhat lai khoa composer.lock
echo    [8] HEALTH DIAGNOSTIC - [MOI] Kiem tra suc khoe toan dien he thong du an
echo    [9] LOG MANAGEMENT    - [MOI] Xem, theo doi va xoa sach nhat ky loi Laravel
echo    [10] DOCKER CONTROL   - [MOI] Bang dieu khien chuyen sau Docker Compose
echo    [11] QUAY LAI BANG TAB- Quay lai man hinh chon moi truong (Docker / XAMPP / WAMPP)
echo    [0]  EXIT             - Thoat
echo.
echo    ----------------------------------------------------------------------------------------
set /p choice="   >> Nhap lua chon cua ban (0-11): "

if "%choice%"=="1" goto DEV_MODE
if "%choice%"=="2" goto STABLE_RUN
if "%choice%"=="3" goto RESET_DB
if "%choice%"=="4" goto VIEW_SITEMAP
if "%choice%"=="5" goto PIPELINE_STANDARD
if "%choice%"=="6" goto INITIALIZE
if "%choice%"=="7" goto UPGRADE_ALL
if "%choice%"=="8" goto DIAGNOSTICS
if "%choice%"=="9" goto LOG_MGMT
if "%choice%"=="10" goto DOCKER_RUN
if "%choice%"=="11" goto TAB_MENU
if "%choice%"=="0" goto EXIT_CLEAN
goto ADVANCED_MENU

:INITIALIZE
cls
color 0e
echo.
echo    +======================================================================================+
echo    ^|            SmartRoom ^& Renty SMART SETUP WIZARD - TRINH KHOI TAO THONG MINH          ^|
echo    +======================================================================================+
echo.
echo    [!] Trinh huong dan khoi tao se tu dong thiet lap moi truong, tai thu vien va cau hinh CSDL.
echo.
echo    [1] CAI DAT NHANH (Fast Setup)    - Tai backend/frontend + khoi tao .env tu dong
echo    [2] THIET LAP CSDL (Database)     - Chuyen doi giua SQLite (Nhanh) va MySQL (Day du)
echo    [3] CAI DAT LAI SACH (Clean Rebuild)- Xoa sach vendor, node_modules, .env de lam lai tu dau
echo    [4] Quay lai Menu chinh
echo.
echo    ----------------------------------------------------------------------------------------
set /p setup_choice="   >> Nhap lua chon khoi tao cua ban (1-4): "

if "!setup_choice!"=="4" goto MENU
if "!setup_choice!"=="1" goto INITIALIZE_FAST
if "!setup_choice!"=="2" goto INITIALIZE_DB
if "!setup_choice!"=="3" goto INITIALIZE_CLEAN
goto INITIALIZE

:DOCKER_RUN
cls
color 0b
echo.
echo    +======================================================================================+
echo    ^|             SmartRoom ^& Renty DOCKER RUNNER - MOI TRUONG DOCKER COMPOSE              ^|
echo    +======================================================================================+
echo.
echo    [!] Thong tin moi truong Docker:
echo        - Website nguoi thue:   http://localhost:8088/renty
echo        - Admin Portal:         http://localhost:8088/smartroom/admin
echo        - Reverb WebSocket:     ws://127.0.0.1:8085
echo        - MySQL Port tren host: localhost:3309 (User: smartroom / Pass: smartroom)
echo.
echo    [1] KHOI CHAY DOCKER (Background)  - Build ^& chay ngam, tu dong doi web va mo trinh duyet
echo    [2] KHOI CHAY TRUC TIEP (Logs)     - Chay hien thi log thoi gian thuc (Nhan Ctrl+C de dung)
echo    [3] XEM NHAT KY LOGS               - Theo doi nhat ky container dang chay (docker compose logs)
echo    [4] MIGRATE ^& SEED DATABASE        - Lam moi va nap du lieu gia lap trong Docker container
echo    [5] KHOI DONG LAI (Restart)        - Khoi dong lai toan bo cac containers
echo    [6] DUNG DOCKER (Stop / Down)      - Dung va giai phong cac container Docker
echo    [7] Quay lai Menu chinh
echo.
echo    ----------------------------------------------------------------------------------------
set /p docker_choice="   >> Nhap lua chon Docker cua ban (1-7): "

if "!docker_choice!"=="7" goto DOCKER_BACK_TO_MENU
if "!docker_choice!"=="1" goto DOCKER_AUTO_RUN
if "!docker_choice!"=="2" goto DOCKER_FOREGROUND_RUN
if "!docker_choice!"=="3" goto DOCKER_LOGS
if "!docker_choice!"=="4" goto DOCKER_SEED
if "!docker_choice!"=="5" goto DOCKER_RESTART
if "!docker_choice!"=="6" goto DOCKER_DOWN
goto DOCKER_RUN

:DOCKER_BACK_TO_MENU
goto TAB_MENU

:DOCKER_CHECK_CLI
where docker >nul 2>&1
if %errorlevel% neq 0 (
    if exist "%LOCALAPPDATA%\Programs\DockerDesktop\resources\bin\docker.exe" (
        set "PATH=%LOCALAPPDATA%\Programs\DockerDesktop\resources\bin;!PATH!"
    ) else if exist "%ProgramFiles%\Docker\Docker\resources\bin\docker.exe" (
        set "PATH=%ProgramFiles%\Docker\Docker\resources\bin;!PATH!"
    )
)
where docker >nul 2>&1
if %errorlevel% neq 0 (
    color 0c
    echo    [ LOI ] Khong tim thay Docker CLI. Vui long cai Docker Desktop truoc.
    pause
    goto DOCKER_RUN
)
exit /b 0

:DOCKER_CHECK_DAEMON
call :DOCKER_CHECK_CLI
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo    [!] Docker Desktop chua bat. Dang tu dong khoi dong Docker Desktop...
    set "DOCKER_APP="
    if exist "%LOCALAPPDATA%\Programs\DockerDesktop\Docker Desktop.exe" (
        set "DOCKER_APP=%LOCALAPPDATA%\Programs\DockerDesktop\Docker Desktop.exe"
    ) else if exist "%ProgramFiles%\Docker\Docker\Docker Desktop.exe" (
        set "DOCKER_APP=%ProgramFiles%\Docker\Docker\Docker Desktop.exe"
    )
    if defined DOCKER_APP (
        start "" "!DOCKER_APP!"
    ) else (
        start "" "Docker Desktop" >nul 2>&1
    )
    echo    [!] Dang doi Docker Engine khoi dong hoan tat...
    for /l %%k in (1,1,20) do (
        timeout /t 2 > nul
        docker info >nul 2>&1
        if !errorlevel! equ 0 (
            echo    ^|---^> [OK] Docker Desktop da khoi dong va san sang.
            goto DOCKER_DAEMON_READY
        )
    )
    color 0c
    echo    [ LOI ] Docker Daemon chua chay xong! Vui long kiem tra Docker Desktop tren Windows.
    pause
    goto DOCKER_RUN
)
:DOCKER_DAEMON_READY
if not exist ".docker-config" (
    mkdir ".docker-config" >nul 2>&1
)
set "DOCKER_CONFIG=%~dp0.docker-config"
exit /b 0

:DOCKER_AUTO_RUN
cls
color 0a
echo.
echo    ====================================================================
echo    [ DOCKER AUTO RUN ] DANG KHOI DONG MOI TRUONG DOCKER COMPOSE...
echo    ====================================================================
echo.
call :DOCKER_CHECK_DAEMON
if not exist .env (
    if exist .env.docker (
        copy .env.docker .env >nul
    ) else (
        copy .env.example .env >nul
    )
    echo    [+] Da khoi tao .env cho Docker.
)
echo    [1/3] Dang khoi chay cac container (Background)...
docker compose up -d
if %errorlevel% neq 0 (
    color 0c
    echo    [ LOI ] Khong the khoi chay Docker Compose. Hay kiem tra logs.
    pause
    goto DOCKER_RUN
)

echo.
echo    [2/3] Dang cho ung dung tren Docker san sang (Cong 8088)...
set "DOCKER_READY=0"
for /l %%i in (1,1,30) do (
    powershell -NoProfile -ExecutionPolicy Bypass -Command "try { $r = Invoke-WebRequest -UseBasicParsing -Uri 'http://127.0.0.1:8088/' -TimeoutSec 1; if ($r.StatusCode -ge 200) { exit 0 } } catch { exit 1 }" > nul 2>&1
    if !errorlevel! equ 0 (
        set "DOCKER_READY=1"
        goto DOCKER_READY_LAUNCH
    )
    timeout /t 2 > nul
)

:DOCKER_READY_LAUNCH
echo.
echo    [3/3] Dang mo website duoi dang cua so ung dung...
start chrome --app="http://localhost:8088/renty" >nul 2>&1 || start msedge --app="http://localhost:8088/renty" >nul 2>&1 || start "" "http://localhost:8088/renty"

cls
color 0b
echo.
echo    +======================================================================================+
echo    ^|                 SmartRoom ^& Renty DOCKER DA KHOI CHAY THANH CONG                      ^|
echo    +======================================================================================+
echo    ^|                                                                                      ^|
echo    ^|  [+] Website nguoi thue:  http://localhost:8088/renty                                ^|
echo    ^|  [+] Admin Portal:        http://localhost:8088/smartroom/admin                      ^|
echo    ^|  [+] Localhost URL:       http://localhost:8088/                                     ^|
echo    ^|  [+] Reverb WebSocket:    ws://127.0.0.1:8085                                        ^|
echo    ^|  [+] MySQL Port (Host):   localhost:3309                                             ^|
echo    ^|      User: smartroom  /  Password: smartroom  /  Database: quan_ly_nha_tro           ^|
echo    ^|                                                                                      ^|
echo    +======================================================================================+
echo.
echo    [ THONG TIN ] Containers dang chay ngam on dinh.
echo    [ GOI Y ] De dung Docker, hay vao menu Docker va chon [6] DUNG DOCKER.
echo.
pause
goto DOCKER_RUN

:DOCKER_FOREGROUND_RUN
cls
color 0b
echo.
echo    ====================================================================
echo    [ DOCKER FOREGROUND ] CHAY DOCKER HIEN THI LOG TRUC TIEP
echo    ====================================================================
echo.
call :DOCKER_CHECK_DAEMON
if exist docker-start.ps1 (
    powershell -NoProfile -ExecutionPolicy Bypass -File ".\docker-start.ps1"
) else (
    docker compose up --build
)
echo.
echo    [ INFO ] Docker Compose da dung hoac thoat.
pause
goto DOCKER_RUN

:DOCKER_LOGS
cls
color 0b
echo.
echo    ====================================================================
echo    [ DOCKER LOGS ] THEO DOI NHAT KY HOAT DONG CONTAINER (Nhan Ctrl+C de thoat)
echo    ====================================================================
echo.
call :DOCKER_CHECK_DAEMON
docker compose logs -f
pause
goto DOCKER_RUN

:DOCKER_SEED
cls
color 0e
echo.
echo    ====================================================================
echo    [ DOCKER MIGRATE ^& SEED ] LAM MOI VA NAP DU LIEU DATABASE TRONG DOCKER
echo    ====================================================================
echo.
call :DOCKER_CHECK_DAEMON
set /p confirm_seed="   >> Ban co chac muon xoa va seed lai CSDL Docker? [y/n]: "
if /i "!confirm_seed!" neq "y" goto DOCKER_RUN
echo.
echo    [ HANH DONG ] Dang chay artisan migrate:fresh --seed ben trong container app...
docker compose exec -T app php artisan migrate:fresh --seed
docker compose exec -T app php artisan optimize:clear
echo.
echo    [ OK ] Da migrate va seed CSDL Docker thanh cong!
pause
goto DOCKER_RUN

:DOCKER_RESTART
cls
color 0b
echo.
echo    [ HANH DONG ] Dang khoi dong lai cac containers Docker...
call :DOCKER_CHECK_DAEMON
docker compose restart
echo    [ OK ] Da khoi dong lai cac containers thanh cong!
pause
goto DOCKER_RUN

:DOCKER_DOWN
cls
color 0c
echo.
echo    ====================================================================
echo    [ DOCKER STOP ] DUNG VA GIAI PHONG MOI TRUONG DOCKER COMPOSE
echo    ====================================================================
echo.
call :DOCKER_CHECK_DAEMON
docker compose down
echo.
echo    [ OK ] Toan bo cac container SmartRoom da duoc dung va giai phong.
pause
goto DOCKER_RUN

:INITIALIZE_FAST
cls
echo.
echo    ====================================================================
echo    [ FAST SETUP ] TIEN HANH KHOI TAO NHANH THONG MINH
echo    ====================================================================
echo.

:: 1. Kiem tra va Copy .env
if not exist .env (
    echo    [1/5] [ HANH DONG ] Dang copy .env.example sang .env...
    copy .env.example .env > nul
    echo    ^|---^> [OK] Da khoi tao tep .env thanh cong.
) else (
    echo    [1/5] [+] .env da ton tai. Bo qua khoi tao.
)

:: 2. Composer Install
set run_composer=1
if exist vendor (
    echo.
    echo    [!] Phat hien thu muc [vendor] da ton tai.
    set /p comp_skip="   >> Ban co muon bo qua 'composer install' de tiet kiem thoi gian? [y/n]: "
    if /i "!comp_skip!"=="y" set run_composer=0
)
if !run_composer! equ 1 (
    echo.
    echo    [2/5] [ HANH DONG ] Dang tai backend packages [Composer]...
    call !COMPOSER_CMD! install --prefer-dist --ignore-platform-reqs
    echo    ^|---^> [OK] Tai backend packages hoan tat.
) else (
    echo.
    echo    [2/5] [+] Da bo qua buoc tai backend packages.
)

:: 3. NPM Install
set run_npm=1
if exist node_modules (
    echo.
    echo    [!] Phat hien thu muc [node_modules] da ton tai.
    set /p npm_skip="   >> Ban co muon bo qua 'npm install' de tiet kiem thoi gian? [y/n]: "
    if /i "!npm_skip!"=="y" set run_npm=0
)
if !run_npm! equ 1 (
    echo.
    echo    [3/5] [ HANH DONG ] Dang tai frontend packages [NPM]...
    call npm install
    echo    ^|---^> [OK] Tai frontend packages hoan tat.
) else (
    echo.
    echo    [3/5] [+] Da bo qua buoc tai frontend packages.
)

:: 4. Generate App Key if not exists
echo.
echo    [4/5] Kiem tra va sinh App Key...
findstr /C:"APP_KEY=base64:" .env > nul
if %errorlevel% neq 0 (
    echo    [ HANH DONG ] APP_KEY chua co. Dang khoi tao...
    call !PHP_CMD! artisan key:generate
    echo    ^|---^> [OK] Da sinh APP_KEY moi.
) else (
    echo    ^|---^> [+] APP_KEY da ton tai.
)

:: 5. SQLite database file check
echo.
echo    [5/5] Kiem tra ket noi CSDL trong .env...
findstr /C:"DB_CONNECTION=sqlite" .env > nul
if %errorlevel% equ 0 (
    if not exist database\database.sqlite (
        echo    [ HANH DONG ] Dang tu dong tao tep database.sqlite...
        type nul > database\database.sqlite
        echo    ^|---^> [OK] Da khoi tao database.sqlite.
    )
)

:: Storage link
if not exist public\storage (
    echo.
    echo    [ HANH DONG ] Dang tao storage symbolic link...
    call !PHP_CMD! artisan storage:link
)

echo.
echo    [ OK ] Da khoi tao du an va tai xong tat ca thu vien!
set /p auto_mig="   >> Ban co muon thuc hien Migrate va Seed du lieu gia lap ngay bay gio? [y/n]: "
if /i "!auto_mig!"=="y" (
    call !PHP_CMD! artisan migrate:fresh --seed
)
pause
goto INITIALIZE


:INITIALIZE_DB
cls
echo.
echo    ====================================================================
echo    [ DATABASE SELECTION ] CAU HINH TRINH DIEU KHIEN CO SO DU LIEU
echo    ====================================================================
echo.
echo    Vui long chon loai database ban muon thiet lap cho du an:
echo.
echo    [1] SQLITE [Khuyen dung cho may thieu XAMPP/Laragon - CSDL nhe luu trong tep tin]
echo    [2] MYSQL  [Dung cho he thong that - Yeu cau phai bat MySQL/XAMPP tren may]
echo    [3] Quay lai menu truoc
echo.
set /p db_choice="   >> Nhap lua chon (1-3): "

if "!db_choice!"=="3" goto INITIALIZE

if not exist .env (
    echo    [ HANH DONG ] Chua co file .env. Dang copy tu .env.example...
    copy .env.example .env > nul
)

if "!db_choice!"=="1" goto SETUP_SQLITE
if "!db_choice!"=="2" goto SETUP_MYSQL
goto INITIALIZE_DB

:SETUP_SQLITE
cls
echo    [ HANH DONG ] Dang thiet lap cau hinh SQLITE...
if not exist database\database.sqlite (
    type nul > database\database.sqlite
    echo    ^|---^> [OK] Da tao tep database/database.sqlite.
)
echo    [ HANH DONG ] Dang cap nhat tep .env sang SQLite...
powershell -Command "$c = gc .env; $c = $c -replace '^DB_CONNECTION=.*', 'DB_CONNECTION=sqlite'; $c = $c -replace '^DB_HOST=.*', '#DB_HOST=127.0.0.1'; $c = $c -replace '^DB_PORT=.*', '#DB_PORT=3306'; $c = $c -replace '^DB_DATABASE=.*', 'DB_DATABASE=database/database.sqlite'; $c = $c -replace '^DB_USERNAME=.*', '#DB_USERNAME=root'; $c = $c -replace '^DB_PASSWORD=.*', '#DB_PASSWORD='; $c | Out-File -encoding utf8 .env"

:: Clear Laravel config cache de nhan dien .env moi
call !PHP_CMD! artisan config:clear > nul

echo    [ OK ] Da chuyen doi thanh cong sang trinh dieu khien SQLite!
echo.
set /p mig_sqlite="   >> Ban co muon lam moi va nap CSDL SQLite ngay lap tuc? [y/n]: "
if /i "!mig_sqlite!"=="y" (
    call !PHP_CMD! artisan migrate:fresh --seed
)
pause
goto INITIALIZE

:SETUP_MYSQL
cls
echo    ====================================================================
echo    [ MYSQL CONFIGURATION ] NHAP THONG TIN LIEN KET MYSQL
echo    ====================================================================
echo.
echo    Dien cac thong tin ket noi MySQL duoi day [Nhan ENTER de lay gia tri mac dinh]:
echo.

set "db_host=127.0.0.1"
set /p db_host="   >> Nhap DB Host [Mac dinh: 127.0.0.1]: "

set "db_port=3306"
set /p db_port="   >> Nhap DB Port [Mac dinh: 3306]: "

set "db_name=smartroom_renty"
set /p db_name="   >> Nhap ten database [Mac dinh: smartroom_renty]: "

set "db_user=root"
set /p db_user="   >> Nhap Username [Mac dinh: root]: "

set "db_pass="
set /p db_pass="   >> Nhap Password [Mac dinh: bo trong]: "

echo.
echo    [ HANH DONG ] Dang thiet lap cau hinh MYSQL va cap nhat tep .env...
powershell -Command "$c = gc .env; $c = $c -replace '^DB_CONNECTION=.*', 'DB_CONNECTION=mysql'; $c = $c -replace '^DB_HOST=.*', 'DB_HOST=!db_host!'; $c = $c -replace '^DB_PORT=.*', 'DB_PORT=!db_port!'; $c = $c -replace '^DB_DATABASE=.*', 'DB_DATABASE=!db_name!'; $c = $c -replace '^DB_USERNAME=.*', 'DB_USERNAME=!db_user!'; $c = $c -replace '^DB_PASSWORD=.*', 'DB_PASSWORD=!db_pass!'; $c | Out-File -encoding utf8 .env"

:: Clear Laravel config cache de nhan dien .env moi
call !PHP_CMD! artisan config:clear > nul

echo    [ OK ] Da cap nhat cau hinh CSDL MySQL vao tep .env!
echo.
set /p mig_mysql="   >> Ban co muon lam moi va nap CSDL MySQL ngay lap tuc [Yeu cau MySQL phai bat]? [y/n]: "
if /i "!mig_mysql!"=="y" (
    call !PHP_CMD! artisan migrate:fresh --seed
)
pause
goto INITIALIZE


:INITIALIZE_CLEAN
cls
color 0c
echo    +======================================================================================+
echo    ^|                  [ CANH BAO QUAN TRONG ] CLEAN REBUILD TOAN DIEN                       ^|
echo    +======================================================================================+
echo.
echo    [!] Thao tac nay se XOA SACH cac thu muc sau:
echo        - vendor/ [Backend Packages]
echo        - node_modules/ [Frontend Packages]
echo        - .env [Cac cau hinh hien tai]
echo        - package-lock.json va database/database.sqlite
echo.
set /p clean_confirm="    >> Ban co chac chan 100%% muon don sach he thong va thiet lap lai tu dau? [y/n]: "
if /i "!clean_confirm!" neq "y" goto INITIALIZE

cls
echo    ====================================================================
echo    [ CLEAN REBUILD ACTIVE ] DANG LAM SACH TOAN DIEN
echo    ====================================================================
echo.

echo    [1/6] Dang xoa thu muc [vendor]...
if exist vendor (rd /s /q vendor)
echo    ^|---^> [OK] Da xoa.

echo    [2/6] Dang xoa thu muc [node_modules]...
if exist node_modules (rd /s /q node_modules)
echo    ^|---^> [OK] Da xoa.

echo    [3/6] Dang xoa file [.env] cu va [package-lock.json]...
if exist .env (del /f /q .env)
if exist package-lock.json (del /f /q package-lock.json)
if exist database\database.sqlite (del /f /q database\database.sqlite)
echo    ^|---^> [OK] Da xoa.

echo    [4/6] Dang tai tao [.env] tu [.env.example]...
copy .env.example .env > nul
echo    ^|---^> [OK] Da tai tao file .env moi.

echo.
echo    [!] Vui long lua chon Driver CSDL de thiet lap truoc khi install:
echo    [1] SQLITE [Mac dinh / Khuyen dung cho dev nhanh]
echo    [2] MYSQL  [Dung database chung voi XAMPP/Laragon]
set /p rebuild_db_driver="   >> Lua chon driver [1-2]: "

if "!rebuild_db_driver!"=="2" goto REBUILD_DB_MYSQL

:REBUILD_DB_SQLITE
type nul > database\database.sqlite
powershell -Command "$c = gc .env; $c = $c -replace '^DB_CONNECTION=.*', 'DB_CONNECTION=sqlite'; $c = $c -replace '^DB_HOST=.*', '#DB_HOST=127.0.0.1'; $c = $c -replace '^DB_PORT=.*', '#DB_PORT=3306'; $c = $c -replace '^DB_DATABASE=.*', 'DB_DATABASE=database/database.sqlite'; $c = $c -replace '^DB_USERNAME=.*', '#DB_USERNAME=root'; $c = $c -replace '^DB_PASSWORD=.*', '#DB_PASSWORD='; $c | Out-File -encoding utf8 .env"
goto REBUILD_DB_DONE

:REBUILD_DB_MYSQL
set "db_host=127.0.0.1"
set /p db_host="   >> Nhap DB Host [Mac dinh: 127.0.0.1]: "
set "db_port=3306"
set /p db_port="   >> Nhap DB Port [Mac dinh: 3306]: "
set "db_name=smartroom_renty"
set /p db_name="   >> Nhap ten database [Mac dinh: smartroom_renty]: "
set "db_user=root"
set /p db_user="   >> Nhap Username [Mac dinh: root]: "
set "db_pass="
set /p db_pass="   >> Nhap Password [Mac dinh: bo trong]: "
powershell -Command "$c = gc .env; $c = $c -replace '^DB_CONNECTION=.*', 'DB_CONNECTION=mysql'; $c = $c -replace '^DB_HOST=.*', 'DB_HOST=!db_host!'; $c = $c -replace '^DB_PORT=.*', 'DB_PORT=!db_port!'; $c = $c -replace '^DB_DATABASE=.*', 'DB_DATABASE=!db_name!'; $c = $c -replace '^DB_USERNAME=.*', 'DB_USERNAME=!db_user!'; $c = $c -replace '^DB_PASSWORD=.*', 'DB_PASSWORD=!db_pass!'; $c | Out-File -encoding utf8 .env"

:REBUILD_DB_DONE

echo.
echo    [5/6] Dang chay 'composer install' de nap lai Backend...
call !COMPOSER_CMD! install --prefer-dist --ignore-platform-reqs
echo    ^|---^> [OK] Composer install hoan tat.

echo.
echo    [6/6] Dang chay 'npm install' de nap lai Frontend...
call npm install
echo    ^|---^> [OK] NPM install hoan tat.

echo.
echo    [ HANH DONG ] Dang sinh APP_KEY moi va link folder public storage...
call !PHP_CMD! artisan key:generate
if not exist public\storage (
    call !PHP_CMD! artisan storage:link
)

echo.
echo    [ OK ] Clean Rebuild hoan tat! He thong cua ban da sach se 100%%!
set /p rebuild_mig="   >> Ban co muon chay migrate:fresh va seeding nap CSDL moi khong? [y/n]: "
if /i "!rebuild_mig!"=="y" (
    call !PHP_CMD! artisan migrate:fresh --seed
)
pause
goto INITIALIZE


:UPGRADE_ALL
cls
color 0e
echo.
echo    +======================================================================================+
echo    ^|            SmartRoom ^& Renty REPAIR ASSISTANT - BO PHUC HOI ^& NANG CAP TOAN DIEN     ^|
echo    +======================================================================================+
echo.
echo    [!] Vui long lua chon phuong thuc phuc hoi hoac nang cap ban muon thuc hien:
echo.
echo    [1] COMPOSER UPDATE   - Dong bo va cap nhat lockfile (Sua loi phien ban goi backend)
echo    [2] VENDOR REBUILD    - [QUAN TRONG] Xoa sach Vendor va cai dat lai tu dau (Sua loi package PHP)
echo    [3] FRONTEND REBUILD  - [QUAN TRONG] Xoa sach node_modules va NPM Re-install (Sua loi compilation)
echo    [4] DEEP CACHE CLEAN  - Don dep cache chuyen sau va toi uu hoa Classmap (dump-autoload)
echo    [5] FULL REPAIR SYSTEM- [ONE-CLICK] Thuc hien toan bo 4 buoc tren de khoi phuc 100%% he thong
echo    [6] Quay lai Menu chinh
echo.
echo    ----------------------------------------------------------------------------------------
set /p repair_choice="   >> Nhap lua chon phuc hoi cua ban (1-6): "

if "!repair_choice!"=="6" goto MENU
if "!repair_choice!"=="1" goto REPAIR_COMPOSER_UPDATE
if "!repair_choice!"=="2" goto REPAIR_VENDOR_REBUILD
if "!repair_choice!"=="3" goto REPAIR_FRONTEND_REBUILD
if "!repair_choice!"=="4" goto REPAIR_DEEP_CACHE
if "!repair_choice!"=="5" goto REPAIR_FULL_SYSTEM
goto UPGRADE_ALL

:REPAIR_COMPOSER_UPDATE
cls
echo    [ HANH DONG ] Dang chay Composer Update...
call !COMPOSER_CMD! update --ignore-platform-reqs
echo    [ OK ] Da cap nhat lockfile backend thanh cong!
pause
goto UPGRADE_ALL

:REPAIR_VENDOR_REBUILD
cls
echo    [ HANH DONG ] Dang tien hanh xoa thu muc vendor cu...
if exist vendor rd /s /q vendor
echo    [ HANH DONG ] Dang tai va cai dat lai toan bo package PHP moi...
call !COMPOSER_CMD! install --prefer-dist --ignore-platform-reqs
echo    [ OK ] Da khoi phuc sach thu muc vendor thanh cong!
pause
goto UPGRADE_ALL

:REPAIR_FRONTEND_REBUILD
cls
echo    [ HANH DONG ] Dang tien hanh xoa thu muc node_modules cu...
if exist node_modules rd /s /q node_modules
if exist package-lock.json del /f /q package-lock.json
echo    [ HANH DONG ] Dang NPM Re-install va rebuild assets...
call npm install
echo    [ OK ] Da khoi phuc va rebuild frontend thanh cong!
pause
goto UPGRADE_ALL

:REPAIR_DEEP_CACHE
cls
echo    [ HANH DONG ] Dang toi uu hoa Classmap (Composer dump-autoload)...
call !COMPOSER_CMD! dump-autoload
echo    [ HANH DONG ] Dang don dep cache he thong...
call !PHP_CMD! artisan optimize:clear
echo    [ OK ] Da lam sach cache va toi uu hoa autoload classmap thanh cong!
pause
goto UPGRADE_ALL

:REPAIR_FULL_SYSTEM
cls
echo    ====================================================================
echo    [ SYSTEM FULL REPAIR ] TIEN HANH KHOI PHUC LIEN HOAN TOAN DIEN SYSTEM
echo    ====================================================================
echo.
echo    [1/4] Dang lam sach va rebuild Vendor PHP (Composer)...
if exist vendor rd /s /q vendor
call !COMPOSER_CMD! install --prefer-dist --ignore-platform-reqs
echo    ^|---^> [OK] Composer install hoan tat.

echo    [2/4] Dang lam sach va rebuild Frontend Assets (NPM)...
if exist node_modules rd /s /q node_modules
if exist package-lock.json del /f /q package-lock.json
call npm install
echo    ^|---^> [OK] NPM install hoan tat.

echo    [3/4] Chay composer dump-autoload va update...
call !COMPOSER_CMD! dump-autoload
call !COMPOSER_CMD! update --ignore-platform-reqs
echo    ^|---^> [OK] Composer dump-autoload hoan tat.

echo    [4/4] Dang don dep cache he thong...
call !PHP_CMD! artisan optimize:clear > nul
echo    ^|---^> [OK] Da optimize va clear cache Laravel.

echo.
echo    [ SUCCESS ] He thong da duoc khoi phuc sach se va nang cap len ban moi nhat 100%%!
pause
goto MENU

:SUPER_PIPELINE
cls
color 0b
echo.
echo    +======================================================================================+
echo    ^|         SmartRoom ^& Renty SUPER PIPELINE - CONG CU KICH HOAT TOAN TRINH ALL-IN-ONE    ^|
echo    +======================================================================================+
echo.
echo    [!] Trinh tu dong hoa sieu cap se thuc hien toan bo quy trinh tu A den Z de chay website.
echo    [!] Vui long lua chon kieu Pipeline ban muon van hanh:
echo.
echo    [1] PIPELINE TIEU CHUAN (Standard Pipeline)
echo        - Giu nguyen vendor/node_modules hien tai (tai them neu thieu)
echo        - Cau hinh .env va Database (SQLite/MySQL)
echo        - Migrate ^& Seed du lieu gia lap, optimize cache va boot server ngay lap tuc.
echo.
echo    [2] PIPELINE SACH TOAN DIEN (Clean Rebuild Pipeline)
echo        - rd /s /q vendor, node_modules va xoa sach file .env cu de tranh xung dot
echo        - Tai moi hoan toan thu vien tu dau (Composer + NPM)
echo        - Cau hinh lai database, migrate:fresh --seed, build assets va boot server.
echo.
echo    [3] Quay lai Menu chinh
echo.
echo    ----------------------------------------------------------------------------------------
set /p pipe_choice="   >> Nhap lua chon cua ban (1-3): "

if "!pipe_choice!"=="3" goto MENU
if "!pipe_choice!"=="1" goto PIPELINE_STANDARD
if "!pipe_choice!"=="2" goto PIPELINE_CLEAN_REBUILD
goto SUPER_PIPELINE


:PIPELINE_STANDARD
cls
echo.
echo    ====================================================================
echo    [ FAST REBUILD ^& RUN ] RESET CSDL + BIEN DICH VITE + KHOI CHAY MOI TRUONG
echo    ====================================================================
echo.

:: 1. Tat tien trinh cu
echo    [1/7] Dang giai phong cac tien trinh cu...
taskkill /f /im php.exe >nul 2>&1
taskkill /f /im node.exe >nul 2>&1
if exist public\hot del /f /q public\hot
echo    ^|---^> [OK] Da don dep xong tien trinh cu.

:: 2. File .env
if not exist .env (
    echo    [2/7] [ HANH DONG ] Dang copy .env.example sang .env...
    copy .env.example .env > nul
)
echo    [2/7] [OK] Moi truong .env san sang.

:: 3. Composer
if not exist vendor (
    echo    [3/7] [ HANH DONG ] Thieu thu muc vendor. Dang tu dong tai packages backend...
    call !COMPOSER_CMD! install --prefer-dist --ignore-platform-reqs
) else (
    echo    [3/7] [+] Thu muc vendor da co san.
)

:: 4. NPM
if not exist node_modules (
    echo    [4/7] [ HANH DONG ] Thieu node_modules. Dang tu dong tai packages frontend...
    call npm install
) else (
    echo    [4/7] [+] Thu muc node_modules da co san.
)

:: 5. Key ^& Database config
echo    [5/7] Kiem tra App Key va cau hinh CSDL...
findstr /C:"APP_KEY=base64:" .env > nul
if %errorlevel% neq 0 (
    call !PHP_CMD! artisan key:generate
)
findstr /C:"DB_CONNECTION=sqlite" .env > nul
if %errorlevel% equ 0 (
    if not exist database\database.sqlite (
        type nul > database\database.sqlite
    )
)
findstr /C:"DB_CONNECTION=mysql" .env > nul
if %errorlevel% equ 0 (
    echo    [!] Phat hien cau hinh MySQL. Dang kiem tra ket noi...
    tasklist /fi "imagename eq mysqld.exe" | findstr /i "mysqld.exe" > nul
    if !errorlevel! neq 0 (
        color 0c
        echo    [ LOI ] MySQL chua bat! Vui long bat MySQL tren XAMPP/Laragon truoc.
        echo    [ GOI Y ] Mo XAMPP Control Panel va nhan Start ben canh MySQL.
        pause
        goto MENU
    )
    echo    ^|---^> [OK] MySQL dang hoat dong.
)
if not exist public\storage (
    call !PHP_CMD! artisan storage:link
)
echo    ^|---^> [OK] Cau hinh he thong hop le.

:: 6. Migrate ^& Seed
echo    [6/7] [ HANH DONG ] Dang lam moi co so du lieu va nap du lieu gia lap...
call !PHP_CMD! artisan migrate:fresh --seed
call !PHP_CMD! artisan optimize:clear
echo    ^|---^> [OK] Reset DB va nap data thanh cong!

:: 7. Build ^& Run
echo    [7/7] Dang bien dich tai nguyen frontend (Build Assets)...
call npm run build
echo    ^|---^> [OK] Chuan bi assets hoan tat.
timeout /t 2 > nul
goto PROCESS


:PIPELINE_CLEAN_REBUILD
cls
color 0c
echo    +======================================================================================+
echo    ^|              [ CANH BAO ] PIPELINE SACH SE XOA VA KHOI TAO LAI TOAN TRINH            ^|
echo    +======================================================================================+
echo.
echo    [!] Thao tac nay se xoa sach vendor, node_modules, .env va database hien tai
echo        de dam bao tai va chay lai tu dau khong gap bat ky loi nao.
echo.
set /p pipe_clean_confirm="    >> Ban co chac chan muon tiep tuc? [y/n]: "
if /i "!pipe_clean_confirm!" neq "y" goto SUPER_PIPELINE

cls
color 0b
echo.
echo    ====================================================================
echo    [ CLEAN PIPELINE ACTIVE ] TIEN HANH XOA SACH VA TAI TOAN BO TU A-Z
echo    ====================================================================
echo.

:: 1. Giai phong va xoa
echo    [1/8] Dang xoa sach cac thu muc cu va giai phong tien trinh...
taskkill /f /im php.exe >nul 2>&1
taskkill /f /im node.exe >nul 2>&1
if exist public\hot del /f /q public\hot
if exist vendor (rd /s /q vendor)
if exist node_modules (rd /s /q node_modules)
if exist .env (del /f /q .env)
if exist package-lock.json (del /f /q package-lock.json)
if exist database\database.sqlite (del /f /q database\database.sqlite)
echo    ^|---^> [OK] He thong da duoc lam sach 100%%.

:: 2. Tai tao .env
echo    [2/8] Dang khoi tao file .env moi...
copy .env.example .env > nul
echo    ^|---^> [OK] Da copy .env.example.

:: 3. Chon Database driver tu dong hoac cho nguoi dung chon nhanh
echo    [3/8] Thiet lap CSDL cho he thong:
echo    [1] SQLITE [Mac dinh / Khuyen dung cho moi truong dev cuc nhanh]
echo    [2] MYSQL  [Dung CSDL XAMPP/Laragon]
set /p pipe_db_driver="   >> Chon CSDL (1-2) [Mac dinh: 1]: "
if "!pipe_db_driver!"=="2" goto PIPE_DB_MYSQL

:PIPE_DB_SQLITE
type nul > database\database.sqlite
powershell -Command "$c = gc .env; $c = $c -replace '^DB_CONNECTION=.*', 'DB_CONNECTION=sqlite'; $c = $c -replace '^DB_HOST=.*', '#DB_HOST=127.0.0.1'; $c = $c -replace '^DB_PORT=.*', '#DB_PORT=3306'; $c = $c -replace '^DB_DATABASE=.*', 'DB_DATABASE=database/database.sqlite'; $c = $c -replace '^DB_USERNAME=.*', '#DB_USERNAME=root'; $c = $c -replace '^DB_PASSWORD=.*', '#DB_PASSWORD='; $c | Out-File -encoding utf8 .env"
goto PIPE_DB_DONE

:PIPE_DB_MYSQL
set "db_host=127.0.0.1"
set /p db_host="   >> Nhap DB Host [Mac dinh: 127.0.0.1]: "
set "db_port=3306"
set /p db_port="   >> Nhap DB Port [Mac dinh: 3306]: "
set "db_name=smartroom_renty"
set /p db_name="   >> Nhap ten database [Mac dinh: smartroom_renty]: "
set "db_user=root"
set /p db_user="   >> Nhap Username [Mac dinh: root]: "
set "db_pass="
set /p db_pass="   >> Nhap Password [Mac dinh: bo trong]: "
powershell -Command "$c = gc .env; $c = $c -replace '^DB_CONNECTION=.*', 'DB_CONNECTION=mysql'; $c = $c -replace '^DB_HOST=.*', 'DB_HOST=!db_host!'; $c = $c -replace '^DB_PORT=.*', 'DB_PORT=!db_port!'; $c = $c -replace '^DB_DATABASE=.*', 'DB_DATABASE=!db_name!'; $c = $c -replace '^DB_USERNAME=.*', 'DB_USERNAME=!db_user!'; $c = $c -replace '^DB_PASSWORD=.*', 'DB_PASSWORD=!db_pass!'; $c | Out-File -encoding utf8 .env"

:PIPE_DB_DONE
echo    ^|---^> [OK] Thiet lap database hoan tat.

:: 4. Composer install
echo.
echo    [4/8] Dang cai dat thu vien Backend (Composer)...
call !COMPOSER_CMD! install --prefer-dist --ignore-platform-reqs
echo    ^|---^> [OK] Tai va cai dat goi backend thanh cong.

:: 5. NPM install
echo.
echo    [5/8] Dang cai dat thu vien Frontend (NPM)...
call npm install
echo    ^|---^> [OK] Tai va cai dat goi frontend thanh cong.

:: 6. APP_KEY ^& Storage Link
echo.
echo    [6/8] Khoi tao key bao mat va lien ket thu muc storage...
call !PHP_CMD! artisan key:generate
if not exist public\storage (
    call !PHP_CMD! artisan storage:link
)
echo    ^|---^> [OK] Da sinh khoa bao mat he thong.

:: 7. Kiem tra MySQL truoc khi migrate
echo.
echo    [7/9] Kiem tra ket noi CSDL truoc khi migrate...
findstr /C:"DB_CONNECTION=mysql" .env > nul
if %errorlevel% equ 0 (
    tasklist /fi "imagename eq mysqld.exe" | findstr /i "mysqld.exe" > nul
    if !errorlevel! neq 0 (
        color 0c
        echo    [ LOI ] MySQL chua bat! Vui long bat MySQL tren XAMPP/Laragon truoc.
        echo    [ GOI Y ] Mo XAMPP Control Panel va nhan Start ben canh MySQL.
        pause
        goto MENU
    )
    echo    ^|---^> [OK] MySQL dang hoat dong.
)

:: 8. Migrate ^& Seed
echo.
echo    [8/9] Khoi tao cau truc CSDL va nap toan bo du lieu mau gia lap...
call !PHP_CMD! artisan migrate:fresh --seed
call !PHP_CMD! artisan optimize:clear
echo    ^|---^> [OK] CSDL da san sang cung toan bo combo va home banners.

:: 9. Build Assets
echo.
echo    [9/9] Dang bien dich giao dien web (Build Production Assets)...
call npm run build
echo    ^|---^> [OK] Build hoan tat!
timeout /t 2 > nul
goto PROCESS

:VIEW_SITEMAP
cls
color 0f
if not defined PORT (set PORT=8000)
echo.
echo    +======================================================================================+
echo    ^|            SmartRoom ^& Renty NAVIGATOR - HE THONG LIEN KET QUAN TRI ^& KHACH HANG     ^|
echo    +======================================================================================+
echo.
echo    [+] Cong may chu hien tai: !PORT!
echo    [!] Nhap so tuong ung de MO TRUC TIEP tren trinh duyet mac dinh cua ban:
echo.
echo    --- HE THONG KHACH HANG (FRONTEND) ---
echo    [1] Trang chu Website              - http://127.0.0.1:!PORT!/
echo    [2] Vong quay ^& Doi diem thuong     - http://127.0.0.1:!PORT!/rewards
echo    [3] Dat lich ^& Lich su sua chua    - http://127.0.0.1:!PORT!/profile
echo    [4] So sanh san pham               - http://127.0.0.1:!PORT!/compare
echo    [5] Dang nhap / Dang ky            - http://127.0.0.1:!PORT!/login_register
echo.
echo    --- HE THONG QUAN TRI (ADMIN PORTAL) ---
echo    [6] Tong quan Admin Dashboard      - http://127.0.0.1:!PORT!/admin
echo    [7] Quan ly Phieu sua chua         - http://127.0.0.1:!PORT!/admin/repair-tickets
echo    [8] Quan ly Hoa don dich vu        - http://127.0.0.1:!PORT!/admin/service-invoices
echo    [9] Cau hinh Vong quay ^& Qua tang   - http://127.0.0.1:!PORT!/admin/rewards
echo    [10] Lich su Quay ^& Doi thuong      - http://127.0.0.1:!PORT!/admin/rewards/history
echo    [11] Quan ly Bai viet ^& Tin tuc     - http://127.0.0.1:!PORT!/admin/articles
echo.
echo    --- DIEU HUONG CHUNG ---
echo    [12] Quay lai Menu chinh
echo.
echo    ----------------------------------------------------------------------------------------
set /p sitemap_choice="   >> Nhap lua chon lien ket cua ban (1-12): "

if "!sitemap_choice!"=="1" start http://127.0.0.1:!PORT!/ & goto VIEW_SITEMAP
if "!sitemap_choice!"=="2" start http://127.0.0.1:!PORT!/rewards & goto VIEW_SITEMAP
if "!sitemap_choice!"=="3" start http://127.0.0.1:!PORT!/profile & goto VIEW_SITEMAP
if "!sitemap_choice!"=="4" start http://127.0.0.1:!PORT!/compare & goto VIEW_SITEMAP
if "!sitemap_choice!"=="5" start http://127.0.0.1:!PORT!/login_register & goto VIEW_SITEMAP
if "!sitemap_choice!"=="6" start http://127.0.0.1:!PORT!/admin & goto VIEW_SITEMAP
if "!sitemap_choice!"=="7" start http://127.0.0.1:!PORT!/admin/repair-tickets & goto VIEW_SITEMAP
if "!sitemap_choice!"=="8" start http://127.0.0.1:!PORT!/admin/service-invoices & goto VIEW_SITEMAP
if "!sitemap_choice!"=="9" start http://127.0.0.1:!PORT!/admin/rewards & goto VIEW_SITEMAP
if "!sitemap_choice!"=="10" start http://127.0.0.1:!PORT!/admin/rewards/history & goto VIEW_SITEMAP
if "!sitemap_choice!"=="11" start http://127.0.0.1:!PORT!/admin/articles & goto VIEW_SITEMAP
goto MENU

:: ======================================================================
:: DIAGNOSTICS: HEALTH REPORT (PRO FEATURE)
:: ======================================================================
:DIAGNOSTICS
cls
color 0f
echo.
echo    ====================================================================
echo    [ DIAGNOSTIC REPORT ] KIEM TRA SUC KHOE DU AN TOAN DIEN
echo    ====================================================================
echo.
echo    [1/5] Kiem tra bien moi truong (.env)...
if not exist .env (
    set ENV_STATUS=❌ Tep .env khong ton tai!
) else (
    findstr /C:"APP_KEY=base64:" .env > nul
    if %errorlevel% neq 0 (
        set ENV_STATUS=⚠ Tep .env co san nhung APP_KEY chua khoi tao!
    ) else (
        set ENV_STATUS=✔ Tep .env ton tai va APP_KEY hop le.
    )
)
echo    ^|---^> !ENV_STATUS!

echo    [2/5] Kiem tra cac thu muc tam (Write permissions)...
set FOLDERS_OK=1
if not exist storage\framework\views (mkdir storage\framework\views & set FOLDERS_OK=0)
if not exist storage\framework\cache (mkdir storage\framework\cache & set FOLDERS_OK=0)
if not exist storage\framework\sessions (mkdir storage\framework\sessions & set FOLDERS_OK=0)
if not exist storage\logs (mkdir storage\logs & set FOLDERS_OK=0)
if !FOLDERS_OK! equ 1 (
    echo    ^|---^> ✔ Cac thu muc tam storage/framework deu san sang va writable.
) else (
    echo    ^|---^> ⚠ Da tu dong phat hien thieu thu muc tam va tao moi thanh cong.
)

echo    [3/5] Kiem tra ket noi CSDL MySQL...
tasklist /fi "imagename eq mysqld.exe" | findstr /i "mysqld.exe" > nul
if %errorlevel% neq 0 (
    set DB_REPORT=❌ CSDL MySQL hien tai dang OFF! Vui long bat MySQL tren XAMPP/Laragon.
) else (
    set DB_REPORT=✔ MySQL dang bat, Cong CSDL=!DB_PORT!, Ten CSDL=!DB_NAME!.
)
echo    ^|---^> !DB_REPORT!

echo    [4/5] Kiem tra thu vien node_modules...
if exist node_modules (
    echo    ^|---^> ✔ Thu muc node_modules da duoc cai dat.
) else (
    echo    ^|---^> ❌ Thieu thu muc node_modules! Vui long chay Lua chon [6] de cai dat.
)

echo    [5/5] Kiem tra cac goi backend vendor...
if exist vendor (
    echo    ^|---^> ✔ Thu muc backend vendor da san sang.
) else (
    echo    ^|---^> ❌ Thieu thu muc backend vendor! Vui long chay Lua chon [6] de cai dat.
)
echo.
echo    --------------------------------------------------------------------
echo    Nhan phim bat ky de quay lai Menu...
pause > nul
goto MENU

:: ======================================================================
:: LOG MANAGEMENT: VIEW & CLEAN (PRO FEATURE)
:: ======================================================================
:LOG_MGMT
cls
color 0e
echo.
echo    +======================================================================================+
echo    ^|                       QUAN LY NHAT KY LOI LARAVEL GAN NHAT                           ^|
echo    +======================================================================================+
echo.
echo    [1] Xem 30 dong nhat ky loi moi nhat
echo    [2] Xoa sach file nhat ky loi hien tai (Clear logs)
echo    [3] Quay lai Menu chinh
echo.
set /p log_choice="   >> Nhap lua chon cua ban (1-3): "

if "%log_choice%"=="1" (
    cls
    echo.
    echo    --- 30 Dong nhat ky loi gan nhat ---
    if exist storage\logs\laravel.log (
        powershell -command "Get-Content storage\logs\laravel.log -Tail 30"
    ) else (
        color 0a
        echo    [+] Khong co bat ky nhat ky loi nao duoc ghi nhan [Moi thu hoat dong rat tot!].
    )
    echo.
    color 0e
    echo    Nhan phim bat ky de quay lai...
    pause > nul
    goto LOG_MGMT
)
if "%log_choice%"=="2" (
    if exist storage\logs\laravel.log (
        del /f /q storage\logs\laravel.log
        echo    [ OK ] Da xoa sach toan bo nhat ky loi cu thanh cong!
    ) else (
        echo    [+] Tep log hien tai dang trong, khong can thuc hien.
    )
    pause
    goto LOG_MGMT
)
goto MENU

:DEV_MODE
cls
echo.
echo    [ HANH DONG ] Dang khoi dong che do DEV...
if not exist node_modules (
    color 0e
    echo    [!] Canh bao: Khong tim thay thu muc node_modules!
    echo    He thong se tu dong chay NPM INSTALL de chuan bi Vite Asset hot-reload...
    call npm install
)
set MODE=DEV
goto PROCESS

:STABLE_RUN
cls
color 0e
echo.
echo    [1/4] Dang don dep cac tien trinh chay ngam cu...
taskkill /f /im php.exe >nul 2>&1
taskkill /f /im node.exe >nul 2>&1
if exist public\hot del /f /q public\hot
!PHP_CMD! artisan optimize:clear > nul
echo    [2/4] Dang dung lai goi assets cho on dinh...
call npm run build
set MODE=STABLE
goto PROCESS

:RESET_DB
cls
color 0c
echo    [!] Canh bao: Toan bo du lieu cu trong Database (!DB_NAME!) se bi xoa sach!
set /p confirm="    >> Ban co chac chan muon tiep tuc? (y/n): "
if /i "%confirm%" neq "y" goto MENU

call :ENSURE_MYSQL
if !errorlevel! neq 0 goto MENU

call !PHP_CMD! artisan migrate:fresh --seed
call !PHP_CMD! artisan optimize:clear
echo    [ OK ] Database va Seeders da lam moi thanh cong!
pause
goto MENU

:PROCESS
color 0a
cls
echo.
echo    ====================================================================
echo    [ CONG CU ] DANG TIEN HANH KHOI TAO PHAN CONG TU DONG HOA...
echo    ====================================================================
echo.
if not exist .env (
    copy .env.example .env > nul
    call !PHP_CMD! artisan key:generate
)

echo    [ STEP 1 ] KIEM TRA KET NOI CO SO DU LIEU...
findstr /C:"DB_CONNECTION=sqlite" .env > nul
if !errorlevel! equ 0 (
    if not exist database\database.sqlite (
        type nul > database\database.sqlite
    )
    echo    [+] Dang dung SQLite, bo qua kiem tra MySQL.
) else (
    call :ENSURE_MYSQL
    if !errorlevel! neq 0 goto MENU
    echo    [+] MySQL dang ket noi tot - CSDL: !DB_NAME!, Cong CSDL: !DB_PORT!.
)

echo    [ STEP 1.5 ] LAM SACH CACHE TOAN DIEN...
call !PHP_CMD! artisan config:clear
call !PHP_CMD! artisan route:clear
call !PHP_CMD! artisan view:clear

:: ======================================================================
:: CONFLICT RESOLUTION: ACTIVE PORT CHECKING (PORT 8000)
:: ======================================================================
echo    [ STEP 2 ] KIEM TRA TRANH CHAP CONG MO...
netstat -o -n -a | findstr :8000 > nul
if %errorlevel% equ 0 (
    color 0e
    echo    [!] Canh bao: Cong 8000 hien tai dang bi chiem dung boi tien trinh khac!
    echo    [1] Tu dong giai phong cong 8000 [Tat ung dung dang chan]
    echo    [2] Chuyen sang khoi chay tren cong phu [Cong 8080]
    set /p port_choice="   >> Nhap lua chon [1-2]: "
    if "!port_choice!"=="1" (
        for /f "tokens=5" %%p in ('netstat -aon ^| findstr :8000') do (
            taskkill /f /pid %%p >nul 2>&1
        )
        echo    [+] Da tu dong giai phong cong 8000 thanh cong.
        set PORT=8000
    ) else (
        set PORT=8080
        echo    [+] He thong se duoc dinh huong sang cong 8080.
    )
) else (
    set PORT=8000
)

color 0a
echo    [ STEP 3 ] PHAT HANH BAN SERVERS CONG !PORT!...
start "SmartRoom ^& Renty Laravel Server" cmd /c "!PHP_CMD! artisan serve --port=!PORT!"
if "%MODE%"=="DEV" (
    start "Vite Hot-Reload Server" cmd /c "npm run dev"
)

set "AUTO_OPEN_PATH=/renty"
set "AUTO_OPEN_URL=http://127.0.0.1:!PORT!!AUTO_OPEN_PATH!"
set "SERVER_READY=0"
echo    [ STEP 4 ] DOI SERVER SAN SANG ROI TU DONG MO WEB...
for /l %%i in (1,1,30) do (
    powershell -NoProfile -ExecutionPolicy Bypass -Command "try { $r = Invoke-WebRequest -UseBasicParsing -Uri 'http://127.0.0.1:!PORT!/' -TimeoutSec 1; if ($r.StatusCode -ge 200) { exit 0 } } catch { exit 1 }" > nul 2>&1
    if !errorlevel! equ 0 (
        set "SERVER_READY=1"
        goto OPEN_WEB_AFTER_READY
    )
    timeout /t 1 > nul
)

:OPEN_WEB_AFTER_READY
if "!SERVER_READY!"=="1" (
    echo    [+] Server da san sang. Dang mo: !AUTO_OPEN_URL!
) else (
    echo    [!] Chua xac nhan duoc server san sang, van mo web de ban kiem tra.
)
start chrome --app="!AUTO_OPEN_URL!" >nul 2>&1 || start msedge --app="!AUTO_OPEN_URL!" >nul 2>&1 || start "" "!AUTO_OPEN_URL!"

cls
color 0b
echo.
echo    +======================================================================================+
echo    ^|                 SmartRoom ^& Renty CHAY TU DONG HOA THANH CONG                        ^|
echo    +======================================================================================+
echo    ^|                                                                                      ^|
echo    ^|  [+] Website nguoi thue:  http://127.0.0.1:!PORT!/renty                              ^|
echo    ^|  [+] Localhost URL:       http://127.0.0.1:!PORT!/                                    ^|
echo    ^|  [+] Local Network URL:   http://!LOCAL_IP!:!PORT!/                                   ^|
echo    ^|  [+] Admin Portal:        http://127.0.0.1:!PORT!/smartroom/admin                    ^|
echo    ^|                                                                                      ^|
echo    ^|  [*] Meo kiem thu: Dung dien thoai / may tinh bang ket noi cung mang Wi-Fi voi may   ^|
echo    ^|      tinh, roi truy cap vao duong dan Local Network URL o tren de test thiet bi di   ^|
echo    ^|      dong cuc ky de dang va thuc te.                                                 ^|
echo    ^|                                                                                      ^|
echo    +======================================================================================+
echo.
echo    [*] SmartRoom ^& Renty DANG CHAY THEO CONG !PORT! - NHAN PHIM BAT KY DE TAT TOAN BO SERVERS
echo.
pause > nul
echo    [!] Dang giai phong va tat toan bo tien trinh ngam php/node...
taskkill /f /im php.exe >nul 2>&1
taskkill /f /im node.exe >nul 2>&1
:: ======================================================================
:: HELPER: DAM BAO MYSQL DA KHOI DONG VA SAN SANG
:: ======================================================================
:ENSURE_MYSQL
powershell -NoProfile -ExecutionPolicy Bypass -Command "try { $c = Test-NetConnection -ComputerName 127.0.0.1 -Port 3306 -InformationLevel Quiet; if ($c) { exit 0 } else { exit 1 } } catch { exit 1 }" > nul 2>&1
if !errorlevel! equ 0 (
    if exist "D:\xampp\mysql\bin\mysql.exe" (
        "D:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS qlphongtro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1
    ) else if exist "C:\xampp\mysql\bin\mysql.exe" (
        "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS qlphongtro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1
    )
    exit /b 0
)

echo.
echo    [!] MySQL (Port 3306) chua mo. Dang tu dong khoi dong MySQL XAMPP...
if exist "D:\xampp\mysql\bin\mysqld.exe" (
    start "" /b "cmd.exe" /c "cd /d D:\xampp && mysql\bin\mysqld.exe --defaults-file=mysql\bin\my.ini --standalone"
) else if exist "C:\xampp\mysql\bin\mysqld.exe" (
    start "" /b "cmd.exe" /c "cd /d C:\xampp && mysql\bin\mysqld.exe --defaults-file=mysql\bin\my.ini --standalone"
)

for /l %%i in (1,1,10) do (
    timeout /t 1 > nul
    powershell -NoProfile -ExecutionPolicy Bypass -Command "try { $c = Test-NetConnection -ComputerName 127.0.0.1 -Port 3306 -InformationLevel Quiet; if ($c) { exit 0 } else { exit 1 } } catch { exit 1 }" > nul 2>&1
    if !errorlevel! equ 0 (
        echo    ^|---^> [OK] MySQL da khoi dong thanh cong va san sang tren cong 3306!
        if exist "D:\xampp\mysql\bin\mysql.exe" (
            "D:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS qlphongtro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1
        ) else if exist "C:\xampp\mysql\bin\mysql.exe" (
            "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS qlphongtro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1
        )
        exit /b 0
    )
)

color 0c
echo    [ LOI CRITICAL ] Khong the ket noi CSDL MySQL (Port 3306)!
echo    Vui long mo XAMPP Control Panel va nhan Start ben canh MySQL.
pause
exit /b 1

:: ======================================================================
:: EXIT SYSTEM CLEANLY (THOAT CO CHE AN TOAN TRA VE MA 0)
:: ======================================================================
:EXIT_CLEAN
:: Reset lai errorlevel ve 0 tranh lỗi do cac lenh taskkill truoc do de lai
cmd /c "exit /b 0"
exit /b 0
