<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */

class site_ctl_admin_route_static extends site_admin_controller
{

    function __construct(&$app) 
    {
        parent::__construct($app);
    }//End Function

    public function index() 
    {
        $this->finder('site_mdl_route_statics', array(
            'title' => '静态路由表',
            'base_filter' => array(),
            'actions'=>array(
                array(
                    'label' => '添加规则', 
                    'href' => 'index.php?app=site&ctl=admin_route_static&act=add', 
                    'target' => 'dialog::{frameable:true, title:\'添加规则\', width:500, height:190}',
                ),
            ),
        ));
    }//End Function

    public function add() 
    {
        $this->pagedata['close_win'] = 1;
        $this->page('admin/route/static/edit.html');
    }//End Function

    public function save() 
    {
        $statics = $this->_request->get_post('statics');
        $this->begin();
        if($row = app::get('site')->model('route_statics')->has_static($statics['static'])){
            if($row['id']!=$statics['id']) $this->end(false, '静态规则已经存在');
        }
        if($row = app::get('site')->model('route_statics')->has_url($statics['url'])){
            if($row['id']!=$statics['id']) $this->end(false, '目标链接已经存在');
        }
        if($statics['id'] > 0){
            $id = $statics['id'];
            unset($statics['id']);
            if(app::get('site')->model('route_statics')->update($statics, array('id'=>$id))){
                $this->end(true, '保存成功');
            }else{
                $this->end(false, '保存失败');
            }
        }else{
            if(app::get('site')->model('route_statics')->insert($statics)){
                $this->end(true, '添加成功');
            }else{
                $this->end(false, '添加失败');
            }
        }
    }//End Function

}//End Class