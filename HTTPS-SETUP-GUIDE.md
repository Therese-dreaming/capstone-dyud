# HTTPS Setup Guide for Capstone Project

## Overview
This guide will help you set up HTTPS for your Laravel project so that camera access works on mobile devices.

---

## Step 1: Update .env File

Open `c:\xampp\htdocs\capstone-dyud\.env` and update:

```env
APP_URL=https://192.168.1.29
ASSET_URL=https://192.168.1.29
```

Save the file.

---

## Step 2: Generate SSL Certificate

1. **Run the certificate generation script:**
   - Double-click: `c:\xampp\htdocs\capstone-dyud\generate-ssl-cert.bat`
   - This will create a self-signed SSL certificate valid for 365 days
   - The certificate includes your IP address (192.168.1.29) and localhost

2. **What it creates:**
   - Certificate: `C:\xampp\apache\conf\ssl.crt\capstone.crt`
   - Private Key: `C:\xampp\apache\conf\ssl.key\capstone.key`

---

## Step 3: Clear Laravel Cache

Open PowerShell/Command Prompt in your project folder and run:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## Step 4: Restart Apache

1. Open **XAMPP Control Panel**
2. **Stop Apache** (if running)
3. **Start Apache** again
4. Ensure **port 443** is not blocked by firewall

---

## Step 5: Access Your Site

### From Host Computer:
- `https://localhost`
- `https://192.168.1.29`

### From Mobile/Other Devices:
- `https://192.168.1.29`

**IMPORTANT:** You will see a security warning because it's a self-signed certificate. This is normal!

---

## Step 6: Accept Security Warning

### On Desktop Browser (Chrome/Edge/Firefox):
1. You'll see "Your connection is not private" or similar
2. Click **Advanced**
3. Click **Proceed to 192.168.1.29 (unsafe)** or **Accept the Risk**

### On Mobile (Android):
1. Tap **Advanced**
2. Tap **Proceed to 192.168.1.29 (unsafe)**

### On Mobile (iOS/Safari):
1. Tap **Show Details**
2. Tap **visit this website**
3. Tap **Visit Website** again

---

## Step 7: Test Camera Access

Once you've accepted the certificate:
1. Navigate to the QR scanner or camera feature
2. The browser should now prompt for camera permission
3. Grant camera access
4. Camera should work! 📷

---

## Troubleshooting

### Apache Won't Start
- **Check if port 443 is in use:**
  ```bash
  netstat -ano | findstr :443
  ```
- **Check Apache error logs:**
  - `C:\xampp\apache\logs\error.log`
  - `C:\xampp\apache\logs\capstone-ssl-error.log`

### Certificate Error Persists
- Make sure you ran the `generate-ssl-cert.bat` script
- Check that the certificate files exist:
  - `C:\xampp\apache\conf\ssl.crt\capstone.crt`
  - `C:\xampp\apache\conf\ssl.key\capstone.key`

### Assets Still Not Loading
- Clear browser cache (Ctrl+Shift+Delete)
- Check `.env` has correct HTTPS URLs
- Run: `php artisan config:clear`

### Camera Still Not Working
- Ensure you're using **HTTPS** (not HTTP)
- Check browser console for errors (F12)
- Make sure you accepted the security certificate warning
- Grant camera permissions when prompted

---

## About IP Address Changes

**Your IP (192.168.1.29) may change if:**
- Your router restarts
- Your DHCP lease expires
- You reconnect to the network

**To prevent this (Optional):**

### Option A: Set Static IP in Router
1. Log into your router (usually 192.168.1.1)
2. Find DHCP settings
3. Reserve 192.168.1.29 for your computer's MAC address

### Option B: Set Static IP in Windows
1. Open Network Connections (Win+R → `ncpa.cpl`)
2. Right-click your network adapter → Properties
3. Select "Internet Protocol Version 4 (TCP/IPv4)" → Properties
4. Select "Use the following IP address"
5. Enter:
   - IP address: `192.168.1.29`
   - Subnet mask: `255.255.255.0`
   - Default gateway: `192.168.1.1` (your router IP)
   - Preferred DNS: `8.8.8.8`
   - Alternate DNS: `8.8.4.4`

**If your IP changes**, you'll need to:
1. Update `.env` with new IP
2. Re-run `generate-ssl-cert.bat` with new IP
3. Restart Apache

---

## Security Notes

⚠️ **This is a self-signed certificate for development only!**

- Do NOT use this in production
- Browsers will always show a warning (this is normal)
- For production, use a proper SSL certificate from Let's Encrypt or a CA

---

## Configuration Files Modified

The following files were configured for HTTPS:

1. `C:\xampp\apache\conf\extra\httpd-ssl.conf` - HTTPS virtual host
2. `C:\xampp\apache\conf\extra\httpd-vhosts.conf` - HTTP to HTTPS redirect
3. `.env` - Laravel app URL configuration

---

## Quick Reference

| What | URL |
|------|-----|
| **Host PC** | https://localhost or https://192.168.1.29 |
| **Mobile/Other Devices** | https://192.168.1.29 |
| **HTTP (auto-redirects)** | http://192.168.1.29 → https://192.168.1.29 |

---

## Need Help?

If you encounter issues:
1. Check Apache error logs
2. Ensure firewall allows port 443
3. Verify certificate files exist
4. Make sure Apache is running in XAMPP
5. Try accessing from host PC first before mobile

---

**Last Updated:** 2025-09-30
**Certificate Validity:** 365 days from generation
