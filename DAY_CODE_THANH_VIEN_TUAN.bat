@echo off
chcp 65001 >nul
title ĐẨY CODE DỰ ÁN 1 - Lê Danh Tuấn
echo ==============================================================
echo   ĐẨY CODE DỰ ÁN 1 - NHÓM 4: NEXVORA PHONE STORE
echo   Thành viên: Lê Danh Tuấn
echo   Nhánh sở hữu: dev-tuan
echo   Phụ trách: Lập trình Bảng điều khiển Quản trị (Admin Panel)
echo ==============================================================
echo.

cd /d "%~dp0"
if exist "..\..\.git" cd /d "%~dp0..\.."

git status
echo.

set /p msg="Nhập nội dung commit (Nhấn Enter để dùng mặc định): "
if "%msg%"=="" set msg=Cap nhat Lập trình Bảng điều khiển Quản trị (Admin Panel) - Lê Danh Tuấn

git add .
git commit -m "%msg%"

git checkout -B dev-tuan
git push -u origin dev-tuan
echo.
echo ==============================================================
echo   ĐẨY CODE THÀNH CÔNG LÊN NHÁNH dev-tuan!
echo ==============================================================
pause
