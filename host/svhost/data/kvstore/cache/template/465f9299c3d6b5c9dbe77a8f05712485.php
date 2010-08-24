<?php exit(); ?>a:2:{s:5:"value";s:6090:"<div class="division">
<table id="_pay_cod" width="100%" cellpadding="0" cellspacing="0"  style="display:<?php if( $this->_vars['delivery']['has_cod'] == 1 ){ ?>block<?php }else{ ?>none<?php } ?>">
            <col class="span-5"></col>
            <col class="span-auto"></col>
            <tbody>
  <tr>
    <th style="text-align:left;"><input type="radio" name="payment[pay_app_id]" value="-1" paytype="offline" id="payment_bank" class="x-payMethod" <?php if( $this->_vars['order']['payment']==-1 ){ ?> checked="checked"<?php } ?> /><strong>货到付款</strong></th>
    <td>由我们的快递人员在将货物送到时收取货款。</td>
  </tr>
  </tbody>
</table>
<table width="100%" cellpadding="0" cellspacing="0"  id="_normal_payment">
        <col class="span-5"></col>
        <col class="span-auto"></col>
        <?php $this->_env_vars['foreach']["payments"]=array('total'=>count($this->_vars['payments']),'iteration'=>0);foreach ((array)$this->_vars['payments'] as $this->_vars['key'] => $this->_vars['payment']){
                        $this->_env_vars['foreach']["payments"]['first'] = ($this->_env_vars['foreach']["payments"]['iteration']==0);
                        $this->_env_vars['foreach']["payments"]['iteration']++;
                        $this->_env_vars['foreach']["payments"]['last'] = ($this->_env_vars['foreach']["payments"]['iteration']==$this->_env_vars['foreach']["payments"]['total']);
?>
            <tr <?php if( $this->_env_vars['foreach']['payments']['last'] ){ ?>class="last"<?php } ?>>
                <th style="text-align:left;" >
                    <label <?php if( $this->_vars['payment']['extend'] ){ ?>class="ExtendCon"<?php } ?>>
                        <input class="x-payMethod" type="radio" name="payment[pay_app_id]" paytype="<?php echo $this->_vars['payment']['pay_type']; ?>" value="<?php echo $this->_vars['payment']['app_id']; ?>"<?php if( $this->_vars['order']['payment']==$this->_vars['payment']['app_id'] ){ ?> checked="checked"<?php } ?> moneyamount="<?php echo $this->_vars['payment']['money']; ?>" formatmoney ="<?php echo $this->_vars['payment']['money']; ?>" />
                        <?php echo $this->_vars['payment']['app_name'];  if( $this->_vars['payment']['config']['method']=="1"  or  $this->_vars['payment']['config']['method']==" " ){  if( $this->_vars['payment']['fee']>0 ){ ?> (支付费率: +<?php echo $this->_vars['payment']['fee']*100; ?>%)<?php }  }else{  if( $this->_vars['payment']['config']['fee']>0 ){ ?> (支付费用: +<?php echo $this->_vars['payment']['config']['fee']; ?>)<?php }  } ?></label>
                </th>
                <td>
                    <?php echo ((isset($this->_vars['payment']['intro']) && ''!==$this->_vars['payment']['intro'])?$this->_vars['payment']['intro']:'&nbsp;');  if( $this->_vars['payment']['extend'] ){  if($this->_vars['payment']['extend'])foreach ((array)$this->_vars['payment']['extend'] as $this->_vars['extkey'] => $this->_vars['extvalue']){ ?>
                          <div class="division paymentextend <?php echo $this->_vars['extvalue']['extconId']; ?> clearfix">
                            <hr />
                            <?php if( $this->_vars['extvalue']['fronttype']<>'select' ){ ?>
                            <ul>
                              <?php $this->_env_vars['foreach']["bank"]=array('total'=>count($this->_vars['extvalue']['value']),'iteration'=>0);foreach ((array)$this->_vars['extvalue']['value'] as $this->_vars['extskey'] => $this->_vars['extsval']){
                        $this->_env_vars['foreach']["bank"]['first'] = ($this->_env_vars['foreach']["bank"]['iteration']==0);
                        $this->_env_vars['foreach']["bank"]['iteration']++;
                        $this->_env_vars['foreach']["bank"]['last'] = ($this->_env_vars['foreach']["bank"]['iteration']==$this->_env_vars['foreach']["bank"]['total']);
?>
                              <li style='float:left;'>
                                <?php if( $this->_vars['extvalue']['fronttype']=="radio" ){ ?>
                                  <input <?php echo $this->_vars['extsval']['checked']; ?> type=<?php echo $this->_vars['extvalue']['fronttype']; ?> name=<?php echo $this->_vars['extvalue']['name']; ?> value=<?php echo $this->_vars['extsval']['value']; ?>>
                                  <?php if( $this->_vars['extsval']['imgurl'] ){  echo $this->_vars['extsval']['imgurl'];  }else{  echo $this->_vars['extsval']['name'];  }  }else{ ?>
                                  <input <?php echo $this->_vars['extsval']['checked']; ?> type="<?php echo $this->_vars['extvalue']['fronttype']; ?>" name="<?php echo $this->_vars['extvalue']['name']; ?>[]" value="<?php echo $this->_vars['extsval']['value']; ?>">
                                  <?php if( $this->_vars['extsval']['imgurl'] ){  echo $this->_vars['extsval']['imgurl'];  }else{  echo $this->_vars['extsval']['name'];  }  } ?>
                              </li>
                              <?php } unset($this->_env_vars['foreach']["bank"]); ?>
                            </ul>
                            <?php }else{ ?>
                              <select name=$extvalue.name>
                              <?php if($this->_vars['extvalue']['value'])foreach ((array)$this->_vars['extvalue']['value'] as $this->_vars['extskey'] => $this->_vars['extsval']){ ?>
                                <option value=<?php echo $this->_vars['extsval']['value'];  if( $this->_vars['extsval']['checked'] ){ ?>selected<?php } ?>><?php echo $this->_vars['extsval']['name']; ?></option>
                              <?php } ?>
                              <select>
                          <?php } ?>   
                          </div>
                        <?php }  } ?>
                </td>
            </tr>
        <?php } unset($this->_env_vars['foreach']["payments"]); ?>
       
</table>
 </div>
</select>
<script>

  if(_checked =$E('#_normal_payment th input[checked]')){
     _checked.fireEvent('click');
  }       
  
</script>
";s:6:"expire";i:0;}