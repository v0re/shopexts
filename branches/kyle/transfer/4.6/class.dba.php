<?php
	require_once("class.msg.php");
	
	class dba extends msg
	{
		//数据库链接句柄
		var $link;
		
		/** 
		* Private 
		* $query stores a query resource 
		*/ 
		var  $rs;  
		var	$innerrs;
		
		/**
		* Private
		* $row stores a query result
		*/
		var  $row;
		
		/**
		* Private
		* $talbe stores a query set
		*/
		var  $table;
		
		/**
		* Private
		* $field stores a field set of a spacify table
		*/
		var  $field;
		
	
		
		function dba($host='localhost',$user='user',$pass='usst6103',$dbname='ahgift')
		{
			//检查操作系统	
			if(substr(PHP_OS, 0, 3) != 'WIN') 
			{
				$this->err('抱歉，本程序目前只能在WINDOWS 操作系统下运行!');	
			}
			//创建到MySQL的持久连接
			$this->link=mysql_connect($host, $user, $pass) 
			or $this->err("MySQL 数据库连接错误，请检查数据库主机变量设置是否正确!");
			mysql_select_db($dbname) 
			or $this->err("选择 MySQL 数据库出错，请确认数据库 $dbname 是否存在!");				
		}
		
		function query($sql)
		{
			if(isset($sql))
			{				
				mysql_query("set names utf8");
				$this->rs=mysql_query($sql,$this->link)
				or $this->err("line:".__LINE__.mysql_error());
			}
			else
			{
				$this->err("查询字符串为空");
			}
		}

		function innerquery($sql)
		{
			if(isset($sql))
			{				
				mysql_query("set names utf8");
				$this->inneers=mysql_query($sql,$this->link)
				or $this->err("line:".__LINE__.mysql_error());
			}
			else
			{
				$this->err("查询字符串为空");
			}
		}
		
		function row($rs) 
		{ 
			if( $this->row=mysql_fetch_array($rs,MYSQL_ASSOC))
			{			
				return $this->row; 
			}
			else
			{
				return false;
			}					 
		}
		//! An accessor
		/**
		* Returns a associative array of a query set
		* @return  mixed
		*/
		function table($rs)
		{			
			while($this->row($rs))
			{
				$this->table[]=$this->row;
			}
			return $this->table;
		}
    
		//! An accessor
		/**
		* Returns the fields of a specify table
		* @param  $table specify table name
		* @param  $kicklist a list of fields would be del from total fields
		* @return Mixed array
		*/
		function fields($table,$kicklist=null)
		{
			$this->query("describe $table");
			while ($row=$this->row()) 
			{
				if(!in_array($row[Field],$kicklist))
				{
					$this->fields[]=$row[Field];
				}
			}
			return $this->fields;
		}

		function gc()
		{
			unset($this->rs);
			unset($this->innerrs);
			unset($this->row);
			unset($this->table);
			unset($this->fields);
			mysql_close($this->link);
		}
		
		/*
		//ACCESS的构造函数
		function dba($source)
		{
			//检查操作系统	
			if(substr(PHP_OS, 0, 3) != 'WIN') 
			{
				$this->err('抱歉，本程序目前只能在WINDOWS 操作系统下运行!');	
			}
			//检查文件	
			if(!file_exists($source)) 
			{
				$this->err("找不到ACCESS数据库文件".$source.",请检查数据库文件路径设置是否正确");
			}
			//创建链接到Access的COM
			$this->link=new COM ("ADODB.Connection") or die("创建COM失败");
			//$ADO="Provider=sqloledb;Data Source=localhost;Initial Catalog=myTest;User Id=sa;Password=sa;";
			$ADO="DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=" . realpath("data.mdb"); 
			$this->link->open($ADO);
		}
		*/
			
	}	
?>