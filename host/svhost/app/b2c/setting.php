<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$setting = array(
'errorpage.p404'=>array('type'=>SET_T_STR,'default'=>__('对不起，无法找到您访问的页面，请返回重新访问。')),
'errorpage.p500'=>array('type'=>SET_T_STR,'default'=>__('对不起，系统无法执行您的请求，请稍后重新访问。')),
'admin.dateFormat'=>array('type'=>SET_T_STR,'default'=>'Y-m-d'),
'admin.timeFormat'=>array('type'=>SET_T_STR,'default'=>'Y-m-d H:i:s'),
'cache.admin_tmpl'=>array('type'=>SET_T_STR,'default'=>DATA_DIR.'/cache/admin_tmpl'),
'cache.apc.enabled'=>array('type'=>SET_T_BOOL,'default'=>false),
'cache.front_tmpl'=>array('type'=>SET_T_STR,'default'=>DATA_DIR.'/cache/front_tmpl'),
'log.level'=>array('type'=>SET_T_INT,'default'=>3),
'log.path'=>array('type'=>SET_T_STR,'default'=>DATA_DIR.'/logs'),

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
'coupon.mc.use_times'=>array('type'=>SET_T_INT,'default'=>1,'desc'=>__('优惠券可用次数')),

'security.guest.enabled'=>array('type'=>SET_T_BOOL,'default'=>'true','desc'=>__('是否支持非会员购物')),
'shop.showGenerator'=>array('type'=>SET_T_BOOL,'default'=>true),
'site.version'=>array('type'=>SET_T_INT,'default'=>time(),'desc'=>__('version的最后修改时间')),
'site.coupon_order_limit'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('每张订单可用优惠券数量')),//WZP
'site.delivery_time'=>array('type'=>SET_T_STR,'default'=>2,'desc'=>__('默认备货时间')),//WZP
'site.goods_property'=>array('type'=>SET_T_STR,'default'=>''),//WZP
'site.index_hot_num'=>array('type'=>SET_T_STR,'default'=>''),
'site.index_new_num'=>array('type'=>SET_T_STR,'default'=>''),
'site.index_recommend_num'=>array('type'=>SET_T_STR,'default'=>''),
'site.index_special_num'=>array('type'=>SET_T_STR,'default'=>''),
'site.show_mark_price'=>array('type'=>SET_T_BOOL,'default'=>'true','desc'=>__('前台是否显示市场价')),//WZP
'site.show_storage'=>array('type'=>SET_T_BOOL,'default'=>'false','desc'=>__('是否显示库存')),

'site.login_valide'=>array('type'=>SET_T_BOOL,'default'=>'true','desc'=>__('会员登录需输入验证码')),//WZP
'site.login_type'=>array('type'=>SET_T_ENUM,'default'=>'dialog','options'=>array('href'=>__('跳转至登录页'),'dialog'=>__('弹出登录窗口')),'desc'=>__('顾客登录方式')),
'site.register_valide'=>array('type'=>SET_T_BOOL,'default'=>'true','desc'=>__('会员注册需输入验证码')),
'site.buy.target'=>array('type'=>SET_T_ENUM,'default'=>'3','options'=>array('2'=>__('弹出购物车页面'),'1'=>__('本页跳转到购物车页面'),'3'=>__('不跳转页面，直接加入购物车'),
//'4'=>'询问用户'
),'desc'=>__('顾客点击商品购买按钮后')),//Ever
'site.market_price'=>array('type'=>SET_T_ENUM,'default'=>'1','desc'=>__('如果不输入市场价，则'),'options'=>array('1'=>'×','2'=>'+')),
'site.market_rate'=>array('type'=>SET_T_STR,'default'=>'1.2','desc'=>__('')),
'site.save_price'=>array('type'=>SET_T_ENUM,'default'=>'1','desc'=>__('商品页是否显示节省金额'),'options'=>array('0'=>__('否'),'1'=>__('显示节省的金额'),'2'=>__('显示百分比'),'3'=>__('显示折扣'))),
'site.member_price_display'=>array('type'=>SET_T_ENUM,'default'=>'0','desc'=>__('会员价显示设定'),'options'=>array('0'=>__('显示当前会员等级价格'),'1'=>__('显示所有会员等级价格'))),//guzhengxiao
'site.meta_desc'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>__('META_DESCRIPTION<br />(页面描述)')),//WZP
'site.meta_key_words'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('META_KEYWORDS<br />(页面关键词)')),//WZP
'site.order_storage'=>array('type'=>SET_T_ENUM,'default'=>'','options'=>array('0'=>__('订单发货后扣除库存'),'1'=>__('订单生成立即扣库存')),'desc'=>__('库存扣除方式')),//WZP
'site.offline_pay'=>array('type'=>SET_T_BOOL,'default'=>'true','desc'=>__('支持线下支付方式')),//WZP
'site.searchlist_num'=>array('type'=>SET_T_STR,'default'=>''),
'site.b2c_certify'=>array('type'=>SET_T_ENUM,'options'=>array('0'=>__('显示在底部'),'1'=>__('显示在左侧')),'default'=>'','desc'=>__('ShopEx Store 认证显示')),//WZP

'site.tax_ratio'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('税率')),//WZP
'site.trigger_tax'=>array('type'=>SET_T_BOOL,'default'=>'','desc'=>__('是否设置含税价格')),//WZP
'site.copyright'=>array('type'=>SET_T_TXT,'default'=>'copyright &copy; 2008','desc'=>__('版权信息')),//WZP
'site.logo'=>array('type'=>SET_T_IMAGE,'default'=>'669f61c74cc8624dc1156939682aacd3','desc'=>__('商店Logo'),'backend'=>'public','width'=>200,'height'=>95),
'site.certtext'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('备案号')),
'site.cert'=>array('type'=>SET_T_FILE,'default'=>'cert/bazs.cert|bazs.cert|fs_storage','desc'=>__('备案证书')),
'site.homepage.tmpl_name'=>array('type'=>SET_T_STR,'default'=>'1-column'),
'site.thumbnail_pic_height'=>array('type'=>SET_T_INT,'default'=>80,'desc'=>__('缩略图高度')),
'site.thumbnail_pic_width'=>array('type'=>SET_T_INT,'default'=>110,'desc'=>__('缩略图宽度')),
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
'site.homepage_title'=>array('type'=>SET_T_STR,'default'=>'{ENV_shopname}','desc'=>__('TITLE(首页标题)'),'display'=>'false'),
'site.homepage_meta_key_words'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('META_KEYWORDS<br />(页面关键词)')),
'site.homepage_meta_desc'=>array('type'=>SET_T_TXT,'default'=>'{ENV_shopname}','desc'=>__('META_DESCRIPTION<br />(页面描述)')),
'site.goods_title'=>array('type'=>SET_T_STR,'default'=>'{ENV_goods_name}_{ENV_shopname}','desc'=>__('TITLE(首页标题)'),'display'=>'false'),
'site.goods_meta_key_words'=>array('type'=>SET_T_STR,'default'=>'{ENV_goods_kw}','desc'=>__('META_KEYWORDS<br />(页面关键词)')),
'site.goods_meta_desc'=>array('type'=>SET_T_TXT,'default'=>__('{ENV_goods_name}现价{ENV_goods_price};{ENV_goods_intro}'),'desc'=>__('META_DESCRIPTION<br />(页面描述)')),
'site.list_title'=>array('type'=>SET_T_STR,'default'=>'{ENV_path}_{ENV_goods_cat_p}_{ENV_shopname}','desc'=>__('TITLE(首页标题)'),'display'=>'false'),
'site.list_meta_key_words'=>array('type'=>SET_T_STR,'default'=>'{ENV_brand}','desc'=>__('META_KEYWORDS<br />(页面关键词)')),
'site.list_meta_desc'=>array('type'=>SET_T_TXT,'default'=>__('{ENV_path},{ENV_shopname}共找到{ENV_goods_amount}个商品'),'desc'=>__('META_DESCRIPTION<br />(页面描述)')),
'site.brand_index_title'=>array('type'=>SET_T_STR,'default'=>__('品牌专区_{ENV_shopname}'),'desc'=>__('TITLE(首页标题)'),'display'=>'false'),
'site.brand_index_meta_key_words'=>array('type'=>SET_T_STR,'default'=>'{ENV_brand}','desc'=>__('META_KEYWORDS<br />(页面关键词)')),
'site.brand_index_meta_desc'=>array('type'=>SET_T_TXT,'default'=>__('{ENV_shopname}提供{ENV_brand}等品牌的商品。'),'desc'=>__('META_DESCRIPTION<br />(页面描述)')),
'site.brand_list_title'=>array('type'=>SET_T_STR,'default'=>'{ENV_brand}_{ENV_shopname}','desc'=>__('TITLE(首页标题)'),'display'=>'false'),
'site.brand_list_meta_key_words'=>array('type'=>SET_T_STR,'default'=>'{ENV_brand_kw}','desc'=>__('META_KEYWORDS<br />(页面关键词)')),
'site.brand_list_meta_desc'=>array('type'=>SET_T_TXT,'default'=>'{ENV_brand_intro}','desc'=>__('META_DESCRIPTION<br />(页面描述)')),
'site.article_list_title'=>array('type'=>SET_T_STR,'default'=>'{ENV_article_cat}_{ENV_shopname}','desc'=>__('TITLE(首页标题)'),'display'=>'false'),
'site.article_list_meta_key_words'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('META_KEYWORDS<br />(页面关键词)')),
'site.article_list_meta_desc'=>array('type'=>SET_T_TXT,'default'=>'{ENV_shopname}{ENV_article_cat}','desc'=>__('META_DESCRIPTION<br />(页面描述)')),
'site.article_title'=>array('type'=>SET_T_STR,'default'=>'{ENV_article_title}_{ENV_shopname}{ENV_article_cat}','desc'=>__('TITLE(首页标题)'),'display'=>'false'),
'site.article_meta_key_words'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('META_KEYWORDS<br />(页面关键词)')),
'site.article_meta_desc'=>array('type'=>SET_T_TXT,'default'=>'{ENV_article_intro}','desc'=>__('META_DESCRIPTION<br />(页面描述)')),
'system.admin_verycode'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>__('管理员后台登陆启用验证码'),'display'=>'false'),
'store.address'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('联系地址')),
'store.certificate_num'=>array('type'=>SET_T_STR,'default'=>''),
'store.company_name'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('网站所有人')),
'store.contact'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('联系人')),
'store.email'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('电子邮件')),
'store.mobile_phone'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('手机')),
//'store.shop_url'=>array('type'=>SET_T_STR,'default'=>kernel::base_url(1),'desc'=>__('商店网址')),
'store.telephone'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('固定电话')),
'store.zip_code'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('邮政编码')),
'system.contact.email'=>array('type'=>SET_T_STR,'default'=>''),
'system.contact.mobile'=>array('type'=>SET_T_STR,'default'=>''),
'system.contact.name'=>array('type'=>SET_T_STR,'default'=>''),
'system.contact.phone'=>array('type'=>SET_T_STR,'default'=>''),
'system.clientdpi'=>array('type'=>SET_T_STR,'default'=>'96'),
'store.greencard'=>array('type'=>SET_T_BOOL,'default'=>true),

'system.mail_encode'=>array('type'=>SET_T_STR,'default'=>''),
'system.mail_lang'=>array('type'=>SET_T_STR,'default'=>''),
/*'system.money.operation.decimals'=>array('type'=>SET_T_ENUM,'default'=>2,'desc'=>__('前台价格精确到'),'options'=>array(0=>__('无小数位'),1=>__('1位小数'),2=>__('2位小数'),3=>__('3位小数')),'display'=>'false'),*/
'system.money.dec_point'=>array('type'=>SET_T_STR,'default'=>'.'),
'system.money.decimals'=>array('type'=>SET_T_ENUM,'default'=>2,'desc'=>__('订单金额显示位数'),'options'=>array(0=>__('无小数位'),1=>__('1位小数'),2=>__('2位小数'),3=>__('3位小数'))),
'system.money.operation.carryset'=>array('type'=>SET_T_ENUM,'default'=>0,'desc'=>__('价格进位方式'),'options'=>array(0=>__('四舍五入'),1=>__('向上取整'),2=>__('向下取整'))),
'system.money.thousands_sep'=>array('type'=>SET_T_STR,'default'=>''),
'site.currency.defalt_currency'=>array('type'=>SET_T_ENUM, 'default'=>'CNY', 'desc'=> __('站点默认货币'), 'options'=>array_merge(array(''=>'---请选择货币---'), app::get('ectools')->model('currency')->getSysCur(false))),

'system.path.article'=>array('type'=>SET_T_STR,'default'=>''),
'system.category.showgoods'=>array('type'=>SET_T_ENUM,'default'=>0,'desc'=>__('商品分类列表页显示设置'),'options'=>array('0'=>__('显示该分类及下属子分类下所有商品'),'1'=>__('仅显示本分类下商品'))),//liujy
'system.product.alert.num'=>array('type'=>SET_T_INT,'default'=>0,'desc'=>__('商品库存报警数量')),
'system.product.zendlucene'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>__('前台商品搜索是否启用zendlucene')),
'system.product.autobn.beginnum'=>array('type'=>SET_T_INT,'default'=>100),
'system.product.autobn.length'=>array('type'=>SET_T_INT,'default'=>6),
'system.product.autobn.prefix'=>array('type'=>SET_T_STR,'default'=>'PDT'),
//'system.goods.fastbuy'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>__('快速购买')),
'system.send_mail_method'=>array('type'=>SET_T_STR,'default'=>''),
'system.shoplang'=>array('type'=>SET_T_STR,'default'=>''),
'system.shopname'=>array('type'=>SET_T_STR,'default'=>__('点此设置您商店的名称'),'desc'=>__('商店名称')),
'system.shopurl'=>array('type'=>SET_T_STR,'default'=>''),

'system.admin_error_login_times'=>array('type'=>SET_T_INT,'default'=>0),
'system.admin_error_login_time'=>array('type'=>SET_T_INT,'default'=>0),
'system.use_cart'=>array('type'=>SET_T_BOOL,'default'=>true),
'system.seo.mklink'=>array('type'=>SET_T_STR,'default'=>'actmapper.getlink'),
'system.seo.parselink'=>array('type'=>SET_T_STR,'default'=>'actmapper.parse'),
'system.seo.emuStatic'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>__('商店页面启用伪静态URL')),
'system.seo.noindex_catalog'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>__('通知搜索引擎不索引目录页')),
'system.ui.current_theme'=>array('type'=>SET_T_STR),
'system.ui.webslice'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>__('支持ie8的webslice特性')),
'system.backup.splitFile'=>array('type'=>SET_T_BOOL,'default'=>false),
//'ux.dragcart'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>'允许拖动加入购物车'),
'site.title_format'=>array('type'=>SET_T_STR,'default'=>'%1 $system.shopname$','desc'=>__('网站标题格式')),
'site.stripHtml'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>__('是否压缩html')),
'site.url.base'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('主站访问地址')),
'site.url.themeres'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('模板资源访问地址')),
'site.url.widgetsres'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('版块资源访问地址')),
//'site.url.mediaurl'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'商品、文章等图片资源访问地址'),
'goods.rate_nums'=>array('type'=>SET_T_INT,'default'=>'10','desc'=>__('相关商品最大数量')),
'site.level_switch'=>array('type'=>SET_T_ENUM,'default'=>'0','options'=>array('0'=>__('按积分'),'1'=>__('按经验值')),'desc'=>__('会员等级升级方式')),
'site.level_point'=>array('type'=>SET_T_ENUM,'default'=>'0','options'=>array('0'=>__('否'),'1'=>__('是')),'desc'=>__('积分消费是否降会员等级')),

'gallery.default_view'=>array('type'=>SET_T_ENUM,'default'=>'index','options'=>array('index'=>__('图文混排'),'grid'=>__('橱窗形式'),'text'=>__('文字列表')),'desc'=>__('商品列表默认展示方式')),

'system.fast_delivery_as_progress'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>__('后台手工发货为"已发货"')),
'system.auto_delivery'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>__('用户到款则自动发货')),
'system.auto_delivery_physical'=>array('type'=>SET_T_STR,'default'=>'no','desc'=>__('用户到款自动发货时，实体商品如何处理(auto:发货为ready,no:不发货,yes:发货为progress)')),
'system.auto_use_advance'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>__('自动使用预存款')),

'search.show.range'=>array('type'=>SET_T_BOOL,'default'=>1,'desc'=>__('搜索是否显示价格区间')),
'errorpage.searchempty'=>array('type'=>SET_T_STR,'default'=>__('<h1 class="error" style="">非常抱歉，没有找到相关商品</h1>
        <p style="margin:15px 1em;"><strong>建议：</strong><br />适当缩短您的关键词或更改关键词后重新搜索，如：将 “索尼手机X1” 改为 “索尼+X1”</p>')),

'order.flow.payed'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>__('订单付款流程')),
'order.flow.consign'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>__('订单发货流程')),
'order.flow.refund'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>__('订单退款流程')),
'order.flow.reship'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>__('订单退货流程')),

'certificate.id'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>__('ShopEx证书编号')),
'certificate.token'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>__('ShopEx证书密钥')),
'certificate.str'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>__('ShopEx证书身份说明')),
'certificate.formal'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>__('ShopEx证书身份')),

'certificate.kft.cid'=>array('type'=>SET_T_STR,'default'=>'false','desc'=>__('客服通公司id')),
'certificate.kft.style'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('客服通风格号')),
'certificate.kft.action'=>array('type'=>SET_T_STR,'default'=>'TOAPPLY','desc'=>__('客服通动作')),
'certificate.kft.enable'=>array('type'=>SET_T_STR,'default'=>'TOAPPLY','desc'=>__('客服通开关')),



'certificate.channel.url'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('渠道url')),
'certificate.channel.name'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('渠道商名')),
'certificate.channel.status'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>__('渠道状态')),
'certificate.channel.service'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('渠道服务类型')),

'certificate.distribute'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>'是否开通分销模块'),

'messenger.sms.config'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('短信sms签名')),

'b2c.wss.username'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('合作统计用户名')),
'b2c.wss.password'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('合作统计密码')),
'b2c.wss.enable'=>array('type'=>SET_T_INT,'default'=>'0','desc'=>__('合作统计开关')),
'b2c.wss.show'=>array('type'=>SET_T_INT,'default'=>'0','desc'=>__('合作统计前台开关')),
'b2c.wss.js'=>array('type'=>SET_T_TXT,'default'=>'','desc'=>__('合作统计js')),

'comment.index.listnum'=>array('type'=>SET_T_STR,'default'=>4,'desc'=>__('商品首页显示评论条数')),
'comment.list.listnum'=>array('type'=>SET_T_STR,'default'=>10,'desc'=>__('评论列表页显示评论条数')),
'comment.switch.ask'=>array('type'=>SET_T_STR,'default'=>'on','desc'=>__('商品询问开关')),
'comment.switch.discuss'=>array('type'=>SET_T_STR,'default'=>'on','desc'=>__('商品评论开关')),
'comment.switch.buy'=>array('type'=>SET_T_STR,'default'=>'off','desc'=>__('商品经验评论开关')),
'comment.display.ask'=>array('type'=>SET_T_STR,'default'=>'reply','desc'=>__('商品评论(询问),回复显示')),
'comment.display.discuss'=>array('type'=>SET_T_STR,'default'=>'reply','desc'=>__('商品评论(评论),回复显示')),
'comment.display.buy'=>array('type'=>SET_T_STR,'default'=>'reply','desc'=>__('商品评论(经验),回复显示')),
'comment.power.ask'=>array('type'=>SET_T_STR,'default'=>'null','desc'=>__('商品评论(询问),发布权限')),
'comment.power.discuss'=>array('type'=>SET_T_STR,'default'=>'member','desc'=>__('商品评论(评论),发布权限')),
'comment.power.buy'=>array('type'=>SET_T_STR,'default'=>'buyer','desc'=>__('商品评论(经验),发布权限')),
'comment.null_notice.ask'=>array('type'=>SET_T_STR,'default'=>__('如果您对本商品有什么问题,请提问咨询!'),'desc'=>__('没有咨询记录,提示文字')),
'comment.null_notice.discuss'=>array('type'=>SET_T_STR,'default'=>__('如果您对本商品有什么评价或经验,欢迎分享!'),'desc'=>__('商品评论(经验),发布权限')),
'comment.null_notice.buy'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('商品评论(经验),发布权限')),
'comment.submit_display_notice.ask'=>array('type'=>SET_T_STR,'default'=>__('您的问题已经提交成功!'),'desc'=>__('没有咨询记录,提示文字')),
'comment.submit_hidden_notice.ask'=>array('type'=>SET_T_STR,'default'=>__('您的问题已经提交成功,管理员会尽快回复!'),'desc'=>__('商品评论(经验),发布权限')),
'comment.submit_display_notice.discuss'=>array('type'=>SET_T_STR,'default'=>__('感谢您的分享!'),'desc'=>__('商品评论(经验),发布权限')),
'comment.submit_hidden_notice.discuss'=>array('type'=>SET_T_STR,'default'=>__('感谢您的分享,管理员审核后会自动显示!'),'desc'=>__('没有咨询记录,提示文字')),
'comment.submit_display_notice.buy'=>array('type'=>SET_T_STR,'default'=>__('如果您对本商品有什么问题,请提问咨询!'),'desc'=>__('商品评论(经验),发布权限')),
'comment.submit_hidden_notice.buy'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('商品评论(经验),发布权限')),

'selllog.display.switch'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>__('是否显示销售记录')),
'selllog.display.limit'=>array('type'=>SET_T_INT,'default'=>'3','desc'=>__('低于多少条不显示销售记录')),
'selllog.display.listnum'=>array('type'=>SET_T_INT,'default'=>'10','desc'=>__('显示条数')),
'goodsbn.display.switch'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>__('是否启用商品编号')),
'goodsprop.display.position'=>array('type'=>SET_T_ENUM,'default'=>'1','options'=>array('1'=>__('仅商品价格上方'),'2'=>__('仅商品详情中'),'0'=>__('两处同时显示')),'desc'=>__('属性显示位置')),
'storeplace.display.switch'=>array('type'=>SET_T_BOOL,'default'=>true,'desc'=>__('是否使用商品货位')),
'system.location'=>array('type'=>SET_T_STR,'default'=>'mainland'),

'gallery.display.listnum'=>array('type'=>SET_T_INT,'default'=>'20','desc'=>__('搜索列表显示条数')),
'gallery.display.grid.colnum'=>array('type'=>SET_T_INT,'default'=>'4','desc'=>__('搜索橱窗页每行显示数')),

'plugin.passport.config.current_use'=>array('type'=>SET_T_STR,'default'=>'', 'desc'=>__('当前使用的passport')),
//'store.gtype.status'=>array('type'=>SET_T_ENUM,'default'=>0,'desc'=>'商店是否有自定义商品类型','options'=>array('0'=>__('否'),'1'=>'是')),//liujy

'system.message.open'=>array('type'=>SET_T_ENUM,'default'=>'off','desc'=>__('商店留言发布'),'options'=>array('on'=>__('会员提交后立即发布'),'off'=>__('管理员回复后发布'))),

'service.wltx'=>array('type'=>SET_T_STR,'default'=>''),
'system.default_storager'=>array('type'=>SET_T_STR,'default'=>'filesystem'),

'site.refer_timeout'=>array('type'=>SET_T_INT,'default'=>15,'desc'=>__('推荐链接过期时间（天）')),
'site.is_open_return_product'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>__('是否开启退货功能')),
'site.api.maintenance.is_maintenance'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>'','display'=>'false'),
'site.api.maintenance.notify_msg'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'','display'=>'false'),

'system.upload.limit'=>array('type'=>SET_T_ENUM,'default'=>0,'desc'=>__('前台图片大小限定'),'options'=>array(0=>'500k',1=>'1M',2=>'2M',3=>'3M',4=>'5M',5=>__('无限制')),'display'=>'false'),
'system.goods.freez.time'=>array('type'=>SET_T_ENUM,'default'=>1,'desc'=>__('库存预占触发时间'),'options'=>array('1'=>'下订单','2'=>'订单付款'),'display'=>'false'),
'system.guide' => array('type'=>SET_T_STR,'default'=>'', 'desc'=>__('向导设置')),
'goodsprop.display.switch'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>__('是否启用商品属性链接')),
'store.site_owner'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('商店所有人')),
'store.contact'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('联系人')),
'store.mobile'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('手机')),
'store.qq'=>array('type'=>SET_T_STR,'default'=>'','desc'=>'qq'),
'store.wangwang'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('旺旺')),
'system.enable_network'=>array('type'=>SET_T_BOOL,'default'=>false,'desc'=>__('启用网络连接')),
'site.rsc_rpc'=>array('type'=>SET_T_BOOL,'default'=>'true','desc'=>__('优化商店运营数据')),

'site.get_policy.method'=>array('type'=>SET_T_ENUM,'default'=>'1','options'=>array('1'=>__('不使用积分'),'2'=>__('按订单商品总价格计算积分 '),'3'=>__('为商品单独设置积分  ')),'desc'=>__('积分计算方式：')),
'site.get_rate.method'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('积分换算比率：')),
'site.min_order_amount'=>array('type'=>SET_T_STR,'default'=>'','desc'=>__('订单起订金额')),

'system.event_listener' => array('type'=>SET_T_STR,'default'=>'','desc'=>__('监控事件监听')),
'system.event_listener_key' => array('type'=>SET_T_STR,'default'=>'','desc'=>__('监控键值')),
);
?>
