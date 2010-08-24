<?php exit(); ?>a:2:{s:5:"value";s:941:"<?php if( $this->_vars['aCart']['promotion']['goods'] ){ ?>
<div class="cart-order-solution-list">
    <div class="division">
    <h3>使用的商品促销规则</h3>
        <table width="100%" cellpadding="3" cellspacing="0" class="liststyle cart-list">
           <col class="span-auto"></col>
          <thead>
            <tr>
              <th>描述</th>
            </tr>
          </thead>
        <tbody >
            <?php if($this->_vars['aCart']['promotion']['goods'])foreach ((array)$this->_vars['aCart']['promotion']['goods'] as $this->_vars['item']){ ?>
            <tr urlremove="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_cart','act' => 'removeCart','arg0' => 'coupon')); ?>">
                <td style="text-align:left;">
                    <?php echo $this->_vars['item']['desc']; ?>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>
<?php } ?>";s:6:"expire";i:0;}