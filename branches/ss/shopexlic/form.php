<?php
include("function.php");

if('addNew' == $_POST['act']){
	addNew();
	exit;
}
if('editNc' == $_POST['act']){
	editNc();
	exit;
}

if('edit' == $_GET['act']){
	$Nid = intval($_GET['id']);
	$selInsert = "SELECT * from licNotice where id='$Nid'";
	$db = db();
	$selResult = sqlite_query($db,$selInsert);
	while ($notice = sqlite_fetch_array($selResult)) {
		$id = $notice['id'];
		$title = $notice['title'];
		$content = $notice['content'];
		$cretime = date("Y-m-d", $notice['cretime']);
		$startime = date("Y-m-d", $notice['startime']);
		$entime = date("Y-m-d", $notice['entime']);
	}
	$do = "editNc";
	echo $content;
}
if('add' == $_GET['act']){
	$id = "";
	$title = "";
	$content = "";
	$cretime = "";
	$startime = "";
	$entime = "";
	$do = "addNew";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>商派客户中心通知管理</title>
</head>

<body>
<div>
<a href="form.php?act=add">添加</a>
<a href="list.php">列表</a>
</div>

<form action="form.php" method="post" name="notice">
	<input name="act" type="hidden" value="<?=$do?>" />
	<input name="id" type="hidden" value="<?=$id?>" />
	<div>标&nbsp;&nbsp;&nbsp;&nbsp;题：<input name="title" type="text" maxlength="30" value="<?=$title?>" /></div>
	<div>开始时间：<input name="startime" type="text" maxlength="30" value="<?=$startime?>" /></div>
	<div>结束时间：<input name="entime" type="text" maxlength="30" value="<?=$entime?>" /></div>
	<div>内&nbsp;&nbsp;&nbsp;&nbsp;容：<textarea name="content" cols="30" rows="5"><?=$content?></textarea></div>
	<div>
		<input name="submit" type="submit" value="确定" />
		<input name="reset" type="reset" value="重置" /></div>
</form>
</body>
</html>