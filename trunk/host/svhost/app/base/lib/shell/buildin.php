<?php
class base_shell_buildin extends base_shell_prototype{

    var $vars;

    var $command_alias = array(
            'ls'=>'list',
            'q'=>'exit',
            'quit'=>'exit',
        );

    function php_call(){
        if($this->vars) extract($this->vars);
        $this->output(eval(func_get_arg(0)));
        $this->vars = get_defined_vars();
    }

    function command_reset(){ 
        $this->cmdlibs = null;
        $this->vars = null;
    }

    var $command_help_options = array(
            'verbose'=>array('title'=>'显示详细信息','short'=>'v'),
        );

    function command_help($help_item=null,$shell_command=null){
        if($help_item){
            list($app_id,$package) = explode(':',$help_item);
            $this->app_help($app_id , $package ,$shell_command);
        }else{
            $this->help();
            $this->output_line('应用提供的命令：');
            if ($handle = opendir(APP_DIR)) {
                while (false !== ($app_id = readdir($handle))) {
                    if($app_id{0}!='.' && is_dir(APP_DIR.'/'.$app_id) && is_dir(APP_DIR.'/'.$app_id.'/lib/command')){
                        $this->app_help($app_id);
                    }
                }
                closedir($handle);
            }
            $this->output_line('原生php命令');
            echo <<<EOF
输入命令如果以分号[;]结尾，则被认为是一条php语句.  例如:
  1> \$a = 2;
     int(2)
  2> pow(\$a,8);
     int(256)

EOF;
        }
    }

    function app_help($app_id,$package=null,$command=false){
        if($package){
            $commander = $this->shell->get_commander($app_id,$package);
            $commander->help($command);
        }else{
            if ($handle = opendir(APP_DIR.'/'.$app_id.'/lib/command')) {
                while (false !== ($file = readdir($handle))) {
                    if (substr($file,-4,4)=='.php' && is_file(APP_DIR.'/'.$app_id.'/lib/command/'.$file)) {
                        $commander = $this->shell->get_commander($app_id,substr($file,0,-4));
                        if($commander){
                            $commander->help();
                        }
                    }
                }
                closedir($handle);
            }
        }
    }

    function name_prefix(){
        return '';
    }

    var $command_exit = '退出';
    function command_exit(){ 
        echo 'exit'; 
        exit;
    }

    var $command_man = '显示帮助';
    function command_man(){
        $args = func_get_args();
        foreach($args as $arg){
            kernel::single('base_misc_man')->show($arg);
        }
    }

    var $command_sh = '执行操作系统命令';
    function command_sh($args){
        eval('system("'.str_replace('"','\\"',implode(' ',$args)).'");');
    }

    var $command_mkconfig = '创建config文件';
    var $command_mkconfig_options = array(
            'dbhost'=>array('title'=>'数据库服务器，默认:localhost','short'=>'h','need_value'=>1),
            'dbpassword'=>array('title'=>'数据库密码','short'=>'p','need_value'=>1),
            'dbuser'=>array('title'=>'数据库用户名','short'=>'u','need_value'=>1),
            'dbname'=>array('title'=>'数据库名','short'=>'n','need_value'=>1),
            'dbprefix'=>array('title'=>'数据库前缀, 默认sdb_','short'=>'a','need_value'=>1),
            'timezone'=>array('title'=>'时区','short'=>'t','need_value'=>1),
        );
    function command_mkconfig(){
			$options = $this->get_options();
			$options = array(
				'db_host'=>$options['dbhost']?$options['dbhost']:'localhost',
				'db_user'=>$options['dbuser']?$options['dbuser']:'root',
				'db_password'=>$options['dbpassword'],
				'db_name'=>$options['dbname'],
				'db_prefix'=>$options['dbprefix']?$options['dbprefix']:'sdb_',
                'default_timezone'=>$options['timezone'],
				);
			kernel::single('base_setup_config')->write($options);
    }

    var $command_ls = '列出所有应用';
    function command_ls(){ 
        $rows = app::get('base')->model('apps')->getlist('*',array('installed'=>true));
        foreach($rows as $k=>$v){
            $rows[$k] = array(
                    'app_id'=>$v['app_id'],
                    'name'=>$v['name'],
                    'version'=>$v['version'],
                    'status'=>$v['status']?$v['status']:'uninstalled',
                );
        }
        $this->output_table( $rows );
    }

    var $command_cd = '切换当前应用';
    function command_cd($app=''){

        if($app=='..'){
            $app = '';
        }elseif($app=='-'){
            $app = $this->last_app_id;
        }

        if($app{0}!='.' && is_dir(APP_DIR.'/'.$app)){
            $this->last_app_id = $this->shell->app_id;
            $this->shell->app_id = $app;
        }else{
            throw new Exception($app.": No such application.\n");
        }
    }

    var $command_install = '安装应用';
    var $command_install_options = array(
            'reset'=>array('title'=>'重新安装','short'=>'r'),
        );
    function command_install(){
        $args = func_get_args();
        $options = $this->get_options();
        $install_queue = kernel::single('base_application_manage')->install_queue($args,$options['reset']);
        
        foreach((array)$install_queue as $app_id=>$app_info){
            if(!$app_info){
                kernel::log('无法找到应用'.$app_id);
                return false;
            }
            $install_options = app::get($app_id)->runtask('install_options');
            if(is_array($install_options) && count($install_options)>0 && !$this->shell->input[$app_id]){
                do{
                    $this->shell->input_option($install_options,$app_id);
                }while(app::get($app_id)->runtask('checkenv',$this->shell->input[$app_id])===false);
            }
            kernel::single('base_application_manage')->install($app_id,$this->shell->input[$app_id]);
        }
    }

    var $command_uninstall = '卸载应用';
    var $command_uninstall_options = array(
            'recursive'=>array('title'=>'递归删除依赖之app','short'=>'r'),
        );
    function command_uninstall(){
		$args = func_get_args();
        $uninstall_queue = kernel::single('base_application_manage')->uninstall_queue($args);
		$options = $this->get_options();
		
		if(!$options['recursive']){
			foreach($uninstall_queue as $app_id=>$type){
				$to_delete[$type[1]][] = $app_id;
			}
			if($to_delete[1]){
				echo 'error in remove app '.implode(' ',$args)."\n";
				echo "以下应用依赖欲删除的app: ".implode(' ',$to_delete[1])."\n";
				echo "使用 -r 参数按依赖关系全部删除";
				return true;
			}
		}
		foreach($uninstall_queue as $app_id=>$type){
			kernel::single('base_application_manage')->uninstall($app_id);
		}
    }
    
    var $command_update = '升级应用程序';
    var $command_update_options = array(
            'sync'=>array('title'=>'升级应用程序信息库'),
            'sync-only'=>array('title'=>'仅升级应用程序信息库'),
            'force-download'=>array('title'=>'强制下载'),
            'download-only'=>array('title'=>'仅下载应用'),
        );
	function command_update(){
        $options = $this->get_options();
        if($options['sync'] || $options['sync-only']){
            kernel::single('base_application_manage')->sync();
        }
        
        if($options['sync-only']){
            return true;
        }
        
	    $args = func_get_args();
		if(!$args){
		    $rows = app::get('base')->model('apps')->getList('app_id',array('installed'=>1));
		    foreach($rows as $r){
		        $args[] = $r['app_id'];
		    }
		}
		foreach($args as $app_id){
			kernel::single('base_application_manage')->download($app_id,$options['force-download']);
		}
		if(!$options['download-only']){
		    foreach($args as $app_id){
    			kernel::single('base_application_manage')->update_app_content($app_id);
    		}
    		kernel::log('Applications database and services is up-to-date, ok.');
	    }
	}

    var $command_trace = '打开/关闭性能检测';
    function command_trace($mode=null){
        switch($mode){
        case 'on':
            $this->register_trigger('trace');
            break;

        case 'off':
            $this->unregister_trigger('trace');
            break;
        }
        $this->shell->skip_trigger = true;
        echo 'Trace mode is ' , $this->shell->trigger['trace']?'on':'off';
    }

    var $command_search = '在程序库中搜索';
    function command_search(){
        $keywrods = func_get_args();
        foreach($keywrods as $word){
            $where[] = "app_id like '%{$word}%' or app_name like '%{$word}%' or `description` like '%{$word}%'";
        }
        $sql = 'select app_id,app_name,description,local_ver,remote_ver from sdb_base_apps where 1 and '.implode(' and ',$where);
        $rows = kernel::database()->select($sql);
        $this->output_table( $rows );
    }

    function begin_trace(){
        $this->memtrace_begin = memory_get_usage();
        list($usec, $sec) = explode(" ", microtime());
        $this->time_start = ((float)$usec + (float)$sec);
    }

    function end_trace(){
        if(!$this->memtrace_begin)return ;
        list($usec, $sec) = explode(" ", microtime());
        $time_start = ((float)$usec + (float)$sec);
        $mem = memory_get_usage() - $this->memtrace_begin;

        list($usec, $sec) = explode(" ", microtime());
        $timediff = ((float)$usec + (float)$sec) - $this->time_start;
        printf("\n * Command memroy useage = %d, Time left = %f " , $mem , $timediff);
    }
    
    public $command_createproject = '创建新项目';
    function command_createproject($project_name=null){
        if(!$project_name){
             $project_name = readline('Project name: ');
        }
        while(file_exists($project_name)){
             $project_name = readline('Project already exists. enter anthoer one: ');
        }
        
        //init files
        $base_dir = realpath(dirname(__FILE__).'/../../');
        kernel::log('Init project '.realpath($project_name),1);
        utils::cp($base_dir.'/examples/project',$project_name);
        utils::cp($base_dir,$project_name.'/app/base');
        utils::cp($base_dir.'/examples/app',$project_name.'/app/'.$project_name);
        chmod($project_name.'/app/base/cmd',0744);
        chmod($project_name.'/data',0777);
        utils::replace_p($project_name.'/config',array('%*APP_NAME*%'=>$project_name));
        utils::replace_p($project_name.'/app/'.$project_name,array('%*APP_NAME*%'=>$project_name));
        
        kernel::log('. done!');
        
        do{
            $install_confirm = readline('Install now? [Y/n] ');
            switch(strtolower(trim($install_confirm))){
                case '':
                case 'y':
                    $install_confirm = true;
                    $command_succ = true;
                break;
                
                case 'n':
                    $install_confirm = false;
                    $command_succ = true;
                break;
                
                default:
                    $command_succ = false;
            }
        }while(!$command_succ);
        
        $install_command = 'app'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'cmd install '.$project_name;
        $project_dir = realpath($project_name);
            
        if($install_confirm){
            kernel::log('Installing...');
            kernel::log("\n".$project_dir.' > '.$install_command."\n");
            chdir($project_dir);
            passthru($install_command);
        }else{
            "Change dir to $project_dir: ".$install_command;
        }
    }

    var $command_kvstorerecovery = 'kvstore数据恢复';
    function command_kvstorerecovery($instance=null) 
    {
        if(!is_null($instance) && !defined('FORCE_KVSTORE_STORAGE')){
            $instance = trim($instance);
            if(!(strpos($instance, '_') === 0)){
                $instance = 'base_kvstore_' . $instance;
            }
            define('FORCE_KVSTORE_STORAGE', $instance);
        }
        $testObj = base_kvstore::instance('test');
        if(get_class($testObj->get_controller()) === 'base_kvstore_mysql'){
            kernel::log('The \'base_kvstore_mysql\' is default persistent, Not necessary recovery');
            exit;
        }
        kernel::log('KVstore Recovery...');
        $db = kernel::database();
        $count = $db->count('SELECT count(*) AS count FROM sdb_base_kvstore', true);
        if(empty($count)){
            kernel::log('No data recovery');
            exit;
        }
        $pagesize = 10;
        $page = ceil($count / 10);
        for($i=0; $i<$page; $i++){
            $rows = $db->selectlimit('SELECT * FROM sdb_base_kvstore', $pagesize, $i*$pagesize);
            foreach($rows AS $row){
                //kernel::log($row['key']);continue;
                if(base_kvstore::instance($row['prefix'])->store($row['key'], unserialize($row['value']))){
                    kernel::log($row['prefix'] .'=>' . $row['key'] . ' ... Recovery Success');
                }else{
                    kernel::log($row['prefix'] .'=>' . $row['key'] . ' ... Recovery Failure');
                }
            }
        }
    }//End Function

    var $command_cacheclean = '清除缓存';
    function command_cacheclean() 
    {
        kernel::log('Cache Clear...');
        cachemgr::init(true);
        if(cachemgr::clean($msg)){
            kernel::log($msg ? $msg : '...Clear Success');
        }else{
            kernel::log($msg ? $msg : '...Clear Failure');
        }
        cachemgr::init(false);
    }//End Function
}

