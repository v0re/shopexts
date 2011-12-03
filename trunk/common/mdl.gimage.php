<?php
/**
 * mdl_gpic
 *
 * @uses modelFactory
 * @package
 * @version $Id: mdl.gimage.php 1867 2008-04-23 04:00:24Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@zovatech.com>
 * @license Commercial
 */
require_once(CORE_DIR.'/model/system/mdl.storager.php');
class mdl_gimage extends mdl_storager{

    var $defaultImages = array('thumbnail','small','big');
    var $elmar_storage='';
    function mdl_gimage(){
        parent::mdl_storager();
        $this->system = &$GLOBALS['system'];
        $this->db = &$this->system->database();
        $this->base_url ='http://img0.jinbaowang.cn/';
        //载入图库类
        if( $this->system->getConf('elmar.status') ){
            $elmar_storage_file = PLUGIN_DIR.'/elmar_storage/elmar_storage.php';
            //error_log("elmar_storage_file:".$elmar_storage_file."\n",3,HOME_DIR."/logs/test.log");
            if( file_exists($elmar_storage_file) ){
                require_once($elmar_storage_file);
                $this->elmar_storage = new elmar_storage();
            }
        }
    }

    function size(){
        $retArr = array();
        foreach($this->defaultImages as $img){
            $retArr[$img] = array('width'=>$this->system->getConf('site.'.$img.'_pic_width'),
                'height'=>$this->system->getConf('site.'.$img.'_pic_height'));
        }

        return $retArr;
    }

    //??ó???
    function getFileSet($ident){
        $tmpArr = explode('|',$ident,3);
        $fileExt[0] = substr($tmpArr[0],strrpos($tmpArr[0],'.'));
        $fileExt[1] = substr($tmpArr[1],strrpos($tmpArr[1],'.'));
        $fileName[0] = substr($tmpArr[0],0,strrpos($tmpArr[0],'.'));
        $fileName[1] = substr($tmpArr[1],0,strrpos($tmpArr[1],'.'));
        $ret = array();
        foreach($this->defaultImages as $v){
            $ret[$v] = $fileName[0].'_'.$v.$fileExt[0].'|'.$fileName[1].'_'.$v.$fileExt[1].'|'.$tmpArr[2];
        }
        return $ret;
    }

    function uploader(){
        /**
        *    @function    :    ????
        *    @notice tito check:???mime???
        */
        $itype =array('*.gif','*.jpg','*.png');

        return implode('; ',$itype);
    }

    function __get_local_file($local_id,$test_dir=false){
        $path = HOME_DIR.'/upload/'.$local_id;
        if(!is_dir($dir = dirname($path))){
            mkdir_p($dir,0777);
        }
        return $path;
    }
    function get_resource_by_id($gimage_id,$type){
        $row=$this->db->selectrow('select * from sdb_gimages where gimage_id='.intval($gimage_id));
        return $this->getUrl($row[$type]);
    }
    function display_source_by_id($gimage_id){
        $row = $this->db->selectrow('select source from sdb_gimages where gimage_id='.intval($gimage_id));
        $file= $this->__get_local_file($row['source']);
        $type = ext_name($file);
        header('Content-type: image/'.substr($type,1));
        $this->system->sfile($file);
    }

    function insert_new($file_info,$goods_id){
        $rs = $this->db->exec('select * from sdb_gimages where 0=1');
        $file_info['goods_id'] = $goods_id;
        $sql = $this->db->getInsertSQL($rs,$file_info);
        $this->db->exec($sql);
        return $this->db->lastInsertId();
    }

    function clone_from_goods($id){
        $imgSrc = $this->get_source_by_id( $id, 'source');
        $ext_name = explode('.',$imgSrc);
        $newImgSrc = 'gpic/'.date('Ymd').'/'.md5($imgSrc.implode(',',microtime())).'.'.$ext_name[count($ext_name)-1];
        copy( $this->__get_local_file($imgSrc,true) , $this->__get_local_file($newImgSrc,true) );
        $row = $this->db->selectrow($sql = 'select src_size_width as width, src_size_height as height from sdb_gimages where gimage_id = '.$id);
        $data = array(
            'source'=>$newImgSrc,
            'src_size_width'=>$row['width'],
            'src_size_height'=>$row['height'],
            'is_remote'=>'false',
            'up_time'=>time()
        );
        $rs = $this->db->exec($sql = 'select * from sdb_gimages where 0=1');
        $sql = $this->db->getInsertSQL($rs,$data);
        if($this->db->exec($sql)){
            $data['gimage_id'] = $this->db->lastInsertId();
            return $data;
        }
        return null;
    }

    function remove_by_goods_id($goods_id){
        $row=$this->db->selectrow('select thumbnail_pic from sdb_goods where goods_id='.intval($goods_id));
        $this->remove($row['thumbnail_pic']);
        return $this->clean($goods_id);
    }

    function get_source_by_id($gimage_id, $type){
        $row=$this->db->selectrow('select * from sdb_gimages where gimage_id='.intval($gimage_id));
        return $row[$type];
    }

    function get_img_by_source($dir, $type){
        $row=$this->db->selectrow('select * from sdb_gimages where source=\''.$dir.'\'');
        return $row[$type];
    }

    function save_upload($pic){
        if($pic['error']){
            return $this->__check_upload($pic);
        }else{
            //???
            if($pic['tmp_name']){
                $ext_name = array(
                    '1'=>'.gif',
                    '2'=>'.jpg',
                    '3'=>'.png',
                    '6'=>'.bmp',
                    '15'=>'.wbmp',
                    '16'=>'.xbm',
                );
                list($size_width,$size_height,$image_type) = getimagesize($pic['tmp_name']);
                if(!isset($ext_name[$image_type])){
                    trigger_error('unknow image type'.$image_type,E_USER_ERROR);
                    return false;
                }
                $image_type = $ext_name[$image_type];
            }else{
                return false;
            }

            $local_id = md5($pic['tmp_name'].implode(',',microtime())).$image_type;
            $local_id = 'gpic/'.date('Ymd').'/'.$local_id;
            //д???
            if(strstr($pic['tmp_name'], '/home/upload/')){
                if(!copy($pic['tmp_name'], $file=$this->__get_local_file($local_id,true))){
                    trigger_error('can\'t copy image file from '.$pic['tmp_name'].' to '.$file,E_USER_ERROR);
                    return false;
                }
                @unlink($pic['tmp_name']);
            }else{
                if(!move_uploaded_file($pic['tmp_name'], $file=$this->__get_local_file($local_id,true))){
                    trigger_error('can\'t move image file from '.$pic['tmp_name'].' to '.$file,E_USER_ERROR);
                    return false;
                }
            }

            //д?
            $rs = $this->db->exec($sql = 'select * from sdb_gimages where 0=1');

            $data = array(
                    'source'=>$local_id,
                    'src_size_width'=>$size_width,
                    'src_size_height'=>$size_height,
                    'is_remote'=>'false',
                    'up_time'=>time()
                );
            if($pic['goods_id']){
                $data['goods_id'] = $pic['goods_id'];
            }
            $sql = $this->db->getInsertSQL($rs,$data);
            if($this->db->exec($sql)){
                $data['gimage_id'] = $this->db->lastInsertId();
                return $data;
            }else{
                trigger_error('Error to insert: '.$sql,E_USER_ERROR);
                return false;
            }
        }
    }

    function getUrl($ident,$size=null){
        if($ident){
            return parent::getUrl($ident);
        }else{
            return isset($this->_default[$size])?$this->_default[$size]:($this->_default[$size] = parent::getUrl($this->system->getConf('site.default_'.$size.'_pic')));
        }
    }

    function getImageExt($srcFile){
        if(!file_exists($srcFile)) return false;
        $info = getimagesize($srcFile);

        switch($info[2]){
            case 1: //gif
                $ext = '.gif';
                break;
            case 2: //jpg
                $ext = '.jpg';
                break;
            case 3: //png
                $ext = '.png';
                break;
            case 6: //bmp
                $ext = '.bmp';
                break;
            case 15: //wbmp
                $ext = '.wbmp';
                break;
            case 16: //xbm
                $ext = '.xbm';
                break;
            default:
                return false;
        }
        return $ext;
    }

    function _last_cfg_time(){
        return $this->system->getConf('system.watermark.lastcfg');
    }

    function gen_all_size_by_goods_id($goods_id,$default_gimage_id,$user_defined_image=false,$use_same_name=false){
        $goods_id = intval($goods_id);
        if($goods_id && $this->_gen_all_size('goods_id='.intval($goods_id),$goods_id,true,$use_same_name)){
            $sql = 'select * from sdb_gimages where goods_id='.$goods_id;
            if($default_gimage_id=intval($default_gimage_id)){
                $sql.=' and gimage_id='.intval($default_gimage_id);
            }
            $gimage = $this->db->selectrow($sql);
            $data = array(
                'goods_id'=>$goods_id,
                'small_pic'=>$gimage['small'],
                'big_pic'=>$gimage['big'],
                'thumbnail_pic'=>$gimage['thumbnail'],
                );
            if($user_defined_image){
                unset($data['thumbnail_pic']);
            }
            return $this->toUpdateImages($data);
        }
    }

    function _gen_all_size($where='0=1',$gid=null,$force_build=false,$use_same_name=false){
        //return true;
        $now = time(); 
        $sql = 'select gimage_id,is_remote,source,big,small,thumbnail,up_time from sdb_gimages where '.$where;

        if(!$force_build){
            $last_cfg_time = $this->_last_cfg_time();
            $sql.=' and (small is null or big is null or thumbnail is null or up_time<'.($last_cfg_time?$last_cfg_time:$now).')';
        }

        //?ν??????????
        
        $rows = $this->db->select($sql);
        foreach($rows as $r){
            if($r['is_remote']=='true'){
                continue;
            }
            $ext_name = ext_name($r['source']);
            $tmp_files = array();
            foreach($this->defaultImages as $tag){
                $tmp_file = tempnam(HOME_DIR.'/tmp','gimg_');
                $this->make_one_image($this->__get_local_file($r['source']),$tmp_file,$tag);
                if($use_same_name){
                    $r[$tag] = $this->replace($tmp_file,$r[$tag],'goods',substr(md5($tmp_file.$r['gimage_id']),0,16).$ext_name);
                }else{
                    if($r[$tag]){
                        $this->remove($r[$tag]);
                    }
                    $r[$tag] = $this->save($tmp_file,'goods',substr(md5($tmp_file.$r['gimage_id']),0,16).$ext_name);
                }
                $tmp_files[$tag] = BASE_DIR.'/'.substr($r[$tag],0,strpos($r[$tag],'|'));
            }
            
            if( $this->elmar_storage ){
                if( $w = strpos($r['big'],'|img_id:') ){
                    $old_img_id = substr($r['big'],$w+strlen('|img_id:'));
                    if($old_img_id){
                        $this->elmar_storage->delete($old_img_id);
                    }
                }
                $elmar_imgs = $this->elmar_storage->save($this->__get_local_file($r['source']));
                //error_log("elmar_imgs:".print_r($elmar_imgs,1)."\n",3,HOME_DIR."/logs/test.log");
                //error_log("tmp_files:".print_r($tmp_files,1)."\n",3,HOME_DIR."/logs/test.log");
                if( $tmp_files && is_array($tmp_files) ){
                    foreach($tmp_files as $k=>$v){
                        if($k=='thumbnail')$k='thumb';
                        if(!$this->elmar_storage->update($elmar_imgs['image_id'],$v,$k)){
                            trigger_error('elmar图库更新失败',E_USER_WARNING);
                        }
                    }
                }
                $r['big'] .= '|'.$elmar_imgs['big'].'|img_id:'.$elmar_imgs['image_id'];
                $r['small'] .= '|'.$elmar_imgs['small'].'|img_id:'.$elmar_imgs['image_id'];
                $r['thumbnail'] .= '|'.$elmar_imgs['thumb'].'|img_id:'.$elmar_imgs['image_id'];
            }
            $rs = $this->db->exec('select * from sdb_gimages where gimage_id='.intval($r['gimage_id']));
            $r['up_time'] = $now;
            if($gid){
                $r['goods_id'] = $gid;
            }
            $sql = $this->db->getUpdateSQL($rs,$r);
            if($sql && !$this->db->exec($sql)){
                trigger_error($this->db->errorInfo(),E_USER_WARNING);
            }
        }
        
        return true;
    }

    //??:?
    function saveImage($gid, $imgThumbnail, $imgDefault, $aImgFile, $imgUdf='false',&$newThumbnail){    
        foreach($aImgFile as $k=>$v){
            if(!$v){
                unset($aImgFile[$k]);
            }
        }
        if(!isset($aImgFile[0])){
            $aImgFile = array(0);
        }

        $this->clean($gid, $aImgFile);
        $this->_gen_all_size('gimage_id in ('.implode(',',$aImgFile).')',$gid);
        if(!($row = $this->db->selectrow('select gimage_id,small,thumbnail,big from sdb_gimages where gimage_id='.intval($imgDefault)))){
            $row = $this->db->selectrow('select gimage_id,small,thumbnail,big from sdb_gimages where goods_id='.intval($gid));
            $imgDefault = $row['gimage_id'];
        }

        if(substr($newThumbnail,0,4)=='http'){
            $row['thumbnail'] = $newThumbnail;
        }
        $data = array(
            'thumbnail_pic' => $row['thumbnail'],
            'small_pic' => $row['small'],
            'big_pic' => $row['big'],
            'image_default' => $imgDefault,
            'goods_id' => $gid,
            'udfimg' => $imgUdf
            );

        $old_img = $this->db->selectrow('select udfimg,thumbnail_pic from sdb_goods where goods_id='.intval($gid));
        if($imgUdf=='true' && $newThumbnail['goods_thumbnail_pic'] && substr($newThumbnail,0,4)!='http'){    //?????
            $data['thumbnail_pic'] = parent::save_upload(
                $newThumbnail['goods_thumbnail_pic']
                ,'goods',
                    substr(md5(implode(',',microtime().rand(0,time()))),0,16) //?
                    .ext_name($newThumbnail['goods_thumbnail_pic']['name'])
                );
            if( $this->elmar_storage ){
                if( $w = strpos($old_img['thumbnail_pic'],'|img_id:') ){
                    $old_img_id = substr($old_img['thumbnail_pic'],$w+strlen('|img_id:'));
                    if($old_img_id){
                        $file = BASE_DIR.'/'.substr($data['thumbnail_pic'],0,strpos($data['thumbnail_pic'],'|'));
                        if(!$this->elmar_storage->update($old_img_id,$file,'thumb')){
                            trigger_error("更新图库信息失败",E_USER_WARNING);
                        }
                        $elmer_t_url = explode('|',$old_img['thumbnail_pic']);
                        $data['thumbnail_pic'] .='|'.$elmer_t_url[3].'|img_id:'.$old_img_id;
                    }
                }
            }
            
            
            if($old_img['udfimg']=='true'){
                $this->remove($old_img['thumbnail_pic']);
            }
        }else{  //???
            if($imgUdf=='false' && $old_img['udfimg']=='true'){
                $this->remove($old_img['thumbnail_pic']);
            }
            if($imgUdf=='true' && $old_img['udfimg']=='true'){
                unset($data['thumbnail_pic']);
            }
        }
        
        return $this->toUpdateImages($data);
    }

    function clean($gid, $aImgFile=null){
        if(!isset($aImgFile[0])){
            $aImgFile = array(0);
        }

        $sql = 'select i.* from sdb_gimages i left join sdb_goods g
                    on g.goods_id=i.goods_id
                    where
                    (g.goods_id=null and i.up_time < '.(time()-36000).')
                    or
                    (g.goods_id = '.intval($gid).' and i.gimage_id not in ('.implode(',',$aImgFile).'))';
        $to_del = array();
        foreach($this->db->select($sql) as $row){
             unlink(HOME_DIR.'/upload/'.$row['source']);
             if($row['small']){
                 $this->remove($row['small']);
             }
             if($row['big']){
                 $this->remove($row['big']);
             }
             if($row['thumbnail']){
                 $this->remove($row['thumbnail']);
             }
             $to_del[] = $row['gimage_id'];
        }
        if(isset($to_del[0])){
            $this->db->exec('delete from sdb_gimages where gimage_id in ('.implode(',',$to_del).')');
        }
        return true;
    }

    function get_by_goods_id($gid){
        foreach($this->db->select('select * from sdb_gimages where goods_id='.intval($gid)) as $row){
            if( $this->system->getConf('elmar.status') ){
                $tmp = $this->parse($row['small']);
                $row['small']=$tmp['url'];
                $tmp = $this->parse($row['big']);
                $row['big']=$tmp['url'];
                $tmp = $this->parse($row['thumbnail']);
                $row['thumbnail']=$tmp['url'];
            }
            $return[$row['gimage_id']] = $row;
        }
        return $return;
    }
    function get_by_gimage_id($gid,$gImgAry=''){
        foreach($this->db->select('select * from sdb_gimages where goods_id='.intval($gid).' and gimage_id IN ('.implode(',',$gImgAry).')') as $row){
            $return[$row['gimage_id']] = $row;
        }
        return $return;
    }
    function toUpdateImages($aData){
        $rs = $this->db->exec('SELECT udfimg,image_default,big_pic,small_pic,thumbnail_pic FROM sdb_goods WHERE goods_id='.$aData['goods_id']);
        $sql = $this->db->getUpdateSQL($rs, $aData,false,1);
        return (!$sql || $this->db->exec($sql));
    }

    function make_one_image($srcfile,$savefile,$tag){
        $ib=$this->system->loadModel('utility/magickwand');
        if($ib->magickwand_loaded){
            $loaded = true;
        }else{
            $ib=$this->system->loadModel('utility/gdimage');
            $loaded = $ib->gd_loaded;
        }
        $storager = $this->system->loadModel('system/storager');
        if($loaded){
                if($tag=='small'||$tag=='big'||$tag=='thumbnail'){
                    $enable = $this->system->getConf('site.watermark.wm_'.$tag.'_enable');
                }else{
                    $enable = 0;
                }

                $ib->wm_image_name = $storager->getFile($this->system->getConf('site.watermark.wm_'.$tag.'_pic'));
                $ib->wm_image_transition = $this->system->getConf('site.watermark.wm_'.$tag.'_transition');
                $ib->wm_text =$this->system->getConf('site.watermark.wm_'.$tag.'_text');
                $ib->wm_text_size=$this->system->getConf('site.watermark.wm_'.$tag.'_font_size');
                $ib->wm_text_font = $this->system->getConf('site.watermark.wm_'.$tag.'_font');
                $ib->wm_text_color = $this->system->getConf('site.watermark.wm_'.$tag.'_font_color');
                $ib->wm_image_pos = $this->system->getConf('site.watermark.wm_'.$tag.'_loc');
                $ib->src_image_name = $srcfile;

                $height = $this->system->getConf('site.'.$tag.'_pic_height');
                $width = $this->system->getConf('site.'.$tag.'_pic_width');
                $ib->save_file = $savefile;

                if(is_file($srcfile)){
                    switch($enable){
                        case 1:
                            $ib->jpeg_quality = 90;             //jpeg??
                            $ib->wm_text = '';
                            $ib->makeThumbWatermark($width,$height);
                            break;
                        case 2:
                            $ib->jpeg_quality = 90;             //jpeg??
                            $ib->wm_image_name = "";
                            $ib->makeThumbWatermark($width,$height);
                            break;
                        default:
                            $ib->jpeg_quality = 90;             //jpeg??
                            $ib->wm_text = '';
                            $ib->wm_image_name = "";
                            $ib->makeThumbWatermark($width,$height);
                    }
                }
                chmod($savefile,0644);
            return true;
        }else{
            return false;
        }
    }

    function set_goods_id($gimage_id, $goods_id){
        $r['gimage_id'] = $gimage_id;
        $rs = $this->db->exec('select * from sdb_gimages where gimage_id='.intval($r['gimage_id']));
        $r['goods_id'] = $goods_id;
        $sql = $this->db->getUpdateSQL($rs,$r);
        return $sql?$this->db->exec($sql):false;
    }
}
