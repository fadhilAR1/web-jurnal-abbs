@echo off
REM Quick Start Script untuk Laragon

echo.
echo ====================================
echo  SMA ABBS Surakarta - Quick Start
echo ====================================
echo.
echo Pastikan:
echo 1. Laragon sudah terinstall di C:\laragon
echo 2. Apache dan MySQL sudah running di Laragon
echo 3. Virtual Host sudah dikonfigurasi
echo.

REM Check if laragon is running
sc query "Apache2.4" >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [!] Apache tidak running. Buka Laragon dan start services.
    echo Tekan Enter untuk membuka Laragon...
    pause
    start C:\laragon\Laragon.exe
)

echo.
echo [*] Membersihkan cache...
php artisan config:cache
php artisan cache:clear

echo.
echo [*] Database siap!
echo.
echo [OK] Aplikasi siap diakses di:
echo     http://sma-abbs-surakarta.local
echo.
echo Kredensial Login:
echo   Email: admin@example.com
echo   Password: password
echo.
echo Tekan Enter untuk membuka browser...
pause

REM Open browser
start http://sma-abbs-surakarta.local

echo.
echo Aplikasi sudah dibuka. Selamat menggunakan!
echo.
pause
