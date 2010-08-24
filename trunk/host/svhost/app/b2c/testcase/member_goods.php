<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class member_goods extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->model = app::get('b2c')->model('member_comments');
    }

    public function testInsert(){
         $table = new base_application_dbtable;
         $table->detect('b2c','member_comments')->install(); 
         $table->detect('b2c','member_goods')->install();  
         $table->detect('b2c','member_msg')->install(); 
         exit;

}
}
