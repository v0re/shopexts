<?php exit(); ?>a:2:{s:5:"value";s:5373:"<div class="success clearfix">
  <div class="span-1 pic"/></div>
  <div class="span-10"><h3>恭喜您已注册本店会员</h3></div>
  
</div>
<div class="FormWrap">
<div class="customMessages"></div>

<form method="post" action="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_member",'act' => "save_attr")); ?>" id='form_saveMember_info' class="section">
<input type="hidden" name="is_register" value=1>
<div class="division" id='crateAdmin'>
<table class="forform" width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php $this->_env_vars['foreach']["item"]=array('total'=>count($this->_vars['attr']),'iteration'=>0);foreach ((array)$this->_vars['attr'] as $this->_vars['row'] => $this->_vars['item']){
                        $this->_env_vars['foreach']["item"]['first'] = ($this->_env_vars['foreach']["item"]['iteration']==0);
                        $this->_env_vars['foreach']["item"]['iteration']++;
                        $this->_env_vars['foreach']["item"]['last'] = ($this->_env_vars['foreach']["item"]['iteration']==$this->_env_vars['foreach']["item"]['total']);
?>
  <tr>
  <th><?php if( $this->_vars['item']['attr_required'] == 'true' ){ ?><em>*</em><?php }  echo $this->_vars['item']['attr_name']; ?></th>
  <td>  
      
  <?php if( $this->_vars['item']['attr_type'] =='date' ){  echo $this->ui()->input(array('class' => "cal",'type' => 'date','name' => $this->_vars['item']['attr_column'],'value' => $this->_vars['item']['attr_value'],'required' => $this->_vars['item']['attr_required'])); }  if( $this->_vars['item']['attr_type'] =='region' ){  echo $this->ui()->input(array('app' => ectools,'type' => "region",'name' => $this->_vars['item']['attr_column'],'vtype' => 'area2')); }  if( $this->_vars['item']['attr_type'] =='gender' ){  echo $this->ui()->input(array('type' => 'gender','name' => $this->_vars['item']['attr_column'],'value' => $this->_vars['item']['attr_value'])); }  if( $this->_vars['item']['attr_type'] =='select' ){ ?>
 <select name='<?php echo $this->_vars['item']['attr_column']; ?>' <?php if( $this->_vars['item']['attr_required'] == 'true' ){ ?>class='_x_ipt' vtype='required'<?php } ?>>
  <option value='' <?php if( $this->_vars['item']['attr_value'] == '' ){ ?>selected='true'<?php } ?>>- 请选择 -</option>
 <?php $this->_env_vars['foreach']["option"]=array('total'=>count($this->_vars['item']['attr_option']),'iteration'=>0);foreach ((array)$this->_vars['item']['attr_option'] as $this->_vars['option']){
                        $this->_env_vars['foreach']["option"]['first'] = ($this->_env_vars['foreach']["option"]['iteration']==0);
                        $this->_env_vars['foreach']["option"]['iteration']++;
                        $this->_env_vars['foreach']["option"]['last'] = ($this->_env_vars['foreach']["option"]['iteration']==$this->_env_vars['foreach']["option"]['total']);
?>
 <option value='<?php echo $this->_vars['option']; ?>' <?php if( $this->_vars['item']['attr_value'] == $this->_vars['option'] ){ ?>selected='true'<?php } ?>><?php echo $this->_vars['option']; ?></option>
 <?php } unset($this->_env_vars['foreach']["option"]); ?>
 </select>
   <?php }  if( $this->_vars['item']['attr_type'] =='checkbox' ){  $this->_env_vars['foreach']["checkbox"]=array('total'=>count($this->_vars['item']['attr_option']),'iteration'=>0);foreach ((array)$this->_vars['item']['attr_option'] as $this->_vars['checkbox']){
                        $this->_env_vars['foreach']["checkbox"]['first'] = ($this->_env_vars['foreach']["checkbox"]['iteration']==0);
                        $this->_env_vars['foreach']["checkbox"]['iteration']++;
                        $this->_env_vars['foreach']["checkbox"]['last'] = ($this->_env_vars['foreach']["checkbox"]['iteration']==$this->_env_vars['foreach']["checkbox"]['total']);
?>
 <input type='checkbox' name=box:<?php echo $this->_vars['item']['attr_column']; ?>[] value='<?php echo $this->_vars['checkbox']; ?>'><label><?php echo $this->_vars['checkbox']; ?></label><br/>
 <?php } unset($this->_env_vars['foreach']["checkbox"]); ?>
 <input type='hidden' name='<?php echo $this->_vars['item']['attr_column']; ?>[]' value='%no%'>
<?php if( $this->_vars['item']['attr_required'] == 'true' ){ ?>
<input type='hidden' class='_x_ipt' vtype='selectc'/>
<?php }  }  if( $this->_vars['item']['attr_type'] == 'text' ){  echo $this->ui()->input(array('type' => 'text','name' => $this->_vars['item']['attr_column'],'value' => $this->_vars['item']['attr_value'],'vtype' => $this->_vars['item']['attr_valtype'])); }  if( $this->_vars['item']['attr_type'] == 'number' ){  echo $this->ui()->input(array('type' => 'text','name' => $this->_vars['item']['attr_column'],'value' => $this->_vars['item']['attr_value'],'vtype' => $this->_vars['item']['attr_type'],'required' => $this->_vars['item']['attr_required'])); }  if( $this->_vars['item']['attr_type'] == 'alpha' ){  echo $this->ui()->input(array('type' => 'text','name' => $this->_vars['item']['attr_column'],'value' => $this->_vars['item']['attr_value'],'vtype' => $this->_vars['item']['attr_type'],'required' => $this->_vars['item']['attr_required'])); } ?>

</td>
  </tr>
  <?php } unset($this->_env_vars['foreach']["item"]); ?>  
  <tr>
    <th></th>
    <td><input class="actbtn btn-save" type="submit" value="保存" /></td>
    </tr>
</table>
</form>
</div>
</div>";s:6:"expire";i:0;}