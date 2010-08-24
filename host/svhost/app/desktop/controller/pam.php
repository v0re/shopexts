<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_pam extends desktop_controller{

    function index(){
        $this->finder('desktop_mdl_pam',array(
            'title'=>'通行证管理',
            'actions'=>array(
             ),                                   
            ));
    }
    
        
    function setting($passport){
        $passport_model =new $passport;
        if($_POST){
            $this->begin('index.php?app=desktop&ctl=pam&act=index');
            $passport_model->set_config($_POST);
            $this->end(true,__('配置成功'));
        }
        #$html = $this->ui()->form_start();
        $len = strlen($html);
        foreach($passport_model->get_config() as $name=>$config){
            if($config['editable'] == 'false' || (isset($config['editable']) && !$config['editable'])) continue;
            $input['name'] = $name;
            $input['title'] = $config['label'];
            $input['type'] = $config['type'];
            if($config['options']){
                $input['options'] = $config['options'];
            }
            if($config['value']){
               $input['value'] = $config['value']; 
            }
            $html .= $this->ui()->form_input($input);
            unset($input);
        }
        if($len == strlen($html)){
            echo __('这个通行证不需要配置');         
        }else{
            #$html .= $this->ui()->form_end();
            $this->pagedata['html'] = $html;
            $this->pagedata['passport'] = $passport;
            $this->page('pam.html');
        }
    }

}
