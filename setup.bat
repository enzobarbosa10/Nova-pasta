@echo off
echo ================================================
echo   Setup Inicial - Expedition Management SaaS
echo ================================================
echo.

REM Verifica Node.js
where node >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERRO] Node.js nao encontrado!
    echo Por favor, instale o Node.js em: https://nodejs.org/
    pause
    exit /b 1
)

REM Verifica PHP
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [AVISO] PHP nao encontrado no PATH!
    echo Usando PHP do XAMPP...
)

echo [1/6] Instalando dependencias do frontend...
call npm install
if %ERRORLEVEL% NEQ 0 (
    echo [ERRO] Falha ao instalar dependencias do frontend!
    pause
    exit /b 1
)

echo.
echo [2/6] Instalando dependencias do backend...
cd backend

REM Tenta usar composer global, senão usa o do XAMPP
where composer >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    composer install
) else (
    if exist "C:\xampp\php\php.exe" (
        if exist "C:\xampp\composer.phar" (
            C:\xampp\php\php.exe C:\xampp\composer.phar install
        ) else (
            echo [AVISO] Composer nao encontrado. Baixando...
            C:\xampp\php\php.exe -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
            C:\xampp\php\php.exe composer-setup.php
            C:\xampp\php\php.exe -r "unlink('composer-setup.php');"
            C:\xampp\php\php.exe composer.phar install
        )
    ) else (
        echo [ERRO] PHP nao encontrado! Por favor, instale o XAMPP.
        pause
        exit /b 1
    )
)

echo.
echo [3/6] Configurando arquivo .env do backend...
if not exist ".env" (
    copy .env.example .env
    echo Arquivo .env criado!
)

echo.
echo [4/6] Gerando chave da aplicacao...
if exist "C:\xampp\php\php.exe" (
    C:\xampp\php\php.exe artisan key:generate
) else (
    php artisan key:generate
)

echo.
echo [5/6] Criando banco de dados...
echo.
echo IMPORTANTE: Certifique-se de que o MySQL/MariaDB esta rodando!
echo Voce pode iniciar pelo XAMPP Control Panel.
echo.
pause

if exist "C:\xampp\php\php.exe" (
    C:\xampp\php\php.exe artisan migrate --seed
) else (
    php artisan migrate --seed
)

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo [AVISO] Erro ao executar migrations!
    echo Verifique se o MySQL esta rodando e se as configuracoes do .env estao corretas.
    echo.
)

cd ..

echo.
echo [6/6] Criando arquivo .env do frontend...
if not exist ".env" (
    echo VITE_API_URL=http://localhost:8000/api/v1 > .env
    echo Arquivo .env do frontend criado!
)

echo.
echo ================================================
echo   Setup Concluido!
echo ================================================
echo.
echo Proximos passos:
echo 1. Inicie o XAMPP e certifique-se de que MySQL esta rodando
echo 2. Execute 'start.bat' para iniciar o projeto completo
echo    OU
echo    Execute 'start-backend.bat' e 'start-frontend.bat' separadamente
echo.
echo ================================================
pause
