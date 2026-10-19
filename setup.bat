@echo off
REM Setup Script untuk SMA ABBS Surakarta

echo.
echo ====================================
echo  SMA ABBS Surakarta Setup Script
echo ====================================
echo.

REM Check if composer is installed
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo Error: Composer tidak ditemukan. Pastikan Composer sudah terinstall.
    exit /b 1
)

REM Step 1: Install dependencies
echo [1/6] Installing Composer dependencies...
call composer install
if %ERRORLEVEL% NEQ 0 (
    echo Error: Gagal install composer dependencies
    exit /b 1
)

REM Step 2: Generate APP_KEY
echo [2/6] Generating APP_KEY...
php artisan key:generate
if %ERRORLEVEL% NEQ 0 (
    echo Error: Gagal generate APP_KEY
    exit /b 1
)

REM Step 3: Create .env if not exists
echo [3/6] Checking .env file...
if not exist ".env" (
    copy .env.example .env
    echo .env file created
)

REM Step 4: Run migrations
echo [4/6] Running migrations...
php artisan migrate --force
if %ERRORLEVEL% NEQ 0 (
    echo Error: Gagal run migrations
    exit /b 1
)

REM Step 5: Run seeders
echo [5/6] Seeding database...
php artisan db:seed --force
if %ERRORLEVEL% NEQ 0 (
    echo Error: Gagal seed database
    exit /b 1
)

REM Step 6: Clear caches
echo [6/6] Clearing caches...
php artisan config:cache
php artisan cache:clear

echo.
echo ====================================
echo  Setup Selesai!
echo ====================================
echo.
echo Kredensial Login:
echo   Email: admin@example.com
echo   Password: password
echo.
echo Jalankan server dengan:
echo   php artisan serve
echo.
echo Atau akses melalui Laragon:
echo   http://sma-abbs-surakarta.local
echo.
pause
