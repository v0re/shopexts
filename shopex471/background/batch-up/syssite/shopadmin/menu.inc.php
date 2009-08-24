<?php
define("MENU_DEV",false);  //菜单开发状态


if(defined("OTHER_FORUMS")){
	if(OTHER_FORUMS == md5("forum_s1a2d3f4".LICENSE_1)){
		$CON_MENU['1008'] = array(0=>$PROG_TAGS["ptag_1843"], 1=>'1', 2=>3, 3=>9);
	}
}

//数组中的元素依次array("菜单名称","是否最底级","菜单级别","属于那一版本：0，联盟版；1，企业版；2，单店版；9，通用;10,测试中",图标,链接,是否菜单项)
$CON_MENU['1'] = array('&nbsp;'.$PROG_TAGS["ptag_1"].'&nbsp;&nbsp;', '0', 1, 9,'<img src="./images/menu-config.gif" />','',1);
$CON_MENU['21'] = array($PROG_TAGS["ptag_86"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['2104'] = array($PROG_TAGS["ptag_90"], '1', 3, 9,'<img src="./images/icon-goodsadd-0.gif" />','admin_goods.php',1);
$CON_MENU['2103'] = array($PROG_TAGS["ptag_89"], '1', 3, 9,'<img src="./images/icon-goodscon-0.gif" />','admin_goods_list.php',1);
$CON_MENU['2107'] = array($PROG_TAGS["ptag_1859"], '1', 3, 9,'<img src="./images/icon-goodsbatchupload-0.gif" />','admin_goods_upload.php',1);
$CON_MENU['2119'] = array('商品批量上下架', '1', 3, 9,'<img src="./images/icon-goodsbatchupload-0.gif" />','admin_goods_up.php',1);
$CON_MENU['2108'] = array($PROG_TAGS["batch_update"], '1', 3, 9,'<img src="./images/icon-goodsbatchedit-0.gif" />','admin_goods_batch_search.php',1);
$CON_MENU['2105'] = array($PROG_TAGS["ptag_goodsnotify"], '1', 3, 9,'<img src="./images/icon-goodscon-noreal-0.gif" />','admin_goodsnotify_list.php',1);
$CON_MENU['17'] = array($PROG_TAGS["ptag_87"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['2101'] = array($PROG_TAGS["ptag_87"], '1', 3, 9,'<img src="./images/icon-goodslist-0.gif" />','admin_pcat_list.php',1);
$CON_MENU['2102'] = array($PROG_TAGS["ptag_88"], '1', 3, 9,'<img src="./images/icon-goodslistadd-0.gif" />','admin_pcat.php',1);
$CON_MENU['2109'] = array($PROG_TAGS["ptag_catcopy"], '1', 3, 9,'<img src="./images/icon-goodslistclone-0.gif" />','admin_pcat_copy.php',1);
$CON_MENU['28'] = array($PROG_TAGS["ptag_brand_management"], '0', 2, 9,'<img src="./images/program.gif" />', '',1);
$CON_MENU['2801'] = array($PROG_TAGS["ptag_brand_list"], '1', 3, 9,'<img src="./images/icon-brandlist.gif" />','admin_brand_list.php',1);
$CON_MENU['2802'] = array($PROG_TAGS["ptag_brand_add"], '1', 3, 9,'<img src="./images/icon-brandadd.gif" />','admin_brand.php',1);
$CON_MENU['18'] = array($PROG_TAGS["ptag_goodsprops"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['2113'] = array($PROG_TAGS["ptag_propcat"], '1', 3, 9,'<img src="./images/icon-propfittingscat.gif" />','admin_propcat_list.php',1);
$CON_MENU['2112'] = array($PROG_TAGS["ptag_goodsprop"], '1', 3, 9,'<img src="./images/icon-propfittingsadd.gif" />','admin_prop.php',1);
$CON_MENU['2110'] = array($PROG_TAGS["ptag_goodsprop_list"], '1', 3, 9,'<img src="./images/icon-propfittingslist.gif" />','admin_prop_list.php',1);
$CON_MENU['23'] = array($PROG_TAGS["ptag_97"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['2301'] = array($PROG_TAGS["ptag_98"], '1', 3, 9,'<img src="./images/icon-memberlist-0.gif" />','admin_member_list.php',1);
$CON_MENU['2302'] = array($PROG_TAGS["ptag_99"], '1', 3, 9,'<img src="./images/icon-memberagree-0.gif" />', 'admin_otherinfo.php?type=contract',1);
$CON_MENU['2303'] = array($PROG_TAGS["ptag_1384"], '1', 3, 9,'<img src="./images/icon-memberlevel-0.gif" />', 'admin_level_list.php',1);
$CON_MENU['2304'] = array($PROG_TAGS["ptag_1392"], '1', 3, 9,'<img src="./images/icon-memberprepay-0.gif" />', 'admin_advance_list.php',1);
$CON_MENU['16'] = array($PROG_TAGS["ptag_1968"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['1601'] = array($PROG_TAGS["ptag_82"], '1', 3, 9,'<img src="images/icon-pay-0.gif" />', 'admin_ptype_list.php',1);
$CON_MENU['14'] = array($PROG_TAGS["ptag_1763"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['1401'] = array($PROG_TAGS["ptag_81"], '1', 3, 9,'<img src="images/icon-send-0.gif" />','admin_ttype_list.php',1);
$CON_MENU['1402'] = array($PROG_TAGS["ptag_1764"], '1', 3, 9,'<img src="images/icon-sendarea-0.gif" />','admin_deliverarea_list.php',1);
$CON_MENU['1403'] = array($PROG_TAGS["ptag_1765"], '1', 3, 9,'<img src="images/icon-sendfx-0.gif" />','admin_deliverexp_list.php',1);
$CON_MENU['1404'] = array($PROG_TAGS["ptag_1990"], '1', 3, 9,'<img src="images/icon-transit-0.gif" />','admin_logistics_list.php',1);
$CON_MENU['1405'] = array($PROG_TAGS["d_inerface_menu"], '1', 3, 9,'<img src="images/icon-delifee-0.gif" />','admin_interface_list.php?cat=SHOP_I_DELIVERY',1);
$CON_MENU['22'] = array($PROG_TAGS["ptag_94"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['2201'] = array($PROG_TAGS["ptag_95"], '1', 3, 9,'<img src="./images/icon-order-0.gif" />','admin_order_list.php',1);
$CON_MENU['2202'] = array($PROG_TAGS["ptag_96"], '1', 3, 9,'<img src="./images/icon-printformat-0.gif" />','admin_order_style.php',1);

$CON_MENU['2203'] = array($PROG_TAGS["ptag_flowset"], '1', 3, 9,'','',0);   //功能
$CON_MENU['2204'] = array($PROG_TAGS["ptag_orderedit"], '1', 3, 9,'','',0);
$CON_MENU['2206'] = array($PROG_TAGS["ptag_orderremove"], '1', 3, 9,'','',0);
$CON_MENU['2205'] = array($PROG_TAGS["ptag_ordercancel"], '1', 3, 9,'','',0);
$CON_MENU['2207'] = array($PROG_TAGS["ptag_orderfund"], '1', 3, 9,'','',0);
$CON_MENU['2208'] = array($PROG_TAGS["ptag_ordergoods"], '1', 3, 9,'','',0);

$CON_MENU['25'] = array($PROG_TAGS["sheets_admin"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['2501'] = array($PROG_TAGS["send_sheets"], '1', 3, 9,'<img src="./images/icon-invoice-0.gif" />', 'admin_consign_list.php',1);
$CON_MENU['2502'] = array($PROG_TAGS["return_sheets"], '1', 3, 9,'<img src="./images/icon-reject-0.gif" />','admin_reship_list.php',1);
$CON_MENU['2503'] = array($PROG_TAGS["check_sheets"], '1', 3, 9,'<img src="./images/icon-moneygathering-0.gif" />', 'admin_orderbill_list.php',1);
$CON_MENU['2504'] = array($PROG_TAGS["back_sheets"], '1', 3, 9,'<img src="./images/icon-refundment-0.gif" />', 'admin_orderrefund_list.php',1);

$CON_MENU['2'] = array('&nbsp;'.$PROG_TAGS["ptag_26"].'&nbsp;&nbsp;', '0', 1, 9,'<img src="./images/menu-config.gif" />','',1);
$CON_MENU['10'] = array($PROG_TAGS["ptag_2"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['1001'] = array($PROG_TAGS["ptag_3"], '1', 3, 9,'<img src="images/icon-basicsetup-0.gif" />','admin_sys.php',1);
$CON_MENU['1002'] = array($PROG_TAGS["ptag_9"], '1', 3, 9,'<img src="images/icon-indexcon-0.gif" />','admin_page.php',1);
$CON_MENU['1006'] = array($PROG_TAGS["ptag_4"], '1', 3, 9,'<img src="images/icon-about-0.gif" />','admin_otherinfo.php?type=aboutus',1);
$CON_MENU['1005'] = array($PROG_TAGS["ptag_2041"], '1', 3, 9,'<img src="images/icon-contactus-0.gif" />','admin_otherinfo.php?type=contactus',1);
$CON_MENU['1007'] = array($PROG_TAGS["ptag_17"], '1', 3, 9,'<img src="images/icon-copyright-0.gif" />', 'admin_otherinfo.php?type=copyright',1);
$CON_MENU['1009'] = array($PROG_TAGS["ttag_1953"], '1', 3, 9,'<img src="images/icon-icp-0.gif" />', 'admin_certup.php',1);
$CON_MENU['1010'] = array($PROG_TAGS["ptag_watermark"], '1', 3, 9,'<img src="images/icon-watermark-0.gif" />','admin_watermark.php',1);
$CON_MENU['12'] = array($PROG_TAGS["ptag_21"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['1201'] = array($PROG_TAGS["ptag_23"], '1', 3, 2,'<img src="images/icon-pagesetup-0.gif" />','admin_template_upload.php',1);
$CON_MENU['1202'] = array($PROG_TAGS["ptag_24"], '1', 3, 9,'<img src="images/icon-templatelist-0.gif" />','admin_tplload_list.php',1);
$CON_MENU['1203'] = array($PROG_TAGS["ptag_83"], '1', 3, 9,'<img src="images/icon-tplsetup-0.gif" />','admin_template_list.php',1);
$CON_MENU['35'] = array($PROG_TAGS["ptag_1964"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['3501'] = array($PROG_TAGS["ptag_1872"], '1', 3, 9,'<img src="./images/icon-cusmark-0.gif" />','admin_definetag_list.php',1);
$CON_MENU['3502'] = array($PROG_TAGS["ptag_binding_tag"], '1', 3, 9,'<img src="./images/icon-productpagetag.gif" />','admin_binding_tag_list.php',1);
$CON_MENU['3503'] = array($PROG_TAGS["ptag_selfhelppage"], '1', 3, 9,'<img src="./images/icon-productconverge.gif" />','admin_selfhelppage_list.php',1);
$CON_MENU['30'] = array($PROG_TAGS["ptag_101"], '0', 2, 9,'<img src="./images/program.gif" />','',1);
$CON_MENU['3001'] = array($PROG_TAGS["ptag_102"], '1', 3, 9,'<img src="./images/icon-banner-0.gif" />',"admin_adv_list.php",1);
$CON_MENU['3002'] = array($PROG_TAGS["ptag_103"], '1', 3, 9,'<img src="./images/icon-banneradd-0.gif" />',"admin_adv.php",1);
$CON_MENU['31'] = array($PROG_TAGS["ptag_42"], '0', 2, 9,'<img src="./images/program.gif" />','',1);
$CON_MENU['3101'] = array($PROG_TAGS["ptag_44"], '1', 3, 9,'<img src="images/icon-linksetup-0.gif" />', 'admin_link_list.php',1);
$CON_MENU['3102'] = array($PROG_TAGS["ptag_45"], '1', 3, 9,'<img src="images/icon-linkadd-0.gif" />','admin_link.php',1);
$CON_MENU['20'] = array($PROG_TAGS["ptag_27"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['2001'] = array($PROG_TAGS["ptag_84"], '1', 3, 9,'<img src="./images/icon-infoclass-0.gif" />', 'admin_ncat_list.php',1);
$CON_MENU['2002'] = array($PROG_TAGS["ptag_85"], '1', 3, 9,'<img src="./images/icon-infoclassadd-0.gif" />', 'admin_ncat.php',1);
$CON_MENU['2003'] = array($PROG_TAGS["ptag_28"], '1', 3, 9,'<img src="./images/icon-infolist-0.gif" />', 'admin_ncon_list.php',1);
$CON_MENU['2004'] = array($PROG_TAGS["ptag_29"], '1', 3, 9,'<img src="./images/icon-infoadd-0.gif" />', 'admin_ncon.php',1);
$CON_MENU['13'] = array($PROG_TAGS["ptag_1744"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['1301'] = array($PROG_TAGS["ptag_1745"], '1', 3, 9,'<img src="images/icon-currency-0.gif" />','admin_currency.php',1);
$CON_MENU['1302'] = array($PROG_TAGS["ptag_1746"], '1', 3, 9,'<img src="images/icon-currencylist-0.gif" />','admin_currency_list.php',1);
$CON_MENU['27'] = array($PROG_TAGS["comment_management"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['2701'] = array($PROG_TAGS["comment_setup"], '1', 3, 9,'<img src="./images/icon-commentglobalset.gif" />','admin_comment_setup.php',1);
$CON_MENU['2702'] = array($PROG_TAGS["comment_item"], '1', 3, 9,'<img src="./images/icon-commentitems.gif" />','admin_comment_item.php',1);
$CON_MENU['2703'] = array($PROG_TAGS["ptag_discusslist"], '1', 3, 9,'<img src="./images/icon-goodsdiscuss-0.gif" />','admin_comment_list.php',1);
$CON_MENU['24'] = array($PROG_TAGS["ptag_1965"], '0', 2, 9,'<img src="./images/program.gif" />', '',1);
$CON_MENU['2401'] = array($PROG_TAGS["ptag_1885"], '1', 3, 9,'<img src="./images/icon-guestbook-0.gif" />','admin_bbs_list.php',1);

//验证是否在MALL_CONFIG中打开设置
$CON_MENU['3'] = array('&nbsp;'.$PROG_TAGS["ptag_40"].'&nbsp;&nbsp;', '0', 1, 9,'<img src="./images/menu-config.gif" />','',1);
$CON_MENU['11'] = array($PROG_TAGS["ptag_1389"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['1102'] = array($PROG_TAGS["ptag_1391"], '1', 3, 9,'<img src="images/icon-manauseradd-0.gif" />','admin_operater.php',1);
$CON_MENU['1101'] = array($PROG_TAGS["ptag_1390"], '1', 3, 9,'<img src="images/icon-manauserlist-0.gif" />','admin_operater_list.php',1);
$CON_MENU['1003'] = array($PROG_TAGS["ptag_8"], '1', 3, 9,'<img src="images/icon-passwdmodi-0.gif" />','admin_psw.php',1);
$CON_MENU['33'] = array($PROG_TAGS["ptag_68"], '0', 2, 9,'<img src="./images/program.gif" />', '',1);
$CON_MENU['3301'] = array($PROG_TAGS["ptag_69"], '1', 3, 9,'<img src="./images/icon-salecount-0.gif" />', 'Cache.php?tjtype=1',1);
$CON_MENU['3302'] = array($PROG_TAGS["ptag_104"], '1', 3, 9,'<img src="./images/icon-packcount-0.gif" />', 'Cache.php?tjtype=2',1);
$CON_MENU['3303'] = array($PROG_TAGS["ptag_105"], '1', 3, 9,'<img src="./images/icon-filter-0.gif" />', 'Cache.php?tjtype=3',1);
$CON_MENU['34'] = array($PROG_TAGS["ptag_49"], '0', 2, 9,'<img src="./images/program.gif" />', '',1);
$CON_MENU['3401'] = array($PROG_TAGS["ptag_50"], '1', 3, 2,'<img src="images/icon-dataupdate-0.gif" />', 'admin_updatesql.php',1);
$CON_MENU['3402'] = array($PROG_TAGS["ptag_1838"], '1', 3, 2,'<img src="images/icon-databackup-0.gif" />', 'admin_databack.php',1);
$CON_MENU['3403'] = array($PROG_TAGS["ptag_1839"], '1', 3, 2,'<img src="images/icon-datarecover-0.gif" />', 'admin_dataresume.php',1);
$CON_MENU['1011'] = array($PROG_TAGS["ptag_api"], '1', 3, 9,'<img src="images/icon-api-0.gif" />','admin_api_list.php',1);
$CON_MENU['1031'] = array($PROG_TAGS["ptag_cachemgr"], '1', 3, 9,'<img src="images/icon-cachemgr-0.gif" />','admin_cachemgr.php',1);
$CON_MENU['3405'] = array($PROG_TAGS["ptag_debuging"], '1', 3, 9,'<img src="images/icon-debuging-0.gif" />','admin_debuging.php',1);
$CON_MENU['3406'] = array($PROG_TAGS["ptag_client_letter"], '1', 3, 9,'<img src="images/icon-messager-0.gif" />','http://www.shopex.cn/products/ShopExNote_intro.html',1);
$CON_MENU['3407'] = array($PROG_TAGS["ptag_shopassistant"], '1', 3, 9,'<img src="images/icon-assistant-0.gif" />','http://www.shopex.cn/products/ShopExAssitant_intro.html',1);
$CON_MENU['3408'] = array($PROG_TAGS["ptag_kft"], '1', 3, 9,'<img src="images/kf.gif" />','admin_shopexkft.php',1);

$CON_MENU['15'] = array($PROG_TAGS["ptag_1961"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['1501'] = array($PROG_TAGS["ptag_1915"], '1', 3, 9,'<img src="images/icon-mailsetup-0.gif" />','admin_mailbasic.php',1);
$CON_MENU['1502'] = array($PROG_TAGS["ptag_1962"], '1', 3, 9,'<img src="images/icon-mailsendsetup-0.gif" />','admin_mailset_list.php',1);
$CON_MENU['32'] = array($PROG_TAGS["ptag_1386"], '0', 2, 9,'<img src="./images/program.gif" />','',1);
$CON_MENU['3201'] = array($PROG_TAGS["ptag_1387"], '1', 3, 9,'<img src="./images/icon-maillistsetup-0.gif" />',  'admin_group_list.php',1);
$CON_MENU['3202'] = array($PROG_TAGS["ptag_1388"], '1', 3, 9,'<img src="./images/icon-mailmaglist-0.gif" />',  'admin_publication_list.php',1);
$CON_MENU['36'] = array($PROG_TAGS["ptag_1966"], '0', 2, 9,'<img src="./images/program.gif" />', '',1);
$CON_MENU['3601'] = array($PROG_TAGS["ptag_1843"], '1', 3, 9,'<img src="./images/icon-forumjoin.gif" />', 'admin_forumset.php',1);
$CON_MENU['3602'] = array($PROG_TAGS["ptag_integrateim"], '1', 3, 9,'<img src="./images/icon-imconn-0.gif" />','admin_integrateim_list.php',1);
$CON_MENU['3603'] = array($PROG_TAGS["ptag_userintegration"], '1', 3, 9,'<img src="images/icon-userconn-0.gif" />', 'admin_interface_list.php?cat=SHOP_I_USER',1);
$CON_MENU['3604'] = array($PROG_TAGS["ptag_53kf"], '1', 3, 9,'<img src="images/icon-servise.gif" />', 'admin_53kf.php',1);

$CON_MENU['6'] = array('&nbsp;'.$PROG_TAGS["ptag_sales_promotion"].'&nbsp;&nbsp;', '0', 1, 9,'<img src="./images/menu-config.gif" />','',1);
$CON_MENU['62'] = array($PROG_TAGS["ptag_pointmanage"], '0', 2, 9,'<img src="./images/program.gif" />', '',1);
$CON_MENU['1008'] = array($PROG_TAGS["ptag_pointsys"], '1', 3, 9,'<img src="images/icon-pointglobalset.gif" />','admin_point_sys.php',1);
$CON_MENU['60'] = array($PROG_TAGS["ptag_gift"], '0', 2, 9,'<img src="./images/program.gif" />', '',1);
$CON_MENU['6001'] = array($PROG_TAGS["ptag_gift_catlist"], '1', 3, 9,'<img src="./images/icon-giftcategory.gif" />', 'admin_gift_cat_list.php',1);
$CON_MENU['6002'] = array($PROG_TAGS["ptag_gift_list"], '1', 3, 9,'<img src="./images/icon-giftlist.gif" />', 'admin_gift_list.php',1);
$CON_MENU['6003'] = array($PROG_TAGS["ptag_gift_rule"], '1', 3, 9,'<img src="./images/icon-changegiftrule.gif" />', 'admin_gift_rule.php',1);
$CON_MENU['61'] = array($PROG_TAGS["ptag_coupon"], '0', 2, 9,'<img src="./images/program.gif" />', '',1);
$CON_MENU['6101'] = array($PROG_TAGS["ptag_coupon_list"], '1', 3, 9,'<img src="./images/icon-couponlist.gif" />', 'admin_coupon_list.php?type=giftrule',1);
$CON_MENU['6102'] = array($PROG_TAGS["ptag_coupon_add"], '1', 3, 9,'<img src="./images/icon-couponadd.gif" />','admin_coupon.php',1);
$CON_MENU['26'] = array($PROG_TAGS["wholesale_management"], '0', 2, 9,'<img src="images/program.gif" />','',1);
$CON_MENU['2601'] = array($PROG_TAGS["wholesale"], '1', 3, 9,'<img src="./images/icon-wholesaleplan-0.gif" />','admin_wholesale_list.php',1);
$CON_MENU['63'] = array($PROG_TAGS["ptag_package"], '0', 2, 9,'<img src="./images/program.gif" />', '',1);
$CON_MENU['2111'] = array($PROG_TAGS["ptag_salepackage"], '1', 3, 9,'<img src="./images/icon-bindingproductlist.gif" />','admin_sale_package_list.php',1);

$CON_MENU['5'] = array('&nbsp;'.$PROG_TAGS["ptag_2180"].'&nbsp;&nbsp;', '0', 1, 9,'<img src="./images/menu-config.gif" />','',1);
$CON_MENU['52'] = array($PROG_TAGS["ptag_seo"], '0', 2, 9,'<img src="./images/program.gif" />', '',1);
$CON_MENU['5201'] = array("Google SiteMap", '1', 3, 9,'<img src="./images/icon-sitemap.gif" />', 'admin_googlesitemap.php',1);
$CON_MENU['5202'] = array($PROG_TAGS["ptag_htmloptimize"], '1', 3, 9,'<img src="./images/icon-html.gif" />', 'admin_seo.php',1);

//*商盟部分开始*/
if(MENU_DEV)
{
$CON_MENU['50'] = array($PROG_TAGS["ptag_2181"], '0', 2, 9,'<img src="./images/program.gif" />','',1);
$CON_MENU['5001'] = array($PROG_TAGS["ptag_2338"], '1', 3, 9,'<img src="./images/icon-forumjoin.gif" />','admin_shopunion_setup.php',1);
$CON_MENU['5002'] = array($PROG_TAGS["ptag_2350"], '1', 3, 9,'<img src="./images/icon-forumjoin.gif" />','admin_shopunion_goods_sync.php',1);
$CON_MENU['5003'] = array($PROG_TAGS["ptag_2351"], '1', 3, 9,'<img src="./images/icon-forumjoin.gif" />','admin_shopunion_promotes_list.php',1);
$CON_MENU['5003'] = array($PROG_TAGS["ptag_2352"], '1', 3, 9,'<img src="./images/icon-forumjoin.gif" />','admin_shopunion_message.php',1);
$CON_MENU['51'] = array($PROG_TAGS["ptag_2182"], '0', 2, 9,'<img src="./images/program.gif" />', '',1);
$CON_MENU['5101'] = array($PROG_TAGS["ptag_2183"], '1', 3, 9,'<img src="./images/icon-forumjoin.gif" />','admin_mutualset.php',1);
$CON_MENU['5102'] = array($PROG_TAGS["ptag_2184"], '0', 3, 9,'<img src="./images/program.gif" />','',1);
$CON_MENU['510201'] = array($PROG_TAGS["ptag_2185"], '1', 4, 9,'<img src="./images/icon-forumjoin.gif" />','admin_mutualgoods_list.php',1);
$CON_MENU['510202'] = array($PROG_TAGS["ptag_2186"], '1', 4, 9,'<img src="./images/icon-forumjoin.gif" />','admin_mutualpic_list.php',1);
$CON_MENU['510203'] = array($PROG_TAGS["ptag_2187"], '1', 4, 9,'<img src="./images/icon-forumjoin.gif" />','admin_mutualhref_list.php', '',1);
$CON_MENU['5103'] = array($PROG_TAGS["ptag_2188"], '1', 3, 9,'<img src="./images/icon-sitemap.gif" />','admin_searchpartner.php',1);
$CON_MENU['5104'] = array($PROG_TAGS["ptag_2189"], '1', 3, 9,'<img src="./images/icon-forumjoin.gif" />','admin_partnermsg_list.php',1);
$CON_MENU['5105'] = array($PROG_TAGS["ptag_2190"], '1', 3, 9,'<img src="./images/icon-forumjoin.gif" />','admin_mutualrequest_list.php',1);
$CON_MENU['5106'] = array($PROG_TAGS["ptag_2191"], '0', 3, 9,'<img src="./images/program.gif" />','',1);
$CON_MENU['510601'] = array($PROG_TAGS["ptag_2192"], '1', 4, 9,'<img src="./images/icon-forumjoin.gif" />','admin_mutualpartner_list.php',1);
$CON_MENU['510602'] = array($PROG_TAGS["ptag_2193"], '1', 4, 9,'<img src="./images/icon-forumjoin.gif" />','admin_mutualclose_list.php',1);
}

//*商盟部分结束*/
$CON_MENU['4'] = array('&nbsp;'.$PROG_TAGS["ptag_53"].'&nbsp;&nbsp;', '0', 1, 9,'<img src="./images/menu-config.gif" />','',1);
$CON_MENU['40'] = array($PROG_TAGS["ptag_shopgeneralize"], '1', 2, 2,'<img src="./images/icon-generalize-0.gif" />', 'admin_shopgeneralize.php',1);
$CON_MENU['41'] = array($PROG_TAGS["ptag_106"], '1', 2, 1,'<img src="./images/icon-onlinesev-0.gif" />','admin_askonline_list.php',1);
$CON_MENU['42'] = array($PROG_TAGS["ptag_54"], '1', 2, 2,'<img src="./images/icon-gotohome-0.gif" />', 'http://www.shopex.cn/',1);
$CON_MENU['43'] = array($PROG_TAGS["ptag_56"], '1', 2, 2,'<img src="./images/icon-checkupdate-0.gif" />', 'http://update.shopex.com.cn/',1);
$CON_MENU['44'] = array($PROG_TAGS["ptag_57"], '1', 2, 9,'<img src="./images/icon-questions-0.gif" />', 'javascript:openhelp(2)',1);
$CON_MENU['45'] = array($PROG_TAGS["ptag_2268"], '1', 2, 2,'<img src="./images/icon-home-0.gif" />', 'admin_home.php',1);
$CON_MENU['46'] = array($PROG_TAGS["ptag_61"], '1', 2, 2,'<img src="./images/icon-aboutsoft-0.gif" />','javascript:openwin("about.php")',1);

?>