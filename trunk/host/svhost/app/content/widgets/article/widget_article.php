<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
    function widget_article(&$setting,&$smarty){
        $oAN = kernel::single("content_article_node");
        $oMAI = app::get('content')->model('article_indexs');
        $iNodeId = $setting['node_id'];
        $lv = $setting['lv'];
        $limit = $setting['limit'];
        $tmp = $oAN->get_node($iNodeId);
        foo($lv, $iNodeId, $limit, $setting['showallart'], $oAN, $oMAI, $tmp['child'], $setting);
        $html = '';
        
        show($smarty, $tmp['child'], $setting, $html, 0, $limit);
        $tmp['__html'] = $html;
        $tmp['__shownode'] = $setting['shownode'];
        return $tmp;
    }
    
    function foo($lv=1, $iNodeId=1, $limit, $showallart, $oAN, $oMAI, &$tmp, $setting) {
        if($lv<0)return;
        $aNodes = $oAN->get_nodes($iNodeId);
        
        if(is_array($aNodes)) {
            foreach ($aNodes as $val) {
                if($val['ifpub']=='false')continue;
                foo(($lv-1), $val['node_id'], $limit, $showallart, $oAN, $oMAI, $tmp['child'][$val['node_id']], $setting);
                if(empty($tmp['child'][$val['node_id']])) unset($tmp[$val['node_id']]);
                $tmp['child'][$val['node_id']]['info'] = $val;
            }
        }
        if( $showallart ) {
            
            if(!$limit) return ;
            #if( $lv==$setting['lv'] ) return false;
            $tmp['article'] = $oMAI->getList_1('*', array('node_id'=>$iNodeId, 'ifpub'=>'true', 'nochildren'=>true),0, $limit);
        } 
    }
    
    
    function show(&$smarty, $tmp, $setting, &$html, $lv=0, &$limit) {
        if($setting['shownode'] && $lv!=0) {
            if(is_object($smarty) && method_exists($smarty, 'gen_url'))
                $url = $smarty->gen_url(array('app'=>'content', 'ctl'=>'site_article', 'act'=>'lists', 'arg0'=>$tmp['info']['node_id']));
            $html .= html($lv, $url, $tmp['info']['node_name']);
        }
        
        if( !$setting['shownode'] ) {
            if( $limit<=0 ) return;
            #$tmp['article'] = array_slice( $tmp['article'], 0, $setting['limit'] );
        }
        
        if($tmp['article']) {
            if($setting['styleart']) {
                $tmp_lv = $setting['shownode'] ? ($setting['lv'] + 1) : 2;
            } else {
                $tmp_lv = $lv + 1;
            }
            foreach ($tmp['article'] as $row) {
                if(is_object($smarty) && method_exists($smarty, 'gen_url'))
                    $url = $smarty->gen_url(array('app'=>'content', 'ctl'=>'site_article', 'act'=>'index', 'arg0'=>$row['article_id']));
                $html .= html($tmp_lv, $url, $row['title']);
                $limit--;
            }
        }
        if($tmp['child']) {
            foreach ($tmp['child'] as $row) {
                show($smarty, $row, $setting, $html, $lv+1, $limit);
            }
        }
    }
    
    function html($lv, $url, $name) {
        return <<<EOF
<div class="cat{$lv}">
    <a href="{$url}">{$name}</a>
</div>
EOF;
    }
