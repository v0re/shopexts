<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
/**
 * 购物车测试用例
 * $ 2010-04-29 14:01 $
 */
class cart extends PHPUnit_Framework_TestCase
{
    function setUp() {
        // 调用model
        $this->app = app::get('b2c');
        $this->oCartObject = $this->app->model('cart_objects');
    }








    /****************************************************
     *********      后台调用   **************************
    $data = array('goods'=>array(
                            array(
                                'goods' => array(
                                    'goods_id'   => 4,
                                    'product_id' => 9,
                                    'adjunct' => 'na',
                                    'num' => 2.5, 
                                ),
                             ),
                     ),
                'coupon'=>array(
                            array('coupon'=>'Atetet'),
                            array('coupon'=>'Atest'),
                            )，
                 );
        $t = $this->mCart->get_cart_object($data);
        print_r($t);exit;
      //************************************************/




    /**
     * 商品添加到购物车 $ 2010-04-29 17:17 $
     */
    function test_goods_add() {
        /**
        $aTest['order'] = array(
                    'goods_id'   => array(1),
                    'product_id' => 1,
                    'adjunct' => 'na',
                    'quantity' => 1,
                    'member_id' => 1,
                 );
        $this->app->controller('admin_order')->create($aTest);
        //*/
        $this->markTestSkipped("商品添加到购物车");
        // 商品加入购物车
        
        $aResult = kernel::single('b2c_cart_object_goods')->add($aTest);
        
        $this->assertTrue(($aResult['quantity'] == 1),"商品数据加入购物车失败");

        // 修改 一般是修改数量的哈 +
        $aTest['quantity'] = 3;
        $aResult = kernel::single('b2c_cart_object_goods')->update($aResult['obj_ident'],$aTest['quantity']);
        $this->assertTrue(($aResult['quantity'] == 3),"购物车商品数据修改失败+");

        // 修改 一般是修改数量的哈 -
        $aTest['quantity'] = 2;
        $aResult = kernel::single('b2c_cart_object_goods')->update($aResult['obj_ident'],$aTest['quantity']);
        $this->assertTrue(($aResult['quantity'] == 2),"购物车商品数据修改失败-");

        // 追加
        $aTest['quantity'] = 2;
        $aResult = kernel::single('b2c_cart_object_goods')->add($aTest);
        $this->assertTrue(($aResult['quantity'] == 4),"购物车商品数据修改失败-");
    }

    /**
     * 删除购物车指定ident商品 $ 2010-04-29 18:04 $
     */
    function test_goods_delete() {
        $this->markTestSkipped("删除购物车指定ident商品");
        $sIdnet = 'goods_1_1_na';
        // 返回的数据是
        // array('rs'=>'xxx','sql'=>'xxxxxx');
        $aResult = kernel::single('b2c_cart_object_goods')->delete($sIdnet);
        $this->assertTrue(!empty($aResult),"指定购物车商品数据删除失败");
    }

    /**
     * 删除购物车商品 $ 2010-04-30 13:36 $
     */
    function test_goods_delete_all() {
        $this->markTestSkipped("清空购物车商品数据");
        // 加入一些数据
        // 商品加入购物车
        $aTest = array(
                    'goods_id'   => 1,
                    'product_id' => 1,
                    'adjunct' => 'na',
                    'quantity' => 1,
                 );
        kernel::single('b2c_cart_object_goods')->add($aTest);
        $this->assertTrue(($aTest['goods_id'] == 1),"商品数据2加入购物车失败");

        // 商品加入购物车
        $aTest['goods_id'] = 2;
        kernel::single('b2c_cart_object_goods')->add($aTest);
        $this->assertTrue(($aTest['goods_id'] == 2),"商品数据2加入购物车失败");

        $aResult = kernel::single('b2c_cart_object_goods')->deleteAll();
        $this->assertTrue(!empty($aResult),"清空购物车商品数据失败");
    }

    /**
     * 获取指定购物车商品信息 $ 2010-04-30 13:49 $
     * notice 需要一条真实的商品信息 从前台取到相应的值
     */
    function test_goods_get() {
        $this->markTestSkipped("获取购物车是指定商品的详细数据");
        // 真实商品加入购物车
        $aTest = array(
                    'goods_id'   => 1,
                    'product_id' => 1,
                    'adjunct' => 'na',
                    'quantity' => 1,
                 );
        kernel::single('b2c_cart_object_goods')->add($aTest);
        $this->assertTrue(($aTest['quantity'] == 1),"商品数据加入购物车失败");


        //
    }

    function test_tt() {
        //$this->markTestSkipped("1111");
        //kernel::single('b2c_cart_object_goods')->deleteAll();
        $data = array('goods'=>array(
                            array(
                                'goods' => array(
                                    'goods_id'   => 1,
                                    'product_id' => 2,
                                    'adjunct' => 'na',
                                    'num' => 2.5, 
                                ),
                             ),
                             /**
                             array(
                                'goods' => array(
                                    'goods_id'   => 2,
                                    'product_id' => 3,
                                    'adjunct' => 'na',
                                    'quantity' => 5,
                                ),
                             ),
                             //*/
                             
                     ),
                         'coupon'=>array(array('coupon'=>'Atestse')),
                 );
        //kernel::single('b2c_cart_object_goods')->no_database();
        foreach ($data as $aTest) {
            //kernel::single('b2c_cart_object_goods')->add($aTest);
        }
        
        echo "\r\n\r\n";
        //echo kernel::single('b2c_cart_object_goods')->member_ident = 'hello';//exit;
        //echo kernel::single('b2c_cart_object_goods')->member_ident;
        echo "\r\n\r\n";
        //$t = kernel::single('b2c_cart_object_goods')->getAll();
        //$t = kernel::single('b2c_cart_object_coupon')->getAll();
        //print_r($t);exit;
        //exit('hyello');
        //exit;
        $t = kernel::single("b2c_mdl_cart")->get_cart_object($data);
        print_r($t);
        //$t = kernel::single("b2c_cart_process_get")->process($t);

    }

}
?>
