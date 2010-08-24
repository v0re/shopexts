<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_recycle{
    var $actions;
    function __construct($app){
        $finder_id = $_GET['_finder']['finder_id'];
        $this->app = $app;
        $this->actions  = array(
            array('label'=>'彻底删除','icon'=>'add.gif','confirm'=>'确定删除选中项？删除后将不可恢复','submit'=>'index.php?app=desktop&ctl=recycle&act=recycle_delete'),
            array('label'=>'恢复所选','icon'=>'del.gif','submit'=>'index.php?app=desktop&ctl=recycle&act=recycle_processtype','target'=>'dialog::{title:\'删除所选\',width:400,height:300}'),
            //array('label'=>'恢复所选','icon'=>'del.gif','submit'=>'index.php?app=desktop&ctl=recycle&act=recycle_restore'),
        );
    }

    function detail_basic($id){
        $recycle = app::get('desktop')->model('recycle');
        $row  = $recycle->dump($id);
        $item_sdf = $row['item_sdf'];
        $obj = app::get($row['app_key'])->model($row['item_type']);
        $schema = $obj->get_schema();
        $render = new base_render($this->app);
        foreach($item_sdf as $key=>$value){
            if(!is_array($value)){
                $label = $schema['columns'][$key]['label'];
                $tmp[($label)?$label:$key] = $value;
            }
        }
        $render->pagedata['sdf'] = $tmp;
        $render->display('recycle.html');

 

    }

}
