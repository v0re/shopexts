<?php 
set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);
?>
<html>
<head>
<title>shopex��װ</title>
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=gb2312">
</head>
<body style="font-size:16px;">
<?php
	require_once("funs.php");
	// ������Ʒ���༰��������
	export_cat();
	scroll("����������","ok");
	// ������ƷƷ��
	export_brand();
	scroll("������ƷƷ�����","ok");
	// ������Ʒ
	export_goods();
	scroll("������Ʒ���","ok");
	//����ע���û�
	export_users();
	scroll("�����û����","ok");
	//export_review();
	//scroll("������Ʒ�������","ok");
	//��������
	//export_article();
	//scroll("�����������","ok");
	//������
	//export_orders();
	//scroll("���������","ok");
	//��������Ʒ
	//export_order_items();
	//scroll("��������Ʒ���","ok");
	//������������
	//export_friendlink();
	//scroll("���������������","ok");
	//��������
	//export_guestbook();
	//scroll("�����������","ok");
?>
</body>
</html>