<?php
	require_once("class.msg.php");
	
	class dba extends msg
	{
		//���ݿ����Ӿ��
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
			//������ϵͳ	
			if(substr(PHP_OS, 0, 3) != 'WIN') 
			{
				$this->err('��Ǹ��������Ŀǰֻ����WINDOWS ����ϵͳ������!');	
			}
			//������MySQL�ĳ־�����
			$this->link=mysql_connect($host, $user, $pass) 
			or $this->err("MySQL ���ݿ����Ӵ����������ݿ��������������Ƿ���ȷ!");
			mysql_select_db($dbname) 
			or $this->err("ѡ�� MySQL ���ݿ������ȷ�����ݿ� $dbname �Ƿ����!");				
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
				$this->err("��ѯ�ַ���Ϊ��");
			}
		}

		function innerquery($sql)
		{
			if(isset($sql))
			{				
				mysql_query("set names utf8");
				$this->innerrs=mysql_query($sql,$this->link)
				or $this->err("line:".__LINE__.mysql_error());
			}
			else
			{
				$this->err("��ѯ�ַ���Ϊ��");
			}
		}
		
		function num_rows($rs)
		{
			return mysql_num_rows($rs);
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
		//ACCESS�Ĺ��캯��
		function dba($source)
		{
			//������ϵͳ	
			if(substr(PHP_OS, 0, 3) != 'WIN') 
			{
				$this->err('��Ǹ��������Ŀǰֻ����WINDOWS ����ϵͳ������!');	
			}
			//����ļ�	
			if(!file_exists($source)) 
			{
				$this->err("�Ҳ���ACCESS���ݿ��ļ�".$source.",�������ݿ��ļ�·�������Ƿ���ȷ");
			}
			//�������ӵ�Access��COM
			$this->link=new COM ("ADODB.Connection") or die("����COMʧ��");
			//$ADO="Provider=sqloledb;Data Source=localhost;Initial Catalog=myTest;User Id=sa;Password=sa;";
			$ADO="DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=" . realpath("data.mdb"); 
			$this->link->open($ADO);
		}
		*/
			
	}	
?>