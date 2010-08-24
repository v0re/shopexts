<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_view_helper{

    function __construct($app){
        $this->app = $app;
    }

    function function_goodsmenu($params,&$smarty){
        $vars = $smarty->pagedata;
        $smarty->pagedata = $params;
        $html = $smarty->fetch('site/product/menu.html',app::get('b2c')->app_id);
        $smarty->pagedata = $vars;
        return $html;
    }

    function function_selector($params, &$smarty){
        $filter = $params['filter'];
        if(is_numeric($params['key'])){
            $data = &$filter['p_'.$params['key']];
        }elseif ($params['key']=="spec"){
            $tmp=explode(",",$params['value']);
            $data = &$filter['s_'.$tmp[0]];
        }else{
            $data = &$filter[$params['key']];
        }

        if($params['mod']=='append'){
            $data[] = $params['value'];
        }elseif($params['mod']=='remove'){
            $data = array_flip($data);
            unset($data[$params['value']]);
            $data = array_flip($data);
        }else{
            if ($params['key']=="spec"){
                $tmpData = explode(",",$params['value']);
                $data = array($tmpData[1]);
            }
            else
                $data = array($params['value']);
        }

        $searchtools = $smarty->app->model('search');
        $args = $params['args'];

        $args[1] = $searchtools->encode($filter);
        $args[4]=1;

        return app::get('site')->router()->gen_url(array('app'=>'b2c', 'ctl'=>'site_gallery','full'=>1,'args'=>$args));
    }

    function function_pagers($params, &$smarty){
    if(!$params['data']['current'])$params['data']['current'] = 1;
    if(!$params['data']['total'])$params['data']['total'] = 1;
    if($params['data']['total']<2){
        return '';
    }

    $prev = $params['data']['current']>1?
        '<a href="'.str_replace($params['data']['token'],$params['data']['current']-1,$params['data']['link']).__('" class="sysiconBtnNoIcon" title="上一页">&laquo;上一页</a>'):
        '&nbsp;';

    $next = $params['data']['current']<$params['data']['total']?
        '<a href="'.str_replace($params['data']['token'],$params['data']['current']+1,$params['data']['link']).__('" class="sysiconBtnNoIcon" title="下一页">下一页&raquo;</a>'):
        '&nbsp;';

    if($params['type']=='mini'){
        return <<<EOF
    <table>
    <tr><td class="pagenum"><span class="pagecurrent">{$params['data']['current']}<span>/<span class="pageall">{$params['data']['total']}</span></td>
    <td>{$prev}</td>
    <td>{$next}</td>
    {$rand}
    </tr></table>
EOF;
    }else{

        $c = $params['data']['current']; $t=$params['data']['total']; $v = array();  $l=$params['data']['link']; $p=$params['data']['token'];

        if($t<11){
            $v[] = $this->_pager_link(1,$t,$l,$p,$c);
            //123456789
        }else{
            if($t-$c<8){
                $v[] = $this->_pager_link(1,3,$l,$p);
                $v[] = $this->_pager_link($t-8,$t,$l,$p,$c);
                //12..50 51 52 53 54 55 56 57
            }elseif($c<10){
                $v[] = $this->_pager_link(1,max($c+3,10),$l,$p,$c);
                $v[] = $this->_pager_link($t-1,$t,$l,$p);
                //1234567..55
            }else{
                $v[] = $this->_pager_link(1,3,$l,$p);
                $v[] = $this->_pager_link($c-2,$c+3,$l,$p,$c);
                $v[] = $this->_pager_link($t-1,$t,$l,$p);
                //123 456 789
            }
        }
        $links = implode('...',$v);

//    str_replace($params['data']['token'],4,$params['data']['link']);
//    if($params['data']['total']
        return <<<EOF
    <table class="pager" cellpadding="0" cellspacing="0" style="width:auto"><tr>
    <td style="padding-right:20px">{$prev}&nbsp;{$links}&nbsp;{$next}</td>
    </tr></table>
EOF;
    }
}
function _pager_link($from,$to,$l,$p,$c=null){
    for($i=$from;$i<$to+1;$i++){
        if($c==$i){
            $r[]=' <span class="borderdown" style="background:#fff; color:#000;font-weight:700;padding:2px 5px">'.$i.'</span> ';
        }else{
            $r[]=' <a class="borderup" href="'.str_replace($p,$i,$l).'">'.$i.'</a> ';
        }
    }
    return implode(' ',$r);
}

    function modifier_paddingleft($vol,$empty,$fill)
    {
        return str_repeat($fill,$empty).$vol;

    }
}
