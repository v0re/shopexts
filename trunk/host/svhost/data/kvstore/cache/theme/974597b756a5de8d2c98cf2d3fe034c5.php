<?php exit(); ?>a:2:{s:5:"value";s:1993:"<?php  echo $this->_fetch_tmpl_compile_require("block/header.html"); ?>
<div class="mains">
   <div class="left_body"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'treenum' => '3',
  'treelistnum' => '68',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('treelist', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['treelist'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/treelist/widget_treelist.php');$this->__widgets_exists['b2c']['treelist']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_treelist_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_treelist($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'83');ob_start();?><div class="TreeList">
<?php echo $this->_vars['data']; ?>
</div><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'帮助中心','widgets_id'=>'site_widgetsid_83','widgets_classname'=>'');?><div class="border2 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
   <div class="right_body"><?php  echo  $this->_fetch_compile_include('site', 'splash/success.html', array());  ?></div>
</div>
<?php  echo $this->_fetch_tmpl_compile_require("block/footer.html"); ?>";s:6:"expire";i:0;}