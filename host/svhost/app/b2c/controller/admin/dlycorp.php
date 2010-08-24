<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_dlycorp extends desktop_controller{

    var $workground = 'b2c_ctl_admin_system';

    function index(){
        $action = array('label'=>'添加物流公司','href'=>'index.php?app=b2c&ctl=admin_dlycorp&act=addnew','target'=>'dialog::{title:\'添加物流公司\',width:500,height:180}');
        $this->finder('b2c_mdl_dlycorp',array('title'=>'物流公司','actions'=>array($action),'use_buildin_new_dialog'=>false,'use_buildin_set_tag'=>false,'use_buildin_recycle'=>false,'use_buildin_export'=>false));
    }

    public function addnew()
    {
        if($_POST)
        {
            $this->begin("index.php?app=b2c&ctl=admin_dlycorp&act=index");
            $dlycorp = $this->app->model('dlycorp');
            $arrdlcorp = $dlycorp->dump(array('name' => trim($_POST['name'])));
            if (!$arrdlcorp)
            {
                if (!$_POST['ordernum'])
                    $_POST['ordernum'] = 50;
                
                $result = $dlycorp->save($_POST);
                $this->end($result, __('物流公司添加成功！'));
            }
            else
            {
                $this->end(false, __('该物流公司已经存在！'));
            }            
        }
        else
        {
            /*$html = $this->ui()->form_start();
            $html .= $this->ui()->form_input(array('title'=>'物流公司名称','name'=>'name', 'vtype' => 'required', 'caution' => '请填写公司名称'));
            $html .= $this->ui()->form_input(array('title'=>'网址','name'=>'website'));
            $html .= $this->ui()->form_input(array('title'=>'询件网址','name'=>'request_url'));
            $html .= $this->ui()->form_end();
            echo $html;*/
            $this->display('admin/delivery/dlycorp_new.html');
        }
    }
    
    public function save()
    {
        if($_POST)
        {
            $this->begin();
            $dlycorp = $this->app->model('dlycorp');            
            if (!$_POST['ordernum'])
                $_POST['ordernum'] = 50;
            $result = $dlycorp->save($_POST);
            $this->end($result, __('物流公司修改成功！'));            
        }
    }
}
