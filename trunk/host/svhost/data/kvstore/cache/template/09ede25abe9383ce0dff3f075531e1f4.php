<?php exit(); ?>a:2:{s:5:"value";s:3593:"<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="vertical-align:top"><div class="tableform">
        <h4>收款单据列表</h4>
        <div class="division">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="gridlist">
          <col class="Coldate"></col>
          <col class="Colamount"></col>      
      <col></col> 
      <col></col> 
            <thead>
              <tr>
                <th>单据日期</th>
                <th>支付金额</th>
                <th>支付方式</th>
                <th>状态</th>
              </tr>
            </thead>
            <tbody>
            <?php if($this->_vars['bills'])foreach ((array)$this->_vars['bills'] as $this->_vars['aBill']){ ?>
            <tr>
              <td><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['aBill']['t_end'],SDATE_STIME); ?></td>
              <td class="Colamount"><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['aBill']['money'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
              <td><?php echo $this->_vars['aBill']['pay_name']; ?></td>
              <td><?php if( $this->_vars['aBill']['status']=='succ' ){ ?>支付成功<?php }elseif( $this->_vars['aBill']['status']=='failed' ){ ?>支付失败<?php }elseif( $this->_vars['aBill']['status']=='cancel' ){ ?>未支付<?php }elseif( $this->_vars['aBill']['status']=='error' ){ ?>参数异常<?php }elseif( $this->_vars['aBill']['status']=='progress' ){ ?>处理中<?php }elseif( $this->_vars['aBill']['status']=='timeout' ){ ?>超时<?php }elseif( $this->_vars['aBill']['status']=='ready' ){ ?>准备中<?php } ?></td>
            </tr>
            <?php } ?>
            </tbody>
            
          </table>
        </div>
      </div></td>
    <td  style="vertical-align:top" ><div class="tableform">
        <h4>退款单据列表</h4>
        <div class="division">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="gridlist">
           <col class="Coldate"></col>
          <col class="Colamount"></col>     
            <thead>
              <tr>
                <th>单据日期</th>
                <th>退款金额</th>
                <th>退款方式</th>
                <th>状态</th>
              </tr>
            </thead>
            <tbody>
            <?php if($this->_vars['refunds'])foreach ((array)$this->_vars['refunds'] as $this->_vars['aBill']){ ?>
            <tr>
              <td><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['aBill']['t_sent'],SDATE_STIME); ?></td>
              <td class="Colamount"><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['aBill']['money'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
              <td><?php echo $this->_vars['aBill']['pay_name']; ?></td>
              <td><?php if( $this->_vars['aBill']['status']=='succ' ){ ?>已退钱<?php }elseif( $this->_vars['aBill']['status']=='received' ){ ?>用户确认到款<?php }elseif( $this->_vars['aBill']['status']=='progress' ){ ?>处理中<?php }elseif( $this->_vars['aBill']['status']=='ready' ){ ?>准备中<?php } ?></td>
            </tr>
            <?php } ?>
            </tbody>
            
          </table>
        </div>
      </div></td>
  </tr>
</table>
";s:6:"expire";i:0;}