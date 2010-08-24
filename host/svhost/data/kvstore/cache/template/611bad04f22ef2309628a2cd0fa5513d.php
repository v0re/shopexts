<?php exit(); ?>a:2:{s:5:"value";s:1427:"<?php if($this->_vars['_input']['items'])foreach ((array)$this->_vars['_input']['items'] as $this->_vars['item']){ ?>
<div class="row">
 <div class='row-line'>
 	<div class="span-1">
   	<span class="opt"><?php echo $this->ui()->img(array('app' => "desktop",'src' => "bundle/delecate.gif",'onclick' => "remove_row(this)"));?></span>
    </div>
	<div class="span-auto span-auto-6">
	<?php $this->_vars["id"]=$this->_vars['item'][$this->_vars['_input']['idcol']]; ?>
	<input type="hidden" name="<?php echo $this->_vars['_input']['name']; ?>[]" item_id="<?php echo $this->_vars['item'][$this->_vars['_input']['idcol']]; ?>" value="<?php echo $this->_vars['item'][$this->_vars['_input']['idcol']]; ?>">
	<?php echo $this->_vars['item'][$this->_vars['_input']['textcol']]; ?>
	</div>
	<?php if( $this->_vars['_input']['view'] ){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include($this->_vars['_input']['view_app'],$this->_vars['_input']['view'], array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  echo $this->_env_vars['capture']['listBody'];  } ?>
   </div>
</div>
<?php } ?>

<script>
function remove_row(el){
		var obj=$(el).getParent('.rows-body'),row=$(el).getParent('.row');
		var ipt=obj.getPrevious('.object-select').getElement('input');		
		var arr=ipt.value.split(',');var tid=row.getElement('input').value;		
		ipt.value=arr.erase(tid);	
		$(el).getParent('.row').remove();
};

</script>

";s:6:"expire";i:0;}