@echo off
setlocal

set "MYSQL_BIN=C:\xampp\mysql\bin"
set "BACKUP_DIR=%~dp0backups"
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

for /f %%I in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd-HHmmss"') do set "STAMP=%%I"
set "BACKUP_FILE=%BACKUP_DIR%\bloomify-%STAMP%.sql"

"%MYSQL_BIN%\mysqldump.exe" --host=localhost --user=root --routines --events bloomify > "%BACKUP_FILE%"
if errorlevel 1 (
    echo Database backup failed.
    if exist "%BACKUP_FILE%" del "%BACKUP_FILE%"
    exit /b 1
)

echo Database backup created:
echo %BACKUP_FILE%
endlocal
