<?php exit(); ?>a:2:{s:5:"value";s:8882:"<div style="border: 1px solid #efefef;" class="table-grid">  
  <input type="hidden" name="order_id" value="<?php echo $this->_vars['order']['order_id']; ?>" />
  <table width="100%" id="order_items" border="0" cellspacing="0" cellpadding="2" class="gridlist">
  <col class="Colsn"></col>
  <col></col>
  <col class="Colamount"></col>
  <col class="Colamount"></col>
  <col class="Colamount"></col>
  <col class="Coloption-1b"></col>
    <thead>
      <tr>
        <th>货号</th>
        <th>商品名称</th>
        <th>价格</th>
        <th>购买数量</th>
        <th>小计</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
    <?php if($this->_vars['order']['items'])foreach ((array)$this->_vars['order']['items'] as $this->_vars['iLoop'] => $this->_vars['aItems']){  $this->_vars["itemid"]=$this->_vars['aItems']['products']['product_id'];  $this->_vars["itemidGoodsid"]=$this->_vars['aItems']['products']['goods_id'];  $this->_vars["itemprice"]=$this->_vars['aItems']['price'];  $this->_vars["itemnums"]=$this->_vars['aItems']['quantity']; ?>
    <tr>
      <input type="hidden" name='aItems[product_id][<?php echo $this->_vars['aItems']['products']['product_id']; ?>_<?php echo $this->_vars['iLoop']; ?>]' value='<?php echo $this->_vars['aItems']['products']['product_id']; ?>' />
      <input type="hidden" name='aItems[object_id][<?php echo $this->_vars['aItems']['products']['product_id']; ?>_<?php echo $this->_vars['iLoop']; ?>]' value='<?php echo $this->_vars['iLoop']; ?>' />
      <td><?php echo $this->_vars['aItems']['bn']; ?></td>
      <td><?php echo $this->_vars['aItems']['name']; ?></td>
      <td><?php echo $this->ui()->input(array('type' => "unsigned",'required' => "true",'class' => "itemPrice_{$this->_vars['itemid']}-{$this->_vars['iLoop']} itemrow",'name' => "aPrice[{$this->_vars['itemid']}_{$this->_vars['iLoop']}]",'value' => $this->_vars['itemprice'],'size' => 8));?></td>
      <td><?php echo $this->ui()->input(array('type' => "positive",'required' => "true",'class' => "itemNum_{$this->_vars['itemid']}-{$this->_vars['iLoop']} itemrow",'name' => "aNum[{$this->_vars['itemid']}_{$this->_vars['iLoop']}]",'value' => $this->_vars['itemnums'],'size' => 4));?></td>
      <td class="itemSub_<?php echo $this->_vars['itemid']; ?>-<?php echo $this->_vars['iLoop']; ?> itemCount Colamount"><?php echo $this->_vars['aItems']['amount']; ?></td>
      <td><?php echo $this->ui()->img(array('src' => "bundle/delecate.gif",'title' => "删除",'style' => " cursor:pointer",'onClick' => "delgoods(this)",'app' => 'desktop'));?></td>
    </tr>
    <?php if( $this->_vars['aItems']['adjunct'] ){ ?>
    	<tr>
    	<td colspan="6">
    	<table width="100%" id="order_items" border="0" cellspacing="0" cellpadding="2" class="gridlist">
    	<tbody>
		<thead>
		<tr>
			<td colspan="6">
				商品配件项
			</td>
		</tr>
		</thead>
	    <?php if($this->_vars['aItems']['adjunct'])foreach ((array)$this->_vars['aItems']['adjunct'] as $this->_vars['ajunctILoop'] => $this->_vars['ajunctItems']){  $this->_vars["ajunctItemid"]=$this->_vars['ajunctItems']['products']['product_id'];  $this->_vars["ajunctItemprice"]=$this->_vars['ajunctItems']['price'];  $this->_vars["ajunctItemnums"]=$this->_vars['ajunctItems']['quantity']; ?>
		<input type="hidden" name='ajunctItems[goods_id]' value='<?php echo $this->_vars['itemidGoodsid']; ?>' />
	    	<tr>
				<input type="hidden" name='ajunctItems[product_id][<?php echo $this->_vars['ajunctItems']['products']['product_id']; ?>_<?php echo $this->_vars['iLoop']; ?>]' value='<?php echo $this->_vars['ajunctItems']['products']['product_id']; ?>' />
	    		<input type="hidden" name='ajunctItems[object_id][<?php echo $this->_vars['ajunctItems']['products']['product_id']; ?>_<?php echo $this->_vars['iLoop']; ?>]' value='<?php echo $this->_vars['iLoop']; ?>' />
	    		<td><?php echo $this->_vars['ajunctItems']['bn']; ?></td>
			    <td><?php echo $this->_vars['ajunctItems']['name']; ?></td>
			    <td><?php echo $this->ui()->input(array('type' => "unsigned",'required' => "true",'class' => "itemPrice_{$this->_vars['ajunctItemid']}-{$this->_vars['iLoop']} itemrow",'name' => "ajunctPrice[{$this->_vars['ajunctItemid']}_{$this->_vars['iLoop']}]",'value' => $this->_vars['ajunctItemprice'],'size' => 8));?></td>
			    <td><?php echo $this->ui()->input(array('type' => "positive",'required' => "true",'class' => "itemNum_{$this->_vars['ajunctItemid']}-{$this->_vars['iLoop']} itemrow",'name' => "ajunctNum[{$this->_vars['ajunctItemid']}_{$this->_vars['iLoop']}]",'value' => $this->_vars['ajunctItemnums'],'size' => 4));?></td>
			    <td class="itemSub_<?php echo $this->_vars['ajunctItemid']; ?>-<?php echo $this->_vars['iLoop']; ?> itemCount Colamount"><?php echo $this->_vars['ajunctItems']['amount']; ?></td>
			    <td><?php echo $this->ui()->img(array('src' => "bundle/delecate.gif",'title' => "删除",'style' => " cursor:pointer",'onClick' => "delgoods(this)",'app' => 'desktop'));?></td>
	    	</tr>
	    <?php } ?>
	    </tbody>
	    </table>
	    </td>
	    </tr>
    <?php }  if( $this->_vars['aItems']['gifts'] ){ ?>
    	<tr>
    	<td colspan="6">
    	<table width="100%" id="order_items" border="0" cellspacing="0" cellpadding="2" class="gridlist">
    	<tbody>
		<thead>
		<tr>
			<td colspan="6">
				商品赠品项
			</td>
		</tr>
		</thead>
	    <?php if($this->_vars['aItems']['gifts'])foreach ((array)$this->_vars['aItems']['gifts'] as $this->_vars['giftsILoop'] => $this->_vars['giftsItems']){  $this->_vars["giftsItemid"]=$this->_vars['giftsItems']['products']['product_id'];  $this->_vars["giftsItemprice"]=$this->_vars['giftsItems']['price'];  $this->_vars["giftsItemnums"]=$this->_vars['giftsItems']['quantity']; ?>
	    	<tr>
	    		<td><?php echo $this->_vars['giftsItems']['bn']; ?></td>
			    <td><?php echo $this->_vars['giftsItems']['name']; ?></td>
			    <td><?php echo $this->_vars['giftsItemprice'] ; ?></td>
			    <td><?php echo $this->_vars['giftsItemnums']; ?></td>
			    <td class="Colamount"><?php echo $this->_vars['giftsItems']['amount']; ?></td>
			    <td>&nbsp;</td>
	    	</tr>
	    <?php } ?>
	    </tbody>
	    </table>
	    </td>
	    </tr>
    <?php }  } ?>
    </tbody>
  </table>
  <?php if( $this->_vars['order']['gifts'] ){ ?>
  <table width="100%" id="order_items" border="0" cellspacing="0" cellpadding="2" class="gridlist">
  <col class="Colsn"></col>
  <col></col>
  <col class="Colamount"></col>
  <col class="Colamount"></col>
  <col class="Colamount"></col>
  <col class="Coloption-1b"></col>
    <thead>
      <tr>
        <th>货号</th>
        <th>商品名称</th>
        <th>价格</th>
        <th>购买数量</th>
        <th>小计</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
    <?php if($this->_vars['order']['gifts'])foreach ((array)$this->_vars['order']['gifts'] as $this->_vars['iLoop'] => $this->_vars['aItems']){  $this->_vars["itemid"]=$this->_vars['aItems']['products']['product_id'];  $this->_vars["itemprice"]=$this->_vars['aItems']['price'];  $this->_vars["itemnums"]=$this->_vars['aItems']['quantity']; ?>
    <tr>
      <input type="hidden" name='aItems[<?php echo $this->_vars['aItems']['products']['product_id']; ?>]' value='<?php echo $this->_vars['aItems']['products']['product_id']; ?>' />
      <td><?php echo $this->_vars['aItems']['bn']; ?></td>
      <td><?php echo $this->_vars['aItems']['name']; ?></td>
      <td><?php echo $this->_vars['itemprice'] ; ?></td>
      <td><?php echo $this->_vars['itemnums'] ; ?></td>
      <td class="Colamount"><?php echo $this->_vars['aItems']['amount']; ?></td>
      <td>&nbsp;</td>
    </tr>
	 <?php } ?>
    </tbody>
    </table>   
  <?php } ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="2" class="gridlist">
    <tr>
      <td style="text-align:left">输入商品货号：<?php echo $this->ui()->input(array('type' => "text",'name' => newbn)); echo $this->ui()->button(array('type' => "submit",'label' => "添加",'id' => "newbtn",'class' => "btn",'name' => "newbtn"));?></td>
    </tr>
  </table>
<?php echo $this->_vars['order']['alertJs']; ?>
</div>
<script type="text/javascript">
$('newbtn').addEvent('click', function(){
    new Request({method:'post',onComplete:function(rs){
            if(rs.substr(0,1)!='<'){
                alert(rs);
            }else{
                $('order_items').getElement('tbody').set("html",$('order_items').getElement('tbody').get("html")+rs);
				$ES(".itemrow","moneyItems").each(function(item, index){
					item.addEvent('change',function(e){count_change(this);});
				  });	
			   countF();
            }
        }}).post('index.php?app=b2c&ctl=admin_order&act=addItem',$('orderItemList'));
		
		return false;
})
</script>
";s:6:"expire";i:0;}