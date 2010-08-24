<?php exit(); ?>a:2:{s:5:"value";s:18807:"<?php $this->__view_helper_model['site_view_helper'] = kernel::single('site_view_helper'); ?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $this->__view_helper_model['site_view_helper']->function_header(array(), $this);?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo kernel::base_url(), "/themes/",  $this->get_theme(), "/";?>images/css.css" />
<script src="<?php echo kernel::base_url(), "/themes/",  $this->get_theme(), "/";?>images/ad.js" type="text/javascript"></script>
</head>
<body>


<div class="zhushi" style="width:980px;border:1px solid #ccc; background:#ffffff; margin:0 auto; height:20px;line-height:20px; text-align:center;padding:0 8px;">友情提示：修改意见与本模板建议请直接在论坛<a style="color:#2755ff" target="_blank" href="http://www.shopex.cn/bbs/read.php?tid=152657">这里反馈</a>，如若要删除本区域，请查看<a target="_blank" style="color:#2755ff" href="http://www.shopex.cn/bbs/read.php?tid=152657">这里的详细步骤>></div>



<div id="top">
   <div class="topmain">
      <div class="logo"><div style="width:0; height:0; font-size:0; overflow:hidden;"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'usercustom' => '<a href="http://www.saffidesign.com">shopex</a>',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('usercustom', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));$this->_vars = array('widgets_id'=>'59');ob_start();?><a href="http://www.saffidesign.com">shopex</a><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_59">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '',
  'ad_pic_height' => '',
  'ad_pic' => '%THEME%/images/ybgx_01.jpg',
  'ad_pic_link' => '',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'60');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_60">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
      <div class="topinfo">
         <div class="toplogin"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'show_cart' => 'on',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('topbar', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['topbar'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/topbar/widget_topbar.php');$this->__widgets_exists['b2c']['topbar']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_topbar_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_topbar($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'61');ob_start();?><span id="foobar_<?php echo $this->_vars['widgets_id']; ?>" style="position: relative;">
 您好,<?php echo $_COOKIE['UNAME']; ?>&nbsp;
 <?php if( !$_COOKIE['UNAME'] ){ ?>
  <span id="loginBar_<?php echo $this->_vars['widgets_id']; ?>">
	<?php if($this->_vars['data']['login_content'])foreach ((array)$this->_vars['data']['login_content'] as $this->_vars['login']){  echo $this->_vars['login'];  } ?>
    <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_passport,'act' => login)); ?>">[请登录]</a>&nbsp;&nbsp;
    <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_passport,'act' => signup)); ?>">[免费注册]</a>
  </span>
  <?php }else{ ?>
  <span id="memberBar_<?php echo $this->_vars['widgets_id']; ?>">
    <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_member)); ?>">[会员中心]</a>&nbsp;&nbsp;
    <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_passport,'act' => logout)); ?>">[退出]</a>
  </span>
  <?php } ?>
  
  
  &nbsp;
  <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_cart,'act' => index)); ?>" target="_blank" class="cart-container">
    <span class="inlineblock CartIco">购物车</span>
  [<span id="Cart_<?php echo $this->_vars['widgets_id']; ?>" class="cart-number">0</span>]
  <?php echo $this->ui()->img(array('src' => "statics/bundle/arrow-down.gif"));?>
  </a>
  
</span>
<script>
/*
*foobar update:2009-9-8 13:46:55
*@author litie[aita]shopex.cn
*-----------------*/
window.addEvent('domready',function(){
       var barId ="<?php echo $this->_vars['widgets_id']; ?>";
       var bar = $('foobar_'+barId);

       var barOptions = {
           MID:Cookie.get('S[MEMBER]'),
           uname:Cookie.get('S[UNAME]'),
           coin:<?php echo ((isset($this->_vars['data']['cur']) && ''!==$this->_vars['data']['cur'])?$this->_vars['data']['cur']:'null'); ?>,
           curCoin:Cookie.get('S[CUR]'),
           cartViewURl:'<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_cart",'act' => "view")); ?>',
           stick:false
       };
       
       /*if(barOptions.MID){
          $('loginBar_'+barId).hide();
	      $('memberBar_'+barId).show();
          $('uname_'+barId).setText(barOptions.uname);
       }else{
          $('loginBar_'+barId).setStyle('visibility','visible');
       }*/

       if(coinBar=$('Cur_sel_'+barId)){
           
           var coinMenu = new Element('div',{'class':'coinmenu fmenu','styles':{'display':'none'}}).inject(document.body);
    /**
           barOptions.coin.each(function(item){
                
                if(item['cur_code']==barOptions['curCoin']){
                   coinBar.getElement('strong').set('text',[item.cur_sign,item.cur_name].join(''));
                }
                coinMenu.adopt(new Element('div',{'class':'item',text:[item.cur_sign,item.cur_name].join(''),events:{
                      
                      click:function(){
                          Cookie.set('S[CUR]',item.cur_code);
                          window.location.href=window.location.href;
                      }
                
                }}));
           });
           //*/
            coinBar.addEvents({
                'mouseenter':function(){
                   coinMenu.setStyles({
                      top:coinBar.getPosition().y+coinBar.getSize().y,
                      left:coinBar.getPosition().x,
                      display:'block',
                      visibility:'visible'
                   });
                }
            });
			new DropMenu(coinBar,{menu:coinMenu});

            
       }
       
       if(cartCountBar = $('Cart_'+barId)){
			cartCountBar.setText(Cookie.get('S[CART_COUNT]')?Cookie.get('S[CART_COUNT]'):0);
            var cartViewMenu =  new Element('div',{'class':'cartviewmenu fmenu','styles':{'display':'none'}}).inject(document.body);
            cartCountBar.addEvents({
                 'mouseenter':function(){
                   cartViewMenu.setStyles({
                      top:bar.getPosition().y+bar.getSize().y,
                      left:bar.getPosition().x,
                      width:bar.getSize().x,
                      display:'block',
                      visibility:'visible'
                   }).set('html','<div class="note">正在加载购物车信息...</div>');
                    this.retrieve('request',{cancel:$empty}).cancel();
                    this.store('request',new Request.HTML({update:cartViewMenu}).get(barOptions.cartViewURl));
                }
            });
			
			new DropMenu(cartCountBar,{menu:cartViewMenu});
            
	   }

});
</script><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_61">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
         <div class="topmenu"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'max_leng' => '',
  'showinfo' => '',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('menu_lv1', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['menu_lv1'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/menu_lv1/widget_menu_lv1.php');$this->__widgets_exists['b2c']['menu_lv1']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_menu_lv1_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_menu_lv1($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'62');ob_start();?><ul class="MenuList">
<?php $this->_env_vars['foreach'][wgtmenu]=array('total'=>count($this->_vars['data']),'iteration'=>0);foreach ((array)$this->_vars['data'] as $this->_vars['key'] => $this->_vars['item']){
                        $this->_env_vars['foreach'][wgtmenu]['first'] = ($this->_env_vars['foreach'][wgtmenu]['iteration']==0);
                        $this->_env_vars['foreach'][wgtmenu]['iteration']++;
                        $this->_env_vars['foreach'][wgtmenu]['last'] = ($this->_env_vars['foreach'][wgtmenu]['iteration']==$this->_env_vars['foreach'][wgtmenu]['total']);
 if( $this->bundle_vars['setting']['max_leng'] && $this->_vars['key']>$this->bundle_vars['setting']['max_leng'] ){  if( $this->_vars['item']['custom_url'] != '' ){ ?>
    <div><a href="<?php echo $this->_vars['item']['custom_url']; ?>" <?php if( $this->_vars['item']['target_blank'] == 'true' ){ ?>target="_blank"<?php } ?>><?php echo $this->_vars['item']['title']; ?></a></div>
    <?php }else{ ?>
    <div><a href="<?php echo kernel::router()->gen_url(array('app' => $this->_vars['item']['app'],'ctl' => $this->_vars['item']['ctl'],'act' => $this->_vars['item']['act'],'args' => $this->_vars['item']['params'],'full' => 1)); ?>"  <?php if( $this->_vars['item']['target_blank'] == 'true' ){ ?>target="_blank"<?php } ?>><?php echo $this->_vars['item']['title']; ?></a></div>
    <?php }  }elseif( $this->_vars['key']==$this->bundle_vars['setting']['max_leng'] && $this->bundle_vars['setting']['max_leng'] ){  $this->_vars["page"]="true"; ?>
    <li style="position:relative;z-index:65535;" class="wgt-menu-more" id="<?php echo $this->_vars['widgets_id']; ?>_menu_base" onClick="if($('<?php echo $this->_vars['widgets_id']; ?>_showMore').style.display=='none'){$('<?php echo $this->_vars['widgets_id']; ?>_showMore').style.display='';}else{ $('<?php echo $this->_vars['widgets_id']; ?>_showMore').style.display='none';}"><a class="wgt-menu-view-more" href="JavaScript:void(0)"></a>

    <div class="v-m-page" style="display:none;position:absolute; top:25px; left:0" id="<?php echo $this->_vars['widgets_id']; ?>_showMore">
    <?php if( $this->_vars['item']['custom_url'] != '' ){ ?>
    <div><a href="<?php echo $this->_vars['item']['custom_url']; ?>"  <?php if( $this->_vars['item']['target_blank'] == 'true' ){ ?>target="_blank"<?php } ?>><?php echo $this->_vars['item']['title']; ?></a></div>
    <?php }else{ ?>
    <div><a href="<?php echo kernel::router()->gen_url(array('app' => $this->_vars['item']['app'],'ctl' => $this->_vars['item']['ctl'],'act' => $this->_vars['item']['act'],'args' => $this->_vars['item']['params'],'full' => 1)); ?>"  <?php if( $this->_vars['item']['target_blank']  == 'true' ){ ?>target="_blank"<?php } ?>><?php echo $this->_vars['item']['title']; ?></a></div>
    <?php }  }else{  if( $this->_vars['item']['custom_url'] != '' ){ ?>
    <li><a <?php if( $this->_env_vars['foreach']['menu']['last'] ){ ?>class="last"<?php } ?> href="<?php echo $this->_vars['item']['custom_url']; ?>"  <?php if( $this->_vars['item']['target_blank'] == 'true' ){ ?>target="_blank"<?php } ?>><?php echo $this->_vars['item']['title']; ?></a></li>
    <?php }else{ ?>
    <li><a href="<?php echo kernel::router()->gen_url(array('app' => $this->_vars['item']['app'],'ctl' => $this->_vars['item']['ctl'],'act' => $this->_vars['item']['act'],'args' => $this->_vars['item']['params'],'full' => 1)); ?>"  <?php if( $this->_vars['item']['target_blank'] == 'true' ){ ?>target="_blank"<?php } ?>><?php echo $this->_vars['item']['title']; ?></a></li>
    <?php }  }  } unset($this->_env_vars['foreach'][wgtmenu]);  if( $this->_vars['page']=="true" ){ ?>
</div>
</li>

<?php } ?>
</ul>

<script>
if($('<?php echo $this->_vars['widgets_id']; ?>_showMore')){
    $('<?php echo $this->_vars['widgets_id']; ?>_showMore').setOpacity(.8);
}
</script>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_62">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
         <div class="topsearch">
             <div class="topkeysearch"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'searchopen' => 'on',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('search', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['search'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/search/widget_search.php');$this->__widgets_exists['b2c']['search']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_search_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_search($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'63');ob_start();?><form action="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_search,'act' => result)); ?>" method="post" class="SearchBar">
  <table cellpadding="0" cellspacing="0">
    <tr>
      <td class="search_label"> <span>关键字：</span>
        <input name="name[]" size="10" class="inputstyle keywords" value="" />
      </td>
      
      <td class="search_price1">价格从 <?php echo $this->ui()->input(array('name' => "price[0]",'type' => "number",'size' => "4",'class' => "inputstyle gprice_from"));?></td>
      <td class="search_price2">到<?php echo $this->ui()->input(array('name' => "price[1]",'type' => "number",'size' => "4",'class' => "inputstyle gprice_to"));?></td>
      
      <td><input type="submit" value="搜索" class="btn_search" onfocus='this.blur();'/>
      </td>
      <td><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_search,'act' => index)); ?>" class="btn_advsearch">高级搜索</a> </td>
    </tr>
  </table>
</form>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_63">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
             <div class="topkey"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'usercustom' => '<span style="font-weight: bold;">热门关键字：</span><a href="#">牛仔</a> <a href="#">OL衬衫</a> <a href="#">条纹</a> <a href="#">流苏</a> <a href="#">OL衬衫</a> <a href="..//#">条纹</a>',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('usercustom', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));$this->_vars = array('widgets_id'=>'64');ob_start();?><span style="font-weight: bold;">热门关键字：</span><a href="#">牛仔</a> <a href="#">OL衬衫</a> <a href="#">条纹</a> <a href="#">流苏</a> <a href="#">OL衬衫</a> <a href="..//#">条纹</a><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_64">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
         </div>
      </div>
   </div>
</div>";s:6:"expire";i:0;}