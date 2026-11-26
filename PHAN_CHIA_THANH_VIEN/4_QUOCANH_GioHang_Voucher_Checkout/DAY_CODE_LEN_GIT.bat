@echo off
chcp 65001 >nul
title DAY CODE CHO THANH VIEN - dev-quocanh
cd /d "%~dp0..\.."
git status
set /p msg="Nhap noi dung commit (Enter de mac dinh): "
if "%msg%"=="" set msg=Cap nhat Lập trình Giỏ hàng, Mã giảm giá & Đặt hàng (Checkout) - Quốc Anh (Phạm Quốc Anh)
git add .
git commit -m "%msg%"
git checkout -B dev-quocanh
git push -u origin dev-quocanh
echo ==============================================================
echo   DA DAY THANH CONG LEN NHANH dev-quocanh!
echo ==============================================================
pause
