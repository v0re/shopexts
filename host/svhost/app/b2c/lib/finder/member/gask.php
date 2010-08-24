<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_finder_member_gask{    
    function __construct(&$app){
        $this->app=$app;
        $this->ui = new base_component_ui($this);
    }    
    
    var $detail_basic = 'Ϣ';
    function detail_basic($comment_id){ 
        $app = app::get('b2c');
        $mem_com = $app->model('member_comments');
        $mem_com->set_admin_readed($comment_id);
        $goods = $app->model('goods');
        $row = $mem_com->getList('*',array('comment_id' => $comment_id));
        $gask_data = $row[0];
        $reply_data = $mem_com->getList('*',array('for_comment_id' => $comment_id)); 
        $row = $goods->dump($gask_data['type_id']);
        $gask_data['goodname'] = $row['name'];
        $render = $app->render();
        if($row){
            $render->pagedata['url'] = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_product','full'=>1,'act'=>'index','arg'=>$gask_data['type_id']));
            $render->pagedata['goods'] = $row;
        }
        $render->pagedata['comment'] = $gask_data;
        $render->pagedata['reply'] = $reply_data;
        $render->pagedata['object_type'] = $mem_com->type;
        $imageDefault = app::get('image')->getConf('image.set');
        $render->pagedata['defaultImage'] = $imageDefault['S']['default_image'];
        return $render->fetch('admin/member/'.$mem_com->type.'.html');
    }  
    
}
