<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 购物车项处理(商品)
 * $ 2010-04-28 19:46 $
 */
class b2c_cart_object_goods implements b2c_interface_cart_object{

    private $app;
    private $member_ident; // 用户标识
    private $oCartObject;
    private $no_database;
    private $no_database_cart_object;
    
    
    
    /**
     * 构造函数
     *
     * @param $object $app  // service 调用必须的
     */
    public function __construct() {
        $this->app = app::get('b2c');
        
        $this->arr_member_info = kernel::single('b2c_frontpage')->get_current_member();
        $this->member_ident = kernel::single("base_session")->sess_id();
        
        $this->oCartObjects = $this->app->model('cart_objects');
        
        $this->o_goods = $this->app->model('goods');
        $this->o_products = $this->app->model('products');
        
        if( !empty($this->arr_member_info) ) {
            $oMemeberLV = $this->app->model('member_lv');
            $aMLV = $oMemeberLV->dump(array('member_lv_id'=>$this->arr_member_info['member_lv']));
            $this->discout = (empty($aMLV['dis_count']) || $aMLV['dis_count'] > 1 || $aMLV['dis_count'] <= 0)? 1 : $aMLV['dis_count'];
        }
        
        $this->db = kernel::database();
    }


    public function get_type() {
        return 'goods';
    }
    
    
    /**
     * 添加购物车项(goods)
     *
     * @param array $aData // array(
     *                          'goods_id'=>'xxxx',   // 商品编号
     *                          'product_id'=>'xxxx', // 货品编号
     *                          'adjunct'=>'xxxx',    // 配件信息
     *                          'num'=>'xxxx',   // 购买数量
     *                        )
     * @param boolean $append // 是否是追加
     * @return array //
     */
    private function _add($aSave,$append = true) {
        
        // 追加|更新
        if($append) {
            // 如果存在相同商品 则追加
            $filter = array(
                        'obj_ident' => $aSave['obj_ident'],
                        'member_ident' => $this->member_ident,
            );

            if ($aData = $this->oCartObjects->getList('*', $filter, 0, -1, -1)){
                $aSave['quantity'] += $aData[0]['quantity'];
                if( is_array($aData[0]['params']['adjunct']) ) {
                    foreach($aData[0]['params']['adjunct'] as $g_id => $row) {
                        if(!isset($aSave['params']['adjunct'][$g_id])) {
                            $aSave['params']['adjunct'][$g_id] = $row;
                        } elseif ( isset($aSave['params']['adjunct'][$g_id]['adjunct']) && is_array($aSave['params']['adjunct'][$g_id]['adjunct']) ) {
                            foreach($aSave['params']['adjunct'][$g_id]['adjunct'] as $p_id => &$s_v) {
                                $s_v += $aData[0]['params']['adjunct'][$g_id]['adjunct'][$p_id];
                            }
                        }
                    }
                }
            }
        }

        if(!$this->_check($aSave)) return false;

        $this->oCartObjects->save($aSave);
        return $aSave;
    }


    public function no_database($status=false, $arr_goods=array(), $member_ident='') {
        $this->no_database = $status;
        $this->member_ident = $member_ident;
        $this->set_cart_object($arr_goods);
    }
    
    public function get_cart_status() {
        return $this->no_database;
    }
    
    
    public function set_cart_object($aData = array()) {
        if(empty($aData) || !is_array($aData)) return false;
        foreach($aData as $key => $row) {
            if(!is_array($row))continue;
            foreach($row as $val) {
                kernel::single('b2c_mdl_cart_objects')->add_object($val, $key);
            }
        }
        return;
        if(isset($aData['coupon']) && !empty($aData['coupon'])) {
            if(is_array($aData['coupon'])) {
                foreach($aData['coupon'] as $row) {
                    kernel::single("b2c_cart_object_coupon")->add(array($row));
                }
            } else {
                kernel::single("b2c_cart_object_coupon")->add($row);
            }
        }
    }
    
    
    public function add($aData) {
        $aData = $aData['goods'];
        // 商品在购物车中的标识
        $objIdent = $this->_generateIdent($aData);

        $aSave = array(
                   'obj_ident'    => $objIdent,
                   'member_ident' => $this->member_ident,
                   'obj_type'     => 'goods',
                   'params'       => $this->_generateParams($aData),
                   'quantity'     => floatval($aData['num'])
                 );
        
        if($this->get_cart_status()) {

            if(!$this->_check($aSave)) return false;
            if($this->no_database) {
                $this->no_database_cart_object[$aSave['obj_ident']] = $aSave;
                return $aSave; //后台 
            }
        }
        return $this->_add($aSave);
    }
    
    
    

    public function update($sIdent,$quantity) {
        if(!floatval($quantity['quantity'])) {
            $flag = $this->delete($sIdent);
            return $flag;
        }

        $aSave = array(
           'obj_ident'    => $sIdent,
           'member_ident' => $this->member_ident,
           'obj_type'     => 'goods',
         );
        
        $filter = array(
                    'obj_ident' => $sIdent,
                    'member_ident' => $this->member_ident,
                    'obj_type' => 'goods',
                    );
        $arr_cart_object_data = $this->oCartObjects->getList('*', $filter, 0, -1, -1);
        $arr_cart_object_data = $arr_cart_object_data[0];

        $aSave['quantity'] = floatval($quantity['quantity']);
        unset($quantity['quantity']);
        if( is_array($quantity) && !empty($arr_cart_object_data) && is_array($arr_cart_object_data) ) {
            $aSave['params'] = $arr_cart_object_data['params'];
            if($quantity) {
                foreach($quantity as $group_id => $row) {
                    unset($arr_cart_object_data);
                    if( !isset($aSave['params']['adjunct'][$group_id]['adjunct']) || !is_array($aSave['params']['adjunct'][$group_id]['adjunct']) )continue;
                    foreach($aSave['params']['adjunct'][$group_id]['adjunct'] as $a_id => $a_num) {
                        if(!isset($row[$a_id]['quantity'])) {
                            unset($aSave['params']['adjunct'][$group_id][$a_id]);
                            unset($aSave['params']['adjunct'][$group_id]['adjunct'][$a_id]);
                            continue;
                        }
                        $aSave['params']['adjunct'][$group_id]['adjunct'][$a_id] = floatval($row[$a_id]['quantity']);
                        
                    }
                }
            } else {
                $aSave['params']['adjunct'] = '';
            }
        }
        return $this->_add($aSave,false);
    }

    /**
     * 指定的购物车商品项
     *
     * @param string $sIdent
     * @param boolean $rich        // 是否只取cart_objects中的数据 还是完整的sdf数据
     * @return array
     */
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

    // 购物车里的所有商品项
    public function getAll($rich = false) {
        if($this->get_cart_status())  {
            $aResult = $this->no_database_cart_object;
        } else {
            $aResult= $this->oCartObjects->getList('*',array(
                                               'obj_type' => 'goods',
                                               'member_ident'=> $this->member_ident,
                                            ));
        }
        
//print_r($aResult);exit;
        if(empty($aResult)) {
            ob_start();
            $this->oCartObjects->_setCookie();
            ob_end_clean();
            return array();
        }

        if(!$rich) return $aResult;

        return $this->_get($aResult);
    }

    // 删除购物车中指定商品项
    public function delete($sIdent = null) {
        if(empty($sIdent)) return $this->deleteAll();
        return $this->oCartObjects->delete(array('member_ident'=>$this->member_ident, 'obj_ident'=>$sIdent, 'obj_type'=>'goods'));
    }

    // 清空购物车中商品项数据
    public function deleteAll() {
        return $this->oCartObjects->delete(array('member_ident'=>$this->member_ident, 'obj_type'=>'goods'));
    }

    // 统计购物车中商品项数据
    public function count(&$aData) {
        // 购物车中不存在goods商品
        
        if(empty($aData['object']['goods'])) return false;
        $aData['goods_min_buy'] = array();
        $aResult = array(
                      'subtotal_weight'=>0,
                      'subtotal'=>0,
                      'subtotal_consume_score'=>0,
                      'subtotal_gain_score'=>0,
                      'discount_amount_prefilter'=>0,
                      'discount_amount_order'=>0,
                      'discount_amount'=>0,
                      'items_quantity'=>0,
                      'items_count'=>0,
                   );
        
        foreach($aData['object']['goods'] as &$row) {
            $this->_count($row);
            $aResult['subtotal_consume_score'] += $row['subtotal_consume_score'];
            $aResult['subtotal_gain_score'] += $row['subtotal_gain_score'] + $row['sales_score_order'];
            $aResult['subtotal'] += $row['subtotal'];
            //if(!(isset($aData['is_free_shipping']) && $aData['is_free_shipping'])) { // 全场免运费
                $aResult['subtotal_weight'] += $row['subtotal_weight'];
            //}
            $aResult['discount_amount_prefilter'] += $row['discount_amount_prefilter'];
            if( $row['subtotal'] < ($row['discount_amount_prefilter'] + $row['discount_amount_order']) )
                $row['discount_amount_order'] = $row['subtotal'] - $row['discount_amount_prefilter'];
            $aResult['discount_amount_order'] += $row['discount_amount_order'];
            $aResult['discount_amount'] += $row['discount_amount_cart'] ;
            $aResult['items_quantity'] += $row['quantity'];
            $aResult['items_count']++;
            $aData['goods_min_buy'][$row['min_buy']['goods_id']]['info'] = $row['min_buy'];
            $aData['goods_min_buy'][$row['min_buy']['goods_id']]['real_quantity'] += $row['quantity'];
            
            if($row['quantity'] > $row['store']['real']) {
                 $aData['cart_status'] = 'false';
                 $aData['cart_error_html'] = '库存错误！';
            }
        }
        
        
        foreach ($aData['goods_min_buy'] as $aGoodsMinBuy) {
            if($aGoodsMinBuy['info']['min_buy'] > $aGoodsMinBuy['real_quantity']) {
                $aData['cart_status'] = 'false';
                $aData['cart_error_html'] = '商品： '. $aGoodsMinBuy['info']['name'] .'数量未达起订量！起订量为：'. $aGoodsMinBuy['info']['min_buy'];
                break;
            }
        }
        
        
       

        return $aResult;
    }

    /**
     * 单件
     *
     * @param array $aData
     */
    private function _count(&$aData) {
        // 重新统计时将以下值 置为0
        $aData['subtotal_consume_score'] = 0;
        $aData['subtotal_gain_score'] = 0;
        $aData['subtotal'] = 0;
        $aData['subtotal_weight'] = 0;
        $aData['discount_amount'] = 0;
        $aData['discount_amount_prefilter'] = 0;
        foreach($aData['obj_items']['products'] as $key=>$row) {
            $temp = array('goods_id'=>$row['goods_id'], 'min_buy'=>$row['min_buy'], 'name'=>$row['name']);
            $aData['min_buy'] = $temp;  //起订量
            if($key != 0) break;
            $aResult = $this->_count_product($row);

            $aData['obj_items']['products'][$key]['subtotal'] = $aResult['subtotal'] * $aData['quantity'];
            //配件
            if( isset($aData['adjunct']) && is_array($aData['adjunct']) && !empty($aData['adjunct']) ) {
                foreach($aData['adjunct'] as $vkey => $row) {
                    $aData['adjunct'][$vkey]['subtotal'] = $row['price']['buy_price'] * $row['quantity'];
                    $aData['subtotal'] += $aData['adjunct'][$vkey]['subtotal'];
                }
            }
            $aData['subtotal_consume_score'] += $aResult['subtotal_consume_score'];
            $aData['subtotal_gain_score'] += $aData['sales_score'] + $aResult['subtotal_gain_score'];
            $aData['subtotal'] += $aResult['subtotal'] * $aData['quantity'];
            $aData['subtotal_weight'] += $aResult['subtotal_weight'];
            $aData['discount_amount_prefilter'] += ($aResult['subtotal'] - $aResult['subtotal_current']);
        }

        
        // 数量
        $aData['subtotal_consume_score'] *= $aData['quantity'];
        $aData['subtotal_gain_score'] *= $aData['quantity'];
        //$aData['subtotal'] = $aData['quantity'];
        #if(!(isset($aData['is_free_shipping']) && $aData['is_free_shipping'])) { // 对指定的商品免运费
            $aData['subtotal_weight'] *= $aData['quantity'];
        #}
        $aData['discount_amount_prefilter'] *= $aData['quantity'];
    }

    private function _count_product(&$row){
        $this->getScroe($row);
        $aResult = array(
                      'subtotal_weight'=>0,
                      'subtotal'=>0,
                      'subtotal_consume_score'=>0,
                      'subtotal_gain_score'=>0,
                      'subtotal_current'=>0,
               );
        $aResult['subtotal_weight'] += $row['weight'] * $row['quantity'];
        $aResult['subtotal'] += $row['price']['price'];// * $row['quantity']; // 按商品价格
        $aResult['subtotal_consume_score'] += $row['consume_score'] * $row['quantity'];
        //$aResult['subtotal_gain_score'] += $row['gain_score'] * $row['quantity'];
        $aResult['subtotal_gain_score'] = $row['gain_score']; //* $row['quantity'];

        $aResult['subtotal_current'] += $row['price']['buy_price']; // 按实际购买价格
        return $aResult;
    }

    // todo 商品添加到购物车中的数据检测在这里处理
    // 商品的上下架 库存
    private function _check($aData, $_check_adjunct=true) {
        if(empty($aData)) trigger_error(_("购物车操作失败"),E_USER_ERROR);

        // 验证商品的正确性
        $obj_ident = $aData['obj_ident'];
        if(empty($obj_ident) || is_array($obj_ident)) return false;
        
        //商品 是否下架 是否删除
        $oSG = $this->o_goods;
        $arr_goods_info = $this->getIdFromIdent($obj_ident);
        $goods_id = $arr_goods_info['goods_id'];
        $product_id = $arr_goods_info['product_id'];
        if( !isset($this->check_goods_info[$goods_id]) )
            $this->check_goods_info[$goods_id] = $oSG->getList('goods_id, store,nostore_sell, marketable', array('goods_id'=> "$goods_id"));
        
        $aResult = $this->check_goods_info[$goods_id];
        
        $aGoods = $aResult[0];
        
        if($aGoods['marketable']=='false') return false;  //未上架

        //规格商品
        $params = is_array($aData['params']) ? $aData['params'] : @unserialize($aData['params']);

        if($params['product_id']) {
            $product_id = $params['product_id'];
        }
        if(empty($product_id)) return false;
        
        if( !isset($this->check_products_info[$product_id]) )
            $this->check_products_info[$product_id] = $this->o_products->getList('product_id,goods_id, store, freez, marketable', array('product_id'=>"$product_id"));
        
        $aResult = $this->check_products_info[$product_id];
        
        
        if(!$aResult[0]) return false;
        $arr_product = $aResult[0];

        if($arr_product['marketable']=='false') return false;   //未上架
        $arr_product['store'] = ( $aGoods['nostore_sell'] ? 99999999999 : ( empty($arr_product['store']) ? ($arr_product['store']===0 ? 0 : 99999999999) : $arr_product['store'] -$arr_product['freez']) );

        if ( !$aGoods['nostore_sell'] ) {
            if(empty($arr_product['store'])){
                if($arr_product['store']===0) return false; //库存0
            // 检测是否够库存
            } else if($aData['quantity']>$arr_product['store']) return false;
            
        }

        if($_check_adjunct)
            return $this->_check_adjunct($aData, $goods_id);
        else 
            return  true;
    }
    
    
    //验证库存、是否上架商品
    private function _check_goods( &$aData, $arr_goods_id ) {
        if( empty($arr_goods_id) ) return ;
        
        $arr = $this->o_goods->getList('goods_id, store,nostore_sell, marketable', array('goods_id'=> $arr_goods_id));

        foreach($arr as $row) {
            $this->check_goods_info[$row['goods_id']] = $row;
            $key = array_search( $row['goods_id'], $arr_goods_id );
            if( $row['marketable']=='false' ) unset($aData[$key]);
            if( $row['nostore_sell'] )
                $this->nostore_sell[$row['goods_id']] = true;
        }
    }
    
    
    //验证库存、是否上架货品
    private function _check_products( &$aData, $arr_products_id ) {
        if( empty($arr_products_id) ) return ;
        
        $arr = $this->o_products->getList('product_id,goods_id, store, marketable', array('product_id'=>$arr_products_id));
        
        foreach($arr as $row) {
            $key = array_search( $row['product_id'], $arr_products_id );
            if( $row['marketable']=='false' ) unset($aData[$key]);
            if( !$this->nostore_sell[$row['goods_id']] ) {
                if( empty($row['store']) ){
                    if( $row['store']===0 ) unset($aData[$key]); //库存0
                    else $row['store'] = 99999999;
                } else if($aData[$key]['quantity']>$row['store']) unset($aData[$key]);
            }
        }
    }
    
    private function _check_adjunct( $aData=array(), $goods_id ) {
        if(isset($aData['params']) && isset($aData['params']['adjunct']) && is_array($aData['params']['adjunct']) && !empty($aData['params']['adjunct']) ) {
            $arr_goods_info = $this->_get_adjuncts($goods_id);
            if(!$arr_goods_info) return false;
            
            $arr_cart_object = $this->oCartObjects->getList('*',array(
                                                       'obj_type' => 'goods',
                                                       'member_ident'=> $this->member_ident,
                                                    ));
            $tmp_products_store = array();
            foreach( $arr_cart_object as $arr ) {
                if( $aData['obj_ident']==$arr['obj_ident'] ) $arr = $aData;
                $tmp_products_store[$arr['params']['product_id']] += $arr['quantity'];
                if( isset($arr['params']['adjunct']) && !empty($arr['params']['adjunct']) && is_array($arr['params']['adjunct']) ) {
                    foreach( $arr['params']['adjunct'] as $adjuncts ) {
                        if( isset($adjuncts['adjunct']) && !empty($adjuncts['adjunct']) && is_array($adjuncts['adjunct']) ) {
                            foreach( $adjuncts['adjunct'] as $p_id => $quantity ) {
                                $tmp_products_store[$p_id] += $quantity;
                            }
                        }
                    }
                }
            }

            $arr_p_id = array();
            foreach($aData['params']['adjunct'] as $row) {
                if( !isset($row['adjunct']) || !is_array($row['adjunct']) ) continue;
                foreach( $row['adjunct'] as $p_id => $quantity ) {
                    if( false===array_search($p_id, $arr_goods_info['value'][$row['group_id']]['items']['product_id']) ) return false;
                    if( $quantity>$arr_goods_info['value'][$row['group_id']]['max_num'] ) return false;
                    $arr_p_id[] = $p_id;
                    /*
                    $arr_tmp_store = $this->o_products->getList('product_id,goods_id, store, marketable', array('product_id'=>$p_id) );
                    $arr_tmp_store = $arr_tmp_store[0];
                    if( empty($arr_tmp_store['goods_id']) ) return false;
                    $this->check_products_info[$p_id] = $arr_tmp_store;

                    if( !isset($this->check_goods_info[$arr_tmp_store['goods_id']]) )
                        $this->check_goods_info[$arr_tmp_store['goods_id']] = $this->o_goods->getList('goods_id, store,nostore_sell, marketable', array('goods_id'=> $arr_tmp_store['goods_id']));
                    $arr_tmp_goods = $this->check_goods_info[$arr_tmp_store['goods_id']];
                    
                    if( empty($arr_tmp_goods['nostore_sell']) ) {
                        if( $arr_tmp_store['store']<$tmp_products_store[$p_id] && $arr_tmp_store['store']!==null ) return false;
                    }
                    */
                }
            }
            
            if( $arr_p_id ) {
                $arr_g_id = array();
                $arr_tmp_store = $this->o_products->getList('product_id,goods_id, store, marketable', array('product_id'=>$arr_p_id) );
                foreach( $arr_tmp_store as $row ) {
                    if( empty($row['goods_id']) ) return false;
                    $this->check_products_info[$p_id] = $arr_tmp_store;
                    $arr_g_id[] = $row['goods_id'];
                }
                

                $arr_tmp_goods = $this->o_goods->getList('goods_id, store,nostore_sell, marketable', array('goods_id'=> $arr_g_id));
                foreach( $arr_tmp_goods as $row ) {
                    $this->check_goods_info[$row['goods_id']] = $row;
                    if( empty($row['nostore_sell']) ) {
                        if( $this->check_goods_info[$row['goods_id']]['store']<$tmp_products_store[$p_id] && $row['store']!==null ) return false;
                    }
                }
            }
            
        }
        return true;
    }

    private function _generateIdent($aData) {

        $adjunct = array();
        if($aData['adjunct']) {
            if(is_array($aData['adjunct'])) {
                foreach($aData['adjunct'] as $val) {
                    if(is_array($val)) {
                        foreach($val as $ap_id => $a_quantity) {
                            $adjunct[$ap_id] = $a_quantity;
                        }
                    } else {
                        $adjunct[] = $val;
                    }
                }
            } else {
                $adjunct[] = $aData['adjunct'];
            }
        } else {
            $adjunct[] = 'na';
        }
        $stradj = array();
        foreach($adjunct as  $key => $val) {
            $adj[] = $key.'('.$val.')';
        }
        

        return "goods_".$aData['goods_id']."_".$aData['product_id'];# .'_'. ( $this->arr_member_info['member_id'] ? $this->arr_member_info['member_id'] : 0 );//."_".implode('_', $adj);
    }
    
    
    
    private function getIdFromIdent($ident='') {
        if(!$ident) return false;
        $temp = explode('_', $ident);
        return array('goods_id'=>$temp[1], 'product_id'=>$temp[2]);
    }


    /**
     * Enter description here...
     *
     * @param array $aData // as add
     * @return array
     */
    private function _generateParams($aData) {
        $adj_items = array();
        if($aData['adjunct'] && $aData['adjunct'] != 'na'){
            if(is_array($aData['adjunct'])) {
                foreach($aData['adjunct'] as $group_id => $adjunct) {
                    $adj_items[$group_id] = array('group_id'=>$group_id, 'adjunct'=>$adjunct);
                }
            }
            
        }
        return  array(
                    'goods_id' => $aData['goods_id'],
                    'product_id' => $aData['product_id'],
                    'adjunct' => $adj_items,
                );
    }

    /**
     *
     *
     * @param array $aData // dbscheme/cart_objects * N
     * @return array
     */
    private function _get($aData) {
        
        $aInfo = $this->_get_basic($aData);
        $aProductId = $aInfo['productid'];
        $aAdjunctId  = $aInfo['adjunctid'];
        $products_store = $tmp_products_store = array();

        
        $aProducts = $this->_get_products($aProductId);
        
        
        
        $arr_goods = $arr_products = array();
        foreach( $aData as $key => $row ) {
            //商品不存在时删除购物车内信息
            if(empty($aProducts[$row['obj_items']['products'][0]])) {
                unset($aData[$key]);continue;
            }
            $arr_goods[$key] = $row['params']['goods_id'];
            $arr_products[$key] = $row['params']['product_id'];
        }
        $this->_check_goods($aData, $arr_goods);  
        $this->_check_products($aData, $arr_products);
        
        $arr_products = array();
        foreach($aData as $key => &$row) {
            // obj_items 第一个是货品信息
            $aData[$key]['obj_items']['products'][0] = $aProducts[$row['obj_items']['products'][0]];
            
            $product_id = $row['obj_items']['products'][0]['product_id'];
            
            if(isset($tmp_products_store[$product_id])) {
                $tmp_products_store[$product_id]['less'] += $row['quantity'];
                $tmp_products_store[$product_id]['quantity'] += $row['quantity'];
            } else {
                $tmp_store = array(
                    'quantity' => $row['quantity'],
                    'store'    => $row['obj_items']['products'][0]['store'],
                    'product_id' => $row['obj_items']['products'][0]['product_id'],
                    'obj_ident' => $row['obj_ident'],
                    'less'      => $row['quantity'],
                    'name'      =>  $row['obj_items']['products'][0]['new_name'],
                );
                $tmp_products_store[$product_id] = $tmp_store;
            }
            // 有配件将配置加入到['obj_items']['products']中
            $tmp_adjunct_name = array();
            $row['adjunct'] = array();
            
            if(isset($row['params']['adjunct']) && !empty($row['params']['adjunct'])) {
                foreach($row['params']['adjunct'] as &$adjunct) {
                    if(is_array($adjunct['adjunct'])) {
                        foreach($adjunct['adjunct'] as $key => $quantity) {
                            $tmp_adjunct_arr = null;
                            if(isset($arr_products[$key]) && !empty($arr_products[$key])) {
                                $tmp_adjunct_arr = $arr_products[$key];
                            } else {
                                 $tmp_tt = $this->get_product_adjunct($key, $adjunct, $quantity, $tmp_adjunct_name, $row, $tmp_products_store);
                                 if(empty($tmp_tt))  unset($$adjunct['adjunct'][$key]);
                                 $tmp_adjunct_arr = $tmp_tt;
                                 $tmp_tt = null;
                            }
                            if($tmp_adjunct_arr) {
                                $tmp_adjunct_arr['store'] = &$products_store[$key][$product_id][$adjunct['group_id']]['store'];
                                $tmp_adjunct_arr['group_id'] = $adjunct['group_id'];
          
                                $row['adjunct'][] = $tmp_adjunct_arr;
                            }
                        }
                    }
                }
            //$row['store'] = &$products_store[$row['obj_ident']]['store'];
            }
            $row['store'] = &$products_store[$product_id]['store'];
            
        }


        $this->get_products_real_store($tmp_products_store, $products_store);
        return $aData;
    }

    
    
    private function get_meta($group_id=null, $goods_id=0) {
        if( $group_id===null || empty($goods_id) ) return false;  // 配件信息（购物车中） 配件分组id
        
        //取得配件
        $arr = app::get('dbeav')->model('meta_register')->getList('mr_id, col_type', array('pk_name'=>'goods_id', 'col_name'=>'adjunct'));
        if( empty($arr) || !isset($arr[0]['col_type']) || empty($arr[0]['col_type']) || !isset($arr[0]['mr_id']) || empty($arr[0]['mr_id']) ) return false;

        $arr_meta_data = app::get('dbeav')->model('meta_value_'.$arr[0]['col_type'])->select($arr[0]['mr_id'], array($goods_id));
        $arr_adjunct_to_goods = is_array($arr_meta_data[$goods_id]['adjunct']) ? $arr_meta_data[$goods_id]['adjunct'] : unserialize($arr_meta_data[$goods_id]['adjunct']);
        $arr = $arr_meta_data = null;
        
        return $arr_adjunct_to_goods[$group_id];
    }

    private function get_products_real_store(&$tmp, &$products_store) {
         foreach($tmp as $val) {
            
            if(isset($val['adjunct_to_goods']) && is_array($val['adjunct_to_goods'])) {
                foreach($val['adjunct_to_goods'] as $p_id => $arr_val) {
                    foreach($arr_val as $g_id => $s_v) {
                        $t_t_store = $val['store'] - $val['less'] + $s_v['quantity'];
                        $products_store[$val['product_id']][$p_id][$g_id]['store'] = array(
                                                                                            'real' => ($t_t_store>$s_v['adjunct']['max_num']) ? $s_v['adjunct']['max_num'] : ($t_t_store),
                                                                                            'less' => $val['less'],
                                                                                            'store' => $val['store'],
                                                                                            'name' => $val['name'],
                                                                                        );
                    }
                }
            } 
            
            $products_store[$val['product_id']]['store'] = array(
                                                    'real' => $val['store'] - $val['less'] + $val['quantity'],
                                                    'less' => $val['less'],
                                                    'store' => $val['store'],
                                                    'name' => $val['name'],
                                                );
        
        }
        
    }

    private function get_product_adjunct($pid=0, $adjunct=array(), $quantity=0, &$tmp_adjunct_name=array(), &$row, &$tmp_products_store) {
        
        if( empty($pid) || empty($adjunct) || empty($quantity) ) return false;
        
        $info = $adjunct['info'];

        if( empty($info) || !isset($adjunct['group_id']) ) return false;
        
        $group_id = $adjunct['group_id'];

        $tmp = $this->_get_products(array($pid));
        if(empty($tmp)) return false;
        if( !isset($tmp[$pid]) ) return false;
        
        
        $tmp = $tmp[$pid];
        $tmp['price']['buy_price'] *= $info['price'];
        $tmp['quantity'] = $quantity;
        $tmp_adjunct_name[] = $tmp['new_name'];
        
        
        $tmp_store = array(
                        'product_id' => $pid,
                        'store' => $tmp['store'],
                        'less'  => $tmp_products_store[$pid]['less'] + $quantity,
                        'quantity' => &$tmp_products_store[$pid]['quantity'],
                        'goods_quantity' => $row['quantity'],
                        'name' => $tmp['new_name'],
                        'adjunct' => array('max_num' => $info['max_num']),
                    );
        $tmp_adjunct_to_goods = array(
                                        'goods_quantity' => $row['quantity'],
                                        'quantity' => $quantity,
                                        'product_id' => $row['obj_items']['products'][0]['product_id'],
                                        'store'    => $row['obj_items']['products'][0]['store'],
                                        'obj_ident' => $row['obj_ident'],
                                        'name' => &$row['obj_items']['products'][0]['new_name'] ,
                                        'adjunct' => array('max_num' => $info['max_num']),
                                    );
        if($tmp_products_store[$pid]['adjunct_to_goods']) {
            $tmp_store['adjunct_to_goods'] = $tmp_products_store[$pid]['adjunct_to_goods'];
        }
        
        $tmp_store['adjunct_to_goods'][$row['obj_items']['products'][0]['product_id']][$group_id] = $tmp_adjunct_to_goods;
        
            
        $tmp_products_store[$pid] = $tmp_store;
        

        return $tmp;
    }

    private function _get_adjuncts($goods_id, $all=false) {
        if(empty($goods_id)) return false;
        !is_array($goods_id) or $goods_id = implode(',', $goods_id);
        
        $db = $this->db;
        $sql = "SELECT b.value, b.pk, b.mr_id FROM 
                    `sdb_dbeav_meta_register` a 
                INNER JOIN
                    `sdb_dbeav_meta_value_text` b
                ON a.mr_id=b.mr_id
                AND a.pk_name='goods_id' AND b.pk IN ($goods_id) ";
        $aInfo = $db->select($sql);
        if(empty($aInfo)) return ;
        foreach($aInfo as &$row) {
            $row['value'] = is_array($row['value']) ? $row['value'] : @unserialize($row['value']);
        }
        return $all ? $aInfo : $aInfo[0];
    }
    
    
    
    private function _get_basic(&$aData) {

        $aResult = array();
        $arr_adjunct_info_goods = $aProductId = array();
        
        foreach( $aData as $row ) {
            $arr_goods_id[] = $row['params']['goods_id'];
        }

        $arr = $this->_get_adjuncts($arr_goods_id, true);
        if( is_array($arr) ) {
            foreach( $arr as $row ) {
                $arr_adjunct_info_goods[$row['pk']] = $row;
            }
        }
        
        foreach($aData as $row) {

            if($row['params']['product_id']) {
                $aProductId[] = $row['params']['product_id'];
            }

            $adjunct_info_goods = $arr_adjunct_info_goods[$row['params']['goods_id']];

            
            if($row['params']['adjunct']){
                foreach($row['params']['adjunct'] as &$_adjunct){
                    if(!$_adjunct['adjunct'])continue;
                    $_adjunct['info'] = $adjunct_info_goods['value'][$_adjunct['group_id']];
                    if(is_array($_adjunct['adjunct'])) {
                        foreach($_adjunct['adjunct'] as $key => $val) {
                            $aDjunct[$row['obj_ident']][$key] = $key;
                        }
                    } else {
                        //$aDjunct[$row['obj_ident']][$_adjunct['adjunct']] = $_adjunct['adjunct'];
                    }
                    
                }
            }

            
            $aResult[] = array(
                            'obj_ident' => $row['obj_ident'],
                            'obj_type' => 'goods',
                            'obj_items' => array(
                                              'products' => array($row['params']['product_id']),
                                           ),
                            'quantity' => $row['quantity'],
                            'params' => $row['params'],
                            'subtotal_consume_score' => 0,
                            'subtotal_gain_score' => 0,
                            'subtotal' => 0,
                            'subtotal_weight' => 0,
                            'discount_amount' => 0,
                            'adjunct' => $row['params']['adjunct'],
                        );
        }
        // 将整理好的数据格式用引用带出
        $aData = $aResult;
        return array('adjunctid'=>$aDjunct, 'productid'=>array_unique($aProductId));
        //return array_unique($aProductId);
    }

    function _get_products($aProductId) {
        if(empty($aProductId)) return array();
        ///////////////// 货品信息 ///////////////////////
        $sSql = "SELECT
                     p.product_id,p.goods_id,p.bn,g.score as gain_score,p.cost,p.name, p.store, p.marketable, g.params, g.package_scale, g.package_unit, g.package_use, p.freez, 
                     g.goods_type, g.nostore_sell, g.min_buy,g.type_id,g.image_default_id,p.spec_info,p.spec_desc,p.price,p.weight,
                     t.setting, t.floatstore
                 FROM  sdb_b2c_products AS p
                 LEFT JOIN  sdb_b2c_goods AS g    ON p.goods_id = g.goods_id
                 LEFT JOIN sdb_b2c_goods_type AS t ON g.type_id  = t.type_id
                 WHERE product_id IN (".implode(',',$aProductId).")";
       
       $aProduct = $this->oCartObjects->db->select($sSql);
       
       ////////// 设置了的会员价 //////////////////////////
       $sSql = "SELECT p.product_id,p.price
                FROM sdb_b2c_goods_lv_price AS p
                LEFT JOIN sdb_b2c_member_lv AS lv ON p.level_id = lv.member_lv_id
                WHERE p.level_id=".(intval($this->arr_member_info['member_lv']))." AND p.product_id IN (".implode(',',$aProductId).")";

       $aPrice = $this->oCartObjects->db->select($sSql);
       $tmp = array();
       foreach($aPrice as $val) {
           $tmp[$val['product_id']] = $val;
       }
       $aPrice = $tmp;
       $tmp = null;
       $aPrice = empty($aPrice)? array() : utils::array_change_key($aPrice,'product_id');

       //////////// 获取会员折扣 //////////////////////////
       //empty($this->arr_member_info)
       if( empty($this->arr_member_info) ) { // 非登陆用户
           $discout = 1;
       } else {// 登陆用户
           $discout = $this->discout;
       }

       //////////// 整理数据 /////////////////////////////
       $aResult = array();
       foreach($aProduct as $row) {
           //$products_store[$row['product_id']]['store'] = $row['store'];
           if($row['marketable']=='false') {  //商品下架购物车中消失处理！
               unset($row);continue;
           }

           //商品不存在时购物车里也同时删除
           $key = array_search($row['product_id'], $aProductId);
           if($key===false) $arrDelGoods[] = $aProductId[$key];
           //商品不存在时购物车里也同时删除
           
           $aResult[$row['product_id']] = array(
                    'bn' => $row['bn'],
                    'price' => array(
                                'price' => $row['price'],
                                'cost' => $row['cost'],
                                'member_lv_price' => empty($aPrice[$row['product_id']]) ? ($row['price'] * $discout) : $aPrice[$row['product_id']]['price'],
                                //'buy_price' => empty($aPrice[$row['product_id']])? ($row['price'] * $discout) : $aPrice[$row['product_id']]['price'] * $discout,
                                'buy_price' => empty($aPrice[$row['product_id']]) ? ($row['price'] * $discout) : $aPrice[$row['product_id']]['price'],
                              ),
                    'product_id' => $row['product_id'],
                    'goods_id' => $row['goods_id'],
                    'goods_type' => $row['goods_type'],
                    'name'=> $row['name'],
                    'consume_score' => 0,
                    'gain_score' => intval($row['gain_score']),
                    'type_setting' => is_array($row['setting']) ? $row['setting'] : @unserialize($row['setting']),
                    'type_id' => $row['type_id'],
                    'min_buy' => $row['min_buy'],
                    'spec_info' => $row['spec_info'],
                    'spec_desc' => is_array($row['spec_desc']) ? $row['spec_desc'] : @unserialize($row['spec_desc']),
                    'weight' => $row['weight'],
                    'quantity' => 1,
                    'params' => is_array($row['params']) ? $row['params'] : @unserialize($row['params']),
                    'floatstore' => $row['floatstore'],
                    'store'=> ( $row['nostore_sell'] ? 99999999999 : ( empty($row['store']) ? ($row['store']===0 ? 0 : 99999999999) : $row['store'] -$row['freez']) ),
                    'package_scale' => $row['package_scale'],
                    'package_unit' => $row['package_unit'],
                    'package_use' => $row['package_use'],
                    'default_image' => array(
                                        'thumbnail' => $row['image_default_id'],
                                      )
           );
           if($row['package_use']) {
               if($row['package_scale']) {
                   $aResult[$row['product_id']]['quantity'] = $row['package_scale'];
                   foreach($aResult[$row['product_id']]['price'] as &$s_v_price) {
                        $s_v_price *= $row['package_scale'];
                   }
               }
           }
           $tmp = $aResult[$row['product_id']]['spec_info'];
           $aResult[$row['product_id']]['new_name'] = $row['name'] . ( $tmp ? ' ('. $tmp .')' : '' );
       }
       
       //商品不存在时购物车里也同时删除
       if(!empty($arrDelGoods)) {
           foreach ($aResult as $key => &$val) {
               if(in_array($val['goods_id'], $arrDelGoods)) {
                   unset($aResult[$key]);
              }
           }
      }
       //商品不存在时购物车里也同时删除
       
       return $aResult;
    }
    
    
    
    
    
    /**
     * 积分
     *
     * @param unknown_type $aData
     */
    private function getScroe(&$aData=array()) {
        
        //获取商店积分规则
        if(!isset($this->site_score_policy) && empty($this->site_score_policy)) {
            $this->site_score_policy = $this->app->getConf('site.get_policy.method');
        }

        //不使用积分
        if($this->site_score_policy==1) {
            $gain_score = 0;
        } else if ($this->site_score_policy==2) {
            if(!isset($this->site_score_rate) && empty($this->site_score_rate)) {
                $this->site_score_rate = $this->app->getConf('site.get_rate.method');
            }
            $gain_score = $aData['price']['buy_price'] * $this->site_score_rate;
        } else if ($this->site_score_policy==3) {
            $gain_score = $aData['gain_score'];
        }
        $aData['gain_score'] = $gain_score;
       
    }
    
    
    
    
    public function set_cookie($var='', $val=array()) {
        if(empty($var))return false;
        if(empty($val) || !is_array($val)) return false;
        kernel::single("base_session")->start();
        $_SESSION[$this->md5m($var)] = $val;
        kernel::single("base_session")->close();

        return true;
    }
    
    
    private function code($val='', $flag=false) {
        if($flag) {
            return base64_encode(@serialize($val));
        } else {
            return @unserialize(base64_decode($val));
        }
    }
    
    
    public function get_cookie($var='') {
        if(empty($var))return false;
        kernel::single("base_session")->start();
        $arr_data = $_SESSION[$this->md5m($var)];
        kernel::single("base_session")->close();
        return (empty($arr_data) ? array() : $arr_data);
    }
    
    public function del_cookie($var='') {
        if(empty($var))return false;
        kernel::single("base_session")->start();
        $_SESSION[$this->md5m($var)] = null;
        kernel::single("base_session")->close();
    }
    
    private function md5m($var='') {
        return $var;
        return md5(md5($var).'_shopex_goods');
    }
    
    
    
    
}
?>
