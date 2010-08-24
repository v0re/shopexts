<?php exit(); ?>a:2:{s:5:"value";s:4048:"<?php if( $this->_vars['minfo'] ){  if($this->_vars['minfo'])foreach ((array)$this->_vars['minfo'] as $this->_vars['product_id'] => $this->_vars['product']){ ?>
<div class='FormWrap'>
	<h5 style="border-bottom:1px solid #ccc; padding:3px 0">填写购买&nbsp;&nbsp;<?php echo $this->_vars['product']['name'];  if( $this->_vars['product']['nums']>1 ){ ?> x <?php echo $this->_vars['product']['nums'];  } ?>&nbsp;&nbsp;时所需信息</h5>
	<div class="field section" >
		<div>
		  <table width="100%" cellspacing="0" cellpadding="0" border="0" class="liststyle data">
			  <col class="span-5"></col>
			  <col></col>
			  <tbody>
					 <?php if($this->_vars['product']['minfo'])foreach ((array)$this->_vars['product']['minfo'] as $this->_vars['key'] => $this->_vars['info']){  $this->_vars["infokey"]=$this->_vars['info']['name']; ?>
					  <tr>
					  <th>
						<em class="c-red">*</em><label for="misc_<?php echo $this->_vars['key']; ?>"><?php echo $this->_vars['info']['label']; ?>：</label>
						<input type="hidden" name="minfo[<?php echo $this->_vars['product_id']; ?>][<?php echo $this->_vars['info']['name']; ?>][label]" value="<?php echo $this->_vars['info']['label']; ?>">
					  </th>
					  <td>
						<?php if( $this->_vars['info']['type']=='select' ){ ?>
						<select class="inputstyle x-input" name="minfo[<?php echo $this->_vars['product_id']; ?>][<?php echo $this->_vars['info']['name']; ?>][value]">
						<?php if($this->_vars['info']['options'])foreach ((array)$this->_vars['info']['options'] as $this->_vars['opt']){ ?>
						<option value="<?php echo $this->_vars['opt']; ?>"><?php echo $this->_vars['opt']; ?></option>
						<?php } ?>
						</select>
						<?php }elseif( $this->_vars['info']['type']=='text' ){  echo $this->ui()->input(array('class' => "inputstyle x-input",'type' => "textarea",'rows' => "3",'cols' => "40",'id' => "misc_{$this->_vars['key']}",'name' => "minfo[{$this->_vars['product_id']}][{$this->_vars['infokey']}][value]",'vtype' => "required")); }else{  echo $this->ui()->input(array('class' => "inputstyle",'id' => "misc_{$this->_vars['key']}",'size' => "30",'name' => "minfo[{$this->_vars['product_id']}][{$this->_vars['infokey']}][value]",'type' => 'required')); } ?>
					  </td>
					  </tr>
					  <?php } ?> 
			  </tbody>
		  </table>
			 
		  </div>
	</div>
</div>
<?php }  } ?>

<div class="FormWrap checkoutbase" >
  <div class="section">
    <div class="form-title">
      <h5>
      <span>1</span>
      收货信息确认
      <em class="c-red">*</em>
      </h5>
    </div>
    <div class="form-body">
      <?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"site/common/receiver.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
    </div>
  </div>

  <div class="section">
    <div class="form-title">
      <h5>
      <span>2</span>
      配送方式确认
      <em class="c-red">*</em>
      </h5>
    </div>
    <div class="form-body">
      <div id="shipping">
        <?php if( $this->_vars['delivery_html'] ){  echo $this->_vars['delivery_html'];  }else{ ?>
          <div class="notice" >
          请先“在收货人信息”中选择“收货地区”
          </div>
        <?php } ?>
      </div>
    </div>
  </div>

  <div class="section">
    <div class="form-title">
      <h5>
        <span>3</span>
        支付方式确认
        <em class="c-red">*</em>
      </h5>
    </div>
    <div class="form-body">
      <div id="_payment_currency" style="margin:0 0 10px;">
      货币类型：
      <?php echo $this->ui()->input(array('type' => "select",'id' => "payment-cur",'name' => "payment[currency]",'rows' => $this->_vars['currencys'],'valueColumn' => "cur_code",'labelColumn' => "cur_name",'value' => $this->_vars['current_currency'],'class' => "inputstyle",'required' => "true"));?>
      </div>
      <div id='payment'>
        <?php echo $this->_vars['payment_html']; ?>
      </div>
    </div>
  </div>
</div>";s:6:"expire";i:0;}