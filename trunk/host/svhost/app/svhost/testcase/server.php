<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class member extends PHPUnit_Framework_TestCase{
   
    static $id;
    static $username;
    
    public function setUp() {
        $this->model = app::get('b2c')->model('members');
    }


    public function testSave(){
        $memberArr = array(
                'pam_account'=>array(
                    'login_name'=>'newuser'.time(),
                    'login_password'=>'admin',
                ),
                'account'=>array(
                    'pw_question'=>'who',
                    'pw_answer'=>'root',
                ),
                'member_lv'=>array(
                    'member_group_id'=>'123',
                ),
                'lang'=>'zh',
                'currency'=>'RMB',
                'contact'=>array(
                    'name'=>'aasdf2',
                    'firstname'=>'asdf',
                    'lastname'=>'asdfaf',
                    'zipcode'=>'123',
                    'addr'=>'addr',
                    'email'=>'asdsaf@fdfaf',
                    'phone'=>array(
                        'mobile' => '12345',
                        'telephone' => '6464',
                    ),
                    'area'=>array(
                        'area_type' => 'mainland',
                        'sar' => array(
                            '上海',
                            '上海市',
                            '徐汇区',
                        ),
                        'id' => '23'
                    ),//重载下 模拟生日 
                    'other'=>array(
                        array(
                                'default'=>1,
                                'name'=>'aasdf1',
                                'firstname'=>'asdf',
                                'lastname'=>'asdfaf',
                                'zipcode'=>'1234',
                                'addr'=>'addr',
                                'phone'=>array(
                                    'mobile' => '12345',
                                    'telephone' => '6464',
                                ),
                                'area'=>array(
                                    'area_type' => 'mainland',
                                    'sar' => array(
                                        '上海',
                                        '上海市',
                                        '徐汇区',
                                    ),
                                    'id' => '23'
                                )//重载下 模拟生日
                            ),
                        array(
                                'name'=>'aasdf2',
                                'firstname'=>'asdf',
                                'lastname'=>'asdfaf',
                                'zipcode'=>'123',
                                'addr'=>'addr',
                                'phone'=>array(
                                    'mobile' => '12345',
                                    'telephone' => '6464',
                                ),
                                'area'=>array(
                                    'area_type' => 'mainland',
                                    'sar' => array(
                                        '上海',
                                        '上海市',
                                        '徐汇区',
                                    ),
                                    'id' => '23'
                                )//重载下 模拟生日 
                            ),
                        array(
                                'name'=>'aasdf3',
                                'firstname'=>'asdf',
                                'lastname'=>'asdfaf',
                                'zipcode'=>'1234',
                                'addr'=>'addr',
                                'phone'=>array(
                                    'mobile' => '12345',
                                    'telephone' => '6464',
                                ), 
                                'area'=>array(
                                    'area_type' => 'mainland',
                                    'sar' => array(
                                        '上海',
                                        '上海市',
                                        '徐汇区',
                                    ),
                                    'id' => '23'
                                )//重载下 模拟生日
                            ),
                        ),
                    ),
                    'profile' =>array(
                        'birthday'=>'1970-12-30',
                        'gender'=>'male',
                        'wedlock'=>'yes',
                    ),
                    'advance'=>array(
                        'total'=>135
                        ,'freeze'=>30,
                        'event'=>array(
                            array(
                                'money'=>103.01,
                                'message'=>'fuck so much 1',
                                'mtime'=>time(),
                                'payment_id'=>null,
                                'order_id'=>1,
                                'paymethod'=>1,
                                'memo'=>'huhuhuhuhuh',
                                'import_money'=>10.6,
                                'explode_money'=>0,
                                'member_advance'=>0.8,
                                'shop_advance'=>700,
                            ),
                            array(
                                'money'=>105.01,
                                'message'=>'fuck so much 2',
                                'mtime'=>time(),
                                'payment_id'=>null,
                                'order_id'=>1,
                                'paymethod'=>1,
                                'memo'=>'hugwghuh',
                                'import_money'=>10.6,
                                'explode_money'=>0,
                                'member_advance'=>0.8,
                                'shop_advance'=>700,
                            ),
                            array(
                                'money'=>106.01,
                                'message'=>'fuck so much 3',
                                'mtime'=>time(),
                                'payment_id'=>null,
                                'order_id'=>1,
                                'paymethod'=>1,
                                'memo'=>'huhuhuhuhuh',
                                'import_money'=>10.6,
                                'explode_money'=>0,
                                'member_advance'=>0.8,
                                'shop_advance'=>700,
                            ),
                                )
                    ),
                    'score'=>array('title'=>'积分'
                        ,'total'=>200
                        ,'freeze'=>10,
                        'event'=>array(
                            array(
                                'point'=>10,
                                'time'=>time(),
                                'reason'=>'just like u',
                                'related_id'=>1,
                                'type'=>1,
                                'operator'=>'god'
                            ),
                            array(
                                'point'=>11,
                                'time'=>time()+1,
                                'reason'=>'just like u',
                                'related_id'=>1,
                                'type'=>1,
                                'operator'=>'god'
                            ),
                            array(
                                'point'=>15,
                                'time'=>time()+100,
                                'reason'=>'just like u',
                                'related_id'=>1,
                                'type'=>1,
                                'operator'=>'god'
                            ),
                        )
                    )
            );
            
            /*$this->model->save($memberArr);
            self::$id = $memberArr['member_id'];
            self::$username = $memberArr[ 'account']['login_name'];*/
    }
    /*
    public function testDump(){
        $sdf = $this->model->dump(self::$id,'*',array('account'=>array('*'),'contact'=>array('*'),'advance'=>array('*'),'score'=>array('*')));
        var_dump($sdf);
    }
    
    public function testUpdate(){
        $sdf = $this->model->dump(self::$id,'*',array('account'=>array('*'),'contact'=>array('*')));
        #修改键名为sdf标准
        $sdf['account'] = $sdf['pam_account'];
        unset( $sdf['pam_account']);
        $sdf['contact']['name'] = 'ken';    
        $this->model->save($sdf);
        $sdf = $this->model->dump(self::$id,'*',array('contact'=>array('*')));
        #var_dump($sdf['contact']);
    }
    */

    public function testCreateHasMeta(){
        $data['member_lv']['member_group_id'] = 1;
        $data['pam_account']['login_name'] = 'cool'.time();
        $data['pam_account']['login_password'] = 'shopex';
        $data['pam_account']['login_password'] = md5(trim($data['account']['login_password']));
        $data['pam_account']['account_type'] = 'member';
        $data['pam_account']['createtime'] = time();
        $data['advance'] ['total'] = 0.0;
        $data['score'] ['total'] = 0.0;
        $data['reg_ip'] = base_request::get_remote_addr(); 
        $data['regtime'] = time();
        $data['contact']['email'] = 'xxxx@sss.com';
        $data['contact']['qq'] = '5555555';
        $this->model->save($data);   
        self::$id = $data['member_id'];
    }
    
    function testUpdateHasMeata(){
        $data['member_id'] = self::$id;
        $data['regtime'] = time();
        $data['contact']['qq'] = '7777777';
        $data['advance'] ['total'] = 0.0;
       # $data['score'] ['total'] = 0.0;
        $this->model->save($data);
    }
    
    
    /*
    public function testLogin(){
        
    }
    
    public function testIsExists(){
        $ret = $this->model->is_exists(self::$username);
        #var_dump($ret);
        $ret = $this->model->is_exists('nobody');
        #var_dump($ret);
    }
*/
}
