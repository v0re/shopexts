<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_goods_sto extends dbeav_model{
    
    function __construct(&$app){
        $this->app = $app;
        $this->columns = array(
                        'goods_name'=>array('label'=>'缺货商品名称','width'=>200),
                        'uname'=>array('label'=>'会员用户名','width'=>200),
                        'email'=>array('label'=>'Email','width'=>100),
                        'send_time'=>array('label'=>'通知时间','width'=>100,'type'=>'time'),
                        'create_time'=>array('label'=>'登记时间','width'=>100,'type'=>'time'),
                        'sto_status'=>array('label'=>'库存状态','width'=>100),
                        'send_status'=>array('label'=>'通知状态','width'=>100),
                   );

        $this->schema = array(
                'default_in_list'=>array_keys($this->columns),
                'in_list'=>array_keys($this->columns),
                'idColumn'=>'gnotify_id',
                'columns'=>&$this->columns
            );  
    }
    
     function table_name($real=false){
         $object = $this->app->model('member_goods');
        $table_name = substr(get_class($object),strlen($this->app->app_id)+5);
        if($real){
            return kernel::database()->prefix.$this->app->app_id.'_'.$table_name;
        }else{
            return $table_name;
        }
    }
    
    function get_schema(){
        return $this->schema;
    }

    function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderby=null){
        $data = self::get_sto_goods();
        return $data;
    }

    function count($filter=null){
        return count($this->getList());
    }
 
    ##获取缺货登记货品列表
    static function get_sto_goods(){
      
       $obj_product = app::get('b2c')->model('products');
       $obj_goods = app::get('b2c')->model('goods');
       $member = app::get('b2c')->model('members');
       $member_goods = app::get('b2c')->model('member_goods');
       $data = $member_goods->getList('*',array('type'=>'sto'));
       #print_r($data);exit;
       $result = array();
       foreach($data as $sto_product ){
        if($sto_product['status'] =='ready'){
        $send_status = '未通知';
        }
          if($sto_product['status'] =='send'){
        $send_status = '已通知';
        }
        $pam = $member->dump($sto_product['member_id'],"*",array(':account@pam'=>array('*')));
        if($pam){
            $uname = $pam['pam_account']['login_name'];
        } 
        else{
            $uname = "非会员顾客";
        }
        $sdf = $obj_product->dump($sto_product['product_id']);
        $aGoods  = $obj_goods->getList('store',array('goods_id' =>$sdf['goods_id']));
        $goods_name = $sdf['name'];
        $product_bn = $sdf['bn'];
        $product_store = $sdf['store'];
        if($aGoods[0]['store']>0.00){
        if($product_store>0.00){
        $sto_status = '已到货';
        }
        else{
               $sto_status = '缺货中，请紧急备货';
        }
    }
    else{
        $sto_status = '缺货中，请紧急备货';
    }
        $result[] = array('gnotify_id'=>$sto_product['gnotify_id'],'goods_name'=>$goods_name,'sto_total'=>$total,'uname'=>$uname,'email'=>$sto_product['email'],'send_time'=>$sto_product['send_time'],'create_time'=>$sto_product['create_time'],'sto_status'=>$sto_status,'send_status'=> $send_status);
    }
    return $result;
         }
    

}  
