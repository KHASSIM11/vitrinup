@echo off
:loop
for /f %%i in ('git rev-parse HEAD') do set NEW=%%i
if not "%OLD%"=="%NEW%" (
    if not "%OLD%"=="" (
        echo Nouveau commit detecte ! Deploy...
        git push origin main
        powershell -ExecutionPolicy Bypass -File deploy.ps1
    )
    set OLD=%NEW%
)
timeout /t 3 /nobreak >nul
goto loop