@echo off
title Tornado Otomatis (TSA PDAM)
color 0A

echo ===================================================
echo   MEMULAI TORNADO (NGROK) UNTUK TIARA SMART ASISTEN
echo ===================================================
echo.

:: Menutup proses ngrok dan localtunnel yang mungkin tersangkut sebelumnya
taskkill /f /im ngrok.exe >nul 2>&1
taskkill /f /im node.exe >nul 2>&1

echo [1/3] Menyalakan Ngrok...
start "Ngrok Tornado" cmd /c "c:\laragon\bin\ngrok\ngrok.exe http pdam-agenda.test:80 --host-header=rewrite"

echo [2/3] Menunggu 5 detik agar Ngrok terhubung ke server pusat...
ping 127.0.0.1 -n 6 > nul

echo [3/3] Mendaftarkan URL Baru ke Telegram Bot...
c:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe set_webhook_auto.php

echo.
echo ===================================================
echo   SELESAI! JENDELA INI BOLEH DITUTUP.
echo   SISTEM SUDAH ONLINE DAN SIAP DIGUNAKAN.
echo ===================================================
pause
