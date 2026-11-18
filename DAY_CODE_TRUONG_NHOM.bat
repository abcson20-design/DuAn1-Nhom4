@echo off
chcp 65001 >nul
title DAY CODE TRUONG NHOM (SON) - DU AN 1 NHOM 4
echo ========================================================
echo   DANG DAY CODE TRUONG NHOM (SON) LEN NHANH MAIN...
echo ========================================================
echo.
cd /d %~dp0
git status -s
echo.
git add .
git commit -m "Son (Truong nhom): Cap nhat he thong Nexvora Store"
git push origin main
echo.
echo ========================================================
echo   DA DAY CODE LEN NHANH MAIN THANH CONG!
echo ========================================================
pause
