<?php exit(); ?>a:2:{s:5:"value";s:4700:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper');  $this->_tag_stack[] = array('capture', array('name' => "header")); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => "header"), null, $this); ob_start();  echo $this->ui()->script(array('src' => "goodseditor.js")); echo $this->ui()->script(array('src' => "catalog.js")); echo $this->ui()->script(array('src' => "coms/pager.js",'app' => 'desktop')); $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => "sidebar")); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => "sidebar"), null, $this); ob_start(); ?>
<div class="side-bx spage-side" style="border-top:none">
    <div class="spage-side-nav">
        <ul>
            <li class="l-handle all" ><span>商品总览</span></li>
            <?php $this->_env_vars['foreach']["sec"]=array('total'=>count($this->_vars['sections']),'iteration'=>0);foreach ((array)$this->_vars['sections'] as $this->_vars['section']){
                        $this->_env_vars['foreach']["sec"]['first'] = ($this->_env_vars['foreach']["sec"]['iteration']==0);
                        $this->_env_vars['foreach']["sec"]['iteration']++;
                        $this->_env_vars['foreach']["sec"]['last'] = ($this->_env_vars['foreach']["sec"]['iteration']==$this->_env_vars['foreach']["sec"]['total']);
 if( $this->_env_vars['foreach']['sec']['iteration'] != 1 ){ ?>
                <li class="l-handle" ><span><?php echo $this->_vars['section']['label']; ?></span></li>
                <?php }  } unset($this->_env_vars['foreach']["sec"]); ?>
        </ul>
    </div>
</div>

<div class="side-bx spage-side" style='display:none'>
    <div class="side-bx-title"><h3>商品标签</h3></div>
    <div class="side-bx-bd">
        <div style="overflow: hidden;" class="tag-editor" id="finder-tag">
            <ul class="tag-editor-group" style="overflow: hidden;">
            <?php if($this->_vars['tagList'])foreach ((array)$this->_vars['tagList'] as $this->_vars['aTag']){ ?>
                <li><input type='checkbox' name='goods[tag][<?php echo $this->_vars['aTag']['tag_id']; ?>][tag][tag_id]' value='<?php echo $this->_vars['aTag']['tag_id']; ?>' <?php if( $this->_vars['goods']['tag'][$this->_vars['aTag']['tag_id']] ){ ?>checked<?php } ?> /><img style="width: 9px; height: 9px; background-position: 0pt -2252px;" class="imgbundle" src="images/transparent.gif"><?php echo $this->_vars['aTag']['tag_name']; ?></li>
            <?php } ?>
            </ul>
        </div>
    </div>
</div>

<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  if($this->_vars['sections'])foreach ((array)$this->_vars['sections'] as $this->_vars['section']){ ?>
<div class="spage-main-box">
  <?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',$this->_vars['section']['file'], array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
</div>
<?php }  $this->_tag_stack[] = array('capture', array('name' => 'footbar')); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => 'footbar'), null, $this); ob_start(); ?>
<table cellspacing="0" cellpadding="0" class="table-action">
      <tbody>
		<tr valign="middle">
        <td>
            <?php echo $this->ui()->button(array('label' => "保存并关闭窗口",'class' => "btn-primary",'onclick' => "subGoodsForm(event,2)")); echo $this->ui()->button(array('label' => "保存并添加相似商品",'class' => "btn-primary",'onclick' => "subGoodsForm(event,1)")); echo $this->ui()->button(array('label' => "保存当前",'class' => "btn-primary",'onclick' => "subGoodsForm(event,3)")); echo $this->ui()->button(array('label' => "关  闭",'class' => "btn-secondary",'onclick' => "if(confirm('确定退出?'))window.close()"));?>
        </td>
        </tr>
        </tbody>
</table>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>";s:6:"expire";i:0;}