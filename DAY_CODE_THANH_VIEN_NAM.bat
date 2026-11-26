@echo off
chcp 65001 >nul
title ĐẨY CODE DỰ ÁN 1 - Nguyễn Văn Nam
echo ==============================================================
echo   ĐẨY CODE DỰ ÁN 1 - NHÓM 4: NEXVORA PHONE STORE
echo   Thành viên: Nguyễn Văn Nam
echo   Nhánh sở hữu: dev-nam
echo   Phụ trách: Lập trình Chi tiết sản phẩm, Gallery ảnh & Đánh giá
echo ==============================================================
echo.

cd /d "%~dp0"
if exist "..\..\.git" cd /d "%~dp0..\.."

git status
echo.

set /p msg="Nhập nội dung commit (Nhấn Enter để dùng mặc định): "
if "%msg%"=="" set msg=Cap nhat Lập trình Chi tiết sản phẩm, Gallery ảnh & Đánh giá - Nguyễn Văn Nam

git add .
git commit -m "%msg%"

git checkout -B dev-nam
git push -u origin dev-nam
echo.
echo ==============================================================
echo   ĐẨY CODE THÀNH CÔNG LÊN NHÁNH dev-nam!
echo ==============================================================
pause
