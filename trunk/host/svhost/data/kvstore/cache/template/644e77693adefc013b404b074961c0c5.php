<?php exit(); ?>a:2:{s:5:"value";s:422:"<?php if( $this->_vars['solution_section'] ){ ?>
<div id="cart-solution-list">
    <?php if($this->_vars['solution_section'])foreach ((array)$this->_vars['solution_section'] as $this->_vars['section']){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include($this->_vars['section']['app'],$this->_vars['section']['file'], array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  } ?>
</div>
<?php } ?>";s:6:"expire";i:0;}