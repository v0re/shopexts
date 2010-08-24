<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class memberaddr extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->db = kernel::database();   
        $this->model = app::get('b2c')->model('member_addrs');
        if(!$this->_is_exist_table('sdb_b2c_member_addrs') ){        
            $table = new base_application_dbtable;
            $table->detect('b2c','member_addrs')->install();   
        }
  
    }
    
    public function _is_exist_table($tbname){
        $tables = $this->db->select('show tables');
        foreach(array_values($tables) as $val){
            $tblist[]  = current($val);
        }

        return in_array($tbname,$tblist);
    }    

    public function testInsert(){
        $data['member_id'] = 1;
        $data['default'] = 1;
        
        $this->model->save($data);      
        
        $data['phone']['telephone'] = '51086858';
        $this->model->save($data);
    }    
    
    public function testUpdate(){
            
        $data['default'] = 0;
        
        $this->model->update($data,array('addr_id'=>12));
        #$this->model->save($data);
    }
}
