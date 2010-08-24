<?php exit(); ?>a:2:{s:5:"value";s:657:"<h3>配件</h3>
<div class="tableform">
<?php echo $this->ui()->button(array('label' => "添加配件",'app' => "desktop",'type' => "button",'onclick' => "goodsEditor.adj.addGrp.bind(goodsEditor)('goods-adj')",'icon' => "btn_add.gif"));?>
<div class="adjs" id="goods-adj">
<?php if($this->_vars['goods']['adjunct'])foreach ((array)$this->_vars['goods']['adjunct'] as $this->_vars['key'] => $this->_vars['adjunct']){  if( $this->_vars['adjunct']['type'] ){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',"admin/goods/detail/adj/row.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  }  } ?>
</div>
</div>";s:6:"expire";i:0;}