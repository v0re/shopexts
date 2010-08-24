<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class image_ctl_admin_manage extends desktop_controller 
{
    
    var $workground = 'image_ctl_admin_manage';

    function index(){
        $action = array(
                array('label'=>'上传新图片','href'=>'index.php?app=image&ctl=admin_manage&act=image_swf_uploader'
                            ,'target'=>'dialog::{title:\'上传图片\',width:300,height:300}'),
                array('label'=>'添加网络图片','href'=>'index.php?app=image&ctl=admin_manage&act=image_www_uploader'
                            ,'target'=>'dialog::{title:\'添加网络图片\',width:550,height:200}'),
                array('label'=>'水印与缩略图','submit'=>'index.php?app=image&ctl=admin_manage&act=rebuild'
                            ,'target'=>'dialog::{title:\'水印与尺寸\',width:500,height:300}'),
                //array('label'=>'切换存储引擎','submit'=>'index.php?ctl=image&act=ch_storage'
                //            ,'target'=>'dialog::{title:\'切换存储引擎\',width:300,height:300}'),
            );
        $this->finder('image_mdl_image',array('title'=>'图片管理','actions'=>$action));
    }
    function image_swf_uploader(){
       $mdl_img = $this->app->model('image');
       $this->pagedata['currentcount'] = $mdl_img->count();
       $this->pagedata['ssid'] =  kernel::single('base_session')->sess_id();
       $this->display('image_swf_uploader.html');
    }
    
    function image_upload(){
       
       $mdl_img   = $this->app->model('image');
       $image_name = $_FILES['upload_item']['name'];
       $image_id  = $mdl_img->store($_FILES['upload_item']['tmp_name'],null,null,$image_name);
       $mdl_img->rebuild($image_id,array('L','M','S'));     
     
       if(isset($_REQUEST['type'])){
            $type=$_REQUEST['type'];
       }else{
            $type='s';
       }
           
       $image_src = base_storager::image_path($image_id,$type);
      
       $this->_set_tag($image_id);
       if($callback = $_REQUEST['callbackfunc']){
            
             $_return = "<script>try{parent.$callback('$image_id','$image_src')}catch(e){}</script>";
       
       }
       
       $_return.="<script>parent.MessageBox.success('图片上传成功');</script>";

       echo $_return;
    
    }
    function _set_tag($image_id){
       $tagctl   = app::get('desktop')->model('tag');
       $tag_rel   = app::get('desktop')->model('tag_rel');
       $data['rel_id'] = $image_id;
       $tags = explode(' ',$_POST['tag']['name']);
       $data['tag_type'] = 'image';
       $data['app_id'] = 'image';
       foreach($tags as $key=>$tag){
           if(!$tag) continue;
            $data['tag_name'] = $tag;
            $tagctl->save($data);
            if($data['tag_id']){
                $data2['tag']['tag_id'] = $data['tag_id'];
                $data2['rel_id'] = $image_id;
                $data2['tag_type'] = 'image';
                $data2['app_id'] = 'image';
                $tag_rel->save($data2);
                unset($data['tag_id']);
            }
       }
    }
    function image_www_uploader(){
        if($_POST['upload_item']){
            $image = $this->app->model('image');
            $image_name = substr(strrchr($_POST['upload_item'],'/'),1);
            $image_id = $image->store($_POST['upload_item'],null,null,$image_name);
            $image_src = base_storager::image_path($image_id);
            $this->_set_tag($image_id);
            if($callback = $_REQUEST['callbackfunc']){
                
                 $_return = "<script>try{parent.$callback('$image_id','$image_src')}catch(e){}</script>";

            }

            $_return.="<script>parent.MessageBox.success('图片上传成功');</script>";

            echo $_return;
            echo <<<EOF
<div id="upload_remote_image"></div>
<script>
$('upload_remote_image').getParent('.dialog').retrieve('instance').close();
</script>
EOF;
        }else{
            $html  ='<div class="division"><h5>网络图片地址：</h5>';
            $ui = new base_component_ui($this);
            $html .= $ui->form_start();
            $html .= $ui->input(array(

                'type'=>'url',
                'name'=>'upload_item',
                'value'=>'http://',
                
                'style'=>'width:70%'
                ));
            $html .='</div><div class="table-action">';
            $html .= $ui->form_end();
            echo $html."</div>";

        }
    }
    
    
    
    
    function image_swf_remote(){
        $image = $this->app->model('image');
        $image_name = $_FILES['Filedata']['name'];
        $image_id = $image->store($_FILES['Filedata']['tmp_name'],null,null,$image_name);
        $this->pagedata['image_id'] = $image_id;
        
        echo $this->fetch('image_swf_uploader_reponse.html');
    
    }
    
    
    function gimage_swf_remote(){
        
        /*
        if($_GET['sess_id']!=$this->app->sess_id){
            
            echo 'sid error';
        
        }*/
        
        $image = $this->app->model('image');
        $image_name = $_FILES['Filedata']['name'];
       
        $image_id = $image->store($_FILES['Filedata']['tmp_name'],null,null,$image_name);
        
        $image->rebuild($image_id,array('L','M','S'));
        
        $image_s = base_storager::image_path($image_id,'s');
        
        $image_b = base_storager::image_path($image_id,'b');
        
        $this->pagedata['gimage']['image_id'] = $image_id;
        
        $this->display('gimage.html');
        
        
    }
    
    
    
    
    
    
    
    
    
    /*图片浏览器*/
    function image_broswer($page=1){

        $pagelimit = 10;

        $otag = app::get('desktop')->model('tag');
        $oimage = $this->app->model('image');
        $tags = $otag->getList('*',array('tag_type'=>'image'));
    
        $this->pagedata['type'] = $_GET['type'];
        $this->pagedata['tags'] = $tags;
        $this->display('image_broswer.html');
    
    }
    function image_lib($tag='',$page=1){
        $pagelimit = 10;

        //$otag = $this->app->model('tag');
        $oimage = $this->app->model('image');

        //$tags = $otag->getList('*',array('tag_type'=>'image'));
        $filter = array();
        if($tag){
            $filter = array('tag'=>array($tag));
        }
        $images = $oimage->getList('*',$filter,$pagelimit*($page-1),$pagelimit);
        $count = $oimage->count($filter);

        $maxsize = 90;
        foreach($images as $key=>$v){
    
            $width = $v['width'];

            $height = $v['height'];

            if($width>$height){
                $props = 'width="'.($width>$maxsize?$maxsize:$width).'"';
            }else{
                $props = 'height="'.($height>$maxsize?$maxsize:$height).'"';
            }

            $v['size'] = $props;
            $images[$key] = $v;
        }
        $this->pagedata['images'] = $images;
        $ui = new base_component_ui($this->app);
        $this->pagedata['pagers'] = $ui->pager(array(
            'current'=>$page,
            'total'=>ceil($count/$pagelimit),
            'link'=>'index.php?app=image&ctl=admin_manage&act=image_lib&p[0]='.$tag.'&p[1]=%d',
            ));
         $this->display('image_lib.html');

     }
    

    function ch_storage(){
    
        
    
    }

    function rebuild(){
        $ui = new base_component_ui($this);
        if($_POST['size']){
            $queue = app::get('base')->model('queue');
            parse_str($_POST['filter'],$filter);
            $data = array(
                'queue_title'=>'重新生成图片',
                'start_time'=>time(),
                'params'=>array(
                    'filter'=>$filter,
                    'watermark'=>$_POST['watermark'],
                    'size'=>$_POST['size'],
                    'queue_time'=>time(),
                ),
                'worker'=>'image_rebuild.run',
                );
            $queue->insert($data);
            echo <<<EOF
<div id="close_image"></div>
<script>
$('close_image').getParent('.dialog').retrieve('instance').close();
</script>
EOF;
        }else{
            $html .= $ui->form_start(array('id'=>'rebuild_form'));
            $size = array(
                'L'=>'大图',
                'M'=>'中图',
                'S'=>'小图',
            );
            foreach($size as $k=>$v){
                $html .= $ui->form_input(array(
                    'title'=>'生成'.$v,
                    'type'=>'checkbox',
                    'name'=>'size[]',
                    'value'=>$k,
                    'checked'=>'checked',
                    ));
            }

            $html.='<tr><td colspan="2" style="height:1px;background:#ccc;overflow:hidden;padding:0"></td><tr>';

            $filter = $_POST;
            unset($filter['_finder']);
            $filter = htmlspecialchars(http_build_query($filter));

            $html .= $ui->form_input(array(
                'title'=>'使用水印',
                'type'=>'bool',
                'name'=>'watermark',
                'value'=>1,
                ));
            $html.='<input type="hidden" name="filter" value="'.$filter.'" />';

            $html .=$ui->form_end();
            echo $html;
            echo <<<EOF
<script>
   $('rebuild_form').store('target',{
        
        onComplete:function(){
                 $('rebuild_form').getParent('.dialog').retrieve('instance').close();
             
        }
   
   });

</script>
EOF;
        }
    }

    function imageset(){
        $image = &app::get('image')->model('image');

       $allsize = array();
        if($_POST['pic']){
            $image_set = $_POST['pic'];

            $cur_image_set = $this->app->getConf('image.set');
    
            foreach($image_set as $size=>$item){
                if($item['wm_type']=='text'){
                    $image_id = '';
                    if($cur_image_set && $cur_image_set[$size] && $cur_image_set[$size]['wm_text_image']){
                        $image_id = $cur_image_set[$size]['wm_text_image'];
                    }
                    //生产文字水印图
                    $tmpfile = tempnam('/tmp','img');
                    $url = 'http://chart.apis.google.com/chart?chst=d_text_outline&chld=000000|20|h|ffffff|_|'.urlencode($item['wm_text']);
                    file_put_contents($tmpfile,file_get_contents($url));
                    $image_id = $image->store($tmpfile,$image_id,null,$item['wm_text']);

                    $image_set[$size]['wm_text_image'] = $image_id;
                }
            }
            $this->app->setConf('image.set',$image_set);

            $cur_image_set = $this->app->getConf('image.set');
            
        }
        $def_image_set = $this->app->getConf('image.default.set');

        $this->pagedata['allsize'] = $def_image_set;
        $cur_image_set = $this->app->getConf('image.set');
        $this->pagedata['image_set'] = $cur_image_set;
        $this->pagedata['this_url'] = $this->url;
        $this->page('imageset.html');
    }

    function view_gimage($image_id){
      //  $oImage = $this->app->model('image');
        $this->pagedata['image_id'] = $image_id;
        $this->page('view_gimages.html');
    }

}//End Class
