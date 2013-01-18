@echo off

rem -----------------------------------------------------------------------
rem OpenWan full-text search service installer
rem $Id: install.bat 894 2010-03-23 04:20:17Z thinkgem $
rem -----------------------------------------------------------------------

sc query |find /i "searchd" >nul 2>nul
if not errorlevel 1 (goto exist) else goto notexist

:exist
@echo Full Text search service already exists
@echo.
pause
goto :eof

:notexist
@echo Configure Full Text Server
setlocal enabledelayedexpansion
set txt=%cd%
set txt=!txt:\=/!
call :replace %cd%\etc\csft.conf.in @CSFTDIR@ %txt% >%cd%\etc\csft.conf
%cd%\bin\searchd --config %cd%\etc\csft.conf --install
%cd%\bin\indexer.exe --config %cd%\etc\csft.conf main
net start searchd
@echo.
@echo Operation Successful
@echo.
pause
goto :eof

:replace
for /f "tokens=1* delims=:" %%i in ('findstr /n ".*" %1') do (
	set txt=%%j
	if "!txt!" == "" (
		echo.
	) else (
		echo !txt:%2=%3!
	)
)
goto :eof


