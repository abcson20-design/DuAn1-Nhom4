@echo off
chcp 65001 >nul
title DAY CODE CHO THANH VIEN - dev-quan
cd /d "%~dp0..\.."
git status
set /p msg="Nhap noi dung commit (Enter de mac dinh): "
if "%msg%"=="" set msg=Cap nhat Lập trình Thanh toán Online & Theo dõi đơn hàng - Quân (Nguyễn Đặng Minh Quân)
git add .
git commit -m "%msg%"
git checkout -B dev-quan
git push -u origin dev-quan
echo ==============================================================
echo   DA DAY THANH CONG LEN NHANH dev-quan!
echo ==============================================================
pause
