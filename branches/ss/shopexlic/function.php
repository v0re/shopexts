<?php
function db($dbname='lic.db'){
	$db = @sqlite_open($dbname);
	if(!$db){
		echo "Connection Sqlite Failed.";
		exit;
	}else{
		return $db;
	}
}

function safeVar(&$data){
	if (is_array($data)){
		foreach ($data as $key => $value){
			safeVar($data[$key]);
		}
	}else{
		$data = addslashes($data);
	}
}

function unSafeVar(&$data)
{
    if (is_array($data)){
        foreach ($data as $key => $value){
            unSafeVar($data[$key]);
        }
    }else{
        $data = stripslashes($data);
    }
}

function addNew(){
	if (!get_magic_quotes_gpc()){
		safeVar($_POST);
	}
	$cretime = time();
	$title = $_POST['title'];
	$content = $_POST['content'];
	$startime = strtotime($_POST['startime']);
	$entime = strtotime($_POST['entime']);
	
	$insertSql = "INSERT INTO licNotice ('title','content','cretime','startime','entime') 
	VALUES (\"$title\",\"$content\",$cretime,$startime,$entime)";

	//echo $insertSql;

	$db = db();
	sqlite_query($db,$insertSql);
	sqlite_close(&$db);
	header("location: list.php");
}

function editNc(){
	unSafeVar($_POST);
	$Nid = $_POST['id'];
	$title = $_POST['title'];
	$content = $_POST['content'];
	$startime = strtotime($_POST['startime']);
	$entime = strtotime($_POST['entime']);

	$updateSql = "UPDATE licNotice set 'title'=\"$title\",'content'=\"$content\",'startime'=$startime,'entime'=$entime  where id='$Nid'";

	//echo $updateSql;
	//exit;

	$db = db();
	sqlite_query($db,$updateSql);
	sqlite_close(&$db);
	header("location: list.php");
}

function delNc($Nid){
	$delInsert = "delete from licNotice where id='$Nid'";
	$db = db();
	sqlite_query($db,$delInsert);
	sqlite_close(&$db);
	header("location: list.php");
}
?>