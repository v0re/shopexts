<?php

/*
 * @package site
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license
 */
class site_ctl_admin_seo extends site_admin_controller
{
    /*
     * workground
     * @var string
     */
    var $workground = 'seo_ctl_admin_seo';

    /*
     * �б�
     * @public
     */
    public function index(){
        $this->finder('site_mdl_seo', array(
            'title' => 'SEO网店优化',
            'base_filter' => array(),
            'use_buildin_set_tag' => false,
            'use_buildin_recycle' => false,
            'use_buildin_export' => false,
            'actions'=>array(
                array(
                    'label' => 'SEO默认配置',
                    'href' => 'index.php?app=site&ctl=admin_seo&act=setDefautSeo',
                    'target' => 'dialog::{frameable:true, title:\'SEO默认配置\', width:600, height:400}',
                ),
            ),

        ));

    }

    function setDefautSeo(){
        $seo = $this->app->model('seo')->dump(array('app'=>'site','ctl'=>'default'),'id,config,param');
        if(is_string($seo['param'])){
            $seo['param'] = unserialize($seo['param']);
        }
        if(is_string($seo['config'])){
            $seo['config'] = unserialize($seo['config']);
        }
        $render = $this->app->render();
        $render->pagedata['id'] = $seo['id'];
        $render->pagedata['param'] = $seo['param'];
        $render->pagedata['config'] = $seo['config'];//print_R($seo);exit;
        return $this->page('admin/seo/base.html');
    }


    public function saveseo($id){
        //$this->begin('index.php?app=site&ctl=admin_module&act=index');
        $data['param'] = serialize($_POST);
        if($id > 0){
            if(app::get('site')->model('seo')->update($data, array('id'=>$id))){
                $this->end(true, __('保存成功'));
            }else{
                $this->end(false, __('保存失败'));
            }
        }else{
            if(app::get('site')->model('seo')->insert($data)){
                $this->end(true, __('添加成功'));
            }else{
                $this->end(false, __('添加失败'));
            }
        }
    }
}//End Class
