@echo off
chcp 65001 >nul
title ĐẨY CODE DỰ ÁN 1 - TRƯỞNG NHÓM (LÊ VĂN SƠN)
echo ==============================================================
echo   ĐẨY CODE DỰ ÁN 1 - NHÓM 4: NEXVORA PHONE STORE
echo   Thành viên: Lê Văn Sơn (Trưởng nhóm)
echo   Nhánh sở hữu: dev-son ^& main
echo   Phụ trách: Kiến trúc MVC, Auth, Checkout, CSRF Security, Payment
echo ==============================================================
echo.

cd /d "%~dp0"

echo [1/5] Kiểm tra trạng thái Git...
git status
echo.

set /p msg="Nhập nội dung commit (Nhấn Enter để dùng mặc định): "
if "%msg%"=="" set msg=Cap nhat he thong boi Truong nhom Le Van Son

echo [2/5] Đang thêm file vào Git...
git add .

echo [3/5] Đang commit code...
git commit -m "%msg%"

echo [4/5] Đang đồng bộ và đẩy lên nhánh dev-son...
git checkout -B dev-son
git push -u origin dev-son

echo [5/5] Đang đồng bộ và đẩy lên nhánh main...
git checkout -B main
git merge dev-son
git push -u origin main

echo.
echo ==============================================================
echo   ĐẨY CODE THÀNH CÔNG LÊN NHÁNH dev-son VÀ main!
echo ==============================================================
pause
