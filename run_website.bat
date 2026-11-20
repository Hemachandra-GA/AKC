@echo off
title AKC Website Local Server (Keep Open)
cd /d "%~dp0"

:: Check if nested inside AKC-main folder
if exist "AKC-main\router.php" (
    cd "AKC-main"
)

echo ========================================================
echo               AKC Website Local Server
echo ========================================================
echo.

set "PHP_CMD="

:: Check if php is in PATH
where php >nul 2>&1
if %ERRORLEVEL% equ 0 (
    set "PHP_CMD=php"
    goto START_SERVER
)

:: Check common installation paths
if exist "C:\xampp\php\php.exe" (
    set "PHP_CMD=C:\xampp\php\php.exe"
    goto START_SERVER
)

if exist "C:\php\php.exe" (
    set "PHP_CMD=C:\php\php.exe"
    goto START_SERVER
)

for /d %%D in ("C:\laragon\bin\php\php*") do (
    if exist "%%D\php.exe" (
        set "PHP_CMD=%%D\php.exe"
        goto START_SERVER
    )
)

for /d %%D in ("C:\wamp64\bin\php\php*") do (
    if exist "%%D\php.exe" (
        set "PHP_CMD=%%D\php.exe"
        goto START_SERVER
    )
)

for /d %%D in ("C:\Program Files\PHP\v*") do (
    if exist "%%D\php.exe" (
        set "PHP_CMD=%%D\php.exe"
        goto START_SERVER
    )
)

:: If PHP is not found anywhere
echo [ERROR] PHP was not found on your computer.
echo.
echo Because this website is built in PHP, PHP is required to run it.
echo.
echo Please choose one of the following options:
echo   1. Install XAMPP from https://www.apachefriends.org
echo      (Then start Apache from XAMPP Control Panel)
echo   2. Or download PHP for Windows from https://windows.php.net/download/
echo      and add it to your System PATH.
echo.
echo ========================================================
pause
exit /b

:START_SERVER
echo Found PHP at: %PHP_CMD%
echo.
echo Starting local web server at http://localhost:8000 ...
echo.
echo *** IMPORTANT: Keep this window OPEN while browsing! ***
echo (You can minimize it. If you close it, the site will stop.)
echo.

:: Automatically launch default web browser after 1 second
start "" cmd /c "timeout /t 1 /nobreak >nul & start http://localhost:8000"

:RUN_LOOP
"%PHP_CMD%" -S localhost:8000 router.php
echo.
echo Server stopped. Restarting in 2 seconds (Press Ctrl+C to stop)...
timeout /t 2 /nobreak >nul
goto RUN_LOOP
