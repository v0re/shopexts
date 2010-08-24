<?php exit(); ?>a:2:{s:5:"value";s:9262:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><form action="index.php?app=b2c&ctl=admin_goods_cat&act=update" id="catEditor" method="post">
    <?php $this->_tag_stack[] = array('area', array('inject' => ".mainHead")); $this->__view_helper_model['base_view_helper']->block_area(array('inject' => ".mainHead"), null, $this); ob_start(); ?>
        <div class="gridlist-action">
            <?php echo $this->ui()->button(array('app' => "desktop",'label' => "添加分类",'icon' => "btn_add.gif",'onclick' => "new Dialog('index.php?app=b2c&ctl=admin_goods_cat&act=addnew',{title:'添加分类',width:550,height:300})")); if( $this->_vars['tree_number']<=500 ){  echo $this->ui()->button(array('app' => "desktop",'label' => "全部展开",'id' => "showCat-handle",'icon' => "btn_unfolded.gif")); echo $this->ui()->button(array('app' => "desktop",'label' => "全部收起",'icon' => "btn_folded.gif",'id' => "hideCat-handle")); } ?>
            &nbsp;
        </div>
        <div class="Node">
        <div class='gridlist-head mainHead '>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td width="25%">分类名称</td>
        <td width="21%">类型</td>
        <td width="11%">添加子类</td>
        <td width="9%">编辑</td>
        <td width="9%">删除</td>
        <td width="9%">查看商品</td>
        <td width="16%">预览</td>
        </tr></table>
              </div></div>
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_area($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>
    <div class="Node-body">
    <div id="cat_tree" class='gridlist'> 
    <?php $this->_env_vars['foreach']["item"]=array('total'=>count($this->_vars['tree']),'iteration'=>0);foreach ((array)$this->_vars['tree'] as $this->_vars['item']){
                        $this->_env_vars['foreach']["item"]['first'] = ($this->_env_vars['foreach']["item"]['iteration']==0);
                        $this->_env_vars['foreach']["item"]['iteration']++;
                        $this->_env_vars['foreach']["item"]['last'] = ($this->_env_vars['foreach']["item"]['iteration']==$this->_env_vars['foreach']["item"]['total']);
?>
        <div depath="<?php echo $this->_vars['item']['step']; ?>" class="clear_cat row" cid="<?php echo $this->_vars['item']['cat_id']; ?>" pid="<?php echo $this->_vars['item']['pid']; ?>">
            <div class='row-line'>
             <table cellpadding="0" cellspacing="0" border="0">
            <tr>
            <td width="25%">
                                 <div style="margin-left:<?php echo $this->_vars['item']['step']*25; ?>px;"><?php if( $this->_vars['tree_number']<=500 ){  if( $this->_vars['item']['cls']=='true' ){ ?>  <span style='width:12px;line-height:12px; height:12px;margin:auto 4px;overflow:hidden;display:inline-block;padding:0;cursor:pointer'>  <?php echo $this->ui()->img(array('src' => "bundle/handle-hide.gif",'alt' => "收起子分类",'title' => "收起子分类",'class' => "handle-hide",'app' => 'desktop')); echo $this->ui()->img(array('src' => "bundle/handle-show.gif",'alt' => "收起子分类",'title' => "展开子分类",'class' => "handle-show",'app' => 'desktop'));?> </span> <?php }else{  echo $this->ui()->img(array('src' => "bundle/blue-dot.gif",'app' => 'desktop')); }  } ?>
                        排序
                        <input class="_x_ipt" type="number" size="2"  name="p_order[<?php echo $this->_vars['item']['cat_id']; ?>]" value="<?php echo $this->_vars['item']['p_order']; ?>" vtype="unsigned">
                        <span class="lnk" style="color:#369; padding-right:15px;" onClick="new Dialog('index.php?app=b2c&ctl=admin_goods_cat&act=edit&p[0]=<?php echo $this->_vars['item']['cat_id']; ?>', {title:'编辑分类', width:550, height:300})"><?php echo $this->_vars['item']['cat_name']; ?></span></div>
               </td>
                <td width="21%"><span class="quiet"><?php if( $this->_vars['item']['type_name'] ){ ?>[<?php echo $this->_vars['item']['type_name']; ?>]<?php } ?></span></td>
                <td width="11%"><?php $this->_vars["cat_id"]=$this->_vars['item']['cat_id']; ?><span class="opt" onClick="new Dialog('index.php?app=b2c&ctl=admin_goods_cat&act=addnew&p[0]=<?php echo $this->_vars['item']['cat_id']; ?>', {title:'添加子类', width:550, height:300})"><?php echo $this->ui()->img(array('src' => "bundle/addcate.gif",'border' => "0",'alt' => "添加子分类",'app' => 'desktop'));?></span></td>
                <td width="9%"><span class="opt" onClick="new Dialog('index.php?app=b2c&ctl=admin_goods_cat&act=edit&p[0]=<?php echo $this->_vars['item']['cat_id']; ?>',{title:'编辑分类', width:550, height:300})"><?php echo $this->ui()->img(array('src' => "bundle/editcate.gif",'border' => "0",'alt' => "编辑",'app' => 'desktop'));?></span></td>
                <td width="9%"><span class="opt" onclick="deleteRow('index.php?app=b2c&ctl=admin_goods_cat&act=toRemove&p[0]=<?php echo $this->_vars['item']['cat_id']; ?>',event)"><?php echo $this->ui()->img(array('src' => "bundle/delecate.gif",'border' => "0",'alt' => "删除",'app' => 'desktop'));?></span></td>
                <td width="9%"><span class="opt" onclick='W.page("index.php?app=b2c&ctl=admin_goods&act=index&filter[cat_id]=<?php echo $this->_vars['item']['link']['cat_id']['v']; ?>")'><?php echo $this->ui()->img(array('src' => "bundle/showcate.gif",'border' => "0",'alt' => "查看此分类下商品",'app' => 'desktop'));?></span></td>
                <td width="16%"><span class="opt" onclick="window.open('<?php echo $this->_vars['item']['url']; ?>')"><?php echo $this->ui()->img(array('src' => "bundle/zoom_btn.gif",'border' => "0",'alt' => "跳转前台查看该",'app' => 'desktop'));?></span></td></tr>
                </table>
            </div>
        </div>
        <?php } unset($this->_env_vars['foreach']["item"]); ?> </div></div>
    <?php $this->_tag_stack[] = array('area', array('inject' => ".mainFoot")); $this->__view_helper_model['base_view_helper']->block_area(array('inject' => ".mainFoot"), null, $this); ob_start(); ?>
    <div class="footer">
        <div class="table-action">
            <?php echo $this->ui()->button(array('class' => "btn-primary",'label' => "保存排序",'type' => "submit",'onclick' => "$('catEditor').fireEvent('submit',{stop:function(){}})"));?>
        </div>
    </div>
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_area($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>
</form>
<script>

function deleteRow(act,event){
e=$(new Event(event).stop().target);
     var row=e.getParent('.row');

    if(confirm('您确定要删除该分类？')){
        W.page(act,{
        method:'get',
        update:'messagebox',
        onComplete:function(re){

            if(re.contains('successSplash')){row.remove();}

            }
        });
    }
}
<?php if( $this->_vars['tree_number']<=500 ){ ?>
void function(){
   $E('#hideCat-handle').addEvent('click',function(){
    $ES('#cat_tree .clear_cat').each(function(e){
        if(e.get('depath')>1){
            e.setStyles({'display':'none'});
        }
    });
    $ES('#cat_tree .handle-hide').hide();
  });
    $E('#showCat-handle').addEvent('click',function(){

        $ES('#cat_tree .clear_cat').each(function(e){
            if(e.get('depath')>1){
                e.setStyles({'display':''});
            }
        });
        $ES('#cat_tree .handle-hide').show();
    });

    $('cat_tree').addEvent('click',function(e){

       if(!e.target.className.match(/handle-/i))return;


      var handle=$(e.stop().target);
            var eventRow=handle.getParent('.row');
            var visible=handle.hasClass('handle-show')?'':'none';
                if(visible=='none'){
                         handle.hide().getNext().show();
                    }else{
                         handle.hide().getPrevious().show();

                    }
            flode(eventRow,visible);

    });

    function flode(eventRow,visible){
            var cid=eventRow.get('cid');
            var pid=eventRow.get('pid');

            eventRow.getAllNext('div[pid='+cid+']').each(function(row){
                if(visible=='none'){
                    row.hide();
                    var obj=row.getElements('.span-8 img');
                    if(obj.length>1){
                        flode(row,visible);
                    }
                }else{
                    row.show();
                    var obj=row.getElements('.span-8 img');
                    if(obj.length>1){
                        var vis=(obj[0].getStyle('display')=='none'?'none':'inline');
                        flode(row,vis);
                    }
                }

            });
    }
}();
<?php } ?>

</script>
";s:6:"expire";i:0;}