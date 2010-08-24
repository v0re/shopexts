<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 购物车项model
 * $ 2010-04-28 20:02 $
 */
class b2c_mdl_cart_objects extends dbeav_model{
    /**
     * 加入购物车 (目前的相法goods,package,coupon) 以后有扩展都以 b2c_cart_add_扩展就行了  有数量的修改也在这里处理
     *
     * @param array  $aData  // $_POST $_GET 等数据
     * @param string $sType  // 类型对应于b2c_cart_apps 里的 b2c_cart_$sType里的add处理
     * @return boolean
     */
    public function add_object($aData,$sType='goods') {
        $obj = kernel::single('b2c_cart_object_'.$sType);
        if(is_object($obj)) {
            $aResult = $obj->add($aData);
            return $aResult;
        } else {
            return false;
        }
        //$this->setCartNum();
        
    }

    /**
     * 获取指定的类型购物车数据 指定$sIdent的数据
     *
     * @param string $sType
     * @param string $sIdent
     * @return array()
     */
    public function get_object($sType = null,$sIdent = null) {
        

        // 清空所有
        if(empty($sType)) {
            $result = array('object'=>array());
            foreach($this->_get_cart_object_apps() as $object){
                $arr = $object->getAll(true);
                if($arr) $result['object'][$object->get_type()] = $arr;
            }
            $flag = $result;
        } else {
            // $sIdent 为空 返回指定类型所有的购物车数据
            $flag = kernel::single('b2c_cart_object_'.$sType)->get($sIdent);
        }
        
        //$this->setCartNum();
        return $flag;
    }

    /**
     * 删除指定的类型购物车数据
     *
     * @param string $sType
     * @param string $sIdent
     */
    public function remove_object($sType='', $sIdent = null) {
        foreach($this->_get_cart_object_apps() as $object){
            if(!is_object($object)) continue;
            if(empty($sType)) {
                $object->delete($sIdent);
                $this->_del_cookie();
                continue;
            }
            
            if(method_exists($object, 'get_type')) {
                $t_type = $object->get_type();
                if(empty($t_type) || $t_type!=$sType) {
                    continue;
                } else {
                    $object->delete($sIdent);
                }
            } else {
                return false;
            }
        }
        //$this->setCartNum();
        return true;

    }

    /**
     * 修改指定$sIdent的数据的数量
     *
     * @param string $sType
     * @param string $sIdent
     * @return array()
     */
    public function update_object($sType='', $sIdent,$quantity) {
        if(empty($sType)) return false;
        
        foreach($this->_get_cart_object_apps() as $object){
            if($object->get_type()==$sType) {
                $status = $object->update($sIdent, $quantity);break;
            }
        }
        //$this->setCartNum();
        return true;
    }
    
    
    private function _get_cart_object_apps() {
        if( !$this->_cart_object_apps ) {
            $this->_cart_object_apps = array();
            foreach(kernel::servicelist('b2c_cart_object_apps') as $object) {
                if(!is_object($object)) continue;
                $this->_cart_object_apps[] = $object;
            }
        }
        return $this->_cart_object_apps;
    }
    /**
     * 将购物车中几种/几个商品数量写入 $_COOKIE['S[CART_COUNT]'] $_COOKIE['S[CART_NUMBER]']
     * @global string document the fact that this function uses $_myvar
     * @staticvar integer $staticvar this is actually what is returned
     * @param string $param1 name to declare
     * @param array $aCart 购物车数组
     * @return bool
     */
    function setCartNum( &$aCart )
    {
        //$sale = &$this->app->Model('sales_sale');
        //$trading = $sale->getCartObject($aCart,$GLOBALS['runtime']['member_lv'],true);
        //$count = count($trading['products'])+count($trading['gift_e'])+count($trading['package']);
        
        if(empty($aCart)) {
            $aCart = $this->get_object();
            //$this->app->model('cart')->count_objects($aCart);
        } 

        $trading = $aCart['object'];
        $number = $count = 0;

        $count = count($trading['goods'])+count($trading['gift_e'])+count($trading['package']);
        
        if($trading['goods'])
            foreach($trading['goods'] as $rows){
                $number += $rows['quantity'];
            }
        if($trading['gift_e'])
            foreach($trading['gift_e'] as $rows){
                $number += $rows['quantity'];
            }
        if($trading['package'])
            foreach($trading['package'] as $rows){
                $number += $rows['quantity'];
            }

        $totalPrice = $aCart['subtotal'] - $aCart['discount_amount'];
        $trading['totalPrice'] = $totalPrice;
        
        $this->_setCookie($count, $number, $totalPrice);
        $arr = $aCart['_cookie'] = array(
                                'CART_COUNT'    =>  $count,
                                'CART_NUMBER'   =>  $number,
                                'CART_TOTAL_PRICE'   =>  $totalPrice,
                                'trading'       =>  $trading,
                            );
        return $arr;
       
    }
    
    public function _setCookie($count=0, $number=0, $totalPrice=0) {
        ob_start();
        if($count!==$_COOKIE['S[CART_COUNT]']){
            setCookie('S[CART_COUNT]',$count, null, '/');
        } else {
            //echo $count, '---', $_COOKIE['S[CART_COUNT]'], '--';
            //echo 'yes';exit;
        }
        if($number!==$_COOKIE['S[CART_NUMBER]']){
            setCookie('S[CART_NUMBER]',$number, null, '/');
        }
        
        setcookie('S[CART_TOTAL_PRICE]', $totalPrice, null, '/');
        ob_end_clean();
    }
    
    
    public function _del_cookie() {
        ob_start();
        setCookie('S[CART_NUMBER]', 0, time()-3600, '/');
        setCookie('S[CART_COUNT]', 0, time()-3600, '/');
        setcookie('S[CART_TOTAL_PRICE]',0, time()-3600, '/');
        ob_end_clean();
    }
    
    
    
    
    public function save( &$data,$mustUpdate = null ){
        $arr_member_info = $this->get_member_info();
        $data['member_id'] = ( $arr_member_info['member_id'] ? $arr_member_info['member_id'] : -1 );
        if( empty($data['obj_ident']) ) return false;
        return parent::save( $data, $mustUpdate );
    }
    
    
    public function getList( $cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null ){
        $arr_member_info = $this->get_member_info();
        
        if( $arr_member_info['member_id'] ) {
            if( is_array($filter) ) {
                $filter['member_id'] = array( $arr_member_info['member_id'], -1 );
            }
        } else {
            $arr_member_info['member_id'] = -1;
            $filter['member_id'] = array($arr_member_info['member_id']);
        }
        
        
        $arr = parent::getList( $cols, $filter, $offset, $limit, $orderType );
        
        $tmp = array();
        if( is_array( $arr ) ) {
            foreach( $arr as $key => $row ) {
                if( isset( $filter['obj_ident'] ) ) {
                    if( $row['member_id']==$arr_member_info['member_id'] ) return array( $row );
                } else {
                    if( isset( $tmp[$row['obj_ident']] ) ) {
                        $tmp[$row['obj_ident']]['quantity'] += $row['quantity'];
                    } else {
                        $tmp[$row['obj_ident']] = $row;
                    }
                }
            }
            
        }
        
        sort( $tmp );
        return $tmp;
    }
    
    
    public function delete( $filter,$subSdf = 'delete' ) { 
        $arr_member_info = $this->get_member_info();
        
        if( $arr_member_info['member_id'] ) {
            $filter['member_id'] = -1;
            $flag = parent::delete( $filter,$subSdf );
            if( !$flag ) return false;
            if( is_array($filter) ) {
                $filter['member_id'] = $arr_member_info['member_id'];
                return parent::delete( $filter,$subSdf );
            }
            return false;
        } else {
            $filter['member_id'] = -1;
            return parent::delete( $filter,$subSdf );
        }
    }
    
    
    private function get_member_info() {
        if( empty( $this->arr_member_info ) ) {
            $this->arr_member_info = kernel::single('b2c_frontpage')->get_current_member();
        }
        return $this->arr_member_info;
    }
    

}
