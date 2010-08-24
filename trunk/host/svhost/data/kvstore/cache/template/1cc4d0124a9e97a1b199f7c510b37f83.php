<?php exit(); ?>a:2:{s:5:"value";s:4431:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><form action="index.php?app=ectools&amp;ctl=currency&amp;act=addnew" id='newcurrency-edit-form'>
	<div class="tableform">    
		<div class="division">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<th><em class="red">*</em><label for="dom_el_2a317d0">货币:</label></th>
						<td>
							<?php if( $this->_vars['curs'] ){ ?>
							<select id="dom_el_2a317d0" name="cur_code" required="1" onchange="var str=this.options[this.selectedIndex].innerHTML;$('cur_sign').value=str.substring(0,str.indexOf(' '));if(str.indexOf('，')!=-1){$('cur_name').value=str.substring(str.indexOf('，')+1);}else{ $('cur_name').value='';}" vtype="required" type="select" title="货币:" class="x-input-select inputstyle">
								<?php if($this->_vars['curs'])foreach ((array)$this->_vars['curs'] as $this->_vars['key'] => $this->_vars['cur']){ ?>
								<option value="<?php echo $this->_vars['key']; ?>"<?php if( $this->_vars['cur_code'] == $this->_vars['key'] ){ ?> selected="selected"<?php } ?>><?php echo $this->_vars['cur']; ?></option>
								<?php } ?>
							</select>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<th><em class="red">*</em><label for="cur_name">货币名称:</label></th>
						<td><input type="text" name="cur_name" id="cur_name" required="1" vtype="required" title="货币名称:" class="x-input " autocomplete="off" value="<?php echo $this->_vars['cur_name']; ?>"></td>
					</tr>
					<tr>
						<th><em class="red">*</em><label for="cur_sign">货币符号:</label></th>
						<td><input type="text" vtype="required" name="cur_sign" id="cur_sign" required="1" size="3" style="font-size: 18px; width: 50px; text-align: center; padding: 0pt;" title="货币符号:" class="x-input " autocomplete="off" value="<?php echo $this->_vars['cur_sign']; ?>"></td>
					</tr>
					<tr>
						<th><em class="red">*</em><label for="dom_el_2a317d1">汇率:</label></th>
						<td><input type="text" id="dom_el_2a317d1" name="cur_rate" required="1" vtype="required&amp;&amp;positive" title="汇率:" class="x-input " autocomplete="off" value="<?php echo $this->_vars['cur_rate']; ?>"></td>
					</tr>
					<tr>
						<th><em class="red">*</em><label for="dom_el_2a317d2">默认货币:</label></th>
						<td><input type="radio"<?php if( $this->_vars['cur_default'] == 'false' or $this->_vars['cur_default'] == '' ){ ?> checked="checked"<?php } ?> value="false" id="dom_el_2a317d2" name="cur_default" required="1" title="默认货币:"><label for="dom_el_2a317d2">不是默认</label><br><input type="radio"<?php if( $this->_vars['cur_default'] == 'true' ){ ?> checked="checked"<?php } ?> value="true" id="dom_el_2a317d2" name="cur_default" required="1" title="默认货币:"><label for="dom_el_2a317d21">默认</label><br></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</form>

<?php $this->_tag_stack[] = array('area', array('inject' => ".mainFoot")); $this->__view_helper_model['base_view_helper']->block_area(array('inject' => ".mainFoot"), null, $this); ob_start(); ?>
	<div class="table-action">
		<table width="100%" cellspacing="0" cellpadding="0">
			<tbody>
				<tr>
					<td>
						<button class="btn btn-primary" id="newcurrency-edit-form-submit" type="submit"><span><span>确定</span></span></button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_area($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>

<script>
(function(){
	var _form = $('newcurrency-edit-form');
	var btn =$('newcurrency-edit-form-submit');
	var finder = finderGroup['<?php echo $_GET['finder_id']; ?>'];
	
	_form.store('target',{
		onComplete:function(){
			
			try{
				var _dialogIns = btn.getParent('.dialog').retrieve('instance');
				//console.info(btn,_dialogIns);
		     }catch(e){}
		    if(_dialogIns){
			     //console.info(_dialogIns);
			    _dialogIns.close();
			    finder.refresh.delay(400,finder);
			}
			
			
		}
		
	});

	    btn.addEvent('click',function(){
		
		    _form.fireEvent('submit',{stop:$empty});
			
		
		
		});
	
})();
	
	
</script>";s:6:"expire";i:0;}