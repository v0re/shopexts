<?php exit(); ?>a:2:{s:5:"value";s:1536:"
<div id="catalog-x" class="catalog">
	<div class="cat-path">
		<?php if( $this->_vars['cat_path'] ){ ?>
		<div class="clearfix">
			<?php if($this->_vars['cat_path'])foreach ((array)$this->_vars['cat_path'] as $this->_vars['key'] => $this->_vars['item']){ ?> 
			<div class="span-auto">
				&laquo;<a pid="<?php echo $this->_vars['item']['parent_id']; ?>" class="lnk subs" href="javascript:void(0);"><span><?php echo $this->_vars['item']['cat_name']; ?></span></a>
			</div>
			<?php } ?>
		</div>
		<?php }else{ ?>
		&laquo;<a class="cat-no-child subs lnk" href="javascript:void(0)"><span>分类不限</span></a>
		<?php } ?>
	</div>
	<div class="cat-group">
		<?php if($this->_vars['cats'])foreach ((array)$this->_vars['cats'] as $this->_vars['key'] => $this->_vars['item']){ ?>
		<div class="clearfix" has_child="<?php if( $this->_vars['item']['child_count']>0 ){ ?>1<?php }else{ ?>0<?php } ?>" type_id="<?php echo $this->_vars['item']['type_id']; ?>" pid="<?php echo $this->_vars['item']['parent_id']; ?>" id="<?php echo $this->_vars['item']['cat_id']; ?>">
			 <div class="span-4"><a class="cat-child lnk" href="javascript:void(0);"><span><?php echo $this->_vars['item']['cat_name']; ?></span></a>
			 </div>
			<?php if( $this->_vars['item']['child_count']>0 ){ ?>
			  <div class="span-auto">
				<a class="subs" id="<?php echo $this->_vars['item']['cat_id']; ?>" title="点击选择子类" href="javascript:void(0);">&nbsp;
				</a>
			  </div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>	
</div>
";s:6:"expire";i:0;}