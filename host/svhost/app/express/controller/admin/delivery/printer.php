<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class express_ctl_admin_delivery_printer extends desktop_controller{
    public $workground = 'ectools_ctl_admin_order';
    
    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
        define('DPGB_TMP_MODE',1);
        define('DPGB_HOME_MODE',2);
        $this->pagedata['dpi'] = intval(app::get('b2c')->getConf('system.clientdpi'));
        if(!$this->pagedata['dpi']){
            $this->pagedata['dpi'] = 96;
        }
        $this->model = $this->app->model('print_tmpl');
        $this->o = &app::get('image')->model('image_attach');
        $this->obj = $this;
    }
    
    public function index()
    {    
        $this->finder('express_mdl_print_tmpl',array(
            'title'=>'快递单模板',
            'actions'=>array(
                            array('label'=>'添加模版','icon'=>'add.gif','href'=>'index.php?app=express&ctl=admin_delivery_printer&act=add_tmpl'),
                            array('label'=>'导入模版','icon'=>'add.gif','href'=>'index.php?app=express&ctl=admin_delivery_printer&act=import'),
                        ),'use_buildin_set_tag'=>false,'use_buildin_recycle'=>true,'use_buildin_filter'=>true,
            ));
    }
    
    
     function do_print(){

        $data = $_POST['order'];
        $data['shop_name'] = $this->app->getConf('system.shopname');

        $obj_dly_center = $this->app->model('dly_center');
        $dly_center = $obj_dly_center->dump($_POST['dly_center']);
        $data['dly_name']=$dly_center['uname'];

        list($pkg,$regions,$region_id) = explode(':',$data['ship_area']);
        foreach(explode('/',$regions) as $i=>$region){
            $data['ship_area_'.$i]= $region;
        }

        if($dly_center['region']){
            list($pkg,$regions,$region_id) = explode(':',$dly_center['region']);
            foreach(explode('/',$regions) as $i=>$region){
                $data['dly_area_'.$i]= $region;
            }
        }

        $data['dly_address']=$dly_center['address'];
        $data['dly_tel']=$dly_center['phone'];
        $data['dly_mobile']=$dly_center['cellphone'];
        $data['dly_zip']=$dly_center['zip'];

        $t = time()+($GLOBALS['user_timezone']-SERVER_TIMEZONE)*3600;
        $data['date_y']=date('Y',$t);
        $data['date_m']=date('m',$t);
        $data['date_d']=date('d',$t);
        $aData = $this->o->getList('image_id',array('target_id' => $_POST['dly_tmpl_id'],'target_type' => 'print_tmpl'));
        $image_id = $aData[0]['image_id'];
        $url = $this->show_bg_picture(1,$image_id);
        $this->pagedata['tmpl_bg'] = $url;
        if(file_exists(HOME_DIR.'/upload/dly_bg_'.$_POST['dly_tmpl_id'].'.jpg')){
            $this->pagedata['tmpl_bg'] = 'index.php?ctl=order/delivery_printer&act=show_bg_picture&p[0]='.DPGB_HOME_MODE.'&p[1]='.$_POST['dly_tmpl_id'];
        }
        unset($data['ship_area']);
        $xmltool = kernel::single('site_utility_xml');
        $data['order_print']=$data['order_id'];
        $this->pagedata['data'] = $xmltool->array2xml($data,'data');
        $this->pagedata['prt_tmpl'] = $this->model->dump($_POST['dly_tmpl_id']);
        $this->pagedata['res_url'] = $this->app->res_url;
        $this->singlepage('admin/delivery/center/printer.html');
    }


    public function add_tmpl($image_id=null)
    {
        $this->pagedata['tmpl'] = array(
            'prt_tmpl_title'=>'',
            'prt_tmpl_width'=>240,
            'prt_tmpl_height'=>135,
        );
        
        if (PRINTER_FONTS){
            $this->pagedata['font'] = explode("|",PRINTER_FONTS);
        }
        $url = $this->show_bg_picture(1,$image_id);
        $this->pagedata['tmpl_bg'] = $url;
        $this->pagedata['elements'] = $this->app->model('print_tmpl')->getElements();
        $this->pagedata['res_url'] = $this->app->res_url;
        $this->page('admin/printer/dly_printer_editor.html');
    }
    
    function save(){
        $o = &app::get('image')->model('image_attach');
        $this->begin('index.php?app=express&ctl=admin_delivery_printer&act=index');
        if($_POST['prt_tmpl_id']){
            if($this->model->update($_POST,array('prt_tmpl_id'=>$_POST['prt_tmpl_id']))){
                $tpl_id = $_POST['prt_tmpl_id'];
                $aData = $o->getList('attach_id',array('target_id' => $tpl_id,'target_type' => 'print_tmpl'));
                $attach_id = $aData[0]['attach_id'];
            }else{
                $tpl_id = false;
            }
        }else{
            $tpl_id = $this->model->insert($_POST);
        }
        if (isset($_POST['tmp_bg']) && $_POST['tmp_bg']){    
            $sdf = array(
            'attach_id' => $attach_id?$attach_id:'',
            'target_id' => $tpl_id,
            'target_type' => 'print_tmpl',
            'image_id' => $_POST['tmp_bg'],
            'last_modified' => time(),
            );
            $o->save($sdf);
        }
        $this->end(true,$tpl);
    }
    
      function edit_tmpl($tmpl_id){ 
        if(PRINTER_FONTS){
            $this->pagedata['font'] = explode("|",PRINTER_FONTS);
        }
        $this->pagedata['tmpl'] = $this->model->dump($tmpl_id);
        $this->pagedata['res_url'] = $this->app->res_url;
        if($this->pagedata['tmpl']){
            $aData = $this->o->getList('image_id',array('target_id' => $tmpl_id,'target_type' => 'print_tmpl'));
            $image_id = $aData[0]['image_id'];
            $url = $this->show_bg_picture(1,$image_id);
            $this->pagedata['tmpl_bg'] = $url;
            $this->pagedata['elements'] = $this->model->getElements();
            $this->page('admin/printer/dly_printer_editor.html');
        }else{
            $this->splash('failed','index.php?app=express&ctl=admin_delivery_printer&act=index',__('无效的快递单模板id'));
        }
    }
    
     function add_same($tmpl_id){/*todo 背景图*/
        $aData = $this->o->getList('image_id',array('target_id' => $tmpl_id,'target_type' => 'print_tmpl'));
        $image_id = $aData[0]['image_id'];
        if($image_id){
            echo '<script> 
              new Element("input",{id:"dly_printer_bg",type:"hidden",name:"tmp_bg",value:"'.$image_id.'"}).inject("dly_printer_form");
            </script>';
        }
        $this->pagedata['tmpl_id'] = $tmpl_id;
        $url = $this->show_bg_picture(1,$image_id);
        $this->pagedata['tmpl_bg'] = $url;
        $this->pagedata['res_url'] = $this->app->res_url;
        $this->pagedata['tmpl_id'] = $tmpl_id;
        if($this->pagedata['tmpl'] = $this->model->dump($tmpl_id)){
                unset($this->pagedata['tmpl']['prt_tmpl_id']);
                $this->pagedata['elements'] = $this->model->getElements();
                $this->page('admin/printer/dly_printer_editor.html');
        }
        else{
                $this->splash('failed','index.php?ctl=order/delivery_printer&act=index',__('无效的快递单模板id'));
        }
}

    
    public function upload_bg($printer_id=0){
        $this->pagedata['dly_printer_id'] = $printer_id;
        $this->display('admin/printer/dly_printer_uploadbg.html');
    }
    
    function import(){
        $this->page('admin/printer/dly_printer_import.html');
    }
    
    public function do_upload_bg()
        {
         $url = $this->show_bg_picture(1,$_POST['background']);
       echo '<script> 
                if($("dly_printer_bg")){
                    $("dly_printer_bg").value = "'.$_POST['background'].'";
        }else{
              new Element("input",{id:"dly_printer_bg",type:"hidden",name:"tmp_bg",value:"'.$_POST['background'].'"}).inject("dly_printer_form");
        }
        window.printer_editor.dlg.close();
        window.printer_editor.setPicture("'.$url.'");
            </script>';
    }
    
    
    function download($tmpl_id){
        $tmpl = $this->model->dump($tmpl_id);
        $tar = kernel::single('base_tar');
        $tar->addFile('info',serialize($tmpl));
        $aData = $this->o->getList('image_id',array('target_id' => $tmpl_id,'target_type' => 'print_tmpl'));
        $image_id = $aData[0]['image_id'];
        
        if($bg = $this->show_bg_picture(1,$image_id)){
            $tar->addFile('background.jpg',file_get_contents($bg));
        }

        #kernel::single('base_session')->close();
        $charset = kernel::single('ectools_charset_utfconvert');
        $name = $charset->utf2local($tmpl['prt_tmpl_title'],'zh');
        @set_time_limit(0);
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header('Content-type: application/octet-stream');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename="'.$name.'.dtp"');
        $tar->getTar('output');
    }
    
    public function done_upload_bg($rs,$file){
        if($rs){
            $url = 'index.php?app=express&ctl=admin_delivery_printer&act=show_bg_picture&p[0]='.$rs.'&p[1]='.$file;
            echo '<script>
                if($("dly_printer_bg")){
                    $("dly_printer_bg").value = "'.$file.'";
        }else{
              new Element("input",{id:"dly_printer_bg",type:"hidden",name:"tmp_bg",value:"__none__"}).inject("dly_printer_form");
        }
        
        window.printer_editor.dlg.close();
        window.printer_editor.setPicture("'.$url.'");
            </script>';
        }else{
            echo 'Error on upload:'.$file;
        }
    }
    
    public function show_bg_picture($mode,$file){
        $obj_storager = kernel::single("base_storager");
        $str_file = $obj_storager->image_path($file);
        return $str_file;
        /*
        $etag = md5_file('test.txt');
        header('Etag: '.$etag);

        if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag){
            header('HTTP/1.1 304 Not Modified',true,304);
            exit(0);
        }else{
            set_time_limit(0);
            header("Expires: " .$expires. " GMT");
            header("Cache-Control: public");
            session_cache_limiter('public');
            $handle = fopen($file, "r");
            while($buffer = fread($handle,102400)){
                echo $buffer;
                flush();
            }
            fclose($handle);
        }*/
    }
    
     function do_upload_pkg(){
       $this->begin('index.php?app=express&ctl=admin_delivery_printer&act=index');
        $file = $_FILES['package'];
        $file_name  = substr($file['name'],strrpos($file['name'],'.'));
        $extname = strtolower($file_name);
        $tar = kernel::single('base_tar');
        if($extname=='.dtp'){
            if($tar->openTAR($file['tmp_name']) && $tar->containsFile('info')){
                if(!($info = unserialize($tar->getContents($tar->getFile('info'))))){
                    echo "<script>alert('无法读取结构信息,模板包可能已损坏')</script>";
                    $this->end(false,__('无法读取结构信息,模板包可能已损坏'));
                }
                $info['prt_tmpl_id']='';
                if($tpl_id=$this->model->insert($info)){
                    if($tar->containsFile('background.jpg')){ //包含背景图
                        $image = app::get('image')->model('image');
                        $image_id = $image->gen_id();
                        file_put_contents('./app/express/statics/images/dly_bg_'.$tpl_id.'.jpg',$tar->getContents($tar->getFile('background.jpg')));
                        $Image_id = $image->store('./app/express/statics/images/dly_bg_'.$tpl_id.'.jpg',$Image_id);
                        unlink('./app/express/statics/images/dly_bg_'.$tpl_id.'.jpg');
                        $sdf = array(
                            'target_id' => $tpl_id,
                            'target_type' => 'print_tmpl',
                            'image_id' => $Image_id,
                            'last_modified' => time(),
                            );
                        if(!($this->o->save($sdf))){
                            echo "<script>alert('摸板包中图片有误')</script>";
                            $this->end(false,__('摸板包中图片有误'));
                        }  
                    }
                }

            }else{
                echo "<script>alert('无法解压缩,模板包可能已损坏')</script>";
                $this->end(false,__('无法解压缩,模板包可能已损坏'));
            }
        }else{ echo "<script>alert('必须是shopex快递单模板包(.dtp)')</script>";
            $this->end(false,__('必须是shopex快递单模板包(.dtp)'));
        }
        echo "<script>alert('快递单安装成功')</script>";
        #$this->redirect('index.php?app=express&ctl=admin_delivery_printer&act=index');
        $this->end(true,__('快递单安装成功'));

        
    }  
}