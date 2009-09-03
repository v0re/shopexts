<?php
set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);
include_once("./config/config.php");
$connect=mysql_connect("localhost","root","") or die ('I cannot  connect to the database.'); 
mysql_query("set names utf8");
?>
<html>
<head>
<title>EcShop to ShopEx</title>
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
</head>
<body style="font-size:16px;">
<?php
createfile();
$done1=new user_convert;
$done2=new goods_convert;
$done3=new order_convert;
$done1->users();
$done1->memberLevel();
$done2->brand();
$done2->goods();
$done2->goods_cat();
$done2->goods_type();
$done3->order();
compress();
?>
<script language="javascript">
alert("转换完成！");
history.go(-1);
</script>
<?php 

//创建文件
function createfile(){
        $file="./ectoshopex.sql";
        $fp1=fopen("$file", "a"); //打开文件指针，创建文件
        fclose($fp1);
}

//压缩sql文件
function compress(){
        /*echo PHP_OS;
        $OS=PHP_OS;
        if(eregi("win",$OS)){
        
        }
        elseif(eregi("linux",$OS)){
            $source="./ectoshopex.sql";
            $target="./ectoshopex.tgz";
            exec("tar cvzf $source $target");
        }*/
//        unlink("./ectoshopex.tgz");
        include("tar.class.php");
        $tar = new tar();
        $tar->addFile("ectoshopex.sql");
        $tar->toTar("ectoshopex.tgz",TRUE);
        $tar->saveTar();
        unlink("./ectoshopex.sql");
        }

//查询数据库
function dbQ($sql,$show=true)
{
    if(is_string($sql)){
        $result = mysql_query($sql) or  die("<br><font color=red>".mysql_error()."</font><hr>".$sql);      
        if($show){
                if(strtoupper(substr($sql,0,6)) != "SELECT"){
                echo " <font color=red>该查询影响 ".mysql_affected_rows()." 行</font><br>";
                    }
                else
                {
                    }
                }
                return $result;
        }
        else{
                return false;
        }
}
//清除表数据
function truncate($table)
{
        if(is_array($table)){
                foreach($table as $val){
                        $sql = "TRUNCATE $val";
                        dbQ($sql,false);
                }
        }
        else{
                $sql = "TRUNCATE $table";
                dbQ($sql,false);
        }
        return true;
}
/*
function Select($name,$from)
{
        if(!is_array($name)){
                return false;
            }
        else{
                foreach($name as $var){
                $tmp.="$var".","
                }
                
        $sql="select ".$tmp." from ".$from;        
            }
        return $sql;
}

function insert($array,$from,$to,$while)
{
    if(!is_array($array)){
                return false;
            }
        else{
                $tmp='(';
                foreach($value as $Var){
                $tmp1.=$Var.",";
                }
                $tmp1=rtrim($tmp1,",").")";
                $sql="insert into ".$to." ".$tmp." select "      
            }
        return $sql;

}
*/
//得到sql语句
function Insert($field,$from,$to,$where)
{
        if(!is_array($field)){
                return false;
        }else{
                $colStr = '( ';
                $valStr = ' ';
                foreach($field as $col=>$val){
                        if(empty($val)) continue;
                        $colStr.='`'.$col.'`,';
                        if(is_numeric($val)){
                                $valStr .= "'".$val."',";
                        }else{
                                $valStr .= $val.",";
                        }
                }
                $colStr = rtrim($colStr,",").")";
                $valStr = rtrim($valStr,",")." ";
        }
        $sql =" INSERT INTO ".$to." ".$colStr." SELECT ".$valStr." FROM ".$from." where ".$where;
        return $sql;
}


//输出到文件
function writeFile($filename,$content)  
{  
    if($filename)  
    {  
        $fp=@fopen($filename,"a");  
        if($fp){  
                fwrite($fp,$content.";\r\n");  
                fclose($fp);  
                }  
        return true;  
    }  
    else
    {
        return false;  
    } 
}

//转换用户
class user_convert{
    function users(){
                //清空会员相关表
         $aTable = array(
                  'shopex.sdb_members',
                  'shopex.sdb_member_coupon',
                  'shopex.sdb_member_dealer',
                  'shopex.sdb_member_mattrvalue',
         		  'shopex.sdb_member_lv'
                );              
        truncate($aTable);
        $field=array(
                  'member_id'			 =>			'user_id',
                  'member_lv_id'		 =>			'1',
                  'uname'				 =>			'user_name',
                  'name'				 => 		'',
                  'lastname'			 => 		'',
                  'firstname'			 => 		'',
                  'password'			 => 		'password',
                  'area'				 =>			'',
                  'mobile'				 => 		'mobile_phone',
                  'tel'					 => 		'home_phone',
                  'email'				 => 		'email',
                  'zip'					 => 		'',
                  'addr'				 => 		'',
                  'province'			 => 		'',
                  'city'				 => 		'',
                  'order_num'			 =>			'',
                  'refer_id'			 => 		'',
                  'refer_url'			 => 		'',
                  'b_year'				 => 		'substring(birthday,1,4)',
                  'b_month'				 => 		'substring(birthday,6,2)',
                  'b_day'				 => 		'substring(birthday,9,2)',
                 // 'sex'					 => 		'sex',
                  'addon'				 => 		'',
                  'wedlock'				 => 		'',
                  'education'			 => 		'',
                  'vocation'			 => 		'',
                  'interest'			 => 		'',
                  'advance'				 => 		'',
                  'advance_freeze'		 => 		'',
                  'point_freeze'		 => 		'',
                  'point_history'		 => 		'',
                  'point'				 => 		'',
                  'score_rate'			 => 		'',
                  'reg_ip'				 => 		'',
                  'regtime'				 => 		'reg_time',
                  'state'				 => 		'',
                  'pay_time'			 => 		'',
                  'biz_money'			 => 		'',
                  'pw_answer'			 => 		'answer',
                  'pw_question'			 => 		'question',
                  'fav_tags'			 => 		'',
                  'custom'				 => 		'',	
                  'cur'					 => 		'',
                  'lang'				 => 		'',
                  'unreadmsg'			 => 		'',
                  'disabled'			 => 		'',
                  'remark'				 => 		'',
                  'role_type'			 => 		'',
                  'remark_type'			 => 		'',						
        );
            $to="shopex.sdb_members";
            $from="ecshop.ecs_users";
            $where="1=1";
       //     mysql_select_db("shopex");
            $insertsql=insert($field,$from,$to,$where); 	
            writeFile("./ectoshopex.sql",$insertsql);
            echo"用户转换中...<br>";
            $rs=dbQ($insertsql);
            
    }

	function memberLevel()
	{
			$field=array(
					'member_lv_id'		 => 		'rank_id',
					'name'				 => 		'rank_name',
					'dis_count'			 => 		'',
					'default_lv'		 => 		'',
					'lv_type'			 => 		''
			);
			$to="shopex.sdb_member_lv";
       	 	$from="ecshop.ecs_user_rank";
       		$where="1=1";
            $insertsql=insert($field,$from,$to,$where); 	
            writeFile("./ectoshopex.sql",$insertsql);
            echo"用户级别转换中...<br>";
            $rs=dbQ($insertsql);

	}
}
//转换商品
class goods_convert{
/*    function goodsImage()
    {
        $sql="update shopex.sdb_goods set small_pic='http://localhost/ecshop/upload/',big_pic='http://localhost/ecshop/upload/',thumbnail_pic='http://localhost/ecshop/upload/'";
        $rs=dbQ($sql);
    }
*/
    function goods_cat()
    {
        $field=array(
                    'cat_id'              =>      'cat_id',
                    'parent_id'           =>      'parent_id',
                    'supplier_id'         =>      '',
                    'supplier_cat_id'     =>      '',
                    'cat_path'            =>      '',
                    'is_leaf'             =>      true,
                    'type_id'             =>      '',
                    'cat_name'            =>      'cat_name',
                    'disabled'            =>      '',
                    'goods_count'         =>      0,
                    'tabs'                =>      '',
                    'finder'              =>      '',
                    'addon'               =>      '',
                    'child_count'         =>      0
            
        );
        $to = 'shopex.sdb_goods_cat';
        $from = 'ecshop.ecs_category';
        truncate($to);
        $where="1=1";
        $insertsql=insert($field,$from,$to,$where); 	
        writeFile("./ectoshopex.sql",$insertsql);
        echo"商品分类转换中...<br>";
        $rs=dbQ($insertsql);
    }
    
    function goods_type()
    {
        $field=array(
                    'type_id'             =>        'cat_id',
                    'name'                =>        'cat_name',
                    'alias'               =>        '',
                    'is_physical'         =>        '',
                    'supplier_id'         =>        '',
                    'supplier_type_id'    =>        '',
                    'schema_id'           =>        true,
                    'props'               =>        '',
                    'spec'                =>        '',
                    'setting'             =>        '',
                    'minfo'               =>        '',
                    'params'              =>        '',
                    'dly_func'            =>        '',
                    'reship'              =>        '',
                    'disabled'            =>        '',
                    'is_def'              =>        '',
                        
        );
        $to = 'shopex.sdb_goods_type';
        $from = 'ecshop.ecs_goods_type';
        truncate($to);
        $where="1=1";
        $insertsql=insert($field,$from,$to,$where); 	
        writeFile("./ectoshopex.sql",$insertsql);
        echo"商品类型转换中...<br>";
        $rs=dbQ($insertsql);
        
    }
    function goods()
    {
        $aTable = array(
                'shopex.sdb_goods',
                'shopex.sdb_goods_keywords',
                'shopex.sdb_goods_lv_price',
                'shopex.sdb_goods_memo',
                'shopex.sdb_goods_spec_index',
                'shopex.sdb_goods_virtual_cat',
                'shopex.sdb_products',
                'shopex.sdb_gimages'
        );
        truncate($aTable);
        $field=array(          
                    'goods_id'             =>      'goods_id',
                    'cat_id'               =>      'cat_id',
                    'type_id'              =>      '1',    //通用商品都是1
                  //  'goods_type'           =>      'goods_type',
                    'brand_id'             =>      'brand_id',
                    'brand'                =>      '',
                    'image_default'        =>      '',
                    'udfimg'               =>      '',
                    'thumbnail_pic'        =>      'goods_thumb',
                    'small_pic'            =>      'original_img',
                    'big_pic'              =>      'goods_img',
                    'image_file'           =>      'original_img',
                    'brief'                =>      'goods_brief',               
                    'intro'                =>      '',
                    'mktprice'             =>      'market_price',
                    'price'                =>      'shop_price',
                    'bn'                   =>      '',
                    'name'                 =>      'goods_name',
                    'marketable'           =>      '',
                    'weight'               =>      'goods_weight',
                    'unit'                 =>      '',
                    'store'                =>      'goods_number',
                    'score_setting'        =>      '',
                    'score'                =>      'integral',
                    'spec'                 =>      '',
                    'pdt_desc'             =>      'goods_desc',
                    'params'               =>      '',
                    'uptime'               =>      'add_time',
                    'downtime'             =>      '',
                    'last_modify'          =>      'last_update',
                    'disabled'             =>      '',
                    'notify_num'           =>      '',
                    'rank'                 =>      '',
                    'rank_count'           =>      '', 
                    'comments_count'       =>      '',    
                    'view_w_count'         =>      '',
                    'view_count'           =>      '',
                    'buy_count'            =>      '',
                    'buy_w_count'          =>      '',
                    'count_stat'           =>      '',
                    'p_order'              =>      '',
                    'd_order'              =>      ''
                );
        $to = "shopex.sdb_goods";
        $from = "ecshop.ecs_goods";
        $where="1=1";
        $insertsql=insert($field,$from,$to,$where); 	
        writeFile("./ectoshopex.sql",$insertsql);
        echo"商品转换中...<br>";
        $rs=dbQ($insertsql);
        
        $sql="select goods_id,thumbnail_pic,small_pic,big_pic,image_file from shopex.sdb_goods";
        $rs=mysql_query($sql);
        while($array=mysql_fetch_array($rs))
        {
        $str1="http://192.168.0.41/ecshop/upload/".$array['thumbnail_pic'];
        $str2="http://192.168.0.41/ecshop/upload/".$array['small_pic'];
        $str3="http://192.168.0.41/ecshop/upload/".$array['big_pic'];
        $str4="http://192.168.0.41/ecshop/upload/".$array['image_file'];
        $str5="<IMG src=\"$str2\">";
        $no=$array['goods_id'];
        $update="update shopex.sdb_goods set image_file='$str4',intro='$str5',thumbnail_pic='$str1',small_pic='$str2',big_pic='$str3' where goods_id='$no'";
        mysql_query($update);
        }
        
        $field=array(          
                    'goods_id'             =>      'goods_id',
                    'thumbnail'            =>      'thumbnail_pic',
                    'small'                =>      'small_pic',
                    'big'                  =>      'big_pic',
                    'source'               =>      'image_file',
                    'src_size_width'       =>      'false',
                    'src_size_height'      =>      'false',
                    'up_time'              =>      'false'     
                    );
        
        $to = "shopex.sdb_gimages";
        $from = "shopex.sdb_goods";
        $where="1=1";
        $insertsql=insert($field,$from,$to,$where); 	
        writeFile("./ectoshopex.sql",$insertsql);
        $rs=dbQ($insertsql);

        $sql="select gimage_id,goods_id from shopex.sdb_gimages";
        $rs=mysql_query($sql);
               while($array=mysql_fetch_array($rs))
               {
               $str=$array['gimage_id'];
               $no=$array['goods_id'];
               $update="update shopex.sdb_goods set image_default='$str' where goods_id='$no'";
               mysql_query($update);
            }
       
        $field = array(          
                    'product_id'           =>      '',
                    'goods_id'             =>      'goods_id',
                    'barcode'              =>      '',
                    'title'                =>      '',
                    'bn'                   =>      '',
                    'price'                =>      'shop_price',
                    'cost'                 =>      '',
                    'name'                 =>      'goods_name',
                    'weight'               =>      '',
                    'unit'                 =>      '',
                    'store'                =>      '',
                    'freez'                =>      '',
                    'pdt_desc'             =>      '',
                    'props'                =>      '',
                    'uptime'               =>      'add_time',
                    'last_modify'          =>      '',
                );
        $to = 'shopex.sdb_products';
        $from = 'ecshop.ecs_goods';
        $where = ' 1=1 ORDER BY add_time DESC';
        //////////////////////////////////////////////////////////////////////////////////////////
        $insertsql = insert($field,$from,$to,$where);
        $rs =dbQ($insertsql);  
        
        
    }
    
    function brand(){
    truncate('shopex.sdb_brand');
    $field = array(
                    'brand_id'             =>      'brand_id',
                    'brand_name'           =>      'brand_name',
                    'brand_url'            =>      'site_url',
                    'brand_desc'           =>      'brand_desc',
                    'brand_logo'           =>      'brand_logo',
                    'brand_keywords'       =>      '',
                    'ordernum'             =>      ''
                    );
                    $to = 'shopex.sdb_brand';
                    $from = 'ecshop.ecs_brand';
                    $where = '1=1';
                    $insertsql=insert($field,$from,$to,$where); 	
                    writeFile("./ectoshopex.sql",$insertsql);
                    echo"商品品牌转换中...<br>";
                    $rs=dbQ($insertsql);
    
    }
    
}
//转换订单
class order_convert{
    function order(){

        $field=array(
                    'order_id'             => 		'order_sn',
                    'member_id'            => 		'user_id',	
                    'confirm'              => 		'',
                    'status'               => 		'order_status',
                    'pay_status'           =>  		'pay_status',
                    'ship_status'          =>   	'shipping_status',	
                    'user_status'          =>  		'',
                    'is_delivery'          =>  		'',
                    'shipping_id'          =>  		'shipping_id',
                    'shipping'             =>  		'shipping_name',
                    'shipping_area'        =>  		'',
                    'payment'              =>  		'',
                    'weight'               =>  		'',
                    'tostr'                =>  		'',
                    'itemnum'              =>  		'',
                    'acttime'              =>  		'',
                    'createtime'           =>  		'',
                    'refer_id'             =>  		'',
                    'refer_url'            =>  		'',
                    'ip'                   =>  		'',
                    'ship_name'            =>   	'',	
                    'ship_area'            =>  		'',
                    'ship_addr'            =>  		'',
                    'ship_zip'             =>  		'zipcode',
                    'ship_tel'             =>  		'',
                    'ship_email'           =>  		'',
                    'ship_time'            =>  		'',
                    'ship_mobile'          =>  		'',
                    'cost_item'            =>  		'',
                    'is_tax'               =>  		'',
                    'cost_tax'             =>  		'',
                    'tax_company'          =>  		'',
                    'cost_freight'         =>  		'shipping_fee',
                    'is_protect'           =>  		'',
                    'cost_protect'         =>  		'insure_fee',
                    'cost_payment'         =>  		'money_paid',
                    'currency'             =>  		'',
                    'cur_rate'             =>  		'',
                    'score_u'              =>  		'integral',
                    'score_g'              =>  		'',
                    'advance'              =>  		'',
                    'discount'             =>  		'discount',
                    'use_pmt'              =>  		'',
                    'total_amount'         =>  		'',
                    'final_amount'         =>  		'',
                    'pmt_amount'           =>  		'',
                    'payed'                =>  		'',
                    'markstar'             =>  		'',
                    'memo'                 =>  		'',
                    'print_status'         =>  		'',
                    'mark_text'            =>  		'pay_note',
                    'disabled'             =>  		'',
                    'last_change_time'     =>      	'',
                    'use_registerinfo'     =>  		'',
                    'mark_type'            =>  		'',
        );
             $to="shopex.sdb_orders";
             $from="ecshop.ecs_order_info";
             $where="1=1";
             truncate($to);
             $insertsql=insert($field,$from,$to,$where); 	
             writeFile("./ectoshopex.sql",$insertsql);
             echo"订单转换中...<br>";
             $rs=dbQ($insertsql);
        
    }
    
}

?>
</body>
</html>