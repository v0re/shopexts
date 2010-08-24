<?php exit(); ?>a:2:{s:5:"value";s:3010:"<?php $this->__view_helper_model['site_view_helper'] = kernel::single('site_view_helper'); ?><div id="foot">
   <div class="footmain">
      <div class="footlogo"><div class="hidd"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'usercustom' => '<a href="http://www.saffidesign.com">做网站</a>',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('usercustom', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));$this->_vars = array('widgets_id'=>'58');ob_start();?><a href="http://www.saffidesign.com">做网站</a><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_58">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '',
  'ad_pic_height' => '',
  'ad_pic' => '%THEME%/images/ybgx_22.jpg',
  'ad_pic_link' => '',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'57');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_57">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
      <div class="footinfo"><?php echo $this->__view_helper_model['site_view_helper']->function_footer(array(), $this);?></div>
      <div class="clear"></div>
   </div>
</div>
</body>
</html>";s:6:"expire";i:0;}