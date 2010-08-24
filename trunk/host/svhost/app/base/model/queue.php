<?php

class base_mdl_queue extends base_db_model{

    var $limit = 100; //最大任务并发
    var $task_timeout = 300; //单次任务超时

    function filter($filter){
        unset($filter['use_like']);
        return parent::filter($filter);
    }

    function flush(){
        $base_url = kernel::base_url();
        foreach($this->db->select('select queue_id from sdb_base_queue limit '.$this->limit) as $r){
            $this->runtask($r['queue_id']);
        }
    }

    function worker(){
        ignore_user_abort(true);
        $task_id = $_POST['task_id'];
        $now = time();
        if($_POST['runkey']){
            $runkey = $_POST['runkey'];
            $this->db->exec('update sdb_base_queue set status="running",worker_active='.$now.'
                where queue_id='.intval($task_id).' and runkey='.$this->db->quote($runkey)); 
        }else{
            $runkey = md5(microtime().rand(0,9999));
            $this->db->exec($sql='update sdb_base_queue set status="running",runkey="'.$runkey.'",worker_active='.$now.'
                where queue_id='.intval($task_id).' and status="hibernate" 
                or (status="running" and worker_active<'.($now-$this->task_timeout).')');
        }
        if($this->db->affect_row()){
            $task_info = $this->dump($task_id);
            list($worker,$method) = explode('.',$task_info['worker']);
            $remaining = call_user_func_array( array(  $worker ,$method),array(&$task_info['cursor_id'],unserialize($task_info['params']) ) );
            if($remaining){
                $this->db->exec('update sdb_base_queue set status="hibernate",cursor_id='.$this->db->quote($task_info['cursor_id']).' where queue_id='.intval($task_id));
                $this->runtask($task_id,$remaining);
            }else{
                $this->db->exec('delete from sdb_base_queue where queue_id='.intval($task_id));
                exit;
            }
        }
    }

    function runtask($task_id){
        $http = kernel::single('base_httpclient');
        $_POST['task_id'] = $task_id;
        $url =  kernel::api_url('api.queue','worker',array('task_id'=>$task_id));
        kernel::log('Spawn [Task-'.$task_id.'] '.$url);
        
        //99%概率不会有问题
	$http->hostaddr = $_SERVER["SERVER_ADDR"]?$_SERVER["SERVER_ADDR"]:'127.0.0.1';
	$http->hostport = $_SERVER["SERVER_PORT"]?$_SERVER["SERVER_PORT"]:'80';
        $ret = $http->post($url,$_POST,null,null,true);
    }
      
}
