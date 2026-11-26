@echo off
chcp 65001 >nul
title DAY CODE CHO THANH VIEN - dev-vietanh
cd /d "%~dp0..\.."
git status
set /p msg="Nhap noi dung commit (Enter de mac dinh): "
if "%msg%"=="" set msg=Cap nhat Lập trình Trang chủ, Danh mục & Bộ lọc sản phẩm - Việt Anh (Nguyễn Việt Anh)
git add .
git commit -m "%msg%"
git checkout -B dev-vietanh
git push -u origin dev-vietanh
echo ==============================================================
echo   DA DAY THANH CONG LEN NHANH dev-vietanh!
echo ==============================================================
pause
