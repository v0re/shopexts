<?php
$setting = array(
'errorpage.p404'=>array('type'=>SET_T_STR,'default'=>'对不起，无法找到您访问的页面，请返回重新访问。'),
'errorpage.p500'=>array('type'=>SET_T_STR,'default'=>'对不起，系统无法执行您的请求，请稍后重新访问。'),
'admin.dateFormat'=>array('type'=>SET_T_STR,'default'=>'Y-m-d'),
'admin.timeFormat'=>array('type'=>SET_T_STR,'default'=>'Y-m-d H:i:s'),
'cache.admin_tmpl'=>array('type'=>SET_T_STR,'default'=>HOME_DIR.'/cache/admin_tmpl'),
'cache.apc.enabled'=>array('type'=>SET_T_BOOL,'default'=>false),
'cache.front_tmpl'=>array('type'=>SET_T_STR,'default'=>HOME_DIR.'/cache/front_tmpl'),
'log.level'=>array('type'=>SET_T_INT,'default'=>3),
'log.path'=>array('type'=>SET_T_STR,'default'=>HOME_DIR.'/logs'),

'misc.53kf_account'=>array('type'=>SET_T_STR,'default'=>''),
'misc.53kf_adv'=>array('type'=>SET_T_STR,'default'=>''),
'misc.53kf_interval'=>array('type'=>SET_T_STR,'default'=>''),
'misc.53kf_open'=>array('type'=>SET_T_STR,'default'=>''),
'misc.53kf_style'=>array('type'=>SET_T_STR,'default'=>''),

'misc.forum_code'=>array('type'=>SET_T_STR,'default'=>''),
'misc.forum_key'=>array('type'=>SET_T_STR,'default'=>''),
'misc.forum_login_api'=>array('type'=>SET_T_STR,'default'=>''),
'misc.forum_url'=>array('type'=>SET_T_STR,'default'=>''),

'misc.im_alpha'=>array('type'=>SET_T_STR,'default'=>''),
'misc.im_hide'=>array('type'=>SET_T_STR,'default'=>''),
'misc.im_list'=>array('type'=>SET_T_STR,'default'=>''),
'misc.im_position'=>array('type'=>SET_T_STR,'default'=>''),
'misc.im_show_page'=>array('type'=>SET_T_STR,'default'=>''),

'point.get_policy'=>array('type'=>SET_T_STR,'default'=>''),
'point.get_rate'=>array('type'=>SET_T_STR,'default'=>''),
'point.refund_method'=>array('type'=>SET_T_STR,'default'=>''),
'point.set_commend'=>array('type'=>SET_T_STR,'default'=>''),
'point.set_commend_help'=>array('type'=>SET_T_STR,'default'=>''),
'point.set_commend_help_v'=>array('type'=>SET_T_STR,'default'=>''),
'point.set_commend_v'=>array('type'=>SET_T_STR,'default'=>''),
'point.set_coupon'=>array('type'=>SET_T_STR,'default'=>''),
'point.set_register'=>array('type'=>SET_T_STR,'default'=>''),
'point.set_register_v'=>array('type'=>SET_T_STR,'default'=>''),

'coupon.code.encrypt_len'=>array('type'=>SET_T_INT,'default'=>5),
'coupon.code.count_len'=>array('type'=>SET_T_INT,'default'=>5),
'coupon.mc.use_times'=>array('type'=>SET_T_INT,'default'=>1,'desc'=>'优惠券可用次数'),

'security.guest.enabled'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>'是否支持非会员购物'),
'shop.showGenerator'=>array('type'=>SET_T_BOOL,'default'=>true),
'site.version'=>array('type'=>SET_T_INT,'default'=>time(),'desc'=>'version的最后修改时间'),
'site.dateFormat'=>array('type'=>SET_T_STR,'default'=>'Y-m-d','desc'=>'商店日期格式'),
'site.timeFormat'=>array('type'=>SET_T_STR,'default'=>'Y-m-d H:i:s','desc'=>'商店日期时间格式'),
'site.coupon_order_limit'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'每张订单可用优惠券数量'),//WZP
'site.decimal_digit'=>array('type'=>SET_T_ENUM,'default'=>2,'desc'=>'订单金额取整位数','options'=>array(0=>'整数取整',1=>'取整到1位小数',2=>'取整到2位小数',3=>'取整到3位小数')),//WZP
'site.decimal_type'=>array('type'=>SET_T_ENUM,'default'=>0,'desc'=>'订单金额取整方式','options'=>array('0'=>'四舍五入','1'=>'向上取整','2'=>'向下取整')),//WZP
'site.delivery_time'=>array('type'=>SET_T_STR,'default'=>2,'desc'=>'默认备货时间'),//WZP
'site.goods_property'=>array('type'=>SET_T_STR,'default'=>''),//WZP
'site.index_hot_num'=>array('type'=>SET_T_STR,'default'=>''),
'site.index_new_num'=>array('type'=>SET_T_STR,'default'=>''),
'site.index_recommend_num'=>array('type'=>SET_T_STR,'default'=>''),
'site.index_special_num'=>array('type'=>SET_T_STR,'default'=>''),
'site.show_mark_price'=>array('type'=>SET_T_BOOL,'default'=>'true','desc'=>'前台是否显示市场价'),//WZP
'site.show_storage'=>array('type'=>SET_T_BOOL,'default'=>'false'),

'site.login_valide'=>array('type'=>SET_T_BOOL,'default'=>'true','desc'=>'会员登录需输入验证码'),//WZP
'site.register_valide'=>array('type'=>SET_T_BOOL,'default'=>'true','desc'=>'会员注册需输入验证码'),
'site.buy.target'=>array('type'=>SET_T_ENUM,'default'=>'2','options'=>array('2'=>'弹出购物车页面','1'=>'本页跳转到购物车页面','3'=>'只将商品加入购物车，不跳转',
//'4'=>'询问用户'
),'desc'=>'顾客点击商品购买按钮后'),//Ever
'site.market_price'=>array('type'=>SET_T_ENUM,'default'=>'1','desc'=>'商品页是否显示市场价','options'=>array('1'=>'×','2'=>'+')),
'site.market_rate'=>array('type'=>SET_T_STR,'default'=>'1.2','desc'=>'请输入比例值或增额值'),
'site.save_price'=>array('type'=>SET_T_ENUM,'default'=>'1','desc'=>'商品页是否显示节省金额','options'=>array('0'=>'否','1'=>'显示节省的金额','2'=>'显示百分比','3'=>'显示折扣')),
'site.retail_member_price_display'=>array('type'=>SET_T_ENUM,'default'=>'0','desc'=>'零售会员价显示设定','options'=>array('0'=>'显示当前会员等级价格','1'=>'显示零售会员所有等级价格','2'=>'显示批发会员所有等级价格','3'=>'显示所有会员等级价格')),//guzhengxiao
'site.wholesale_member_price_display'=>array('type'=>SET_T_ENUM,'default'=>'0','desc'=>'批发会员价显示设定','options'=>array('0'=>'显示当前会员等级价格','1'=>'显示零售会员所有等级价格','2'=>'显示批发会员所有等级价格','3'=>'显示所有会员等级价格')),//guzhengxiao
'site.meta_desc'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>'META_DESCRIPTION<br />(页面描述)'),//WZP
'site.meta_key_words'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'META_KEYWORDS<br />(页面关键词)'),//WZP
'site.order_storage'=>array('type'=>SET_T_ENUM,'default'=>'','options'=>array('0'=>'订单发货后扣除库存','1'=>'订单生成立即扣库存'),'desc'=>'库存扣除方式'),//WZP
'site.offline_pay'=>array('type'=>SET_T_BOOL,'default'=>'true','desc'=>'支持线下支付方式'),//WZP
'site.searchlist_num'=>array('type'=>SET_T_STR,'default'=>''),
'site.shopex_certify'=>array('type'=>SET_T_ENUM,'options'=>array('0'=>'显示在底部','1'=>'显示在左侧'),'default'=>'','desc'=>'ShopEx Store 认证显示'),//WZP

'site.tax_ratio'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'税率'),//WZP
'site.trigger_tax'=>array('type'=>SET_T_BOOL,'default'=>'','desc'=>'是否设置含税价格'),//WZP
'site.copyright'=>array('type'=>SET_T_TXT,'default'=>'copyright &copy; 2008','desc'=>'版权信息'),//WZP
'site.logo'=>array('type'=>SET_T_FILE,'default'=>'images/default/default_logo.png|default/default_logo.png|fs_storage','desc'=>'商店Logo','backend'=>'public'),
'site.certtext'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'备案号'),
'site.cert'=>array('type'=>SET_T_FILE,'default'=>'cert/bazs.cert|bazs.cert|fs_storage','desc'=>'备案证书'),
'site.homepage.tmpl_name'=>array('type'=>SET_T_STR,'default'=>'1-column'),
'site.thumbnail_pic_height'=>array('type'=>SET_T_INT,'default'=>80,'desc'=>'缩略图高度'),
'site.thumbnail_pic_width'=>array('type'=>SET_T_INT,'default'=>110,'desc'=>'缩略图宽度'),
'site.small_pic_height'=>array('type'=>SET_T_INT,'default'=>300,'desc'=>''),
'site.small_pic_width'=>array('type'=>SET_T_INT,'default'=>300,'desc'=>''),
'site.big_pic_height'=>array('type'=>SET_T_INT,'default'=>600,'desc'=>''),
'site.big_pic_width'=>array('type'=>SET_T_INT,'default'=>600,'desc'=>''),
'site.default_thumbnail_pic'=>array('type'=>SET_T_STR,'default'=>'images/default/default_thumbnail_pic.gif|default/default_thumbnail_pic.gif|fs_storage','desc'=>''),
'site.default_small_pic'=>array('type'=>SET_T_STR,'default'=>'images/default/default_small_pic.gif|default/default_small_pic.gif|fs_storage','desc'=>''),
'site.default_big_pic'=>array('type'=>SET_T_STR,'default'=>'images/default/default_big_pic.gif|default/default_big_pic.gif|fs_storage','desc'=>''),
'site.watermark.wm_small_enable'=>array('type'=>SET_T_INT,'default'=>0,'desc'=>''),
'site.watermark.wm_small_loc'=>array('type'=>SET_T_INT,'default'=>1,'desc'=>''),
'site.watermark.wm_small_text'=>array('type'=>SET_T_STR,'default'=>'','desc'=>''),
'site.watermark.wm_small_font'=>array('type'=>SET_T_STR,'default'=>'','desc'=>''),
'site.watermark.wm_small_font_size'=>array('type'=>SET_T_INT,'default'=>10,'desc'=>''),
'site.watermark.wm_small_font_color'=>array('type'=>SET_T_STR,'default'=>'#000000','desc'=>''),
'site.watermark.wm_small_pic'=>array('type'=>SET_T_STR,'default'=>'images/default/wm_big_pic.gif|default_big_pic.gif|fs_storage','desc'=>''),
'site.watermark.wm_small_transition'=>array('type'=>SET_T_INT,'default'=>100,'desc'=>''),
'site.watermark.wm_big_enable'=>array('type'=>SET_T_INT,'default'=>0,'desc'=>''),
'site.watermark.wm_big_loc'=>array('type'=>SET_T_INT,'default'=>0,'desc'=>''),
'site.watermark.wm_big_font'=>array('type'=>SET_T_STR,'default'=>'','desc'=>''),
'site.watermark.wm_big_text'=>array('type'=>SET_T_STR,'default'=>'','desc'=>''),
'site.watermark.wm_big_font_size'=>array('type'=>SET_T_INT,'default'=>10,'desc'=>''),
'site.watermark.wm_big_font_color'=>array('type'=>SET_T_STR,'default'=>'#000000','desc'=>''),
'site.watermark.wm_big_pic'=>array('type'=>SET_T_STR,'default'=>'images/default/wm_big_pic.gif|default/wm_big_pic.gif|fs_storage','desc'=>''),
'site.watermark.wm_big_transition'=>array('type'=>SET_T_INT,'default'=>100,'desc'=>''),
'system.money.operation.decimals'=>array('type'=>SET_T_ENUM,'default'=>2,'desc'=>'前台价格精确到','options'=>array(0=>'无小数位',1=>'1位小数',2=>'2位小数',3=>'3位小数'),'display'=>'false'),
'system.admin_verycode'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>'管理员后台登陆启用验证码','display'=>'false'),
'store.address'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'联系地址'),
'store.certificate_num'=>array('type'=>SET_T_STR,'default'=>''),
'store.company_name'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'网站所有人'),
'store.contact'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'联系人'),
'store.email'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'电子邮件'),
'store.mobile_phone'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'手机'),
'store.shop_url'=>array('type'=>SET_T_STR,'default'=>'http://'.$_SERVER['HTTP_HOST'].substr(PHP_SELF, 0, strrpos(PHP_SELF, '/') + 1),'desc'=>'商店网址'),
'store.telephone'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'固定电话'),
'store.zip_code'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'邮政编码'),
'system.contact.email'=>array('type'=>SET_T_STR,'default'=>''),
'system.contact.mobile'=>array('type'=>SET_T_STR,'default'=>''),
'system.contact.name'=>array('type'=>SET_T_STR,'default'=>''),
'system.contact.phone'=>array('type'=>SET_T_STR,'default'=>''),
'system.clientdpi'=>array('type'=>SET_T_STR,'default'=>'96'),
'store.greencard'=>array('type'=>SET_T_BOOL,'default'=>true),

'system.mail_encode'=>array('type'=>SET_T_STR,'default'=>''),
'system.mail_lang'=>array('type'=>SET_T_STR,'default'=>''),
'system.money.dec_point'=>array('type'=>SET_T_STR,'default'=>'.'),
'system.money.decimals'=>array('type'=>SET_T_ENUM,'default'=>2,'desc'=>'订单金额显示位数','options'=>array(0=>'无小数位',1=>'1位小数',2=>'2位小数',3=>'3位小数')),
'system.money.operation.carryset'=>array('type'=>SET_T_ENUM,'default'=>0,'desc'=>'价格进位方式','options'=>array(0=>'四舍五入',1=>'向上取整',2=>'向下取整')),

'system.money.thousands_sep'=>array('type'=>SET_T_STR,'default'=>''),
'system.path.article'=>array('type'=>SET_T_STR,'default'=>''),
'system.category.showgoods'=>array('type'=>SET_T_ENUM,'default'=>0,'desc'=>'商品分类列表页显示设置','options'=>array('0'=>'显示该分类及下属子分类下所有商品','1'=>'仅显示本分类下商品')),//liujy
'system.product.alert.num'=>array('type'=>SET_T_INT,'default'=>0,'desc'=>'商品库存报警数量'),
'system.product.autobn.beginnum'=>array('type'=>SET_T_INT,'default'=>100),
'system.product.autobn.length'=>array('type'=>SET_T_INT,'default'=>6),
'system.product.autobn.prefix'=>array('type'=>SET_T_STR,'default'=>'PDT'),
'system.send_mail_method'=>array('type'=>SET_T_STR,'default'=>''),
'system.shoplang'=>array('type'=>SET_T_STR,'default'=>''),
'system.shopname'=>array('type'=>SET_T_STR,'default'=>'点此设置您商店的名称','desc'=>'商店名称'),
'system.shopurl'=>array('type'=>SET_T_STR,'default'=>''),

'system.index_title'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'TITLE(首页标题)','display'=>'false'),
'system.use_gzip'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>'商店页面启用gzip压缩输出'),
'system.admin_error_login_times'=>array('type'=>SET_T_INT,'default'=>0),
'system.admin_error_login_time'=>array('type'=>SET_T_INT,'default'=>0),
'system.use_cart'=>array('type'=>SET_T_BOOL,'default'=>true),
'system.seo.mklink'=>array('type'=>SET_T_STR,'default'=>'actmapper.getlink'),
'system.seo.parselink'=>array('type'=>SET_T_STR,'default'=>'actmapper.parse'),
'system.seo.emuStatic'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>'商店页面启用伪静态URL'),
'system.seo.noindex_catalog'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>'通知搜索引擎不索引目录页'),
'system.ui.current_theme'=>array('type'=>SET_T_STR,'default'=>'jmsy'),
'system.ui.webslice'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>'支持ie8的webslice特性'),
'system.backup.splitFile'=>array('type'=>SET_T_BOOL,'default'=>false),
'system.timezone.default'=>array('type'=>SET_T_INT,'default'=>8,'desc'=>'用户默认时区'),
//'ux.dragcart'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>'允许拖动加入购物车'),

'site.title_format'=>array('type'=>SET_T_STR,'default'=>'%1 $system.shopname$','desc'=>'网站标题格式'),
'site.stripHtml'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>'是否压缩html'),
'site.url.base'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'主站访问地址'),
'site.url.themeres'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'模板资源访问地址'),
'site.url.widgetsres'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'版块资源访问地址'),
//'site.url.mediaurl'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'商品、文章等图片资源访问地址'),
'goods.rate_nums'=>array('type'=>SET_T_INT,'default'=>'10','desc'=>'相关商品最大数量'),

'gallery.default_view'=>array('type'=>SET_T_ENUM,'default'=>'index','options'=>array('index'=>'图文混排','grid'=>'橱窗形式','text'=>'文字列表'),'desc'=>'商品列表默认展示方式'),

'system.fast_delivery_as_progress'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>'后台手工发货为"已发货"'),
'system.auto_delivery'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>'用户到款则自动发货'),
'system.auto_delivery_physical'=>array('type'=>SET_T_STR,'default'=>'no','desc'=>'用户到款自动发货时，实体商品如何处理(auto:发货为ready,no:不发货,yes:发货为progress)'),
'system.auto_use_advance'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>'自动使用预存款'),

'site.trigger_tax'=>array('type'=>SET_T_BOOL,'default'=>1,'desc'=>'是否设置含税价格'),//WZP


'search.show.range'=>array('type'=>SET_T_BOOL,'default'=>1,'desc'=>'搜索是否显示价格区间'),
'errorpage.searchempty'=>array('type'=>SET_T_STR,'default'=>'<h1 class="error" style="">非常抱歉，没有找到相关商品</h1>
        <p style="margin:15px 1em;"><strong>建议：</strong><br />适当缩短您的关键词或更改关键词后重新搜索，如：将 “索尼手机X1” 改为 “索尼+X1”</p>'),

'order.flow.payed'=>array('type'=>SET_T_INT,'default'=>1,'desc'=>'订单付款流程'),
'order.flow.consign'=>array('type'=>SET_T_INT,'default'=>1,'desc'=>'订单发货流程'),
'order.flow.refund'=>array('type'=>SET_T_INT,'default'=>1,'desc'=>'订单退款流程'),
'order.flow.reship'=>array('type'=>SET_T_INT,'default'=>1,'desc'=>'订单退货流程'),

'certificate.id'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>'ShopEx证书编号'),
'certificate.token'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>'ShopEx证书密钥'),
'certificate.str'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>'ShopEx证书身份说明'),
'certificate.formal'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>'ShopEx证书身份'),

'certificate.kft.cid'=>array('type'=>SET_T_STR,'default'=>'false','desc'=>'客服通公司id'),
'certificate.kft.style'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'客服通风格号'),
'certificate.kft.action'=>array('type'=>SET_T_STR,'default'=>'TOAPPLY','desc'=>'客服通动作'),
'certificate.kft.enable'=>array('type'=>SET_T_STR,'default'=>'TOAPPLY','desc'=>'客服通开关'),



'certificate.channel.url'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'渠道url'),
'certificate.channel.name'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'渠道商名'),
'certificate.channel.status'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>'渠道状态'),
'certificate.channel.service'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'渠道服务类型'),

'messenger.sms.config'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'短信sms签名'),

'shopex.wss.username'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'合作统计用户名'),
'shopex.wss.password'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'合作统计密码'),
'shopex.wss.enable'=>array('type'=>SET_T_INT,'default'=>'0','desc'=>'合作统计开关'),
'shopex.wss.show'=>array('type'=>SET_T_INT,'default'=>'0','desc'=>'合作统计前台开关'),
'shopex.wss.js'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>'合作统计js'),
'system.area_depth'=>array('type'=>SET_T_INT,'default'=>'3','desc'=>'地区级数'),

'comment.index.listnum'=>array('type'=>SET_T_STR,'default'=>4,'desc'=>'商品首页显示评论条数'),
'comment.list.listnum'=>array('type'=>SET_T_STR,'default'=>10,'desc'=>'评论列表页显示评论条数'),
'comment.switch.ask'=>array('type'=>SET_T_STR,'default'=>'on','desc'=>'商品询问开关'),
'comment.switch.discuss'=>array('type'=>SET_T_STR,'default'=>'on','desc'=>'商品评论开关'),
'comment.switch.buy'=>array('type'=>SET_T_STR,'default'=>'off','desc'=>'商品经验评论开关'),
'comment.display.ask'=>array('type'=>SET_T_STR,'default'=>'reply','desc'=>'商品评论(询问),回复显示'),
'comment.display.discuss'=>array('type'=>SET_T_STR,'default'=>'reply','desc'=>'商品评论(评论),回复显示'),
'comment.display.buy'=>array('type'=>SET_T_STR,'default'=>'reply','desc'=>'商品评论(经验),回复显示'),
'comment.power.ask'=>array('type'=>SET_T_STR,'default'=>'null','desc'=>'商品评论(询问),发布权限'),
'comment.power.discuss'=>array('type'=>SET_T_STR,'default'=>'member','desc'=>'商品评论(评论),发布权限'),
'comment.power.buy'=>array('type'=>SET_T_STR,'default'=>'buyer','desc'=>'商品评论(经验),发布权限'),
'comment.null_notice.ask'=>array('type'=>SET_T_STR,'default'=>'如果您对本商品有什么问题,请提问咨询!','desc'=>'没有咨询记录,提示文字'),
'comment.null_notice.discuss'=>array('type'=>SET_T_STR,'default'=>'如果您对本商品有什么评价或经验,欢迎分享!','desc'=>'商品评论(经验),发布权限'),
'comment.null_notice.buy'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'商品评论(经验),发布权限'),
'comment.submit_display_notice.ask'=>array('type'=>SET_T_STR,'default'=>'您的问题已经提交成功!','desc'=>'没有咨询记录,提示文字'),
'comment.submit_hidden_notice.ask'=>array('type'=>SET_T_STR,'default'=>'您的问题已经提交成功,管理员会尽快回复!','desc'=>'商品评论(经验),发布权限'),
'comment.submit_display_notice.discuss'=>array('type'=>SET_T_STR,'default'=>'感谢您的分享!','desc'=>'商品评论(经验),发布权限'),
'comment.submit_hidden_notice.discuss'=>array('type'=>SET_T_STR,'default'=>'感谢您的分享,管理员审核后会自动显示!','desc'=>'没有咨询记录,提示文字'),
'comment.submit_display_notice.buy'=>array('type'=>SET_T_STR,'default'=>'如果您对本商品有什么问题,请提问咨询!','desc'=>'商品评论(经验),发布权限'),
'comment.submit_hidden_notice.buy'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'商品评论(经验),发布权限'),

'selllog.display.switch'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>'是否显示销售记录'),
'selllog.display.limit'=>array('type'=>SET_T_INT,'default'=>'3','desc'=>'低于多少条不显示销售记录'),
'selllog.display.listnum'=>array('type'=>SET_T_INT,'default'=>'10','desc'=>'显示条数'),
'goodsbn.display.switch'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>'是否启用商品编号'),
'goodsprop.display.position'=>array('type'=>SET_T_ENUM,'options'=>array('1'=>'仅商品价格上方','2'=>'仅商品详情中','0'=>'两处同时显示')),
'system.location'=>array('type'=>SET_T_STR,'default'=>'mainland'),

'plugin.passport.config.current_use'=>array('type'=>SET_T_STR,'default'=>'', 'desc'=>'当前使用的passport'),
//'store.gtype.status'=>array('type'=>SET_T_ENUM,'default'=>0,'desc'=>'商店是否有自定义商品类型','options'=>array('0'=>'否','1'=>'是')),//liujy

'system.message.open'=>array('type'=>SET_T_ENUM,'default'=>'off','desc'=>'商店留言发布','options'=>array('on'=>'会员提交后立即发布','off'=>'管理员回复后发布')),

'service.wltx'=>array('type'=>SET_T_STR,'default'=>''),

'site.refer_timeout'=>array('type'=>SET_T_INT,'default'=>15,'desc'=>'推荐链接过期时间（天）'),
'site.is_open_return_product'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>'是否开启退货功能'),
'spec.image.height'=>array('type'=>SET_T_INT,'default'=>30,'desc'=>'规格图片宽度'),
'spec.image.width'=>array('type'=>SET_T_INT,'default'=>30,'desc'=>'规格图片高度'),
'spec.default.pic'=>array('type'=>SET_T_STR,'default'=>'images//default/spec_def.gif|//default/spec_def.gif|fs_storage','desc'=>'规格默认图片'),
'system.editortype'=>array('type'=>SET_T_BOOL,'default'=>'true','desc'=>'HTML编辑器设置'),

'system.upload.limit'=>array('type'=>SET_T_ENUM,'default'=>0,'desc'=>'前台图片大小限定','options'=>array(0=>'500k',1=>'1M',2=>'2M',3=>'3M',4=>'5M',5=>'无限制'),'display'=>'false'),

'system.guide' => array('type'=>SET_T_STR,'default'=>'', 'desc'=>'向导设置'),
'goodsprop.display.switch'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>'是否启用商品属性链接'),
);
?>
