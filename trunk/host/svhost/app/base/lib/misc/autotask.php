<?php
class base_misc_autotask{

    function trigger(){

        set_time_limit(0);
        ignore_user_abort(1);
        $status = $this->status();
        $now = time();

        foreach(kernel::servicelist('autotask') as $k=>$o){
            foreach($this->type() as $name => $interval){
                if(isset($status[$k][$name]) && $now-$status[$k][$name]<$interval ){
                    continue;
                }
                kernel::log($k.'::'.$name);
                $o->$name();
                $data = array( 'last'=>$now, 'task'=>$k, $name=>$now,);
                app::get('base')->model('task')->replace($data ,array('task'=>$k));
            }
        }

    }

    function type(){
        return array(
            'minute' => 60,
            'hour' => 3600,
            'day'=> 3600*24,
            'week' => 3600*24*7,
            'month'=> 3600*24*30,
        );
    }

    function status(){
        $status = array();
        foreach(app::get('base')->model('task')->getlist('*') as $row){
            $status[$row['task']] = $row;
        }
        return $status;
    }

}
