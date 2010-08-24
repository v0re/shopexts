<?php exit(); ?>a:2:{s:5:"value";s:3906:"<?php if( $this->_vars['goods']['adjunct'] ){ ?>
  <tr>
    <td><?php echo $this->_vars['goods']['obj_items']['products']['0']['new_name']; ?> ( 配件 )</td>
    <td colspan="7">
        <table width="100%" cellpadding="2" cellspacing="0" class="liststyle cart-list">
              <col class="span-2 "></col>
              <col class="span-auto"></col>
              <col class="span-2"></col>
              <col class="span-2"></col>
              <col class="span-2"></col>
              <col class="span-1"></col>
              <col class="span-2"></col>
              <?php if( !$this->_vars['checkout'] ){ ?><col class="span-2"></col><?php }  if($this->_vars['goods']['adjunct'])foreach ((array)$this->_vars['goods']['adjunct'] as $this->_vars['adjunct']){ ?>
            <tr  urlupdate="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_cart','act' => 'updateCart','arg0' => 'goods','arg1' => $this->_vars['item']['link_key'])); ?>" urlremove="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_cart','act' => 'removeCart','arg0' => 'goods')); ?>" number="<?php echo $this->_vars['adjunct']['store']['real']; ?>" g_name="<?php echo $this->_vars['adjunct']['store']['name']; ?>" floatstore="<?php echo $this->_vars['adjunct']['floatstore']; ?>" >
            <td>
                <div class='cart-product-img' isrc="<?php echo kernel::single('base_view_helper')->modifier_storager(((isset($this->_vars['adjunct']['default_image']['thumbnail']) && ''!==$this->_vars['adjunct']['default_image']['thumbnail'])?$this->_vars['adjunct']['default_image']['thumbnail']:app),'s'); ?>" ghref='<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => "index",'arg0' => $this->_vars['adjunct']['goods_id'])); ?>' style='width:50px;height:50px;'>
                    <img src='statics/loading.gif'/>
                 </div>
            </td>
            <td style="text-align:left"><a target="_blank" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'arg0' => $this->_vars['adjunct']['goods_id'])); ?>"><?php echo $this->_vars['adjunct']['new_name']; ?></a></td>
            <td><?php echo $this->_vars['adjunct']['gain_score']; ?></td>
            <td class="mktprice1"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['adjunct']['price']['price']); ?></td>
            <td><?php echo app::get('ectools')->model('currency')->changer($this->_vars['adjunct']['price']['buy_price']); ?> </td>
            <?php if( !$this->_vars['checkout'] ){ ?>
                <td>
                    <div class="Numinput">
                        <input type="text" class="_x_ipt textcenter" name="modify_quantity[<?php echo $this->_vars['goods']['obj_ident']; ?>][<?php echo $this->_vars['adjunct']['group_id']; ?>][<?php echo $this->_vars['adjunct']['product_id']; ?>][quantity]" size="3" value="<?php echo $this->_vars['adjunct']['quantity']; ?>"  onchange="Cart.ItemNumUpdate(this,this.value,event);" />
                        <span class="numadjust increase" ></span>
                        <span class="numadjust decrease"></span><h3 class="t" style="display:none">(提示:需要备货)</h3>
                    </div>
                </td>
            <?php }else{ ?>
                <td><?php echo $this->_vars['adjunct']['quantity']; ?></td>
            <?php } ?>
            <td class="itemTotal fontcolorRed"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['adjunct']['subtotal']); ?></td>
            <?php if( !$this->_vars['checkout'] ){ ?><td><span><?php echo $this->ui()->img(array('src' => 'icons/icon_delete.gif','app' => b2c,'alt' => '删除','style' => 'cursor:pointer','class' => "delItem"));?></span></td><?php } ?>
           </tr>
         <?php } ?>
         
       </table>
   </td>
  </tr>
<?php } ?>";s:6:"expire";i:0;}