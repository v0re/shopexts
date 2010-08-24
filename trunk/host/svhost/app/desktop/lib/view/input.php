<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_view_input{


    function input_image($params){

        $params['type'] = 'image';
        $ui = new base_component_ui($this);
        $domid = $ui->new_dom_id();
        
        $input_name = $params['name'];
        $input_value = $params['value'];
      
        $image_src = base_storager::image_path($input_value,'s');
        
        
        
        if(!$params['width']){
           $params['width']=50;
        }
        
        if(!$params['height']){
         $params['height']=50;
        }
        
        $imageInputWidth = $params['width']+24;
        $url="&quot;index.php?app=desktop&act=alertpages&goto=".urlencode("index.php?app=image&ctl=admin_manage&act=image_broswer")."&quot;";

        $html = '<div class="image-input clearfix" style="width:'.$imageInputWidth.'px;" gid="'.$domid.'">';
            $html.= '<div class="flt"><div class="image-input-view" style="display:table-cell;text-align:center;vertical-align: middle;width:';
            $html.=  $params['width'].'px;height:'.$params['height'].'px;font-size:'.($params['height']*0.875).'px;overflow:hidden;">';
            $html.= '<img src="'.$image_src.'" onload="$(this).zoomImg('.$params['width'].','.$params['height'].');"/>';
            $html.= '</div></div>';
            $html.= '<div class="image-input-handle" onclick="new imgDialog('.$url.',{handle:this});" style="width:20px;height:'.$params['height'].'px;">选择'.$ui->img(array('src'=>'bundle/arrow-down.gif','app'=>'desktop'));
            $html.= '</div>';
            $html.= '<input type="hidden" name="'.$input_name.'" value="'.$input_value.'"/>';
            $html.= '</div>';
            
        
        
        return $html;
    }

    function input_object($params){
        $params['breakpoint'] = $params['breakpoint']?$params['breakpoint']:20;

        $object = $params['object'];
        if(strpos($params['object'],'@')!==false){
            list($object,$app_id) = explode('@',$params['object']);
        }elseif($params['app']){
            $app_id = $params['app'];
        }else{
            $app_id = $this->app->app_id;
        }

        $app = app::get($app_id);        
        $o = $app->model($object);
        $render = new base_render(app::get('desktop'));
        $ui = new base_component_ui($app);


        $dbschema = $o->get_schema();

        $params['app_id'] = $app_id;

        if(isset($params['filter'])){
            if(!is_array($params['filter'])){
                parse_str($params['filter'],$params['filter']);
            }
        }

        $params['domid'] = substr(md5(uniqid()),0,6);

        $key = $params['key']?$params['key']:$dbschema['idColumn'];
        $textcol = $params['textcol']?$params['textcol']:$dbschema['textColumn'];


        $tmp_filter = $params['filter']?$params['filter']:null;
        $count = $o->count($tmp_filter);
        if($count<=$params['breakpoint']&&!$params['multiple']&&$params['select']!='checkbox'){
            if(strpos($textcol,'@')===false){
                $list = $o->getList($key.','.$textcol,$tmp_filter);
                foreach($list as $row){
                    $type[$row[$key]] = $row[$textcol];
                }
                
            }else{
                list($name,$table,$app_id) = explode('@',$textcol);
                $app = $app_id?app::get($app_id):$app;
                $mdl = $app->model($table);
                $list = $o->getList($key,$tmp_filter);
                foreach($list as $row){
                    $tmp_row = $mdl->getList($name,array($mdl->idColumn=>$row[$key]),0,1);
                    $type[$row[$key]] = $tmp_row[0][$name];
                }

            }
            $tmp_params['name'] = $params['name'];
            $tmp_params['type'] = $type;
            $str_filter = $ui->input($tmp_params);
            unset($tmp_params);
            return $str_filter;

        }

        $params['idcol'] = $keycol['keycol'] = $key;
        $params['textcol'] = $textcol;
        if($params['value']){
            if(strpos($params['view'],':')!==false){
                list($view_app,$view) = explode(':',$params['view']);
                $params['view_app'] = $view_app;
                $params['view'] = $view;
            }
            if(is_string($params['value'])){
                $params['value'] = explode(',',$params['value']);
            }
            $params['items'] = &$o->getList('*',array($key=>$params['value']),0,-1);
        }

        if(isset($params['multiple']) && $params['multiple']){
            $render->pagedata['_input'] = $params;
            return $render->fetch('finder/input.html');
        }else{
            if($params['value']){
                $string = $params['items'][0][$textcol];
            }else{
                $string = $params['emptytext']?$params['emptytext']:'请选择...';
            }

            unset($params['app']);

            if($params['data']){
                $_params = (array)$params['data'];
                unset($params['data']);
                $params = array_merge($params,$_params);
            }

            if($params['select']=='checkbox'){
                $params['type'] = 'checkbox';
            }else{
                $id = "handle_".$params['domid'];
                $params['type'] = 'radio';
                $getdata = '&singleselect=radio';
            }
            if(is_array($params['items'])){
                foreach($params['items'] as $key=>$item){
                    $items[$key] = $item[$params['idcol']];
                }
            }
            $vars = $params;
            $vars['items'] = $items;
            
            $object = http_build_query($vars);

            $url = 'index.php?app=desktop&act=alertpages&goto='.urlencode('index.php?app=desktop&ctl=editor&act=finder_common&app_id='.$app_id.'&'.$object.$getdata);
            
            $render->pagedata['string'] = $string;
            $render->pagedata['url'] = $url;
            $render->pagedata['id'] = $id;
            $render->pagedata['params'] = $params;
            $render->pagedata['object'] = $object;
            return $render->fetch('finder/input_radio.html');
        }
    }
    function input_html($params){
        $id = 'mce_'.substr(md5(rand(0,time())),0,6);
        $editor_type=app::get('desktop')->getConf("system.editortype");
        $editor_type==''?$editor_type='wysiwyg':$editor_type='wysiwyg';
        $includeBase=$params['includeBase']?$params['includeBase']:true;
        $params['id']=$id;

        $img_src = app::get('desktop')->res_url;
        $render = new base_render(app::get('desktop'));
        $render->pagedata['id'] = $id;
        $render->pagedata['img_src'] = $img_src;
        $render->pagedata['includeBase'] = $includeBase;
        $render->pagedata['params'] = $params;
        
        $style2=$render->fetch('editor/html_style2.html');

        if($editor_type =='textarea'||$params['editor_type']=='textarea'){
            $html=$style2;
        }else{
            $style1 = $render->fetch('editor/html_style1.html');
            $html=$style1;
            $html.=$style2;
        }
        return $html;
    }
}
