<?php exit(); ?>a:2:{s:5:"value";s:1140:"<div class="section" id="cart-items">
  <div class="FormWrap" id="cartItems">
  <?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"site/cart/cart_items.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"site/cart/cart_solution.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"site/cart/cart_total.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
</div>
</div>

<div class="CartBtn clearfix" style="margin-bottom:5px;">
   <div class="span-auto"><a href="./" class="actbtn btn-return" >&laquo;继续购物</a></div>
   <div class="span-auto"><a id="clearCart" class="actbtn btn-clearcat" href="javascript:Cart.empty('<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_cart','act' => 'removeCart','arg0' => all)); ?>');">清空购物车</a></div>
   <div class="span-auto floatRight"> <input type="submit" class="actbtn btn-next" value="下单结算&raquo;" ></input></div>
</div>

";s:6:"expire";i:0;}