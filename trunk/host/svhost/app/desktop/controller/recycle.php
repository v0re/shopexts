<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_recycle extends desktop_controller{
    
    var $workground = 'desktop_ctl_recycle';
    function index(){
        $this->finder('desktop_mdl_recycle',array(
            'title'=>'回收站',
            'use_buildin_recycle'=>false,'use_buildin_filter'=>true,
            ));
    }
    function recycle_show($item_type=null,$page=1){
        $filter = array();
        $pagelimit = 20;
        if($item_type&&$item_type!='_ALL_') $filter['item_type'] = $item_type;
        $o = $this->app->model('recycle');
        $this->pagedata['item_type'] = $o->get_item_type();
        $count = $o->count(null,$filter,0,-1);
        $this->pagedata['items'] = $o->getList('*',$filter,$pagelimit*($page-1),$pagelimit);
        $this->pagedata['pager'] = array(
            'current'=>$page,
            'total'=>ceil($count/$pagelimit),
            'link'=>'index.php?app=desktop&ctl=recycle&act=recycle_show&p[0]='.$item_type.'&p[1]=%s',
            //'link'=>'index.php?ctl=default&act=recycle&p[0]='.$item_type.'p[1]='.$page,
            'token'=>time()
            );
        $this->display('common/recycle.html');
    }
    function recycle_processtype(){
        $this->pagedata['options'] = array('add'=>__('新记录加后缀后新增'),'none'=>__('不做处理'));
        $this->display('common/recycle_processtype.html');
    }
    function recycle_restore(){
        $this->begin('javascript:finderGroup["'.$_GET['finder_id'].'"].unselectAll();finderGroup["'.$_GET['finder_id'].'"].refresh();');
        $oRecycle = $this->app->model('recycle');
        $rows = $oRecycle->getList('*',array('item_id'=>$_POST['item_id']),0,-1);
        $restore_type = $_POST['restore_type']?$_POST['restore_type']:'cover';
        foreach($rows as $row){
            $object_name = $row['item_type'];
            $app_key = $row['app_key'];
            $data = ($row['item_sdf']);
            $app = app::get($app_key);
            $o = $app->model($object_name);
            if($o->count(array($o->schema['idColumn']=>$data[$o->schema['idColumn']]))){
                unset($data[$o->schema['idColumn']]);
            }
            
            if(method_exists($o, 'pre_restore')){
                if(!$o->pre_restore($data,$restore_type)){
                    $this->end(false,'恢复失败');
    
                    return false;
                }
            }
            if($data['need_delete'] || !method_exists($o, 'pre_restore')){
                if($o->save($data)){
                    $oRecycle->delete(array('item_id'=>$row['item_id']));
                }
            }
            if(method_exists($o, 'suf_restore')){
                if(!$o->suf_restore($data)){
                    $this->end(false,'恢复失败');

                    return false;
                }
            }

            unset($data[$o->idColumn]);
        }
        $this->end(true,'恢复成功');

    }
    function recycle_delete(){
        $this->begin('javascript:finderGroup["'.$_GET['finder_id'].'"].unselectAll();finderGroup["'.$_GET['finder_id'].'"].refresh();');
        $o = $this->app->model('recycle');
        $rows = $o->getList('*',array('item_id'=>$_POST['item_id']),0,-1);
        foreach($rows as $row){
            $object_name = $row['item_type'];
            $app_key = $row['app_key'];
            $object_full_name = $row['app_key'].'_mdl_'.$row['item_type'];
            $data = ($row['item_sdf']);
            $app = app::get($app_key);
            $obj = $app->model($object_name);

            if(method_exists($obj, 'pre_delete')){
                if(!$obj->pre_delete($data)){
                    $this->end(false,'删除失败');
                    return false;
                }
            }

            $o->delete(array('item_id'=>$row['item_id']));

            if(method_exists($obj, 'suf_delete')){
                if(!$obj->suf_delete($data[$obj->schema['idColumn']])){
                $this->end(false,'删除失败');
                return false;
                }
            }
        }
        $this->end(true,'删除成功');
    }
}
