<?php exit(); ?>a:2:{s:5:"value";s:2594:"<div class="tableform" id="Order_Form_Mark_<?php echo $this->_vars['orderid']; ?>">
<?php if( $this->_vars['_is_singlepage'] ){ ?><input type='hidden' name='all_reload' value='1'><?php } ?>
  <input type="hidden" name='orderid' value="<?php echo $this->_vars['orderid']; ?>">
  <div class="division">
  <table width="100%" border="0" cellpadding="0" cellspacing="0" >
   <tr>
   <th align='right'>标记：</th>
   <td align='left'>
      <label><input type='radio' name='mark_type' value='b1' id='b1'><img src='<?php echo $this->_vars['res_url']; ?>/remark_icons/b1.gif' width='20' height='20'></label>
	   <label><input type='radio' name='mark_type' value='b2' id='b2'><img src='<?php echo $this->_vars['res_url']; ?>/remark_icons/b2.gif' width='20' height='20'></label>
	  <label><input type='radio' name='mark_type' value='b3' id='b3'><img src='<?php echo $this->_vars['res_url']; ?>/remark_icons/b3.gif' width='20' height='20'></label>
	  <label><input type='radio' name='mark_type' value='b4' id='b4'><img src='<?php echo $this->_vars['res_url']; ?>/remark_icons/b4.gif' width='20' height='20'></label>
	  <label><input type='radio' name='mark_type' value='b5' id='b5'><img src='<?php echo $this->_vars['res_url']; ?>/remark_icons/b5.gif' width='20' height='20'></label>
	  <label><input type='radio' name='mark_type' value='b0' id='b0'><img src='<?php echo $this->_vars['res_url']; ?>/remark_icons/b0.gif' width='20' height='20'></label>
   </td>
   </tr>
    <tr>
      <th>订单备注：</th>
      <td><textarea name="mark_text" rows="6" style="width:80%"><?php echo $this->_vars['mark_text']; ?></textarea></td>
    </tr>
    </table>
    <div class="table-action">
        <?php echo $this->ui()->button(array('label' => "保存",'id' => "btn_do_submit"));?>
    </div>  
  </div>
</div>




<script>
	
	window.addEvent('domready',function(){
		if('<?php echo $this->_vars['mark_type']; ?>'!=''){
		    $('<?php echo $this->_vars['mark_type']; ?>').checked = true;
		}else{
		    $('b1').checked = true;
		}
		
		$('btn_do_submit').addEvent('click', function(){
			new Request({
				url:'index.php?app=b2c&ctl=admin_order&act=saveMarkText',
				data:$('Order_Form_Mark_<?php echo $this->_vars['orderid']; ?>'),
				method:'post',
				onSuccess:function(response){
					// 还原原来的input的值.
					$(JSON.decode(response).mark_type).checked = true;
					$E("#Order_Form_Mark_<?php echo $this->_vars['orderid']; ?> textarea[name^=mark_text]").value = JSON.decode(response).mark_text;
				}
			}).send();
	});
	});
</script>";s:6:"expire";i:0;}