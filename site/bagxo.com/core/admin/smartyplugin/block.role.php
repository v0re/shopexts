<?php
function smarty_block_role($params, $content, &$smarty,$s)
{
    if($content){
        $op = $GLOBALS['op'];
        if($op->is_super)return $content;
        $system = &$GLOBALS['system'];
        $opmod = &$system->loadModel('admin/operator');
        $act = &$opmod->getActions($op->opid);
        $require = explode(',',$params['require']);
        if(count($require)>1){
            if($params['mode']=='or'){
                $pass=0;
                foreach($require as $r){
                    if(isset($act[$r])){
                        return $content;
                    }
                }
                return null;
            }else{
                foreach($require as $r){
                    if(!isset($act[$r])){
                        return;
                    }
                }
            }
        }else{
            if(!isset($act[$require[0]])){
                return;
            }
        }
        return $content;
    }
}

?>
