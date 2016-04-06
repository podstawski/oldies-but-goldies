@echo off

set APR_ICONV_PATH=win\iconv
set Path=%Path%;win
set cmd=%1
set file=%2/%3

if %cmd%==commit set cmd=-m a %cmd%
if %cmd%==update set file=%2

svn.exe --username %4 --password %5 --non-interactive  %cmd% %file% >nul 2>%4.err

type %4.err
del %4.err