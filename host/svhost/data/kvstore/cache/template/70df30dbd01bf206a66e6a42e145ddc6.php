<?php exit(); ?>a:2:{s:5:"value";s:10859:"<?php if( count($this->_vars['goodsItems']) > 0 ){ ?>
<div class="division">
  <div  class="table-grid">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="gridlist">
      <thead>
      <th>货号</th>
        <th>商品名称</th>
        <th>分类</th>
        <th>价格</th>
        <th>购买量</th>
        <th>已发货量</th>
      </tr>
      </thead>
      
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
          <td class="Colamount" width="15%"><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['aGoods']['price'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
          <td class="Colamount" width="15%"><?php echo $this->_vars['aGoods']['quantity']; ?></td>
          <td class="Colamount"><?php echo $this->_vars['aGoods']['sendnum']; ?></td>
        </tr>
      <?php if( $this->_vars['aGoods']['adjunct'] ){ ?>
      <tr>
        <td colspan="6" style="padding:0 0 0 20px;  background:#F7FAFB"><div style="padding-left:40px; border-bottom:1px solid #E8E8E8;  font-weight:bold; text-align:left">商品配件</div>
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
              <td class="Colamount" width="15%" style="border:none;"><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['ajuncts']['price'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
              <td class="Colamount" width="15%" style="border:none;"><?php echo $this->_vars['ajuncts']['quantity']; ?></td>
              <td class="Colamount" style="border:none;"><?php echo $this->_vars['ajuncts']['sendnum']; ?></td>
            </tr>
            <?php } unset($this->_env_vars['foreach']["ajunctsItem"]); ?>
          </table></td>
      </tr>
      <?php }  if( $this->_vars['aGoods']['gifts'] ){ ?>
      <tr>
        <td colspan="6" style="padding:0 0 0 20px;" ><div style=" border-bottom:1px solid #ddd;padding-left:40px;font-weight:bold; background:#F4F4F4;text-align:left">商品赠品</div>
        <table cellpadding="0" cellspacing="0" border="0">
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
        <td class="Colamount" width="15%" ><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['gifts']['price'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
        <td class="Colamount" width="15%" ><?php echo $this->_vars['gifts']['quantity']; ?></td>
        <td class="Colamount" ><?php echo $this->_vars['gifts']['sendnum']; ?></td></tr>
      
      <?php } unset($this->_env_vars['foreach']["giftsItem"]); ?>
     </table></td></tr> 
      <?php }  } unset($this->_env_vars['foreach']["item"]);  if( $this->_vars['giftItems'] ){ ?>
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
        <td class="Colamount"><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['aGoods']['price'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
        <td class="Colamount"><?php echo $this->_vars['aGoods']['quantity']; ?></td>
        <td class="Colamount"><?php echo $this->_vars['aGoods']['sendnum']; ?></td>
      </tr>
      </td>
      </tr>
      <?php } unset($this->_env_vars['foreach']["item"]);  } ?> 
    </table>
    </div>
</div> 

<script> 
$$('.x-view-img').each(function(item){
		 item.store('tip:text','<div style="width:220px;height:220px;overflow:hidden;"><img  src="'+item.get('imgsrc')+'" onload="$(this).zoomImg(220,220);" /></div>');
	     Xtip.attach(item);
});
</script>
 <?php } ?>";s:6:"expire";i:0;}