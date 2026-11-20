@echo off
title Deploy AKC to Vercel (akc-beta)
cd /d "%~dp0"

:: Check if nested inside AKC-main folder
if exist "AKC-main\vercel.json" (
    cd "AKC-main"
)

echo ========================================================
echo             Deploying AKC to Vercel
echo ========================================================
echo.
echo Target Project: akc-beta
echo Target URL:     https://akc-beta.vercel.app
echo.

where npx >nul 2>&1
if %ERRORLEVEL% equ 0 (
    echo [INFO] Running Vercel deployment...
    echo.
    echo If prompted:
    echo  - Set up and deploy? [Y]
    echo  - Link to existing project? [Y]
    echo  - What is the name of the existing project? Enter: akc-beta
    echo.
    npx --yes vercel --prod
    goto DONE
)

where vercel >nul 2>&1
if %ERRORLEVEL% equ 0 (
    echo [INFO] Running Vercel CLI...
    vercel --prod
    goto DONE
)

echo [ERROR] Neither Node.js (npx) nor Vercel CLI was found.
echo.
echo To deploy to Vercel, please do one of the following:
echo   Option 1: Push your latest code changes to your GitHub repository
echo             (Vercel will automatically build and deploy to akc-beta).
echo.
echo   Option 2: Install Node.js from https://nodejs.org
echo             Then run this script again.
echo.

:DONE
echo.
echo ========================================================
pause

