<?php
function smarty_function_input($params, &$smarty){

    if(isset($params['attributes'])){
        $params = $params['attributes'];
    }

    $params['class'] = $params['class']?$params['class'].' _x_ipt':'_x_ipt'.' '.$params['type'];
    $params['vtype'] = isset($params['vtype'])?$params['vtype']:$params['type'];
    if(isset($params['default']) && !$params['value']){
        $params['value'] = $params['default'];
    }
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
        $params['value'] = mydate('Y-m-d',$params['value']);
        $params['type'] = 'text';
        return buildTag($params,'input autocomplete="off"').'<script>$("'.$domid.'").makeCalable();</script>';
        break;

    case 'time':
        $params['value'] = mydate('Y-m-d H:i',$params['value']);
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
                $html = '<img src="'.$url.'" />';
            }else{
                $html = $url;
            }
        }else{
            $html='';
        }
        return buildTag($params,'input autocomplete="off"').$html;
        break;

    case 'bool':
        $params['type'] = 'checkbox';
        if($value=='true')$params['checked']='checked';
        $params['value'] = "true";
        return buildTag($params,'input');
        break;

    case 'combox':
        return buildTag($params,'input autocomplete="off"');
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
        smarty_core_load_plugins(array('plugins' => array(array('function', 'html_radios',$smarty->_current_file,$smarty->_current_line_no, 20, false))),$smarty);
        return smarty_function_html_radios($params, $smarty);
        break;

    case 'select':
        if(isset($params['rows'])){
            foreach($params['rows'] as $item){
                $params['options'][$item[$params['valueColumn']]] = $item[$params['labelColumn']];
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

    case 'color':
        $params['selected'] = ($params['value']=='')?'#000000':$params['value'];
        $params['style'] = 'width:50px;background-color:'.$params['selected'];
        $params['onChange'] = "this.style.backgroundColor=this.options[this.selectedIndex].value;";
        $t = buildTag($params,'select',false);
        unset($params['name']);
        $params['color'] = array('#00ffff'=>'&nbsp;','#000000'=>'&nbsp;','#ff00ff'=>'&nbsp;','#800000'=>'&nbsp;',
                '#008000'=>'&nbsp;','#00ff00'=>'&nbsp;','#800000'=>'&nbsp;','#000080'=>'&nbsp;','#808000'=>'&nbsp;',
                    '#800080'=>'&nbsp;','#ff0000'=>'&nbsp;','#c0c0c0'=>'&nbsp;','#008080'=>'&nbsp;',
                    '#ffffff'=>'&nbsp;','#ffff00'=>'&nbsp;','#0000ff'=>'&nbsp;');
        return $t._build_color_options($params).'</select>';
        break;

    case 'region':

        $SYSTEM = &$GLOBALS['system'];
        $loc = &$SYSTEM->loadModel('system/local');
        if($params['required'] == 'true'){
            $req = ' vtype="area"';
        }else{
            $req = ' vtype='.$params['vtype'];
        }
    
        if(!$params['value']){
            $package = $params['package']?$params['package']:$SYSTEM->getConf('system.location');
            return '<span package="'.$package.'" class="span _x_ipt"'.$req.'><input '. ( $params['id']?' id="'.$params['id'].'"  ':'' ) .' type="hidden" name="'.$params['name'].'" />'.$loc->get_area_select(null,$params).'</span>';
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
                        $notice = "-";
                        $data = $loc->get_area_select($region['p_region_id'],$params,$region['region_id']);
                        if(!$data){
                            $notice = "";
                        }
                        $ret = '<span class="x-region-child">&nbsp;'.$notice.'&nbsp'.$loc->get_area_select($region['p_region_id'],$params,$region['region_id']).$ret.'</span>';
                    }else{
                        $ret = '<span package="'.$package.'" class="span _x_ipt"'.$req.'><input type="hidden" value="'.$params['value'].'" name="'.$params['name'].'" />'.$loc->get_area_select(null,$params,$region['region_id']).$ret.'</span>';
                    }
                }
                if(!$ret){
                    $ret = '<span package="'.$package.'" class="span _x_ipt"'.$req.'><input type="hidden" value="" name="'.$params['name'].'" />'.$loc->get_area_select(null,$params,$region['region_id']).'</span>';
                }
                return $ret;
            }
        }
        break;

    case 'fontset':
        $params['options'] = array('0'=>'','1'=>'粗体','2'=>'斜体','3'=>'中线');
        $params['selected'] = $params['value'];
        $t = buildTag($params,'select',false);
        unset($params['name']);
        smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options',$smarty->_current_file,$smarty->_current_line_no, 20, false))),$smarty);
        return $t.smarty_function_html_options($params, $smarty).'</select>';
        //return '<select id="'.$params['name'].'" name="'.$params['name'].'">'._comset_set($params['value'],$params['font']).'</select>';
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
    default:
        return buildTag($params,'input autocomplete="off"');
}
}
function _build_color_options($params){
    $_html = '';
    foreach($params['color'] as $k=>$v){
        if($k==$params['selected'])
            $_html .= '<option style="background-color:'.$k.'" value="'.$k.'" selected>'.$v.'</option>';
        else
            $_html .= '<option style="background-color:'.$k.'" value="'.$k.'">'.$v.'</option>';
    }
    return $_html;
}
?>
