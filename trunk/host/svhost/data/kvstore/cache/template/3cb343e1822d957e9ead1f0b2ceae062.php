<?php exit(); ?>a:2:{s:5:"value";s:5222:"<?php if( $this->_vars['aCart']['object']['goods'] ){ ?>
<h3>购买的商品</h3>
<div id="goodsbody" class="division">
    <table width="100%" cellpadding="3" cellspacing="0" class="liststyle cart-list">
      <col class="span-2 "></col>
      <col class="span-auto"></col>
      <col class="span-2"></col>
      <col class="span-2"></col>
      <col class="span-2"></col>
      <col class="span-1"></col>
      <col class="span-2"></col>
      <?php if( !$this->_vars['checkout'] ){ ?><col class="span-2"></col><?php } ?>
      <thead>
        <tr>
          <th>图片</th>
          <th>商品名称</th>
          <th>商品积分</th>
          <th>销售价格</th>
          <th>优惠价格</th>
          <th>数量</th>
          <th>小计</th>
          <?php if( !$this->_vars['checkout'] ){ ?><th>删除</th><?php } ?>
        </tr>
      </thead>
      <tbody >
      
      <?php if($this->_vars['aCart']['object']['goods'])foreach ((array)$this->_vars['aCart']['object']['goods'] as $this->_vars['goods']){ ?>
      <tr urlupdate="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_cart','act' => 'updateCart','arg0' => 'goods','arg1' => $this->_vars['item']['link_key'])); ?>" urlremove="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_cart','act' => 'removeCart','arg0' => 'goods')); ?>" number="<?php echo $this->_vars['goods']['store']['real']; ?>" g_name="<?php echo $this->_vars['goods']['store']['name']; ?>" floatstore="<?php echo $this->_vars['goods']['obj_items']['products']['0']['floatstore']; ?>" >
        <td>
            <div class='cart-product-img' isrc="<?php echo kernel::single('base_view_helper')->modifier_storager(((isset($this->_vars['goods']['obj_items']['products']['0']['default_image']['thumbnail']) && ''!==$this->_vars['goods']['obj_items']['products']['0']['default_image']['thumbnail'])?$this->_vars['goods']['obj_items']['products']['0']['default_image']['thumbnail']:app),'s'); ?>" ghref='<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => "index",'arg0' => $this->_vars['goods']['obj_items']['products']['0']['goods_id'])); ?>' style='width:50px;height:50px;'>
                <img src='statics/loading.gif'/>
             </div>
        </td>
        <td style="text-align:left"><a target="_blank" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'arg0' => $this->_vars['goods']['obj_items']['products']['0']['goods_id'])); ?>"><?php echo $this->_vars['goods']['obj_items']['products']['0']['new_name']; ?></a></td>
        <td><?php echo $this->_vars['goods']['obj_items']['products']['0']['gain_score']; ?></td>
        <td class="mktprice1"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods']['obj_items']['products']['0']['price']['price']); ?></td>
        <td><?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods']['obj_items']['products']['0']['price']['buy_price']); ?></td>
        <?php if( !$this->_vars['checkout'] ){ ?>
            <td>
                <div class="Numinput">
                    <input type="text" class="_x_ipt textcenter" name="modify_quantity[<?php echo $this->_vars['goods']['obj_ident']; ?>][quantity]" size="3" value="<?php echo $this->_vars['goods']['quantity']; ?>"  onchange="Cart.ItemNumUpdate(this,this.value,event);" />
                    <span class="numadjust increase" ></span>
                    <span class="numadjust decrease"></span><h3 class="t" style="display:none">(提示:需要备货)</h3>
                </div>
                <?php if( $this->_vars['goods']['obj_items']['products']['0']['floatstore'] ){ ?>可输入小数<?php }  if( $this->_vars['goods']['store']['less']<$this->_vars['goods']['obj_items']['products']['0']['min_buy'] ){ ?><h6 class="fontcolorRed">(提示:该商品不足起订量!起订量为：<?php echo $this->_vars['goods']['obj_items']['products']['0']['min_buy']; ?>)</h3><?php } ?>
            </td>
        <?php }else{ ?>
            <td><?php echo $this->_vars['goods']['quantity']; ?></td>
        <?php } ?>
        <td class="itemTotal fontcolorRed"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods']['obj_items']['products']['0']['subtotal'] - $this->_vars['goods']['discount_amount_prefilter']); ?></td>
        <?php if( !$this->_vars['checkout'] ){ ?><td><span><?php echo $this->ui()->img(array('src' => 'icons/icon_delete.gif','app' => b2c,'alt' => '删除','style' => 'cursor:pointer','class' => "delItem"));?></span></td><?php } ?>
       </tr>
       
             <?php if( $this->_vars['item_goods_section'] ){  if($this->_vars['item_goods_section'])foreach ((array)$this->_vars['item_goods_section'] as $this->_vars['section']){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include($this->_vars['section']['app'],$this->_vars['section']['file'], array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  }  }  } ?>
       </tbody>
    </table>
    
</div>
<?php }  if( $this->_vars['aCart']['cart_status']=='false' ){ ?>
<div id="error_str" style="display:none;">
    <?php echo $this->_vars['aCart']['cart_error_html']; ?>
</div>
<?php } ?>

";s:6:"expire";i:0;}