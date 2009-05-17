<?php

define(lnbr,"\r\n");

require_once("../include/mall_config.php");
$link=mysql_connect($dbHost,$dbUser,$dbPass);
$dbName = 'ygd527';
mysql_select_db($dbName);
mysql_query("set names utf8");

echo "Start.....<br>";
mysql_dump($dbName);
echo "<br>Done!";




function mysql_dump($database) {

	$savedir = "./".date("YmdHis",time())."/";
	mkdir($savedir,0777);

	$tables = @mysql_list_tables($database);
	while ($row = @mysql_fetch_row($tables)) { 
		$table_list[] = $row[0]; 
	}
	
	for ($i = 0; $i < @count($table_list); $i++) {
		$query = '';
		$tbname = $table_list[$i];
		$results = mysql_query('DESCRIBE ' . $database . '.' . $tbname);
		$query .= 'DROP TABLE IF EXISTS `' . $database . '.' . $tbname . '`;' . lnbr;
		$query .= lnbr . 'CREATE TABLE `' . $database . '.' . $tbname . '` (' . lnbr;
		$tmp = '';
		
		while ($row = @mysql_fetch_assoc($results)) {
			$query .= '`' . $row['Field'] . '` ' . $row['Type'];
			if ($row['Null'] != 'YES') { 
				$query .= ' NOT NULL'; 
			}
			if ($row['Default'] != '') { 
				$query .= ' DEFAULT \'' . $row['Default'] . '\''; 
			}
			if ($row['Extra']) { 
				$query .= ' ' . strtoupper($row['Extra']); 
			}
			if ($row['Key'] == 'PRI') { 
				$tmp = 'primary key(' . $row['Field'] . ')'; 
			}

			$query .= ','. lnbr;
		}	

		$query .= $tmp . lnbr . ');' . str_repeat(lnbr, 2);
		$results = mysql_query('SELECT * FROM ' . $database . '.' . $tbname) or die(mysql_error());
		while ($row = @mysql_fetch_assoc($results)) {
			$query .= 'INSERT INTO `' . $database . '.' . $tbname .'` (';
			$data = Array();
			while (list($key, $value) = @each($row)) { 
				$data['keys'][] = $key; $data['values'][] = addslashes($value); 
			}
			$query .= join($data['keys'], ', ') . ')' . lnbr . 'VALUES (\'' . join($data['values'], '\', \'') . '\');' . lnbr;

		}

		$query .= str_repeat(lnbr, 2);
		error_log($query,3,$savedir.$tbname.".sql");
		echo "$tbname backup completed!<br>";
	}
}


?>