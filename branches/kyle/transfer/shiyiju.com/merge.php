<?php

mysql_connect('localhost','root','shopex');
mysql_select_db('gongyipinnew');

//生成会员数据
//dumpTable('sdb_members');
//echo "member data dumped<br>";
//flush();

//生成预付款纪录数据
//dumpTableCraped('sdb_advance_logs','select log_id,member_id,money,mtime from sdb_advance_logs');
dumpTable('sdb_advance_logs');
echo "sdb_advance_logs dumped<br>";
flush();

mysql_query('set names utf8');


//生成留言板数据
dumpTable('sdb_message');
echo "sdb_message dumped<br>";
flush();

//dumpTable('sdb_order_items');
//echo "order items dumped<br>";
//flush();

dumpTable('sdb_orders');
echo "sdb_orders dumped<br>";
flush();

//生成留言板数据
dumpTable('sdb_member_addrs');
echo "sdb_member_addrs dumped<br>";
//flush();

mysql_close();

/////////////////////////////////////////////////////////////////////////////////////
echo "<hr>";
//改变数据库
mysql_connect('localhost','root','shopex');
mysql_select_db('shiyijucnc');
mysql_query('set names utf8');

//
insertTable('sdb_members');
echo "insert table sdb_members ok<br>";
flush();
//
insertTable('sdb_orders');
echo "insert table sdb_orders ok<br>";
flush();
//
insertTable('sdb_order_items');
echo "insert table sdb_order_items ok<br>";
flush();
//
insertTable('sdb_message');
echo "insert table sdb_message ok<br>";
flush();
//
insertTable('sdb_member_addrs');
echo "insert table sdb_member_addrs ok<br>";
flush();
//
insertTable('sdb_advance_logs');
echo "insert table sdb_advance_logs ok<br>";
flush();


//////////////////////////////////////////////////////////////////////////////////////
echo "<br><hr>all done!";


//将结果集存储为文件，数据搬迁
function dumpTable($tblname){
	if(file_exists($tblname)) unlink($tblname);
	
	$sql = "select * from $tblname";
	$rs = mysql_query($sql);
	while($row = mysql_fetch_array($rs,MYSQL_ASSOC)){
		$table[] = $row;
	}
	
	$data = serialize($table);
	error_log($data,3,$tblname);

	return true;
}

function dumpTableCraped($tblname,$sql){
	if(file_exists($tblname)) unlink($tblname);

	$rs = mysql_query($sql);
	while($row = mysql_fetch_array($rs,MYSQL_ASSOC)){
		$table[] = $row;
	}
	
	$data = serialize($table);
	error_log($data,3,$tblname);

	return true;
}

//把数据插入目标表
function insertTable($tblname){
	//判断是否需要自增id
	if(in_array($tblname,array('sdb_members','sdb_orders'))){
		$autoid = false;
	}else{
		$autoid = true;
	}
	//获取数据
	$sTmp = file_get_contents($tblname);
	$aData = unserialize($sTmp);
	if(is_array($aData)){
		foreach($aData as $row){				
			//订单id号第三位设置为1
			if($row['order_id']){
				//订单编号+100
				$row['order_id']{11} = 1;
			}
			//会员id + 600，600这个数值根据CNC站点实际情况调整
			if($row['member_id']){
				$row['member_id'] = intval($row['member_id']) + 600;
			}
			//去掉第一个单元
			if($autoid){
				array_shift($row);
			}
			//生成insert语句
			$column = '(';
			$values = '(';
			foreach($row as $_k=>$_v){
					$column .= '`'.$_k.'`,';
					$values .= '\''.$_v.'\',';
			}
			$column = rtrim($column,',').")";
			$values = rtrim($values,',').")";
			$sql = "insert into $tblname $column values $values";
			//纪录重复的用户
			if(!mysql_query($sql) && $tblname == 'sdb_members'){
				$uname = $row['uname'];
				echo ++$uu." user $uname exist skip <br>";
				if($uid = getUIDbyUname($uname)){
					$aMemBad[$uname] = $uid;
				}				
			}			
		}//循环结束
		//纪录重复的用户
		if($tblname == 'sdb_members'){
			if(file_exists('membad')) unlink('membad');
			file_put_contents('membad',serialize($aMemBad));
		}

	}else{
		var_export($sTmp);
		die('unserialize failed!');
	}

	return true;

}

function getUIDbyUname($uname){
	$sql = "select member_id from sdb_members where uname='{$uname}'";
	$rs = mysql_query($sql);
	if($row = mysql_fetch_array($rs)){
		return $row['member_id'];
	}else{
		mysql_error();
		die();
	}
}



?>