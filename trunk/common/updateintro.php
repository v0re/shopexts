<?php
require('config/config.php');
require_once(CORE_DIR.'/kernel.php');
mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
MySQL_query("SET NAMES 'utf8'");
mysql_select_db(DB_NAME);
class tools extends kernel{
    var $system;
    var $db;
    function tools(){
        parent::kernel();
        $this->system=&$GLOBALS['system'];
        $this->db = &$this->system->database();
    }
function updateGoodsIntro($newURL,$oldURL)
   {
	$table=DB_PREFIX.'goods';
	$sql="update ".$table." set intro=replace(intro,'".$oldURL."','".$newURL."')";
	mysql_query($sql);
   }
 }
 $do = new tools();
 if($_POST['submit']){
    $newURL = trim($_POST['newURL']);
    $oldURL = trim($_POST['oldURL']);
    $do->updateGoodsIntro($newURL,$oldURL);
    echo '修改成功!';
}else{
?>
<title>详细介绍图片路径修改</title>
<form action="" method="POST">
我不用它了:<input type="text" name="oldURL" size="12"> <br>以后就用它了:<input type="text" name="newURL"><input type="submit" name="submit" value="调一调">
</form>
<?php } ?>