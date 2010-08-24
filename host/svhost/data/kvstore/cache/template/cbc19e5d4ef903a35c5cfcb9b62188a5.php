<?php exit(); ?>a:2:{s:5:"value";s:30415:"<?php $this->__view_helper_model['desktop_view_helper'] = kernel::single('desktop_view_helper');  $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper');  $this->_tag_stack[] = array('capture', array('name' => "header")); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => "header"), null, $this); ob_start();  echo $this->ui()->script(array('src' => "treelist.js",'app' => "ectools")); $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>
<div id="dltype-info-<?php echo $this->_vars['dt_id']; ?>">
<div style="text-align:right;"><?php $this->_tag_stack[] = array('help', array('docid' => "69",'type' => "link")); $this->__view_helper_model['desktop_view_helper']->block_help(array('docid' => "69",'type' => "link"), null, $this); ob_start(); ?>点击查看帮助<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['desktop_view_helper']->block_help($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?></div>
<h3 class="head-title">添加配送方式</h3>


<form action="index.php?app=b2c&ctl=admin_dlytype&act=saveDlType" id="dtypeForm" method="post" class="tableform">
<div class="division">
<?php if( $this->_vars['dt_info']['dt_id'] ){ ?>
	<input type="hidden" name="dt_id" id="aEditor-Garticleid-input" value="<?php echo $this->_vars['dt_info']['dt_id']; ?>" />
<?php }else{ ?>
	<input type="hidden" name="dt_id" id="aEditor-Garticleid-input" value="" />
<?php } ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <th><em class="red">*</em>配送方式名称：</th>
    <td><input type="text" name="dt_name" value="<?php echo $this->_vars['dt_info']['dt_name']; ?>" vtype="required" class="_x_ipt" caution="请填写配送方式名称" maxlength="20"></td>

  </tr>
    <tr>
    <th>选择默认物流公司：</th>
    <td>
      <select name="corp_id">
        <option value="0">请选择物流公司</option>
      <?php if($this->_vars['clist'])foreach ((array)$this->_vars['clist'] as $this->_vars['corp']){ ?>
        <option value='<?php echo $this->_vars['corp']['corp_id']; ?>' <?php if( $this->_vars['dt_info']['corp_id'] == $this->_vars['corp']['corp_id'] ){ ?>selected<?php } ?>><?php echo $this->_vars['corp']['name']; ?></option>
      <?php } ?>
      </select>
    </td>
  </tr>
  <tr>
    <th>类型：</th>
    <td><label><input type="radio" name="has_cod" value="0" <?php if( $this->_vars['dt_info']['has_cod'] == 'false' or $this->_vars['dt_info'] == "" ){ ?>checked<?php } ?>>先收款后发货</label>      <br />
    <label><input type="radio" name="has_cod" value="1" <?php if( $this->_vars['dt_info']['has_cod'] == 'true' ){ ?>checked<?php } ?>>货到付款</label><span class="notice-inline">选择货到付款后顾客无需再选择支付方式</span></td>
  </tr>
  <tr>
    <th>重量设置：</th>
    <td>首重重量&nbsp;<select name="firstunit" ><?php echo $this->__view_helper_model['base_view_helper']->function_html_options(array('options' => $this->_vars['weightunit'],'selected' => $this->_vars['dt_info']['firstunit']), $this);?></select>&nbsp;续重单位&nbsp;<select name="continueunit"><?php echo $this->__view_helper_model['base_view_helper']->function_html_options(array('options' => $this->_vars['weightunit'],'selected' => $this->_vars['dt_info']['continueunit']), $this);?></select></td>
    <th>&nbsp;</th><td>&nbsp;</td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <td><input type="checkbox" name="protect" value="1" <?php if( $this->_vars['dt_info']['protect']=='true' ){ ?>checked<?php } ?>>支持物流保价
    <script>
               $E('#dltype-info-<?php echo $this->_vars['dt_id']; ?> input[name=protect]').addEvent('click',function(){
                    if(this.checked){
                        $('protect_rate').show().getFormElements().set('disabled',false);
                    }else{
                      $('protect_rate').hide().getFormElements().set('disabled',true);
                    }               
               });
        $E('#dltype-info-<?php echo $this->_vars['dt_id']; ?> input[name=protect]')
        .fireEvent('click',$E('#dltype-info-<?php echo $this->_vars['dt_id']; ?> input[name=protect]'));

		  </script>
    </td>
    <th>&nbsp;</th><td>&nbsp;</td>
  </tr>
  <tr id="protect_rate">
    <th>保价设置：</th>
    <td>
        费率&nbsp;<input type="text" name="protect_rate" value="<?php echo $this->_vars['dt_info']['protect_rate']; ?>" style="width:25px;" class='_x_ipt' vtype='required&&number' caution="该项必填且只允许填写数字金额">%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;最低保价费&nbsp;<input type="text" name="minprice" value="<?php echo $this->_vars['dt_info']['minprice']; ?>" style="width:25px;" class='_x_ipt' vtype='required&&number' caution="该项必填且只允许填写数字金额">
    </td>
    <th>&nbsp;</th><td>&nbsp;</td>
  </tr>
  <tr>
    <th>地区费用类型：</th>
    <td>
    <div id='deliveryAreaToggle'>
      <label><input type="radio" name="setting" value='1' <?php if( $this->_vars['dt_info']['setting']=="1" or $this->_vars['dt_info']['setting']=="" ){ ?>checked<?php } ?>>统一设置</label><br /><label><input type="radio" name="setting" value='0' <?php if( $this->_vars['dt_info']['setting']=="0" ){ ?>checked<?php } ?>>指定配送地区和费用</label>
    </div>    
    <script>
          
          function deliveryAreaToggle(radio){
                 var tradio=radio;
                 var tradioValue=tradio.get('value');
                 var tmap={
                       '1':$('def_dexp'),
                       '0':$('deliveryAreabox')
                     };
                  if(tradio.checked){
                    tmap[tradioValue].show();
                    $('def_area_dexp').show().getFormElements().set('disabled',false);
                    tmap[tradioValue].getFormElements().set('disabled',false);
                  }else{
                    tmap[tradioValue].hide();
                    $('def_area_dexp').hide().getFormElements().set('disabled',true);

                    tmap[tradioValue].getFormElements().set('disabled',true);
                  }
          }
       
       </script>
    
    </td>
    <th>&nbsp;</th><td>&nbsp;</td>
  </tr>
    </table>
   </div>

<div class="division" id='def_dexp'>   
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <input type="hidden" name="price" value=0>
  <tr class="b-tp">
    <th style="padding-top:13px">配送费用：</th>
    <td>
    <div class='deliveryexpbox'>
      <div class='deliveryexp' style="<?php if( $this->_vars['dt_info']['dt_useexp']==1 ){ ?>display:none<?php } ?>">
                首重费用 <input style="width:30px;" type='text' name='firstprice' value="<?php echo $this->_vars['dt_info']['firstprice']; ?>" class="_x_ipt" vtype="required&&number" caution="该项必填且只允许填写数字金额"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                续重费用 <input style="width:30px;" type='text' name='continueprice' value="<?php echo $this->_vars['dt_info']['continueprice']; ?>" class="_x_ipt" vtype="required&&number" caution="该项必填且只允许填写数字金额"/>
				邮寄折扣 <input style="width:30px;" type='text' name='dt_discount' value="<?php echo $this->_vars['dt_info']['dt_discount']; ?>" class="_x_ipt" vtype="required&&number" caution="该项必填且只允许填写数字金额"/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='lnk chgexp' onclick='chaexps(this)'>使用公式</span>
                
     </div>
      <div class='deliveryexp' style='<?php if( $this->_vars['dt_info']['dt_useexp']==0 ){ ?>display:none<?php } ?>'>
                  配送公式 <input style="width:300px;" type='text' name='dt_expressions' value="<?php if( $this->_vars['dt_info']['dt_useexp'] ){  echo $this->_vars['dt_info']['dt_expressions'];  } ?>"  class="_x_ipt" vtype="required&&checkExp1&&checkExp2" /><?php echo $this->ui()->button(array('label' => "验证",'class' => "checkexp",'onclick' => "checkExp(this);"));?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='lnk chgexp' onclick='chaexps(this)'>取消公式</span>
				  <input type="hidden" name="dt_useexp" value="<?php echo $this->_vars['dt_info']['dt_useexp']; ?>">
       </div>  
      </div> 
    </td>
  </tr>
  </table>
  </div>
  <div class="division" id='def_area_dexp'>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="b-tp">
        <tr >
            <th>&nbsp;</th>
            <td><input type="checkbox" name="def_area_fee" value="1" <?php if( $this->_vars['dt_info']['def_area_fee']=='true' ){ ?>checked<?php } ?>>启用默认费用<span class="notice-inline">注意：未启用默认费用时，不在指定配送地区的顾客不能使用本配送方式下订单</span></td>
          <script>

               $E('#dltype-info-<?php echo $this->_vars['dt_id']; ?> input[name=def_area_fee]').addEvent('click',function(){
                    
                    if(this.checked){
                        $('area_dexp').show().getFormElements().set('disabled',false);
                    }else{
                      $('area_dexp').hide().getFormElements().set('disabled',true);
                    }
               
               });
        $E('#dltype-info-<?php echo $this->_vars['dt_id']; ?> input[name=def_area_fee]').fireEvent('click',$E('#dltype-info-<?php echo $this->_vars['dt_id']; ?> input[name=def_area_fee]'));
         $$("#deliveryAreaToggle input[name=setting]").addEvent('click',function(){
				
			$$("#deliveryAreaToggle input[name=setting]").each(deliveryAreaToggle);
        }).each(deliveryAreaToggle);
          </script>
        </tr>
        <tr id="area_dexp">
            <th>&nbsp;</th>
            <td>
                <div class='deliveryexpbox'>
                <div class='deliveryexp' style="<?php if( $this->_vars['dt_info']['dt_useexp']==1 ){ ?>display:none<?php } ?>">
                首重费用 <input type="text" name="firstprice" id="firstprice" value="<?php echo $this->_vars['dt_info']['firstprice']; ?>" style="width:30px;" class='_x_ipt' vtype='required&&number' caution="该项必填且只允许填写数字金额">&nbsp;续重费用<input type="text" name="continueprice" id="continueprice" value="<?php echo $this->_vars['dt_info']['continueprice']; ?>" style="width:30px;" class='_x_ipt' vtype='required&&number' caution="该项必填且只允许填写数字金额">
				&nbsp;邮寄折扣<input type="text" name="dt_discount" id="dt_discount" value="<?php echo ((isset($this->_vars['dt_info']['dt_discount']) && ''!==$this->_vars['dt_info']['dt_discount'])?$this->_vars['dt_info']['dt_discount']:1); ?>" style="width:30px;" class='_x_ipt' vtype='required&&number' caution="该项必填且只允许填写数字金额">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='lnk chgexp' onclick='chaexps(this)'>使用公式</span>
                </div>
                <div class='deliveryexp' style='<?php if( $this->_vars['dt_info']['dt_useexp']==0 ){ ?>display:none<?php } ?>'>
                  配送公式 <input style="width:300px;" type='text' name='dt_expressions' value="<?php echo $this->_vars['dt_info']['dt_expressions']; ?>"  class="_x_ipt" vtype="required&&checkExp1&&checkExp2" /><?php echo $this->ui()->button(array('label' => "验证",'class' => "checkexp",'onclick' => "checkExp(this);"));?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='lnk chgexp' onclick='chaexps(this)'>取消公式</span>
                  <input type="hidden" name="dt_useexp" value="<?php echo $this->_vars['dt_info']['dt_useexp']; ?>">
                </div> 
                </div>
            </td>
        </tr>
    </table>
  </div>
  
  <div class="division" id='deliveryAreabox'>
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <th>支持的配送地区:</th>
    <td>
        <div  id="deliveryArea-<?php echo $this->_vars['dt_info']['dt_id']; ?>" class='deliveryArea' style="background:#f8f8f8; border:1px solid #ccc;">
           <ol>
           
           <?php if( $this->_vars['dt_info']['area_fee_conf'] ){ ?>
           <input type='hidden' name='delidgroup'/>
               <script>
                       function deleteDelivery(d){
                          if (!confirm('删除后无法恢复，确定要删除吗？')){
                            return;
                          }
                          var areaid_group=d.getElement('input[name^=area_fee_conf]');
                          var delidgroupHidden=$E('#deliveryArea-<?php echo $this->_vars['dt_info']['dt_id']; ?> input[name=delidgroup]');
                          var _id;
                          areaid_group.get('name').replace(/\[(\d+)?\]/,function($0,$1){
                                _id=$1;   
                          });
                          if(_id){
                                delidgroupHidden.value+=(delidgroupHidden.value!=''?",":'')+_id;
                          }
                          d.dispose();
                         }
               </script>
             <?php if($this->_vars['dt_info']['area_fee_conf'])foreach ((array)$this->_vars['dt_info']['area_fee_conf'] as $this->_vars['key'] => $this->_vars['area']){ ?>
             <li class='division' style="list-style:decimal; margin-left:15px;">
                <div class='deliverycity'>
                                 <span style="float:right;" onclick='deleteDelivery($(this).getParent("li"))'><?php echo $this->ui()->img(array('app' => "desktop",'src' => 'bundle/delete.gif','style' => 'cursor:pointer;','alt' => "删除"));?></span>
                配送地区 <input style="width:300px;" type='text' name='area_fee_conf[<?php echo $this->_vars['key']; ?>][areaGroupName]' readonly=true required="true" value='<?php echo $this->_vars['area']['areaGroupName']; ?>' class="_x_ipt" vtype="required" caution="配送地区不能为空" onclick="regionSelect(this);">
                <input type='hidden' name='area_fee_conf[<?php echo $this->_vars['key']; ?>][areaGroupId]' value="<?php echo $this->_vars['area']['areaGroupId']; ?>"/><?php echo $this->ui()->img(array('class' => 'regionSelect','src' => 'bundle/editcate.gif','style' => 'cursor:pointer;','onclick' => 'regionSelect(this)','alt' => '编辑地区','title' => "编辑地区"));?>
                </div>
					<div class='deliveryexpbox' acckey="<?php echo $this->_vars['key']; ?>">
						<div class='deliveryexp' style="<?php if( $this->_vars['area']['dt_useexp']==1 ){ ?>display:none<?php } ?>">
						首重费用 <input style="width:30px;" type='text' name='area_fee_conf[<?php echo $this->_vars['key']; ?>][firstprice]' value="<?php echo $this->_vars['area']['firstprice']; ?>" <?php if( $this->_vars['area']['dt_useexp']=="0" ){ ?>class="_x_ipt"<?php } ?> vtype="required&&number" caution="该项必填且只允许填写数字金额"/>
						&nbsp;续重费用 <input style="width:30px;" type='text' name='area_fee_conf[<?php echo $this->_vars['key']; ?>][continueprice]' value="<?php echo $this->_vars['area']['continueprice']; ?>" <?php if( $this->_vars['area']['dt_useexp']=="0" ){ ?>class="_x_ipt"<?php } ?> vtype="required&&number" caution="该项必填且只允许填写数字金额"/>
						&nbsp;邮寄折扣<input type="text" name="area_fee_conf[<?php echo $this->_vars['key']; ?>][dt_discount]" value="<?php echo ((isset($this->_vars['area']['dt_discount']) && ''!==$this->_vars['area']['dt_discount'])?$this->_vars['area']['dt_discount']:1); ?>" style="width:30px;" class='_x_ipt' vtype='required&&number' caution="该项必填且只允许填写数字金额">
						&nbsp;<span class='lnk chgexp' onclick='chaexps(this)'>使用公式</span>
							</div>
						<div class='deliveryexp' style='<?php if( $this->_vars['area']['dt_useexp']==0 ){ ?>display:none<?php } ?>'>
						 配送公式 <input style="width:300px;" type='text' name='area_fee_conf[<?php echo $this->_vars['key']; ?>][expressions]' value="<?php echo $this->_vars['area']['dt_expressions']; ?>" <?php if( $this->_vars['area']['dt_useexp']=="1" ){ ?>class="_x_ipt"<?php } ?> vtype="required&&checkExp1&&checkExp2" /><?php echo $this->ui()->button(array('label' => "验证",'class' => "checkexp",'onclick' => "checkExp(this);"));?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='lnk chgexp' onclick='chaexps(this)'>取消公式</span> 
						  <input type='hidden' name='area_fee_conf[<?php echo $this->_vars['key']; ?>][dt_useexp]' value='<?php echo $this->_vars['area']['dt_useexp']; ?>'/>
						</div>
					</div>
               
             </li>
             <?php }  }else{  } ?>
           </ol>
           <?php echo $this->ui()->button(array('label' => "为指定的地区设置运费",'class' => "add-dlyarea"));?>
        </div>
    </td>
  </tr>
    </table>
    </div>
    
    <div class="division b-tp">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <th>排序：</th>
                <td>
                    <input  type="text" vtype="unsignedint" name="ordernum" id="ordernum" value="<?php echo $this->_vars['dt_info']['ordernum']; ?>" size=5 caution="请输入排序项，并且为正整数" class="_x_ipt"/>
                </td>
            </tr>
            <tr>
                <th>状态：</th>
                <td><input type="radio" name="dt_status" <?php if( $this->_vars['dt_info']['dt_status']=="1" or $this->_vars['dt_info']['dt_status']=="" ){ ?>checked<?php } ?> value="1">启用<br /><input type="radio" name="dt_status" value="0" <?php if( $this->_vars['dt_info']['dt_status']=="0" ){ ?>checked<?php } ?>>关闭</td>
            </tr>
         </table>
  </tr>
    </div>
    
    <div class="division b-tp">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <th>详细介绍：</th>
    <td><?php echo $this->ui()->input(array('type' => "html",'name' => "detail",'value' => $this->_vars['dt_info']['detail']));?></td>
  </tr>
    </table>
    </div>

</form>
</div>


<script>
/*var DialogExp ;
$ES(".checkexp","area-tbody-dom").each(function(item, index, itobj){
  item.addEvent('click',function(e){
    e=new Event(e);
    var ipt=e.target;
    var id = 'check_'+ipt.getAttribute('areaid');
    DialogExp = new Dialog('index.php?app=b2c&app=ectools&ctl=regions&act=checkExp&area_id='+ipt.getAttribute('areaid')+'&expvalue='+encodeURIComponent($(id).value),{modal:true,title:'验算配送公式'});
  });
});*/

validateMap.set('checkExp1',['公式中含有非法字符',function(e,v){
           return !(new RegExp("/^[^\]\[\}\{\)\(0-9WwPp\+\-\/\*]+$/")).test(v);
   }]);
validateMap.set('checkExp2',['公式格式不正确',function(e,v){
           var price = 100;
           var weight = 100;
           var str ;
              str = v.replace(/(\[)/g, "getceil(");
              str = str.replace(/(\])/g, ")");
              str = str.replace(/(\{)/g, "getval(");
              str = str.replace(/(\})/g, ")");
              str = str.replace(/(W)/g, weight);
              str = str.replace(/(w)/g, weight);
              str = str.replace(/(P)/g, price);
              str = str.replace(/(p)/g, price);
          try {
            eval(str);
            return true;
          }catch(e){
            return false;
          }
   }]);


function saveDt(){
   $ES('textarea[ishtml=true]',$('dltype-info-<?php echo $this->_vars['dt_info']['dt_id']; ?>')).getValue();
   W.page('index.php?app=b2c&app=ectools&ctl=regions&act=saveDlType',{data:$('dltype-info-<?php echo $this->_vars['dt_info']['dt_id']; ?>'),method:'post'})
}
function getval(expval){
  if (eval(expval) > 0.000001){
    return 1;
  }else if (eval(expval) >-0.000001&&eval(expval)< 0.000001){
    return 1/2;
  }else{
    return 0;
  }
}
function getceil(expval){
  if (eval(expval) > 0){
    return Math.ceil(eval(expval)-0.000001);
  }else{
    return 0;
  }
}


       function chaexps(el){
        var  dexpsbox = $(el).getParent('.deliveryexpbox');
		var arr_depxs = dexpsbox.getElements('.deliveryexp');
		arr_depxs.each(function(obj){			
			obj.setStyle('display','none'==obj.getStyle('display')?'':'none');
			if (obj.getElement('input[name^=useexp]'))
			{
				if (!obj.isDisplay())
				{
					obj.getElement('input[name^=useexp]').set('value',0);
				}
				else
				{
					obj.getElement('input[name^=useexp]').set('value',1);
				}
			}
			
			if (obj.getElement('input[name^=dt_useexp]'))
			{
				if (!obj.isDisplay())
				{
					obj.getElement('input[name^=dt_useexp]').set('value',0);
				}
				else
				{
					obj.getElement('input[name^=dt_useexp]').set('value',1);
				}
			}
			
			if (obj.getElement('input[name^=\'area_fee_conf[' + dexpsbox.get('acckey') + '][useexp]\']'))
			{
				if (!obj.isDisplay())
				{
					obj.getElement('input[name^=\'area_fee_conf[' + dexpsbox.get('acckey') + '][useexp]\']').set('value',0);
				}
				else
				{
					obj.getElement('input[name^=\'area_fee_conf[' + dexpsbox.get('acckey') + '][useexp]\']').set('value',1);
				}
			}
			
			if (obj.getElement('input[name^=\'area_fee_conf[' + dexpsbox.get('acckey') + '][dt_useexp]\']'))
			{
				if (!obj.isDisplay())
				{
					obj.getElement('input[name^=\'area_fee_conf[' + dexpsbox.get('acckey') + '][dt_useexp]\']').set('value',0);
				}
				else
				{
					obj.getElement('input[name^=\'area_fee_conf[' + dexpsbox.get('acckey') + '][dt_useexp]\']').set('value',1);
				}
			}
			
		});
         /*   dexps.each(function(i){
               if(i.getElement('input[name^=useexp]')){
                   if(!i.isDisplay()){
                     i.getElement('input[name^=useexp]').set('value',0);
                   }else{
                    i.getElement('input[name^=useexp]').set('value',1);
                   }
               }
               else if (i.getElement('input[name^=dt_useexp]'))
               {
                   if(!i.isDisplay()){
                     i.getElement('input[name^=dt_useexp]').set('value',0);
                   }else{
                    i.getElement('input[name^=dt_useexp]').set('value',1);
                   }
               }
            });*/
       }
 
       void function(){
            var btn_add=$E('#deliveryArea-<?php echo $this->_vars['dt_info']['dt_id']; ?> .add-dlyarea');
			
            var getItemTemplete=function(){
			var key = $ES('#deliveryArea-<?php echo $this->_vars['dt_info']['dt_id']; ?> .division ').length + 1;
            var btn = '<?php echo $this->ui()->button(array('label' => "验证",'class' => "checkexp",'onclick' => "checkExp(this);"));?>';
            var itemTemplete = "<div class='deliverycity'>";
                itemTemplete += "<span style='float:right;' onclick='";
                itemTemplete += "if(!confirm(\"删除后无法恢复,确定要删除吗?\"))return;$(this).getParent(\"li\").remove();' ";
                itemTemplete += '><?php echo $this->ui()->img(array('app' => "desktop",'src' => "bundle/delete.gif",'style' => "cursor:pointer;",'alt' => "删除",'title' => "删除"));?></span>配送地区 ';
                itemTemplete += "<input style='width:300px;' type='text' name='area_fee_conf[" + key + "][areaGroupName]' readonly=true  value='' class='_x_ipt' vtype='required' caution='配送地区不能为空' onclick=\"regionSelect(this);\">";
                itemTemplete+="<input type='hidden' name='area_fee_conf[" + key + "][areaGroupId]' value=''/>";
                itemTemplete+='<?php echo $this->ui()->img(array('class' => "regionSelect",'src' => "bundle/editcate.gif",'style' => "cursor:pointer;",'onclick' => "regionSelect(this)",'alt' => "编辑地区",'title' => "编辑地区"));?>';
             
                itemTemplete+="</div>";
                itemTemplete+="<div class='deliveryexpbox'><div class='deliveryexp'>";
                itemTemplete+="首重费用 <input style='width:30px;' type='text' name='area_fee_conf[" + key + "][firstprice]' class='_x_ipt' vtype='required&&number' caution='该项必填且只允许填写数字金额' value="+$('firstprice').get('value')+" >";
                itemTemplete+="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;续重费用 <input style='width:30px;' type='text' name='area_fee_conf[" + key + "][continueprice]' class='_x_ipt' vtype='required&&number' caution='该项必填且只允许填写数字金额' value="+$('continueprice').get('value')+" >";
                itemTemplete+="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;邮寄折扣 <input style='width:30px;' type='text' name='area_fee_conf[" + key + "][dt_discount]' class='_x_ipt' value='1' vtype='required&&number' caution='该项必填且只允许填写数字金额' value="+$('dt_discount').get('value')+" >";
                itemTemplete+="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='lnk chgexp' onclick='chaexps(this)'>使用公式</span>";
                itemTemplete+="</div>";
                itemTemplete+="<div class='deliveryexp' style='display:none'>";
                itemTemplete+="配送公式 <input style='width:300px;' type='text' name='area_fee_conf[" + key + "][expressions]' value='' vtype='required&&checkExp1&&checkExp2' />";
                itemTemplete+= btn +"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='lnk chgexp' onclick='chaexps(this)'>取消公式</span>";
                itemTemplete+="<input type='hidden' name='area_fee_conf[" + key + "][dt_useexp]' value='0'/>";
                itemTemplete+="</div></div>";
             
                return  itemTemplete;
              };
            var list=$E('#deliveryArea-<?php echo $this->_vars['dt_info']['dt_id']; ?> ol');
            btn_add.addEvent('click',function(){
               var newitem=new Element('li',{'class':'division','style':'line-height:30px;'}).set('html',getItemTemplete()).inject(list);
               regionSelect(newitem.getElement('.regionSelect'));
            });
            
            //$$('#dltype-info-<?php echo $this->_vars['dt_id']; ?> .chgexp').each(chaexps);
           
       }();
       function regionSelect(el){
          var el=$(el).getParent('.deliverycity');
          var iptText=el.getElement('input[type=text]');
          var iptHidden=el.getElement('input[type=hidden]');

		  new ModeDialog('index.php?app=ectools&ctl=regions&act=showRegionTreeList&p[0]='+el.uid+'&p[1]=multi',{
			  width:270,height:330,params:{iptText:iptText,iptHidden:iptHidden}});
		}
    var checkExp=function(btn){
        btn=$(btn);
        var ipt=btn.getPrevious('input');
        var expValue=ipt.getValue();
        new Dialog('index.php?app=b2c&ctl=admin_dlytype&act=checkExp&expvalue='+encodeURIComponent(expValue),
        { modal:true,
         title:'验算配送公式',
         onShow:function(){

             this.dialog.store('targetIpt',ipt);
          }
        }
        );
    }

	$('dtypeForm').store('target',{
		onComplete:function(){
			if(opener.finderGroup['<?php echo $_GET['_finder']['finder_id']; ?>'])
			opener.finderGroup['<?php echo $_GET['_finder']['finder_id']; ?>'].refresh();
			window.close();
		}
	});
   </script>
   
   <script>

subDtypeForm = function (event,sign){ 
	   var target={};
	   switch (sign){
			case 1:                    //保存不关闭
				$extend(target,{
					onComplete:function(e){
						if(window.opener.finderGroup&&window.opener.finderGroup['<?php echo $_GET['finder_id']; ?>'])
						window.opener.finderGroup['<?php echo $_GET['finder_id']; ?>'].refresh();
						
						if(!JSON.decode(e).dt_id){
							event.target.removeProperty('disabled');
							return;
						}
						var id = JSON.decode(e).dt_id;
						if(id > 0){
							$('aEditor-Garticleid-input').value = JSON.decode(e).dt_id
						}
						event.target.disabled = false;
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
	    var _form=$('dtypeForm');
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
            <?php echo $this->ui()->button(array('label' => "保存并关闭窗口",'class' => "btn-primary",'onclick' => "subDtypeForm(event,2)")); echo $this->ui()->button(array('label' => "保存当前",'class' => "btn-primary",'onclick' => "subDtypeForm(event,1)")); echo $this->ui()->button(array('label' => "关  闭",'class' => "btn-secondary",'onclick' => "if(confirm('确定退出?'))window.close()"));?>
        </td>
        </tr>
        </tbody></table>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>
";s:6:"expire";i:0;}