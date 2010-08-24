<?php exit(); ?>a:2:{s:5:"value";s:746:"<div class="CartInfoItems" style="text-align:center;">
目前选购商品共 <span><?php echo $this->_vars['cartCount']; ?></span> 种 <span><?php echo $this->_vars['cartNumber']; ?></span> 件&nbsp;&nbsp;&nbsp;&nbsp;合计:<span><?php echo app::get('ectools')->model('currency')->changer($this->_vars['cartTotalPrice']); ?></span>
  
      <div class='btnBar clearfix' style="margin-top:10px; padding-left:60px;">
        <a class="actbtn btn-viewcart" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_cart','act' => 'index')); ?>" target="_blank">进入购物车</a>
        <span class="actbtn btn-continue" onclick="$(this).getParent('.dialog').remove();" >继续购物</span>
        
      </div> 
</div>";s:6:"expire";i:0;}