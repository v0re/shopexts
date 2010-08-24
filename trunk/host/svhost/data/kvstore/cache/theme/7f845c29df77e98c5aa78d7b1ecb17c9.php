<?php exit(); ?>a:2:{s:5:"value";s:4036:"<?php  echo $this->_fetch_tmpl_compile_require("block/header.html"); ?>
<div class="AllWrapInside clearfix">

  <div class="mainColumn pageMain"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('nav', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['nav'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/nav/widget_nav.php');$this->__widgets_exists['b2c']['nav']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_nav_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_nav($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'55');ob_start();?><div class="Navigation">您当前的位置：
  
  <?php $this->_env_vars['foreach']["nav"]=array('total'=>count($this->_vars['data']),'iteration'=>0);foreach ((array)$this->_vars['data'] as $this->_vars['item']){
                        $this->_env_vars['foreach']["nav"]['first'] = ($this->_env_vars['foreach']["nav"]['iteration']==0);
                        $this->_env_vars['foreach']["nav"]['iteration']++;
                        $this->_env_vars['foreach']["nav"]['last'] = ($this->_env_vars['foreach']["nav"]['iteration']==$this->_env_vars['foreach']["nav"]['total']);
 if( $this->_vars['item']['title'] && $this->_vars['item']['link'] ){  if( $this->_env_vars['foreach']['nav']['last'] ){ ?>
  <span class="now"><?php echo $this->_vars['item']['title']; ?></span>
  <?php }else{ ?>
  <span><a href="<?php echo $this->_vars['item']['link']; ?>" alt="<?php echo $this->_vars['item']['tips']; ?>" title="<?php echo $this->_vars['item']['tips']; ?>"><?php echo $this->_vars['item']['title']; ?></a></span>
  <span>&raquo;</span></td>
  <?php }  }  } unset($this->_env_vars['foreach']["nav"]); ?>
  
</div><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_55">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata;   echo  $this->_fetch_compile_include('site', 'splash/success.html', array());  ?></div>
<div class="sideColumn pageSide"> <?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'treenum' => '2',
  'treelistnum' => '68',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('treelist', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['treelist'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/treelist/widget_treelist.php');$this->__widgets_exists['b2c']['treelist']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_treelist_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_treelist($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'56');ob_start();?><div class="TreeList">
<?php echo $this->_vars['data']; ?>
</div><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_56">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?> </div>
</div>
<?php  echo $this->_fetch_tmpl_compile_require("block/footer.html"); ?>";s:6:"expire";i:0;}