һ����Ҫ����ı�

sdb_members		��Ա��Ϣ��

sdb_member_addrs	��Ա��ַ

sdb_advance_logs	Ԥ�����¼

sdb_orders		������Ϣ��

sdb_order_items		������Ʒ��Ϣ��

sdb_message		�ͻ����Ա�


������������id�ص��Ĵ�������

	1. sdb_members:		member_id

	CNC�е�sdb_members������member_id��541��TEL��sdb_members����С��member_id��5���ۺ�������TEL���뵽CNC��sdb_member�У�member_id��������ǣ�
		
		$member_id = intval($member_id) + 600;

	2. sdb_orders:		order_id,member_id

	���ⶩ�����е�order_id�ص������ŵĶ��������ڲ�����ͨ����ǰorder_id�ĵ���λҪ���1,member_id��sdb_membersһ��
		
		$member_id = intval($member_id) + 600;
		$order_id{11} = 1;



	3. sdb_advance_logs:	member_id

	member_id��sdb_membersһ��
	
		$member_id = intval($member_id) + 600;


	4. sdb_member_addrs:	member_id

	member_id��sdb_membersһ��
	
		$member_id = intval($member_id) + 600;








