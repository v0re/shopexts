<?php
include('smarty/Smarty.class.php');
$smarty = new smarty();
$smarty->force_compile=true;//WZP
$smarty->plugins_dir[] = 'smartyplugin';
$smarty->template_dir = 'view';
$smarty->compile_dir = 'template_c';
$smarty->left_delimiter='<{';
$smarty->right_delimiter='}>';


$pagedata=array('_PAGE_'=>(isset($_GET['act'])?$_GET['act']:'dashboard').'.html');

ob_start();
error_reporting(E_ALL);



$pagedata['nav'][] = array('label'=>'link_'.__LINE__,'link'=>'page.php?act=test&p[0]='.__LINE__);
$pagedata['nav'][] = array('label'=>'link_'.__LINE__,'link'=>'page.php?act=test&p[0]='.__LINE__);
$pagedata['nav'][] = array('label'=>'link_'.__LINE__,'link'=>'page.php?act=test&p[0]='.__LINE__);
$pagedata['nav'][] = array('label'=>'link_'.__LINE__,'link'=>'page.php?act=test&p[0]='.__LINE__);
$pagedata['nav'][] = array('label'=>'link_'.__LINE__,'link'=>'page.php?act=test&p[0]='.__LINE__);

if(count($pagedata['nav'])>2){
    $pagedata['_top_nav'] = $pagedata['nav'][count($pagedata['nav'])-2];
}
foreach($pagedata as $k=>$v){
    $smarty->assign($k,$v);
}
if(isset($_GET['_ajax'])){
    $content = $smarty->fetch('page.html');
}else{
    $content = $smarty->fetch('singlepage.html');
}
$etag = md5($content);
header('Etag: '.$etag);

//header("Cache-Control: no-cache, must-revalidate"); // 强制更新
//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//header("Pragma: no-cache");
if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag){
    header('HTTP/1.1 304 Not Modified',true,304);
    exit(0);
}else{
    echo $content;
}
?>
