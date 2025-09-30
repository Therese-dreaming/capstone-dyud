# CSS/Design Not Loading - Troubleshooting Guide

## Problem: Design works on host PC but NOT on other devices

---

## ✅ CHECKLIST - Do these in order:

### 1. Check .env File (CRITICAL!)

Open: `c:\xampp\htdocs\capstone-dyud\.env`

**Must have NO trailing slashes:**
```env
APP_URL=https://192.168.1.29
ASSET_URL=https://192.168.1.29
```

**NOT:**
```env
APP_URL=https://192.168.1.29/    ❌ WRONG!
```

After changing, run:
```bash
php artisan config:clear
php artisan config:cache
```

---

### 2. Restart Apache

1. Open XAMPP Control Panel
2. Click **Stop** on Apache
3. Wait 3 seconds
4. Click **Start** on Apache
5. Ensure it starts without errors

---

### 3. Clear Browser Cache on OTHER Device

**This is CRITICAL! Old cached files cause issues.**

#### Desktop Browser:
- Press `Ctrl + Shift + Delete`
- Select "Cached images and files"
- Clear for "All time"
- Click Clear

#### Mobile Browser (Chrome/Safari):
- Go to browser Settings
- Privacy → Clear browsing data
- Select "Cached images and files"
- Clear

**OR** use Incognito/Private mode to test.

---

### 4. Test Diagnostic Page

On the other device, visit:
```
https://192.168.1.29/diagnostic.html
```

**What you should see:**
- Blue box with white text
- Red box with white text  
- Green box with white text

**If you see plain text (no colors):**
- CSS is NOT loading
- Check browser console (F12) for errors
- Look for 404 errors on CSS files

---

### 5. Check Asset Files Exist

On host PC, verify these files exist:
```
c:\xampp\htdocs\capstone-dyud\public\build\assets\app-URgzVN3Q.css
c:\xampp\htdocs\capstone-dyud\public\build\manifest.json
```

If missing, run:
```bash
npm run build
```

---

### 6. Test Direct CSS Access

On other device, try accessing CSS directly:
```
https://192.168.1.29/build/assets/app-URgzVN3Q.css
```

**Should see:** CSS code

**If you see 404 or error:**
- Apache virtual host issue
- Check: `C:\xampp\apache\conf\extra\httpd-ssl.conf`
- Ensure DocumentRoot points to: `C:/xampp/htdocs/capstone-dyud/public`

---

## 🔍 Common Issues & Solutions

### Issue: "NET::ERR_CERT_AUTHORITY_INVALID"

**Solution:** Accept the security certificate
1. Click "Advanced"
2. Click "Proceed to site"
3. This is normal for self-signed certificates

---

### Issue: CSS loads on HTTP but not HTTPS

**Solution:**
1. Check `.env` has `https://` (not `http://`)
2. Clear config cache: `php artisan config:clear`
3. Restart Apache

---

### Issue: Mixed Content Errors

**Solution:** Ensure ALL URLs use HTTPS
- Check browser console for mixed content warnings
- Update any hardcoded `http://` URLs to `https://`

---

### Issue: Assets load on localhost but not on IP

**Solution:**
1. `.env` must use IP address: `https://192.168.1.29`
2. NOT `https://localhost`
3. Clear config cache
4. Restart Apache

---

### Issue: "Failed to load resource: net::ERR_CONNECTION_REFUSED"

**Solution:**
1. Apache is not running - start it in XAMPP
2. Firewall blocking port 443
   - Windows Firewall → Allow an app
   - Allow Apache on Private networks
3. Check Apache error logs:
   - `C:\xampp\apache\logs\error.log`
   - `C:\xampp\apache\logs\capstone-ssl-error.log`

---

## 🛠️ Quick Fix Commands

Run these in PowerShell from project directory:

```powershell
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild config
php artisan config:cache

# Check if CSS exists
Test-Path "public\build\assets\app-URgzVN3Q.css"

# View current APP_URL
Get-Content ".env" | Select-String "APP_URL"
```

---

## 📱 Mobile-Specific Issues

### iOS Safari:
- Clear website data: Settings → Safari → Advanced → Website Data
- Try Private Browsing mode

### Android Chrome:
- Clear site data: Settings → Site settings → capstone-dyud → Clear & reset
- Try Incognito mode

---

## 🔧 Nuclear Option (If nothing works)

1. **Stop Apache**

2. **Delete config cache:**
   ```bash
   rm bootstrap/cache/config.php
   ```

3. **Update .env** (no trailing slashes):
   ```env
   APP_URL=https://192.168.1.29
   ASSET_URL=https://192.168.1.29
   ```

4. **Clear everything:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   php artisan optimize:clear
   ```

5. **Rebuild:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

6. **Start Apache**

7. **Clear browser cache on OTHER device**

8. **Test in Incognito/Private mode first**

---

## 📊 Verification Steps

After fixing, verify on OTHER device:

1. ✅ Visit: `https://192.168.1.29/diagnostic.html`
   - Should see colored boxes

2. ✅ Visit: `https://192.168.1.29/login`
   - Should see styled login page

3. ✅ Open browser console (F12)
   - No 404 errors for CSS files
   - No mixed content warnings

4. ✅ Check Network tab
   - CSS file loads with 200 status
   - Not 404 or 301/302 redirects

---

## 🆘 Still Not Working?

Check these logs for errors:

1. **Apache Error Log:**
   ```
   C:\xampp\apache\logs\error.log
   C:\xampp\apache\logs\capstone-ssl-error.log
   ```

2. **Laravel Log:**
   ```
   storage\logs\laravel.log
   ```

3. **Browser Console:**
   - Press F12
   - Check Console tab for JavaScript errors
   - Check Network tab for failed requests

---

## 💡 Prevention Tips

1. **Always use IP in .env** (not localhost) when accessing from other devices
2. **No trailing slashes** in URLs
3. **Clear caches** after any config change
4. **Restart Apache** after changing virtual host configs
5. **Clear browser cache** on client devices after updates

---

**Last Updated:** 2025-09-30
