<?php exit(); ?>a:2:{s:5:"value";s:4075:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper');  $this->_tag_stack[] = array('capture', array('name' => "header")); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => "header"), null, $this); ob_start(); ?>
   <!--JAVASCRIPTS SRC-->     
    <!--JAVASCRIPTS SRC END-->

<?php echo $this->ui()->script(array('src' => "coms/dropmenu.js",'app' => 'desktop')); echo $this->ui()->script(array('src' => "coms/datapicker.js",'app' => 'desktop')); echo $this->ui()->script(array('src' => "coms/colorpicker.js",'app' => 'desktop')); echo $this->ui()->script(array('src' => "coms/editor.js",'app' => 'desktop')); echo $this->ui()->script(array('src' => "coms/editor_style_1.js",'app' => 'desktop')); echo $this->ui()->script(array('src' => "coms/finder.js",'app' => 'desktop')); echo $this->ui()->script(array('src' => "coms/modedialog.js",'app' => 'desktop')); $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>

<div class="spage-main-box">
    <?php if( $this->_vars['order'] ){  if( $this->_vars['order']['is_has_remote_pdts']!=='true' ){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',"admin/order/order_edit.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  }elseif( $this->_vars['order']['is_has_remote_pdts']=='true' ){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',"admin/order/edit_po.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  }  }else{  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',"admin/order/detail/detail.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  } ?>   
</div>

<script>

subOrderForm = function (event,sign){ 
	   var target={};
	   switch (sign){
			case 1:                    //保存不关闭
				$extend(target,{
					onComplete:function(){
						if(window.opener.finderGroup&&window.opener.finderGroup['<?php echo $_GET['finder_id']; ?>'])
						window.opener.finderGroup['<?php echo $_GET['finder_id']; ?>'].refresh();
						window.location.reload();
					}}
				);
			break;
			case 2:                   //保存关闭
				$extend(target,{
					onComplete:function(){
						if(window.opener.finderGroup&&window.opener.finderGroup['<?php echo $_GET['finder_id']; ?>'])
						window.opener.finderGroup['<?php echo $_GET['finder_id']; ?>'].refresh();
						window.close();
					}}
				);
			break;				
	   }
	    var _form=$('orderEdit');
		if(!_form)return;
		var _formActionURL=_form.get('action'); 
		
		_form.store('target',target);
        _form.set('action',_formActionURL+'&but='+sign).fireEvent('submit',new Event(event));
    };
</script>
 
<?php $this->_tag_stack[] = array('capture', array('name' => 'footbar')); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => 'footbar'), null, $this); ob_start(); ?>
<table cellspacing="0" cellpadding="0" style="margin:0 auto; width:100%" class="table-action">
      <tbody><tr valign="middle">
        <td>
            <?php echo $this->ui()->button(array('label' => "保存并关闭窗口",'class' => "btn-primary",'onclick' => "subOrderForm(event,2)")); echo $this->ui()->button(array('label' => "保存当前",'class' => "btn-primary",'onclick' => "subOrderForm(event,1)")); echo $this->ui()->button(array('label' => "关  闭",'class' => "btn-secondary",'onclick' => "if(confirm('确定退出?'))window.close()"));?>
        </td>
        </tr>
        </tbody></table>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>
";s:6:"expire";i:0;}