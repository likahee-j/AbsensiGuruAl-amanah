@echo off
title Buka Port 8000 - Absensi Guru
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo Meminta izin Administrator...
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)
echo ============================================
echo  Membuka port 8000 untuk Absensi Guru...
echo ============================================
netsh advfirewall firewall delete rule name="Laravel Absensi 8000" >nul 2>&1
netsh advfirewall firewall add rule name="Laravel Absensi 8000" dir=in action=allow protocol=TCP localport=8000
echo.
echo  SELESAI! Port 8000 sudah dibuka.
echo  Buka dari HP: http://192.168.1.7:8000
echo.
pause
