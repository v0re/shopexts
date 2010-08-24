<?php exit(); ?>a:2:{s:5:"value";s:6265:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><form class="tableform" method='post' action='index.php?app=b2c&ctl=admin_order&act=dopay' id="doorderpay-form">
<input type='hidden' name='order_id' value='<?php echo $this->_vars['order']['order_id']; ?>'>
<input type='hidden' name='inContent' value='true'>

<div class="division">
<table width="100%">
  <tr>
    <th>订单号：</th>
    <td><?php echo $this->_vars['order']['order_id']; ?> 【<?php if( $this->_vars['order']['pay_status'] == 0 ){ ?>未支付<?php }elseif( $this->_vars['order']['pay_status'] == 1 ){ ?>已支付<?php }elseif( $this->_vars['order']['pay_status'] == 2 ){ ?>处理中<?php }elseif( $this->_vars['order']['pay_status'] == 3 ){ ?>部分付款<?php }elseif( $this->_vars['order']['pay_status'] == 4 ){ ?>部分退款<?php }elseif( $this->_vars['order']['pay_status'] == 5 ){ ?>已退款<?php } ?>】</td>
    <th>下单日期：</th>
    <td><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['order']['createtime'],'SDATE_STIME'); ?></td>
      </tr>
  <tr>
    <th>订单总金额：</th>
    <td><?php echo app::get('ectools')->model('currency')->changer($this->_vars['order']['total_amount']); ?></td>
    <th>已收金额：</th>
    <td><?php echo app::get('ectools')->model('currency')->changer($this->_vars['order']['payed']); ?></td>
    </tr>
  <tr>
    <th>收款银行：</th>
    <td colspan="3"><?php echo $this->ui()->input(array('type' => 'text','id' => 'payBank','name' => 'bank','value' => '','width' => "80"));?> &nbsp;&nbsp;<?php echo $this->ui()->input(array('id' => "selectAccount",'type' => "select",'name' => 'select_account','options' => $this->_vars['pay_account'],'value' => 0));?></td>
    </tr>
    <tr>
      <th>收款帐号：</th>
      <td colspan="3"><?php echo $this->ui()->input(array('type' => 'text','id' => 'payAccount','name' => 'account','value' => '','width' => "200"));?></td>
    </tr>
    <tr>
      <th>收款金额：</th>
      <td><?php echo $this->ui()->input(array('type' => 'text','name' => 'money','value' => $this->_vars['order']['total_amount']-$this->_vars['order']['payed'],'width' => "100"));?></td>
      <th>收款人：</th>
      <td><?php echo $this->_vars['op_name']; ?></td> 
    </tr>
  <tr>
    <th>付款类型：</th>
    <td colspan="3"><?php echo $this->ui()->input(array('type' => "radio",'name' => "pay_type",'options' => $this->_vars['typeList'],'value' => $this->_vars['pay_type']));?></td>
  </tr>
     <tr>
    <th>支付方式：</th>
    <td><?php echo $this->ui()->input(array('type' => "select",'name' => 'payment','rows' => $this->_vars['payment'],'valueColumn' => "app_id",'labelColumn' => "app_name",'value' => $this->_vars['order']['payinfo']['pay_app_id']));?></td>
    <th>客户支付货币：</th>
    <td><?php echo $this->_vars['order']['cur_name']; ?> (<?php echo app::get('ectools')->model('currency')->changer($this->_vars['order']['final_amount'],$this->_vars['order']['currency'],false,true); ?>)</td>
    </tr> 
    <tr>
    <th>是否开票：</th>
    <td><?php if( $this->_vars['order']['is_tax'] == 'true' ){ ?>是<?php }else{ ?>否<?php } ?></td>
    <th>税金：</th>
    <td><?php echo app::get('ectools')->model('currency')->changer($this->_vars['order']['cost_tax']); ?></td>
   </tr>
	<tr>
    <th>当前状态：</th>
    <td><?php if( $this->_vars['order']['pay_status'] == 0 ){ ?>未支付<?php }elseif( $this->_vars['order']['pay_status'] == 1 ){ ?>已支付<?php }elseif( $this->_vars['order']['pay_status'] == 2 ){ ?>处理中<?php }elseif( $this->_vars['order']['pay_status'] == 3 ){ ?>部分付款<?php }elseif( $this->_vars['order']['pay_status'] == 4 ){ ?>部分退<?php }elseif( $this->_vars['order']['pay_status'] == 5 ){ ?>已退款<?php } ?></td>
      <th>收取支付费用：</th>
      <td><?php echo $this->_vars['order']['payinfo']['cost_payment']; ?></td>
     </tr> 
	<tr>
    <th>发票抬头：</th>
    <td><?php echo $this->_vars['order']['tax_title']; ?></td>
      <th>付款人：</th>
      <td><?php echo $this->ui()->input(array('type' => 'text','name' => 'pay_account','style' => "width:90px",'value' => $this->_vars['member']['name']));?></td>
     </tr>
    <tr>
        <th>收款单备注：</th>
        <td colspan="3"><textarea name="memo"  cols="40" style="width:92%" rows="" value='<?php echo $this->_vars['detail']['memo']; ?>'></textarea></td>
    </tr -->
    </table>
</div>

</form>

<?php $this->_tag_stack[] = array('area', array('inject' => '.mainFoot')); $this->__view_helper_model['base_view_helper']->block_area(array('inject' => '.mainFoot'), null, $this); ob_start(); ?>
<div class="table-action">
	<?php echo $this->ui()->button(array('label' => "提交",'id' => "doorderpay-form-submit",'type' => "submit"));?>
</div>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_area($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>

<script type="text/javascript">
$('selectAccount').addEvent('change', function(e){
  e=new Event(e);
  var ipt=e.target;
  var str = ipt.value;
  var aItems = str.split('-');
  $('payBank').value = aItems[0];
  $('payAccount').value = aItems[1];
});

(function(){
	var _form = $('doorderpay-form');
	var btn =$('doorderpay-form-submit');
	var finder = finderGroup['<?php echo $_GET['_finder']['finder_id']; ?>'];
	
	_form.store('target',{
		onComplete:function(){			
			
		},
		onSuccess:function(response){
			var hash_res_obj = JSON.decode(response);
			if (hash_res_obj.success != undefined && hash_res_obj.success != "")
			{
				try{
					var _dialogIns = btn.getParent('.dialog').retrieve('instance');
				}catch(e){}
				
				if(_dialogIns)
				{
					_dialogIns.close();
					finder.refresh.delay(400,finder);
				}
			}
			else
			{
				//alert(hash_res_obj.error);
			}			
		}
		
	});

	    btn.addEvent('click',function(){
		
		    _form.fireEvent('submit',{stop:$empty});
			
		
		
		});
	
})();
</script>";s:6:"expire";i:0;}