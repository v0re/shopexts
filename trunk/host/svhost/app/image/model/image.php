<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class image_mdl_image extends dbeav_model{
    var $defaultOrder = array();
    function store($file,$image_id,$size=null,$name=null){
        list($w,$h,$t) = getimagesize($file);
  
        $extname = array(
                1 => '.gif',
                2 => '.jpg',
                3 => '.png',
                6 => '.bmp',
            );
    
        if(!isset($extname[$t])){
            return false;
        }

        if($image_id){
            $params = $this->dump($image_id);
            if($name)
                $params['image_name'] = $name;
            $params['image_id'] = $image_id;
        }else{
            $params['image_id'] = $this->gen_id();
            $params['image_name'] = $name;
            $params['storage'] = $this->app->getConf('system.default_storager');
        }
        if(substr($file,0,4)=='http'){
            $params['storage'] = 'network';
            $params['url'] = $file;
            $params['ident'] = $file;
            $params['width'] = $w;
            $params['height'] = $h;
            $this->save($params);
            return $params['image_id'];
        }


        $storager = new base_storager();
        $params['last_modified'] = time();
        list($url,$ident,$no) = explode("|",$storager->save_upload($file,'','',$msg,$extname[$t]));
        if($size){
            $size = strtolower($size);
            $params[$size.'_url'] = $url;
            $params[$size.'_ident'] = $ident;
        }else{
            $params['url'] = $url;
            $params['ident'] = $ident;
            $params['width'] = $w;
            $params['height'] = $h;
        }
        parent::save($params);
        return $params['image_id'];
    }


    function rebuild($image_id,$sizes,$watermark=true){
        if($sizes){

            $cur_image_set = $this->app->getConf('image.set');
            $allsize = $this->app->getConf('image.default.set');

            $this->watermark_define = array();
            $this->watermark_default = '';

            $tmp_target = tempnam('/tmp', 'img');
            $img = $this->dump($image_id);
            if(is_array($img))  $org_file = $img['url'];

            if(substr($org_file,0,4)=='http'){
                $response = kernel::single('base_http')->action('get',$org_file);
                if($response===false){
                    $data = array('image_id'=>$image_id,'last_modified'=>time());
                    parent::save($data);
                    return true;                    
                }
                $org_file = tempnam('/tmp', 'imgorg');
                file_put_contents($org_file,file_get_contents($img['url']));
           }

            if(!file_exists($org_file)){
                $data = array('image_id'=>$image_id,'last_modified'=>time());
                parent::save($data);
                return true;
            }
           foreach($sizes as $s){

                if(isset($allsize[$s])){

                    $w = $cur_image_set[$s]['width'];
                    $h = $cur_image_set[$s]['height'];
                    $wh = $allsize[$s]['height'];
                    $wd = $allsize[$s]['width'];
                    $w = $w?$w:$wd;
                    $h = $h?$h:$wh;
                    image_clip::image_resize($this,$org_file,$tmp_target,$w,$h);
                    if($watermark&&$cur_image_set[$s]['wm_type']!='none'&&($cur_image_set[$s]['wm_text']||$cur_image_set[$s]['wm_image'])){
                        image_clip::image_watermark($this,$tmp_target,$cur_image_set[$s]);
                    }
                    $this->store($tmp_target,$image_id,$s);
                    // error_log($tmp_target,3,'d:\text.txt');

                }
            }
            unlink($tmp_target);
         }
    }

    function fetch($image_id,$size=null){
        $img = $this->dump($image_id);
        $k = $size?(strtolower($size).'_ident'):'ident';
        return PUBLIC_DIR.'/images'.$img[$k];
    }

    function attach($image_id,$target_type,$target_id){
    }

    function gen_id(){
        return md5(rand(0,9999).microtime());
    }

    function all_storages(){
        return; 
    }

    function modifier_storage(&$list){
        $all_storages = $this->all_storages();
        $all_storages['network'] = '远程';
        $list = (array)$list;
        foreach($list as $k=>$v){
            $list[$k] = $all_storages[$k];
        }
    }

}
