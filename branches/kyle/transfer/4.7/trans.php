<?php 
set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);
?>
<html>
<head>
<title>shopex��װ</title>
</head>
<body style="font-size:16px;">
<?php
	require_once("class.func.php");
	$func=new func();
	// ������Ʒ���༰��������
	$func->export_cat();
	$func->db->scroll("����������","ok");
	// ������ƷƷ��
	$func->export_brand();
	$func->db->scroll("������ƷƷ�����","ok");
	// ������Ʒ
	$func->export_goods();
	$func->db->scroll("������Ʒ���","ok");
	//����ע���û�
	$func->export_users();
	$func->db->scroll("�����û����","ok");
	//��������
	$func->export_article();
	$func->db->scroll("�����������","ok");
	//������
	//$func->export_orders();
	//������Դ
	$func->gc();
?>
</body>
</html>