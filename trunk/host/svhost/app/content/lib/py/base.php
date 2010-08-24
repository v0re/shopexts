<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_py_base 
{
    function __construct() 
    {
        $this->table = require_once(dirname(__FILE__) . '/table.php');
    }//End Function

    public function get_array($string, $encoding='') 
    {
        if(function_exists('iconv')){
            if($encoding)  $string = iconv($encoding, 'GBK', $string);
        }else{
            if($encoding)  $string = kernel::single('base_charset')->utf2local($string, 'zh');
        }
        $flow = array();
        for ($i=0;$i<strlen($string);$i++)
        {
            if (ord($string[$i]) >= 0x81 and ord($string[$i]) <= 0xfe) 
            {
                $h = ord($string[$i]);
                if (isset($string[$i+1])) 
                {
                    $i++;
                    $l = ord($string[$i]);
                    if (isset($this->table[$h][$l])) 
                    {
                        array_push($flow,$this->table[$h][$l]);
                    }
                    else 
                    {
                        array_push($flow,$h);
                        array_push($flow,$l);
                    }
                }
                else 
                {
                    array_push($flow,ord($string[$i]));
                }
            }
            else
            {
                array_push($flow,ord($string[$i]));
            }
        }
        
        $data = array();
        foreach($flow AS $k=>$v){
            $data[] = (is_numeric($v)) ? chr($v) : $v[0];
        }

        return $data;
    }//End Function

    
}//End Class
