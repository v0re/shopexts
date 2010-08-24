<?php exit(); ?>a:2:{s:5:"value";s:376:"<!-- 赠品 -->
<?php if( $this->_vars['item_other'] ){  if($this->_vars['item_other'])foreach ((array)$this->_vars['item_other'] as $this->_vars['section']){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include($this->_vars['section']['app'],$this->_vars['section']['file'], array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  }  } ?>
<!-- end -->";s:6:"expire";i:0;}