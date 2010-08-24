<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_sales_csv extends desktop_io_type_csv {

    var $name = 'csv-逗号分隔的文本文件';
    var $importforObjects = 'goods';

    
    
    public function download($title='优惠券代码', $prex='coupon', $nums=0, $data=array()) {
        $aData = array(
                        'title' =>  $this->charset->utf2local($title),
                        'name'  =>  $prex .'-'. date("ymdhis") .'('. $nums .')',
                        'contents'  =>  $data,
                    );
        $this->export($aData, $this);
    }
    
    
    
    


}
?>
