<?php
header("Content-type: text/html; charset=utf-8");
include("function.php");


if('del' == $_GET['act']){
	$Nid = intval($_GET['id']);
	delNc($Nid);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>商派客户中心通知管理</title>
</head>

<body>
<h1>商派客户中心通知管理</h1>
<div>
<a href="form.php?act=add">添加</a>
</div>

<div>
<table border="1" cellspacing="0" cellpadding="0" style="text-align:center; width:860px;">
	<tr>
		<td width="40">ID</td>
		<td>标题</td>
		<td width="120">创建时间</td>
		<td width="120">开始时间</td>
		<td width="120">结束时间</td>
		<td width="120">操作</td>
	</tr>
<?php
$db = db();
$result = sqlite_query($db,'select * from licNotice');
while ($notice = sqlite_fetch_array($result)) {
	$id = $notice['id'];
	$title = $notice['title'];
	$cretime = date("Y-m-d", $notice['cretime']);
	$startime = date("Y-m-d", $notice['startime']);
	$entime = date("Y-m-d", $notice['entime']);
?>
	<tr>
		<td><?=$id?></td>
		<td><a href="form.php?act=edit&id=<?=$id?>"><?=$title?></a></td>
		<td><?=$cretime?></td>
		<td><?=$startime?></td>
		<td><?=$entime?></td>
		<td><a href="list.php?act=del&id=<?=$id?>">删除</a>|<a href="form.php?act=edit&id=<?=$id?>">编辑</a>|<a href="notice.php?id=<?=$id?>" target="_blank">查看</a></td>
	</tr>
<?php
}
unset($id,$title,$cretime,$startime,$entime);
sqlite_close($db);
?>
</table>

</div>
</body>
</html>