<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_member_lv extends desktop_controller{

    var $workground = 'b2c_ctl_admin_member';

    function index(){
         
        $this->finder('b2c_mdl_member_lv',array(
            'title'=>'会员等级',
            'actions'=>array(
                            array('label'=>'添加会员等级','href'=>'index.php?app=b2c&ctl=admin_member_lv&act=addnew','target'=>'dialog::{width:680,height:250,title:\'添加会员等级\'}'),
                        )
            ));
    }

    function addnew($member_lv_id=null){

         

            $aLv['default_lv_options'] = array('1'=>__('是'),'0'=>__('否'));
            $aLv['default_lv'] = '0';
            $aLv['lv_type_options'] = array('retail'=>__('普通零售会员等级'),'wholesale'=>__('批发代理会员等级'));
            $aLv['lv_type'] = 'retail';
            $this->pagedata['levelSwitch']= $this->app->getConf('site.level_switch');
            $this->pagedata['lv'] = $aLv;
   
            if($member_lv_id!=null){
                $mem_lv = $this->app->model('member_lv');
                $aLv = $mem_lv->dump($member_lv_id); 
                  $aLv['default_lv_options'] = array('1'=>__('是'),'0'=>__('否'));
              $this->pagedata['lv'] = $aLv;
            }
            
            $this->display('admin/member/lv.html');
    }

    function save(){
        $this->begin('index.php?app=b2c&ctl=admin_member_lv&act=index');
        $objMemLv = $this->app->model('member_lv');
        if($objMemLv->validate($_POST,$msg)){
        if($objMemLv->save($_POST)){
            $this->end(true,__('保存成功'));
        }
        else{
        $this->end(false,__('保存失败'));
           }
    }
    else{
        $this->end(false,$msg);
    }
    
    }

}
