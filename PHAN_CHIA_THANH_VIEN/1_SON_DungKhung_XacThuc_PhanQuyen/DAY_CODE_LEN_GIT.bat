@echo off
chcp 65001 >nul
title DAY CODE CHO THANH VIEN - dev-son
cd /d "%~dp0..\.."
git status
set /p msg="Nhap noi dung commit (Enter de mac dinh): "
if "%msg%"=="" set msg=Cap nhat Dựng khung hệ thống, Xác thực tài khoản, Phân quyền & Ghép nối mã nguồn - Sơn (Lê Văn Sơn - Trưởng nhóm)
git add .
git commit -m "%msg%"
git checkout -B dev-son
git push -u origin dev-son
echo ==============================================================
echo   DA DAY THANH CONG LEN NHANH dev-son!
echo ==============================================================
pause
