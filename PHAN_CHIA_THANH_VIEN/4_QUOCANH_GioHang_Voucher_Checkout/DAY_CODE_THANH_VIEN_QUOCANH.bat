@echo off
chcp 65001 >nul
title ĐẨY CODE DỰ ÁN 1 - THÀNH VIÊN (PHẠM QUỐC ANH)
echo ==============================================================
echo   ĐẨY CODE DỰ ÁN 1 - NHÓM 4: NEXVORA PHONE STORE
echo   Thành viên: Phạm Quốc Anh
echo   Nhánh sở hữu: dev-quocanh
echo   Phụ trách: Lập trình Giỏ hàng, Mã giảm giá & Đặt hàng (Checkout)
echo ==============================================================
echo.

cd /d "%~dp0"

git status
echo.

set /p msg="Nhập nội dung commit (Nhấn Enter để dùng mặc định): "
if "%msg%"=="" set msg=Lap trinh Gio hang, Ma giam gia va Dat hang (Checkout) - Pham Quoc Anh

git add .
git commit -m "%msg%"

git checkout -B dev-quocanh
git push -u origin dev-quocanh

echo.
echo ==============================================================
echo   ĐẨY CODE THÀNH CÔNG LÊN NHÁNH dev-quocanh!
echo ==============================================================
pause