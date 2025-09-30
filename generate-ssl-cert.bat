@echo off
echo ========================================
echo Generating SSL Certificate for Capstone
echo ========================================
echo.

cd C:\xampp\apache

echo Creating OpenSSL configuration file...
(
echo [req]
echo default_bits = 2048
echo prompt = no
echo default_md = sha256
echo distinguished_name = dn
echo req_extensions = v3_req
echo.
echo [dn]
echo C=PH
echo ST=Philippines
echo L=Manila
echo O=Capstone Project
echo OU=Development
echo CN=192.168.1.29
echo.
echo [v3_req]
echo subjectAltName = @alt_names
echo.
echo [alt_names]
echo IP.1 = 192.168.1.29
echo DNS.1 = localhost
echo DNS.2 = capstone.local
) > openssl-capstone.cnf

echo.
echo Generating private key and certificate...
bin\openssl.exe req -x509 -nodes -days 365 -newkey rsa:2048 ^
    -keyout conf\ssl.key\capstone.key ^
    -out conf\ssl.crt\capstone.crt ^
    -config openssl-capstone.cnf ^
    -extensions v3_req

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo SUCCESS! Certificate generated!
    echo ========================================
    echo.
    echo Certificate: C:\xampp\apache\conf\ssl.crt\capstone.crt
    echo Private Key: C:\xampp\apache\conf\ssl.key\capstone.key
    echo.
    echo Next steps:
    echo 1. The certificate files have been created
    echo 2. Restart Apache in XAMPP Control Panel
    echo 3. Access your site via https://192.168.1.29
    echo 4. Accept the security warning in your browser
    echo.
) else (
    echo.
    echo ERROR: Failed to generate certificate
    echo Please check if OpenSSL is available in XAMPP
)

del openssl-capstone.cnf
pause
