<?php exit(); ?>a:2:{s:5:"value";s:2774:"<?php if( $this->_vars['side'] == 'sidepanel' ){  }  if( $this->_vars['side'] == 'leftpanel' ){ ?>


<!--<div class="side-title"><?php echo $this->_vars['workground']['menu_title']; ?></div> -->
<?php if( count($this->_vars['nogroup']['menu']) > 0 ){ ?>
<div class="side-bx">
    <div class="side-bx-bd">
        <ul><?php if($this->_vars['nogroup']['menu'])foreach ((array)$this->_vars['nogroup']['menu'] as $this->_vars['menu']){ ?> 
            <li><a target="<?php echo $this->_vars['menu']['target']; ?>" href="index.php?<?php echo $this->_vars['menu']['menu_path']; ?>" mid="<?php echo $this->_vars['menu']['menu_id']; ?>"><?php echo $this->_vars['menu']['menu_title']; ?></a></li>
            <?php } ?>
        </ul>
    </div>
</div>
<?php }  $this->_env_vars['foreach'][side_menus]=array('total'=>count($this->_vars['menus_data']),'iteration'=>0);foreach ((array)$this->_vars['menus_data'] as $this->_vars['menus']){
                        $this->_env_vars['foreach'][side_menus]['first'] = ($this->_env_vars['foreach'][side_menus]['iteration']==0);
                        $this->_env_vars['foreach'][side_menus]['iteration']++;
                        $this->_env_vars['foreach'][side_menus]['last'] = ($this->_env_vars['foreach'][side_menus]['iteration']==$this->_env_vars['foreach'][side_menus]['total']);
?>  
<div class="side-bx<?php if( $this->_env_vars['foreach']['side_menus']['first'] ){ ?> first<?php } ?>">
    <div class="side-bx-title">
        <h3><?php echo $this->_vars['menus']['menugroup']; ?></h3>
    </div>
    <div class="side-bx-bd">
        <ul><?php if($this->_vars['menus']['menu'])foreach ((array)$this->_vars['menus']['menu'] as $this->_vars['menu']){ ?> 
            <li><a target="<?php echo $this->_vars['menu']['target']; ?>" href="index.php?<?php echo $this->_vars['menu']['menu_path']; ?>" mid="<?php echo $this->_vars['menu']['menu_id']; ?>"><?php echo $this->_vars['menu']['menu_title']; ?></a></li>
            <?php } ?>
        </ul>
    </div>
</div>
<?php } unset($this->_env_vars['foreach'][side_menus]); ?>  



<script>
(function(){
   /* var cur = '<?php echo $this->_vars['workground']['menu_title']; ?>';
	$$('.head-nav .wg').each(function(wg){
				  
		if(cur&&wg.getElement('span:contains("<?php echo $this->_vars['workground']['menu_title']; ?>")')){
			wg.addClass('current');
		}else{
			wg.removeClass('current');
		}
		
	});  */
	 
	$$('.side-bx-title').addEvent('click', function(e){
		e.stop();
		var bx = this.getParent(),
			bd = this.getNext('.side-bx-bd');
		if(bx.hasClass('side-bx-toggled')){
			bd.show();
			bx.removeClass('side-bx-toggled');
		}else{
			bd.hide();
			bx.addClass('side-bx-toggled');
		}
	});
})();
window.Breadcrumbs = "<?php echo $this->_vars['dataid']; ?>";
</script>
<?php } ?>";s:6:"expire";i:0;}