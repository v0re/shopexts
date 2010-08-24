<?php exit(); ?>a:2:{s:5:"value";s:1511:"<?php if( $this->_vars['item_other'] ){  if($this->_vars['item_other'])foreach ((array)$this->_vars['item_other'] as $this->_vars['section']){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include($this->_vars['section']['app'],$this->_vars['section']['file'], array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  }  } ?>
<div class="clear"></div>

<?php if( !$this->_vars['checkout'] ){ ?>
<div class="flt">
     <div id='cart-coupon-add'>
         使用优惠券:<?php echo $this->ui()->input(array('type' => "text",'name' => "coupon",'size' => "25",'value' => "请输入优惠券号码",'onclick' => "this.value=(this.value=='请输入优惠券号码')?'':this.value"));?> <input id='cart-coupon-submitBtn' type="button" value="确定" /> 
         <script>
          $('cart-coupon-submitBtn').addEvent('click',function(e){
              e.stop();
              new Element('form',{method:'post',action:'<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_cart",'act' => "addToCart",'arg0' => "coupon")); ?>'}).adopt($('cart-coupon-add').clone()).inject(document.body).submit();
          });
         </script>
     </div>
</div>
<?php } ?>

<div class="frt" style="margin-right:20px;line-height:20px;">
    总额：<span style="padding-right: 15px;" class="totalprice price1"><?php echo app::get('ectools')->model('currency')->changer(($this->_vars['aCart']['subtotal'] - $this->_vars['aCart']['discount_amount'])); ?></span>
</div>
<div class="clear"></div>";s:6:"expire";i:0;}