<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_apps{

    var $addon_cols='local_ver,remote_ver,status,app_id';
    var $column_tools='操作';
    var $column_tools_width='150';
    function column_tools($row){
        $local_ver = $row[$this->col_prefix.'local_ver'];
        $remote_ver = $row[$this->col_prefix.'remote_ver'];
        $status = $row[$this->col_prefix.'status'];
        $app_id = $row[$this->col_prefix.'app_id'];

        $update_install_btn = '<button onclick="appmgr(\''.$app_id.'\').run([\'install\'])">升级并安装</button>';
        $download_install_btn = '<button onclick="appmgr(\''.$app_id.'\').run([\'install\'])">下载并安装</button>';
        $install_btn = '<button onclick="appmgr(\''.$app_id.'\').run([\'install\'])">安装</button>';
        
        $protetced_app = array('base','desktop','dbeav','pam');
        
        if(in_array($app_id,$protetced_app)){
            $uninstall_btn = '<!--<button disabled="disabled">卸载</button>-->';
        }else{
            $uninstall_btn = '<button onclick="appmgr(\''.$app_id.'\').run([\'uninstall\'])">卸载</button>';    
        }
        
        //$stop_btn = '<button onclick="appmgr(\''.$app_id.'\').run([\'stop\'])">关闭</button>';
        //$start_btn = '<button onclick="appmgr(\''.$app_id.'\').run([\'stop\'])">启动</button>';
        $update_btn = '<button onclick="appmgr(\''.$app_id.'\').run([\'download\',\'update\'])">升级</button>';

        $output = '';
        switch($status){
            case 'uninstalled':
            if(!$local_ver){
                $output .= $download_install_btn;
            }elseif(version_compare($remote_ver,$local_ver,'>')){
                $output .= $update_install_btn;  
            }else{
                $output .= $install_btn;     
            }
            break;

            case 'installed':
            $output .= $start_btn;
            $output .= $uninstall_btn;
            if(version_compare($remote_ver,$local_ver,'>')){
                $output .= $update_btn;
            }
            break;

            case 'active':
            $output .= $stop_btn;
            $output .= $uninstall_btn;
            if(version_compare($remote_ver,$local_ver,'>')){
                $output .= $update_btn;
            }
            break;
        }
        return $output;
    }

    var $detail_info='info';
    function detail_info($id){
        $render = app::get('desktop')->render();
        $render->pagedata['appinfo'] = app::get($id)->define();

        $docs = array();
        $dir = app::get($id)->app_dir.'/docs';
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if($file{0}!='.' && isset($file{5}) && substr($file,-4,4)=='.t2t' && is_file($dir.'/'.$file)){
                        $rs = fopen($dir.'/'.$file, 'r');
                        $docs[$file] = fgets($rs,1024);
                        fclose($rs);
                    }
                }
                closedir($dh);
            }
        }

        $render->pagedata['docs'] = $docs;

        return $render->fetch('appmgr/info.html');
    }

}
