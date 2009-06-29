<%
set conn=server.createobject("adodb.connection")
// 'web'是sqlserver中mydb的数据源名，'admin'是访问mydb的用户名，'123456'是访问mydb的密码
conn.open 'web', 'admin', '123456' 
set rs=server.createobject("adodb.recordset")
//这条sql语句实现了将datetime类型的recdate字段转化成unix时间戳的int型
sql="select ID,name,username,password,datediff(s,'1970-01-01 00:00:00',recdate)-8*3600,reid,filename,fileContentType,filevalue from senddate" 
rs.open sql,conn,1,3
set conn1=server.createobject("adodb.connection")
conn1.open "myoa","root","q1-d6=7?"
i=1
do while not rs.eof
  field1 = rs(0)   
  field2 = rs(1)   
  field3 = rs(2)   
  field4 = rs(3)   
  field5 = rs(4)   
  sql1 = "insert into user(ID,name,username,password,recdate)        

values("&field1&",'"&field2&"','"&field3&"','"&field4&"',"&field5&")" 

conn1.execute sql1
rs.movenext
i=i+1
loop
rs.close
set rs=nothing
conn.close
set conn=nothing
conn1.close
set conn1=Nothing


function makeattach(fileContentType,filevalue,i)
    select case fileContentType
        case "application/msword" 
            ext="doc"

        case "application/vnd.ms-excel"
            ext="exl"
            
        case "application/vnd.ms-powerpoint"
            ext="pps"
            
        case "application/x-rar-compressed"
            ext="rar"
            
        case "application/x-zip-compressed"
            ext="zip"
            
        case "image/gif"
            ext="gif"
            
        case "image/pjpeg"
            ext="jpg"
            
        case "text/plain"
            ext="txt"
            
        case else
            ext="x"
            
    end select
    if ext<>"x" then
        set fso=server.createobject("FileSystemObject")
        fName="attech"&i&"."&ext
        Dir="d:attach"
        If fso.FileExists(Dir & fName) Then fso.deletefile Dir & fName
        If fName<>"" AND NOT fso.FileExists(Dir & fName) Then
            Set strm1=Server.CreateObject("ADODB.Stream")
            strm1.Open
            strm1.Type=1 'Binary
            strm1.Write filevalue
            strm1.SaveToFile Dir & fName,2
            Set strm1=Nothing
        end if
        makeattach=fName
    end if
end function


%>
