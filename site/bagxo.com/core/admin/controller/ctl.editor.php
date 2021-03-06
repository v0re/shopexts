<?php
class ctl_editor extends adminPage{

    function link(){
        $sitemap = $this->system->loadModel('content/sitemap');
        $this->pagedata['linked'] = $sitemap->getLinkNode();
        foreach($this->pagedata['linked']['page'] as $k=>$p){
            $pos = strpos($p['action'],':');
            $ident = substr($p['action'],$pos+1);
            $this->pagedata['linked']['page'][$k]['url'] = $this->system->realUrl('page',$ident,null,'html',$this->system->base_url());
        }

        if($_POST['goods']){
            $mod = &$this->system->loadModel('goods/products');
            $rows = $mod->getList('name',array('goods_id'=>$_POST['goods']));
            $this->pagedata['goodsInfo'] = $rows[0]['name'].'<input type="hidden" name="goods" value="'.$_POST['goods'].'" />';
        }
        if($_POST['article']){
            $mod = &$this->system->loadModel('content/article');
            $rows = $mod->getList('title',array('article_id'=>$_POST['article']));
            $this->pagedata['articleInfo'] = $rows[0]['title'].'<input type="hidden" name="article" value="'.$_POST['article'].'" />';
        }

        $this->setView('editor/dlg_lnk.html');
        $this->output();
    }

    function find($type,$keywords){
        if(!$keywords){
            echo '请输入关键字。';
            return;
        }
        if($type=='goods'){
            $mod = &$this->system->loadModel('goods/products');
            foreach($mod->getList('goods_id,name',array('name'=>$keywords)) as $k=>$r){
                $list[] = array(
                    'url'=>$this->system->realUrl('product','index',array($r['goods_id']),'html',$this->system->base_url())
                    ,'label'=>$r['name']);
            }
            $this->pagedata['list'] = $list;
        }elseif($type=='article'){
            $mod = &$this->system->loadModel('content/article');
            foreach($mod->getList('article_id,title',array('keywords'=>$keywords)) as $k=>$r){
                $list[] = array(
                    'url'=>$this->system->realUrl('article','index',array($r['article_id']),'html',$this->system->base_url()),
                    'label'=>$r['title']);
            }
            $this->pagedata['list'] = $list;
        }
        if(count($list)>0){
            $this->pagedata['type'] = $type;
            $this->setView('editor/dlg_result.html');
            $this->output();
        }else{
            echo '没有符合条件<b>"'.$keywords.'"</b>的记录。';
        }
    }

    function table(){
        $this->setView('editor/dlg_table.html');
        $this->output();
    }

    function image($showpicset=1){
        $this->setView('editor/dlg_image.html');
        $tag = &$this->system->loadModel('system/tag');
        $this->pagedata['show_picset']=$showpicset;
        $this->pagedata['imgtags'] = $tag->tagList('image');
        $this->output();
    }
    function flash(){
        $this->setView('editor/dlg_flash.html');
        $tag = &$this->system->loadModel('system/tag');
        $this->pagedata['imgtags'] = $tag->tagList('image');
        $this->output();
    }
    function uploader(){
        $storager = &$this->system->loadModel('system/storager');
        set_error_handler(array(&$this,'_eH')); //如果上传图片时遇到trigger_error或者意外错误，执行_eH
        header('Content-Type: text/html; charset=utf-8');
        if($s = $storager->save_upload($_FILES['file'],'','',$msg)){
            restore_error_handler();
            $pubFile = &$this->system->loadModel('system/pubfile');
            $pubFile->insert(array(
                    'file_name'=>$_FILES['file']['name'],
                    'file_ident'=>$s,
                    'cdate'=>time(),
                    'memo'=>$_POST['memo'],
                    'tags'=>space_split(stripslashes($_POST['tags'])),
                    'file_type'=>$_POST['file_type']
            ));
            $info = array('url'=>$storager->getUrl($s),'ident'=>$s);
            echo '<script>window.top.uploadCallback('.json_encode($info).')</script>';
        }else{
            restore_error_handler();
            echo '<script>window.top.uploadCallback("'.($msg?$msg:'上传失败').'")</script>';
        }
    }

    function _eH($errno, $errstr, $errfile, $errline){
        restore_error_handler();
        echo '<script>window.top.uploadCallback(false)</script>';
    }

    function gallery($tag=0,$page=1,$file_type=0){
        $pubFile = &$this->system->loadModel('system/pubfile');
        $p = 18;
       
        $result=$pubFile->getList(null,array('tag'=>$tag,'file_type'=>$file_type),$p*($page-1),$p,$c);
        foreach($result as $k=>$v){
            if(preg_match('/\.swf/',$v['file_name'])){
                unset($result[$k]);
            }

        }
        $c=count($result);
        $this->pagedata['images'] = $result;
        $this->setView('editor/gallery_img.html');

        $this->pagedata['pager'] = array(
                'current'=>$page,
                'total'=>floor($c/$p)+1,
                'link'=>'javascript:showResLib(\''.$tag.'\',orz)',
                'token'=>'orz'
        );

        $this->output();
    }

    function gallery_SWF($tag=0,$page=1,$file_type=0){
        $pubFile = &$this->system->loadModel('system/pubfile');
        $p = 18;
        $result = $pubFile->getList(null,array('tag'=>$tag,'file_type'=>$file_type),$p*($page-1),$p,$c);
        foreach($result as $k=>$v){
            if(!preg_match('/\.swf/',$v['file_name'])){
                unset($result[$k]);
            }

        }
        $c=count($result);       
        $this->pagedata['swfs'] = $result;
        $this->setView('editor/gallery_swf.html');

        $this->pagedata['pager'] = array(
                'current'=>$page,
                'total'=>floor($c/$p)+1,
                'link'=>'javascript:showResLib(\''.$tag.'\',orz)',
                'token'=>'orz'
        );

        $this->output();
    }


    function editHTML(){

       if(file_exists(CORE_DIR.'/admin/view/editor/dlg_editHTML.html')){
           $this->setView('editor/dlg_editHTML.html');
       }else{
            $this->setView('editor/dlg_editHtml.html');
       }
       $this->pagedata['htmls']=$_POST['htmls'];
       $this->pagedata['seri']=$_POST['seri'];
       $this->output();
    }
}
?>
