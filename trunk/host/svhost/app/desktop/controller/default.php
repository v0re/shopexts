<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_default extends desktop_controller{
    
    var $workground = 'desktop_ctl_dashboard';

    function index(){
        $desktop_user = kernel::single('desktop_user');
        
        $menus = $desktop_user->get_work_menu();
        $user_id = $this->user->get_id();
        $desktop_user->get_conf('fav_menus',$fav_menus);
        //默认显示5个workground
        if(!$fav_menus){
            $i = 0;
            foreach((array)$menus['workground'] as $key=>$value){
                if($i++>4) break;
                $fav_menus[] = $key;
            }
        }
        $this->pagedata['title'] = $titlename.' - Powered By ShopEx';
        $this->pagedata['session_id'] = kernel::single('base_session')->sess_id();
        $this->pagedata['uname'] = $this->user->get_name();
        $this->pagedata['param_id'] = $user_id;
        $this->pagedata['menus'] = $menus;
        $this->pagedata['fav_menus'] = (array)$fav_menus;
        $this->pagedata['shop_base']  = kernel::base_url(1);
        $this->pagedata['shopadmin_dir'] = ($_SERVER['REQUEST_URI']);
        $desktop_user->get_conf('shortcuts_menus',$shortcuts_menus);
        $this->pagedata['shortcuts_menus'] = (array)$shortcuts_menus;
        $desktop_menu = array();
        foreach(kernel::servicelist('desktop_menu') as $service){
            $array = $service->function_menu();
            $desktop_menu = (is_array($array)) ? array_merge($desktop_menu, $array) : array_merge($desktop_menu, array($array));
        }
        $this->pagedata['desktop_menu'] = (count($desktop_menu)) ? '<span>'.join('</span>|<span>', $desktop_menu).'</span>' : '';
        list($this->pagedata['theme_scripts'],$this->pagedata['theme_css']) =
            desktop_application_theme::get_files($this->user->get_theme());
       
        
        
        
        $this->openapi();
        
        $this->display('index.html');
        
    }
    
    
    public function openapi() {
        $params['certi_app']       = 'open.login'; 
        $this->Certi = base_certificate::get('certificate_id');
        $this->Token = base_certificate::get('token');
        $params['certificate_id']  = $this->Certi;  
        $token = $this->Token;
        $str   = '';
        ksort($params);
        foreach($params as $key => $value){
            $str.=$value;
        }
        $params['certi_ac'] = md5($str.$token);
        $params['format'] = 'image';
        $this->pagedata['open_api_url'] = LICENSE_CENTER_V .'?'. http_build_query( $params );
        //echo stripslashes(kernel::single('base_httpclient')->post( LICENSE_CENTER_V,$params ));
        //echo trim(stripslashes(kernel::single('base_httpclient')->post( LICENSE_CENTER_V,$params )), '"');
    }
    
    
    
    function set_favs(){
        $desktop_user = new desktop_user();
        $workground = $_POST['workgrounds'];
        $desktop_user->set_conf('fav_menus',$workground);
        echo '{success:"成功"}';
    }
    
    
    
    
    
    function allmenu(){
        $desktop_user = new desktop_user();
        $menus = $desktop_user->get_work_menu();
        $desktop_user->get_conf('shortcuts_menus',$shortcuts_menus);
        
        foreach($menus['workground'] as $k=>$v){
            $v['menu_group'] = $menus['menu'][$k];
            $workground_menus[$k]  = $v;
        }
        $this->pagedata['menus'] = $workground_menus;
        $this->pagedata['shortcuts_menus'] = (array)$shortcuts_menus;
        $this->display('allmenu.html');

    }
    
    function workground(){
        $wg = $_GET['wg'];
        if(!$wg){
            echo "参数错误";exit;
        }
        $user = new desktop_user();
        $menus = $this->app->model('menus');
        $group = $user->group();
        $aPermission = array();
        foreach((array)$group as $val){
            #$sdf_permission = $menus->dump($val);
            $aPermission[] = $val;
        }
        
        if($user->is_super()){
            $sdf = $menus->getList('*',array('menu_type' => 'menu','workground' => $wg));
        }
        else{
            $sdf = $menus->getList('*',array('menu_type' => 'menu','workground' => $wg,'permission' => $aPermission));
        }

        foreach((array)$sdf as $value){
            $url = $value['menu_path'];
            if($value['display'] == 'true'){
                $url_params = unserialize($value['addon']);       
                if(count($url_params['url_params'])>0){
                    foreach((array)$url_params['url_params'] as $key => $val){
                        $parmas =$params.'&'.$key.'='.$val; 
                    }
                }
                $url = $value['menu_path'].$parmas; break;
            }
               
        }
        $this->redirect('index.php?'.$url);
        
    }
    
    
    function alertpages(){
        $this->pagedata['goto'] = $_GET['goto'];
        $this->singlepage('loadpage.html');
    }
    
    
    
    function set_shortcuts(){
        $desktop_user = new desktop_user();
        $_POST['shortcuts'] = ($_POST['shortcuts']?$_POST['shortcuts']:array());
        foreach($_POST['shortcuts'] as $k=>$v){
            list($k,$v) = explode('|',$v);
            $shortcuts[$k] = $v;
        }
        $desktop_user->set_conf('shortcuts_menus',$shortcuts);
        header('Content-Type:text/jcmd; charset=utf-8'); 
        echo '{success:"设置成功"}';
    }
    
    
    
    
    
    
    function status(){

        set_time_limit(0);
        ob_start();
        if($_POST['events']){
            foreach($_POST['events'] as $worker=>$task){
                foreach(kernel::servicelist('desktop_task.'.$worker) as $object){
                    $object->run($task,$this);
                }
            }
        }

        $flow = &$this->app->model('flow');
        if($flow->fetch_role_flow($this->user)){
            echo '<script>alert("您有新短消息！");</script>';
        }

        $output = ob_get_contents();
        ob_end_clean();
        header('Content-length: '.strlen($output));
        header('Connection: close');
        echo $output;
        if(!isset($_POST['events'])){
            sleep(5);
        }

        app::get('base')->model('queue')->flush();
        kernel::single('base_misc_autotask')->trigger();
        kernel::single('base_session')->close(false);
    }

    function sel_region($path,$depth)
    {
        $path = $_GET['p'][0];
        $depth = $_GET['p'][1];
        
        header('Content-type: text/html;charset=utf8');
        //$local = app::get('ectools')->model('regions');
        //$ret = $local->get_area_select($path,array('depth'=>$depth));
        $local = kernel::single('ectools_regions_select');
        $ret = $local->get_area_select(app::get('ectools'),$path,array('depth'=>$depth));
        if($ret){
            echo '&nbsp;-&nbsp;'.$ret;
        }else{
            echo '';
        }
    }
}
