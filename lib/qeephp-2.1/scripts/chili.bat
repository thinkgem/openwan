@echo off
setlocal
set SCRIPT_DIR=%~dp0
php %SCRIPT_DIR%..\commands\cli\chili.php %CD% %*
