<?php
function smarty_function_input($params, &$smarty){

    if(isset($params['attributes'])){
        $params = $params['attributes'];
    }

    $params['style'] .= '';
    if(isset($params['width'])){
        $params['style'] = 'width:'.$params['width'].'px;'.$params['style'];
        unset($params['width']);
    }

    $params['class'] = $params['class']?$params['class'].' _x_ipt':'_x_ipt'.' '.$params['type'];

    $params['vtype'] = isset($params['vtype'])?$params['vtype']:$params['type'];
    if(isset($smarty->_tpl_vars['disabledElement']) && $smarty->_tpl_vars['disabledElement']){
        $params['disabled'] = 'disabled';
    }
    
    if(substr($params['type'],0,4)=='enum'){
        $params['type'] = $params['inputType']?$params['inputType']:'select';
        $params['vtype'] = $params['inputType']?$params['inputType']:'select';
    }elseif(substr($params['type'],0,7)!='object:'){
        $params['type'] = $params['inputType']?$params['inputType']:$params['type'];
        $params['vtype'] = $params['inputType']?$params['inputType']:$params['type'];
    }

    if(substr($params['type'],0,7)=='object:'){
        include('objects.php');
        $aTmp = explode(':',$params['type']);
        $params['filter'] = $params['options'];    //传递filter参数 added by Ever 20080701 
        if (!$smarty->_loaded_object_module[$aTmp[1]][$aTmp[2]]['data']){
            $mod = &$smarty->system->loadModel($objects[$aTmp[1]]);
            if($aTmp[2]){
                $params['data'] = $mod->$aTmp[2]();
                $smarty->_loaded_object_module[$aTmp[1]][$aTmp[2]]['data']=$params['data'];
            }
        }else{
            $mod = &$smarty->system->loadModel($objects[$aTmp[1]]);
            $params['data']= $smarty->_loaded_object_module[$aTmp[1]][$aTmp[2]]['data'];
        }
        return $mod->inputElement($params);
    }else{
        switch($params['type']){
        case 'text':
            return buildTag($params,'input autocomplete="off"');
            break;

        case 'password':
            return buildTag($params,'input autocomplete="off"');
            break;

        case 'search':
            return buildTag($params,'input autocomplete="off"');
            break;

        case 'date':
            if(!$params['id']){
                $domid = 'mce_'.substr(md5(rand(0,time())),0,6);
                $params['id'] = $domid;
            }else{
                $domid = $params['id'];
            }
            if(is_int($params['value'])){
                $params['value'] = mydate('Y-m-d',$params['value']);
            }
//            $params['value'] = mydate('Y-m-d',$params['value']);           
            $params['type'] = 'text';
            return buildTag($params,'input autocomplete="off"').'<script>$("'.$domid.'").makeCalable();</script>';
            break;
            
        case 'color':
            if(!$params['id']){
                $domid = 'colorPicker_'.substr(md5(rand(0,time())),0,6);
                $params['id'] = $domid;
            }else{
                $domid = $params['id'];
            }
            if($params['value']==''){
               $params['value']='default';
     //          $params['style']='background-color:#ffffff;cursor:pointer';
            }else{
       //        $params['style']='background-color:'.$params['value'].';cursor:pointer';
            }
 //           $params['readonly']='false';
            return buildTag($params,'input autocomplete="off"').' <input type="button" id="c_'.$domid.'" style="width:22px;height:22px;background-color:'.$params['value'].';border:0px #ccc solid;cursor:pointer"/><script>
            new GoogColorPicker("c_'.$domid.'",{
               onSelect:function(hex,rgb,el){
                  $("'.$domid.'").set("value",hex);
                  el.setStyle("background-color",hex);
               }
            })</script>';
            break;

        case 'time':
            if(is_int($params['value'])){
                $params['value'] = mydate('Y-m-d H:i',$params['value']);
            }
            return buildTag($params,'input autocomplete="off"');
            break;

        case 'file':
            if($params['backend']=='public'){
                if(!$GLOBALS['storager']){
                    $system = &$GLOBALS['system'];
                    $GLOBALS['storager']=$system->loadModel('system/storager');
                }
                $storager = &$GLOBALS['storager'];
                $url = $storager->getUrl($params['value']);
                $img=array('png'=>1,'gif'=>1,'jpg'=>1,'jpeg'=>1);
                if($img[strtolower(substr($url,strrpos($url,'.')+1))]){
                    $html = '<img src="'.$url.'?'.time().'" style="float:none" />';
                }else{
                    $html = $url;
                }
            }else{
                $html='';
            }
            return $html.buildTag($params,'input autocomplete="off"');
            break;

        case 'bool':
            $params['type'] = 'checkbox';
            if($params['value']=='true' || intval($params['value']) > 0)$params['checked']='checked';
            if($params['value'] === '0' || $params['value'] === '1'){
                $params['value'] = 1;
            }else{
                $params['value'] = 'true';
            }
            return buildTag($params,'input');
            break;

        case 'combox':
            return buildTag($params,'input autocomplete="off"');
            break;

        case 'html':
            $id = 'mce_'.substr(md5(rand(0,time())),0,6);
            $system = &$GLOBALS['system'];
            $editor_type=$system->getConf("system.editortype");
            $editor_type==''?$editor_type='textarea':$editor_type='wysiwyg';
            if($editor_type =='textarea'||$params['editor_type']=='textarea'){
               $smarty->_smarty_include(array(
                    'smarty_include_tpl_file' =>'editor/style_2.html',
                    'smarty_include_vars' =>array('var'=>$id,'for'=>$id)
                ));
            }else{
              $smarty->_smarty_include(array(
                    'smarty_include_tpl_file' =>'editor/style_1.html', 
                    'smarty_include_vars' =>array('var'=>$id,'for'=>$id,'includeBase'=>($params['includeBase']?$params['includeBase']:true))
                ));
            }
            $params['id']=$id;
            $params['editor_type']=$params['editor_type']?$params['editor_type']:$editor_type;
            $smarty->_smarty_include(array(
                    'smarty_include_tpl_file' =>'editor/body.html', 
                    'smarty_include_vars' =>$params
                ));
            break;

        case 'textarea':
            $value = $params['value'];
            if($params['width']){
                $params['style'].=';width:'.$params['width'];
                unset($params['width']);
            }
            if($params['height']){
                $params['style'].=';height:'.$params['height'];
                unset($params['height']);
            }
            unset($params['value']);
            return buildTag($params,'textarea',false).htmlspecialchars($value).'</textarea>';
            break;

        case 'checkbox':
            $params['selected'] = $params['value'];
            smarty_core_load_plugins(array('plugins' => array(array('function', 'html_checkboxes',$smarty->_current_file,$smarty->_current_line_no, 20, false))),$smarty);
            return smarty_function_html_checkboxes($params, $smarty);
            break;

        case 'radio':
            $params['selected'] = $params['value'];
            unset($params['value']);
            smarty_core_load_plugins(array('plugins' => array(array('function', 'html_radios',$smarty->_current_file,$smarty->_current_line_no, 20, false))),$smarty);
            return smarty_function_html_radios($params, $smarty);
            break;

        case 'select':
            if(isset($params['rows'])){
                
                    foreach($params['rows'] as $item){
                        $params['options'][$item[$params['valueColumn']]] = $item[$params['labelColumn']].$out;
                    }
                
            }
            if($params['nulloption']){
                $params['options'] = array_merge2(array('' => __('- 请选择 -')), $params['options']);
            }
            $params['selected'] = $params['value'];
            $t = buildTag($params,'select',false);
            unset($params['name']);
            smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options',$smarty->_current_file,$smarty->_current_line_no, 20, false))),$smarty);
            return $t.smarty_function_html_options($params, $smarty).'</select>';
            break;

        case 'fontset':
            $params['options'] = array('0'=>'默认','1'=>'粗体','2'=>'斜体','3'=>'中线');
            $params['selected'] = $params['value'];
            $t = buildTag($params,'select',false);
            unset($params['name']);
            smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options',$smarty->_current_file,$smarty->_current_line_no, 20, false))),$smarty);
            return $t.smarty_function_html_options($params, $smarty).'</select>';
            //return '<select id="'.$params['name'].'" name="'.$params['name'].'">'._comset_set($params['value'],$params['font']).'</select>';
            break;

        case 'region':
            $SYSTEM = &$GLOBALS['system'];
            $loc = &$SYSTEM->loadModel('system/local');
            if($params['required'] == 'true'){
                $req = ' vtype="area"';
            }else{
                $req = ' vtype='.$params['pptype'];
            }
            if(!$params['value']){
                $package = $params['package']?$params['package']:$SYSTEM->getConf('system.location');                
                $package=$package?$package:'mainland';
                return '<span package="'.$package.'" class="span _x_ipt"'.$req.'><input type="hidden" name="'.$params['name'].'" />'.$loc->get_area_select(null,$params).'</span>';
            }else{
                list($package,$regions,$region_id) = explode(':',$params['value']);
                if(!is_numeric($region_id)){
                    if(!$package){
                        $package = $SYSTEM->getConf('system.location');
                    }
                    return '<span package="'.$package.'" class="span _x_ipt"'.$req.'><input type="hidden" name="'.$params['name'].'" />'.$loc->get_area_select(null,$params).'</span>';
                }else{
                    $arr_regions = array();
                    $ret = '';
                    while($region_id && ($region = $loc->instance($region_id,'region_id,local_name,p_region_id'))){
                        array_unshift($arr_regions,$region);
                        if($region_id = $region['p_region_id']){
                            $ret = '<span class="x-region-child">&nbsp;-&nbsp'.$loc->get_area_select($region['p_region_id'],$params,$region['region_id']).$ret.'</span>';
                        }else{
                            $ret = '<span package="'.$package.'" class="span _x_ipt"'.$req.'><input type="hidden" value="'.$params['value'].'" name="'.$params['name'].'" />'.$loc->get_area_select(null,$params,$region['region_id']).$ret.'</span>';
                        }
                    }
                    return $ret;
                }
            }
            break;

        case 'arrow':
            $params['selected'] = $params['value'];
            $params['options'] = array('arrow_1.gif'=>'箭头1','arrow_2.gif'=>'箭头2','arrow_3.gif'=>'箭头3','arrow_4.gif'=>'箭头4'
                ,'arrow_5.gif'=>'箭头5',6=>'自定义');
            //return '<select id="'.$params['name'].'" name="'.$params['name'].'" onClick="choosePic()">'._comset_set($params['value'],$params['arrow']).'</select>';
            $t = buildTag($params,'select',false);
            unset($params['name']);
            smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options',$smarty->_current_file,$smarty->_current_line_no, 20, false))),$smarty);
            return $t.smarty_function_html_options($params, $smarty).'</select>';
            break;

        case 'rank':
            $params['selected'] = $params['value'];
            $params['options'] = array('view_w_count'=>'周访问次数','view_count'=>'总访问次数','buy_w_count'=>'周购买次数','buy_count'=>'总购买次数'
                ,'comments_count'=>'评论次数');
            //return '<select id="'.$params['name'].'" name="'.$params['name'].'" onClick="choosePic()">'._comset_set($params['value'],$params['arrow']).'</select>';
            $t = buildTag($params,'select',false);
            unset($params['name']);
            smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options',$smarty->_current_file,$smarty->_current_line_no, 20, false))),$smarty);
            return $t.smarty_function_html_options($params, $smarty).'</select>';
            break;

        case 'com_select':
            $params['from']=intval($params['from']);
            $params['options']=array();
            $params['to']=intval($params['to']);
            for($i=$params['from'];$i<=$params['to'];$i++){
                array_push($params['options'],$i);
            }
            $params['selected'] = $params['value'];
            $t = buildTag($params,'select',false);
            unset($params['name']);
            smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options',$smarty->_current_file,$smarty->_current_line_no, 20, false))),$smarty);
            return $t.smarty_function_html_options($params, $smarty).'</select>';
            break;
        case 'viewIMG':
             return "<a style='text-decoration:none;' class='viewIMG' href='javascript:void(0);' onclick='viewIMG(\"".$params['value']."\",this);this.blur();'  title='点击查看图片'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>";
             break;
        default:
            return buildTag($params,'input autocomplete="off"');
        }
    }
}
function _build_color_options($params){
    $_html = '<option style="background-color:#FFFFFF" value="default">默认<option>';

    foreach($params['color'] as $k=>$v){
        if($k==$params['selected'])
            $_html .= '<option style="background-color:'.$k.'" value="'.$k.'" selected>&nbsp;</option>';
        else
            $_html .= '<option style="background-color:'.$k.'" value="'.$k.'">&nbsp;</option>';
    }
    return $_html;
}


?>
