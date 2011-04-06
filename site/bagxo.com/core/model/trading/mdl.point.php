<?php
class mdl_point extends modelFactory{
    function savePointSetting($aData) {
        foreach($aData as $k=>$v){
            $this->system->setConf('point.'.$k,$v);
        }
        return true;
    }
}
?>