<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class goods extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->model = app::get('b2c')->model('goods');
    }

    public function testInsert(){
//        $row = $this->model->db->exec('delete from sdb_members');
        $goodsData = array(
            'type'=>array(
                'type_id'=>'2'
            ),
            'products'=>'1',
            //    'goods_id'=>'',
            'title'=>'just a goods',
            'url'=>'http://234',
            'status'=>'true',
            'goods_type'=>'normal',
            'unit' => 'lit',
    //        'link'=>array(
    //            'url'=>'http://afeggg',
    //            'type'=>'',
    //            'option'=>''
    //        ),
            'last_modified'=>time(),
            'keywords' => array(
                array(
                    'keyword'=>'aabbcc1',
                    'res_type'=>'goods'
                ),
                array(
                    'keyword'=>'aabbcc21',
                    'res_type'=>'goods'
                ),
            ),
            'brief'=>'goods brief',
            'props'=>array(
                'p_1'=>array(
                    'key'=>'jkljlkj',
                    'value'=>2,
                    'desc'=>'redww'
                ),
                'p_2'=>array(
                    'key'=>'fefe',
                    'value'=>3,
                    'desc'=>'gegeg'
                ),
                'p_21'=>array(
                    'key'=>'gge',
                    'value'=>'alialialialialialia'
                )
            ),
            /*
            'orderinfo'=>array(
                
                ),
             */
            'adjunct'=>array(
                array(
                    'title'=>'adj title',
                    'min'=>'',
                    'max'=>'',
                    'adj_filter'=>'',
                    'adj_include'=>'',
                    'ajd_exclude'=>''
                ),
                array(
                    'title'=>'adj title',
                    'min'=>'',
                    'max'=>'',
                    'adj_filter'=>'',
                    'adj_include'=>'',
                    'ajd_exclude'=>''
                ),
            ),
            'description'=>'goods des',
            'category'=>array(
                'cat_id'=>'3',
            ),
            'brand'=>array(
                //brand 外键
                'brand_id'=>3,
                'brand_name'=> 'fred perry'
            ),
            'defalut_image'=>array(
                'gimage_id'=>1,
                'is_remote'=>'true',
                'default'=>1,
                'width'=>'320',
                'height'=>'480',
                'source'=>array(
                    'storager'=>'',
                    'url'=>'http://wfewf'
                ),
                'thumbnail'=>array(
                    'storager'=>'',
                    'url'=>'http://ffff',
                ),
                'small'=>array(
                    'storager'=>'',
                    'url'=>'http://ffff',
                ),
                'big'=>array(
                    'storager'=>'',
                    'url'=>'http://ffff',
                ),
            ),
            'adjunct' => array(
                'gwag'=>array(
                    'gewgew'
                    ),
            ),
            'spec'=>array(
                array(
                    'spec_name'=>'color',
                    'spec_id'=>'1',
                    'option'=>array(
                        array(
                            'private_spec_value_id'=>'12345678901',
                            'spec_value_id'=>'2',
                            'spec_value'=>'aaaa',
                            'spec_image'=>'http://fefe',
                            'spec_goods_images'=>'23,124'
                        ),
                        array(
                            'private_spec_value_id'=>'12345678902',
                            'spec_value_id'=>'4',
                            'spec_value'=>'',
                            'spec_image'=>'',
                            'spec_goods_images'=>''
                        ),
                    ),
                ),
                array(
                    'spec_name'=>'size',
                    'spec_id'=>'2',
                    'option'=>array(
                        array(
                            'private_spec_value_id'=>'12345678912',
                            'spec_value_id'=>'12',
                            'spec_value'=>'aaaaccc',
                            'spec_image'=>'',
                            'spec_goods_images'=>'23,124'
                        ),
                        array(
                            'private_spec_value_id'=>'12345678934',
                            'spec_value_id'=>'42',
                            'spec_value'=>'',
                            'spec_image'=>'',
                            'spec_goods_images'=>''
                        ),
                    ),
                )
            ),
            /*
            'image'=>array(
                array(
                    'gimage_id'=>1,
                    'is_remote'=>'true',
                    'default'=>1,
                    'width'=>'320',
                    'height'=>'480',
                    'source'=>array(
                        'storager'=>'',
                        'url'=>'http://wfewf'
                    ),
                    'thumbnail'=>array(
                        'storager'=>'',
                        'url'=>'http://ffff',
                    ),
                    'small'=>array(
                        'storager'=>'',
                        'url'=>'http://ffff',
                    ),
                    'big'=>array(
                        'storager'=>'',
                        'url'=>'http://ffff',
                    ),
                ),
                array(
                    'gimage_id'=>2,
                    'is_remote'=>'true',
                    'width'=>'320',
                    'height'=>'480',
                    'source'=>array(
                        'storager'=>'',
                        'url'=>'http://wfewf'
                    ),
                    'thumbnail'=>array(
                        'storager'=>'',
                        'url'=>'http://ffff',
                    ),
                    'small'=>array(
                        'storager'=>'',
                        'url'=>'http://ffff',
                    ),
                    'big'=>array(
                        'storager'=>'',
                        'url'=>'http://ffff',
                    ),
                ),
            ),
             */
            'product'=>array(
                array(
                    'title'=>'asfee',
                    'default'=>1,
                    'bn'=>'abacc'.time(),
                    'barcode'=>'fe',
                    'unit'=>'lit',
                    'freez'=>'0',
                    'status'=>'true',
                    'price'=>array(
                        'mktprice'=>array(
                            'title'=>'市场价',
                            'price'=>100.00
                        ),
                        'cost'=>array(
                            'title'=>'成本价',
                            'price'=>'50.00'
                        ),
                        'price'=>array(
                            'title'=>'销售价',
                            'price'=>'180.00'
                        ),

                    ),
                    'spec_info'=>'fefe,fefe',
                    'spec_desc'=>'',
                    'store'=>10,
                    'weight'=>100,
                    'last_modifie'=>1234567890
                ),

            ),
        );
//        $subsdf = array(
//            'product'=>array(
//                '*',
//            ),
//       );
//        print_r( $this->model->dump(20,'*',$subsdf) );
        $this->model->save($goodsData);
//        $row = $this->model->db->selectrow('select * from sdb_members');
//        $this->assertEquals($row['member_lv_id'],$memberArr['member_group_id']);
//        $this->assertEquals($row['lang'],$memberArr['lang']);
//        $this->assertEquals($row['cur'],$memberArr['currency']);
    }

}
