��̨Ȩ�ޣ�
�ܺ�̨����·����http://shop.gougo.cn/adgougo
�û�����shopex
���룺shopex51703080

���ǵ���վ���̳�ϵͳ���ж����꣬���������Ѿ�����������ֻ��shopIDΪ��205�������꣬Ҳ����cbv.cn,���Ĺ���·��Ϊ��
http://shop.gougo.cn/service
�û�����xiang.zhang.cn@gmail.com
���룺shopex51703080


ftp�û�����shopex
ftp���룺shopex51703080
IP:222.73.164.233

���ݿ����ͣ�MSSQL
��¼����eshop1201ss666
���룺nvcKH97hV^f9
���ݿ�����eshop
���ݿ�IP��222.73.164.233

------------------------------------------------------------

ע��������Ϣ��Ҫ�������Ϣ�����赼��ı�ṹδ��˵����ÿһ��ð�ź���Ǹ��ֶε�˵�����в����׵�������ѯ�ʣ������ֶβ��ܵ��룬�뼰ʱ�����ҡ�лл��

QQ:430059032
�ֻ���13918393179 ����

------------------------------------------------------------

�û���Ϣ

TABLE [manager_info]
	[managerID] [bigint] :��ԱID
	[admin_name] [varchar] (50) ���û���
	[admin_password] [varchar] (50) ���û�����,md5����
	[admin_email] [varchar] (50) ���û��ʼ���ַ
	[admin_question] [varchar] (50) ����������
	[admin_answer] [varchar] (50) �������
	[admin_count] [money] ����ԱԤ����
	[real_name] [varchar] (50) ����ʵ����
	[sex] [varchar] (50) ���Ա�
	[provinceID] [int] ��ʡID�����������ڱ�province��
	[cityID] [int] ��ID�����������ڱ�city��
	[address] [varchar] (255)����ַ
	[zipcode] [varchar] (50) ���ʱ�
	[phone] [varchar] (50) ���绰����
	[mobile] [varchar] (50) ���ֻ�
	[oicq] [varchar] (50) ��QQ
	[msn] [varchar] (50) ��MSN
	[other] [varchar] (50) ��������ϵ��ʽ
	[born] [varchar] (50) ������
	[certificate] [varchar] (50) ��֤������
	[certificateNO] [varchar] (50) ��֤�����
	[jobtype] [varchar] (50)  ��ְҵ
	[explain] [varchar] (2000) ����ע
	[act] [bit] :����״̬��1Ϊ���Ĭ��Ϊ���0Ϊδ����
	[login_num] [int] :��¼����
	[end_login] [datetime] :����¼ʱ��
	[create_time] [datetime] :ע��ʱ��


------------------------------------------------------------

��Ʒ����ע�⣺��ֻҪת�����ݿ���shopIDΪ205����Ʒ��������Ʒ�Ǳ���������ƽ̨���������е���Ʒ������Ҫת��

��Ʒ
TABLE [goods_info]
	[goodsID] [int] ����ƷID
	[shopID] [int] :�����Ҫת��where shopID=205����Ʒ����Ҫת����
	[goodsNO] [varchar] (50) ����Ʒ���
	[goods_name] [varchar] (255) ����Ʒ����
	[units] [varchar] (50) ����Ʒ��λ
	[common_price] [money] ���г���
	[member_price] [money] ����Ա��
	[brand] [varchar] (50) ��Ʒ������
	[spec] [varchar] (2000) �����
	[sales_promotion] [varchar] (2000) ��������Ϣ,�뽫���ֶ��е��ַ�����@#$�������滻Ϊ�ո�
	[goods_desc] [varchar] (2000) ����Ʒ����
	[goods_explain] [text] ����Ʒ����,�뽫���ֶ��е��ַ�����@#$�������滻Ϊ�ո�
	[after_service] [varchar] (2000) ���ۺ����
	[typeNO] [varchar] (20) �����ID����������ġ���ƷĿ¼������
	[big_pic] [varchar] (50) :��Ʒ��ͼ��ַ
	[small_pic] [varchar] (50) ������ͼ��ַ
	[mark_leave] [int] �������
	[mark_comment] [bit] ��1��Ϊ�Ƽ���0���Ƽ�
	[mark_price] [bit] ��1��Ϊ�ؼۣ�0�����ؼ�
	[mark_new] [bit] ��1��Ϊ��Ʒ��0������Ʒ
	[click_num] [int] :�����
	[buy_num] [int] ��������
	[create_time] [datetime] ������ʱ��
	[show] [bit] �Ƿ��ϼܣ�1Ϊ�ϼܣ�0Ϊ�¼�


------------------------------------------------------------

��ƷĿ¼

TABLE [type_info] 
	[typeID] [int] :ID��û����
	[shopID] [int] ��ͬ�ϣ�ֻҪshopID=205������
	[typeNO] [decimal](18, 0) ������ID,��λ������λ�����Ǵ��࣬��������С�࣬С���ǰ��λ����ǰ��λ�������Ĵ�����ͬ��
	[type_name] [varchar] (50) ��Ŀ¼����


------------------------------------------------------------
��Ʒ����

TABLE [goods_comment] 
	[commentID] [int] ��ID��û����
	[shopID] [int] :where shopID=205����Ʒ����Ҫת����
	[goodsID] [int] ����ƷID����Ӧ��Ʒ��[goods_info]�е�goodsid,�ô���ʶ�����ĸ���Ʒ������
	[userID] [int] NULL ,
	[managerID] [int] �������˵�ID����Ӧ��Ա��[manager_info]�е�managerID,��������ѣ�����Ϊ��������Ҳû��ϵ
	[title] [varchar] (2000) �����۱���
	[content] [varchar] (2000) ����������
	[mark_post] [bit] ���Ƿ񷢲�,1Ϊ��
	[mark_answer] [bit] ���Ƿ��ѻش�,1Ϊ��
	[answer] [varchar] (1000) ���ش������
	[answer_time] [datetime] �ش�ʱ��
	[create_time] [datetime] ����ʱ��

------------------------------------------------------------
���깫��

TABLE [center_news_info] 
	[newsID] [int] ��ID��û����
	[news_title] [varchar] (255) ������
	[news_desc] [varchar] (4000) ������
	[mark_post] [bit] ��ֻҪ����Ϊ1������,���ֶβ��赼��
	[typeid] [int] ��ֻҪ����Ϊ0������,���ֶβ��赼��
	[toid] [bigint] ��ֻҪ����null������,���ֶβ��赼��
	[create_time] [datetime] ����ʱ��

