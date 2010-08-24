<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class bdlink_ctl_clink extends desktop_controller {
    
    private $_ident_op = '#r-p';
    
    
    
    public function get( $var ) {
        return $this->$var;
    }
    
    
    public function index() {
        $this->pagedata['ident_op'] = $this->_ident_op;
        
        $this->path[] = array('text'=>__("站外推广链接"));
        if( empty($this->pagedata['arr_link_info']) ) {
            $this->pagedata['arr_link_info']['targetURL'] = rtrim($this->app->base_url(true), '/');
            $this->pagedata['arr_link_info']['validtime'] = 30;
        }

        $this->page('create_link.html');
    }
    
    public function edit() {
        $id = intval( $_GET['id'] );
        if( empty($id) ) {
            $this->begin($this->gen_url( array( 'app'=>'bdlink', 'ctl'=>'clink', 'act'=>'lists' ) ));
            $this->end(false, '不存在！');
        } else {
            $arr = $this->app->model('list')->dump( $id );
            $tmp = explode( $this->_ident_op, $arr['generatecode'] );
            $arr['targetURL'] = $tmp[0];
            $arr['usercode'] = $tmp[1];
            if( $arr['validtime'] )
                $arr['validtime'] = $arr['validtime'];
            
            $this->pagedata['arr_link_info'] = $arr;
            
            $this->index();
        }
    }
    
    
    public function lists() {
        $this->finder('bdlink_mdl_list',array(
            'title'=>'推广链接',
            'actions'=>array(
                            array('label'=>'创建链接','icon'=>'add.gif','href'=>'index.php?app=bdlink&ctl=clink&act=index'),
                        ),//'finder_aliasname'=>'gift_mdl_goods','finder_cols'=>'cat_id',
            ));
    }
    
    public function create_link() {
        $filter = $this->_filter($_POST);
        $id = $this->valid( true );
        if( $id )
            $filter['id'] = $id;
        
        if ( $this->app->model('list')->save( $filter ) ) {
            header('Content-Type:text/jcmd; charset=utf-8');
            echo '{success:"操作成功！",_:null}';
        } else {
            header('Content-Type:text/jcmd; charset=utf-8');
            echo '{success:"操作失败",_:null}';
        }
    }
    
    
    public function valid( $flag=false ) {
        $filter['generatecode'] = $_POST['generatecode'];
        $arr = $this->app->model('list')->getList( '*', $filter );
        $return = empty($arr) ? array('status'=>true) : array('status'=>false, 'msg'=>'记录已存在！保存后将修改原始记录', '_id'=>$arr[0]['id']);
        if( $flag ) return $arr[0]['id'];
        else echo json_encode( $return );
    }
    
    private function _filter( $arr ) {
        
        $filter['time'] = time();
        $filter['user_id'] = kernel::single('desktop_user')->user_id;
        
        if( $arr['generatecode'] )
            $filter['generatecode'] = $arr['generatecode'];
        
        if( $arr['validtime'] ) {
            $filter['validtime'] = $arr['validtime'];
        }
        
        return $filter;
    }
    
}