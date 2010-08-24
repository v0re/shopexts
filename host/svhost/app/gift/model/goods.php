<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
    
    
    
class gift_mdl_goods extends b2c_mdl_goods {
    
    var $has_tag = true;
    var $defaultOrder = array('p_order',' DESC',',goods_id',' DESC');
    var $has_many = array(
        //'product' => 'products:contrast',
        'images' => 'image_attach@image:contrast:goods_id^target_id',
    );
    var $has_one = array(
        'member_ref' => 'member_ref@gift:replace:goods_id^goods_id'
    );
    var $subSdf = array(
            'default' => array(
        /*
                'product'=>array(
                    '*',array(
                        'price/member_lv_price'=>array('*')
                    )
                ),
        */
                ':cat@gift'=>array(
                    '*'
                ),
                'images'=>array(
                    '*',array(
                        ':image'=>array('*')
                    )
                ),
                'member_ref'=>array(
                    '*',
                ),
            ),
            'delete' => array(
                /*
                'product'=>array(
                    '*',array(
                        'price/member_lv_price'=>array('*')
                    )
                ),
                */
                'images'=>array(
                    '*'
                ),
                'member_ref'=>array(
                    '*'
                ),
            )
        );
    
    
    var $filter_default = array('goods_type'=>'gift');
    

    public function dump($id, $col='*') {
        $filter = array(
                'goods_id'   => $id,
            );
        $filter = array_merge($filter, $this->filter_default);
        $arr_gift_info = parent::dump($filter, $col, 'default');
        return $arr_gift_info;
    }
    

    
    public function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null) {
        is_array($filter) or $filter = array();
        $filter = array_merge($filter, $this->filter_default);
        
        return parent::getList($cols, $filter, $offset, $limit, $orderType);
    }
    
    
    public function count($filter = array()) {
        is_array($filter) or $filter = array();
        $filter = array_merge($filter, $this->filter_default);
        return parent::count($filter);
    }
    
    function modifier_cat_id($cols){
        if( !$cols )
            return '-';
        else{
            $a = app::get('gift')->model('cat')->dump($cols);
            return $a['cat_name'];
        }
    }
    public function get_schema(){
        $this->app = app::get('b2c');
        $columns = parent::get_schema();
        $a['goods_id']['label'] = '赠品ID';
        $a['bn']['label'] = '赠品编号';
        $a['cat_id']['label'] = '赠品分类';
        $a['name']['label'] = '赠品名称';
        $a['marketable']['label'] = '是否开启';
        $a['uptime']['label'] = '兑换起始时间';
        $a['downtime']['label'] = '兑换结束时间';
        if(is_array($columns['columns'])) {
            foreach($columns['columns'] as $key => &$val) {
                if(!in_array($key, array('goods_id', 'bn', 'cat_id', 'name', 'marketable', 'uptime', 'downtime', 'p_order', 'price', 'weight', 'store'))) {
                    unset($columns['in_list'][array_search($key, $columns['in_list'])]);
                }
                if($a[$key])
                    $val['label'] = $a[$key]['label'];
            }
        }
        //$this->app = app::get('gift');
        return $columns;
    }
    
    
    public function table_name($real=false){
        $app_id = $this->app->app_id;
        $table_name = substr(get_parent_class($this),strlen($app_id)+5);
        if($real){
            return kernel::database()->prefix.$this->app->app_id.'_'.$table_name;
        }else{
            return $table_name;
        }
    }
    
    public function _columns() {
        $tmp = parent::_columns();
        $tmp['cat_id']['type'] = 'table:cat@gift';
        return $tmp;
    }
    
    
	public function save(&$goods,$mustUpdate = null){
	    if( $goods['store'] )
		    $goods['product'][0]['store'] = $goods['store'];
			
		return parent::save( $goods,$mustUpdate );
	}
    
    /**
     * @params string goods_id
     * @params string product_id
     * @params string num
     */
    public function unfreez($goods_id, $num){
        $sdf_pdt = $this->dump($goods_id, 'goods_id,params,store,marketable');
        if( empty($sdf_pdt) ) return false;
        $objMath = kernel::single('ectools_math');
        $params = $sdf_pdt['params'];

        if(is_null($params['freez']) || $params['freez'] === ''){
            if (is_null($sdf_pdt['store']) || $sdf_pdt['store'] === '')
                return true;

            $params['freez'] = 0;
        }elseif($num < $params['freez']){
            $params['freez'] = $objMath->number_minus(array($params['freez'], $num));
            //$sdf_pdt['freez'] -= $num;
        }elseif($num >= $params['freez']){
            $params['freez'] = 0;
        }
        $sdf_pdt['params'] = $params;
		

        return $this->save($sdf_pdt);
    }
    
    
     /**
     * 冻结产品的库存
     * @params string goods_id
     * @params string product_id
     * @params string num
     */
    public function freez( $goods_id,$num )
    {
        $sdf_pdt = $this->dump($goods_id, 'goods_id,params,store,marketable');
        if( empty($sdf_pdt) ) return false;
        $objMath = kernel::single('ectools_math');
        $params = $sdf_pdt['params'];

        if(is_null($params['freez']) || $params['freez'] === ''){
            if (is_null($sdf_pdt['store']) || $sdf_pdt['store'] === '')
                return true;

            $params['freez'] = 0;
            $params['freez'] = $objMath->number_plus(array($params['freez'], $num));
            //$sdf_pdt['freez'] += $num;
        }elseif($objMath->number_plus(array($params['freez'], $num)) > $sdf_pdt['store'] ){
            $params['freez'] = $sdf_pdt['store'];
        }else{
            $params['freez'] = $objMath->number_plus(array($params['freez'], $num));
            //$sdf_pdt['freez'] += $num;
        }
        $sdf_pdt['params'] = $params;


        return $this->save($sdf_pdt);
    }
    
    
    
}