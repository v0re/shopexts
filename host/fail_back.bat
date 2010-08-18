@echo off
set url=http://bbs.green3c.com
curl -I  %url%  > result.txt
for /F %%i in ('findstr "200 OK" result.txt') do set X="%%i"

if %X%=="HTTP/1.1" (goto OK) else goto NO
:OK
    echo %X%
     goto END
 :NO
     net start apache2.2
    goto END
 :END
 set X=""

