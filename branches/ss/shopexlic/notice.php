<?php
include('function.php');
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>商派通知</title>
</head>

<body>
<?=$title?>
<div>
<?php echo stripslashes($content); ?>
</div>
</body>
</html>
