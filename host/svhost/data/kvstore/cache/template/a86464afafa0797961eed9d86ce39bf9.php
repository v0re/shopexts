<?php exit(); ?>a:2:{s:5:"value";s:4327:"<?php if( count($this->_vars['product']['spec_desc_info'])>1 ){ ?>
<li class="addcart buy-select" style="z-index:<?php echo $this->_vars['zindex']; ?>">
<?php if( $this->_vars['showtextbuy'] ){ ?><a class='lnk'>加入购物车</a><?php } ?>
<div class="buy-select-list" style="display:none;">
<h3><?php if( count($this->_vars['product']['spec_desc_info']) == 0 ){ ?>该商品暂时无货<?php }else{ ?>请选择规格<?php } ?></h3>
<table width="100%">
<?php if($this->_vars['product']['spec_desc_info'])foreach ((array)$this->_vars['product']['spec_desc_info'] as $this->_vars['item']){ ?>
  <tr>
    <td width="100"><?php echo $this->_vars['item']['spec_info']; ?></td>
    <td ><span class="fontcolorRed fontbold"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['item']['price']); ?></span></td>
    <td width="100" align="right" style="vertical-align:middle">
    <?php if( $this->_vars['addcart_disabled'] != 1 ){ ?>

      <a type="g" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_cart,'act' => addToCart,'arg0' => goods,'arg1' => $this->_vars['product']['goods_id'],'arg2' => $this->_vars['item']['product_id'],'arg3' => '1')); ?>" buy="<?php echo $this->_vars['product']['goods_id']; ?>sdfsdf" product="<?php echo $this->_vars['product_id']; ?>"<?php if( $this->_vars['setting']['buytarget'] == 2 ){ ?> target="_blank_cart"<?php }elseif( $this->_vars['setting']['buytarget'] == 3 ){ ?> target="_dialog_minicart"<?php } ?> title="加入购物车" rel="nofollow" class="btnbuy">购买</a>

    <?php } ?>
    </td>
  </tr>
<?php } ?>
</table>
</div>
<?php }else{  if( $this->_vars['addcart_disabled'] != 1 ){ ?>

    <li class="addcart">
<?php if($this->_vars['product']['spec_desc_info'])foreach ((array)$this->_vars['product']['spec_desc_info'] as $this->_vars['item']){ ?>
    <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_cart,'act' => addToCart,'arg0' => goods,'arg1' => $this->_vars['product']['goods_id'],'arg2' => $this->_vars['item']['product_id'],'arg3' => '1')); ?>" type="g" buy="<?php echo $this->_vars['product']['goods_id']; ?>" class="listact"<?php if( $this->_vars['setting']['buytarget'] == 2 ){ ?> target="_blank_cart"<?php }elseif( $this->_vars['setting']['buytarget'] == 3 ){ ?> target="_dialog_minicart"<?php } ?> title="加入购物车" rel="nofollow">加入购物车</a>
<?php break;  } ?>
    </li>


  <?php }  } ?>
</li>
<?php if( $this->_vars['product']['promotions'] && ($this->_vars['product']['promotions'] >0)  ){ ?>
    <li <?php if( $this->_vars['login']!="nologin" ){ ?>star="<?php echo $this->_vars['product']['goods_id']; ?>"<?php } ?>
         title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['product']['name'],html); ?>" class="star-off">
         <a  <?php if( $this->_vars['login']=="nologin" ){ ?> href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_passport",'act' => "login")); ?>"
             <?php }else{ ?>href="#" onclick="return false;"
             <?php } ?>
         class="listact" title="促销优惠" rel="nofollow">促销优惠</a>
    </li>
<?php } ?>
<li <?php if( $this->_vars['login']!="nologin" ){ ?>star="<?php echo $this->_vars['product']['goods_id']; ?>"<?php } ?> title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['product']['name'],html); ?>" class="star-off"><a  <?php if( $this->_vars['login']=="nologin" ){ ?> href="<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_passport",'act' => "login")); ?>" <?php }else{ ?>href="#" onclick="return false;"<?php } ?> class="listact" title="加入收藏" rel="nofollow">加入收藏</a></li>
<li class="vdetail zoom"><a title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['product']['name'],html); ?>" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => index,'arg0' => $this->_vars['product']['goods_id'])); ?>" pic='<?php echo kernel::single('base_view_helper')->modifier_storager(((isset($this->_vars['product']['big_pic']) && ''!==$this->_vars['product']['big_pic'])?$this->_vars['product']['big_pic']:app)); ?>' target="_blank" class="listact" title="查看详细">查看详细</a></li>
";s:6:"expire";i:0;}