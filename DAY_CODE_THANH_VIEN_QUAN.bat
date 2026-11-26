@echo off
chcp 65001 >nul
title ĐẨY CODE DỰ ÁN 1 - THÀNH VIÊN (NGUYỄN ĐẶNG MINH QUÂN)
echo ==============================================================
echo   ĐẨY CODE DỰ ÁN 1 - NHÓM 4: NEXVORA PHONE STORE
echo   Thành viên: Nguyễn Đặng Minh Quân
echo   Nhánh sở hữu: dev-quan
echo   Phụ trách: Quản lý Khách hàng, Liên hệ & Hỗ trợ bảo hành
echo ==============================================================
echo.

cd /d "%~dp0"

git status
echo.

set /p msg="Nhập nội dung commit (Nhấn Enter để dùng mặc định): "
if "%msg%"=="" set msg=Cap nhat module Khach hang, Lien he va Ho tro - Nguyen Dang Minh Quan

git add .
git commit -m "%msg%"

git checkout -B dev-quan
git push -u origin dev-quan

echo.
echo ==============================================================
echo   ĐẨY CODE THÀNH CÔNG LÊN NHÁNH dev-quan!
echo ==============================================================
pause
