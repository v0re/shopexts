<?php exit(); ?>a:2:{s:5:"value";s:758:"<?php if( $this->_vars['aCart']['promotion']['order'] ){ ?>
<div class="cart-order-solution-list">
    <h3>使用的订单促销规则</h3>
    <div class="division">
        <table width="100%" cellpadding="3" cellspacing="0" class="liststyle cart-list">
          <thead>
            <tr>
              <th>描述</th>
            </tr>
          </thead>
        <tbody>
          <?php if($this->_vars['aCart']['promotion']['order'])foreach ((array)$this->_vars['aCart']['promotion']['order'] as $this->_vars['item']){ ?>
            <tr>
                <td>
                    <?php echo $this->_vars['item']['desc']; ?> 
                </td>
            </tr>
          <?php } ?>
        </table>
    </div>
</div>
<?php } ?>
";s:6:"expire";i:0;}