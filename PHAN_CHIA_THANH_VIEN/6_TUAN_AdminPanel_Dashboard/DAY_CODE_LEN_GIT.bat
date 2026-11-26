@echo off
chcp 65001 >nul
title DAY CODE CHO THANH VIEN - dev-tuan
cd /d "%~dp0..\.."
git status
set /p msg="Nhap noi dung commit (Enter de mac dinh): "
if "%msg%"=="" set msg=Cap nhat Lập trình Bảng điều khiển Quản trị (Admin Panel) - Tuấn (Lê Danh Tuấn)
git add .
git commit -m "%msg%"
git checkout -B dev-tuan
git push -u origin dev-tuan
echo ==============================================================
echo   DA DAY THANH CONG LEN NHANH dev-tuan!
echo ==============================================================
pause
