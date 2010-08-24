<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class memberattr extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->db = kernel::database();   
        $this->model = app::get('b2c')->model('member_attr');
    }
    
    /*
    public function testSaveOfAdd(){
        $ext_attr = array(
            array('attr_name'=>'QQ', 'attr_column'=>'qq','attr_type'=>'text','attr_required'=>'false','attr_search'=>'true','attr_option'=>'','attr_show'=>'false','attr_valtype'=>'','attr_tyname'=>'QQ','attr_order'=>'11','attr_group'=>'contact'),
            array('attr_name'=>'MSN','attr_column'=>'msn','attr_type'=>'text','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'false','attr_valtype'=>'email','attr_tyname'=>'MSN','attr_order'=>'12','attr_group'=>'contact'),
            array('attr_name'=>'Skype','attr_column'=>'skype','attr_type'=>'text','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'false','attr_valtype'=>'alphaint','attr_tyname'=>'Skype','attr_order'=>'13','attr_group'=>'contact'),
            array('attr_name'=>__('旺旺'),'attr_column'=>'wangwang','attr_type'=>'text','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'false','attr_valtype'=>'','attr_tyname'=>__('旺旺'),'attr_order'=>'14','attr_group'=>'contact'),
        );
        $attr_model = app::get('b2c')->model('member_attr');
        foreach($ext_attr as $item){
            #$this->model->save($item);
        }
        //

    }*/
    
    
    function testInit(){
        $this->model->init();
        $all = $this->model->getList();
    }
    /*
    function testGenForm(){
        $html = $this->model->gen_form();
        echo $html;
    }    */


}
