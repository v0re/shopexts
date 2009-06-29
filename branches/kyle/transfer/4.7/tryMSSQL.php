<?
//'web'是sqlserver中mydb的数据源名，'admin'是访问mydb的用户名，'123456'是访问mydb的密码
$cnx = odbc_connect('web', 'admin', '123456');
//打开sql server中mydb数据库的user表
$cur= odbc_exec( $cnx, 'select *  from user' );
$num_row=0;
// 连接mysql
$conn=mysql_pconnect("localhost","root","123456");
//打开mysql的mydb数据库
@mysql_select_db('mydb',$conn) or die("无法连接到数据库，请与管理员联系！");
//从sql server的mydb库中的user表逐条取出数据，如果对数据进行选择，可在前面的select语句中加上条件判断
while( odbc_fetch_row( $cur ))            
{
  $num_row++;
  // 这里的参数i(1,2,3..)指的是记录集中的第i个域，你可以有所选择地进行选取，fieldi得到对应域的值，然后你可以对fieldi进行操作
  $field1 = odbc_result( $cur, 1 );   
  $field2 = odbc_result( $cur, 2 );   
  $field3 = odbc_result( $cur, 3 );   
  $field4 = odbc_result( $cur, 4 );   
  $field5 = odbc_result( $cur, 5 );   
  $field6 = odbc_result( $cur, 6 );
  //这里是对sql server中的datetime类型的字段进行相应转换处理，转换成我所需要的int型   
  $field5 = timetoint($field5);      
  $querystring = "insert into user(id,name,username,password,recdate) values('$field1','$field2','$field3','$field4','$field5')" ;
  mysql_query($querystring,$conn);
}

function timetoint($str){
  $arr1=split(" ",$str);
  $datestr=$arr1[0];
  $timestr=$arr1[1];
  $arr_date=split("-",$datestr);
  $arr_time=split(":",$timestr);
  $year=$arr_date[0];
  $month=$arr_date[1];
  $day=$arr_date[2];
  $hour=$arr_time[0];
  $minute=$arr_time[1];
  $second=$arr_time[2];
  $time_int=mktime($hour,$minute,$second,$month,$day,$year);
  return $time_int;
}
?>
