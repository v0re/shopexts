@echo off
set url=http://localhost
curl -I  %url%  > result.txt
for /f %%i in (result.txt) do set X=%%i&goto TODO

:TODO
    if "%X%"=="HTTP/1.1" (goto OK) else goto NO
:OK
     goto END
:NO
    taskkill /F /IM  httpd.exe >nul 2>nul
    net start apache2.2
    tasklist > report.txt
    bin\blat.exe report.txt -to ken@shopex.cn -u helloxui@126.com -pw usst103 -subject "fail back report@"%date /T%
    goto END
:END

 set X=""

