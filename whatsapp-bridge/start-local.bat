@echo off
cd /d "%~dp0"
echo Mindlytics WhatsApp Bridge (local)
echo Laravel on Hostinger connects to this via ngrok when deployed.
echo.
if not exist node_modules (
    echo Installing dependencies...
    call npm install
)
if not exist .env (
    echo Copy .env.example to .env and set API_TOKEN first.
    pause
    exit /b 1
)
echo Starting on http://127.0.0.1:3001
call npm start
