@echo off
echo ================================================
echo   Verificacao do Sistema
echo ================================================
echo.

set ERRORS=0

echo [1/5] Verificando Node.js...
where node >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    node --version
    echo [OK] Node.js encontrado!
) else (
    echo [ERRO] Node.js nao encontrado!
    set /a ERRORS+=1
)

echo.
echo [2/5] Verificando NPM...
where npm >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    npm --version
    echo [OK] NPM encontrado!
) else (
    echo [ERRO] NPM nao encontrado!
    set /a ERRORS+=1
)

echo.
echo [3/5] Verificando PHP...
if exist "C:\xampp\php\php.exe" (
    C:\xampp\php\php.exe --version | findstr /C:"PHP"
    echo [OK] PHP encontrado no XAMPP!
) else (
    where php >nul 2>nul
    if %ERRORLEVEL% EQU 0 (
        php --version | findstr /C:"PHP"
        echo [OK] PHP encontrado!
    ) else (
        echo [ERRO] PHP nao encontrado!
        set /a ERRORS+=1
    )
)

echo.
echo [4/5] Verificando MySQL/MariaDB...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if %ERRORLEVEL% EQU 0 (
    echo [OK] MySQL esta rodando!
) else (
    echo [AVISO] MySQL nao esta rodando!
    echo Por favor, inicie o MySQL pelo XAMPP Control Panel.
    set /a ERRORS+=1
)

echo.
echo [5/5] Verificando arquivos de configuracao...
if exist ".env" (
    echo [OK] Frontend .env encontrado!
) else (
    echo [AVISO] Frontend .env nao encontrado!
)

if exist "backend\.env" (
    echo [OK] Backend .env encontrado!
) else (
    echo [ERRO] Backend .env nao encontrado!
    set /a ERRORS+=1
)

if exist "node_modules" (
    echo [OK] Dependencias do frontend instaladas!
) else (
    echo [AVISO] Dependencias do frontend nao instaladas!
    echo Execute: npm install
)

if exist "backend\vendor" (
    echo [OK] Dependencias do backend instaladas!
) else (
    echo [AVISO] Dependencias do backend nao instaladas!
    echo Execute: cd backend && composer install
)

echo.
echo ================================================
if %ERRORS% EQU 0 (
    echo   Sistema Pronto para Uso!
    echo ================================================
    echo.
    echo Tudo esta configurado corretamente.
    echo Execute 'start.bat' para iniciar o projeto.
) else (
    echo   Encontrados %ERRORS% problemas!
    echo ================================================
    echo.
    echo Por favor, corrija os problemas acima antes de continuar.
    echo Execute 'setup.bat' para configurar automaticamente.
)
echo.
pause
