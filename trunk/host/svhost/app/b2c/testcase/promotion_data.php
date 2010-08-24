<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 促销规则测试用例数据
 * $ 2010-04-21 17:38 $
 */

///////////////////////////////////////////  购物车信息 /////////////////////////////////////////////////
$data['cart_objects'] =array();

///////////////////////////////////////////  商品促销规则 /////////////////////////////////////////////////
$data['rule_goods'] =array(
        // 只是一个conditions
        0 => array(
               'type'=>'b2c_sales_goods_aggregator_combine',
               'aggregator' => 'all', // 'all'|'any' [and连接条件 或 or连接条件]
               'value'      => 1, // 0|1   不满足以下条件 | 满足以下条件  (//不满足? 暂无很好的处理方法 可以用子查询实现 效率太低了 1暂时写死)
               'conditions' => array(
                                  '0' => array( // bn号 前包含 'xxxx'
                                           'type'=>'b2c_sales_goods_item_goods',
                                           'attribute' => 'goods_bn',  // 商品的属性
                                           'operator'  => '()#',   // 操作
                                           'value'     => 'xxx', // 值 string | array
                                  ),
                                  '1' => array(
                                           'type'=>'b2c_sales_goods_aggregator_combine',
                                           'aggregator'=>'any',
                                           'value' => 1,
                                           'conditions' => array(
                                                               '0' => array( // 价格大于50\
                                                                         'type'=>'b2c_sales_goods_item_goods',
                                                                         'attribute' => 'goods_price',  // 商品的属性
                                                                         'operator'  => '>=',   // 操作
                                                                         'value'     => '50', // 值 string | array
                                                                ),
                                                                /*
                                                                '1' => array(// 品牌名称不为空
                                                                         'type'=>'b2c_sales_goods_item_brand',
                                                                         'attribute' => 'brand_brand_name',  // 商品的属性
                                                                         'operator'  => 'null',   // 操作
                                                                         'value'     => 'xxx', // 值 string | array
                                                                ),*/
                                                           )
                                 ),
                                 /* 其它属性 brand|cat|type
                                  '2' => array(
                                           'attribute' => 'brand/brand_name',  // 商品的属性
                                           'operator'  => '()',   // 操作
                                           'value'     => 'xxx', // 值 string | array
                                  ),*/
                                 '2' => array( // 库存大于50
                                           'type'=>'b2c_sales_goods_item_goods',
                                           'attribute' => 'goods_store',  // 商品的属性
                                           'operator'  => '>',   // 操作
                                           'value'     => '50', // 值 string | array
                                  ),
                         )
             ),
      // 一个完整的sales_rule_goods的记录
      1 => array(
             'rule_id'     => '1',
             'name'        => '测试规则1',
             'description' => '规则1描述',
             'from_time'   => 0,          // 规则开始时间
             'to_time'     => 0,          // 规则结束时间
             'status'      => 'true',     // 规则启用状态
             'action_solution' => serialize(null), // 运用规则 还不知道要怎么做呢... 2010-03-23 15:06
             'member_lv_ids'   => serialize(array(1,2,3,4)), // 规则运用的会员组
             'stop_rules_processing' => 'false',  // 是否允许以后的规则运用
             'free_shipping'=> 0, // 是否免运费
             'create_time'=>time(),
             'sort_order' => 0,
             'conditions' => serialize(array(// 存在库里是系列化的
                                          'type'=>'b2c_sales_goods_aggregator_combine',
                                          'aggregator' => 'all',
                                          'value'      => '1',
                                          'conditions' => array(
                                                            0=>array(
                                                                 'type'=>'b2c_sales_goods_item_goods',
                                                                 'attribute' => 'goods_goods_id',  // 商品的属性
                                                                 'operator'  => '=',   // 操作
                                                                 'value'     => 1,
                                                            )
                                          )
                             ))
      ),
      // 一个完整的sales_rule_goods的记录2
      2 => array(
             'rule_id'     => '2',
             'name'        => '测试规则2',
             'description' => '规则2描述',
             'from_time'   => 0,          // 规则开始时间
             'to_time'     => 0,          // 规则结束时间
             'status'      => 'true',     // 规则启用状态
             'action_solution' => serialize(null), // 运用规则 还不知道要怎么做呢... 2010-03-23 15:06
             'member_lv_ids'   => serialize(array(1)), // 规则运用的会员组
             'stop_rules_processing' => 'false',  // 是否允许以后的规则运用
             'free_shipping'=> 0, // 是否免运费
             'create_time'=>time(),
             'sort_order' => 0,
             'conditions' => serialize(array(// 存在库里是系列化的
                                          'type'=>'b2c_sales_goods_aggregator_combine',
                                          'aggregator' => 'all',
                                          'value'      => '1',
                                          'conditions' => array(
                                                            0=>array(
                                                                 'type'=>'b2c_sales_goods_item_goods',
                                                                 'attribute' => 'goods_goods_id',  // 商品的属性
                                                                 'operator'  => '=',   // 操作
                                                                 'value'     => 2,  // 改成1 sdb_goods_promotion_ref 将有两条规则
                                                            )
                                          )
                             ))
      )
);

///////////////////////////////////////////  订单促销规则 /////////////////////////////////////////////////
$data['rule_order'] =array(
                        // order rule 1 (combine + found)
                        0 => array(
                                'conditions' => array(
                                                 'type' =>'b2c_sales_order_aggregator_combine',
                                                 'aggregator' => 'all',
                                                 'value'=> 1,
                                                 'conditions'=> array(
                                                                 0 => array(
                                                                        'type'=>'b2c_sales_order_aggregator_found',
                                                                        'aggregator' => 'all',
                                                                        'value'    => 1,
                                                                        'conditions'=> array(
                                                                                         0 => array(
                                                                                                 'type' => 'b2c_sales_order_item_goods',
                                                                                                 'operator'=> '=',
                                                                                                 'attribute' => 'goods_goods_id',
                                                                                                 'value'=>'1',
                                                                                         ),
                                                                        )
                                                                 ), // 2cc0
                                                                 1 => array(
                                                                        'type'=>'b2c_sales_order_aggregator_found',
                                                                        'aggregator' => 'all',
                                                                        'value'    => 1,
                                                                        'conditions'=> array(
                                                                                         0 => array(
                                                                                                'type' => 'b2c_sales_order_item_goods',
                                                                                                'operator'=> '()',
                                                                                                'value'=> array(2,4,5,6),
                                                                                                'attribute'=>'goods_goods_id'
                                                                                         )
                                                                        )
                                                                 ),// 2cc1
                                                 ),
                                ),
                                'action_conditions' => array(
                                                         'type'=>'b2c_sales_order_aggregator_item',
                                                         'aggregator' => 'any',
                                                         'value'=> 0,
                                                         'conditions' => array(
                                                                          0 => array(
                                                                                 'type' => 'b2c_sales_order_item_goods',
                                                                                 'operator'=> '!()',
                                                                                 'attribute' => 'goods_goods_id',
                                                                                 'value'=>'1',
                                                                           ),
                                                                           1 => array(
                                                                                 'type' => 'b2c_sales_order_item_goods',
                                                                                 'operator'=> '()',
                                                                                 'value'=> array(2,4,5),
                                                                                 'attribute'=>'goods_goods_id'
                                                                           ),
                                                                           2 => array(
                                                                                 'type'=>'b2c_sales_order_aggregator_item',
                                                                                 'aggregator' => 'any',
                                                                                 'value'=> 0,
                                                                                 'conditions' => array(
                                                                                                      0 => array(
                                                                                                             'type' => 'b2c_sales_order_item_goods',
                                                                                                             'operator'=> '!()',
                                                                                                             'attribute' => 'goods_goods_id',
                                                                                                             'value'=>'1',
                                                                                                       ),
                                                                                                       1 => array(
                                                                                                             'type' => 'b2c_sales_order_item_goods',
                                                                                                             'operator'=> '()',
                                                                                                             'value'=> array(2,4,5),
                                                                                                             'attribute'=>'goods_goods_id'
                                                                                                       )
                                                                                                 )
                                                                           )
                                                         ) // 2acc
                                )
                             ),
                         // order rule 2 (address)
                         1 => array(
                                'conditions' => array(
                                                 'type' =>'b2c_sales_order_aggregator_combine',
                                                 'aggregator' => 'all',
                                                 'value'=> 1,
                                                 'conditions'=> array(
                                                                 0 => array(
                                                                        'type'=>'b2c_sales_order_item_order',
                                                                        'operator' => '>',
                                                                        'value'    => 0,
                                                                        'attribute'=>'order_subtotal'
                                                                 ),
                                                 ),
                                ),
                                'action_conditions' => array(
                                                         'type'=>'b2c_sales_order_aggregator_item',
                                                         'aggregator' => 'all',
                                                         'value'=> 1,
                                                         'conditions' => array( // todo get_rich_objects [0]obj_items['products']['type_id']  多返回 'cat_id','brand_id'
                                                                           0 => array(
                                                                                   'type'=>'b2c_sales_order_item_goods',
                                                                                   'operator' => '()', // 包含
                                                                                   'value' => array(1,2),
                                                                                   'attribute'=>'goods_type_id'
                                                                           ), // 可以定义多种ID集合条件 (brand_id,cat_id,type_id)
                                                         )
                                ),
                         ),
                         // order rule3  subselect + address
                         2 => array(
                                'conditions' => array(
                                                  'type' => 'b2c_sales_order_aggregator_combine', // 处理类型(不知道怎么定义的,也不知道有多少种)
                                                  'value' => 1,    // 1:成立 0:不成立  条件的成立要求  () | !()
                                                  'aggregator' => 'all', // all:所有 any:其中一样    and|or
                                                  'conditions' => array( // 成立条件
                                                                    0 => array( // 订单总金额50元以上
                                                                            'type' => 'b2c_sales_order_item_order',
                                                                            'attribute' => 'order_subtotal',   // 要验证的属性(商品,购物车)
                                                                            'operator' => '>=',
                                                                            'value' => '50',
                                                                    ),
                                                                    1 => array( // 总定购数量20以上(售价<=100的商品)
                                                                            'type' => 'b2c_sales_order_aggregator_subselect',
                                                                            'attribute' => 'order_quantity',
                                                                            'operator' => '>=',
                                                                            'value' => 20,
                                                                            'conditions' => array(
                                                                                               0 => array(
                                                                                                       'type' => 'b2c_sales_order_item_goods',
                                                                                                       'attribute' => 'goods_buy_price',
                                                                                                       'operator' => '<=',
                                                                                                       'value' => 100,
                                                                                               ), // c10
                                                                            ), // c1c
                                                                    ), // c1
                                                ),// conditions[conditions]
                                ),
                                'action_conditions' => array(
                                                         'type' => 'b2c_sales_order_aggregator_item',
                                                         'value' => 1,
                                                         'aggregator' => 'any',
                                                         'conditions' => array(
                                                                            0 => array( // 购买价格>=100的商品 给予优惠
                                                                                   'type' => 'b2c_sales_order_item_goods',
                                                                                   'attribute' => 'goods_buy_price',
                                                                                   'operator' => '>=',
                                                                                   'value' => '100',
                                                                             ),
                                                          ),
                                ),
                           ),
                          // order rule4
);

///////////////////////////////////////////  商品促销规则模板 /////////////////////////////////////////////////
$data['tpl_goods'] =array();

///////////////////////////////////////////  订单促销规则模板 /////////////////////////////////////////////////
$data['tpl_order'] =array(
      // template 1 第一个是简单的模板 (simple config)
      0 => array(
              'conditions'=> array(
                                'type'=>'b2c_sales_order_aggregator_combine',
                                'conditions'=> array(
                                                  0=>array(
                                                     'type'=>'b2c_sales_order_item_goods',
                                                     'attribute'=>'goods_goods_id'
                                                  ),
                                                  1=>array(
                                                     'type'=>'b2c_sales_order_item_order',
                                                     'attribute'=>'order_subtotal'
                                                  ),
                                                  2=> array(
                                                        'type'=>'b2c_sales_order_aggregator_subselect',   // 这个必段要写咯(没有实现由condition 去判断type的, 不写的话默认是'combine') 可以选择的是 subselect|found|combine 三种咯
                                                        'conditions'=>array( // 如果type是 subselect|found 条件必须是商品项的属性
                                                                         0=>array(
                                                                                'type'=>'b2c_sales_order_item_goods',
                                                                                'attribute'=>'goods_name'
                                                                            ),
                                                                        1=>array(
                                                                                'type'=>'b2c_sales_order_item_goods',
                                                                                'attribute'=>'goods_weight'
                                                                            ),
                                                                      )
                                                      ),
                                                  3=> array(
                                                        'type'=>'b2c_sales_order_aggregator_combine',
                                                        'conditions'=> array(
                                                                            0=>array(
                                                                                 'type'=>'b2c_sales_order_item_goods',
                                                                                 'attribute'=>'goods_cost'
                                                                            ),
                                                                            1=>array(
                                                                                 'type'=>'b2c_sales_order_item_order',
                                                                                 'attribute'=>'order_subtotal'
                                                                            ),
                                                                       ),
                                                  )
                                               ),
                             ),
              'action_conditions'=> array(
                                      'type'=>'b2c_sales_order_aggregator_item', // 这个必段设置  用同一方法处理的 标准默认为'combine' (todo:待调整)
                                      'conditions'=> array( // 这里只能是 订单商品项的信息
                                                        array(
                                                                'type'=>'b2c_sales_order_item_goods',
                                                                'attribute'=>'goods_name'
                                                        ),
                                                        array(
                                                                'type'=>'b2c_sales_order_item_goods',
                                                                'attribute'=>'goods_goods_id'
                                                        ),
                                                        array(
                                                                'type'=>'b2c_sales_order_item_goods',
                                                                'attribute'=>'goods_quantity'
                                                        ),
                                                        array(
                                                            'type'=>'b2c_sales_order_aggregator_item',
                                                            'conditions'=>array(
                                                                                array(
                                                                                    'type'=>'b2c_sales_order_item_goods',
                                                                                    'attribute'=>'goods_weight'
                                                                                ),
                                                                                array(
                                                                                    'type'=>'b2c_sales_order_item_goods',
                                                                                    'attribute'=>'goods_price'
                                                                                ),
                                                                                array(
                                                                                    'type'=>'b2c_sales_order_item_goods',
                                                                                    'attribute'=>'goods_buy_price'
                                                                                )
                                                                          ),
                                                        ),
                                                     ),
                                    ),
           ),
      // template 2 complex config(有一些不能实现,要调整代码 2010-04-07 15:51 wubin)
      1 => array(
              'conditions'=> array(
                                'type'=>'b2c_sales_order_aggregator_combine',
                                'aggregator'=>'any',
                                'value'=>0,
                                'conditions'=>array(
                                                 0=> array(
                                                       'type'=>'b2c_sales_order_item_goods',
                                                       'attribute'=>array(
                                                                    'default'=>'goods_type_id',
                                                                    'desc'=>'商品类型哦哦哦',
                                                        ),
                                                       'operator'=>'()',
                                                       'value'=>array(
                                                                    //'input'=>'text',
                                                                    'default'=>0,
                                                                )
                                                     ),
                                                 1=> array(
                                                        'type'=>'b2c_sales_order_aggregator_subselect', // 这个使用的都是默认的 可以修改成可配置的 'aggregator,value的设置都没有咯'
                                                        'attribute'=>'order_subtotal',
                                                        'operator'=>'<>',
                                                        'value'=>'500',
                                                        'conditions'=>array( // 只能是item的属性(todo:解析还没有处理过滤的能力 2010-04-07 17;29 wubin)
                                                                         array(
                                                                            'type'=>'b2c_sales_order_item_goods',
                                                                            'attribute'=>'goods_name'
                                                                         ),
                                                                         array(
                                                                            'type'=>'b2c_sales_order_item_goods',
                                                                            'attribute'=>'goods_quantity'
                                                                         )
                                                                      )
                                                     ),
                                              ),
                             ),
              'action_conditions'=> array(
                                       'type'=>'b2c_sales_order_aggregator_item',
                                       'aggregator'=>array(
                                                        'input'=>'hidden',
                                                        'default'=>'any',
                                                        'desc'=>'以下任意一条规则'
                                                     ),
                                       'conditions'=> array(
                                                        0=> array(
                                                               'type'=>'b2c_sales_order_item_goods',
                                                               'attribute'=>'goods_type_id',
                                                               'operator'=>array(
                                                                                'input'=>'hidden',
                                                                                'desc'=>'等于',
                                                                                'default'=>'='
                                                                           ),
                                                               'value'=>array(
                                                                            'input'=>'text',
                                                                            'default'=>'123',
                                                                )
                                                            ),
                                                      )
                                    ),
           ),
     // template 3
     2 => array(
            'conditions'=> array(
                                'type'=>'b2c_sales_order_aggregator_combine',
                                'conditions'=>array(
                                                 array(
                                                    'type'=>'b2c_sales_order_item_order',
                                                    'attribute'=>'order_subtotal'
                                                 ),
                                                 array(
                                                    'type'=>'b2c_sales_order_aggregator_subselect',
                                                    'conditions'=>array(
                                                                    array(
                                                                        'type'=>'b2c_sales_order_item_goods',
                                                                        'attribute'=>'goods_buy_price'
                                                                    )
                                                                  ),
                                                ),
                                           )
                           ),
            'action_conditions'=> array(
                                     'type'=>'b2c_sales_order_aggregator_item',
                                     'conditions'=>array(
                                                        array(
                                                            'type'=>'b2c_sales_order_item_goods',
                                                            'attribute'=>'goods_buy_price')
                                                   ),
                                  ),
     )
);
?>
