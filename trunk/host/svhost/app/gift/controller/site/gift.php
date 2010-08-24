<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * ctl_cart
 *
 * @uses b2c_frontpage
 * @package
 * @version $Id: ctl.cart.php 1952 2008-04-25 10:16:07Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @author <kxgsy163@163.com>
 * @license Commercial
 */
class gift_ctl_site_gift extends b2c_frontpage{
    
    
    public function index() {
        $this->begin($this->gen_url(array('app'=>'gift', 'ctl'=>'site_gift', 'act'=>'lists')));
        $id = $this->_request->get_param(0);
        $id = intval($id);
        if($id) {
            $arr_gift_info = $this->app->model('goods')->dump($id);
            if(is_array($arr_gift_info) && !empty($arr_gift_info['goods_id'])) {
                if($arr_gift_info['member_ref'] && isset($arr_gift_info['member_ref']['member_lv_ids'])) {
                    $tmp_member_lv_info = $this->app->model('member_lv')->getList('*', array('member_lv_id'=>explode(',', $arr_gift_info['member_ref']['member_lv_ids'])), 0, -1, 'member_lv_id ASC');
                    $this->pagedata['member_lv'] = $tmp_member_lv_info;
                }
                $this->pagedata['details'] = $arr_gift_info;
                $this->page('site/index.html');
            } else {
                $this->end(false, '赠品信息错误！');
            }
        } else {
            $this->end(false, '该赠品不存在！访问错误！');
        }
        
    }
    
    
    public function lists() {
        $this->begin('index.html');
        $art_list_id = $this->_request->get_param(0);
        $art_list_id = intval($art_list_id);
        $filter = array('marketable'=>'true');
        //$GLOBALS['runtime']['path'] = $aPath;
        
        //title keywords description
        //$this->get_seo_info($info, $aPath);
        
        //每页条数
        $pageLimit = $this->app->getConf('gallery.display.listnum');
        $pageLimit = ($pageLimit ? $pageLimit : 10);
        
        //当前页
        $page = (int)$this->_request->get_param(1);
        
        if($page) {
            $filter['cat_id'] = $art_list_id;
        } else {
            $page = $art_list_id;
        }
        
        
        $page or $page=1;
        
        //总数
        $count = $this->app->model('goods')->count($filter);
        $arr_gift_list = $this->app->model('goods')->getList('*', $filter, $pageLimit*($page-1),$pageLimit);        

        //标识用于生成url
        $token = md5("page{$page}");
        $this->pagedata['pager'] = array(
                'current'=>$page,
                'total'=>ceil($count/$pageLimit),
                'link'=>$this->gen_url(array('app'=>'gift', 'ctl'=>'site_gift', 'act'=>'lists',  'arg2'=>$token)),
                'token'=>$token
            );
        $this->pagedata['gift_list'] = $arr_gift_list;
        $this->page('site/list.html');
    }
    
    
    public function add_to_cart() {
        $gift_id = (int)$this->_request->get_param(0);
        if(($return=kernel::single('gift_cart_object_gift')->add($this->get_data()))!==true) {
            if(!is_array($return)) {
                $this->begin($this->gen_url(array('app'=>'gift', 'ctl'=>'site_gift', 'act'=>'lists')));
                $this->end(false, '赠品不存在！');return;
            }
        } else {
            
            unset($return);
            $return['begin'] = array('app'=>'b2c', 'ctl'=>'site_cart', 'act'=>'index');
            $return['end']   = array('status'=>true,  'msg'=>'加入购物车成功！');
        }
        $this->begin($this->gen_url($return['begin']));
        $this->end($return['end']['status'], $return['end']['msg']);
        
    }
    
    
    
    private function get_data() {
        $gift_id = (int)$this->_request->get_param(0);
        $num = (int)$this->_request->get_param(1);
        return array('gift_id' => $gift_id, 'num' => $num);
    }
    
    
    
    
    
    
}
