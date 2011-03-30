<?php
/*
	该文件由 江东 创建，你可以修改和使用
	但是必须保留版权
	All Rights Reserved
	cn.cos@126.com
	http://www.homeun.com
	2003.9.15
*/

class Mysql
{
	

	var $result ;

	var $num	= 0;

	var $db		= "" ;
	
	var $table   = "";
	
	var $query  = "";

	var $conn   = "";
// 执行查询操作
	function Mysql($db=null)
	{
		

		if( $db == null )
		{

			$this->db = DB_NAME;
		}
		else
		{

			$this->db   = $db;

		}

		$this->conn     = @mysql_pconnect(DB_HOST,DB_USER,DB_PASSWORD)	or die("数据库连接失败");

		mysql_select_db($this->db);
		
	}

	function setTable($tb)
	{

		$this->table = $tb;	

	}
	function doQuery($query)
	{

		$this->result = mysql_query($query)
			or die("<div align='center' style='color:red;'><b>something wrong happend!<br/>Notice:</b>".mysql_error()."<br/>table name:".$this->db.".".$this->table."|".$query."</div>");
		
	}	

	function doUpdate($array,$condition)
	{

		while(list($key,$val) = each($array))

		{

			$sql = $sql;

			$sql = $sql.$key."='".$val."',";

		}

		$query = "UPDATE ".$this->table." set ".$sql."<cos_to_be_replaced>".$condition;
		
		$query = str_replace(",<cos_to_be_replaced>"," ",$query);
		
		$this->query = $query;
		
		$this->result = mysql_query($query)
			or die("<div align='center' style='color:red;'><b>something wrong happend!<br/>Notice:</b>".mysql_error()."<br />".$this->query."<br/>table name:".$this->db.".".$this->table."</div>");

	


	}
	/*
	根据数组插入表格
	*/
	function doInsert($array)
	{

		$i = 0;
		
		while(list($key,$val) = each($array))
		{

			$key_array[$i] = $key;
			
			$val_array[$i] = $val;
			
			$i++;
		}

		$total = $i;

		$i = 0;

		while($i < $total)
		{
			
			$j = $i + 1;
			
			if($key_array[$j]!="")
			
			$tmp1 .= $key_array[$i].",";
			
			else
				$tmp1 .= $key_array[$i];
			
			$i ++;
		}
		$i = 0;

		while( $i < $total )
		{
			
			$j = $i + 1;
			
			if($i != ($total-1))
			
			$tmp2 .= "'".$val_array[$i]."',";
			
			else
				$tmp2 .= "'".$val_array[$i]."'";
			
			$i ++;
		}
		
		$query = "INSERT INTO `".$this->table."` (".$tmp1.") VALUES (".$tmp2.")";
		//echo $query;
		
		$this->query = $query;
		
		$this->result = mysql_query($query)
			or die("<div align='center' style='color:red;'><b>something wrong happend!<br/>Notice:</b>".mysql_error()."<br/>table name:".$this->db.".".$this->table."</div>");
	}
	/*
	删除表格内容
	*/
	function doDelete($con)
	{
		
		$query		  = "DELETE FROM ".$this->table." ".$con;
		//echo $query." |||||<br /><br />";
		
		$this->result = mysql_query($query)
			or die("<div align='center' style='color:red;'><b>something wrong happend!<br/>Notice:</b>".mysql_error()."<br/>table name:".$this->db.".".$this->table."</div>");

		

	}
	/*
	取出查询涉及到的行数量
	*/
	function GetNum()
	{
		$this->num = mysql_num_rows($this->result);

		return $this->num;
	}
	function AffNum()
	{
		$this->num = mysql_affected_rows($this->result);

		return $this->num;
	}

    /*
	取出查询数据
	*/
	function GetData()
	{
		 $object = mysql_fetch_object($this->result);

		 return $object;
	}
	/*
	取出查询数据
	*/
	function GetArray()
	{
		 $object = mysql_fetch_array($this->result);

		 return $object;
	}
}
?>