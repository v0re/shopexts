<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * goods aggregator(商品促销规则 组合条件)
 * $ 2010-05-12 13:36 $
 */
class b2c_sales_goods_aggregator_combine extends b2c_sales_goods_aggregator
{
    public function getItem() {
        // 其实一个aggregator 只有一条记录的哈
        return array(
                   'b2c_sales_goods_aggregator_combine' => array(
                                       'name'=>__('商品条件'),
                                       'value'=>'goods_combine',
                                       'object'=>'b2c_sales_goods_aggregator_combine',
                                       'support'=>array(
                                                     'aggregator'=>'all',
                                                     'item'=>array(
                                                                'goods'=>__('-----商品属性-----'),
                                                                'brand'=>__('-----商品品牌-----'),
                                                                'type'=>__('-----商品类型-----'),
                                                                'cat'=>__('-----商品分类-----'),
                                                             )
                                                  ),
                                      )
               );
    }

    protected function _init_aggregator() {
        if(is_null($this->_aAggregator)) {
            $aResult = array();
            /*
            // 这里会产生死循环的递归操作
            // 暂时使用kernel::servicelist 内部的代码进行处理
            foreach(kernel::servicelist($this->aggregator_apps) as $object) {
                //if(get_class($this))
                $aResult = array_merge($aResult,$object->getItem());
            }*/

            // 多个的时候也会出现递归的问题 这个得好好想想解决方案 2010-05-17 14:27 wubin

            // todo 这里只是暂时的处理 等待解决方案 2010-05-14 15:21
            $apps = app::get('base')->model('app_content')->getlist('content_path,app_id', array('content_type'=>'service','content_name'=>$this->aggregator_apps ));
            foreach($apps as $row) {
                if($row['content_path']){
                    if($row['content_path'] == get_class($this)) {
                        $aResult = array_merge($aResult,$this->getItem());
                    } else {
                        $aResult = array_merge($aResult,kernel::single($row['content_path'],app::get($row['app_id']))->getItem());
                    }
                }
            }
            $this->_aAggregator = $aResult;
        }
    }
}
?>
