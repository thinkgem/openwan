@echo off

rem -----------------------------------------------------------------------
rem OpenWan full-text search service uninstaller
rem $Id: uninstall.bat 893 2010-03-23 04:16:43Z thinkgem $
rem -----------------------------------------------------------------------


net stop searchd
%cd%\bin\searchd --delete
@echo.
@echo Operation Successful
@echo.
pause