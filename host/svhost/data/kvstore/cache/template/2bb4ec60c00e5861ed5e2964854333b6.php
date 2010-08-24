<?php exit(); ?>a:2:{s:5:"value";s:4343:"<div class="division">
<table width="100%" border="0" cellpadding="0" cellspacing="0" >
<tbody>
  <tr>
    <th><em class="c-red">*</em>收货地区：</td>
    <td colspan=2>
      <span id="checkout-select-area"><?php echo $this->ui()->input(array('type' => "region",'id' => "shipping-area",'name' => "delivery[ship_area]",'app' => "ectools",'required' => "true",'value' => $this->_vars['addr']['area']));?></span>
    </td>
  </tr>
  <tr>
    <th><em class="c-red">*</em>收货地址：</td>
    <td>
    <input type='hidden' name='delivery[ship_addr_area]' value='' id='selected-area-hidden'/>    
    <span id='selected-area' class='fontcolorGray' title='系统将拼接地区选择结果到收获地址'>[地区]</span>
    <?php echo $this->ui()->input(array('class' => "inputstyle",'name' => "delivery[ship_addr]",'id' => "addr",'vtype' => "required",'value' => $this->_vars['addr']['addr'],'size' => "50"));?>
    </td>
  </tr>
  <tr>
   <th>
	邮编：</th>
    <td>
	<?php echo $this->ui()->input(array('class' => "inputstyle",'name' => "delivery[ship_zip]",'size' => "15",'id' => "zip",'type' => "text",'value' => $this->_vars['addr']['zipcode']));?>
	</td>
  </tr>
  <tr>
    <th><em class="c-red">*</em>收货人姓名：</td>
    <td colspan=2><?php echo $this->ui()->input(array('class' => "inputstyle",'name' => "delivery[ship_name]",'size' => "15",'id' => "name",'required' => "true",'type' => "text",'value' => $this->_vars['addr']['name']));?></td>
  </tr>
  <?php if( !$this->_vars['address']['member_id'] ){ ?>
  <tr>
    <th><em class="c-red">*</em>Email：</td>
    <td colspan=2><?php echo $this->ui()->input(array('name' => "delivery[ship_email]",'class' => "inputstyle",'id' => "ship_email",'size' => "15",'required' => "true",'type' => "text",'vtype' => "email",'value' => $this->_vars['addr']['email']));?></td>
  </tr>
  <?php } ?>
  <tr>
    <th>
      <em class="c-red">*</em>手机：
    </td>
    <td colspan=2>
     <?php echo $this->ui()->input(array('class' => "inputstyle",'name' => "delivery[ship_mobile]",'size' => "15",'type' => "text",'id' => "mobile",'value' => $this->_vars['addr']['phone']['mobile']));?>
    </td>
  </tr>
  <tr>
    <th>
      固定电话：
    </td>
    <td colspan=2>
     <?php echo $this->ui()->input(array('class' => "inputstyle",'name' => "delivery[ship_tel]",'size' => "15",'type' => "text",'id' => "tel",'value' => $this->_vars['addr']['phone']['telephone']));?>
    </td>
  </tr>
  <?php if( $this->_vars['address']['member_id'] ){ ?>
  <tr class="recsave">
    <th></th>
    <td colspan=2><label><input name="delivery[is_save]" type="checkbox" checked="checked" value=1>保存此收货地址</label></td>
  </tr>
  <?php } ?>
  </tbody>
</table>
</div>
<?php echo $this->_vars['selectArea']; ?>
<script>
window.addEvent('domready',function(){

	$E('#checkout-select-area input[name^=delivery[]').store('onselect',function(sel){

        if($E('option[has_c]',sel)){
            $('shipping').set('html','<div class="valierror clearfix" style=" padding-left:20px">请选择收货地区</div>');   
            $('selected-area').set('text','[地区]').removeClass('fontcolorBlack').addClass('fontcolorGray');
        }

    });

    $E('#checkout-select-area input[name^=delivery[]').store('lastsel',function(lastselect){
        var areaSels=$ES("#checkout-select-area select");
        var areaSelPrt=areaSels[0].getParent('*[package=mainland]');
        var selected=[];
        areaSels.each(function(s){
           var text = s[s.selectedIndex].text.trim().clean();
           if(['北京','天津','上海','重庆'].indexOf(text)>-1)return;
           selected.push(text);
        });
        var selectedV = selected.join('');
        $('selected-area').setText(selectedV).removeClass('fontcolorGray').addClass('fontcolorBlack');
        $('selected-area-hidden').value =  selectedV;
        $('addr').set('value',$('addr').value.replace(selectedV,''));
        Order.setShippingFromArea(lastselect);
    });
    var areaSels=$ES("#checkout-select-area select");

    var lastSel=areaSels[areaSels.length-1];
	
	if( lastSel.get('value') != '' && lastSel.get('value') != '_NULL_' );
    
	 lastSel.onchange(lastSel,lastSel.value,(areaSels.lengtd-1));
	
});
</script>";s:6:"expire";i:0;}