@echo off
echo ================================================
echo   Testando Conectividade da API
echo ================================================
echo.

echo Verificando se o backend esta rodando...
echo.

REM Tenta fazer uma requisição para o backend
curl -s http://localhost:8000/api/v1/health >nul 2>&1

if %ERRORLEVEL% EQU 0 (
    echo [OK] Backend esta respondendo!
    echo.
    curl http://localhost:8000/api/v1/health
) else (
    echo [ERRO] Backend nao esta respondendo em http://localhost:8000
    echo.
    echo Certifique-se de que o backend esta rodando.
    echo Execute: start-backend.bat
)

echo.
echo ================================================
pause
