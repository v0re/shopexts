<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_queue extends desktop_controller{

    var $workground = 'desktop_ctl_system';

    function index(){
        $params = array(
            'title'=>'队列管理',
            'actions'=>array(
                array('label'=>'全部启动','submit'=>'index.php?app=desktop&ctl=queue&act=run'),
                array('label'=>'全部暂停','submit'=>'index.php?app=desktop&ctl=queue&act=pause'),
                ),
            );
        $this->finder('base_mdl_queue',$params);
    }

    function run(){
        $this->begin('index.php?app=desktop&ctl=queue&act=index');
        $queue_model = app::get('base')->model('queue');
        foreach((array)$_POST['queue_id'] as $id){
            $item['queue_id'] = $id;
            $item['status'] = 'hibernate';
            $queue_model->save($item);        
        }
        $queue_model->flush();
        $this->end(true,__('启动成功'));
    }
    
    function pause(){
        $this->begin('index.php?app=desktop&ctl=queue&act=index');
        $queue_model = app::get('base')->model('queue');
        foreach((array)$_POST['queue_id'] as $id){
            $item['queue_id'] = $id;
            $item['status'] = 'paused';
            $queue_model->save($item);
        }
        $this->end(true,__('暂停成功'));
    }


}
