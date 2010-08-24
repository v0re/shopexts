<?php exit(); ?>a:2:{s:5:"value";s:9435:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><form class="tableform" method='post' action='index.php?app=b2c&ctl=admin_order&act=dodelivery' id="order-delivery-form">
  <input type='hidden' name='order_id' value='<?php echo $this->_vars['order']['order_id']; ?>'>

<?php if( $this->_vars['order']['is_delivery'] == "Y" ){ ?>
<div class="division">
<table cellpadding="0" cellspacing="0">
<tr>
    <th>订单号:</th>
    <td><?php echo $this->_vars['order']['order_id']; ?> 【<?php if( $this->_vars['order']['ship_status'] == 0 ){ ?>未发货<?php }elseif( $this->_vars['order']['ship_status'] == 1 ){ ?>已发货<?php }elseif( $this->_vars['order']['ship_status'] == 2 ){ ?>部分发货<?php }elseif( $this->_vars['order']['ship_status'] == 3 ){ ?>部分退货<?php }elseif( $this->_vars['order']['ship_status'] == 4 ){ ?>已退货<?php } ?>】</td>
    <th>下单日期:</th>
    <td><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['order']['createtime'],'SDATE_STIME'); ?></td>
</tr>
<?php if( $this->_vars['order']['is_delivery'] == "Y" ){ ?>
<tr>
  <th>配送方式:</th>
  <td><?php echo $this->ui()->input(array('type' => "select",'name' => 'delivery','rows' => $this->_vars['shippings'],'valueColumn' => "dt_id",'labelColumn' => "dt_name",'value' => $this->_vars['order']['shipping']['shipping_id']));?></td>
  <th>配送费用:</th>
  <td><?php echo $this->_vars['order']['shipping']['cost_shipping']; ?></td>
</tr>
<tr><!--
  <th>配送地区:</th>
  <td><?php echo $this->_vars['order']['shipping_area']; ?></td> -->
  <th>是否要求保价:</th>
  <td colspan="3"><?php if( $this->_vars['order']['shipping']['is_protect'] == 'true' ){ ?>是 （保价费用 ＋<?php echo $this->_vars['order']['cost_protect']; ?>）<?php }else{ ?>否<?php } ?></td>
</tr>
<?php } ?>
    <tr>
      <th>物流公司:</th>
    <td><?php echo $this->ui()->input(array('type' => "select",'name' => 'logi_id','rows' => $this->_vars['corplist'],'valueColumn' => "corp_id",'labelColumn' => "name",'value' => $this->_vars['corp_id'],'onchange' => "$(this).get('value')=='other'?$('otherinput').show():$('otherinput').hide()"));?>&nbsp;&nbsp;
	<span id='otherinput' style="display:none"><input type="text" class='_x_ipt' vtype='text' required='true' size='10' name='other_name'></span>
	</td>
    <th>物流单号:</th>
    <td><?php echo $this->ui()->input(array('type' => 'text','name' => 'logi_no','value' => $this->_vars['order']['account'],'width' => "100"));?></td>
    </tr>
  <tr>
    <th>物流费用:</th>
    <td><?php echo $this->ui()->input(array('type' => 'text','name' => 'money','value' => $this->_vars['order']['shipping']['cost_shipping'],'width' => "50"));?></td>
    <th>物流保价:</th>
    <td>
      <?php echo $this->ui()->input(array('name' => "is_protect",'type' => "radio",'options' => $this->_vars['order']['protectArr'],'value' => $this->_vars['order']['shipping']['is_protect']));?></td>
  </tr>
  <tr>
        <th>保价费用:</th>
    <td colspan="3"><?php echo $this->ui()->input(array('type' => 'text','name' => 'cost_freight','value' => $this->_vars['order']['shipping']['cost_protect'],'width' => "50"));?></td>
  </tr>
    <tr>
      <th>收货人姓名:</th>
    <td><?php echo $this->ui()->input(array('type' => 'text','name' => 'ship_name','value' => $this->_vars['order']['consignee']['name'],'width' => "80"));?></td>
    <th>电话:</th>
    <td><?php echo $this->ui()->input(array('type' => 'text','name' => 'ship_tel','value' => $this->_vars['order']['consignee']['telephone'],'width' => "150"));?></td>
    </tr>
  <tr>
    <th>手机:</th>
    <td><?php echo $this->ui()->input(array('type' => 'text','name' => 'ship_mobile','value' => $this->_vars['order']['consignee']['mobile'],'width' => "150"));?></td>
		<th>邮政编码:</th>
		<td><?php echo $this->ui()->input(array('type' => 'text','name' => 'ship_zip','value' => $this->_vars['order']['consignee']['zip'],'width' => "80"));?></td>
  </tr>
    <tr>
    <th>地区:</th>
		<td colspan="3"><?php echo $this->ui()->input(array('type' => 'region','app' => "ectools",'name' => 'ship_area','value' => $this->_vars['order']['consignee']['area']));?></td>
	</tr>
  <tr>
		<th>地址:</th>
		<td colspan="3"><?php echo $this->ui()->input(array('type' => 'text','name' => 'ship_addr','value' => $this->_vars['order']['consignee']['addr'],'style' => "width:360px"));?></td>
  </tr>
  <tr>
    <th>发货单备注:</th>
    <td colspan="3"><?php echo $this->ui()->input(array('type' => 'textarea','name' => 'memo','style' => "width:95%",'value' => $this->_vars['order']['memo']));?></td>
  </tr>
</table>
</div>
<?php }  if( count($this->_vars['items']) > 0 ){ ?>
<div class="division">
<table cellpadding="0" cellspacing="0"  class="gridlist">
					<col style="width:20%"></col>
					<col style="width:35%"></col>
					<col style="width:15%"></col>
					<col style="width:10%"></col>
					<col style="width:10%"></col>
					<col style="width:10%"></col>
      <thead>
        <tr>
          <th>货号</th>
          <th>商品名称</th>
          <th>当前库存</th>
          <th>购买数量</th>
          <th>已发货</th>
          <th>此单发货</th>
        </tr>
      </thead>
      <tbody>      <?php if($this->_vars['items'])foreach ((array)$this->_vars['items'] as $this->_vars['aProduct']){ ?>
      <tr>
        <td><?php echo $this->_vars['aProduct']['bn']; ?></td>
        <td><?php echo $this->_vars['aProduct']['name'];  if($this->_vars['aProduct']['minfo'])foreach ((array)$this->_vars['aProduct']['minfo'] as $this->_vars['name'] => $this->_vars['minfo']){ ?>
          <br><?php echo $this->_vars['minfo']['label']; ?>：<?php echo $this->_vars['minfo']['value'];  } ?>
          <!--<?php echo $this->_vars['aProduct']['addon']['adjname']; ?>--></td>
        <td><?php echo $this->_vars['aProduct']['products']['store']; ?></td>
        <td><?php echo $this->_vars['aProduct']['quantity']; ?></td>
        <td><?php echo $this->_vars['aProduct']['sendnum']; ?></td>
        <td><?php $this->_vars["nums"]=$this->_vars['aProduct']['quantity'];  $this->_vars["sendnum"]=$this->_vars['aProduct']['sendnum'];  $this->_vars["pid"]=$this->_vars['aProduct']['product_id'];  $this->_vars["item_id"]=$this->_vars['aProduct']['item_id'];  if( $this->_vars['aProduct']['needsend'] > 0 ){  echo $this->ui()->input(array('type' => "text",'vtype' => "required&&number",'name' => "send[{$this->_vars['item_id']}]",'value' => $this->_vars['aProduct']['needsend'],'width' => "50")); }else{ ?>已经发货<?php } ?></td>
      </tr>
      <?php } ?>
      </tbody>
    </table>
</div>
<?php }  if( count($this->_vars['giftItems']) > 0 ){ ?>
<div class="division">
<table class="gridlist" cellpadding="0" cellspacing="0">
      <thead>
        <tr>
          <th>赠品名称</th>
          <th>兑换积分</th>
		  <th>当前库存</th>
          <th>已发货/兑换量</th>
          <th>需发货</th>
        </tr>
      </thead>
      <tbody>      <?php if($this->_vars['giftItems'])foreach ((array)$this->_vars['giftItems'] as $this->_vars['aGift']){ ?>
      <tr>
        <td><?php echo $this->_vars['aGift']['name']; ?></td>
        <td><?php echo $this->_vars['aGift']['point']; ?></td>
		<th><?php echo $this->_vars['aGift']['store']; ?></th>
        <td><?php echo $this->_vars['aGift']['sendnum']; ?>/<?php echo $this->_vars['aGift']['nums']; ?></td>
        <td><?php $this->_vars["item_id"]=$this->_vars['aGift']['item_id'];  if( $this->_vars['aGift']['needsend'] > 0 ){ ?><input type="text" name="gift_send[<?php echo $this->_vars['item_id']; ?>]" value="<?php echo $this->_vars['aGift']['needsend']; ?>" size="3"><?php }else{ ?>已经发货<?php } ?></td>
      </tr>
      <?php } ?>
    </table>
</div>
<?php } ?>

</form>

<?php $this->_tag_stack[] = array('area', array('inject' => '.mainFoot')); $this->__view_helper_model['base_view_helper']->block_area(array('inject' => '.mainFoot'), null, $this); ob_start(); ?>
<div class="table-action">
	<?php echo $this->ui()->button(array('label' => "发货",'id' => "order-delivery-form-submit",'type' => "submit"));?>
</div>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_area($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>

<script type="text/javascript">
(function(){
	var _form = $('order-delivery-form');
	var btn =$('order-delivery-form-submit');
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