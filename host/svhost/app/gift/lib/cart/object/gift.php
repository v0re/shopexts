<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
    
    
    class gift_cart_object_gift {
        private $member_ident; // 用户标识
        private $max_store = 999999999;
        
        public function __construct() {
            $this->app = app::get('b2c');
            
            $this->arr_member_info = kernel::single('b2c_frontpage')->get_current_member();
            $this->member_ident = kernel::single("base_session")->sess_id();
            
            $this->oCartObjects = $this->app->model('cart_objects');
            
            $o_ctl = app::get('gift')->controller('site_gift');
            $this->o_p_ctl = kernel::single(get_parent_class($o_ctl));
            
            $this->o_gift = kernel::single('gift_mdl_goods');
            
        }
        
        public function get_type() {
            return 'gift';
        }
    
    
        public function add($data=array()) {
            if(empty($data)) return false;
            $objIdent = $this->_generateIdent($data);
            $filter = $aSave = array(
                   'obj_ident'    => $objIdent,
                   'member_ident' => $this->member_ident,
                   'obj_type'     => 'gift',
                 );
            $aSave['params']       = array(
                                        'gift_id'   =>  $data['gift_id'],
                                        //'product_id' => $data['product_id'],
                                        );
            $aSave['quantity']     = $data['num'];
            if ($aData = $this->oCartObjects->getList('*', $filter, 0, -1, -1)){
                $aSave['quantity'] += $aData[0]['quantity'];
            }
            
            if(($flag=$this->_check($aSave))!==true) return $flag;
            $flag = $this->oCartObjects->save($aSave);
            
            return $flag ? true : false;
        }
        
        
        private function _generateIdent($data) {
            return "gift_".$data['gift_id'];#.'_'. ( $this->arr_member_info['member_id'] ? $this->arr_member_info['member_id'] : 0 );
        }
        
        
        

    public function update($sIdent='', $quantity=0) {
        if( empty($sIdent) || empty($quantity) ) return false;
        $arr_data = array(
                        'obj_ident' => $sIdent,
                        'member_ident' => $this->member_ident,
                        'obj_type' => 'gift',
                        'quantity' => $quantity,
                    );
        if(($flag=$this->_check($arr_data))!==true) return $flag['end'];
        
        $flag = $this->oCartObjects->save($arr_data);
        return $flag ? true : false;
    }

    public function get($sIdent = null,$rich = false) {
        if(empty($sIdent)) return $this->getAll($rich);
        
        $aResult = $this->oCartObjects->getList('*',array(
                                           'obj_ident' => $sIdent,
                                           'member_ident'=> $this->member_ident,
                                        ));
        if(empty($aResult)) return array();
        if($rich) {
            $aResult = $this->_get($aResult);
            $aResult = $aResult[0];
        }
        
        return $aResult;
    }
    
    
    private function _get_gift_info( $aData ) {
        foreach($aData as $_key => $row) {
            $params = $row['params'];
            if(!isset($params['gift_id']) || empty($params['gift_id'])) continue;
            $arr_gift_id[] = $params['gift_id'];
        }
        if( $arr_gift_id ) {
            return kernel::single("gift_cart_object_goods")->_get_products($arr_gift_id);
        }
        return array();
    }
    public function _get($aData){

        $aResult['cart'] = array();
        $arr_gift = $this->_get_gift_info( $aData );
        if( !isset($this->arr_member_info) ) {
            $this->arr_member_info = $this->o_p_ctl->get_current_member();
        }
        $arr_member_info = $this->arr_member_info;
            
        foreach($aData as $_key => $row) {
            
            $params = $row['params'];
            if(!isset($params['gift_id']) || empty($params['gift_id'])) continue;
            
            $info[intval($params['gift_id'])] =  $arr_gift[intval($params['gift_id'])];
            
            if( empty($arr_member_info) ) {
                unset($aData[$key]);
                continue;
            }
            if( is_array($info) ) {
                foreach( $info as &$_gift ) {
                    if( empty($_gift['params']['consume_score']) ) continue;
                    $store = floor($arr_member_info['point'] / $_gift['params']['consume_score']);
                    !empty($_gift['params']['max_store']) or $_gift['params']['max_store'] = $this->max_store;
                    if( $store < $_gift['params']['max_store'] ) $_gift['params']['real'] = $store;
                    else $_gift['params']['real'] = $_gift['params']['max_store'];
                }
            }
            if( empty($info) ) continue;

            $aResult['cart'][] = array(
                            'obj_ident' => $row['obj_ident'],
                            'obj_type' => 'gift',
                            'quantity' => $row['quantity'],
                            'gift_id'=> $params['gift_id'],
                            'info' => $info[$params['gift_id']],
                        );
        }

        return $aResult;
    }

    public function getAll($rich = false) {
        $aResult = $this->oCartObjects->getList('*',array(
                                           'obj_type' => 'gift',
                                           'member_ident'=> $this->member_ident,
                                       ));

        if(empty($aResult)) return array();

        if(!$rich) return $aResult;
        return $this->_get($aResult);
    }
    
    public function delete($sIdent = null) {
        if(empty($sIdent)) return $this->deleteAll();
        return $this->oCartObjects->delete(array('obj_ident'=>$sIdent, 'obj_type'=>'gift', 'member_ident'=> $this->member_ident));
    }


    public function deleteAll() {
        return $this->oCartObjects->delete(array( 'obj_type'=>'gift', 'member_ident'=> $this->member_ident));
    }


    public function count(&$aData) {
    	
        if(isset($aData['object']) && isset($aData['object']['gift']['cart'])) {
            foreach($aData['object']['gift']['cart'] as $row) {
                $aData['subtotal_consume_score'] += $row['info']['params']['consume_score'];
            }
        }
    }


    
    
    private function _check( &$data ) {
        if(empty($this->member_ident)) return false;
        $params = $data['params'];
        if( !isset($data['params']) || empty($data['params']) ) {
            $filter['obj_ident'] = $data['obj_ident'];
            $filter['member_ident'] = $data['member_ident'];
            $filter['obj_type'] = $data['obj_type'];
            $tmp_arr = $this->oCartObjects->getList('*', $filter);
            $params = $tmp_arr[0]['params'];
        }

        if(empty($params['gift_id'])) return false;
        
        $gift_id = $params['gift_id'];
        
        if( !$this->arr_member_info ) {
            $this->arr_member_info = $this->o_p_ctl->get_current_member();
        }
        $arr_member_info = $this->arr_member_info;

        if($arr_member_info['member_id']) {
            if($gift_id) {
                $arr_gift_info = $this->o_gift->getList('*', array('goods_id'=>$gift_id));
                $arr_gift_info = $arr_gift_info[0];
                
				if( empty($arr_gift_info['store']) && $arr_gift_info['store'] !==0 ) $arr_gift_info['store']  = $this->max_store;
				$arr_gift_info['store'] = $arr_gift_info['store'] - $arr_gift_info['params']['freez'];
                if( $data['quantity'] <= $arr_gift_info['store'] ) {
                    if( empty($arr_gift_info['params']['max_store']) ) $arr_gift_info['params']['max_store'] = $this->max_store;
                    if($data['quantity']>$arr_gift_info['params']['max_store']) $data['quantity']=$arr_gift_info['params']['max_store'];
                    $arr_member_ref = kernel::single('gift_mdl_member_ref')->dump($gift_id);
                    if($arr_member_info) {
                        if(in_array($arr_member_info['member_lv'], explode(',', $arr_member_ref['member_lv_ids']))) {
                            $num = (int)$data['quantity'];
                            $num = $num ? $num : 1;
                            if(isset($arr_gift_info['params']['consume_score']) && (($arr_gift_info['params']['consume_score']*$num)>$arr_member_info['point'])) {
                                if($tmp_arr[0]) $data['quantity'] = $tmp_arr[0]['quantity'];
                                $return['begin'] = array('app'=>'gift', 'ctl'=>'site_gift', 'act'=>'index', 'arg0'=>$gift_id);
                                $return['end']   = array('status'=>false,  'msg'=>'积分不足！加入购物车失败!');
                            } else {
                                return true;
                            }
                        } else {
                             $return['begin'] = array('app'=>'gift', 'ctl'=>'site_gift', 'act'=>'index', 'arg0'=>$gift_id);
                             $return['end']   = array('status'=>false, 'msg'=>'加入购物车失败！您所在会员类型不能兑换');
                        }
                    } else {
                         $return['begin'] = array('app'=>'gift', 'ctl'=>'site_gift', 'act'=>'index', 'arg0'=>$gift_id);
                         $return['end']   = array('status'=>false,  'msg'=>'加入购物车失败！');
                    }
                } else {
                    $return['begin'] = array('app'=>'gift', 'ctl'=>'site_gift', 'act'=>'index', 'arg0'=>$gift_id);
                    $return['end']   = array('status'=>false,  'msg'=>'加入购物车失败！赠品数量不足@！');
                }
            } else {
                $return['begin'] = array('app'=>'gift', 'ctl'=>'site_gift', 'act'=>'lists');
                $return['end']   = array('status'=>false, 'msg'=>'赠品不存在！');
            }
        } else {
            $return['begin'] = array('app'=>'gift', 'ctl'=>'site_gift', 'act'=>'lists');
            $return['end']   = array('status'=>false, 'msg'=>'赠品只限会员积分兑换！！');
        }
        $return['end']['quantity'] = $data['quantity'];
        
        return $return;
    }




}