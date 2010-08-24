<?php exit(); ?>a:2:{s:5:"value";s:6582:"<div class="infoContent" container="true" id="contents" style="visibility: visible; opacity: 1;">
  <div class="tabs"><div >
  <table cellspacing="0" cellpadding="0" style="margin:10px" border="0" width="100%">
    <tbody><tr>
    <td width="220" valign="top" style="vertical-align: top;">
      <div class="span-6"><div class="division">
    <div class="table-grid">
      <table cellspacing="0" cellpadding="0" border="0" width="100%" class="gridlist">
        <thead>
          <th>订单商品 (购买量)</th>

        <tbody>
        <?php if($this->_vars['goodsItems'])foreach ((array)$this->_vars['goodsItems'] as $this->_vars['aGoods']){ ?>
        <tr>
          	<td style=" white-space:normal; text-align:left;">
	          	<a href="<?php echo $this->_vars['aGoods']['link']; ?>" target="_blank"><?php echo $this->_vars['aGoods']['name']; ?></a>
	          	<span class="fontcolorOrange">×(<?php echo $this->_vars['aGoods']['quantity']; ?>)</span>
        	</td>
        </tr>
        <?php if( $this->_vars['aGoods']['adjunct'] ){ ?>
        <tr>
	        <td>
		        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="gridlist">
			        <tbody>
			        <?php if($this->_vars['aGoods']['adjunct'])foreach ((array)$this->_vars['aGoods']['adjunct'] as $this->_vars['adjunctItem']){ ?>
			        	<tr>
			        		<td>
				        		<a href="<?php echo $this->_vars['adjunctItem']['link']; ?>" target="_blank"><?php echo $this->_vars['adjunctItem']['name']; ?></a>
								<span class="fontcolorOrange">×(<?php echo $this->_vars['adjunctItem']['quantity']; ?>)</span>
			        		</td>
			        	</tr>        	
			        <?php } ?>
			        </tbody>
		        </table>
	        </td>
        </tr>
        <?php }  if( $this->_vars['aGoods']['gifts'] ){ ?>
        <tr>
	        <td>
		        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="gridlist">
			        <tbody>
			        <?php if($this->_vars['aGoods']['gifts'])foreach ((array)$this->_vars['aGoods']['gifts'] as $this->_vars['giftItem']){ ?>
			        	<tr>
			        		<td>
				        		<a href="<?php echo $this->_vars['giftItem']['link']; ?>" target="_blank"><?php echo $this->_vars['giftItem']['name']; ?></a>
								<span class="fontcolorOrange">×(<?php echo $this->_vars['giftItem']['quantity']; ?>)</span>
			        		</td>
			        	</tr>        	
			        <?php } ?>
			        </tbody>
		        </table>
	        </td>
        </tr>
        <?php }  } ?>
        </tbody>
      </table>
      <?php if( $this->_vars['giftsItems'] ){ ?>
      <table cellspacing="0" cellpadding="0" border="0" width="100%" class="gridlist">
        <thead>
          <th>订单赠品 (购买量)</th>

        <tbody>
        <?php if($this->_vars['giftsItems'])foreach ((array)$this->_vars['giftsItems'] as $this->_vars['aGifts']){ ?>
        <tr>
          	<td style=" white-space:normal; text-align:left;">
	          	<a href="<?php echo $this->_vars['aGifts']['link']; ?>" target="_blank"><?php echo $this->_vars['aGifts']['name']; ?></a>
	          	<span class="fontcolorOrange">×(<?php echo $this->_vars['aGifts']['quantity']; ?>)</span>
        	</td>
        </tr>
        <?php } ?>
        </tbody>
      </table>
      <?php } ?>
    </div>
  </div>
    </div></td>
        <td style="vertical-align: top;">

        <div>

      <table cellspacing="0" cellpadding="0" border="0" width="100%" id="order_form_msg_<?php echo $this->_vars['orderid']; ?>" style="display:none">
      <tr>
      <td width="40">标题：</td>
    <td height="30">
		<?php if($this->_vars['ordermsg'])foreach ((array)$this->_vars['ordermsg'] as $this->_vars['newmsg']){  if( $this->_vars['newmsg']['for_comment_id'] == '0' ){  $this->_vars[reSubject]=$this->_vars['newmsg']['title'];  $this->_vars[msgid]=$this->_vars['newmsg']['comment_id'];  break;  }  }  echo $this->ui()->input(array('type' => "text",'name' => "msg[subject]",'required' => 'true','maxlength' => 30,'value' => "Re:{$this->_vars['reSubject']}"));?>
		<input type="hidden" name="hid_res_subj" value="Re:<?php echo $this->_vars['reSubject']; ?>" />
	</td>
    </tr>
    <tr>
    <td style="vertical-align: top;">内容：</td>
    <td><?php echo $this->ui()->input(array('type' => "textarea",'name' => "msg[message]",'rows' => 3,'style' => "width:40%"));?><input type="hidden" name="msg[msg_id]" value="<?php echo $this->_vars['msgid']; ?>" /></td>
    </tr>
    <tr>
        <td>&nbsp;<input type="hidden" name="msg[orderid]" value="<?php echo $this->_vars['orderid']; ?>"/></td>
        <td style="padding:5px 0 0 0">
        <?php echo $this->ui()->button(array('label' => "提交留言",'id' => "btn_do_submit"));?>
        </td>
    </tr>
    </table>

        </div>
        <?php if( $this->_vars['ordermsg'] ){ ?>
	<div class="division" id="tab_odr_msg">
		<?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"admin/order/od_msg_item.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
	</div>
<?php }else{ ?>
	<div class="division" id="tab_odr_msg">
	</div>
<?php } ?>
          </td>
      </tr>
      <tr>
      <td colspan="2"><div class="table-action" style="text-align:center; padding:5px 25px 5px 0;">
<!--    <label>
    <input type="button"  value="给顾客留言" onclick="$('order_form_msg_<?php echo $this->_vars['orderid']; ?>').style.display=''"/>
    </label>-->
    <?php echo $this->ui()->button(array('label' => "给顾客留言",'onclick' => "$('order_form_msg_{$this->_vars['orderid']}').style.display=''"));?>
  </div></td></tr>
    </tbody></table>
</div>

  </div>

</div>
<script>
window.addEvent('domready', function(){
	$('btn_do_submit').addEvent('click', function(){
		new Request.HTML({
			url:'index.php?app=b2c&ctl=admin_order&act=saveOrderMsgText',
			update:'tab_odr_msg',
			data:$('order_form_msg_<?php echo $this->_vars['orderid']; ?>'),
			method:'post',
			onSuccess:function(response){
				$('order_form_msg_<?php echo $this->_vars['orderid']; ?>').style.display='none';
				// 还原原来的input的值.
				$E("#order_form_msg_<?php echo $this->_vars['orderid']; ?> input[name^=msg[subject]]").value = $E("#order_form_msg_<?php echo $this->_vars['orderid']; ?> input[name^=hid_res_subj]").value;
				$E("#order_form_msg_<?php echo $this->_vars['orderid']; ?> textarea[name^=msg[message]]").value = '';
			}
        }).send();
	});	
});
</script>
";s:6:"expire";i:0;}