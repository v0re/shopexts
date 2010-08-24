<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * goods_items
 * $ 2010-05-09 16:15 $
 */
class b2c_sales_goods_item_goods extends b2c_sales_goods_item
{
    public function getItem() {
        return array(
                  'goods_goods_id'     => array('name'=>__('商品'),   'path'=>'goods_id',      'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('contain'), 'input'=>'dialog', 'table'=>'goods',),
                  'goods_brand_id'     => array('name'=>__('商品品牌'),   'path'=>'brand_id',      'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('contain'), 'input'=>'checkbox', 'options'=>'table:SELECT brand_id AS id,brand_name AS name FROM sdb_b2c_brand'),
                  'goods_cat_id'       => array('name'=>__('商品分类'),   'path'=>'cat_id',        'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('contain'), 'input'=>'checkbox', 'options'=>'table:SELECT cat_id AS id,cat_name AS name FROM sdb_b2c_goods_cat'),
                  'goods_type_id'      => array('name'=>__('商品类型'),   'path'=>'type_id',       'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('contain'), 'input'=>'checkbox', 'options'=>'table:SELECT type_id AS id,name FROM sdb_b2c_goods_type'),

                  'goods_name'         => array('name'=>__('商品名称'),   'path'=>'name',          'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','contain','contain1')),
                  'goods_brief'        => array('name'=>__('商品简介'),   'path'=>'brief',         'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','contain','contain1','null')),
                  'goods_intro'        => array('name'=>__('商品介绍'),   'path'=>'intro',         'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','contain','contain1','null')),
                  'goods_mktprice'     => array('name'=>__('商品市场价'), 'path'=>'mktprice',      'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','equal1'),  'vtype'=>'unsigned'),
                  'goods_cost'         => array('name'=>__('商品成本价'), 'path'=>'cost',          'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','equal1'),  'vtype'=>'unsigned'),
                  'goods_price'        => array('name'=>__('商品销售价'), 'path'=>'price',         'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','equal1'),  'vtype'=>'unsigned'),
                  'goods_bn'           => array('name'=>__('商品货号'),   'path'=>'bn',            'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','contain','contain1'),  'vtype'=>'alphaint'),
                  'goods_weight'       => array('name'=>__('商品重量'),   'path'=>'weight',        'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','equal1'),  'vtype'=>'unsigned'),
                  'goods_unit'         => array('name'=>__('商品单位'),   'path'=>'unit',          'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','contain','contain1'),  'vtype'=>'alphanum'),
                  'goods_store'        => array('name'=>__('商品库存'),   'path'=>'store',         'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','equal1'),  'vtype'=>'digits'),
                  'goods_score'        => array('name'=>__('商品积分'),   'path'=>'score',         'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','equal1'),  'vtype'=>'digits'),
                  'goods_last_modify'   => array('name'=>__('修改时间'),  'path'=>'last_modify',  'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','equal1'),  'input'=>'datetime','vtype'=>'date'),
                  'goods_rank'         => array('name'=>__('商品评分'),   'path'=>'rank',         'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','equal1'),  'vtype'=>'number'),
                  'goods_rank_count'    => array('name'=>__('评分次数'),  'path'=>'rank_count',   'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','equal1'),  'vtype'=>'digits'),
                  'goods_view_count'    => array('name'=>__('浏览次数'),  'path'=>'view_count',   'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','equal1'),  'vtype'=>'digits'),
                  'goods_buy_count'     => array('name'=>__('购买次数'),  'path'=>'buy_count',    'type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','equal1'),  'vtype'=>'digits'),
                  'goods_comment_count' => array('name'=>__('评论次数'),  'path'=>'comment_count','type'=>'goods', 'object'=>'b2c_sales_goods_item_goods', 'operator'=>array('equal','equal1'),  'vtype'=>'digits'),
        );
    }

    // 和主表的关系
    public function getRefInfo() {
        return false; // 使用就是主表的
    }

}
?>
