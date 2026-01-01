@echo off
echo Starting Laravel and Vite...

REM Start Laravel server
start "Laravel Server" cmd /k "php artisan serve --host 0.0.0.0 --port 80"

REM Start Vite / npm dev server
start "Vite Dev Server" cmd /k "npm run dev"

echo All servers started.