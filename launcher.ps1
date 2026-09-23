# ==============================================================================
# SmartRoom & Renty - GUI App Launcher
# ==============================================================================

Add-Type -AssemblyName PresentationFramework
Add-Type -AssemblyName PresentationCore
Add-Type -AssemblyName WindowsBase
Add-Type -AssemblyName System.Drawing

[xml]$xaml = @"
<Window xmlns="http://schemas.microsoft.com/winfx/2006/xaml/presentation"
        xmlns:x="http://schemas.microsoft.com/winfx/2006/xaml"
        Title="SmartRoom &amp; Renty - Trình Khởi Chạy Ứng Dụng"
        Height="580" Width="660"
        WindowStartupLocation="CenterScreen"
        ResizeMode="NoResize"
        Background="#0f172a"
        Foreground="#f8fafc"
        FontFamily="Segoe UI">

    <Window.Resources>
        <!-- TabItem Style -->
        <Style TargetType="TabItem">
            <Setter Property="Background" Value="#1e293b"/>
            <Setter Property="Foreground" Value="#94a3b8"/>
            <Setter Property="FontSize" Value="13"/>
            <Setter Property="FontWeight" Value="SemiBold"/>
            <Setter Property="Padding" Value="16,8"/>
            <Setter Property="Margin" Value="0,0,4,0"/>
            <Setter Property="BorderThickness" Value="0"/>
            <Setter Property="Template">
                <Setter.Value>
                    <ControlTemplate TargetType="TabItem">
                        <Border Name="Border" Background="{TemplateBinding Background}" CornerRadius="8,8,0,0" Padding="{TemplateBinding Padding}">
                            <ContentPresenter ContentSource="Header" HorizontalAlignment="Center" VerticalAlignment="Center"/>
                        </Border>
                        <ControlTemplate.Triggers>
                            <Trigger Property="IsSelected" Value="True">
                                <Setter TargetName="Border" Property="Background" Value="#1e293b"/>
                                <Setter Property="Foreground" Value="#38bdf8"/>
                            </Trigger>
                            <Trigger Property="IsMouseOver" Value="True">
                                <Setter Property="Foreground" Value="#ffffff"/>
                            </Trigger>
                        </ControlTemplate.Triggers>
                    </ControlTemplate>
                </Setter.Value>
            </Setter>
        </Style>
    </Window.Resources>

    <Grid Margin="18">
        <Grid.RowDefinitions>
            <RowDefinition Height="Auto"/>
            <RowDefinition Height="*"/>
            <RowDefinition Height="Auto"/>
            <RowDefinition Height="Auto"/>
        </Grid.RowDefinitions>

        <!-- Brand Header with SmartRoom x Renty Logo -->
        <Border Grid.Row="0" Margin="0,0,0,14" Padding="14,10" Background="#1e293b" CornerRadius="12" BorderThickness="1" BorderBrush="#334155">
            <Grid>
                <Grid.ColumnDefinitions>
                    <ColumnDefinition Width="*"/>
                    <ColumnDefinition Width="Auto"/>
                    <ColumnDefinition Width="*"/>
                </Grid.ColumnDefinitions>

                <!-- Left: SmartRoom Logo Badge -->
                <StackPanel Grid.Column="0" Orientation="Horizontal" VerticalAlignment="Center">
                    <Border Width="40" Height="40" CornerRadius="10" Margin="0,0,10,0">
                        <Border.Background>
                            <LinearGradientBrush StartPoint="0,0" EndPoint="1,1">
                                <GradientStop Color="#4f46e5" Offset="0"/>
                                <GradientStop Color="#7c3aed" Offset="1"/>
                            </LinearGradientBrush>
                        </Border.Background>
                        <TextBlock Text="🏢" FontSize="20" HorizontalAlignment="Center" VerticalAlignment="Center"/>
                    </Border>
                    <StackPanel VerticalAlignment="Center">
                        <TextBlock Text="SmartRoom" FontSize="16" FontWeight="ExtraBold" Foreground="#a5b4fc"/>
                        <TextBlock Text="Quản Trị Nhà Trọ" FontSize="10" FontWeight="SemiBold" Foreground="#94a3b8"/>
                    </StackPanel>
                </StackPanel>

                <!-- Middle: Connect Badge -->
                <Border Grid.Column="1" Background="#0f172a" CornerRadius="12" Padding="8,4" VerticalAlignment="Center" Margin="10,0">
                    <TextBlock Text="⚡" FontSize="12" Foreground="#38bdf8"/>
                </Border>

                <!-- Right: Renty Logo Badge -->
                <StackPanel Grid.Column="2" Orientation="Horizontal" VerticalAlignment="Center" HorizontalAlignment="Right">
                    <Border Width="40" Height="40" CornerRadius="10" Margin="0,0,10,0">
                        <Border.Background>
                            <LinearGradientBrush StartPoint="0,0" EndPoint="1,1">
                                <GradientStop Color="#059669" Offset="0"/>
                                <GradientStop Color="#0d9488" Offset="1"/>
                            </LinearGradientBrush>
                        </Border.Background>
                        <TextBlock Text="🔍" FontSize="20" HorizontalAlignment="Center" VerticalAlignment="Center"/>
                    </Border>
                    <StackPanel VerticalAlignment="Center">
                        <TextBlock Text="Renty" FontSize="16" FontWeight="ExtraBold" Foreground="#6ee7b7"/>
                        <TextBlock Text="Thuê Trọ Thông Minh" FontSize="10" FontWeight="SemiBold" Foreground="#94a3b8"/>
                    </StackPanel>
                </StackPanel>
            </Grid>
        </Border>

        <!-- Tab Selection Control -->
        <TabControl Name="mainTabControl" Grid.Row="1" Background="#1e293b" BorderThickness="0" Margin="0,0,0,12">
            <!-- TAB 1: DOCKER -->
            <TabItem Header="🐳 Tab Docker">
                <Border Background="#1e293b" CornerRadius="0,8,8,8" Padding="16">
                    <StackPanel>
                        <TextBlock Text="Môi trường Docker Compose (Độc lập &amp; Đầy đủ)" FontSize="14" FontWeight="Bold" Foreground="#60a5fa" Margin="0,0,0,8"/>
                        <TextBlock Text="Khởi chạy toàn bộ hệ thống gồm Website Laravel, máy chủ Reverb WebSocket và cơ sở dữ liệu MySQL 8.4 trong các container riêng biệt." TextWrapping="Wrap" FontSize="12" Foreground="#cbd5e1" Margin="0,0,0,12"/>

                        <!-- Info box -->
                        <Border Background="#0f172a" CornerRadius="6" Padding="10" Margin="0,0,0,14">
                            <StackPanel>
                                <TextBlock Text="• Cổng Web Ứng dụng:  http://localhost:8088/renty" FontSize="11" Foreground="#38bdf8"/>
                                <TextBlock Text="• Cơ sở dữ liệu Docker:  localhost:3309 (smartroom / smartroom)" FontSize="11" Foreground="#94a3b8"/>
                                <TextBlock Text="• Máy chủ Reverb WS:   ws://127.0.0.1:8085" FontSize="11" Foreground="#94a3b8"/>
                            </StackPanel>
                        </Border>

                        <!-- Main Run Button -->
                        <Button Name="btnRunDocker" Content="🚀 KHỞI CHẠY BẰNG DOCKER" Height="42" FontSize="13" FontWeight="Bold"
                                Background="#2563eb" Foreground="White" BorderThickness="0" Cursor="Hand" Margin="0,0,0,10">
                            <Button.Resources>
                                <Style TargetType="Border">
                                    <Setter Property="CornerRadius" Value="8"/>
                                </Style>
                            </Button.Resources>
                        </Button>

                        <!-- Sub Buttons -->
                        <Grid>
                            <Grid.ColumnDefinitions>
                                <ColumnDefinition Width="*"/>
                                <ColumnDefinition Width="*"/>
                                <ColumnDefinition Width="*"/>
                            </Grid.ColumnDefinitions>
                            <Button Name="btnDockerLogs" Grid.Column="0" Content="📋 Xem Logs" Height="28" FontSize="11"
                                    Background="#334155" Foreground="#cbd5e1" BorderThickness="0" Margin="0,0,4,0" Cursor="Hand"/>
                            <Button Name="btnDockerSeed" Grid.Column="1" Content="🌱 Seed CSDL" Height="28" FontSize="11"
                                    Background="#334155" Foreground="#cbd5e1" BorderThickness="0" Margin="2,0,2,0" Cursor="Hand"/>
                            <Button Name="btnDockerStop" Grid.Column="2" Content="⏹️ Dừng Docker" Height="28" FontSize="11"
                                    Background="#7f1d1d" Foreground="#fca5a5" BorderThickness="0" Margin="4,0,0,0" Cursor="Hand"/>
                        </Grid>
                    </StackPanel>
                </Border>
            </TabItem>

            <!-- TAB 2: XAMPP -->
            <TabItem Header="🟠 Tab XAMPP">
                <Border Background="#1e293b" CornerRadius="0,8,8,8" Padding="16">
                    <StackPanel>
                        <TextBlock Text="Môi trường XAMPP Cục Bộ (Nhanh &amp; Tiện lợi)" FontSize="14" FontWeight="Bold" Foreground="#fb923c" Margin="0,0,0,8"/>
                        <TextBlock Text="Sử dụng PHP và MySQL đã được cài đặt sẵn trong thư mục XAMPP (ưu tiên tìm tại D:\xampp, C:\xampp). Hỗ trợ tự bật MySQL nếu chưa chạy." TextWrapping="Wrap" FontSize="12" Foreground="#cbd5e1" Margin="0,0,0,12"/>

                        <!-- Info box -->
                        <Border Background="#0f172a" CornerRadius="6" Padding="10" Margin="0,0,0,14">
                            <StackPanel>
                                <TextBlock Text="• Cổng Web Ứng dụng:  http://127.0.0.1:8000/renty" FontSize="11" Foreground="#fb923c"/>
                                <TextBlock Text="• Cơ sở dữ liệu XAMPP: localhost:3306 (root / không mật khẩu)" FontSize="11" Foreground="#94a3b8"/>
                                <TextBlock Text="• PHP Engine:         Tự động nhận diện D:\xampp\php\php.exe" FontSize="11" Foreground="#94a3b8"/>
                            </StackPanel>
                        </Border>

                        <!-- Main Run Button -->
                        <Button Name="btnRunXampp" Content="⚡ KHỞI CHẠY BẰNG XAMPP" Height="42" FontSize="13" FontWeight="Bold"
                                Background="#ea580c" Foreground="White" BorderThickness="0" Cursor="Hand" Margin="0,0,0,10">
                            <Button.Resources>
                                <Style TargetType="Border">
                                    <Setter Property="CornerRadius" Value="8"/>
                                </Style>
                            </Button.Resources>
                        </Button>

                        <TextBlock Text="* Gợi ý: Hãy bật MySQL trong XAMPP Control Panel trước nếu cần." FontSize="11" Foreground="#64748b" HorizontalAlignment="Center"/>
                    </StackPanel>
                </Border>
            </TabItem>

            <!-- TAB 3: WAMPP -->
            <TabItem Header="🟢 Tab WAMPP">
                <Border Background="#1e293b" CornerRadius="0,8,8,8" Padding="16">
                    <StackPanel>
                        <TextBlock Text="Môi trường WampServer Cục Bộ" FontSize="14" FontWeight="Bold" Foreground="#4ade80" Margin="0,0,0,8"/>
                        <TextBlock Text="Khởi chạy thông qua PHP và CSDL MySQL của WampServer (tự động quét tại C:\wamp64, D:\wamp64, C:\wamp). Phù hợp máy tính dùng Wamp." TextWrapping="Wrap" FontSize="12" Foreground="#cbd5e1" Margin="0,0,0,12"/>

                        <!-- Info box -->
                        <Border Background="#0f172a" CornerRadius="6" Padding="10" Margin="0,0,0,14">
                            <StackPanel>
                                <TextBlock Text="• Cổng Web Ứng dụng:  http://127.0.0.1:8000/renty" FontSize="11" Foreground="#4ade80"/>
                                <TextBlock Text="• Cơ sở dữ liệu WAMP:  localhost:3306 hoặc 3308" FontSize="11" Foreground="#94a3b8"/>
                                <TextBlock Text="• Tương thích:        WampServer 64-bit &amp; 32-bit" FontSize="11" Foreground="#94a3b8"/>
                            </StackPanel>
                        </Border>

                        <!-- Main Run Button -->
                        <Button Name="btnRunWampp" Content="🌿 KHỞI CHẠY BẰNG WAMPP" Height="42" FontSize="13" FontWeight="Bold"
                                Background="#16a34a" Foreground="White" BorderThickness="0" Cursor="Hand" Margin="0,0,0,10">
                            <Button.Resources>
                                <Style TargetType="Border">
                                    <Setter Property="CornerRadius" Value="8"/>
                                </Style>
                            </Button.Resources>
                        </Button>

                        <TextBlock Text="* Gợi ý: Đảm bảo biểu tượng Wamp ở góc màn hình có màu xanh lá." FontSize="11" Foreground="#64748b" HorizontalAlignment="Center"/>
                    </StackPanel>
                </Border>
            </TabItem>

            <!-- TAB 4: ADVANCED MENU (MENU NÂNG CAO CÓ LOGO) -->
            <TabItem Header="⚙️ Menu Nâng Cao">
                <Border Background="#1e293b" CornerRadius="0,8,8,8" Padding="16">
                    <StackPanel>
                        <!-- Banner Logo in Advanced Menu -->
                        <Border Background="#0f172a" CornerRadius="10" Padding="12,10" Margin="0,0,0,12" BorderThickness="1" BorderBrush="#334155">
                            <Grid>
                                <Grid.ColumnDefinitions>
                                    <ColumnDefinition Width="Auto"/>
                                    <ColumnDefinition Width="*"/>
                                </Grid.ColumnDefinitions>
                                <StackPanel Grid.Column="0" Orientation="Horizontal" VerticalAlignment="Center" Margin="0,0,14,0">
                                    <TextBlock Text="🏢" FontSize="24" VerticalAlignment="Center" Margin="0,0,4,0"/>
                                    <TextBlock Text="✕" FontSize="14" Foreground="#64748b" VerticalAlignment="Center" Margin="4,0"/>
                                    <TextBlock Text="🔍" FontSize="24" VerticalAlignment="Center"/>
                                </StackPanel>
                                <StackPanel Grid.Column="1" VerticalAlignment="Center">
                                    <TextBlock Text="SMARTROOM &amp; RENTY - SYSTEM SUITE" FontSize="13" FontWeight="Bold" Foreground="#38bdf8"/>
                                    <TextBlock Text="Bộ công cụ quản trị, bảo trì CSDL, dọn dẹp cache và điều hướng sitemap" FontSize="10" Foreground="#94a3b8"/>
                                </StackPanel>
                            </Grid>
                        </Border>

                        <!-- Grid of Advanced Quick Actions -->
                        <Grid Margin="0,0,0,10">
                            <Grid.RowDefinitions>
                                <RowDefinition Height="Auto"/>
                                <RowDefinition Height="Auto"/>
                                <RowDefinition Height="Auto"/>
                            </Grid.RowDefinitions>
                            <Grid.ColumnDefinitions>
                                <ColumnDefinition Width="*"/>
                                <ColumnDefinition Width="*"/>
                            </Grid.ColumnDefinitions>

                            <!-- Button: Reset DB -->
                            <Button Name="btnAdvResetDb" Grid.Row="0" Grid.Column="0" Height="36" Margin="0,0,4,6"
                                    Background="#1e1e38" Foreground="#c084fc" BorderThickness="1" BorderBrush="#4f46e5" Cursor="Hand">
                                <StackPanel Orientation="Horizontal">
                                    <TextBlock Text="🔄 " FontSize="12"/>
                                    <TextBlock Text="Làm mới CSDL (Reset &amp; Seed)" FontSize="11" FontWeight="SemiBold"/>
                                </StackPanel>
                            </Button>

                            <!-- Button: Clear Cache -->
                            <Button Name="btnAdvClearCache" Grid.Row="0" Grid.Column="1" Height="36" Margin="4,0,0,6"
                                    Background="#0f2b24" Foreground="#6ee7b7" BorderThickness="1" BorderBrush="#059669" Cursor="Hand">
                                <StackPanel Orientation="Horizontal">
                                    <TextBlock Text="🧹 " FontSize="12"/>
                                    <TextBlock Text="Dọn Cache &amp; Tối Ưu Hóa" FontSize="11" FontWeight="SemiBold"/>
                                </StackPanel>
                            </Button>

                            <!-- Button: Sitemap -->
                            <Button Name="btnAdvSitemap" Grid.Row="1" Grid.Column="0" Height="36" Margin="0,0,4,6"
                                    Background="#172554" Foreground="#93c5fd" BorderThickness="1" BorderBrush="#1d4ed8" Cursor="Hand">
                                <StackPanel Orientation="Horizontal">
                                    <TextBlock Text="🗺️ " FontSize="12"/>
                                    <TextBlock Text="Mở Sitemap Navigator" FontSize="11" FontWeight="SemiBold"/>
                                </StackPanel>
                            </Button>

                            <!-- Button: Diagnostics -->
                            <Button Name="btnAdvDiag" Grid.Row="1" Grid.Column="1" Height="36" Margin="4,0,0,6"
                                    Background="#2d1537" Foreground="#f472b6" BorderThickness="1" BorderBrush="#db2777" Cursor="Hand">
                                <StackPanel Orientation="Horizontal">
                                    <TextBlock Text="🩺 " FontSize="12"/>
                                    <TextBlock Text="Chẩn Đoán Sức Khỏe" FontSize="11" FontWeight="SemiBold"/>
                                </StackPanel>
                            </Button>

                            <!-- Button: Full Console CLI -->
                            <Button Name="btnAdvOpenCli" Grid.Row="2" Grid.ColumnSpan="2" Height="36" Margin="0,2,0,0"
                                    Background="#334155" Foreground="#f8fafc" BorderThickness="1" BorderBrush="#475569" Cursor="Hand">
                                <StackPanel Orientation="Horizontal">
                                    <TextBlock Text="💻 " FontSize="12"/>
                                    <TextBlock Text="Mở Toàn Bộ 11 Chức Năng Bằng Menu Console CMD" FontSize="11" FontWeight="Bold"/>
                                </StackPanel>
                            </Button>
                        </Grid>
                    </StackPanel>
                </Border>
            </TabItem>
        </TabControl>

        <!-- Status & App-mode row -->
        <Grid Grid.Row="2" Margin="0,0,0,10">
            <Grid.ColumnDefinitions>
                <ColumnDefinition Width="*"/>
                <ColumnDefinition Width="Auto"/>
            </Grid.ColumnDefinitions>
            <TextBlock Name="txtStatus" Grid.Column="0" Text="💡 Hãy chọn 1 Tab ở trên và nhấn nút để bắt đầu." FontSize="12" Foreground="#38bdf8" VerticalAlignment="Center"/>
            <CheckBox Name="chkAppMode" Grid.Column="1" Content="Mở tab nhỏ như App (App Mode)" IsChecked="True" FontSize="12" Foreground="#cbd5e1" VerticalAlignment="Center"/>
        </Grid>

        <!-- Footer Actions -->
        <Border Grid.Row="3" Padding="0,4,0,0">
            <Grid>
                <Grid.ColumnDefinitions>
                    <ColumnDefinition Width="*"/>
                    <ColumnDefinition Width="Auto"/>
                    <ColumnDefinition Width="Auto"/>
                </Grid.ColumnDefinitions>
                <TextBlock Grid.Column="0" Text="SmartRoom x Renty • System Orchestrator v8.0" FontSize="11" Foreground="#475569" VerticalAlignment="Center"/>
                <Button Name="btnOpenAdvTab" Grid.Column="1" Content="⚙️ Menu Nâng Cao" Height="28" Padding="12,0" FontSize="11"
                        Background="#1e293b" Foreground="#cbd5e1" BorderThickness="1" BorderBrush="#334155" Cursor="Hand" Margin="0,0,8,0"/>
                <Button Name="btnExit" Grid.Column="2" Content="Thoát" Height="28" Padding="16,0" FontSize="11"
                        Background="#334155" Foreground="#f8fafc" BorderThickness="0" Cursor="Hand"/>
            </Grid>
        </Border>
    </Grid>
</Window>
"@

$nodeReader = New-Object System.Xml.XmlNodeReader $xaml
$window = [System.Windows.Markup.XamlReader]::Load($nodeReader)

# Get element controls
$mainTabControl  = $window.FindName("mainTabControl")
$btnRunDocker    = $window.FindName("btnRunDocker")
$btnDockerLogs   = $window.FindName("btnDockerLogs")
$btnDockerSeed   = $window.FindName("btnDockerSeed")
$btnDockerStop   = $window.FindName("btnDockerStop")
$btnRunXampp     = $window.FindName("btnRunXampp")
$btnRunWampp     = $window.FindName("btnRunWampp")
$btnAdvResetDb   = $window.FindName("btnAdvResetDb")
$btnAdvClearCache= $window.FindName("btnAdvClearCache")
$btnAdvSitemap   = $window.FindName("btnAdvSitemap")
$btnAdvDiag      = $window.FindName("btnAdvDiag")
$btnAdvOpenCli   = $window.FindName("btnAdvOpenCli")
$btnOpenAdvTab   = $window.FindName("btnOpenAdvTab")
$btnExit         = $window.FindName("btnExit")
$txtStatus       = $window.FindName("txtStatus")
$chkAppMode      = $window.FindName("chkAppMode")

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path

# Ham dam bao MySQL da bat va san sang tiep nhan ket noi
function Ensure-MySQL {
    # 1. Kiem tra xem port 3306 da mo chua
    $isOpen = Test-NetConnection -ComputerName 127.0.0.1 -Port 3306 -InformationLevel Quiet -WarningAction SilentlyContinue
    if ($isOpen) {
        # Kiem tra & tu dong tao DB qlphongtro neu chua co
        $xamppDirs = @("D:\xampp", "C:\xampp", "E:\xampp")
        foreach ($d in $xamppDirs) {
            if (Test-Path "$d\mysql\bin\mysql.exe") {
                Start-Process -FilePath "$d\mysql\bin\mysql.exe" -ArgumentList "-u root -e `"CREATE DATABASE IF NOT EXISTS qlphongtro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`"" -WindowStyle Hidden -Wait
                break
            }
        }
        return $true
    }

    $txtStatus.Text = "⏳ MySQL chưa bật. Đang tự động kích hoạt CSDL MySQL XAMPP..."

    # 2. Tim mysqld.exe
    $xamppDirs = @("D:\xampp", "C:\xampp", "E:\xampp")
    $mysqldPath = $null
    $xamppRoot = $null
    foreach ($d in $xamppDirs) {
        if (Test-Path "$d\mysql\bin\mysqld.exe") {
            $mysqldPath = "$d\mysql\bin\mysqld.exe"
            $xamppRoot = $d
            break
        }
    }

    if ($mysqldPath) {
        Start-Process -FilePath "cmd.exe" -ArgumentList "/c cd /d `"$xamppRoot`" & mysql\bin\mysqld.exe --defaults-file=mysql\bin\my.ini --standalone" -WindowStyle Hidden
    } else {
        foreach ($w in @("C:\wamp64", "D:\wamp64")) {
            if (Test-Path "$w\bin\mysql") {
                $wMysql = Get-ChildItem "$w\bin\mysql\mysql*\bin\mysqld.exe" -ErrorAction SilentlyContinue | Select-Object -First 1
                if ($wMysql) {
                    Start-Process -FilePath $wMysql.FullName -ArgumentList "--standalone" -WindowStyle Hidden
                    break
                }
            }
        }
    }

    # 3. Cho port 3306 mo
    for ($i = 0; $i -lt 12; $i++) {
        Start-Sleep -Seconds 1
        $isOpen = Test-NetConnection -ComputerName 127.0.0.1 -Port 3306 -InformationLevel Quiet -WarningAction SilentlyContinue
        if ($isOpen) {
            if ($xamppRoot -and (Test-Path "$xamppRoot\mysql\bin\mysql.exe")) {
                Start-Process -FilePath "$xamppRoot\mysql\bin\mysql.exe" -ArgumentList "-u root -e `"CREATE DATABASE IF NOT EXISTS qlphongtro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`"" -WindowStyle Hidden -Wait
            }
            return $true
        }
    }

    return $false
}

# Ham mo website dang App Window
function Open-AppWindow($url) {
    if ($chkAppMode.IsChecked) {
        # Thu mo bang Chrome truoc (tao cua so tach biet nhu app)
        $chromePaths = @(
            "$env:ProgramFiles\Google\Chrome\Application\chrome.exe",
            "${env:ProgramFiles(x86)}\Google\Chrome\Application\chrome.exe",
            "$env:LOCALAPPDATA\Google\Chrome\Application\chrome.exe"
        )
        foreach ($p in $chromePaths) {
            if (Test-Path $p) {
                Start-Process -FilePath $p -ArgumentList "--app=$url"
                return
            }
        }
        # Neu khong co Chrome, mo bang Microsoft Edge App Mode
        $edgePaths = @(
            "$env:ProgramFiles\Microsoft\Edge\Application\msedge.exe",
            "${env:ProgramFiles(x86)}\Microsoft\Edge\Application\msedge.exe"
        )
        foreach ($p in $edgePaths) {
            if (Test-Path $p) {
                Start-Process -FilePath $p -ArgumentList "--app=$url"
                return
            }
        }
    }
    # Neu bo chon hoac khong co trinh duyet tren, mo mac dinh
    Start-Process $url
}

# 1. RUN DOCKER
$btnRunDocker.Add_Click({
    $txtStatus.Text = "⏳ Đang kiểm tra Docker & khởi chạy containers..."
    $btnRunDocker.IsEnabled = $false

    # Kiem tra Docker Daemon
    $dockerCheck = docker info 2>&1
    if ($LASTEXITCODE -ne 0) {
        [System.Windows.MessageBox]::Show("Docker Desktop chưa bật! Vui lòng khởi động Docker Desktop trên máy trước.", "Lỗi Docker", [System.Windows.MessageBoxButton]::OK, [System.Windows.MessageBoxImage]::Warning)
        $txtStatus.Text = "❌ Docker Desktop chưa chạy."
        $btnRunDocker.IsEnabled = $true
        return
    }

    # Chay docker compose up -d
    $txtStatus.Text = "⏳ Đang build và chạy Docker Compose (App + Database)..."
    Start-Process -FilePath "docker" -ArgumentList "compose up -d" -WorkingDirectory $scriptDir -Wait -NoNewWindow

    # Cho web san sang
    $txtStatus.Text = "⏳ Đang đợi dịch vụ trên cổng 8088 sẵn sàng..."
    $ready = $false
    for ($i = 0; $i -lt 30; $i++) {
        try {
            $resp = Invoke-WebRequest -Uri "http://127.0.0.1:8088/" -UseBasicParsing -TimeoutSec 1 -ErrorAction SilentlyContinue
            if ($resp.StatusCode -ge 200) {
                $ready = $true
                break
            }
        } catch { }
        Start-Sleep -Seconds 1
    }

    # Mo website dang cua so app
    $targetUrl = "http://localhost:8088/renty"
    Open-AppWindow $targetUrl
    $txtStatus.Text = "✅ Docker đã khởi chạy thành công! Đã mở cửa sổ ứng dụng."
    $btnRunDocker.IsEnabled = $true
})

# DOCKER LOGS
$btnDockerLogs.Add_Click({
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c title Docker Logs & cd /d `"$scriptDir`" & docker compose logs -f"
})

# DOCKER SEED
$btnDockerSeed.Add_Click({
    $txtStatus.Text = "⏳ Đang migrate & seed CSDL Docker..."
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c title Docker Seed & cd /d `"$scriptDir`" & docker compose exec app php artisan migrate:fresh --seed & pause"
    $txtStatus.Text = "✅ Đã gửi lệnh Seed CSDL vào container Docker."
})

# DOCKER STOP
$btnDockerStop.Add_Click({
    $txtStatus.Text = "⏳ Đang dừng toàn bộ container Docker..."
    Start-Process -FilePath "docker" -ArgumentList "compose down" -WorkingDirectory $scriptDir -Wait -NoNewWindow
    $txtStatus.Text = "⏹️ Đã dừng toàn bộ dịch vụ Docker thành công."
})

# 2. RUN XAMPP
$btnRunXampp.Add_Click({
    $txtStatus.Text = "⏳ Đang kiểm tra môi trường XAMPP..."
    $btnRunXampp.IsEnabled = $false

    # Phat hien PHP XAMPP
    $phpPath = $null
    $xamppDir = $null
    $candidates = @("D:\xampp", "C:\xampp", "E:\xampp")
    foreach ($cand in $candidates) {
        if (Test-Path "$cand\php\php.exe") {
            $phpPath = "$cand\php\php.exe"
            $xamppDir = $cand
            break
        }
    }
    if (-not $phpPath) {
        $p = (Get-Command php -ErrorAction SilentlyContinue).Source
        if ($p) { $phpPath = $p }
    }

    if (-not $phpPath) {
        [System.Windows.MessageBox]::Show("Không tìm thấy PHP XAMPP tại D:\xampp hoặc C:\xampp!", "Lỗi XAMPP", [System.Windows.MessageBoxButton]::OK, [System.Windows.MessageBoxImage]::Error)
        $txtStatus.Text = "❌ Không tìm thấy PHP của XAMPP."
        $btnRunXampp.IsEnabled = $true
        return
    }

    # Kiem tra & khoi dong MySQL
    $dbOk = Ensure-MySQL
    if (-not $dbOk) {
        [System.Windows.MessageBox]::Show("Không thể kết nối hoặc khởi động MySQL trên cổng 3306! Vui lòng kiểm tra XAMPP Control Panel.", "Cảnh báo CSDL", [System.Windows.MessageBoxButton]::OK, [System.Windows.MessageBoxImage]::Warning)
    }

    # Chuan hoa .env cho XAMPP
    $envFile = "$scriptDir\.env"
    if (Test-Path $envFile) {
        $content = Get-Content $envFile -Raw
        if ($content -match "DB_HOST=mysql") {
            $content = $content -replace "DB_HOST=mysql", "DB_HOST=127.0.0.1"
            $content = $content -replace "DB_PORT=.*", "DB_PORT=3306"
            $content = $content -replace "DB_USERNAME=.*", "DB_USERNAME=root"
            $content = $content -replace "DB_PASSWORD=.*", "DB_PASSWORD="
            [System.IO.File]::WriteAllText($envFile, $content, [System.Text.Encoding]::UTF8)
        }
    }

    # Khoi chay Laravel + Vite
    $txtStatus.Text = "⏳ Đang khởi chạy Laravel Server (port 8000) & Vite..."
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c title SmartRoom Laravel Server & cd /d `"$scriptDir`" & `"$phpPath`" artisan serve --port=8000"
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c title Vite Hot-Reload & cd /d `"$scriptDir`" & npm run dev"

    Start-Sleep -Seconds 3
    $targetUrl = "http://127.0.0.1:8000/renty"
    Open-AppWindow $targetUrl
    $txtStatus.Text = "✅ XAMPP đã khởi chạy thành công! Đã mở cửa sổ ứng dụng."
    $btnRunXampp.IsEnabled = $true
})

# 3. RUN WAMPP
$btnRunWampp.Add_Click({
    $txtStatus.Text = "⏳ Đang quét môi trường WampServer..."
    $btnRunWampp.IsEnabled = $false

    $phpPath = $null
    $wampDirs = @("C:\wamp64", "D:\wamp64", "C:\wamp", "D:\wamp")
    foreach ($w in $wampDirs) {
        if (Test-Path "$w\bin\php") {
            $phpVersions = Get-ChildItem "$w\bin\php\php*" -ErrorAction SilentlyContinue | Sort-Object Name -Descending
            foreach ($pv in $phpVersions) {
                if (Test-Path "$($pv.FullName)\php.exe") {
                    $phpPath = "$($pv.FullName)\php.exe"
                    break
                }
            }
            if ($phpPath) { break }
        }
    }

    if (-not $phpPath) {
        $p = (Get-Command php -ErrorAction SilentlyContinue).Source
        if ($p) { $phpPath = $p }
    }

    if (-not $phpPath) {
        [System.Windows.MessageBox]::Show("Không tìm thấy WampServer tại C:\wamp64 hoặc D:\wamp64!", "Lỗi WampServer", [System.Windows.MessageBoxButton]::OK, [System.Windows.MessageBoxImage]::Warning)
        $txtStatus.Text = "❌ Không tìm thấy WampServer."
        $btnRunWampp.IsEnabled = $true
        return
    }

    # Khoi chay
    $txtStatus.Text = "⏳ Đang khởi chạy ứng dụng với Wamp PHP..."
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c title SmartRoom Laravel (WAMP) & cd /d `"$scriptDir`" & `"$phpPath`" artisan serve --port=8000"
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c title Vite Server & cd /d `"$scriptDir`" & npm run dev"

    Start-Sleep -Seconds 3
    $targetUrl = "http://127.0.0.1:8000/renty"
    Open-AppWindow $targetUrl
    $txtStatus.Text = "✅ WampServer đã khởi chạy thành công! Đã mở cửa sổ ứng dụng."
    $btnRunWampp.IsEnabled = $true
})

# ADVANCED ACTIONS
$btnAdvResetDb.Add_Click({
    $txtStatus.Text = "⏳ Đang kiểm tra CSDL MySQL trước khi làm mới..."
    $dbOk = Ensure-MySQL
    if (-not $dbOk) {
        [System.Windows.MessageBox]::Show("Không thể kết nối hoặc khởi động MySQL (Port 3306)! Vui lòng mở XAMPP Control Panel và nhấn Start MySQL.", "Lỗi CSDL", [System.Windows.MessageBoxButton]::OK, [System.Windows.MessageBoxImage]::Error)
        $txtStatus.Text = "❌ CSDL MySQL chưa sẵn sàng."
        return
    }
    $txtStatus.Text = "⏳ Đang làm mới CSDL (migrate:fresh --seed)..."
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c title Reset DB & cd /d `"$scriptDir`" & php artisan migrate:fresh --seed & pause"
    $txtStatus.Text = "✅ Đã chạy lệnh làm mới CSDL."
})

$btnAdvClearCache.Add_Click({
    $txtStatus.Text = "⏳ Đang dọn dẹp cache & optimize hệ thống..."
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c title Optimize Clear & cd /d `"$scriptDir`" & php artisan optimize:clear & pause"
    $txtStatus.Text = "✅ Đã dọn sạch cache hệ thống."
})

$btnAdvSitemap.Add_Click({
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c title Sitemap Navigator & cd /d `"$scriptDir`" & start.bat --cli"
})

$btnAdvDiag.Add_Click({
    $txtStatus.Text = "⏳ Đang mở báo cáo chẩn đoán..."
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c title Health Diagnostic & cd /d `"$scriptDir`" & php artisan about & pause"
    $txtStatus.Text = "✅ Đã mở báo cáo hệ thống."
})

$btnAdvOpenCli.Add_Click({
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c cd /d `"$scriptDir`" & start.bat --cli"
})

# FOOTER BUTTONS
$btnOpenAdvTab.Add_Click({
    $mainTabControl.SelectedIndex = 3
})

$btnExit.Add_Click({
    $window.Close()
})

# Hien thi Window
$window.ShowDialog() | Out-Null
