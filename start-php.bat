@echo off
echo ========================================
echo   INICIANDO SERVIDOR PHP
echo ========================================
echo.
echo O servidor PHP sera iniciado em:
echo http://localhost/backend/public/dashboard.php
echo.
echo Pressione Ctrl+C para parar o servidor
echo ========================================
echo.

REM Verificar se o XAMPP esta rodando
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] Apache esta rodando
) else (
    echo [ERRO] Apache nao esta rodando!
    echo Por favor, inicie o XAMPP Control Panel e inicie o Apache
    pause
    exit /b 1
)

REM Verificar se o MySQL esta rodando
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL esta rodando
) else (
    echo [AVISO] MySQL nao esta rodando
    echo Algumas funcionalidades podem nao funcionar
)

echo.
echo ========================================
echo Abrindo navegador...
echo ========================================

REM Abrir o navegador
start http://localhost/backend/public/dashboard.php

echo.
echo Servidor rodando! Pressione qualquer tecla para sair...
pause >nul
