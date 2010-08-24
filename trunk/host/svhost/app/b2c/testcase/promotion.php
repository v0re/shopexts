<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 促销规则测试用例
 * $ 2010-04-21 17:04 $
 */
class promotion extends PHPUnit_Framework_TestCase
{
    function setUp() {
        // 调用model
        $this->app = app::get('b2c');
        $this->oSGP = kernel::single('b2c_sales_goods_process'); // 商品促销规则
        $this->oSOP = kernel::single('b2c_sales_order_process'); // 订单促销规则


        // 载入测试数据
        require(dirname(__FILE__)."/promotion_data.php");
        $this->cart_objects = $data['cart_objects'];
        $this->rule_goods = $data['rule_goods'];
        $this->rule_order = $data['rule_order'];
        $this->tpl_order = $data['tpl_order'];
        $this->tpl_goods = $data['tpl_goods'];
    }




    function test_product_promotion () {
        $blocks = array('promotion'=>array('goods_id'=>1));
        $t = kernel::single("b2c_site_goods_detail_block_promotion");
        echo $t->get_blocks($blocks);
        exit;
    }





    function test_first() {
        $this->markTestSkipped("只是测试看看的");
        echo "test";
        print_r($this->oSGP);
    } // end testFirst


// 以下是 商品促销规则 相关测试用例////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * lib/sales/goods/process::filter() 测试用例
     */
    function test_sales_goods_process_filter(){
        $this->markTestSkipped("商品促销规则过滤,生成对应的sql语句");
        $where = $this->oSGP->filter($this->rule_goods[0]);
        $this->assertTrue((is_string($where) && ($where == "(bn  LIKE '%xxx'  AND  (price >= 50 )  AND  store > 50 )")),"生成sql条件为:".$where);
    }

    /**
     * lib/sales/goods/process::apply() 测试用例
     * @todo  需要goods_id = 1的数据一条
     * 本测试用例使用的是_apply方法 apply方法入参是$rule_id 从数据库中取出 再将取出的数据传入到_apply方法里处理
     */
    function test_sales_goods_process_apply() {
        $this->markTestSkipped('预处理一条规则');
        $bFlag = $this->oSGP->_apply($this->rule_goods[1]);
        $this->assertTrue($bFlag,'预处理规则1失败');
    }

    /**
     * lib/sales/goods/process::clear() 测试用例
     */
    function test_sales_goods_process_clear() {
        $this->markTestSkipped('清空规则1');
        $bFlag = $this->oSGP->clear(1);
        $this->assertTrue($bFlag,'清除商品促销规则1失败');
    }

    /**
     * 应用所有goods promotiom
     * lib/sales/goods/process::_apply()
     * 实际应用apply foreach 多条 大量的话可能得使用到任务列表异步ajax
     * 这个测试用例只是原型处理方法 在控制器上做文章
     */
    function test_sales_goods_process_applyall() {
         $this->markTestSkipped('预处理多条商品促销规则');
         unset($this->rule_goods[0]); // 第一个测试数据只是一个conditions
         foreach($this->rule_goods as $key => $row) {
             $this->oSGP->_apply($row);
         }

         $sSql = "SELECT count(*) AS num FROM sdb_b2c_goods_promotion_ref WHERE goods_id=1";
         $aTemp = $this->oSGP->db->selectrow($sSql);

         $this->assertEquals($aTemp['num'],1,"商品促销规则应该只有1条");

         /////////////////////////////////////////////////////////////////////////////

         $this->oSGP->clearAll();

         // 两条规则都能预处理到goods_id=1上
         $this->rule_goods[2]['conditions'] = serialize(array(// 存在库里是系列化的
                                                           'type'=>'b2c_sales_goods_aggregator_combine',
                                                           'aggregator' => 'all',
                                                           'value'      => '1',
                                                           'conditions' => array(
                                                                              0 => array(
                                                                                     'type'=>'b2c_sales_goods_item_goods',
                                                                                     'attribute' => 'goods_goods_id',  // 商品的属性
                                                                                     'operator'  => '=',   // 操作
                                                                                     'value'     => 1,  // 改成1 sdb_goods_promotion_ref 将有两条规则
                                                                               )
                                                            )
                                               ));
         // goods_id 有两条促销规则
         foreach($this->rule_goods as $key => $row) {
             $this->oSGP->_apply($row);
         }

         $sSql = "SELECT count(*) AS num FROM sdb_b2c_goods_promotion_ref WHERE goods_id=1";
         $aTemp = $this->oSGP->db->selectrow($sSql);

         $this->assertEquals($aTemp['num'],2,"商品促销规则应该只有2条");
    }

    /**
     *  lib/sales/goods/process::chearAll() 测试用例
     */
    function test_sales_goods_process_clearall() {
        $this->markTestSkipped('清空所有规则');
        $bFlag = $this->oSGP->clearAll();
        $this->assertTrue($bFlag,'清除所有商品促销规则失败');
    }

// 以下是 商品促销规则模板处理 相关测试用例////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * lib/sales/goods/item/[goods|brand|cat|type]::getOptions测试用例
     * service list: b2c_sales_basic_operator_apps
     * 参看lib/sales/basic/filter::_init_operator
     * 操作符集 在lib/sales/basic/operator下
     *  equal [=,<>]
     *  equal1 [<,<=,>,>=]
     *  null [null,!null]
     *  contain [(),!()]
     *  contain1 [#(),()#]
     *  belong [{},!{}]
     */
    function test_sales_goods_item_getOperators() {
        $this->markTestSkipped('获取指定类型的所有操作符信息');

        // 所有
        $aResult = kernel::single('b2c_sales_goods_item_goods')->getOperators();
        $this->assertTrue( (!empty($aResult) && count($aResult) == 14),"获得所有的操作符集有不对哦!");

        // equal(=,<>) & equal1 (<,<=,>,>=)
        $aResult = kernel::single('b2c_sales_goods_item_goods')->getOperators(array('equal','equal1'));
        $this->assertTrue( (!empty($aResult) && count($aResult) == 6),"获得所有的操作符集有不对哦!");

        // equal(=,<>) & equal1 (<,<=,>,>=) & equal1 pass掉
        $aResult = kernel::single('b2c_sales_goods_item_goods')->getOperators(array('equal','equal1'),true);
        $this->assertTrue( (!empty($aResult) && count($aResult) == 8),"获得所有的操作符集有不对哦!");
    }

    /**
     * 得到标准格式 用于输出模板而用 (goods aggregator)
     */
    function test_sales_goods_aggregator_standard_data() {
        $this->markTestSkipped('一些标准数据输出(goods aggregator)');

        $oAggregator = kernel::single('b2c_sales_goods_aggregator_combine');

        // 默认的aggregator数据 (没有模板 没有数据)
        $aResult = $oAggregator->getStandardData();
        $this->assertTrue( (   is_array($aResult['aggregator'])
                            && $aResult['aggregator']['input'] == 'select'
                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'select'
                            && is_array($aResult['type'])
                            && $aResult['type']['input'] == 'hidden'
                            && $aResult['type']['default'] == 'b2c_sales_goods_aggregator_combine'
                           ),"(aggregator)输出的标准格式不对哦!");

        // 有模板的
        $aTemplate = array(
                        'aggregator'=> array(
                                        'input'=>'hidden',
                                        'default'=>'any',
                                        'desc'=>'以下条件任意一条',
                                      ),
                         'value'=> array(
                                      'input'=>'hidden',
                                      'default'=>0,
                                      'desc'=>'不符合'
                                   )
                     );
        $aResult = $oAggregator->getStandardData($aTemplate);
        $this->assertTrue( (   is_array($aResult['aggregator'])
                            && $aResult['aggregator']['input'] == 'hidden'
                            && $aResult['aggregator']['default'] == 'any'
                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'hidden'
                            && $aResult['value']['default'] == 0
                            && is_array($aResult['type'])
                            && $aResult['type']['input'] == 'hidden'
                            && $aResult['type']['default'] == 'b2c_sales_goods_aggregator_combine'
                           ),"(aggregator)输出的标准格式不对哦!");

        // 模板 + 数据
        $aData= array( // todo:这个是杜撰的哈
                    'type' => 'x',
                    'aggregator'=>'a',
                    'value'=>'v',
                    'condition'=>array()
                );
        $aResult = $oAggregator->getStandardData($aTemplate,$aData);
        $this->assertTrue( (   is_array($aResult['aggregator'])
                            && $aResult['aggregator']['input'] == 'hidden'
                            && $aResult['aggregator']['default'] == 'a'
                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'hidden'
                            && $aResult['value']['default'] == 'v'
                            && is_array($aResult['type'])
                            && $aResult['type']['input'] == 'hidden'
                            && $aResult['type']['default'] == 'x'
                           ),"(aggregator)输出的标准格式不对哦!");
    }

    /**
     * 得到标准格式 用于输出模板而用 (goods item)
     */
    function test_sales_goods_item_standard_data() {
        $this->markTestSkipped('一些标准数据输出(goods item)');

        /**
         * b2c_sales_goods_item_goods
         * b2c_sales_goods_item_brand
         * b2c_sales_goods_item_cat
         * b2c_sales_goods_item_type
         *
         * 条件只能是 对象里getItem提供的 key
         */
        $oItem = kernel::single('b2c_sales_goods_item_goods');

        // 默认的aggregator数据 (没有模板 没有数据)
        $aResult = $oItem->getStandardData();
        $this->assertTrue( ($aResult == false ),"条件的话 如果都为空 则返回空");

        // 有模板的
        $aTemplate = array(
                        'type'=> array(
                                        'input'=>'text',
                                        'default'=>'t',
                                        'desc'=>'type',
                                      ),
                         'attribute'=> array(
                                          'input'=>'hidden',
                                          'default'=>'goods_goods_id',
                                          'desc'=>'商品编号'
                                       ),
                         'operator'=> array(
                                        'input'=>'hidden',
                                        'default'=>'=',
                                        'desc'=>'等于'
                                      ),
                         'value'=> array(
                                      'input'=>'text',
                                      'default'=>0,
                                   )
                     );
        $aResult = $oItem->getStandardData($aTemplate);
        $this->assertTrue( (   is_array($aResult['type'])
                            && $aResult['type']['input'] == 'text'
                            && $aResult['type']['default'] == 't'

                            && is_array($aResult['attribute'])
                            && $aResult['attribute']['input'] == 'hidden'
                            && $aResult['attribute']['default'] == 'goods_goods_id'

                            && is_array($aResult['operator'])
                            && $aResult['operator']['input'] == 'hidden'
                            && $aResult['operator']['default'] == '='

                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'text'
                            && $aResult['value']['default'] == 0
                           ),"(condition)输出的标准格式不对哦!");

        // 模板 + 数据
        $aData= array( // todo:这个是杜撰的哈
                    'type' => 'x',
                    'attribute'=>'a',
                    'operator'=>'o',
                    'value'=>'v',
                );
        $aResult = $oItem->getStandardData($aTemplate,$aData);
        $this->assertTrue( (   is_array($aResult['type'])
                            && $aResult['type']['input'] == 'text'
                            && $aResult['type']['default'] == 'x'

                            && is_array($aResult['attribute'])
                            && $aResult['attribute']['input'] == 'hidden'
                            && $aResult['attribute']['default'] == 'a'

                            && is_array($aResult['operator'])
                            && $aResult['operator']['input'] == 'hidden'
                            && $aResult['operator']['default'] == 'o'

                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'text'
                            && $aResult['value']['default'] == 'v'
                           ),"(condition)输出的标准格式不对哦!");
    }

    /**
     * 生成html(goods promotion) only data 解数据 ok 2010-05-14 18:17
     */
    function test_sales_template_goods_html_only_data() {
        // 这个不知道怎么断言了 可以用error_log打出来生成html文件进行测试
        $this->markTestSkipped("only data");
        $sHtml = $this->oSGP->getTemplate(array(),$this->rule_goods[0]);
        //echo $sHtml;
        //error_log($sHtml,3,'d:/g_tpl_1.html');
    }

    /**
     * 生成html(goods promotion) only tpl 解模板
     */
    function test_sales_template_goods_tpl() {
        $this->markTestSkipped("only template");
        $aTempalte = array(
                        'conditions'=> array(
                                          0=>array(
                                               'type'=>'b2c_sales_goods_item_goods',
                                               'attribute'=>'goods_goods_id',
                                          ),
                                          1=>array(
                                               'type'=>'b2c_sales_goods_item_brand',
                                               'attribute'=>'brand_brand_name',
                                          ),
                                          2=>array(
                                               'type'=>'b2c_sales_goods_aggregator_combine',
                                               'conditions'=> array(
                                                                 0=>array(
                                                                     'type'=>'b2c_sales_goods_item_goods',
                                                                     'attribute'=>'goods_mktprice',
                                                                 ),
                                                                 1=>array(
                                                                      'type'=>'b2c_sales_goods_aggregator_combine',
                                                                      'aggregator'=>'any',
                                                                      'vaule'=>'1',
                                                                      'conditions'=> array(
                                                                                        0=>array(
                                                                                                 'type'=>'b2c_sales_goods_item_goods',
                                                                                                 'attribute'=>'goods_cost',
                                                                                           ),
                                                                                        1=>array(
                                                                                              'type'=>'b2c_sales_goods_item_goods',
                                                                                              'attribute'=>array(
                                                                                                              'default'=>'goods_goods_id',
                                                                                                           )
                                                                                         ),
                                                                                         2=>array(
                                                                                                 'type'=>'b2c_sales_goods_item_goods',
                                                                                                 'attribute'=>'goods_price',
                                                                                         ),
                                                                                     )
                                                                 ),
                                                              )
                                          )
                         )
                     );
        $sHtml = $this->oSGP->makeTemplate($aTempalte);
        //echo $sHtml;
        error_log($sHtml,3,'d:/g_tpl_2.html');
    }

    /**
     * 生成html(goods promotion) data and tpl 解模板+数据
     */
    function test_sales_template_goods_data_and_tpl() {
        $this->markTestSkipped("template+data");
        $sHtml = $this->oSGP->getTemplate(array(
                                               'type'=>'b2c_sales_goods_aggregator_combine',
                                               'conditions'=>
                                                         array(0=>array(
                                                                     'type'=>'b2c_sales_goods_item_goods',
                                                                     'attribute'=>'goods_goods_id',
                                                                     'value'=>array(
                                                                                'input'=>'text',
                                                                              )
                                                                   )
                                                          )
                                                ),unserialize($this->rule_goods[2]['conditions']));
        // echo $sHtml;
        // error_log($sHtml,3,'d:/g_tpl_3.html');
    }

// 以下是 订单促销规则 相关测试用例////////////////////////////////////////////////////////////////////////////////////////////

    function test_sales_rule_order_first() {
        $this->markTestSkipped('清空所有规则');
    }

// 以下是sales_template_order 相关测试用例////////////////////////////////////////////////////////////////////////////////////////
    /**
     * 生成(order promotion) aggreagor 聚合层 标准数据
     */
    function test_sales_order_aggregator_standard() {
        $this->markTestSkipped("promtion order(aggregator standard data)");

        ///// combine /////////////////////////////////////////////////////////////////////////////////////////////////
        $oAggregator = kernel::single('b2c_sales_order_aggregator_combine');

        // 默认的aggregator数据 (没有模板 没有数据)
        $aResult = $oAggregator->getStandardData();
        $this->assertTrue( (   is_array($aResult['aggregator'])
                    && $aResult['aggregator']['input'] == 'select'
                    && is_array($aResult['value'])
                    && $aResult['value']['input'] == 'select'
                    && is_array($aResult['type'])
                    && $aResult['type']['input'] == 'hidden'
                    && $aResult['type']['default'] == 'b2c_sales_order_aggregator_combine'
                   ),"(aggregator::combine)输出的标准格式不对哦!");

                // 有模板的
        $aTemplate = array(
                        'aggregator'=> array(
                                        'input'=>'hidden',
                                        'default'=>'any',
                                        'desc'=>'以下条件任意一条',
                                      ),
                         'value'=> array(
                                      'input'=>'hidden',
                                      'default'=>0,
                                      'desc'=>'不符合'
                                   )
                     );
        $aResult = $oAggregator->getStandardData($aTemplate);
        $this->assertTrue( (   is_array($aResult['aggregator'])
                            && $aResult['aggregator']['input'] == 'hidden'
                            && $aResult['aggregator']['default'] == 'any'
                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'hidden'
                            && $aResult['value']['default'] == 0
                            && is_array($aResult['type'])
                            && $aResult['type']['input'] == 'hidden'
                            && $aResult['type']['default'] == 'b2c_sales_order_aggregator_combine'
                           ),"(aggregator::combine)输出的标准格式不对哦!");

        // 模板 + 数据
        $aData= array( // todo:这个是杜撰的哈
                    'type' => 'x',
                    'aggregator'=>'a',
                    'value'=>'v',
                    'condition'=>array()
                );
        $aResult = $oAggregator->getStandardData($aTemplate,$aData);
        $this->assertTrue( (   is_array($aResult['aggregator'])
                            && $aResult['aggregator']['input'] == 'hidden'
                            && $aResult['aggregator']['default'] == 'a'
                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'hidden'
                            && $aResult['value']['default'] == 'v'
                            && is_array($aResult['type'])
                            && $aResult['type']['input'] == 'hidden'
                            && $aResult['type']['default'] == 'x'
                           ),"(aggregator::combine)输出的标准格式不对哦!");

        ///// found /////////////////////////////////////////////////////////////////////////////////////////////////
        $oAggregator = kernel::single('b2c_sales_order_aggregator_found');

        // 默认的aggregator数据 (没有模板 没有数据)
        $aResult = $oAggregator->getStandardData();
        $this->assertTrue( (   is_array($aResult['aggregator'])
                    && $aResult['aggregator']['input'] == 'select'
                    && is_array($aResult['value'])
                    && $aResult['value']['input'] == 'select'
                    && is_array($aResult['type'])
                    && $aResult['type']['input'] == 'hidden'
                    && $aResult['type']['default'] == 'b2c_sales_order_aggregator_combine'
                   ),"(aggregator::found)输出的标准格式不对哦!");

        // 有模板的
        $aTemplate = array(
                        'aggregator'=> array(
                                        'input'=>'hidden',
                                        'default'=>'any',
                                        'desc'=>'以下条件任意一条',
                                      ),
                         'value'=> array(
                                      'input'=>'hidden',
                                      'default'=>0,
                                      'desc'=>'不符合'
                                   )
                     );
        $aResult = $oAggregator->getStandardData($aTemplate);
        $this->assertTrue( (   is_array($aResult['aggregator'])
                            && $aResult['aggregator']['input'] == 'hidden'
                            && $aResult['aggregator']['default'] == 'any'
                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'hidden'
                            && $aResult['value']['default'] == 0
                            && is_array($aResult['type'])
                            && $aResult['type']['input'] == 'hidden'
                            && $aResult['type']['default'] == 'b2c_sales_order_aggregator_combine'
                           ),"(aggregator::found)输出的标准格式不对哦!");

        // 模板 + 数据
        $aData= array( // todo:这个是杜撰的哈
                    'type' => 'x',
                    'aggregator'=>'a',
                    'value'=>'v',
                    'condition'=>array()
                );
        $aResult = $oAggregator->getStandardData($aTemplate,$aData);
        $this->assertTrue( (   is_array($aResult['aggregator'])
                            && $aResult['aggregator']['input'] == 'hidden'
                            && $aResult['aggregator']['default'] == 'a'
                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'hidden'
                            && $aResult['value']['default'] == 'v'
                            && is_array($aResult['type'])
                            && $aResult['type']['input'] == 'hidden'
                            && $aResult['type']['default'] == 'x'
                           ),"(aggregator::found)输出的标准格式不对哦!");

        ///// subselect /////////////////////////////////////////////////////////////////////////////////////////////
        $oAggregator = kernel::single('b2c_sales_order_aggregator_subselect');

        // 默认的aggregator数据 (没有模板 没有数据)
        $aResult = $oAggregator->getStandardData();
        $this->assertTrue( (   is_array($aResult['attribute'])
                            && $aResult['attribute']['input'] == 'select'
                            && count($aResult['attribute']['options']) == 5

                            && is_array($aResult['operator'])
                            && $aResult['operator']['input'] == 'select'
                            && count($aResult['operator']['options']) == 6

                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'text'
                            && $aResult['value']['vtype'] == 'number'

                            && is_array($aResult['type'])
                            && $aResult['type']['input'] == 'hidden'
                            && $aResult['type']['default'] == 'b2c_sales_order_aggregator_combine'
                           ),"(aggregator::subselect)输出的标准格式不对哦!");

        // 有模板的
        $aTemplate = array(
                        'type'=>'b2c_sales_order_aggregator_subselect',
                        'attribute'=> array(
                                        'default'=>'order_subtotal',
                                      ),
                        'operator'=> array(
                                        'default'=>'>=',
                                      ),
                     );
        $aResult = $oAggregator->getStandardData($aTemplate);
        $this->assertTrue( (   is_array($aResult['attribute'])
                            && $aResult['attribute']['input'] == 'select'
                            && $aResult['attribute']['default'] == 'order_subtotal'

                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'text'

                            && is_array($aResult['operator'])
                            && $aResult['operator']['input'] == 'select'
                            && $aResult['operator']['default'] == '>='

                            && is_array($aResult['type'])
                            && $aResult['type']['input'] == 'hidden'
                            && $aResult['type']['default'] == 'b2c_sales_order_aggregator_subselect'
                           ),"(aggregator::subselect)输出的标准格式不对哦!");

        // 模板 + 数据
        $aData= array( // todo:这个是杜撰的哈
                    'type' => 'x',
                    'attribute'=>'a',
                    'operator'=>'o',
                    'value'=>'v',
                    'condition'=>array()
                );
        $aResult = $oAggregator->getStandardData($aTemplate,$aData);
        $this->assertTrue( (   is_array($aResult['attribute'])
                            && $aResult['attribute']['input'] == 'select'
                            && $aResult['attribute']['default'] == 'a'

                            && is_array($aResult['operator'])
                            && $aResult['operator']['input'] == 'select'
                            && $aResult['operator']['default'] == 'o'

                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'text'
                            && $aResult['value']['default'] == 'v'

                            && is_array($aResult['type'])
                            && $aResult['type']['input'] == 'hidden'
                            && $aResult['type']['default'] == 'x'
                           ),"(aggregator::found)输出的标准格式不对哦!");

        ///// item /////////////////////////////////////////////////////////////////////////////////////////////////
        $oAggregator = kernel::single('b2c_sales_order_aggregator_item');

        // 默认的aggregator数据 (没有模板 没有数据)
        $aResult = $oAggregator->getStandardData();
        $this->assertTrue( (   is_array($aResult['aggregator'])
                    && $aResult['aggregator']['input'] == 'select'
                    && is_array($aResult['value'])
                    && $aResult['value']['input'] == 'select'
                    && is_array($aResult['type'])
                    && $aResult['type']['input'] == 'hidden'
                    && $aResult['type']['default'] == 'b2c_sales_order_aggregator_item'
                   ),"(aggregator::item)输出的标准格式不对哦!");

                // 有模板的
        $aTemplate = array(
                        'aggregator'=> array(
                                        'input'=>'hidden',
                                        'default'=>'any',
                                        'desc'=>'以下条件任意一条',
                                      ),
                         'value'=> array(
                                      'input'=>'hidden',
                                      'default'=>0,
                                      'desc'=>'不符合'
                                   )
                     );
        $aResult = $oAggregator->getStandardData($aTemplate);
        $this->assertTrue( (   is_array($aResult['aggregator'])
                            && $aResult['aggregator']['input'] == 'hidden'
                            && $aResult['aggregator']['default'] == 'any'
                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'hidden'
                            && $aResult['value']['default'] == 0
                            && is_array($aResult['type'])
                            && $aResult['type']['input'] == 'hidden'
                            && $aResult['type']['default'] == 'b2c_sales_order_aggregator_item'
                           ),"(aggregator::item)输出的标准格式不对哦!");

        // 模板 + 数据
        $aData= array( // todo:这个是杜撰的哈
                    'type' => 'x',
                    'aggregator'=>'a',
                    'value'=>'v',
                    'condition'=>array()
                );
        $aResult = $oAggregator->getStandardData($aTemplate,$aData);
        $this->assertTrue( (   is_array($aResult['aggregator'])
                            && $aResult['aggregator']['input'] == 'hidden'
                            && $aResult['aggregator']['default'] == 'a'
                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'hidden'
                            && $aResult['value']['default'] == 'v'
                            && is_array($aResult['type'])
                            && $aResult['type']['input'] == 'hidden'
                            && $aResult['type']['default'] == 'x'
                           ),"(aggregator::item)输出的标准格式不对哦!");
    }

     /**
     * 得到标准格式 用于输出模板而用 (order item)
     */
    function test_sales_order_item_standard_data() {
        $this->markTestSkipped('一些标准数据输出(order item)');

        /**
         * b2c_sales_order_item_goods
         * b2c_sales_order_item_order
         *
         * 条件只能是 对象里getItem提供的 key
         */
        $oItem = kernel::single('b2c_sales_order_item_goods');

        // 默认的aggregator数据 (没有模板 没有数据)
        $aResult = $oItem->getStandardData();
        $this->assertTrue( ($aResult == false ),"条件的话 如果都为空 则返回空");

        // 有模板的
        $aTemplate = array(
                        'type'=> array(
                                        'input'=>'text',
                                        'default'=>'t',
                                        'desc'=>'type',
                                      ),
                         'attribute'=> array(
                                          'input'=>'hidden',
                                          'default'=>'goods_goods_id',
                                          'desc'=>'商品编号'
                                       ),
                         'operator'=> array(
                                        'input'=>'hidden',
                                        'default'=>'=',
                                        'desc'=>'等于'
                                      ),
                         'value'=> array(
                                      'input'=>'text',
                                      'default'=>0,
                                   )
                     );
        $aResult = $oItem->getStandardData($aTemplate);
        $this->assertTrue( (   is_array($aResult['type'])
                            && $aResult['type']['input'] == 'text'
                            && $aResult['type']['default'] == 't'

                            && is_array($aResult['attribute'])
                            && $aResult['attribute']['input'] == 'hidden'
                            && $aResult['attribute']['default'] == 'goods_goods_id'

                            && is_array($aResult['operator'])
                            && $aResult['operator']['input'] == 'hidden'
                            && $aResult['operator']['default'] == '='

                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'dialog'
                            && $aResult['value']['default'] == 0
                           ),"(condition)输出的标准格式不对哦!");

        // 模板 + 数据
        $aData= array( // todo:这个是杜撰的哈
                    'type' => 'x',
                    'attribute'=>'a',
                    'operator'=>'o',
                    'value'=>'v',
                );
        $aResult = $oItem->getStandardData($aTemplate,$aData);
        $this->assertTrue( (   is_array($aResult['type'])
                            && $aResult['type']['input'] == 'text'
                            && $aResult['type']['default'] == 'x'

                            && is_array($aResult['attribute'])
                            && $aResult['attribute']['input'] == 'hidden'
                            && $aResult['attribute']['default'] == 'a'

                            && is_array($aResult['operator'])
                            && $aResult['operator']['input'] == 'hidden'
                            && $aResult['operator']['default'] == 'o'

                            && is_array($aResult['value'])
                            && $aResult['value']['input'] == 'text'
                            && $aResult['value']['default'] == 'v'
                           ),"(condition)输出的标准格式不对哦!");
    }

    /**
     * 生成HTML(order promotion) conditions条件 only data
     */
    function test_sales_order_conditions_tpl_data() {
        $this->markTestSkipped("order promotion conditions data");
        // found
        $aTemplate = array();
        $aData = $this->rule_order[0]['conditions'];
        $sHtml =  $this->oSOP->getConditionTemplate($aTemplate,$aData);
        //print_r($sHtml);
        //error_log($sHtml,3,'d:/o_tpl_1.html');

        // address (订单项条件)
        $aData = $this->rule_order[1]['conditions'];
        $sHtml =  $this->oSOP->getConditionTemplate($aTemplate,$aData);
        //print_r($sHtml);
        //error_log($sHtml,3,'d:/o_tpl_1.html');

        // subselect + address
        $aData =  $this->rule_order[2]['conditions'];
        $sHtml =  $this->oSOP->getConditionTemplate($aTemplate,$aData);
        //print_r($sHtml);
        //error_log($sHtml,3,'d:/o_tpl_1.html');
    }

    /**
     * 生成HTML(order promotion) action_conditions条件 only data
     */
    function test_sales_order_action_condtions_tpl_data() {
        $this->markTestSkipped("order promotion action_conditions data");

        $aTemplate = null;
        $aData = $this->rule_order[0]['action_conditions'];

        $sHtml =  $this->oSOP->getActionConditionTemplate($aTemplate,$aData);
        //print_r($sHtml);
        //error_log($sHtml,3,'d:/o_tpl_2.html');
    }

    /**
     * 生成HTML(order promotion) conditions条件 only template
     */
    function test_sales_template_order_conditions_template() {
        $this->markTestSkipped("order promotion conditions template");

        $aData = array();
        // simple config
        $aTemplate['info'] = $this->tpl_order[0]['conditions'];
        $sHtml =  $this->oSOP->getConditionTemplate($aTemplate,$aData);
        //print_r($sHtml);
        //error_log($sHtml,3,'d:/o_tpl_3.html');

        // complex config
        $aTemplate['info'] = $this->tpl_order[1]['conditions'];
        $sHtml =  $this->oSOP->getConditionTemplate($aTemplate,$aData);
        //print_r($sHtml);
        error_log($sHtml,3,'d:/o_tpl_3.html');
    }

    /**
     * 生成HTML(order promotion) action_conditions条件 only template
     */
    function test_sales_template_order_action_conditions_template() {
        $this->markTestSkipped("order promotion action_conditions template");
        // simple config
        $aTemplate['info'] = $this->tpl_order[0]['action_conditions'];
        $aData = null;
        $sHtml =  $this->oSOP->getActionConditionTemplate($aTemplate,$aData);
        //print_r($sHtml);
        //error_log($sHtml,3,'d:/o_tpl_4.html');

        // complex config
        $aTemplate['info'] = $this->tpl_order[1]['action_conditions'];
        $sHtml =  $this->oSOP->getActionConditionTemplate($aTemplate,$aData);
        //print_r($sHtml);
        //error_log($sHtml,3,'d:/o_tpl_4.html');
    }

    /**
     * 生成HTML(order promotion) conditions条件 template and data
     */
    function test_sales_template_order_conditions_template_and_data() {
        $this->markTestSkipped("order promotion conditions template and data");

        $aTemplate['info'] = $this->tpl_order[2]['conditions'];
        $aData = $this->rule_order[2]['conditions'];
        $sHtml =  $this->oSOP->getConditionTemplate($aTemplate,$aData);
        //print_r($sHtml);
        //error_log($sHtml,3,'d:/o_tpl_5.html');
    }

    /**
     * 生成HTML(order promotion) action_conditions条件 template and data
     */
    function test_sales_template_order_action_conditions_template_and_data() {
        $this->markTestSkipped("order promotion action_conditions template and data");
        $aTemplate['info'] = $this->tpl_order[2]['action_conditions'];
        $aData = $this->rule_order[2]['action_conditions'];
        $sHtml = $this->oSOP->getActionConditionTemplate($aTemplate,$aData);
        //print_r($sHtml);
        error_log($sHtml,3,'d:/o_tpl_6.html');
    }
}
?>
