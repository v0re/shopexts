<?php exit(); ?>a:2:{s:5:"value";s:13021:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><script>
function delgoods(obj){
if(!confirm('确定删除?'))return false;
 for(obj=obj.parentNode; obj.tagName!='TR'; obj=obj.parentNode);
 obj.parentNode.removeChild(obj);
}

function calculate(){
 var iList = document.getElementsByName('aPrice[]');
}
</script>

<form method='post' action='index.php?app=b2c&ctl=admin_order&act=toEdit&finder_id=<?php echo $this->_vars['finder_id']; ?>'  id="orderEdit" extra="subOrder" >
<div class="tableform">
  <h3>商品信息</h3>
 <div class="division" id="orderItemList">
 <?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',"admin/order/detail/edit_items.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
 </div>

 <h3 >订单信息</h3>
 <div class="division">
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th>订单号：</th>
    <td><?php echo $this->_vars['order']['order_id']; ?></td>
    <th>下单日期：</th>
    <td><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['order']['createtime'],'SDATE_STIME'); ?></td>
  </tr>
   <tr>
    <th>商品总金额：</th>
    <td><input id="iditem_amount" TYPE="text" name="cost_item" value="<?php echo $this->_vars['order']['cost_item']; ?>" size=10 disabled="disabled"></td>
    <th>配送方式：</th>
    <td><select name=shipping_id><?php echo $this->__view_helper_model['base_view_helper']->function_html_options(array('options' => $this->_vars['order']['selectDelivery'],'selected' => $this->_vars['order']['shipping']['shipping_id']), $this);?></select></td>
  </tr>
   <tr>
    <th>配送费用：</th>
    <td><?php echo $this->ui()->input(array('id' => "idcost_freight",'class' => 'item itemrow','name' => "cost_freight",'value' => $this->_vars['order']['shipping']['cost_shipping'],'type' => "unsigned",'size' => 10));?></td>
    <th>支付方式：</th>
    <td>
        <select name=payment><?php echo $this->__view_helper_model['base_view_helper']->function_html_options(array('options' => $this->_vars['order']['selectPayment'],'selected' => $this->_vars['order']['payinfo']['pay_app_id']), $this);?></select>
        &nbsp;&nbsp;
        <?php if($this->_vars['order']['extendCon'])foreach ((array)$this->_vars['order']['extendCon'] as $this->_vars['key'] => $this->_vars['item']){  echo $this->_vars['item']; ?>&nbsp;&nbsp;
        <?php } ?></td>
  </tr>
  <tr>
    <th>保价：</th>
    <td><?php echo $this->ui()->input(array('id' => "idcost_protect",'class' => "item itemrow",'type' => "unsigned",'name' => "cost_protect",'size' => 10,'value' => $this->_vars['order']['shipping']['cost_protect']));?> 是否要保价<input id="idis_protect" name="is_protect" type='checkbox' value='true' <?php if( $this->_vars['order']['shipping']['is_protect'] == 'true' ){ ?>checked="checked"<?php } ?>></td>
    <th>商品重量：</th>
    <td><?php echo $this->ui()->input(array('name' => weight,'type' => "unsigned",'class' => inputstyle,'size' => 10,'value' => $this->_vars['order']['weight']));?></td>
  </tr>
  <tr>
    <th>支付手续费：</th>
    <td><?php echo $this->ui()->input(array('id' => "idcost_payment",'class' => 'item itemrow','name' => cost_payment,'type' => "unsigned",'size' => 10,'value' => $this->_vars['order']['payinfo']['cost_payment']));?></td>
    <th>发票抬头：</th>
    <td><?php echo $this->ui()->input(array('id' => "idtax_company",'name' => "tax_company",'value' => $this->_vars['order']['tax_title']));?></td>
  </tr>
   <tr>
    <th>税金：</th>
    <td><?php echo $this->ui()->input(array('id' => "idcost_tax",'class' => 'item itemrow','name' => "cost_tax",'type' => "unsigned",'size' => 10,'value' => $this->_vars['order']['cost_tax']));?> 是否开发票<input id="idis_tax" name="is_tax" type='checkbox' value='true' <?php if( $this->_vars['order']['is_tax'] == 'true' ){ ?>checked="checked"<?php } ?>></td>
    <th>支付币别：</th>
    <td><?php if( $this->_vars['order']['order_id'] == '' ){  echo $this->__view_helper_model['base_view_helper']->function_html_options(array('options' => $this->_vars['order']['curList'],'selected' => $this->_vars['order']['currency']), $this); }else{  echo $this->_vars['order']['cur_name'];  if( $this->_vars['order']['cur_rate'] != 1 ){ ?>(<?php echo $this->_vars['order']['cur_rate']; ?>)<?php }  } ?></td>
  </tr>
   <tr>
    <th>促销优惠金额：</th>
    <td><input id="idpmt_order" class='item itemrow' name="pmt_order" value="<?php echo $this->_vars['order']['pmt_order']; ?>" size=10></td>
    <th>订单折扣或涨价：</th>
    <td><input id="iddiscount" class='item itemrow' name="discount" value="<?php echo $this->_vars['order']['discount']; ?>" size=10><br>要给顾客便宜100元，则输入"-100";要提高订单价格100元，则输入"100".
  </tr>
   <tr>
    <th>订单总金额：</th>
    <td><input id="idtotal_amount" name=total_amount value="<?php echo $this->_vars['order']['total_amount']; ?>" disabled="disabled"></td>

</td>
  </tr>
  
</table>
</div>

<h3>购买人信息</h3>
<div class="division">
  <?php if( $this->_vars['order']['order_id'] == '' ){ ?>
  <input TYPE="text" NAME="uname" value='' class=inputstyle size=15>
  <input TYPE="button"  class=inputstyle value="导入会员" onClick="seluser(adminForm.uname.value)">
  <input TYPE="button"  class=inputstyle value="非会员" onClick="seluser('anonymous')">
  <input TYPE="hidden" name="userid" value="{userid}"> 
  <?php } ?>
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
  
   <tr>
    <th>会员用户名：</th>
    <td><?php echo $this->_vars['order']['member']['pam_account']['login_name']; ?></td>
    <th>姓名：</th>    
    <td><?php echo $this->_vars['order']['member']['contact']['name']; ?></td>
  </tr>
   <tr>
    <th>固定电话：</th>
    <td><?php echo $this->_vars['order']['member']['contact']['phone']['telephone']; ?></td>
    <th>Email地址：</th>
    <td><?php echo $this->_vars['order']['member']['contact']['email']; ?></td>
  </tr>
  <tr>
    <th>移动电话：</th>
    <td><?php echo $this->_vars['order']['member']['contact']['phone']['mobile']; ?></td>
    <th></th>
    <td></td>
  </tr>
   <tr>
    <th>地区：</th>
    <td><?php echo kernel::single('base_view_helper')->modifier_region($this->_vars['order']['member']['contact']['area']); ?></td>
    <th>邮政编码：</th>
    <td><?php echo $this->_vars['order']['member']['contact']['zipcode']; ?></td>
  </tr>
   <tr>
    <th>地址：</th>
    <td><?php echo $this->_vars['order']['member']['contact']['addr']; ?></td>
    <th></th>
    <td></td>
  </tr>
</table>
</div>


<?php if( $this->_vars['order']['is_delivery'] == 'Y' ){ ?>
 <h3>收货人信息</h3>
 <div class="division" id="order_edit_receiver">
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr>
    <th><em class="red">*</em>收货人姓名：</th>
    <td><?php echo $this->ui()->input(array('type' => "text",'NAME' => "receiver_name",'required' => "true",'value' => $this->_vars['order']['consignee']['name']));?></td>
    <th>联系手机：</th>
    <td><input type="text" NAME="ship_mobile" value="<?php echo $this->_vars['order']['consignee']['mobile']; ?>" class=inputstyle></td>
  </tr>
   <tr>
    <th>联系电话：</th>
    <td><?php echo $this->ui()->input(array('type' => "order_tel",'NAME' => "ship_tel",'class' => inputstyle,'value' => $this->_vars['order']['consignee']['telephone']));?></td>
    <th>Email地址：</th>
    <td><?php if( $this->_vars['order']['member']['contact']['email'] ){  echo $this->_vars['order']['member']['contact']['email'];  }else{ ?><input vtype="email" name="ship_email" value="<?php echo $this->_vars['order']['consignee']['email']; ?>"/><?php } ?></td>
  </tr>
   <tr>
    <th>送货时间：</th>
    <td><input type="text" NAME="ship_time" value="<?php echo $this->_vars['order']['consignee']['r_time']; ?>" class=inputstyle></td>
    <th>邮政编码：</th>
    <td><?php echo $this->ui()->input(array('NAME' => "ship_zip",'class' => inputstyle,'value' => $this->_vars['order']['consignee']['zip']));?></td>
  </tr>
   <tr>
    <th><em class="red">*</em>收货地区：</th>
    <td colspan="3"><?php echo $this->ui()->input(array('type' => "region",'name' => "ship_area",'required' => "true",'value' => $this->_vars['order']['consignee']['area'],'app' => "ectools"));?></td>
  </tr>
  <tr>
    <th><em class="red">*</em>收货地址：</th>
    <td colspan="3"><?php echo $this->ui()->input(array('type' => "text",'NAME' => "ship_addr",'required' => "true",'class' => inputstyle,'value' => $this->_vars['order']['consignee']['addr']));?></td>
  </td>
</table>
</div>
</div>
 <?php } ?> 
<!--
<div class="table-action">
    <?php echo $this->ui()->button(array('label' => "提交",'type' => "submit",'name' => 'butsubmit'));?>
</div>-->


</form>
<script>

	
	 $ES(".itemrow","moneyItems").each(function(item, index){
		item.addEvent('change',function(e){count_change(this);});
	  });

 
 function count_change(ipt){
 if(ipt.className.split('_').length > 1){

	   var key=(ipt.className.split('_')[1]);
	   var key = key.split(' ')[0];
	   var item_unit_price = $E('.itemPrice_'+key,'moneyItems').getValue();
	   var item_unit_quantity = $E('.itemNum_'+key,'moneyItems').getValue();

	   var json_arr = '{"unit_price":'+item_unit_price+', "item_quantity":'+item_unit_quantity+'}';
	   new Request({
			   data:"json_arr="+json_arr+"&operaction=multiple", 
		onSuccess:function(response){		
		   $E('.itemSub_'+key).setText(response);
		   countF();		
		}
	   }).post('index.php?app=b2c&ctl=admin_order&act=caculate_item_total');
	   //var result=$E('.itemPrice_'+key,'moneyItems').getValue().toFloat()*$E('.itemNum_'+key,'moneyItems').getValue().toInt();
	   //$E('.itemSub_'+key).setText(result);
	  }
	  //countF();
	
 }
 
function countF()
{
	var count=0;
	var json_arr='{';
	var index = 0;
	var item_total = 0;
	// item total
	$ES(".itemCount","moneyItems").each(function(item){
	 //count += item.getText().toFloat()*1;
	 	if (index == 0)
	 		json_arr += '"'+index.toString()+'":'+item.getText();
		else
			json_arr += ','+'"'+index.toString()+'":'+item.getText();
	
		index++;
	});      
	
	json_arr += '}';

   	// subitemtotal.
	new Request({
	   		data:"json_arr="+json_arr+"&operaction=plus", 
			onSuccess:function(response){
				$('iditem_amount').set('value',response);
			
				json_arr='{';
				json_arr += '"item_subtotal":'+response+'';
				// cost protect fee.
			   	var cost_protect = 0;
				if($('idis_protect').checked){
				    cost_protect = $('idcost_protect').value;
				    json_arr += ',"cost_protect":'+cost_protect;
				}   
				// cost tax fee.
				var cost_tax = 0;
				if($('idis_tax').checked){
				    cost_tax = $('idcost_tax').value;
				    json_arr += ',"cost_tax":'+cost_tax;
				}
			
				json_arr += ',"cost_freight":'+$('idcost_freight').value;
				json_arr += ',"cost_payment":'+$('idcost_payment').value;
				json_arr += ',"discount":'+$('iddiscount').value;
				json_arr += ',"pmt_order":-'+$('idpmt_order').value;
				/*var count = 0;
				 count = Number($('iditem_amount').value) + cost_protect + $('idcost_freight').value.toFloat() + Number($('idcost_payment').value) + cost_tax + Number($('iddiscount').value) - Number($('idpmt_order').value);*/
				json_arr += '}';
			
				new Request({
					data:"json_arr="+json_arr+"&operaction=plus",
					onSuccess:function(res)
					{
						$('idtotal_amount').value = res;
					}
				}).post('index.php?app=b2c&ctl=admin_order&act=caculate_item_total');	
		}
	}).post('index.php?app=b2c&ctl=admin_order&act=caculate_item_total');
}
 
 $('idis_protect').addEvent('click',function(e){
  $('idcost_protect').disabled = !$('idis_protect').checked;
  countF();
 });
 
 $('idis_tax').addEvent('click',function(e){
  $('idcost_tax').disabled = !$('idis_tax').checked;
  countF();
//  if($('idis_tax').checked){
//   $('idtax_company').style.display = 'block';
//  }else{
//   $('idtax_company').style.display = 'none';
//  }
 });

$('idcost_tax').disabled = !$('idis_tax').checked;

 if(!$('idis_protect').checked){
  $('idcost_protect').disabled = true;
 }
var extra_validator={};
if(!extra_validator['subOrder']){
  extra_validator['subOrder'] ={
    'order_tel':['请至少输入联系电话和联系手机中的一项',function(f,i){
        var tel = $('order_edit_receiver').getElement('input[name=ship_tel]').getProperty('value');
        var mob = $('order_edit_receiver').getElement('input[name=ship_mobile]').getProperty('value');
        if(tel.trim() == '' && mob.trim() == ''){
          return false;
        }else{
            return true;
        }
      }]
  };
}
</script>
";s:6:"expire";i:0;}