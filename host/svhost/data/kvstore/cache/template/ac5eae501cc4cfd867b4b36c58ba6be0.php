<?php exit(); ?>a:2:{s:5:"value";s:1097:"<ul class="clearfix">
	<?php $this->_env_vars['foreach'][pdata]=array('total'=>count($this->_vars['data']),'iteration'=>0);foreach ((array)$this->_vars['data'] as $this->_vars['key'] => $this->_vars['item']){
                        $this->_env_vars['foreach'][pdata]['first'] = ($this->_env_vars['foreach'][pdata]['iteration']==0);
                        $this->_env_vars['foreach'][pdata]['iteration']++;
                        $this->_env_vars['foreach'][pdata]['last'] = ($this->_env_vars['foreach'][pdata]['iteration']==$this->_env_vars['foreach'][pdata]['total']);
 if( $this->_vars['item']['addon']>0 ){ ?> 
	<li class="<?php if( ($_GET['view'] && $_GET['view']==$this->_vars['key']) or (!$_GET['view']&&$this->_env_vars['foreach']['pdata']['iteration']==1) ){ ?>current<?php }  if( $this->_env_vars['foreach']['pdata']['last'] ){ ?> last<?php } ?>"><a href="<?php echo $this->_vars['item']['href']; ?>">
		<span><?php echo $this->_vars['item']['label']; ?>(<?php echo $this->_vars['item']['addon']; ?>)</span></a>
	</li> 
	<?php }  } unset($this->_env_vars['foreach'][pdata]); ?>  
</ul>";s:6:"expire";i:0;}