<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class memberlv extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
 
        $this->db = kernel::database();   
        $this->model = app::get('b2c')->model('member_lv');
        if(!$this->is_exist_table('sdb_b2c_member_lv') ){        
            $table = new base_application_dbtable;
            $table->detect('b2c','member_lv')->install();   
        }
        
    }
    
    private function is_exist_table($tbname){
        $tables = $this->db->select('show tables');
        foreach(array_values($tables) as $val){
            $tblist[]  = current($val);
        }

        return in_array($tbname,$tblist);
    }
    


    public function testInsert(){
      kernel::database()->exec('delete from sdb_b2c_member_lv');

      $data['name'] = '普通会员';
      $data['default_lv'] = 1;
      $data['dis_count'] = 1;
      $data['point'] = 0;   

      $this->model->save($data);
      
      $data['name'] = '高级会员';
      $data['default_lv'] = 0;
      $data['dis_count'] = 1;
      $data['point'] = 1000;   

      $this->model->save($data);

    }
    
    function testGetDefauleLv(){
        $ret = $this->model->get_default_lv();
        var_dump($ret);
    }
    

}
