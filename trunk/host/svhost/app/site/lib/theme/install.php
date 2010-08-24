<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_theme_install 
{

    public function check_install() 
    {
        $this->check_dir();
        $d = dir(THEME_DIR);
        while (false !== ($entry = $d->read())) {
            if(in_array($entry, array('.', '..', '.svn')))   continue;
            if(is_dir(THEME_DIR . '/' . $entry)){
                $themeData = app::get('site')->model('themes')->select()->where('theme = ?', $entry)->instance()->fetch_row();
                if(empty($themeData)){
                    $this->init_theme($entry);
                }
            }
            if(!kernel::single('site_theme_base')->get_default()){
                kernel::single('site_theme_base')->set_default($entry);
            }
        }
        $d->close();
    }//End Function

    public function check_dir() 
    {
        if(!is_dir(THEME_DIR))
            utils::mkdir_p(THEME_DIR);
    }//End Function
    
    public function allow_upload(&$message){
        if(!function_exists("gzinflate")){
            $message = 'gzip';
            return $message;
        }
        if(!is_writable(THEME_DIR)){
            $message = 'writeable';
            return $message;
        }
        return true;
    }

    public function remove_theme($theme) 
    {
        $dir = THEME_DIR . '/' . $theme;
        if(is_dir($dir)){
            $this->flush_theme($theme);
            return $this->__remove_dir($dir);
        }else{
            return false;
        }
    }//End Function

    public function flush_theme($theme) 
    {
        app::get('site')->model('themes')->delete(array('theme'=>$theme));
        app::get('site')->model('themes_tmpl')->delete(array('theme'=>$theme));
        kernel::single('site_theme_widget')->delete_widgets_by_theme($theme);
    }//End Function

    public function install($file, &$msg) 
    {
        $this->check_dir();
        if(!$this->allow_upload($msg)) return false;
        $tar = kernel::single('base_tar');
        $handle = fopen($file['tmp_name'], "r");
        $contents = file_get_contents($file['tmp_name']);
        preg_match('/\<id\>(.*?)\<\/id\>/',$contents,$tar_name);
        $filename=$tar_name[1]?$tar_name[1]:time();
        if(is_dir(THEME_DIR.'/'.trim($filename))){
           $filename=time();
        }
        $sDir=$this->__build_dir(str_replace('\\','/',THEME_DIR.'/'.trim($filename)));
        if($tar->openTAR($file['tmp_name'], $sDir)){
            if($tar->containsFile('theme.xml')) {

                foreach($tar->files as $id => $file) {
                    if(!preg_match('/\.php/',$file['name'])){
                        $fpath = $sDir.$file['name'];
                        if(!is_dir(dirname($fpath))){
                            if(mkdir(dirname($fpath), 0755, true)){
                                file_put_contents($fpath,$tar->getContents($file));
                            }else{
                                $msg = __('权限不允许');
                                return false;
                            }
                        }else{
                            file_put_contents($fpath,$tar->getContents($file));
                        }
                    }
                }
                $tar->closeTAR();

                if(!$config=$this->init_theme(basename($sDir),'','upload')){
                    $this->__remove_dir($sDir);
                    $msg=__('shopEx模板包创建失败');
                    return false;
                }
                return $config;
            }else{
                $msg = __('不是标准的shopEx模板包');
                return false;
            }
        }else{
            $msg = __('模板包已损坏，不是标准的shopEx模板包').$file['tmp_name'];
            return false;
        }
    }

    public function init_theme($theme, $replaceWg=false, $upload='', $loadxml=''){
        if(empty($loadxml)){
            $loadxml='theme.xml';
        }
        $configxml='info.xml';
        $this->separatXml($theme);
        $sDir=THEME_DIR.'/'.$theme.'/';
        $xml = kernel::single('site_utility_xml');
        if(is_file($sDir.$loadxml)){
            $wightes_info = $xml->xml2arrayValues(file_get_contents($sDir . $loadxml));
        }
        if(is_file($sDir.$configxml)){
            $config = $xml->xml2arrayValues(file_get_contents($sDir . $configxml));
        }elseif(!empty($wightes_info)){
            $config = $wightes_info;
        }else{
            return false;
        }

        if($upload=="upload" && $config['theme']['id']['value']){
            $config['theme']['id']['value']=preg_replace('@[^a-zA-Z0-9]@','_',$config['theme']['id']['value']);
            if($this->file_rename(THEME_DIR.'/'.$theme,THEME_DIR.'/'.$config['theme']['id']['value'])){
                $sDir=THEME_DIR.'/'.$config['theme']['id']['value'];
                $theme=$config['theme']['id']['value'];
                $replaceWg=false;
            }
        }
        $aTheme=array(
            'name'=>$config['theme']['name']['value'],
            'id'=>$config['theme']['id']['value'],
            'version'=>$config['theme']['version']['value'],
            'info'=>$config['theme']['info']['value'],
            'author'=>$config['theme']['author']['value'],
            'site'=>$config['theme']['site']['value'],
            'update_url'=>$config['theme']['update_url']['value'],
            'config'=>array(
                'config'=>$this->__change_xml_array($config['theme']['config']['set']),
                'borders'=>$this->__change_xml_array($config['theme']['borders']['set']),
                'views'=>$this->__change_xml_array($config['theme']['views']['view'])
            )
        );

        $aWidgets=$wightes_info['theme']['widgets']['widget'];
        $aTheme['theme']=$theme;
        $aTheme['stime']=time();
        if(is_array($aTheme['config']['views']) && count($aTheme['config']['views'])>0){
            foreach($aTheme['config']['views'] as $v){
                $tmp[]=$v['tpl'];
            }
            $aTheme['template']=implode(',',$tmp);
        }else{
            $aTheme['template']='';
        }

        for($i=0;$i<count($aWidgets);$i++){
        	if($aWidgets[$i]['attr']['coreid']) {
        		$aTmp[$i]['core_file']=$aTheme['theme'].'/'.$aWidgets[$i]['attr']['file'];
	            $aTmp[$i]['core_slot']=$aWidgets[$i]['attr']['slot'];
	            $aTmp[$i]['core_id']=$aWidgets[$i]['attr']['coreid'];
        	} else {
	            $aTmp[$i]['base_file']='user:'.$aTheme['theme'].'/'.$aWidgets[$i]['attr']['file'];
	            $aTmp[$i]['base_slot']=$aWidgets[$i]['attr']['slot'];
	            $aTmp[$i]['base_id']=$aWidgets[$i]['attr']['baseid'];
	        }
            $aTmp[$i]['widgets_type']=$aWidgets[$i]['attr']['type'];
            $aTmp[$i]['widgets_order']=$aWidgets[$i]['attr']['order'];
            $aTmp[$i]['title']=$aWidgets[$i]['attr']['title'];
            $aTmp[$i]['domid']=$aWidgets[$i]['attr']['domid'];
            $aTmp[$i]['border']=$aWidgets[$i]['attr']['border'];
            $aTmp[$i]['classname']=$aWidgets[$i]['attr']['classname'];
            $aTmp[$i]['tpl']=$aWidgets[$i]['attr']['tpl'];
            $aTmp[$i]['app']=$aWidgets[$i]['attr']['app'];
            

            /*$set=$aWidgets[$i]['set'];
            $aSet = array();
            for($y=0;$y<count($set);$y++){
                $aSet[$set[$y]['attr']['key']]=$set[$y]['attr']['value'];
            }*/
            $aTmp[$i]['params']= unserialize(strtr($aWidgets[$i]['value'], array_flip(get_html_translation_table(HTML_SPECIALCHARS))));
        }
        $aWidgets=$aTmp;

        $this->flush_theme($theme); //flush数据

        $aNumber= kernel::single('site_theme_widget')->count_widgets_by_theme($theme);
        $nNumber=intval($aNumber);
        $insertWidgets=false;
        //echo $nNumber;exit;
        if($replaceWg){

            if($nNumber){
                kernel::single('site_theme_widget')->delete_widgets_by_theme($theme);
            }
            $insertWidgets=true;
        }else{
            if($nNumber==0){
                $insertWidgets=true;
            }
        }
        if($insertWidgets && count($aWidgets)>0){

            foreach($aWidgets as $k=>$wg){
                kernel::single('site_theme_widget')->insert_widgets($wg);
            }
        }

        kernel::single('site_theme_tmpl')->install($theme);

        if(!kernel::single('site_theme_base')->update_theme($aTheme)){
            return false;
        }else{
            return $aTheme;
        }
    }

    private function __remove_dir($sDir) {
        if($rHandle=opendir($sDir)){
            while(false!==($sItem=readdir($rHandle))){
                if ($sItem!='.' && $sItem!='..'){
                    if(is_dir($sDir.'/'.$sItem)){
                        $this->__remove_dir($sDir.'/'.$sItem);
                    }else{
                        if(!unlink($sDir.'/'.$sItem)){
                            trigger_error(__('因权限原因，模板文件').$sDir.'/'.$sItem.__('无法删除'),E_USER_NOTICE);
                        }
                    }
                }
            }
            closedir($rHandle);
            rmdir($sDir);
            return true;
        }else{
            return false;
        }
    }

    private function __build_dir($sDir){
        if(file_exists($sDir)){
            $aTmp=explode('/',$sDir);
            $sTmp=end($aTmp);
            if(strpos($sTmp,'(')){
                $i=substr($sTmp,strpos($sTmp,'(')+1,-1);
                $i++;
                $sDir=str_replace('('.($i-1).')','('.$i.')',$sDir);
            }else{
                $sDir.='(1)';
            }
            return $this->__build_dir($sDir);
        }else{
            return $sDir.'/';
        }
    }

    private function __change_xml_array($aArray){
        $aData = array();
        if(is_array($aArray)){
            foreach($aArray as $i=>$v){
                unset($v['attr']);
                $aData[$i]=array_merge($v,$aArray[$i]['attr']);
            }
        }
        return $aData;
    }

    private function separatXml($theme){
        $workdir = getcwd();
        chdir(THEME_DIR.'/'.$theme);
        if(!is_file('info.xml')){
            $content=file_get_contents('theme.xml');
            $rContent=substr($content,0,strpos($content,'<widgets>'));
            file_put_contents('info.xml',$rContent.'</theme>');
        }
        chdir($workdir);

    }

    private function file_rename($source,$dest){
        if(is_file($dest)){
            if(PHP_OS=='WINNT'){
                @copy($source,$dest);
                @unlink($source);
                if(file_exists($dest)) return true;
                else return false;
            }else{
                return @rename($source,$dest);
            }
        }else{
            return false;
        }
    }

}//End Class
