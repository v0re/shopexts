后台权限：
总后台管理路径：http://shop.gougo.cn/adgougo
用户名：shopex
密码：shopex51703080

我们的网站是商城系统，有多网店，现在我们已决定废弃他，只用shopID为“205”的网店，也就是cbv.cn,他的管理路径为：
http://shop.gougo.cn/service
用户名：xiang.zhang.cn@gmail.com
密码：shopex51703080


ftp用户名：shopex
ftp密码：shopex51703080
IP:222.73.164.233

数据库类型：MSSQL
登录名：eshop1201ss666
密码：nvcKH97hV^f9
数据库名：eshop
数据库IP：222.73.164.233

------------------------------------------------------------

注：以下信息是要导入的信息，不需导入的表结构未加说明，每一行冒号后的是该字段的说明，有不明白的请向我询问，如有字段不能导入，请及时告诉我。谢谢！

QQ:430059032
手机：13918393179 张翔

------------------------------------------------------------

用户信息

TABLE [manager_info]
	[managerID] [bigint] :会员ID
	[admin_name] [varchar] (50) ：用户名
	[admin_password] [varchar] (50) ：用户密码,md5加密
	[admin_email] [varchar] (50) ：用户邮件地址
	[admin_question] [varchar] (50) ：密码问题
	[admin_answer] [varchar] (50) ：密码答案
	[admin_count] [money] ：会员预付款
	[real_name] [varchar] (50) ：真实姓名
	[sex] [varchar] (50) ：性别
	[provinceID] [int] ：省ID，具体名称在表province中
	[cityID] [int] 市ID，具体名称在表city中
	[address] [varchar] (255)：地址
	[zipcode] [varchar] (50) ：邮编
	[phone] [varchar] (50) ：电话号码
	[mobile] [varchar] (50) ：手机
	[oicq] [varchar] (50) ：QQ
	[msn] [varchar] (50) ：MSN
	[other] [varchar] (50) ：其它联系方式
	[born] [varchar] (50) ：生日
	[certificate] [varchar] (50) ：证书类型
	[certificateNO] [varchar] (50) ：证书号码
	[jobtype] [varchar] (50)  ：职业
	[explain] [varchar] (2000) ：备注
	[act] [bit] :激活状态，1为激活，默认为激活，0为未激活
	[login_num] [int] :登录次数
	[end_login] [datetime] :最后登录时间
	[create_time] [datetime] :注册时间


------------------------------------------------------------

商品部分注意：我只要转移数据库中shopID为205的商品，其它商品是别人在我们平台开的网店中的商品，不需要转。

商品
TABLE [goods_info]
	[goodsID] [int] ：商品ID
	[shopID] [int] :本项不需要转，where shopID=205的商品才需要转过来
	[goodsNO] [varchar] (50) ：商品编号
	[goods_name] [varchar] (255) ：商品名称
	[units] [varchar] (50) ：商品单位
	[common_price] [money] ：市场价
	[member_price] [money] ：会员价
	[brand] [varchar] (50) ：品牌名称
	[spec] [varchar] (2000) ：规格
	[sales_promotion] [varchar] (2000) ：促销信息,请将此字段中的字符串“@#$”批量替换为空格
	[goods_desc] [varchar] (2000) ：商品简述
	[goods_explain] [text] ：商品描述,请将此字段中的字符串“@#$”批量替换为空格
	[after_service] [varchar] (2000) ：售后服务
	[typeNO] [varchar] (20) ：类别ID，请参阅下文“商品目录”部分
	[big_pic] [varchar] (50) :商品大图地址
	[small_pic] [varchar] (50) ：缩略图地址
	[mark_leave] [int] ：库存量
	[mark_comment] [bit] ：1设为推荐，0不推荐
	[mark_price] [bit] ：1设为特价，0不是特价
	[mark_new] [bit] ：1设为新品，0不是新品
	[click_num] [int] :点击数
	[buy_num] [int] ：购买数
	[create_time] [datetime] ：创建时间
	[show] [bit] 是否上架，1为上架，0为下架


------------------------------------------------------------

商品目录

TABLE [type_info] 
	[typeID] [int] :ID，没用了
	[shopID] [int] ：同上，只要shopID=205的数据
	[typeNO] [decimal](18, 0) ：分类ID,四位数和五位数的是大类，更长的是小类，小类的前四位数或前五位数和它的大类相同。
	[type_name] [varchar] (50) ：目录名称


------------------------------------------------------------
商品评论

TABLE [goods_comment] 
	[commentID] [int] ：ID，没用了
	[shopID] [int] :where shopID=205的商品才需要转过来
	[goodsID] [int] ：商品ID，对应商品表[goods_info]中的goodsid,用此来识别是哪个商品的评论
	[userID] [int] NULL ,
	[managerID] [int] ：评价人的ID，对应会员表[manager_info]中的managerID,如果有困难，都设为匿名评论也没关系
	[title] [varchar] (2000) ：评论标题
	[content] [varchar] (2000) ：评论内容
	[mark_post] [bit] ：是否发布,1为是
	[mark_answer] [bit] ：是否已回答,1为是
	[answer] [varchar] (1000) ：回答的内容
	[answer_time] [datetime] 回答时间
	[create_time] [datetime] 评论时间

------------------------------------------------------------
网店公告

TABLE [center_news_info] 
	[newsID] [int] ：ID，没用了
	[news_title] [varchar] (255) ：标题
	[news_desc] [varchar] (4000) ：内容
	[mark_post] [bit] ：只要该项为1的数据,本字段不需导入
	[typeid] [int] ：只要该项为0的数据,本字段不需导入
	[toid] [bigint] ：只要该项null的数据,本字段不需导入
	[create_time] [datetime] 公告时间

