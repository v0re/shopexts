<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_finder_goods{
    var $detail_basic = '基本信息';
    var $column_control = '商品操作';
    function __construct($app){
        $this->app = $app;
    }
    function column_control($row){
        return '<a href="index.php?app=b2c&ctl=admin_goods_editor&act=edit&p[0]='.$row['goods_id'].'&finder_id='.$_GET['_finder']['finder_id'].'"  target="blank">编辑</a>';
    }
    function detail_basic($gid){

        $render =  app::get('b2c')->render();
        $o = $render->app->model('goods');
        $goods=$o->getList('thumbnail_pic,udfimg,marketable,rank_count,view_count,view_w_count,buy_count,buy_w_count,count_stat,image_default_id',array('goods_id'=>$gid));

        $goods = current($goods);
        $render->pagedata['goods'] = &$goods;
        $render->pagedata['is_pub'] = ($goods['marketable']!='false');
        $render->pagedata['url'] = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_product','full'=>1,'act'=>'index','arg'=>$gid));
        $render->pagedata['buy_w_count'] = $goods['buy_w_count'];
        $render->pagedata['view_w_count'] = $goods['view_w_count'];

        $render->pagedata['buy_count'] = $goods['buy_count'];
        $render->pagedata['view_count'] = $goods['view_count'];

        $render->pagedata['status'] = unserialize($goods['count_stat']);

        $today = $this->day(time());
        $view_chardata=array();
        $buy_chardata=array();

        foreach(range($today-14,$today) as $day){
            $view_chardata[$day]=intval($this->pagedata['status']['view'][$day]);
            $buy_chardata[$day]=intval($this->pagedata['status']['buy'][$day]);
        }
        $render->pagedata['view_chart'] = $this->_linechart($view_chardata);
        $render->pagedata['buy_chart'] = $this->_linechart($buy_chardata);
        $imageDefault = app::get('image')->getConf('image.set');
        $render->pagedata['defaultImage'] = $imageDefault['S']['default_image'];//print_R($render->pagedata['defaultImage']);exit;
        return $render->fetch('admin/goods/detail/detail.html',$this->app->app_id);
    }


     function day($time=null){
        if(!isset($GLOBALS['_day'][$time])){
            return $GLOBALS['_day'][$time] = floor($time/86400);
        }else{
            return $GLOBALS['_day'][$time];
        }
     }

     function _linechart($chardata){

        $max = max(10,intval(max($chardata)*1.25));

        $w = 300;
        $y = array();
        $xday = array();
        $i=0;
        $xmonty = array();
        $lastMonth = null;
        $count = count($chardata);
        $xmark=array('B,76A4FB,0,0,0');
        $color_1 = '000099';

        foreach($chardata as $d=>$v){

            $d = $d*3600*24;

            if($lastMonth!=($month=date('Y.m',$d))){
                $lastMonth = $month;
                $xmonth[intval($i*(100/$count))] = $month;
                if($i>0)$xmark[] = 'V,'.$color_1.',0,'.$i.',1';
            }
            $i++;
            $xday[] = date('d',$d);
        }
        return 'chs='.$w.'x100&chd=t:'.implode(',',$chardata).
        '&chxt=x,y,x,r&chco=224499&chxl=0:|'.implode('|',$xday).'|1:|0|'.round($max/2).'|'.$max.'|2:|'.implode('|',$xmonth).
        '|3:|0|'.round($max/2).'|'.$max.'&cht=lc&chds=0,'.$max.'&chxp=2,'.implode(',',array_keys($xmonth)).'&chxs=2,'.$color_1.',13&chm='.implode('|',$xmark).'&chg=5,25,1';
     }




}
