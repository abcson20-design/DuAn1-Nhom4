@echo off
chcp 65001 >nul
title ĐẨY CODE DỰ ÁN 1 - Lê Văn Sơn (Trưởng nhóm)
echo ==============================================================
echo   ĐẨY CODE DỰ ÁN 1 - NHÓM 4: NEXVORA PHONE STORE
echo   Thành viên: Lê Văn Sơn (Trưởng nhóm)
echo   Nhánh sở hữu: dev-son
echo   Phụ trách: Dựng khung hệ thống, Xác thực tài khoản, Phân quyền & Ghép nối mã nguồn
echo ==============================================================
echo.

cd /d "%~dp0"
if exist "..\..\.git" cd /d "%~dp0..\.."

git status
echo.

set /p msg="Nhập nội dung commit (Nhấn Enter để dùng mặc định): "
if "%msg%"=="" set msg=Cap nhat Dựng khung hệ thống, Xác thực tài khoản, Phân quyền & Ghép nối mã nguồn - Lê Văn Sơn (Trưởng nhóm)

git add .
git commit -m "%msg%"

git checkout -B dev-son
git push -u origin dev-son

git checkout -B main
git merge dev-son
git push -u origin main
echo.
echo ==============================================================
echo   ĐẨY CODE THÀNH CÔNG LÊN NHÁNH dev-son!
echo ==============================================================
pause
