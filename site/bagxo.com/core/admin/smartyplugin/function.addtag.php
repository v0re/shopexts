<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.getlink.php
 * Type:     function
 * Name:     eightball
 * Purpose:  outputs a random magic answer
 * -------------------------------------------------------------
 */

function smarty_function_addtag($params, &$smarty)
{
    $system = &$GLOBALS['system'];
    $obj = $system->loadModel('system/tag');
    $html = '';
    foreach($obj->tagList($params['type']) as $tag){
        $html.='<option submit="index.php?ctl=default&act=addTag&p[0]='.$params['type'].'&p[1]='.$tag['tag_id'].'" target="refresh">[+] '.$tag['tag_name'].'</option>';
    }
    return $html.'<option submit="index.php?ctl=default&act=addTag&p[0]='.$params['type'].'" prompt="请输入新标签名称" target="refresh">[*] 增加新标签...</option><option submit="index.php?ctl=default&act=clearTag&p[0]='.$params['type'].'">[-] 清除标签</option>';
}
?>
