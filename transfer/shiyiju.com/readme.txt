一、需要处理的表

sdb_members		会员信息表

sdb_member_addrs	会员地址

sdb_advance_logs	预付款记录

sdb_orders		订单信息表

sdb_order_items		订单商品信息表

sdb_message		客户留言表


二、避免自增id重叠的处理方法：

	1. sdb_members:		member_id

	CNC中的sdb_members的最大的member_id是541，TEL中sdb_members中最小的member_id是5，综合下来，TEL插入到CNC的sdb_member中，member_id变更策略是：
		
		$member_id = intval($member_id) + 600;

	2. sdb_orders:		order_id,member_id

	避免订单表中的order_id重叠，电信的订单数据在插入网通数据前order_id的第三位要变成1,member_id跟sdb_members一致
		
		$member_id = intval($member_id) + 600;
		$order_id{11} = 1;



	3. sdb_advance_logs:	member_id

	member_id跟sdb_members一致
	
		$member_id = intval($member_id) + 600;


	4. sdb_member_addrs:	member_id

	member_id跟sdb_members一致
	
		$member_id = intval($member_id) + 600;








