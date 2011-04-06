<?php
function smarty_function_toinput($params, &$smarty){
    $html = null;
    _smarty_function_toinput($params['from'],$ret,$params['name']);
    foreach($ret as $k=>$v){
        $html.='<input type="hidden" name="'.$k.'" value="'.$v."\" />\n";
    }
    return $html;
}

function _smarty_function_toinput($data,&$ret,$path=null){
    foreach($data as $k=>$v){
        $d = $path?$path.'['.$k.']':$k;
        if(is_array($v)){
            _smarty_function_toinput($v,$ret,$d);
        }else{
            $ret[$d]=$v;
        }
    }
}
?>
