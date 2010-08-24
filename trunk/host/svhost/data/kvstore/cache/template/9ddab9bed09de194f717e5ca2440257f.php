<?php exit(); ?>a:2:{s:5:"value";s:304:"<div class="note">
<?php echo $this->_vars['img'];  echo $this->_vars['msg']; ?>
</div>
<script>
window.addEvent('domready', function(){
	<?php if( $_GET['finder_id'] ){ ?>
	opener.window.finderGroup['<?php echo $_GET['finder_id']; ?>'].refresh();
	<?php } ?>

   window.close();
});
</script>";s:6:"expire";i:0;}