@echo off
chcp 65001 >nul
title ĐẨY CODE DỰ ÁN 1 - Nguyễn Việt Anh
echo ==============================================================
echo   ĐẨY CODE DỰ ÁN 1 - NHÓM 4: NEXVORA PHONE STORE
echo   Thành viên: Nguyễn Việt Anh
echo   Nhánh sở hữu: dev-vietanh
echo   Phụ trách: Lập trình Trang chủ, Danh mục & Bộ lọc sản phẩm
echo ==============================================================
echo.

cd /d "%~dp0"
if exist "..\..\.git" cd /d "%~dp0..\.."

git status
echo.

set /p msg="Nhập nội dung commit (Nhấn Enter để dùng mặc định): "
if "%msg%"=="" set msg=Cap nhat Lập trình Trang chủ, Danh mục & Bộ lọc sản phẩm - Nguyễn Việt Anh

git add .
git commit -m "%msg%"

git checkout -B dev-vietanh
git push -u origin dev-vietanh
echo.
echo ==============================================================
echo   ĐẨY CODE THÀNH CÔNG LÊN NHÁNH dev-vietanh!
echo ==============================================================
pause
