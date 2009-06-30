<?php
	
	/**
	 * checkRepeat
	 * 作用:重命名表中某一字段的重复值
	 *
	 *
	 */
	function checkRepeatField()
	{
		$link=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD); 
		mysql_select_db(DB_NAME);
		mysql_query("set names utf8");

		$flag = 0;		//标志符,记录$sql结果中count( `bn` )的最大值
		$sql = 'SELECT `goods_id` , `bn` , count( `bn` )
				FROM `sdb_goods`
				GROUP BY `bn`
				HAVING count( `bn` ) >1';
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_row($result))
		{
			//print_r($row);
			//echo '<br />';

			//产生一个随机bn			
			$randBn = $row[1].mt_rand(100,999).chr(mt_rand(65,90));			
			//将重复的bn重命名
			$updateSql = "update `sdb_goods` set `bn`='".$randBn."' where goods_id='".$row[0]."'";
			
			if (mysql_query($updateSql))
			{
				echo '<span syle="color:red;">'.$row[1].'被更新为'.$randBn.'</span><br />';
				//保存每次结果中的最大值
				$flag = ($flag>=($row[2]))?$flag:($row[2]);
			}
		}
		if (strtoupper(substr(trim($sql),0,6)) == 'SELECT')
		{
			echo '<span style="color:red;">该结果影响了'.mysql_num_rows($result).'<br />';
		}
		else
		{
			echo '<span style="color:red;">该结果影响了'.mysql_affected_rows().'<br />';
		}
		
		//echo $flag.'<br />';
		//如果$flag的最大值>1 说明bn还有重复值,递归循环处理 
		if ($flag > 1)
		{
			checkRepeatField();
		}
		else
		{
			echo '去除重复bn完成';
		}		
	}

	set_time_limit(0);
	error_reporting(E_ALL^E_NOTICE);
	require_once("config.php");			//数据库的配置文件
	checkRepeatField();
?>