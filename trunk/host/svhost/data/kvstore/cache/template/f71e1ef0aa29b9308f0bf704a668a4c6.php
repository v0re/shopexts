<?php exit(); ?>a:2:{s:5:"value";s:698:"<div id="ipt_<?php echo $this->_vars['_input']['domid']; ?>" class='clearfix'>
<?php echo $this->ui()->input(array('type' => "object",'object' => "{$this->_vars['_input']['object']}",'app' => $this->_vars['_input']['app_id'],'id' => "sel_{$this->_vars['_input']['domid']}",'data' => $this->_vars['_input'],'cols' => $this->_vars['_input']['cols'],'filter' => $this->_vars['_input']['filter'],'select' => checkbox));?>
<div class="gridlist rows-body" id="handle_<?php echo $this->_vars['_input']['domid']; ?>">
<?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("desktop","finder/input-row.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
</div>
</div>";s:6:"expire";i:0;}