<?php exit(); ?>a:2:{s:5:"value";s:7479:"<?php $this->__view_helper_model['site_view_helper'] = kernel::single('site_view_helper'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php echo $this->__view_helper_model['site_view_helper']->function_header(array(), $this);?>
<link rel="stylesheet" type="text/css" href="<?php echo kernel::base_url(), "/themes/",  $this->get_theme(), "/";?>images/style.css" />

</head><body>
<div class="page">
<div class="header">
  <div class="logo"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('logo', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['logo'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/logo/widget_logo.php');$this->__widgets_exists['b2c']['logo']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_logo_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_logo($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'29');ob_start();?><a href="./"><img src="<?php echo $this->_vars['data']['logo_image']; ?>" border="0"/></a><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_29">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  <div class="tell"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'usercustom' => '客服电话：<br /><span>021-665555858</span>',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('usercustom', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));$this->_vars = array('widgets_id'=>'30');ob_start();?>客服电话：<br /><span>021-665555858</span><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_30">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
 
    <div class="top_login"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('member', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['member'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/member/widget_member.php');$this->__widgets_exists['b2c']['member']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_member_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_member($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'28');ob_start(); if( !$_COOKIE['MEMBER'] ){ ?>

<span id="loginBar_<?php echo $this->_vars['widgets_id']; ?>">
<?php if($this->_vars['data']['login_content'])foreach ((array)$this->_vars['data']['login_content'] as $this->_vars['login']){  echo $this->_vars['login'];  } ?>
  <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_passport,'act' => login)); ?>">[请登录]</a>&nbsp;&nbsp;
  <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_passport,'act' => signup)); ?>">[免费注册]</a>
</span> 
<?php }else{ ?>
<span id="memberBar_<?php echo $this->_vars['widgets_id']; ?>">
    您好<span id="uname_<?php echo $this->_vars['widgets_id']; ?>"></span>！
  <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_member,'act' => index)); ?>">[会员中心]</a>&nbsp;&nbsp;
  <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_passport,'act' => logout)); ?>">[退出]</a>
</span>
<script>
$("uname_<?php echo $this->_vars['widgets_id']; ?>").innerHTML = Cookie.read('UNAME');
</script>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_28">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
   <div class="top_search"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('search', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['search'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/search/widget_search.php');$this->__widgets_exists['b2c']['search']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_search_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_search($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'31');ob_start();?><form action="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_search,'act' => result)); ?>" method="post" class="SearchBar">
  <table cellpadding="0" cellspacing="0">
    <tr>
      <td class="search_label"> <span>关键字：</span>
        <input name="name[]" size="10" class="inputstyle keywords" value="" />
      </td>
      
      <td><input type="submit" value="搜索" class="btn_search" onfocus='this.blur();'/>
      </td>
      <td><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_search,'act' => index)); ?>" class="btn_advsearch">高级搜索</a> </td>
    </tr>
  </table>
</form>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_31">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
</div>
<div class="menu"><a href="?">首页</a><span>|</span><a href="?page-nonmember.html">关于我们</a><span>|</span><a href="?page-nonmember.html">新闻</a><span>|</span><a href="?gallery---index.html">产品 (在线商城)</a><span>|</span><a href="?page-nonmember.html">服务</a><span>|</span><a href="?page-nonmember.html">招贤纳士</a><span>|</span><a href="?page-nonmember.html">全球网络</a></div>";s:6:"expire";i:0;}