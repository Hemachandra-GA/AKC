@echo off
title Push AKC Code to GitHub
color 0A

echo ========================================================
echo             PUSHING AKC CODE TO GITHUB
echo ========================================================
echo.

cd /d "%~dp0"
echo Current Directory: %CD%
echo.

set "REPO_URL=https://github.com/mamatha1212/AKC.git"
echo Default GitHub repository: %REPO_URL%
echo.
set /p "USER_INPUT_REPO=Press Enter to use default repo, or paste new URL: "
if not "%USER_INPUT_REPO%"=="" set "REPO_URL=%USER_INPUT_REPO%"

echo.
echo Target Repository: %REPO_URL%
echo.

echo [1/5] Checking Git repository...
if not exist ".git" (
    git init
    git branch -M main
)

echo.
echo [2/5] Setting Remote origin...
git remote remove origin 2>nul
git remote add origin %REPO_URL%

echo.
echo [3/5] Staging files...
git add -A

echo.
echo [4/5] Committing changes...
git commit -m "Update AKC portal: sticky inline calendar, anti-spam mail sender, and admin controls"

echo.
echo [5/5] Pushing to GitHub (main)...
git push -u origin main
if %ERRORLEVEL%==0 goto done

echo.
echo --------------------------------------------------------
echo [NOTE] Push was rejected. Attempting to pull with rebase...
echo --------------------------------------------------------
git pull origin main --rebase
git push -u origin main
if %ERRORLEVEL%==0 goto done

echo.
echo --------------------------------------------------------
echo If you wish to overwrite the remote branch with your
echo current local code, choose Force Push below.
echo --------------------------------------------------------
set /p "FORCE_CHOICE=Do you want to Force Push to GitHub? (Y/N): "
if /i "%FORCE_CHOICE%"=="Y" (
    git push -u origin main --force
)

:done
echo.
echo ========================================================
echo   Finished!
echo ========================================================
pause

