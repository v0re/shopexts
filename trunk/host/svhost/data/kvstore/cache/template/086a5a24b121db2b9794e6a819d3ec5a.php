<?php exit(); ?>a:2:{s:5:"value";s:2317:"<?php if( $this->_vars['type']['dialog'] ){ ?>
<span>
	<span onclick="new Dialog($E('div',this.parentNode).clone().setStyle('display',''),{modal:true,title:'帮助 <?php echo $this->_vars['label']; ?>'});"><img src="<?php echo $this->_vars['type']['icon']; ?>" /><?php if( $this->_vars['label'] ){  echo $this->_vars['label'];  } ?></span>
	<div style="display:none; padding:15px;">
	<p><?php echo $this->_vars['text']; ?></p>
	<?php if( $this->_vars['url'] ){ ?><p><a target="blank" href="<?php echo $this->_vars['url']; ?>">更多</a></p><?php }  if( $this->_vars['movie'] ){ ?><p><a href="http://docs.shopex.cn/videos/<?php echo $this->_vars['movie']; ?>">观看视频</a></p><?php } ?>
	</div>
</span>
<?php }else{  if( $this->_vars['docid'] ){ ?>
		<a href="http://click.shopex.cn/free_click.php?id=<?php echo $this->_vars['docid']; ?>" target="_blank" style="margin:0;padding:0" title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['text'],"html"); ?>">
		<img src="<?php echo $this->_vars['type']['icon']; ?>" style="cursor:pointer;" title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['text'],"html"); ?>" /><?php if( $this->_vars['label'] ){  echo $this->_vars['label'];  } ?>
		</a>
	<?php }elseif( $this->_vars['href'] ){ ?>
		<a href="<?php echo $this->_vars['href']; ?>" target="_blank" style="margin:0;padding:0" title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['text'],"html"); ?>">
		<img src="<?php echo $this->_vars['type']['icon']; ?>" style="cursor:pointer;" title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['text'],"html"); ?>" /><?php if( $this->_vars['label'] ){  echo $this->_vars['label'];  } ?>
		</a>
	<?php }else{ ?>
		<span id="<?php echo $this->_vars['dom_id']; ?>">
		<img src="<?php echo $this->_vars['type']['icon']; ?>" /><?php if( $this->_vars['label'] ){  echo $this->_vars['label'];  } ?>
		</span>
		<?php if( $this->_vars['text'] ){ ?>
		<script>
		$('<?php echo $this->_vars['dom_id']; ?>').store('tip:title','').store('tip:text',"<?php echo kernel::single('base_view_helper')->modifier_replace($this->_vars['text'],'"','\\"'); ?>");
		Xtip.attach($('<?php echo $this->_vars['dom_id']; ?>'));
		</script>
		<?php }  }  } ?>";s:6:"expire";i:0;}