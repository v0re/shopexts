<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 促销规则 operator 的测试用例
 * lib/sales/basic/operator/xxx.php
 * $ 2010-05-20 15:22 $
 */
class sales_operator extends PHPUnit_Framework_TestCase
{
    function setUp(){

    }

    /**
     * lib/sales/basic/operator/equal.php 的测试用例
     *
     */
    function test_equal() {
        $oOperator = kernel::single('b2c_sales_basic_operator_equal');
        // 存在方法
        //print_r(get_class_methods(get_class($oOperator)));
        $this->assertTrue((method_exists($oOperator,'getOperators')
                           && method_exists($oOperator,'validate')
                           && method_exists($oOperator,'getString')),'不是一个完整的operator运行一定会出错的');
        // getItem
        $aResult = $oOperator->getOperators();
        $this->assertTrue((is_array($aResult)
                           && is_array($aResult['='])
                           && is_array($aResult['<>'])),'少了一些操作符');
       // getString
       $aCondition = array(
                        'attribute'=>'a',
                        'operator'=>'=',
                        'value'=>'v'
                     );
       $this->assertTrue(($oOperator->getString($aCondition) == "a='v' "),'getString处理有问题');

       $aCondition = array(
                        'attribute'=>array(
                                        'attribute'=>'brand_name',
                                        'ref_id'=>'brand_id',
                                        'table'=>'sdb_b2c_brand',
                                        'pkey'=>'brand_id'
                                     ),
                        'operator'=>'=',
                        'value'=>'v'
                     );
       $this->assertTrue(($oOperator->getString($aCondition) == " brand_id IN (SELECT `brand_id` FROM sdb_b2c_brand WHERE brand_name='v' ) "),'getString处理有问题');

       // validate
       $this->assertTrue($oOperator->validate('=',1,1),'验证错误喽');
       $this->assertFalse($oOperator->validate('=',1,2),'验证错误喽');
       $this->assertTrue($oOperator->validate('<>',1,2),'验证错误喽');
       $this->assertFalse($oOperator->validate('<>',1,1),'验证错误喽');

       $this->assertTrue($oOperator->validate('=','test','test'),'验证错误喽');
       $this->assertFalse($oOperator->validate('<>','test','test'),'验证错误喽');
       $this->assertTrue($oOperator->validate('<>','test','test1'),'验证错误喽');

       $this->assertTrue($oOperator->validate('=','中文','中文'),'验证错误喽');
       $this->assertTrue($oOperator->validate('<>','國','国'),'验证错误喽');
    }

    /**
     * lib/sales/basic/operator/equal1.php 的测试用例
     *
     */
    function test_equal1() {
        $oOperator = kernel::single('b2c_sales_basic_operator_equal1');
        // 存在方法
        $this->assertTrue((method_exists($oOperator,'getOperators')
                           && method_exists($oOperator,'validate')
                           && method_exists($oOperator,'getString')),'不是一个完整的operator运行一定会出错的');
        // getItem
        $aResult = $oOperator->getOperators();
        $this->assertTrue((is_array($aResult)
                           && is_array($aResult['>='])
                           && is_array($aResult['>'])
                           && is_array($aResult['<='])
                           && is_array($aResult['>'])),'少了一些操作符');

       // getString
       $aCondition = array(
                        'attribute'=>'a',
                        'operator'=>'>',
                        'value'=>'v'
                     );
       $this->assertTrue(($oOperator->getString($aCondition) == "a > v "),'getString处理有问题');

       // validate
       $this->assertTrue($oOperator->validate('>',1,2),'验证错误喽');
       $this->assertTrue($oOperator->validate('>=',1,2),'验证错误喽');
       $this->assertTrue($oOperator->validate('<',2,1),'验证错误喽');
       $this->assertFalse($oOperator->validate('<=',1,3),'验证错误喽');
       //exit;
    }

    /**
     * lib/sales/basic/operator/null.php 的测试用例
     *
     */
    function test_null() {
        $oOperator = kernel::single('b2c_sales_basic_operator_null');
        // 存在方法
        $this->assertTrue((method_exists($oOperator,'getOperators')
                           && method_exists($oOperator,'validate')
                           && method_exists($oOperator,'getString')),'不是一个完整的operator运行一定会出错的');
        // getItem
        $aResult = $oOperator->getOperators();
        $this->assertTrue((is_array($aResult)
                           && is_array($aResult['null'])
                           && is_array($aResult['!null'])),'少了一些操作符');

       // getString
       $aCondition = array(
                        'attribute'=>'a',
                        'operator'=>'null',
                        'value'=>'v'
                     );
       $this->assertTrue(($oOperator->getString($aCondition) == "a IS NULL "),'getString处理有问题');

       // validate
       $this->assertTrue($oOperator->validate('null',1,''),'验证错误喽');
       $this->assertTrue($oOperator->validate('!null',1,111),'验证错误喽');
       //exit;
    }

    /**
     * lib/sales/basic/operator/belong.php 的测试用例
     *
     */
    function test_belong() {
        $oOperator = kernel::single('b2c_sales_basic_operator_belong');
        // 存在方法
        $this->assertTrue((method_exists($oOperator,'getOperators')
                           && method_exists($oOperator,'validate')
                           && method_exists($oOperator,'getString')),'不是一个完整的operator运行一定会出错的');
        // getItem
        $aResult = $oOperator->getOperators();
        $this->assertTrue((is_array($aResult)
                           && is_array($aResult['{}'])
                           && is_array($aResult['!{}'])),'少了一些操作符');

       // getString
       $aCondition = array(
                        'attribute'=>'a',
                        'operator'=>'{}',
                        'value'=>'v'
                     );
       // 目前return false 2010-05-20 16:52 wubin
       $this->assertTrue(($oOperator->getString($aCondition) === false),'getString处理有问题');

       // validate
       $this->assertTrue($oOperator->validate('{}',1,array(1,2,3)),'验证错误喽');
       $this->assertTrue($oOperator->validate('!{}',4,array(1,2,3)),'验证错误喽');
       //exit;
    }

    /**
     * lib/sales/basic/operator/contain.php 的测试用例
     *
     */
    function test_contain() {
        $oOperator = kernel::single('b2c_sales_basic_operator_contain');
        // 存在方法
        $this->assertTrue((method_exists($oOperator,'getOperators')
                           && method_exists($oOperator,'validate')
                           && method_exists($oOperator,'getString')),'不是一个完整的operator运行一定会出错的');
        // getItem
        $aResult = $oOperator->getOperators();
        $this->assertTrue((is_array($aResult)
                           && is_array($aResult['()'])
                           && is_array($aResult['!()'])),'少了一些操作符');

       // getString
       $aCondition = array(
                        'attribute'=>'a',
                        'operator'=>'()',
                        'value'=>'v'
                     );
       $this->assertTrue(($oOperator->getString($aCondition) == "a LIKE '%v%'"),'getString处理有问题');

       $aCondition = array(
                        'attribute'=>'a',
                        'operator'=>'()',
                        'value'=>array(1,2)
                     );
       $this->assertTrue(($oOperator->getString($aCondition) == "a IN ('1','2')"),'getString处理有问题');

       $aCondition = array(
                        'attribute'=>'a',
                        'operator'=>'!()',
                        'value'=>array(1,2)
                     );
       $this->assertTrue(($oOperator->getString($aCondition) == "a NOT IN ('1','2')"),'getString处理有问题');

       // validate
       $this->assertTrue($oOperator->validate('()','str','strteset'),'验证错误喽');
       $this->assertTrue($oOperator->validate('!()','xxx','teststr'),'验证错误喽');
       $this->assertTrue($oOperator->validate('()','xxx',array('xxx',1,2)),'验证错误喽');
       $this->assertTrue($oOperator->validate('!()','xxx',array(3,1,2)),'验证错误喽');
    }

    /**
     * lib/sales/basic/operator/contain1.php 的测试用例
     *
     */
    function test_contain1() {
        $oOperator = kernel::single('b2c_sales_basic_operator_contain1');
        // 存在方法
        $this->assertTrue((method_exists($oOperator,'getOperators')
                           && method_exists($oOperator,'validate')
                           && method_exists($oOperator,'getString')),'不是一个完整的operator运行一定会出错的');
        // getItem
        $aResult = $oOperator->getOperators();
        $this->assertTrue((is_array($aResult)
                           && is_array($aResult['#()'])
                           && is_array($aResult['()#'])),'少了一些操作符');

       // getString
       $aCondition = array(
                        'attribute'=>'a',
                        'operator'=>'#()',
                        'value'=>'v'
                     );
       $this->assertTrue(($oOperator->getString($aCondition) == "a  LIKE 'v%'"),'getString处理有问题');

       // validate
       $this->assertTrue($oOperator->validate('#()','str','strteset'),'验证错误喽');
       $this->assertTrue($oOperator->validate('()#','str','teststr'),'验证错误喽');
    }

}
?>
