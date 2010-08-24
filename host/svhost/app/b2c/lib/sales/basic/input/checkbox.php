<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_sales_basic_input_checkbox
{
    public $type = 'checkbox';
    public function create($aData, $table_info=array()) {
        $aData['default'] = (is_null($aData['default']) || (empty($aData['default'])) )? array() : (is_array($aData['default'])? $aData['default'] : explode(',',$aData['default']) ) ;
        // 目前是调试 改成functions 后可以用封装好的js框架接口做 现在使用原生js
        $html = '<script>
        function promotion_check_all (o) {
            var checks = o.parentNode.parentNode.getElementsByTagName("input");
            if(checks == null) return false;
            for(var i = 0; i < checks.length; i++) {
                checks[i].checked = o.checked;
            }
        }
        </script><label><input type="checkbox" onclick="promotion_check_all(this)"/>全选</label>';
        if(is_array($aData['options'])) {
            foreach($aData['options'] as $key => $row) {
                $html .= '<label><input type="checkbox" name="'.$aData['name'].'[]" value="'.$key.'" '.(in_array($key,$aData['default'])? 'checked' : '').' />'.$row['name'].'</label>';
            }
        }
        return "<span>{$html}</span>";
    }
}
?>
