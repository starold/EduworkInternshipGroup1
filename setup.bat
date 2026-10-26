@echo off
echo ===================================================
echo     ED.RENT - Otomatisasi Setup Awal Proyek
echo ===================================================
echo.

if not exist .env (
    echo [1/5] Menyalin .env.example ke .env...
    copy .env.example .env
) else (
    echo [1/5] File .env sudah ada.
)

echo [2/5] Menginstal dependensi Composer...
call composer install --no-interaction

echo [3/5] Menghasilkan Application Key (APP_KEY)...
call php artisan key:generate

echo [4/5] Menjalankan migrasi database dan seeder...
call php artisan migrate --seed --force

echo [5/5] Membuat symbolic link storage...
call php artisan storage:link

echo.
echo ===================================================
echo   Setup Selesai!
echo   Sekarang Anda bisa menjalankan: php artisan serve
echo ===================================================
pause
