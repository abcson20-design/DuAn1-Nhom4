@echo off
chcp 65001 >nul
title ĐẨY CODE DỰ ÁN 1 - Nguyễn Đặng Minh Quân
echo ==============================================================
echo   ĐẨY CODE DỰ ÁN 1 - NHÓM 4: NEXVORA PHONE STORE
echo   Thành viên: Nguyễn Đặng Minh Quân
echo   Nhánh sở hữu: dev-quan
echo   Phụ trách: Lập trình Thanh toán Online & Theo dõi đơn hàng
echo ==============================================================
echo.

cd /d "%~dp0"
if exist "..\..\.git" cd /d "%~dp0..\.."

git status
echo.

set /p msg="Nhập nội dung commit (Nhấn Enter để dùng mặc định): "
if "%msg%"=="" set msg=Cap nhat Lập trình Thanh toán Online & Theo dõi đơn hàng - Nguyễn Đặng Minh Quân

git add .
git commit -m "%msg%"

git checkout -B dev-quan
git push -u origin dev-quan
echo.
echo ==============================================================
echo   ĐẨY CODE THÀNH CÔNG LÊN NHÁNH dev-quan!
echo ==============================================================
pause
