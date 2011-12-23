<?php
/*
*该方法目的是防止黑客使用标准md5库进行暴力破解
*/
function shopex_md5($username,$passwd,$reg_time){
    $tmp = md5($passwd);
    $tmp = $tmp.$username.$reg_time;
    $tmp = md5($tmp);
    $tmp{0} = 's'; #把md5后的第一位改成s，可以作为新老密码的表示，方便升级替换
    
    return $tmp;
}

#测试

#现在做一个测试数据类
class user_entry{
    
    var $passwd;
    var $reg_time;
    
    function __construct($username){
        $this->username = $username;
        $this->passwd = "sd0cf8589c5c8364531bae98de221644";
        $this->reg_time = strtotime('1984-11-20 12:30:42');
    }

    function get_passwd(){
        return $this->passwd;
    }

    function get_reg_time(){
        return $this->reg_time;
    }
    
    function update_passwd($passwd,$reg_time){
        $this->passwd = shopex_md5($this->username,$passwd,$reg_time);
    }
}



/*
*验证示例
*
*/

function try_login($username,$passwd){
    global $obj_user;
    $my_passwd = $obj_user->get_passwd();
    $reg_time = $obj_user->get_reg_time();
    if($my_passwd{0} == 's'){        
        if( shopex_md5($username,$passwd,$reg_time) == $my_passwd ){
            return true;
        }else{
            return false;
        }
    }else{
        if ( md5($passwd) == $my_passwd){
            $obj_user->update_passwd($passwd,$reg_time); #按新hash方法加密
            return true;
        } else {
            return false;
        }
    }
    return false;    
}


$username = 'ken';
$passwd = 'shopex';
$reg_time = strtotime('1984-11-20 12:30:42');

#生成一个实例
$obj_user = new user_entry($username);

#test standard md5 hash
$obj_user->passwd = md5($passwd);

echo "test old hash method";
echo "shopex standard hash is : ";
echo $obj_user->passwd;
echo "\n";

echo "test old method bad\n";
$passwd = 'comax';
$v = try_login($username,$passwd);
var_dump($v);

echo "test old method ok\n";
$passwd = 'shopex';
$v = try_login($username,$passwd);
var_dump($v);
echo "\n";

echo "\n--------------------------\n";


#test our md5 hash
echo "test new hash method";
echo "shopex new hash is : ";
echo $obj_user->passwd;
echo "\n";

echo "test new method bad\n";
$passwd = 'comax';
$v = try_login($username,$passwd);
var_dump($v);

echo "test new method ok\n";
$passwd = 'shopex';
$v = try_login($username,$passwd);
var_dump($v);
echo "\n";

echo "\n--------------------------\n";

echo "all test done!";

#测试方法:在命令行先执行： php md5.php