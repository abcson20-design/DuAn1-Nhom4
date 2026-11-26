@echo off
chcp 65001 >nul
title DAY CODE CHO THANH VIEN - dev-nam
cd /d "%~dp0..\.."
git status
set /p msg="Nhap noi dung commit (Enter de mac dinh): "
if "%msg%"=="" set msg=Cap nhat Lập trình Chi tiết sản phẩm, Gallery ảnh & Đánh giá - Nam (Nguyễn Văn Nam / Vũ Đình Nam)
git add .
git commit -m "%msg%"
git checkout -B dev-nam
git push -u origin dev-nam
echo ==============================================================
echo   DA DAY THANH CONG LEN NHANH dev-nam!
echo ==============================================================
pause
