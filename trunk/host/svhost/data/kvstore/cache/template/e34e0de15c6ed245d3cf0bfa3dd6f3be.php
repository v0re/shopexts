<?php exit(); ?>a:2:{s:5:"value";s:6727:"<div class='clearfix'>
 <table width="100%" cellpadding="0" cellspacing="0" class="liststyle data">
            <col class="span-auto ColColorGray"></col>
            <col class="span-4 ColColorOrange textright"></col>
      <tr>
        <th>商品总价格：</th>
        <td value="<?php echo $this->_vars['order_detail']['cost_item']; ?>" class="price"><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order_detail']['cost_item'],$this->_vars['order_detail']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
      </tr>
      <?php if( $this->_vars['order_detail']['cost_freight'] > 0 ){ ?>
      <tr>
        <th>配送费用：</th>
        <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order_detail']['cost_freight'],$this->_vars['order_detail']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
      </tr>
      <?php }  if( $this->_vars['order_detail']['cost_protect'] > 0 ){ ?>
      <tr>
        <th>配送保价费：</th>
        <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order_detail']['cost_protect'],$this->_vars['order_detail']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
      </tr>
      <?php }  if( $this->_vars['order_detail']['cost_payment'] > 0 ){ ?>
      <tr>
        <th>支付费率：(客户承担支付手续费)</th>
        <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order_detail']['cost_payment'],$this->_vars['order_detail']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
      </tr>
      <?php }  if( count($this->_vars['order_detail']['gift_p'])>0 ){ ?>
	<tr>
		<th>获得赠品：</th>
		<td>
			<ol>
			<?php if($this->_vars['trading']['gift_p'])foreach ((array)$this->_vars['trading']['gift_p'] as $this->_vars['key'] => $this->_vars['item']){ ?>
				<li <?php if( $this->_vars['item']['storage']==0 ){ ?>class="mktprice1"<?php } ?>><?php echo $this->_vars['item']['name'];  echo $this->_vars['item']['nums']; ?> 件&nbsp;&nbsp;<?php if( $this->_vars['item']['storage']==0 ){ ?>（无货）<?php } ?></li>
			<?php } ?>
			</ol>
		</td>
	</tr>
	<?php }  if( $this->_vars['order_detail']['pmt_amount'] ){ ?>
      <tr>
        <th>优惠金额：</th>
        <td style="color:#f00;">-<?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order_detail']['pmt_amount'],$this->_vars['order_detail']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
      </tr>
      <?php }  if( $this->_vars['order_detail']['discount'] != 0 ){ ?>
      <tr>
        <th>订单减免：</th>
        <td style="color:#f00;"><?php if( $this->_vars['order_detail']['discount']>0 ){ ?>-<?php echo app::get('ectools')->model('currency')->changer($this->_vars['order_detail']['discount'],$this->_vars['order_detail']['currency']);  }else{ ?>+<?php echo app::get('ectools')->model('currency')->changer(0-$this->_vars['order_detail']['discount'],$this->_vars['order_detail']['currency']);  } ?></td>
      </tr>
      <?php }  if( $this->_vars['order_detail']['totalConsumeScore'] ){ ?>
      <tr>
        <th>抵扣积分：</th>
        <td style="color:#f00;"><?php echo $this->_vars['order_detail']['totalConsumeScore']; ?></td>
      </tr>
      <?php }  if( $this->_vars['order_detail']['cur_code'] != '' && $this->_vars['order_detail']['cur_code'] != $this->_vars['order_detail']['cur_display'] ){ ?>
	<tr>
		<th>货币汇率:</th>
		<td><?php echo $this->_vars['order_detail']['cur_rate']; ?></td>
	</tr>
	<tr>
		<th>货币结算金额:</th>
		<td><?php echo kernel::single('base_view_helper')->modifier_amount($this->_vars['order_detail']['final_amount'],$this->_vars['order_detail']['currency'],false,false); ?></td>
	</tr>
	<?php }  if( $this->_vars['order_detail']['cost_tax']>0 ){ ?>
		<tr>
		   <th>开发票所需税金：</th>
		   <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order_detail']['cost_tax'],$this->_vars['order_detail']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
		</tr>
		<?php } ?>
          
      <tr>
        <th>订单总金额：</th>
        <td><span class="price1"><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order_detail']['total_amount'],$this->_vars['order_detail']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></span>
        <br />
        <?php if( $this->_vars['trigger_tax'] == 'true' ){ ?>
              是否需要发票?(税金:<strong><?php echo $this->_vars['tax_ratio']; ?>%</strong>)<input type="checkbox" id="is_tax" name="payment[is_tax]" onclick="Order.updateTotal()" <?php if( $this->_vars['order_detail']['trigger_tax'] == 'true' ){ ?>checked="checked"<?php } ?> value="true" />
              <p id='tax_company'> 发票抬头：<input type="text" name="payment[tax_company]" class="inputstyle" value="<?php echo $this->_vars['order_detail']['tax_company']; ?>" /></p>
              <script>
                 $('is_tax').addEvent('click',function(){
                       $('tax_company').setStyle('visibility',this.checked?'visible':'hidden');
                 }).fireEvent('click');
              </script>
           <?php } ?>
        
        </td>
      </tr>
	  
	 <?php if( ($this->_vars['order_detail']['totalGainScore']<>0) or ($this->_vars['order_detail']['totalConsumeScore']<>0) ){  if( $this->_vars['order_detail']['totalConsumeScore'] <> 0 ){ ?>
      <tr>
        <th>此订单的消费积分数：</th>
        <td style="color:#f00;"><?php echo $this->_vars['order_detail']['totalConsumeScore']; ?></td>
      </tr>
      <?php } ?>
      <tr>
        <th>此订单数获得积分：</th>
        <td><?php echo $this->_vars['order_detail']['totalGainScore']; ?></td>
      </tr>
	  <?php if( $this->_vars['order_detail']['totalScore'] <> 0 ){ ?>
      <tr>
        <th>您的积分总计：</th>
        <td><?php echo $this->_vars['order_detail']['totalScore']; ?></td>
      </tr>
      <?php }  } ?>
   
    </table>
</div>";s:6:"expire";i:0;}