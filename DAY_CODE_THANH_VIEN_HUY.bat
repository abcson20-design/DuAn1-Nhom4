@echo off
chcp 65001 >nul
title ĐẨY CODE DỰ ÁN 1 - THÀNH VIÊN (LÊ THÀNH HUY)
echo ==============================================================
echo   ĐẨY CODE DỰ ÁN 1 - NHÓM 4: NEXVORA PHONE STORE
echo   Thành viên: Lê Thành Huy
echo   Nhánh sở hữu: dev-huy
echo   Phụ trách: Quản lý Sản phẩm, Danh mục, Thương hiệu (Catalog & Products)
echo ==============================================================
echo.

cd /d "%~dp0"

git status
echo.

set /p msg="Nhập nội dung commit (Nhấn Enter để dùng mặc định): "
if "%msg%"=="" set msg=Cap nhat module San pham, Danh muc, Thuong hieu - Le Thanh Huy

git add .
git commit -m "%msg%"

git checkout -B dev-huy
git push -u origin dev-huy

echo.
echo ==============================================================
echo   ĐẨY CODE THÀNH CÔNG LÊN NHÁNH dev-huy!
echo ==============================================================
pause
