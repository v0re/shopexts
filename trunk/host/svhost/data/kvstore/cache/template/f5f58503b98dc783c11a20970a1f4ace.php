<?php exit(); ?>a:2:{s:5:"value";s:914:"<div id="gEditor-GCat-category" dropmenu="gEditor-GCat-list" class="object-select clearfix" >	
	<div class="label" id="<?php echo $this->_vars['id']; ?>">
	<?php if( $this->_vars['category'] ){  echo $this->_vars['category']['cat_name'];  }else{ ?>
	分类不限
	<?php } ?>
	
	</div>
	<div class="handle">&nbsp;</div><input type="hidden"  id="gEditor-GCat-input" name=<?php echo $this->_vars['params']['name']; ?> value="<?php echo $this->_vars['category']['cat_id']; ?>" />
</div>					
<div id="gEditor-GCat-list" class="x-drop-menu" style="width:200px"></div>
<script>
window.addEvent('domready',function(){
	new DropMenu($('gEditor-GCat-category'),{offset:{y:20},stopEl:true,onLoad:function(el){
		new CatalogSelect(el,{updateMain:this.menu,onCallback:<?php if( $this->_vars['params']['callback'] ){  echo $this->_vars['params']['callback'];  }else{ ?>$empty<?php } ?>});
	}});
});
	
</script>";s:6:"expire";i:0;}