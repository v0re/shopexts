<?php exit(); ?>a:2:{s:5:"value";s:4077:"<?php if( $this->_vars['goods']['product'] ){  $this->_vars[product]=current($this->_vars['goods']['product']);  } ?>
<input type='hidden' name='goods[product][0][status]' value='true'/>
<table border="0" cellpadding="0" cellspacing="0" id="nospec_body">
<tbody>  
  <tr>
    <th>销售价：<input type='hidden' name='goods[product][0][product_id]' value='<?php echo $this->_vars['product']['product_id']; ?>'></th>
    <td>
    <?php echo $this->ui()->input(array('type' => "unsigned",'key' => "price",'value' => $this->_vars['product']['price']['price']['price'],'name' => "goods[product][0][price][price][price]",'style' => "width:60px",'maxlength' => "25")); echo $this->ui()->button(array('type' => "button",'label' => "编辑会员价格",'onclick' => "goodsEditor.mprice.bind(goodsEditor)(this)",'icon' => "btn_edit.gif",'app' => 'desktop'));?>
		<span>
    <?php if($this->_vars['mLevels'])foreach ((array)$this->_vars['mLevels'] as $this->_vars['lv']){ ?>
	<input type="hidden" key="member_lv_price_<?php echo $this->_vars['lv']['member_lv_id']; ?>" name="goods[product][0][price][member_lv_price][<?php echo $this->_vars['lv']['member_lv_id']; ?>]" level="<?php echo $this->_vars['lv']['member_lv_id']; ?>" value="<?php if( $this->_vars['product']['price']['member_lv_price'][$this->_vars['lv']['member_lv_id']][custom] == 'true' ){  echo $this->_vars['product']['price']['member_lv_price'][$this->_vars['lv']['member_lv_id']][price];  } ?>" type="money" vtype="mprice" />
      <?php } ?>
	  </span>
	  </td>
  </tr>
  <tr>
    <th>成本价：</th>
    <td><?php echo $this->ui()->input(array('type' => "unsigned",'key' => "cost",'name' => "goods[product][0][price][cost][price]",'maxlength' => "30",'style' => "width:60px",'value' => $this->_vars['product']['price']['cost']['price']));?><span class="notice-inline">前台不会显示，仅供后台使用。</span></td>
  </tr>
  <tr>
	<th>市场价：</th>
	<td><?php echo $this->ui()->input(array('type' => "unsigned",'key' => "mktprice",'name' => "goods[product][0][price][mktprice][price]",'maxlength' => "30",'style' => "width:60px",'value' => $this->_vars['product']['price']['mktprice']['price']));?></td>
  </tr>
  <tr>
    <th>货号：</th>
    <td><?php echo $this->ui()->input(array('type' => "text",'value' => $this->_vars['product']['bn'],'key' => "bn",'name' => "goods[product][0][bn]",'maxlength' => "25"));?></td>
  </tr>
  <tr>
    <th>重量：</th>
    <td><?php echo $this->ui()->input(array('type' => "unsigned",'key' => "weight",'value' => $this->_vars['product']['weight'],'name' => "goods[product][0][weight]",'style' => "width:60px",'maxlength' => "25"));?>克(g)</td>
  </tr>
  <?php if( $this->_vars['goods']['type']['is_physical'] ){ ?>
  <tr>
    <th>库存：</th>
    <td><?php echo $this->ui()->input(array('type' => "unsigned",'key' => "store",'value' => $this->_vars['product']['store'],'name' => "goods[product][0][store]",'style' => "width:60px",'maxlength' => "25"));?></td>
  </tr>
  <?php }  if( $this->_vars['storeplace'] == 'true' ){ ?>
    <tr>
        <th>货位：</th>
        <td><?php echo $this->ui()->input(array('type' => "text",'key' => "store_place",'value' => $this->_vars['goods']['store_place'],'name' => "goods[product][0][store_place]",'maxlength' => "25"));?></td>
    </tr>
    <?php } ?>
  <tr class="advui">
    <th>规格：</th>
    <td style="padding:4px 0">
        <?php echo $this->ui()->button(array('icon' => "btn_add.gif",'label' => "开启规格",'app' => "desktop",'id' => "open_spec",'onclick' => "goodsEditor.spec.addCol.bind(goodsEditor)(false,$('gEditor-GType-input').get('value'))"));?>
        <span class="notice-inline">开启规格前先填写以上价格等信息，可自动复制信息到货品</span>
    </td>
  </tr>
</tbody>
</table>
<script>
(function(){
	$('open_spec').addEvent('click',function(e){
		var hsspec=new Hash();	
		$ES('#nospec_body input[key]').each(function(ipt){
			if(ipt.value.trim().length)
			hsspec.set(ipt.get('key'),ipt.value);
		});	
		$('goods-spec').store('hsspec',hsspec);
	});
})();
</script>
";s:6:"expire";i:0;}