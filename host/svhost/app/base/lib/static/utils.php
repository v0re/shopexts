<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class utils{

    static function match_network ($nets, $ip, $first=false) {
        $return = false;
        if (!is_array ($nets)) $nets = array ($nets);

        foreach ($nets as $net) {
            $rev = (preg_match ("/^\!/", $net)) ? true : false;
            $net = preg_replace ("/^\!/", "", $net);

            $ip_arr  = explode('/', $net);
            $net_long = ip2long($ip_arr[0]);
            $x        = ip2long($ip_arr[1]);
            $mask    = long2ip($x) == $ip_arr[1] ? $x : 0xffffffff << (32 - $ip_arr[1]);
            $ip_long  = ip2long($ip);

            if ($rev) {
                if (($ip_long & $mask) == ($net_long & $mask)) return false;
            } else {
                if (($ip_long & $mask) == ($net_long & $mask)) $return = true;
                if ($first && $return) return true;
            }
        }
        return $return;
    }


    static function microtime(){
        list($usec, $sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
    }


    static function addslashes_array($value){
        if(empty($value)){
            return $value;
        }else{
            if(is_array($value)){
                foreach($value as $k=>$v){
                    if(is_array($v)){
                        $value[$k] = self::addslashes_array($v);
                    }else{
                        $value[$k] = addslashes($v);
                    }
                }
                return $value;
            }else{
                return addslashes($value);
            }
        }
    }

    static function stripslashes_array($value){
        if(empty($value)){
            return $value;
        }else{
            if(is_array($value)){
                $tmp = $value;
                foreach($tmp as $k=>$v){
                    $k = stripslashes($k);
                    $value[$k] = $v;

                    if(is_array($v)){
                        $value[$k] = self::stripslashes_array($v);
                    }else{
                        $value[$k] = stripslashes($v);
                    }
                }
                return $value;
            }else{
                return stripslashes($value);
            }
        }
    }


    static function _apath(&$array,$path,&$ret){
        $key = array_shift($path);
        if( ($p1 = strpos($key,'[')) && ($p2 = strrpos($key,']'))){
            $predicates = substr($key,$p1+1,$p2-$p1-1);
            $key = substr($key,0,$p1);
        }

        if(is_array($array)&&array_key_exists($key,$array)){
            $next = $array[$key];
            if(isset($predicates) && is_array($next)){
                switch(true){
                case $predicates=='first()':
                    $next = reset($next);
                    break;

                case $predicates=='last()':
                    $next = end($next);
                    break;

                case is_numeric($predicates):
                    $next = $next[$predicates];
                    break;

                default:
                    list($k,$v) = explode('=',$key);
                    if($v){
                        foreach($next as $item){
                            if(isset($item[$k]) && $item[$k]==$v){
                                $nextrst = $item;
                                break;
                            }
                        }
                    }else{
                        foreach($next as $item){
                            if(isset($item[$k])){
                                $nextrst = $item;
                                break;
                            }
                        }
                    }
                    if(isset($nextrst)){
                        $next = $nextrst;
                    }elseif($predicates=='default'){
                        $next = reset($next);
                    }else{
                        return false;
                    }
                    break;
                }
            }
            if(!$path){
                $ret = $next;
                return true;
            }else{
                return self::_apath($next,$path,$ret);
            }
        }else{
            return false;
        }
    }

    static function apath( &$array, $map ){
        if(self::_apath($array,$map,$ret) !== false){
            return $ret;
        }else{
            return false;
        }
    }

    static function unapath(&$array,$col,$path,&$ret){

        if( !array_key_exists($col,$array) )
            return false;
        $ret = '';
        $arrKey = '';
        $tmpArr = null;
        $pathCount = count($path);
        $pathItem = 1;
        foreach( $path as $v ){
            if( ($p1 = strpos($v,'[')) && ($p2 = strrpos($v,']'))){
                $predicates = substr($v,$p1+1,$p2-$p1-1);
                $v = substr($v,0,$p1);
            }
            if( $pathCount == $pathItem++ ){
                eval( '$ret'.$arrKey.'["'.$v.'"] = $array[$col];' );
                unset($array[$col]);
                return true;
            }
            $arrKey .= '["'.$v.'"]';
            if( $predicates ){
                return false;
            }
            $predicates = null;
        }

        return true;

    }

    static function array_path($array, $path){
        $path_array = explode('/', $path);
        $_code = '$return = $array';
        if($path_array){
            foreach($path_array as $s_path){
                $_code .= '[\''.$s_path.'\']';
            }
        }
        $_code = $_code.';';
        eval($_code);
        return $return;
    }

    static function buildTag($params,$tag,$finish=true){
        $ret = array();
        foreach((array)$params as $k=>$v){
            if(!is_null($v) && !is_array($v)){
                if($k=='value'){
                    $v=htmlspecialchars($v);
                }
                $ret[]=$k.'="'.$v.'"';
            }
        }
        return '<'.$tag.' '.implode(' ',$ret).($finish?' /':'').'>';
    }

    function has_error($code) {
        ini_set('track_errors','on');
        if(@eval('return true;' . $code)){
            ini_set('track_errors','off');
            return null;
        }else{
            ini_set('track_errors','off');
            return $php_errormsg;
        }
    }

    static function mkdir_p($dir,$dirmode=0755){
        $path = explode('/',str_replace('\\','/',$dir));
        $depth = count($path);
        for($i=$depth;$i>0;$i--){
            if(file_exists(implode('/',array_slice($path,0,$i)))){
                break;
            }
        }
        for($i;$i<$depth;$i++){
            if($d= implode('/',array_slice($path,0,$i+1))){
                mkdir($d,$dirmode);
            }
        }
        return is_dir($dir);
    }

    static function cp($src,$dst){
        if(is_dir($src)){
            $obj = dir($src);
            while(($file = $obj->read()) !== false){
                if($file{0} == '.' ) continue;
                $s_daf = "$src/$file";
                $d_daf = "$dst/$file";
                if(is_dir($s_daf)){
                    if(!file_exists($d_daf)){
                        self::mkdir_p($d_daf);
                    }
                    self::cp($s_daf,$d_daf);
                }else{
                    $d_dir = dirname($d_daf);
                    if(!file_exists($d_dir)){
                        self::mkdir_p($d_dir);
                    }
                    copy($s_daf,$d_daf);
                }
            }
        }else{
            @copy($src,$dst);
        }
    }

    static function remove_p($sDir) 
    {
        if($rHandle=opendir($sDir)){
            while(false!==($sItem=readdir($rHandle))){
                if ($sItem!='.' && $sItem!='..'){
                    if(is_dir($sDir.'/'.$sItem)){
                        self::remove_p($sDir.'/'.$sItem);
                    }else{
                        if(!unlink($sDir.'/'.$sItem)){
                            trigger_error(__('因权限原因 ').$sDir.'/'.$sItem.__('无法删除'),E_USER_NOTICE);
                        }
                    }
                }
            }
            closedir($rHandle);
            rmdir($sDir);
            return true;
        }else{
            return false;
        }
    }//End Function
    
    static function replace_p($path,$replace_map){
        if(is_dir($path)){
            $obj = dir($path);
            while(($file = $obj->read()) !== false){
                if($file{0} == '.' ) continue;
                if(is_dir($path.'/'.$file)){
                    self::replace_p($path.'/'.$file,$replace_map);
                }else{
                    self::replace_in_file($path.'/'.$file,$replace_map);
                }
            }
        }else{
            self::replace_in_file($path,$replace_map);
        }
    }

    static function replace_in_file($file,$replace_map){
        file_put_contents($file,str_replace(array_keys($replace_map),array_values($replace_map),file_get_contents($file)));
    }

    static function tree($dir){
        self::$ret = array();
        $obj = dir($dir);
        while(($file = $obj->read()) !== false){
             if(substr($file,0,1) == '.' ) continue;
             $daf = "$dir/$file";
             self::$ret[] = $daf;
             if(is_dir($daf)) self::tree($daf);
        }
    }

    // 原func_ext.php中的 array_change_key
    static function &array_change_key(&$items, $key, $is_resultset_array=false){
            if (is_array($items)){
                $result = array();
                if (!empty($key) && is_string($key)) {
                    foreach($items as $_k => $_item){
                        if($is_resultset_array){
                            $result[$_item[$key]][] = &$items[$_k];
                        }else{
                            $result[$_item[$key]] = &$items[$_k];
                        }
                    }
                    return $result;
                }
            }
            return false;
    }
    
    
    //配送公式验算function
    static function cal_fee($exp,$weight,$totalmoney,$defPrice=0){
        if($str=trim($exp)){
            $dprice = 0;
            $weight = $weight + 0;
            $totalmoney = $totalmoney + 0;
            $str = str_replace("[", "self::_getceil(", $str);
            $str = str_replace("]", ")", $str);
            $str = str_replace("{", "self::_getval(", $str);
            $str = str_replace("}", ")", $str);
    
            $str = str_replace("w", $weight, $str);
            $str = str_replace("W", $weight, $str);
            $str = str_replace("p", $totalmoney, $str);
            $str = str_replace("P", $totalmoney, $str);
            eval("\$dprice = $str;");
            if($dprice === 'failed'){
                return $defPrice;
            }else{
                return $dprice;
            }
        }else{
            return $defPrice;
        }
    }

    static function mydate($f,$d=null){
        global $_dateCache;
        if(!$d)$d=time();
        if(!isset($_dateCache[$d][$f])){
            $_dateCache[$d][$f] = date($f,$d);
        }
        return $_dateCache[$d][$f];
    }

    function _getval($expval){
        $expval = trim($expval);
        if($expval !== ''){
        eval("\$expval = $expval;");
        if ($expval > 0){
            return 1;
        }else if ($expval == 0){
            return 1/2;
        }else{
            return 0;
        }
        }else{
            return 0;
        }
    }
    function _getceil($expval){
        if($expval = trim($expval)){
        eval("\$expval = $expval;");
        if ($expval > 0){
            return ceil($expval);
        }else{
            return 0;
        }
        }else{
            return 0;
        }
    }
    
    //多维数组转成一维数组，键用/分隔
    static function array_to_flat($array,&$ret,$p_key=null){
        foreach($array as $key=>$item){
        	if($p_key != null){
            	$key = "$p_key/$key";
        	}    
           if(is_array($item)){
               self::array_to_flat($item,$ret,$key);
           }else{
               $ret[$key] = $item;
           }
        }
    }
    static function steprange($start,$end,$step){
        if($end-$start){
            if($step<2)$step=2;
            $s = ($end - $start)/$step;
            $r=array(floor($start)-1);

            for($i=1;$i<$step;$i++){
                $n = $start+$i*$s;
                $f=pow(10,floor(log10($n-$r[$i-1])));
                $r[$i] = round($n/$f)*$f;
                $q[$i] = array($r[$i-1]+1,$r[$i]);
            }
            $q[$i] = array($r[$step-1]+1,ceil($end));
            return $q;
        }else{
            if(!$end)$end = $start;
            return array(array($start,$end));
        }
    }
}
