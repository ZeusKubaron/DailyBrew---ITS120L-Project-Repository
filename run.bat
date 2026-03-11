@echo off
title DailyBrew - AI Student Scheduler
cd /d %~dp0
echo.
echo ========================================
echo    Starting DailyBrew...
echo ========================================
echo.

:: Check if portable PHP exists
if not exist "php\php.exe" (
    echo ERROR: PHP not found!
    echo.
    echo Please download portable PHP:
    echo 1. Go to: https://windows.php.net/download
    echo 2. Download "PHP 8.x Thread Safe" ZIP
    echo 3. Extract the ZIP contents into the "php" folder
    echo.
    echo Or use XAMPP if you have it installed.
    echo.
    pause
    exit /b 1
)

echo Starting server at http://localhost:8000
echo Press Ctrl+C to stop the server
echo.
php\php.exe -c php.ini -d extension_dir="php/ext" -S localhost:8000
pause

