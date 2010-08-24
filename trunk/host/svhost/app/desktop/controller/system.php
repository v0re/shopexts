<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_system extends desktop_controller{

    var $require_super_op = true;

    function index(){
        echo $this->app->getConf('shopadminVcode');
    }
    
    function service()
        {
           if($_POST){
            $this->app->setConf('shopadminVcode',$_POST['shopamin_vocde']);    
        }
        $services = app::get('base')->model('services');
        $filter = array(
                'content_type'=>'service_category',
                'content_path'=>'select',
                'disabled'=>'true',
            );
        
        $all_category = $services->getList('*', $filter);
        $filter = array(
                'content_type'=>'service',
                'disabled'=>'true',
            );
        $all_services = $services->getList('*', $filter);
        foreach($all_services as $k => $row){
            $vars = get_class_vars($row['content_path']);
            $servicelist[$row['content_name']][$row['content_path']] = $vars['name'];
        }
        $html .= $this->ui()->form_start();
        foreach($all_category as $ik => $item){
             if( $item['content_name'] == 'ectools_regions.ectools_mdl_regions' ){
                unset( $all_category[$ik] );
                continue;
            }
           $current_set = app::get('base')->getConf('service.'.$item['content_name']);
            if(@array_key_exists($item['content_name'],$_POST['service'])){
                if($current_set!=$_POST['service'][$item['content_name']]){
                    $current_set = $_POST['service'][$item['content_name']];
                    app::get('base')->setConf('service.'.$item['content_name'], $current_set);
                }
            }
            $form_input = array(
                    'title'=>$item['content_title'],
                    'type'=>'select',
                    'required'=>true,
                    'name'=>"service[".$item['content_name']."]",
                    'tab'=>$tab,
                    'value'=> $current_set,
                    'options'=>$servicelist[$item['content_name']],
            );
            
            $html.=$this->ui()->form_input($form_input);
        }
        $select = $this->app->getConf('shopadminVcode');
        if($select === 'true'){
             $html .="<tr><th><label>后台登陆启用验证码</label></th><td><select name='shopamin_vocde' type='select' ><option value='true' selected='selected'>是</option><option value='false' >否</option></select></td></tr>";
        }
        else{
             $html .="<tr><th><label>后台登陆启用验证码</lable></th><td>&nbsp;&nbsp;<select name='shopamin_vocde' type='select' ><option value='true'>是</option><option value='false' selected='selected'>否</option></select></td></tr>";
        }
        $html .= $this->ui()->form_end();
        $this->pagedata['_PAGE_CONTENT'] = $html;
        $this->page();
    }

    function licence(){
        $this->sidePanel();
        echo '<iframe width="100%" height="100%" src="'.constant('URL_VIEW_LICENCE').'" ></iframe>';
    }

}

