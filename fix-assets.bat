@echo off
echo ========================================
echo Fixing Asset Loading Issues
echo ========================================
echo.

cd /d C:\xampp\htdocs\capstone-dyud

echo Step 1: Clearing Laravel caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo.
echo Step 2: Removing hot file if exists...
if exist "public\hot" (
    del "public\hot"
    echo Hot file removed
) else (
    echo No hot file found
)

echo.
echo Step 3: Checking build assets...
if exist "public\build\assets\app-URgzVN3Q.css" (
    echo [OK] CSS file exists
) else (
    echo [ERROR] CSS file missing! Run: npm run build
)

if exist "public\build\manifest.json" (
    echo [OK] Manifest exists
) else (
    echo [ERROR] Manifest missing! Run: npm run build
)

echo.
echo Step 4: Caching config...
php artisan config:cache

echo.
echo ========================================
echo DONE!
echo ========================================
echo.
echo Next steps:
echo 1. Make sure .env has NO trailing slash:
echo    APP_URL=https://192.168.1.29
echo    ASSET_URL=https://192.168.1.29
echo.
echo 2. Restart Apache in XAMPP
echo.
echo 3. Clear browser cache on OTHER device:
echo    - Chrome/Edge: Ctrl+Shift+Delete
echo    - Mobile: Clear browser data
echo.
echo 4. Test with diagnostic page:
echo    https://192.168.1.29/diagnostic.html
echo.
pause
