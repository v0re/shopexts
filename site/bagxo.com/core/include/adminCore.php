<?php
require(CORE_DIR.'/kernel.php');
require(CORE_DIR.'/func_ext.php');
class adminCore extends kernel{
    var $_base_url;
    var $_err = array();
    var $ErrorSet = array();

    function adminCore(){
        define('PHP_SELF',dirname($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']));
        parent::kernel();
        if(file_exists(BASE_DIR.'/upgrade.php')){
            if($_GET['_ajax']){
                $url = 'index.php';
                $output =<<<EOF
<script>
        var href = top.location.href;
        var pos = href.indexOf('#') + 1;
        window.location.href="$url"+(pos ? ('&return='+encodeURIComponent(href.substr(pos))) : '');
</script>
EOF;
                echo $output;
                exit;
            }
            $upgrade = $this->loadModel('system/upgrade');
            $upgrade->exec($_GET['act']);
        }elseif($_GET['ctl']=='upgrade'){
            header('Location: index.php');
        }else{
            $this->run();
        }
    }

    function run(){
        if($_POST['api_url'] == 'time_auth'){
            header("Content-type:text/html;charset=utf-8");
            $this->shopex_auth=$this->loadModel('service/certificate');
            if($this->shopex_auth->check_api()){
                $data=$this->shopex_auth->show_pack_data();
                $data=json_encode($data);    
                echo $data;
                exit;
            }
        }

        define('__ADMIN__','admin');
        require_once('adminPage.php');
        $mod = $_GET['ctl']?$_GET['ctl']:'default';
        $act = $_GET['act']?$_GET['act']:'index';

        $this->request = array('action'=>array('controller'=>$mod,'method'=>$act));

        if(isset($_GET['pkg'])){
            define('__PKG__',$_GET['pkg']);
        }else{
            if($pos=strpos($mod,'/')){
                $domain = substr($mod,0,$pos);
            }else{
                $domain = $mod;
            }
            define('LOCALE_DIR',CORE_DIR.'/'.__ADMIN__.'/locale');
        }

        $this->session = $this->loadModel('opSession');
        $this->session->start();

        $controller = &$this->getController($mod);

        if(!is_object($controller)){
            $this->responseCode(404);
            exit();
        }
        if(!$this->callAction($controller,$act,$_GET['p'])){
            $this->responseCode(404);
            exit();
        }
    }

    function setExpries($time){;}

    /**
     * &getController
     *
     * @param mixed $mod
     * @access public
     * @return void
     */
    function &getController($mod,$args=null){
        require_once('pageFactory.php');
        $baseName = basename($mod,$args);
        $dirName = dirname($mod);

        $fname = CORE_DIR.'/'.__ADMIN__.'/controller/'.$dirName.'/ctl.'.$baseName.'.php';
        $loaded = include_once($fname);

        if (defined('CUSTOM_CORE_DIR') && include_once($cusfname = CUSTOM_CORE_DIR.'/'.__ADMIN__.'/controller/'.$dirName.'/cct.'.$baseName.'.php')){
            $mod_name = 'cct_'.$baseName;
        }elseif($loaded){
            $mod_name = 'ctl_'.$baseName;
        }else{
            return false;
        }

        if(class_exists($mod_name)){
            $object = new $mod_name($this);
            $object->system = &$this;
            $object->controller = $mod;
            if(!$object->in) $object->in = &$this->incomming();
            return $object;
        }else{
            return false;
        }
    }

    function mkUrl($ctl,$act='index',$args=null,$extName = 'html'){        
        return $this->realUrl($ctl,$act,$args,$extName,$this->request['base_url']);
    }
    function sfile($file,$file_bak=null,$use=false){
        $this->session->close();
        parent::sfile($file,$file_bak,$use);
    }

}
?>
