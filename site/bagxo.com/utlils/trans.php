<?php 
header("Content-Type: text/html; charset=UTF-8");
set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);

class log
{
    static function scroll($str,$state)
    {
            if($state=="ok")
            {
                $str="<font color=\"green\">".$str."</font><br>";
            }
            if($state=="fail")
            {
                $str="<font color=\"red\" >".$str."</font><br>";
            }
            $str.=<<<MSG
            <script language="JavaScript">
                if(document.body.scrollHeight>document.body.clientHeight-30)
                 {
                     scroll(0,document.body.scrollHeight-document.body.clientHeight+30);
                }
            </script>
MSG;
            flush();
            echo $str;
    }
    
    static function rollback($msg)
    {
        echo "<script language=\"JavaScript\">alert(\"".$msg."\");history.go(-1);</script>";
    }
    
    static function alert($msg)
    {
        echo "<script language=\"JavaScript\">alert(\"".$msg."\");</script>";
    }
    
    static function err($imsg)
    {
        $msg="<span style='color:red;font-size:12px'>".$imsg."</span><br>";
        die($msg);
    }
    
    static function confirm($msg)
    {
        
    }
    
    static function write($msg){
        $msg .= "\n";
        error_log($msg,3,__FILE__.".log");
    }
    
}
    
class dba 
{
    //数据库链接句柄
    var $link;
    var  $rs;  
    var  $innerrs;
    var  $row;    
    var  $table;
    var  $field;    

    
    function dba($host='localhost',$user='user',$pass='usst6103',$dbname='ahgift')
    {
        
        //检查操作系统    
        if(substr(PHP_OS, 0, 3) != 'WIN') 
        {
            log::err('windows os only');    
        }
        //创建到MySQL的持久连接
        $this->link=mysql_connect($host, $user, $pass) 
        or log::err("MySQL connection error!");
        mysql_select_db($dbname) 
        or log::err("$dbname not exists!");                
    }
    
    function query($sql)
    {
        if(isset($sql))
        {                
            mysql_query("set names utf8");
            $this->rs=mysql_query($sql,$this->link)
            or log::err("line:".__LINE__.mysql_error());
        }
        else
        {
            log::err("query is empty");
        }
    }

    function innerquery($sql)
    {
        if(isset($sql))
        {                
            mysql_query("set names utf8");
            $this->inneers=mysql_query($sql,$this->link)
            or log::err("$sql@line:".__LINE__.mysql_error());
        }
        else
        {
            log::err("query is empty");
        }
    }
    
    function row($rs) 
    { 
        if( $this->row=mysql_fetch_array($rs,MYSQL_ASSOC))
        {            
            return $this->row; 
        }
        else
        {
            return false;
        }                     
    }
    //! An accessor
    /**
    * Returns a associative array of a query set
    * @return  mixed
    */
    function table($rs)
    {            
        while($this->row($rs))
        {
            $this->table[]=$this->row;
        }
        return $this->table;
    }

    //! An accessor
    /**
    * Returns the fields of a specify table
    * @param  $table specify table name
    * @param  $kicklist a list of fields would be del from total fields
    * @return Mixed array
    */
    function fields($table,$kicklist=null)
    {
        $this->query("describe $table");
        while ($row=$this->row()) 
        {
            if(!in_array($row[Field],$kicklist))
            {
                $this->fields[]=$row[Field];
            }
        }
        return $this->fields;
    }
    
}    

class member
{


function  member()
{
                    
    $dbHost = "127.0.0.1";
    $dbUser = "root";
    $dbPass = "shopex";
    $this->source_db = "my7martcom";
    $this->target_db = "bagxocom";
    //生成实例
    $this->source_db_i = new dba($dbHost,$dbUser,$dbPass,$this->source_db);
    $this->target_db_i =  new dba($dbHost,$dbUser,$dbPass,$this->target_db);
}

    function clear_tables() 
    {
        //清空 用户表
        $sql = "delete from sdb_members where member_id > 50";
        $this->target_db_i->query($sql);
    }

    function export_users()
    {

            //处理用户
            $map = array(
                "member_lv_id"=>"level",
                "uname"=>"user",
                "name"=>"name",
                "lastname"=>"",
                "firstname"=>"",
                "password"=>"password",
                "area"=>"",
                "mobile"=>"mov",
                "tel"=>"tel",
                "email"=>"email",
                "zip"=>"zip",
                "addr"=>"addr",
                "province"=>"province",
                "city"=>"city",
                "order_num"=>"",
                "b_year"=>"",
                "b_month"=>"",
                "b_day"=>"",
                "sex"=>"",
                "advance"=>"advance",
                "point_freeze"=>"",
                "point_history"=>"",
                "point"=>"point",
                "reg_ip"=>"ip",
                "regtime"=>"regtime",
                "pw_answer"=>"pw_answer",
                "pw_question"=>"pw_question",
                "remark"=>"birthday",
                "fav_tags"=>"sex",
            );
            
            $target_col_list = "";
            $source_col_list = "";
            foreach ($map as $new_col=>$old_col){
                if(!$old_col){
                    continue;
                }                
                $target_col_list .= "`$new_col`,";
                $source_col_list .= "`$old_col`,";          
            }
            $target_col_list = trim($target_col_list,",");
            $source_col_list = trim($source_col_list,",");
            $sql = "INSERT INTO `$this->target_db`.`sdb_members` 
            (".$target_col_list.") 
            SELECT  ". $source_col_list." 
            FROM `$this->source_db`.`sdb_mall_member`";
            log::write($sql);
            $this->target_db_i->query($sql);

            $sql = "select * from sdb_members";
            $this->target_db_i->query($sql);
            while ($row = $this->target_db_i->row($this->target_db_i->rs)) 
            {
                
                if($row['fav_tags'] == 1){
                    $row['sex'] = '1';
                }
                if($row['fav_tags'] == 0 ){
                    $row['sex'] = '0';
                }
               if( $b = explode('-',$row['remark']) ){
                   $row['b_year'] = str_replace('\'','',$b[0]);
                   $row['b_month'] = str_replace('\'','',$b[1]);
                   $row['b_day'] = str_replace('\'','',$b[2]);
               }
                
                #$sql = "update sdb_members set sex='{$row[sex]}',b_year='{$row[b_year]}',b_month='{$row[b_month]}',b_day='{$row[b_day]}' ,remark='',fav_tags='' where member_id='{$row[member_id]}'";
                $sql = "update sdb_members set sex='{$row[sex]}',b_year='{$row[b_year]}',b_month='{$row[b_month]}',b_day='{$row[b_day]}'  where member_id='{$row[member_id]}'";
                
                
                $this->target_db_i->innerquery($sql);
            }
    }
    
    function clear(){
        $sql = "select * from sdb_members";
        $this->target_db_i->query($sql);
        while ($row = $this->target_db_i->row($this->target_db_i->rs)) 
        {
            $sql = "update sdb_members set remark='',fav_tags='' where member_id='{$row[member_id]}'";   
            $this->target_db_i->innerquery($sql);
        }
    }


    function translate_local2utf($key, $fields, $table)
    {
        $str_fields = $key.','.implode(',', $fields);
    
        $str_query = "select {$str_fields} from {$table}";
        $result = mysql_query( $str_query );
        report_mysql_errors($str_query);
        $i=0;
        while ($row = mysql_fetch_array($result)) 
        {
        //    print_r( $row);
            unset($arr_fields);
            foreach ($fields as $v) {
    //            if ($row[$v]!='')
                $arr_fields[$v] = addslashes(stripslashes(local2utf($row[$v], 'zh')));
            }
    //        echo "<pre>";
    //        print_r($arr_fields);
    //        echo "<pre>";
            $db_string = compile_db_update_string($arr_fields);
            $str_query = "UPDATE {$table} SET $db_string WHERE {$key}='".$row[$key]."'";
    //        rptout($str_query);
            mysql_query( $str_query );
            report_mysql_errors($str_query);
            $i++;
            if ($i%60==0) rptout($i);
        }
        rptout($i);
    }
        
    function intString($intvalue,$len){
        $intstr=strval($intvalue);
        //echo strlen($intstr);
        for ($i=1;$i<=$len-strlen($intstr);$i++){
            $tmpstr .= "0";
        }
        return $tmpstr.$intstr;
    }
    
    function compile_db_update_string($data) {
        
        $return_string = "";
        
        foreach ($data as $k => $v)
        {
            //$v = preg_replace( "/'/", "\\'", $v );
            if($return_string=="")
            $return_string  = $k . "='".$v."'";
            else
            $return_string .= ",".$k . "='".$v."'";
    
        }
            
            //$return_string = preg_replace( "/,$/" , "" , $return_string );
            
            return $return_string;
    }
    
    function local2utf($string,$encoding)
    {
        global $lencodingtable;
    //                echo ;
        if(!trim($string)) return $string;
    
         if(!isset($lencodingtable[$encoding]))
        {
            $filename=realpath(dirname(__FILE__)."/coding/".$encoding.".txt"); 
    
            if(!file_exists($filename)||$filename=="")
            {
    
               return $string;
            }
            $tmp=file($filename);
            $codetable=array();
            while(list($key,$value)=each($tmp))
                $codetable[hexdec(substr($value,0,6))]=hexdec(substr($value,7,6));
            $lencodingtable[$encoding] = $codetable;
        }
        else
        {
            $codetable = $lencodingtable[$encoding];
        }
    
        $ret="";
        while(strlen($string)>0) {
            if( ord(substr($string,0,1)) > 127 ) {
                $t=substr($string,0,2);
                $string=substr($string,2);
                $ret .= u2utf8($codetable[hexdec(bin2hex($t))]);
            }
            else 
            { 
                $t=substr($string,0,1);
                $string=substr($string,1);
                $ret .= u2utf8($t);
            }
        }
        return $ret;
    
    }
    
    function u2utf8($c) {
        $str='';
        if ($c < 0x80) {
            $str.=$c;
            }
        else if ($c < 0x800) {
            $str.=chr(0xC0 | $c>>6);
            $str.=chr(0x80 | $c & 0x3F);
            }
        else if ($c < 0x10000) {
            $str.=chr(0xE0 | $c>>12);
            $str.=chr(0x80 | $c>>6 & 0x3F);
            $str.=chr(0x80 | $c & 0x3F);
        }
        else if ($c < 0x200000) {
            $str.=chr(0xF0 | $c>>18);
            $str.=chr(0x80 | $c>>12 & 0x3F);
            $str.=chr(0x80 | $c>>6 & 0x3F);
            $str.=chr(0x80 | $c & 0x3F);
        }
        return $str;
    }



}


$func=new member();
$func->clear_tables();
//处理注册用户
$func->export_users();
$func->source_db = "do4bagnet";
$func->export_users();
$func->clear();


