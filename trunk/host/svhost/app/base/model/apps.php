<?php
class base_mdl_apps extends base_db_model{

    function filter($filter){
        unset($filter['use_like']);
        $addons = array();
        if(isset($filter['installed'])){
            $addons[] = $filter['installed']?'status!="uninstalled"':'status="uninstalled"';
            unset($filter['installed']);
        }
        $addons = implode(' AND ',$addons);
        if($addons) $addons.=' AND ';
        return $addons.parent::filter($filter);
    }

}
