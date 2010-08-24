<?php exit(); ?>a:2:{s:5:"value";s:11207:"<div class='CartWrap'>
    <div class="CartNav clearfix">
    <div class="floatLeft">
    <img src="<?php echo $this->_vars['res_url']; ?>/cartnav-step4.gif" alt="购物流程--确认订单填写购物信息" />
    </div>
    <div class="floatRight"><img src="<?php echo $this->_vars['res_url']; ?>/cartnav-cart.gif"  /></div>
    </div>
</div>
<form id="f_order_pay" target="_blank" action="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_paycenter','act' => 'dopayment','arg0' => 'order')); ?>" method="post">
<input type="hidden" name="payment[order_id]" value="<?php echo $this->_vars['order']['order_id']; ?>" />
<input type="hidden" name="payment[money]" value="<?php echo $this->_vars['order']['total_amount']-$this->_vars['order']['payed']; ?>" id="hidden_money"/>
<input type="hidden" name="payment[currency]" value="<?php echo $this->_vars['order']['currency']; ?>" />
<input type="hidden" name="payment[cur_money]" value="<?php echo $this->_vars['order']['cur_money']; ?>" id="hidden_cur_money"/>
<input type="hidden" name="payment[cur_rate]" value="<?php echo $this->_vars['order']['cur_rate']; ?>" />
<input type="hidden" name="payment[cur_def]" value="<?php echo $this->_vars['order']['cur_def']; ?>" />
<input type="hidden" name="payment[cost_payment]" value="<?php echo $this->_vars['order']['payinfo']['cost_payment']; ?>" />
<input type="hidden" name="payment[cur_amount]" value="<?php echo $this->_vars['order']['cur_amount']; ?>" />
<input type="hidden" name="payment[memo]" value="<?php echo $this->_vars['order']['memo']; ?>" />
<!--<input type="hidden" name="payment[return_url]" value="<?php echo $this->_vars['return_url']; ?>" />-->

<div class="success clearfix pushdown-2">
   <h3>恭喜！您的订单已经提交！</h3>
 
</div>

<h3>订单信息</h3>
  <div class='ColColorBlue' style='padding:5px;border:1px #ccc solid;'>
      <span>订单编号：</span><strong class='font14px'><?php echo $this->_vars['order']['order_id']; ?></strong>&nbsp;&nbsp;[ <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_order,'act' => detail,'arg0' => $this->_vars['order']['order_id'])); ?>" >查看订单详细信息&raquo;</a> ]
  </div>
  <div class='division' style='padding:15px;'>
     <span>订单金额:</span><strong class="hueorange fontcolorRed font20px" id="span_amount"><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order']['total_amount'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></strong>
  </div>

<h3>订单支付</h3>
<?php if( $this->_vars['order']['total_amount'] > $this->_vars['order']['payed'] ){ ?>
  <div class='ColColorBlue' style='padding:5px;border:1px #ccc solid;'>
      <?php if( !$this->_vars['order']['selecttype'] ){ ?>
        您选择了：<strong class="hueorange fontcolorRed font14px"><?php echo $this->_vars['order']['payinfo']['pay_name']; ?></strong>
         <a href='<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_order",'act' => "index",'arg0' => $this->_vars['order']['order_id'],'arg1' => 'true')); ?>' >[ 选择其他支付方式 ]</a>
      <?php }else{ ?>
         请选择支付方式:
     <?php } ?>
  </div>
  <?php } ?>
  <div class='division' style="padding: 15px;">
  
  <?php if( $this->_vars['order']['selecttype'] ){ ?>
    <div class='select-paymethod'>
        <?php if( $this->_vars['payments'] ){ ?>
        <table width="100%" cellspacing="0" cellpadding="0" id="_normal_payment" class="liststyle data"> 
            <col class="span-5 ColColorGray">
            <col class="span-auto">
            <tbody>
            <?php if($this->_vars['payments'])foreach ((array)$this->_vars['payments'] as $this->_vars['key'] => $this->_vars['item']){ ?>
                <tr>
                    <th style="text-align: left;">
                        <label><input type="radio" onclick="Order.paymentChange(this)" formatmoney="<?php echo $this->_vars['order']['cur_def'];  echo $this->_vars['item']['total_amount']; ?>" curmoney="<?php echo $this->_vars['item']['cur_money']; ?>" moneyamount="<?php echo $this->_vars['item']['total_amount']; ?>"<?php if( $this->_vars['order']['payinfo']['pay_app_id'] == $this->_vars['item']['app_id'] ){ ?> checked="checked"<?php } ?> value="<?php echo $this->_vars['item']['app_id']; ?>" paytype="<?php echo $this->_vars['item']['app_id']; ?>" name="payment[pay_app_id]" class="x-payMethod" style="cursor: pointer;"><?php echo $this->_vars['item']['app_name']; ?></label> 
                    </th>
                    <td class="ColColorBlue selected"><?php echo $this->_vars['item']['app_des']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php } ?>
    </div>
  <div class="textcenter" style="padding:10px;">
   <input type="submit" class='actbtn btn-pay' value="立刻支付" />
  </div>
<?php }else{ ?>
  <input type="hidden" name="payment[pay_app_id]" value="<?php echo $this->_vars['order']['payinfo']['pay_app_id']; ?>" />
  <?php if( $this->_vars['extendInfo'] ){ ?>
      <div class='division paymethodextendInfo'>
      <?php if($this->_vars['extendInfo'])foreach ((array)$this->_vars['extendInfo'] as $this->_vars['key'] => $this->_vars['item']){  if( $this->_vars['item']['type']=='select' ){ ?>
            <select name=<?php echo $this->_vars['key']; ?>>
                <?php if($this->_vars['item']['value'])foreach ((array)$this->_vars['item']['value'] as $this->_vars['vkey'] => $this->_vars['vitem']){ ?>
                    <option value="<?php echo $this->_vars['vitem']['value']; ?>" <?php if( $this->_vars['vitem']['checked'] ){ ?>selected<?php } ?>><?php echo $this->_vars['vitem']['name']; ?></option>
                <?php } ?>
            </select>
        <?php }else{  if($this->_vars['item']['value'])foreach ((array)$this->_vars['item']['value'] as $this->_vars['vkey'] => $this->_vars['vitem']){  if( $this->_vars['item']['type']=='radio' ){ ?>
                    <input <?php echo $this->_vars['vitem']['checked']; ?> type='radio' name=<?php echo $this->_vars['key']; ?> value=<?php echo $this->_vars['vitem']['value']; ?>><?php if( $this->_vars['vitem']['imgname'] ){  echo $this->_vars['vitem']['imgname'];  }else{  echo $this->_vars['vitem']['name'];  } ?></if>
                <?php }else{ ?>
                    <input <?php echo $this->_vars['vitem']['checked']; ?> type='checkbox' name="<?php echo $this->_vars['key']; ?>[]" value=<?php echo $this->_vars['vitem']['value']; ?>><?php if( $this->_vars['vitem']['imgname'] ){  echo $this->_vars['vitem']['imgname'];  }else{  echo $this->_vars['vitem']['name'];  } ?></if>
                <?php }  }  }  } ?>
      </div>
  <?php } ?>
  
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%">
          <?php if( $this->_vars['order']['total_amount'] > $this->_vars['order']['payed'] ){  if( $this->_vars['order']['payinfo']['pay_app_id']=="offline"  or  $this->_vars['order']['payinfo']['pay_app_id'] ==-1 ){  if( $this->_vars['order']['payinfo']['pay_app_id']==-1 ){ ?><div class="customMessages">货到付款</div><?php }else{ ?>
              <div class="customMessages">您选择了线下支付，请根据支付方式说明进行付款，付款后可通过网站信息联系我们。</strong>
			  <?php }  }else{  if( $this->_vars['order']['payinfo']['pay_app_id']==-1 ){ ?><div class="customMessages">货到付款</div><?php }else{  if( $this->_vars['order']['payinfo']['pay_app_id']=="deposit" ){ ?>
                <strong>您选择了预存款支付</strong>
              <?php }else{ ?>
                <div class="customMessages">
                    <?php if( $this->_vars['payments'] ){ ?>
                    <h3>本网站支持的在线支付方式：</h3>
                     （此为默认内容，具体内容可以在后台“页面管理-提示信息管理- 订单付款页(线上付款)提示信息”中修改）
                    <table width="100%" cellspacing="5" cellpadding="5" border="0" class="liststyle data">
                    <tbody>
                    <?php if($this->_vars['payments'])foreach ((array)$this->_vars['payments'] as $this->_vars['key'] => $this->_vars['item']){  if( $this->_vars['item']['app_pay_type'] == 'true' ){ ?>
                        <tr>
                            <td><img src="<?php echo $this->_vars['res_url']; ?>/copyright_<?php echo $this->_vars['item']['app_id']; ?>.gif"></td>
                            <td><?php echo $this->_vars['item']['app_info']; ?></td>
                        </tr>
                        <?php }  } ?>
                    </tbody>
                    </table>
                    <?php } ?>
                </div>
              <?php }  } ?>
            </td>
            </tr>
            <tr>
            <td>
			<?php if( $this->_vars['order']['payinfo']['pay_app_id'] != -1 ){ ?>
            <input type="submit" class='actbtn btn-pay' value="立刻支付" />
			<?php }  }  }else{ ?>
             订单不需要再支付,请等待我们处理
          <?php } ?>
        </td>
      </tr>
    </table>

<?php } ?>
  
  </div>


</form>

<script>
        void function(){
        var form= $('f_order_pay');
            Order ={
                
                paymentChange:function(target){
                         if(!target)return;
                         target = $(target);
                     var money  = target.get('moneyamount');
					 var cur_money = target.get('curmoney');
                     var fmoney = target.get('formatmoney');
                     var paytype= target.get('paytype');
                     
                     $('hidden_money').set('value',money);
                     $('hidden_cur_money').set('value',cur_money);
                     $('span_amount').set('text',fmoney);
                     form.getElement('input[type=submit]').set('value',paytype!='offline'?'立即付款':'确定');
                     
                     form.getElement('input[type=submit]')[(paytype=='offline'?'addClass':'removeClass')]('btn-pay-ok');
                     /* $$('#_normal_payment th .ExtendCon input[type=radio]').fireEvent('checkedchange');*/
                }
            
            };
            
            if($E('#f_order_pay .select-paymethod')){
                Order.paymentChange($E('#f_order_pay .select-paymethod input[checked]'));
                
                if(form&&form.getElement('input[type=submit]')){
                    form.getElement('input[type=submit]').addEvent('click',function(e){
                        
                        if(!$E('#f_order_pay .select-paymethod input[checked]')){
                        MessageBox.error('请选择支付方式');
                        return e.stop();
                        }
                    
                    });
                }
            }
        }();
</script>
";s:6:"expire";i:0;}