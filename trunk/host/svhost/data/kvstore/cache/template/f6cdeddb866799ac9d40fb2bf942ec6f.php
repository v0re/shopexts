<?php exit(); ?>a:2:{s:5:"value";s:2490:"<?php if( $this->_vars['shippings'] ){ ?>
<div class="division">
<table width="100%" cellpadding="0" cellspacing="0" >
            <col class="span-5"></col>
            <col class="span-auto textleft"></col>
  <tbody>
  <?php $this->_env_vars['foreach']["shippings"]=array('total'=>count($this->_vars['shippings']),'iteration'=>0);foreach ((array)$this->_vars['shippings'] as $this->_vars['key'] => $this->_vars['shipping']){
                        $this->_env_vars['foreach']["shippings"]['first'] = ($this->_env_vars['foreach']["shippings"]['iteration']==0);
                        $this->_env_vars['foreach']["shippings"]['iteration']++;
                        $this->_env_vars['foreach']["shippings"]['last'] = ($this->_env_vars['foreach']["shippings"]['iteration']==$this->_env_vars['foreach']["shippings"]['total']);
?>
  <tr <?php if( $this->_env_vars['foreach']['shippings']['last'] ){ ?>class="last"<?php } ?>>
  <td colspan="2">
   <label style="width:auto; margin-right:10px">
    <input type="radio" name="delivery[shipping_id]" id='shipping_<?php echo $this->_vars['shipping']['dt_id']; ?>' required="true" value="<?php echo $this->_vars['shipping']['dt_id']; ?>" class="toCheck"  has_cod="<?php echo $this->_vars['shipping']['has_cod']; ?>"/>
      <?php echo $this->_vars['shipping']['dt_name']; ?>
    </label>
      <span style="font-size:14px;" class="fontcolorRed">+<?php echo app::get('ectools')->model('currency')->changer($this->_vars['shipping']['money']); ?></span>
      <?php if( $this->_vars['shipping']['protect']=='true' ){ ?>
      <div style="padding: 0 0 0 10px;">
      <input id="use_protect_<?php echo $this->_vars['key']; ?>"  type="checkbox" name="delivery[is_protect][<?php echo $this->_vars['shipping']['dt_id']; ?>]" value="1" />
      <label for="use_protect_<?php echo $this->_vars['key']; ?>">保价费率</label>
      (商品价格的<?php echo $this->_vars['shipping']['protect_rate']*100; ?>% ，不足<?php echo app::get('ectools')->model('currency')->changer($this->_vars['shipping']['minprice']); ?>按<?php echo app::get('ectools')->model('currency')->changer($this->_vars['shipping']['minprice']); ?>计算)。</div>
     <?php }  echo $this->_vars['shipping']['detail']; ?>	
  </td>
  </tr>
  <?php } unset($this->_env_vars['foreach']["shippings"]); ?>
  </tbody>
</table>
</div>
<?php }else{ ?>
<div class='notice'>不支持您当前所在地区的物流配送，请直接与我们联系</div>
<?php } ?>";s:6:"expire";i:0;}