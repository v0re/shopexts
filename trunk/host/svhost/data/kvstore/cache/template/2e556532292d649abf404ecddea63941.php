<?php exit(); ?>a:2:{s:5:"value";s:29234:"<?php $this->__view_helper_model['desktop_view_helper'] = kernel::single('desktop_view_helper');  if( !$this->_vars['_is_singlepage'] ){ ?>
<div class="action-bar">
  <div class="order-ctls clearfix" order_id="<?php echo $this->_vars['order']['order_id']; ?>">
	<strong class="action-bar-info">订单状态操作：</strong><?php echo $this->_vars['html_button'];  $this->_vars["order_id"]=$this->_vars['order']['order_id']; ?>
  </div>
</div>
<?php }  if( $this->_vars['is_bklinks'] == 'true' ){  echo $this->ui()->input(array('type' => 'refer','id' => $this->_vars['order']['order_id'],'ident' => 'order','name' => 'order','show' => 'refer_id,refer_url')); } ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td style="vertical-align:top">
        <div>
  <?php if( count($this->_vars['giftItems']) > 0 ){ ?>
  <div class="tableform gridlist">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th>赠品名称与购买量</th>
          </tr>
        </thead>
        <tbody>        <?php if($this->_vars['giftItems'])foreach ((array)$this->_vars['giftItems'] as $this->_vars['aGift']){ ?>
        <tr>
          <td style="white-space:normal; text-align:left;"><?php echo $this->_vars['aGift']['name']; ?><sup>x <?php echo $this->_vars['aGift']['nums']; ?></sup></td>
        </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
    <?php } ?></div>
        
        <div class="tableform">
        <table border="0" cellpadding="0" cellspacing="0" class="orderdetails_basic">
  <tr>
    <td style=" padding:0;" >
<h5 align="center" style="height:19px">商品价格</h5>
    <table class="multi-cols" border="0" cellpadding="0" cellspacing="0" >
      
      <tr>
        <th >商品总额：</th>
        <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order']['cost_item'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
      </tr>
      <tr>
        <th >配送费用：</th>
        <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order']['shipping']['cost_shipping'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
      </tr>
      <?php if( $this->_vars['order']['shipping']['is_protect'] == 'true' ){ ?>
      <tr>
        <th >保价费用：</th>
        <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order']['shipping']['cost_protect'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
      </tr>
      <?php }  if( $this->_vars['order']['payinfo']['cost_payment'] > 0 ){ ?>
          <tr>
            <th >支付手续费：</th>
            <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order']['payinfo']['cost_payment'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
          </tr>
      <?php }  if( $this->_vars['order']['is_tax'] == 'true' ){ ?>
          <tr>
            <th >税金：</th>
            <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order']['cost_tax'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
          </tr>
      <?php }  if( $this->_vars['order']['pmt_amount'] > 0 ){ ?>
          <tr>
            <th >使用优惠方案名称：</th>
            <td><?php echo $this->_vars['order']['use_pmt']; ?></td>
          </tr>
          <tr>
            <th >优惠抵扣金额：</th>
            <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order']['pmt_amount'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
          </tr>
      <?php }  if( $this->_vars['order']['discount'] != 0 ){ ?>
          <tr>
            <th >订单减免：</th>
            <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order']['discount'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
          </tr>
      <?php } ?>
      <tr>
        <th >订单总额：</th>
        <td><span class="price0"><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order']['total_amount'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></span></td>
      </tr>
      <tr>
        <th >已支付金额：</th>
        <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order']['payed'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
      </tr>
      <?php if( $this->_vars['order']['cur_rate'] != 1 ){ ?>
      <tr>
        <th >货币：</th>
        <td><?php echo $this->_vars['order']['currency']; ?></td>
      </tr>
      <tr>
        <th >汇率：</th>
        <td><?php echo $this->_vars['order']['cur_rate']; ?></td>
      </tr>
      <tr>
        <th >结算货币金额：</th>
        <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order']['cur_amount'],$this->_vars['order']['currency'],false,true,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
      </tr>
      <?php } ?>
    </table>
    </td>
    <td style=" padding:0" >
<h5 align="center" style="height:19px">订单其他信息</h5>
    <table class="multi-cols" border="0" cellpadding="0" cellspacing="0" >
      
      <tr>
        <th >配送方式：</th>
        <td><?php echo $this->_vars['order']['shipping']['shipping_name']; ?></td>
      </tr>
      <tr>
        <th >配送保价：</th>
        <td><?php if( $this->_vars['order']['shipping']['is_protect'] == 'true' ){ ?>是<?php }else{ ?>否<?php } ?></td>
      </tr>
      <tr>
        <th >商品重量：</th>
        <td><?php echo $this->_vars['order']['weight']+0; ?> g</td>
      </tr>
      <tr>
        <th >支付方式：</th>
        <td>
			<?php echo $this->_vars['order']['payinfo']['pay_app_id']; ?>
			&nbsp;
			<?php if($this->_vars['order']['extendCon'])foreach ((array)$this->_vars['order']['extendCon'] as $this->_vars['key'] => $this->_vars['item']){  echo $this->_vars['item']; ?>&nbsp;&nbsp;
			<?php } ?>
		</td>
      </tr>
      <tr>
        <th >是否开票：</th>
        <td><?php if( $this->_vars['order']['is_tax'] == 'true' ){ ?>是<?php }else{ ?>否<?php } ?></td>
      </tr>
      <?php if( $this->_vars['order']['is_tax'] == 'true' ){ ?>
      <tr>
        <th >发票抬头：</th>
        <td><?php echo $this->_vars['order']['tax_title']; ?></td>
      </tr>
      <?php } ?>
      <tr>
        <th >可得积分：</th>
        <td><?php echo $this->_vars['order']['score_g']+0; ?></td>
      </tr>
    </table>
    </td>
    <td style=" padding:0" >
    <h5 align="center" style="height:19px">购买人信息</h5>
    <table class="multi-cols" border="0" cellpadding="0" cellspacing="0" >
      
      <?php if( $this->_vars['order']['member_id'] > 0 ){ ?>
      <tr>
        <th >用户名：</th>
        <td>
        <a href="index.php?app=b2c&ctl=admin_member&act=index&action=detail&id=<?php echo $this->_vars['order']['member_id']; ?>" target="_blank"><?php echo $this->_vars['order']['member']['pam_account']['login_name']; ?></a></td>
      </tr>
      <tr>
        <th >姓名：</th>
        <td><?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['order']['member']['contact']['name'],'html'); ?></td>
      </tr>
      <tr>
        <th >电话：</th>
        <td><?php echo $this->_vars['order']['member']['contact']['phone']['telephone']; ?></td>
      </tr>
      <tr>
        <th >地区：</th>
        <td ><?php echo $this->_vars['order']['member']['contact']['area']; ?></td>
      </tr>
      <!--            <tr>
              <td>Email：</td><td><?php echo $this->_vars['order']['member']['email']; ?>
            </td></tr>
            <tr>
              <td>省份：</td><td><?php echo $this->_vars['order']['member']['province']; ?>
            </td></tr>
            <tr>
              <td>邮编：</td><td><?php echo $this->_vars['order']['member']['zip']; ?>
            </td></tr> -->
      <!--<tr>
        <th >地址：</th>
        <td><?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['order']['member']['addr'],'html'); ?></td>
      </tr>-->
      <tr>
        <th >Email：</th>
        <td><a  target="_self" href="mailto:<?php echo $this->_vars['order']['member']['contact']['email']; ?>"><?php echo $this->_vars['order']['member']['contact']['email']; ?></a></td>
      </tr>
	  <?php if( $this->_vars['tree'] ){  $this->_env_vars['foreach'][contact]=array('total'=>count($this->_vars['tree']),'iteration'=>0);foreach ((array)$this->_vars['tree'] as $this->_vars['contact']){
                        $this->_env_vars['foreach'][contact]['first'] = ($this->_env_vars['foreach'][contact]['iteration']==0);
                        $this->_env_vars['foreach'][contact]['iteration']++;
                        $this->_env_vars['foreach'][contact]['last'] = ($this->_env_vars['foreach'][contact]['iteration']==$this->_env_vars['foreach'][contact]['total']);
?>
        
        <tr>
        <th ><?php echo $this->_vars['contact']['attr_name']; ?>:</th>
      <td><?php if( $this->_vars['contact']['attr_tyname'] == 'QQ' && $this->_vars['contact']['value'] !='' ){ ?>
     <a target="_self" href="tencent://message/?uin=<?php echo $this->_vars['contact']['value']; ?>&&Site=www.shopex.cn&&Menu=yes"><img border="0" SRC='http://wpa.qq.com/pa?p=1:<?php echo $this->_vars['contact']['value']; ?>:1' title='<?php echo $this->_vars['contact']['value']; ?>'></a>
      
      
      <?php }  if( $this->_vars['contact']['attr_tyname'] == 'MSN' && $this->_vars['contact']['value'] !='' ){ ?>
      <a  target="_self" href="msnim:chat?contact=<?php echo $this->_vars['contact']['value']; ?>"><img width="30" height="30" border="0" src="http://im.live.com/Messenger/IM/Images/Icons/Messenger.Logo.gif" title='<?php echo $this->_vars['contact']['value']; ?>'/></a>
      
    
      <?php }  if( $this->_vars['contact']['attr_tyname'] == '旺旺' && $this->_vars['contact']['value'] !='' ){ ?>
     <a target="_blank" href="http://amos1.taobao.com/msg.ww?v=2&uid=<?php echo $this->_vars['contact']['value']; ?>&s=1" ><img border="0" src="http://amos1.taobao.com/online.ww?v=2&uid=<?php echo $this->_vars['contact']['value']; ?>&s=1"  title='<?php echo $this->_vars['contact']['value']; ?>'/></a>
   
      <?php }  if( $this->_vars['contact']['attr_tyname'] == 'Skype' && $this->_vars['contact']['value'] !='' ){ ?>
  <a href="skype:<?php echo $this->_vars['contact']['value']; ?>?call"  target="_self"onclick="return skypeCheck();"><img src="http://mystatus.skype.com/smallclassic/<?php echo $this->_vars['contact']['value']; ?>" style="border: none;" alt="Call me!" title='<?php echo $this->_vars['contact']['value']; ?>'/></a>
   
      <?php } ?>
      
      
      </td>
        
      </tr>       
        
        <?php } unset($this->_env_vars['foreach'][contact]);  }  }else{ ?>
      <tr>
        <th >非会员顾客</th>
        <td></td>
      </tr>
      <?php } ?>
    </table>
    </td>
    <td style=" padding:0;" >
    <?php if( $this->_vars['order']['is_delivery'] == 'Y' ){ ?>
        <h5 align="center">收货人信息 
        <button class="btn" style="margin-top:-8px" id="order_receiver_copy" status="Y" info="<?php echo $this->_vars['order']['consignee']['area']; ?>,<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['order']['consignee']['addr'],'html'); ?>,<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['order']['consignee']['name'],'html');  if( $this->_vars['order']['consignee']['telephone'] ){ ?>,<?php echo $this->_vars['order']['consignee']['telephone'];  }  if( $this->_vars['order']['consignee']['mobile'] ){ ?>,<?php echo $this->_vars['order']['consignee']['mobile'];  } ?>,<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['order']['consignee']['zip'],'html'); ?>"><span><span>复制收货人信息</span></span></button>&nbsp; <?php $this->_tag_stack[] = array('help', array()); $this->__view_helper_model['desktop_view_helper']->block_help(array(), null, $this); ob_start(); ?>此功能按照将收货人信息整合后复制到剪贴板，方便店主粘贴至目标位置，如：给顾客确认地址的邮件<br /><br />复制格式：<br />地区,地址,姓名,电话,手机,邮编<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['desktop_view_helper']->block_help($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?></h5>
        <table class="multi-cols" border="0" cellpadding="0" cellspacing="0" >
          <tr>
            <th >发货日期：</th>
            <td><?php echo $this->_vars['order']['consignee']['r_time']; ?></td>
          </tr>
          <tr>
            <th >姓名：</th>
            <td><?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['order']['consignee']['name'],'html'); ?></td>
          </tr>
          <tr>
            <th >电话：</th>
            <td><?php echo $this->_vars['order']['consignee']['telephone']; ?></td>
          </tr>
          <tr>
            <th >手机：</th>
            <td><?php echo $this->_vars['order']['consignee']['mobile']; ?></td>
          </tr>
          <tr>
            <th >地区：</th>
            <td><?php echo $this->_vars['order']['consignee']['area']; ?></td>
          </tr>
          <tr>
            <th >地址：</th>
            <td style="white-space:normal; line-height:18px;"><?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['order']['consignee']['addr'],'html'); ?></td>
          </tr>
          <tr>
            <th >邮编：</th>
            <td><?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['order']['consignee']['zip'],'html'); ?></td>
          </tr>
          <?php if( !$this->_vars['order']['member_id'] ){ ?>
          <tr>
            <th >Email：</th>
            <td><?php echo $this->_vars['order']['consignee']['email']; ?></td>
          </tr>
          <?php } ?>
        </table>
     <?php } ?>
     </td>
  </tr>
</table></div></td>
      </tr>
            <tr>
      <td>
      
	  <?php if( count($this->_vars['goodsItems']) > 0 ){ ?>
      <div class="tableform">
      <h3>商品信息</h3>
      <table cellpadding="0" class="gridlist" cellspacing="0" width="100%" border="0">
      <thead>
      <tr><th>货品编号</th><th>货品名称</th><th>单价</th><th>合计金额</th><th>购买数量</th><th>已发货量</th></tr>
      </thead>
      <tbody>
	  <?php $this->_env_vars['foreach']["item"]=array('total'=>count($this->_vars['goodsItems']),'iteration'=>0);foreach ((array)$this->_vars['goodsItems'] as $this->_vars['aGoods']){
                        $this->_env_vars['foreach']["item"]['first'] = ($this->_env_vars['foreach']["item"]['iteration']==0);
                        $this->_env_vars['foreach']["item"]['iteration']++;
                        $this->_env_vars['foreach']["item"]['last'] = ($this->_env_vars['foreach']["item"]['iteration']==$this->_env_vars['foreach']["item"]['total']);
?>
		<tr>
          <td width="19%"><?php echo $this->_vars['aGoods']['bn']; ?></td>
          <td class="textleft" width="30%"><a href="<?php echo $this->_vars['aGoods']['link']; ?>" target="_blank"><?php echo $this->_vars['aGoods']['name'];  if( $this->_vars['aGoods']['minfo'] && is_array($this->_vars['aGoods']['minfo']) ){  if($this->_vars['aGoods']['minfo'])foreach ((array)$this->_vars['aGoods']['minfo'] as $this->_vars['name'] => $this->_vars['minfo']){ ?> <br>
            <?php echo $this->_vars['minfo']['label']; ?>：<?php echo $this->_vars['minfo']['value'];  }  } ?></a><?php if( $this->_vars['aGoods']['is_type'] == 'goods' && $this->_vars['aGoods']['small_pic'] ){ ?> <a style='text-decoration:none;' class='x-view-img' href='javascript:void(0);' imgsrc='<?php echo kernel::single('base_view_helper')->modifier_storager(((isset($this->_vars['aGoods']['small_pic']) && ''!==$this->_vars['aGoods']['small_pic'])?$this->_vars['aGoods']['small_pic']:app),'s'); ?>'  title='<?php echo $this->_vars['aGoods']['name']; ?>'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a> <?php } ?> </td>
          <td width="12%" ><?php echo $this->_vars['aGoods']['item_type']; ?></td>
          <td class="Colamount" width="15%"><?php echo $this->_vars['aGoods']['price']; ?></td>
          <td class="Colamount" width="15%"><?php echo $this->_vars['aGoods']['quantity']; ?></td>
          <td class="Colamount"><?php echo $this->_vars['aGoods']['sendnum']; ?></td>
        </tr>
		<?php if( $this->_vars['aGoods']['adjunct'] ){ ?>
      <tr>
        <td colspan="6" style="padding:0 0 0 20px;  background:#F7FAFB"><div style="padding-left:32px; border-bottom:1px solid #E8E8E8;  font-weight:bold; text-align:left">商品配件</div>
          <table  border="0" align="center"cellpadding="0" cellspacing="0" style=" background:#F7FAFB" >
            <?php $this->_env_vars['foreach']["ajunctsItem"]=array('total'=>count($this->_vars['aGoods']['adjunct']),'iteration'=>0);foreach ((array)$this->_vars['aGoods']['adjunct'] as $this->_vars['ajuncts']){
                        $this->_env_vars['foreach']["ajunctsItem"]['first'] = ($this->_env_vars['foreach']["ajunctsItem"]['iteration']==0);
                        $this->_env_vars['foreach']["ajunctsItem"]['iteration']++;
                        $this->_env_vars['foreach']["ajunctsItem"]['last'] = ($this->_env_vars['foreach']["ajunctsItem"]['iteration']==$this->_env_vars['foreach']["ajunctsItem"]['total']);
?>
            <tr>
              <td width="19%" style="border:none;"><?php echo $this->_vars['ajuncts']['bn']; ?></td>
              <td class="textleft" width="30%" style="border:none;"><a href="<?php echo $this->_vars['ajuncts']['link']; ?>" target="_blank"><?php echo $this->_vars['ajuncts']['name'];  if( $this->_vars['ajuncts']['minfo'] && is_array($this->_vars['ajuncts']['minfo']) ){  if($this->_vars['ajuncts']['minfo'])foreach ((array)$this->_vars['ajuncts']['minfo'] as $this->_vars['name'] => $this->_vars['minfo']){ ?> <br>
                <?php echo $this->_vars['minfo']['label']; ?>：<?php echo $this->_vars['minfo']['value'];  }  } ?></a><?php if( $this->_vars['ajuncts']['is_type'] == 'goods' && $this->_vars['ajuncts']['small_pic'] ){ ?> <a style='text-decoration:none;' class='x-view-img' href='javascript:void(0);' imgsrc='<?php echo kernel::single('base_view_helper')->modifier_storager(((isset($this->_vars['ajuncts']['small_pic']) && ''!==$this->_vars['ajuncts']['small_pic'])?$this->_vars['ajuncts']['small_pic']:app),'s'); ?>'  title='<?php echo $this->_vars['ajuncts']['name']; ?>'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a> <?php } ?> </td>
              <td width="12%" style="border:none;" ><?php echo $this->_vars['ajuncts']['item_type']; ?></td>
              <td class="Colamount" width="15%" style="border:none;"><?php echo $this->_vars['ajuncts']['price']; ?></td>
              <td class="Colamount" width="15%" style="border:none;"><?php echo $this->_vars['ajuncts']['quantity']; ?></td>
              <td class="Colamount" style="border:none;"><?php echo $this->_vars['ajuncts']['sendnum']; ?></td>
            </tr>
            <?php } unset($this->_env_vars['foreach']["ajunctsItem"]); ?>
          </table></td>
      </tr>
      <?php }  if( $this->_vars['aGoods']['gifts'] ){ ?>
      <tr>
        <td colspan="6" style="border:none;padding:0" ><div style=" border-bottom:1px solid #ddd;font-weight:bold; background:#F4F4F4; padding-left:50px;text-align:left">商品赠品</div></td>
      </tr>
      <?php $this->_env_vars['foreach']["giftsItem"]=array('total'=>count($this->_vars['aGoods']['gifts']),'iteration'=>0);foreach ((array)$this->_vars['aGoods']['gifts'] as $this->_vars['gifts']){
                        $this->_env_vars['foreach']["giftsItem"]['first'] = ($this->_env_vars['foreach']["giftsItem"]['iteration']==0);
                        $this->_env_vars['foreach']["giftsItem"]['iteration']++;
                        $this->_env_vars['foreach']["giftsItem"]['last'] = ($this->_env_vars['foreach']["giftsItem"]['iteration']==$this->_env_vars['foreach']["giftsItem"]['total']);
?>
      <tr>
        <td width="19%" ><?php echo $this->_vars['gifts']['bn']; ?></td>
        <td class="textleft" width="30%"><a href="<?php echo $this->_vars['gifts']['link']; ?>" target="_blank"><?php echo $this->_vars['gifts']['name'];  if( $this->_vars['gifts']['minfo'] && is_array($this->_vars['gifts']['minfo']) ){  if($this->_vars['gifts']['minfo'])foreach ((array)$this->_vars['gifts']['minfo'] as $this->_vars['name'] => $this->_vars['minfo']){ ?> <br>
          <?php echo $this->_vars['minfo']['label']; ?>：<?php echo $this->_vars['minfo']['value'];  }  } ?></a><?php if( $this->_vars['gifts']['small_pic'] ){ ?> <a style='text-decoration:none;' class='x-view-img' href='javascript:void(0);' imgsrc='<?php echo kernel::single('base_view_helper')->modifier_storager(((isset($this->_vars['gifts']['small_pic']) && ''!==$this->_vars['gifts']['small_pic'])?$this->_vars['gifts']['small_pic']:app),'s'); ?>'  title='<?php echo $this->_vars['gifts']['name']; ?>'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a> <?php } ?> </td>
        <td width="12%" ><?php echo $this->_vars['gifts']['item_type']; ?></td>
        <td class="Colamount" width="15%" ><?php echo $this->_vars['gifts']['price']; ?></td>
        <td class="Colamount" width="15%" ><?php echo $this->_vars['gifts']['quantity']; ?></td>
        <td class="Colamount" ><?php echo $this->_vars['gifts']['sendnum']; ?></td>
      </tr>
      <?php } unset($this->_env_vars['foreach']["giftsItem"]);  }  } unset($this->_env_vars['foreach']["item"]);  if( $this->_vars['giftItems'] ){ ?>
      <tr>
        <td colspan="6" style="border:none;padding:0" ><div style=" border-bottom:1px solid #ddd;font-weight:bold;background:#F4F4F4;  padding-left:50px; text-align:left">优惠赠品</div></td>
      </tr>
      <?php $this->_env_vars['foreach']["item"]=array('total'=>count($this->_vars['giftItems']),'iteration'=>0);foreach ((array)$this->_vars['giftItems'] as $this->_vars['aGoods']){
                        $this->_env_vars['foreach']["item"]['first'] = ($this->_env_vars['foreach']["item"]['iteration']==0);
                        $this->_env_vars['foreach']["item"]['iteration']++;
                        $this->_env_vars['foreach']["item"]['last'] = ($this->_env_vars['foreach']["item"]['iteration']==$this->_env_vars['foreach']["item"]['total']);
?>
      <tr>
        <td width="19%"><?php echo $this->_vars['aGoods']['bn']; ?></td>
        <td class="textleft"><a href="<?php echo $this->_vars['aGoods']['link']; ?>" target="_blank"><?php echo $this->_vars['aGoods']['name']; ?></a> <?php if( $this->_vars['aGoods']['small_pic'] ){ ?> <a style='text-decoration:none;' class='x-view-img' href='javascript:void(0);' imgsrc='<?php echo kernel::single('base_view_helper')->modifier_storager(((isset($this->_vars['aGoods']['small_pic']) && ''!==$this->_vars['aGoods']['small_pic'])?$this->_vars['aGoods']['small_pic']:app),'s'); ?>'  title='<?php echo $this->_vars['aGoods']['name']; ?>'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a> <?php } ?> </td>
        <td width="12%"><?php echo $this->_vars['aGoods']['item_type']; ?></td>
        <td class="Colamount"><?php echo $this->_vars['aGoods']['price']; ?></td>
        <td class="Colamount"><?php echo $this->_vars['aGoods']['quantity']; ?></td>
        <td class="Colamount"><?php echo $this->_vars['aGoods']['sendnum']; ?></td>
      </tr>
      </td>
      </tr>
      <?php } unset($this->_env_vars['foreach']["item"]);  } ?>
      </tbody>
       </table>
	  <?php if( $this->_vars['order']['pmt_list'] ){ ?>
       <h3>优惠方案</h3>
        <table cellpadding="0" class="gridlist" cellspacing="0" width="100%" border="0">
      <thead>
      <tr><th>优惠方案</th><th>优惠金额</th></tr>
      </thead>
      <tbody>
	  <?php if($this->_vars['order']['pmt_list'])foreach ((array)$this->_vars['order']['pmt_list'] as $this->_vars['aBill']){ ?>
     <tr>
       <td><?php echo $this->_vars['aBill']['pmt_describe']; ?></td>
       <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['aBill']['pmt_amount'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
     </tr>
	 <?php } ?>
      </tbody>
       </table>
	   <?php } ?>
      </div>
	  <?php } ?>
      </td>
      </tr>
      <tr><td>
    <div class="tableform">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tbody>
       <tr><td style="text-align:left;"><strong>会员备注：</strong><?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['order']['member']['remark'],'html'); ?></td></tr>
        <tr><td style="text-align:left;"><strong>订单备注：</strong><?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['order']['mark_text'],'html'); ?></td></tr>
         <tr><td style="text-align:left;"><strong>订单附言：</strong><?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['order']['memo'],'html'); ?></td></tr>
        </tbody>
      </table>
  </div>
      </td></tr>
    </table>
	
<script>  
var openHandler={};
OrderMgr = {act:{
  run:function(method,el,tag){
    var order_id=$(el).getParent('div[order_id]').get('order_id');
    OrderMgr.currentInfo = el.getContainer();
    if(tag>0){
      openHandler = new Dialog('index.php?ctl=order/order&act='+method+'&p[0]='+order_id, {title:'订单['+order_id+']'+el.value+'操作',
          width:window.getSize().x*0.85,
          height:window.getSize().y*0.85,
          onLoad:function(){
              <?php if( $_GET['_finder_name'] ){ ?>
                this.dialog.getElement('form').store('target',{
                   update:'messagebox',
                   onComplete:function(){
                      openHandler.close();
                      finderGroup['<?php echo $_GET['_finder_name']; ?>'].refresh();
                   }
                });
              <?php } ?>
          }
      });
    }else{
      $(el).setProperty('disabled','disabled');
      W.page('index.php?ctl=order/order&act='+method+'&p[0]='+order_id,{method:'post',data:{_o:'_'},update:'messagebox',
        onComplete:function(){
        
         if(!window.finderGroup)return location.reload();
        
          finderGroup['<?php echo $_GET['_finder_name']; ?>'].refresh();}},el
        
        );
    }
  }
}};

if($('order_receiver_copy')){
$('order_receiver_copy').addEvent('click',function(e){
    if($('order_receiver_copy').get('status') == "Y"){
        copy_code($('order_receiver_copy').get('info'));
        $('order_receiver_copy').set('status','N');
    }
});
}

function copy_code(copyText){
    if (window.clipboardData){
        window.clipboardData.setData("Text", copyText);
    }else{
        prompt('请复制收货人信息：',copyText);
    }
    $$('#order_receiver_copy span')[1].setText('信息已经复制到剪切板中');
    (function(){
        if(!$('order_receiver_copy'))return;
        $$('#order_receiver_copy span')[1].setText('复制收货人信息');
        $('order_receiver_copy').set('status','Y');
    }).delay(2000);
}


$$('.x-view-img').each(function(item){
		 item.store('tip:text','<div style="width:220px;height:220px;overflow:hidden;"><img  src="'+item.get('imgsrc')+'" onload="$(this).zoomImg(220,220);" /></div>');
	     Xtip.attach(item);
});


</script>
 ";s:6:"expire";i:0;}