<?php exit(); ?>a:2:{s:5:"value";s:1730:"<div id="cart-coupon-list">
<?php if( $this->_vars['aCart']['object']['coupon'] ){ ?>
<h3>使用的优惠券</h3>
<div>
    <table width="100%" cellpadding="3" cellspacing="0" class="liststyle cart-list">
       <col class="span-5"></col>
       <col class="span-auto"></col>
       <col class="span-1"></col>
       <?php if( !$this->_vars['checkout'] ){ ?><col class="span-2"></col><?php } ?>
      <thead>
        <tr>
          <th>优惠券</th>
          <th>描述</th>
          <th>是否适用</th>
          <?php if( !$this->_vars['checkout'] ){ ?><th>删除</th><?php } ?>
        </tr>
      </thead>
    <tbody >
      <?php if($this->_vars['aCart']['object']['coupon'])foreach ((array)$this->_vars['aCart']['object']['coupon'] as $this->_vars['coupon']){ ?>
        <tr urlremove="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_cart','act' => 'removeCart','arg0' => 'coupon')); ?>">
            <td style="text-align:left;">
                <?php if( !$this->_vars['checkout'] ){ ?><input type="hidden" name="modify_quantity[<?php echo $this->_vars['coupon']['obj_ident']; ?>]" value="1" /><?php }  echo $this->_vars['coupon']['name']; ?>
            </td>
            <td><?php echo $this->_vars['coupon']['description']; ?></td>
            <td><?php if( $this->_vars['coupon']['used'] ){ ?><font color='green'>是</font><?php }else{ ?><font color='red'>否</font><?php } ?></td>
            <?php if( !$this->_vars['checkout'] ){ ?><td><span><?php echo $this->ui()->img(array('src' => 'icons/icon_delete.gif','app' => b2c,'alt' => '删除','style' => 'cursor:pointer','class' => "delItem"));?></span></td><?php } ?>
        </tr>
      <?php } ?>
    </table>
</div>
<?php } ?>


</div>
";s:6:"expire";i:0;}