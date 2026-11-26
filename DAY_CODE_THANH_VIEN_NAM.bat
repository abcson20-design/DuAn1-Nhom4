@echo off
chcp 65001 >nul
title ĐẨY CODE DỰ ÁN 1 - THÀNH VIÊN (NGUYỄN HOÀNG NAM)
echo ==============================================================
echo   ĐẨY CODE DỰ ÁN 1 - NHÓM 4: NEXVORA PHONE STORE
echo   Thành viên: Nguyễn Hoàng Nam
echo   Nhánh sở hữu: dev-nam
echo   Phụ trách: Mã giảm giá Khuyến mại, Đánh giá & Bình luận (Coupons & Reviews)
echo ==============================================================
echo.

cd /d "%~dp0"

git status
echo.

set /p msg="Nhập nội dung commit (Nhấn Enter để dùng mặc định): "
if "%msg%"=="" set msg=Cap nhat module Ma giam gia va Danh gia binh luan - Nguyen Hoang Nam

git add .
git commit -m "%msg%"

git checkout -B dev-nam
git push -u origin dev-nam

echo.
echo ==============================================================
echo   ĐẨY CODE THÀNH CÔNG LÊN NHÁNH dev-nam!
echo ==============================================================
pause
