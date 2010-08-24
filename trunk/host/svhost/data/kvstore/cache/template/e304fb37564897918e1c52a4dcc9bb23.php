<?php exit(); ?>a:2:{s:5:"value";s:2731:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><div class="widgets clearfix"> 
	<div class="wcol l-1 flt">
		<?php if($this->_vars['widgets'])foreach ((array)$this->_vars['widgets'] as $this->_vars['widget']){  if( $this->_vars['widget']['width']=='normal' or $this->_vars['widget']['width']=='l-1' ){ ?> 
			<div class="item <?php echo $this->_vars['widget']['className']; ?>">
				<div class="dashbd-action"><h3>&nbsp;<?php echo $this->_vars['widget']['title']; ?></h3></div>
				<div class="dashbd-bd"><?php echo $this->_vars['widget']['html']; ?></div>
			</div>  
		<?php }  } ?>
	</div>
	<div class="wcol l-2 flt">
		<?php if($this->_vars['widgets'])foreach ((array)$this->_vars['widgets'] as $this->_vars['widget']){  if( $this->_vars['widget']['width']=='l-2' ){ ?>
		  <div class="item <?php echo $this->_vars['widget']['className']; ?>">
			 <div class="dashbd-action"><h3>&nbsp;<?php echo $this->_vars['widget']['title']; ?></h3></div>
			 <div class="dashbd-bd"><?php echo $this->_vars['widget']['html']; ?></div>
		   </div> 
		 <?php }  } ?>
	</div>  
</div>

<?php $this->_tag_stack[] = array('area', array('inject' => ".mainFoot")); $this->__view_helper_model['base_view_helper']->block_area(array('inject' => ".mainFoot"), null, $this); ob_start(); ?>
<div class="note clearfix" style="margin-bottom:0;height:20px">
	 <div class="span-auto">
		  当前版本：<?php echo $this->_vars['deploy']['product_name']; ?>  V<?php echo $this->_vars['deploy']['product_version']; ?> <span class="lnk">检查更新</span>
	  </div>     
	<div class="user-tip frt">
		<b>你知道么?&nbsp;&nbsp;&nbsp;&nbsp;</b><span id="dashboard-tip"><?php echo $this->_vars['tip']; ?></span>
		 &nbsp;<span class="lnk" onclick="$('dashboard-tip').set('opacity',0);new Request.HTML({onComplete:function(){$('dashboard-tip').set('opacity',1);},update:'dashboard-tip',evalScripts:false}).get('index.php?app=desktop&ctl=dashboard&act=fetch_tip')">再一条</span>
	</div>
</div>

<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_area($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>  

<input type="hidden" id="dashboard-side-r-trigger"/>
<script>
    //new Equalizer('.span-9').equalize();
   $$('.widgets .valigntop').each(function(item){
			item.injectTop(item.getParent());
});
   new Side_R('index.php?app=desktop&ctl=dashboard&act=advertisement',{width:250,title:'<span class="font9px">advertisement</span>',trigger:$('dashboard-side-r-trigger')});
</script>



";s:6:"expire";i:0;}