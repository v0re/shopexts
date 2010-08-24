<?php exit(); ?>a:2:{s:5:"value";s:12097:"<script>
$('gEditor').retrieve('setTabs',function(){
         var gtabs=$ES('#menu-desktop .spage-side-nav li.l-handle');        
         new ItemAgg(gtabs,$ES('#gEditor .spage-main-box'),{
            activeName:'cur',
            onActive:function(tab,item){    
                 var anotherItems=$$($A(this.items).remove(item));
                  if(tab.hasClass('all')){
                     anotherItems.show();
                  }else{
                     anotherItems.hide();
                  }
            }
         });
}());
window.addEvent('domready',function(){
    category_change= function (id,typeid){
        if(goodsEditor)goodsEditor.catalogSelect(typeid,id);
    }


});

</script>

<h3 >基本信息</h3> 
<div id="x-g-basic" class="goods-detail" >
<div class="tableform" >
	 	 <table border="0" cellpadding="0" cellspacing="0">
             <tr>
 					 <th>分类：</th>
 	                    <td>
 	  <?php echo $this->ui()->input(array('type' => "category",'value' => $this->_vars['goods']['category']['cat_id'],'name' => "goods[category][cat_id]",'callback' => "category_change"));?>
 	                    </td>
 			</tr>

             <tr>
			   		<th>类型：</th>
                 <td><select name="goods[type][type_id]" id="gEditor-GType-input">
                         <!--<option value='1'>通用商品类型</option>-->
                         <?php if($this->_vars['gtype'])foreach ((array)$this->_vars['gtype'] as $this->_vars['type']){ ?> 
                         <option class="optionlevels" value='<?php echo $this->_vars['type']['type_id']; ?>' <?php if( $this->_vars['type']['type_id']==$this->_vars['goods']['type']['type_id'] ){ ?>selected<?php } ?>><?php echo $this->_vars['type']['name']; ?>
                         </option>
                         <?php } ?>
                     </select>
                 </td>
                 </tr>
                 <tr>
                     <th>商品名称：</th>
                     <td><?php echo $this->ui()->input(array('type' => "text",'id' => "id_gname",'name' => "goods[name]",'required' => "true",'vtype' => 'required','value' => $this->_vars['goods']['name'],'style' => "width:60%"));?><em><font color="red">*</font></em></td>
                 </tr>
                <?php if( $this->_vars['goodsbn_display_switch'] ){ ?>
                <tr>
                    <th>商品编号：</th>
                    <td><?php echo $this->ui()->input(array('type' => "text",'name' => "goods[bn]",'value' => $this->_vars['goods']['bn']));?></td>
                </tr>
                <?php }else{ ?>
                <input type='hidden' name="goods[bn]" value="<?php echo $this->_vars['goods']['bn']; ?>">
                <?php } ?>
                
                <tr>
                    <th>商品关键词：</th>
                    <td><?php if($this->_vars['goods']['keywords'])foreach ((array)$this->_vars['goods']['keywords'] as $this->_vars['keywords']){  $this->_vars['keyword']="{$this->_vars['keyword']}|{$this->_vars['keywords']['keyword']}";  } ?><input type="text" name="keywords" value="<?php echo trim($this->_vars['keyword'],'|'); ?>" maxlength="100"/><span class="notice-inline ">仅用于在前台、后台筛选商品，多个关键词用半角竖线"|"分开</span></td>
                </tr>
                <tr>
				 <th>品牌：</th>
                  <td><?php echo $this->ui()->input(array('type' => "select",'name' => "goods[brand][brand_id]",'nulloption' => "1",'rows' => $this->_vars['brandList'],'valueColumn' => "brand_id",'labelColumn' => "brand_name",'value' => $this->_vars['goods']['brand']['brand_id']));?></td>   
                </tr>  
			 	

                
                <tr>
                    <th>商品简介：</th>
                    <td><?php echo $this->ui()->input(array('type' => "textarea",'class' => "x-input",'value' => $this->_vars['goods']['brief'],'name' => "goods[brief]",'cols' => "50",'rows' => "5",'maxth' => "255"));?>
                        <span class="notice-inline">简短的商品介绍,请不要超过255字节</span></td>
                </tr>
				

	 <tr>
           <th>
				商品相册：
		   </th>

            <td>
				<div class="division" style="margin:0">
                	<div class="clearfix">
                    	<span id="pic-uploader">
				  <?php echo $this->ui()->button(array('app' => "desktop",'class' => "btn-upload",'label' => "添加商品图片",'icon' => "image_new.gif"));?>
						</span>  
                     </div>     
                    <div class="pic-area" id="pic-area">
				       <input type="hidden" name="image_default" value="<?php echo $this->_vars['goods']['image_default_id']; ?>" />
						<?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',"admin/goods/detail/img/gimage_goods.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
					</div>   
                </div> 
			</td>
        </tr> 
        <th>
			列表页图片：
		</th> 
		<td> 
		 <input id="udfimg-false" type="radio" name="goods[udfimg]" value=false <?php if( $this->_vars['goods']['udfimg'] != 'true' ){ ?>checked<?php } ?> />
		    <label for="udfimg-false" >用商品相册默认图</label> 
		<input id="udfimg-true" type="radio" name="goods[udfimg]" value=true <?php if( $this->_vars['goods']['udfimg'] == 'true' ){ ?>checked<?php } ?> />
         <label for="udfimg-true" >自定义商品列表页图片</label>  


		<div id="imageuparea" style="display:<?php if( $this->_vars['goods']['udfimg']=='true' ){ ?>block<?php }else{ ?>none<?php } ?>">
		   <h4 style="margin:0">商品在列表页显示的图片</h4>
   <?php echo $this->ui()->input(array('name' => "goods[thumbnail_pic]",'type' => "image",'value' => $this->_vars['goods']['thumbnail_pic'],'width' => "120",'height' => "90"));?>
		 </div>
		<script> 
				$$('#x-g-basic input[name^=goods[udfimg]').addEvent('change',function(){
							
							if($('udfimg-true').checked){
								$('imageuparea').show();
							}else{
								 $('imageuparea').hide();
							}
					
				});
		</script>
		</td>
</table>
</div>

<div class="tableform" id="goods-spec">
                     
	<?php if( $this->_vars['goods']['spec']  ){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',"admin/goods/detail/spec/spec.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  }else{  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',"admin/goods/detail/spec/nospec.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  } ?>

</div>



<?php if( $this->_vars['goods']['type']['setting']['use_props'] ){ ?>  
 <div class="tableform">  
       
            <table border="0" cellpadding="0" cellspacing="0">
                <?php if( $this->_vars['goods']['type']['setting']['use_props'] ){  if($this->_vars['goods']['type']['props'])foreach ((array)$this->_vars['goods']['type']['props'] as $this->_vars['key'] => $this->_vars['aProp']){ ?>
                <tr class="prop">
                    <th><?php echo $this->_vars['aProp']['name']; ?>：</th>
                    <td><?php $this->_vars["p_col"]="p_{$this->_vars['key']}";  if( $this->_vars['aProp']['type'] == 'select' ){  echo $this->ui()->input(array('name' => "goods[props][p_{$this->_vars['key']}][value]",'type' => "select",'nulloption' => "1",'options' => $this->_vars['aProp']['options'],'value' => "{$this->_vars['goods']['props'][$this->_vars['p_col']]['value']}")); }else{  echo $this->ui()->input(array('type' => "text",'name' => "goods[props][p_{$this->_vars['key']}][value]",'maxlength' => "25",'value' => "{$this->_vars['goods']['props'][$this->_vars['p_col']]['value']}")); } ?></td>
                </tr>
                <?php }  } ?>
            </table>
   </div>
<?php } ?>



		<div class="tableform">
				<table border="0" cellpadding="0" cellspacing="0">   
					
					<tr>
	                     <th>起定量：</th>
	                     <td><?php echo $this->ui()->input(array('type' => "unsigned",'value' => $this->_vars['goods']['min_buy'],'name' => "goods[min_buy]",'style' => "width:60px",'maxlength' => "25"));?></td>
	                </tr>
	                <?php if( $this->_vars['goods']['type']['is_physical'] ){ ?>
	                <tr>
	                    <th>计量单位：</th>
	                    <td><?php echo $this->ui()->input(array('type' => "text",'value' => $this->_vars['goods']['unit'],'name' => "goods[unit]",'maxlength' => "25",'style' => "width:60px"));?></td>
	                </tr>
	                <?php }  if( $this->app->getConf('goods.package_use.switch') ){ ?>
	                <tr>
	                     <th>开启打包：</th>
	                     <td>
	                     <input type="radio"  name="goods[package_use]"<?php if( $this->_vars['goods']['package_use'] == 1  ){ ?> checked="checked"<?php } ?> value="1" >是
	                     <input type="radio"  name="goods[package_use]"<?php if( $this->_vars['goods']['package_use'] == 0 ){ ?> checked="checked"<?php } ?> value="0" >否
	                     </td>
	                </tr>
	                 <tr class="package_use" <?php if( $this->_vars['goods']['package_use'] == 0  ){ ?>style="display:none"<?php } ?>>
	                     <th>打包比例：</th>
	                      <td><?php echo $this->ui()->input(array('type' => "unsigned",'value' => $this->_vars['goods']['package_scale'],'name' => "goods[package_scale]",'style' => "width:60px",'maxlength' => "25"));?>(多少个一包)</td>
	                </tr>
	                <tr class="package_use" <?php if( $this->_vars['goods']['package_use'] == 0  ){ ?>style="display:none"<?php } ?>>
	                     <th>打包单位：</th>
	                      <td><?php echo $this->ui()->input(array('type' => "text",'value' => $this->_vars['goods']['package_unit'],'name' => "goods[package_unit]",'style' => "width:60px",'maxlength' => "25"));?></td>
	                </tr>
                    <?php } ?>
                    
	                <tr>
	                    <th>立即上架：</th>
	                     <td>
	                     <input type="radio" name="goods[status]"<?php if( $this->_vars['goods']['status'] != 'false'  ){ ?> checked="checked"<?php } ?> value="true" >是
	                     <input type="radio" name="goods[status]"<?php if( $this->_vars['goods']['status'] == "false" ){ ?> checked="checked"<?php } ?> value="false" >否
	                     </td>
	                </tr>
	                <tr>
	                     <th>无库存也可销售：</th>
	                     <td>
	                     <input type="radio"  name="goods[nostore_sell]"<?php if( $this->_vars['goods']['nostore_sell'] == 1  ){ ?> checked="checked"<?php } ?> value="1" >是
	                     <input type="radio"  name="goods[nostore_sell]"<?php if( $this->_vars['goods']['nostore_sell'] == 0 ){ ?> checked="checked"<?php } ?> value="0" >否
	                     </td>
	                </tr>

	                <?php if( $this->app->getConf('site.get_policy.method') ==3 ){ ?>
	                  <tr>
	                    <th>积分：</th>
	                    <td><?php echo $this->ui()->input(array('type' => "digits",'value' => $this->_vars['goods']['gain_score'],'name' => "goods[gain_score]",'maxlength' => "25"));?><input type='hidden' name='goods[score_setting]' value='number'/></td>
	                  </tr>
	                <?php } ?>
					</table>
			</div>




</div>
<script>
(function(){
    
$ES('#x-g-basic input[name^=goods[package_use]').addEvent('click',function(e){
     
        var v=this.checked&&this.value=="1"?[false,'']:[true,'none'];
      
        $$('.package_use').each(function(el){   
            el.setStyle('display',v[1]).getElement('input').set('disabled',v[0]);
        });
});

})();


</script>



    
";s:6:"expire";i:0;}