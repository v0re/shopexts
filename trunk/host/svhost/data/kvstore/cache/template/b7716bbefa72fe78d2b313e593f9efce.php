<?php exit(); ?>a:2:{s:5:"value";s:4408:"<form method="post" action="index.php?app=b2c&ctl=admin_goods_cat&act=save" id='add-category-form'>
<div class="tableform">
    <div class="division">
      <table cellpadding="0" cellspacing="0" border="0">
        <tr>
          <th>分类名称：
            <?php if( $this->_vars['cat']['cat_id']  ){ ?><input type="hidden" value="<?php echo $this->_vars['cat']['cat_id']; ?>" name="cat[cat_id]" /></th><?php } ?>
          <td><input type="text" value="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['cat']['cat_name'],'html'); ?>" name="cat[cat_name]" vtype='required' class='x-input'/>
            <?php echo $this->_vars['cat_name']; ?></td>
        </tr>
        <tr>
          <th>上级分类：</th>
          <td><?php echo $this->ui()->input(array('type' => "select",'name' => "cat[parent_id]",'style' => "font-size:12px;",'required' => '1','value' => $this->_vars['cat']['parent_id'],'rows' => $this->_vars['catList'],'valueColumn' => "cat_id",'labelColumn' => "cat_name"));?>
            <span class="notice-inline">顶级分类请选择“无”</span></td>
        </tr>
        <tr>
          <th>商品类型：</th>
          <td><select name="cat[type_id]">
              <?php if($this->_vars['gtypes'])foreach ((array)$this->_vars['gtypes'] as $this->_vars['type']){ ?> <option value="<?php echo $this->_vars['type']['type_id']; ?>" <?php if( $this->_vars['type']['type_id'] == $this->_vars['cat']['type_id'] ){ ?>selected="selected"<?php } ?>><?php echo $this->_vars['type']['name']; ?>
              </option>
              <?php } ?>
            </select></td>
        </tr>
  

        <tr>
          <th>排序：</th>
          <td><input style="width:50px;" vtype="unsigned" value="<?php echo $this->_vars['cat']['p_order']; ?>" name="cat[p_order]" class="_x_ipt"/>
            <span class="notice-inline">数字越小越靠前</span></td>
        </tr>

      </table>
    </div>


</div>




<div class="table-action">
	<?php echo $this->ui()->button(array('label' => "保存",'type' => "submit"));?>
	<!--
   <?php echo $this->ui()->button(array('label' => "保存并继续添加",'type' => "button",'id' => 'easy-save-category'));?>
   //-->
	<?php echo $this->ui()->button(array('class' => "btn-secondary ",'label' => "取消",'isclosedialogbtn' => "true",'onclick' => "W.page('index.php?app=b2c&ctl=admin_goods_cat&act=index')"));?>
</div>

<script>
           $E('#add-category-form input[name^=cat[cat_name]').addEvent('keydown',function(e){

               if(e.key=='enter'){


                 e.stop();

                   return $('easy-save-category').fireEvent('click',{stop:$empty});
               }

           });

           $('add-category-form').store('target',{onComplete:function(){

                  if(gms=$('g_menu_sec_2')){
                      gms.empty();
                      gms.retrieve('update',$empty)();
                  }


           }});

			if($('easy-save-category'))
           $('easy-save-category').addEvent('click',function(e){
                    e.stop();
                    var _form=this.form;

                    var cname=$E('#add-category-form input[name^=cat[cat_name]');
                    var value=cname.get('value').clean().trim();

                    var cid=$E('#add-category-form input[name^=cat[cat_id]');
                    if(cid) cid.remove();

                    if(!value)return MessageBox.error('分类名称为不能为空.');
                    if(this.retrieve('tempname',[]).contains(value)){
                       if(!confirm('您刚才已经添加了分类:"'+value+'"\n要重复添加么?'))return;
                    }

                    this.retrieve('tempname',[]).include(value);

                    if(!$(_form).get('target')){

                      var _target={
                         update:'messagebox'
                      }
                       _form.set('target',JSON.encode(_target));
                    }
                    _form.fireEvent('submit',e).erase('target');

           });
			$('add-category-form').store('target',{
				onComplete:function(){
					if($('add-category-form').getParent('.dialog'))
					$('add-category-form').getParent('.dialog').retrieve('instance').close();
				}
			});
        </script>
</form>
";s:6:"expire";i:0;}