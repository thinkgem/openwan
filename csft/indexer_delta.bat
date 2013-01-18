@echo off

rem -----------------------------------------------------------------------
rem OpenWan full-text search service recreate indexer
rem $Id: indexer.bat 894 2010-03-23 04:20:17Z thinkgem $
rem -----------------------------------------------------------------------

%cd%\bin\indexer.exe --config %cd%\etc\csft.conf delta --rotate
%cd%\bin\indexer.exe --config %cd%\etc\csft.conf --merge main delta --merge-dst-range deleted 0 0 --rotate
@echo.
@echo Operation Successful
@echo.
pause