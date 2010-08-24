<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_treelist(&$setting,&$app){
    return '';
    //var_dump($setting);
    return; //todo 修正widget
    $sitemap = $app->model('sitemaps');
    $temp=$sitemap->dump();
    $s=$temp['item'][7]['item'];
    //var_dump($s);exit;
    //var_dump($_SERVER);exit;
    $rest=array();
    foreach($s as $k=>$v){
        if($v['node_id']==$setting['treelistnum']){
            //var_dump($v);
            $rest["label"]=$v['title'];
            $rest["depth"]=$v['depth'];
            $rest["hidden"]=$v['hidden'];
            $rest["item_id"]=$v['item_id'];
            $rest["link"]=doLink($v['action']);
            foreach($v['item'] as $k=>$v){
                $temp=array();
                $temp["label"]=$v['title'];
                $temp["depth"]=$v['depth'];
                $temp["hidden"]=$v['hidden'];
                $temp["item_id"]=$v['item_id'];
                $temp["link"]=doLink($v['action']);
                $rest['sub'][$v['node_id']]=$temp;
            }
        }
    }
    $content="";
    //var_dump($rest);exit;
    if($setting['showroot']=="true" && $rest['hidden']=='false'){
        if($rest['item_id']=='1') $jump="target='_blank'";
        $content.=' <div class="cat'.$rest['depth'].'"><a href="'.$rest['link'].'" '.$jump.'>'.$rest['label'].'</a></div>';
    }
    
    undoTree($rest['sub'],$content,($setting['treenum']+$rest['depth']));
    return $content;
}
function doLink($action){
    return $_SERVER['HTTP_REFERER'].'?'.str_replace(':','-',$action).'.html';
}
function undoTree($result,&$content,$length){
    foreach($result as $k=>$v){
        if($v['item_id']==1){
            $jump="target='_blank'";
        }
        if($v['depth']>$length){
            break;
        }else{
            if($v['hidden']=='false'){
                    $content.=' <div class="cat'.$v['depth'].'"><a href="'.$v['link'].'" '.$jump.'>'.$v['label'].'</a></div>';
            }
        }
        if($v['sub']){
            undoTree($v['sub'],$content,$length);
        }
    }
}
?>
