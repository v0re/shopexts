<?
//'web'��sqlserver��mydb������Դ����'admin'�Ƿ���mydb���û�����'123456'�Ƿ���mydb������
$cnx = odbc_connect('web', 'admin', '123456');
//��sql server��mydb���ݿ��user��
$cur= odbc_exec( $cnx, 'select *  from user' );
$num_row=0;
// ����mysql
$conn=mysql_pconnect("localhost","root","123456");
//��mysql��mydb���ݿ�
@mysql_select_db('mydb',$conn) or die("�޷����ӵ����ݿ⣬�������Ա��ϵ��");
//��sql server��mydb���е�user������ȡ�����ݣ���������ݽ���ѡ�񣬿���ǰ���select����м��������ж�
while( odbc_fetch_row( $cur ))            
{
  $num_row++;
  // ����Ĳ���i(1,2,3..)ָ���Ǽ�¼���еĵ�i�������������ѡ��ؽ���ѡȡ��fieldi�õ���Ӧ���ֵ��Ȼ������Զ�fieldi���в���
  $field1 = odbc_result( $cur, 1 );   
  $field2 = odbc_result( $cur, 2 );   
  $field3 = odbc_result( $cur, 3 );   
  $field4 = odbc_result( $cur, 4 );   
  $field5 = odbc_result( $cur, 5 );   
  $field6 = odbc_result( $cur, 6 );
  //�����Ƕ�sql server�е�datetime���͵��ֶν�����Ӧת������ת����������Ҫ��int��   
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
