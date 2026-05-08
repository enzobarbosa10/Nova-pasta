@echo off
echo ================================================
echo   Iniciando Backend API (Laravel)
echo ================================================
echo.

cd backend

REM Verifica se o arquivo .env existe
if not exist ".env" (
    echo Criando arquivo .env...
    copy .env.example .env
    C:\xampp\php\php.exe artisan key:generate
)

REM Verifica se as dependências estão instaladas
if not exist "vendor" (
    echo Instalando dependencias...
    C:\xampp\php\php.exe C:\xampp\composer.phar install
)

echo.
echo ================================================
echo   SISTEMA EXPEDITIONOS
echo ================================================
echo.
echo Backend API: http://localhost:8000
echo Frontend PHP: http://localhost/backend/public/dashboard.php
echo Welcome Page: http://localhost/welcome.html
echo.
echo Iniciando servidor Laravel...
echo.

C:\xampp\php\php.exe artisan serve
