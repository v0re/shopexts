<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_desktop_widgets_exstatistics implements desktop_interface_widget{
    
    
    function __construct($app){
        $this->app = $app; 
        $this->render =  new base_render(app::get('b2c'));  
    }
    
    function get_title(){
            
        return __("统计分析");
        
    }
    function get_html(){

        $render = $this->render;

        //近一周成交订金额
        /*
        $mdl_orders = $this->app->model('orders');
        $db = kernel::database();
        $lastweek_filter = array(
                    '_createtime_search'=>'between',
                    'createtime_from'=>date('Y-m-d',strtotime('-1 week')),
                    'createtime_to'=>date('Y-m-d'),
                    'createtime' => date('Y-m-d'),
                    '_DTIME_'=>
                        array(
                            'H'=>array('createtime_from'=>date('H'),'createtime_to'=>date('H')),
                            'M'=>array('createtime_from'=>date('i'),'createtime_to'=>date('i'))
                        ),
                    'pay_status'=>'1',
                );
        $from = time();
        $to = strtotime('-1 week');
        $rows = $db->select('select sum(total_amount) as order_amount,count(1) as order_nums, DATE_FORMAT(FROM_UNIXTIME(createtime),"%Y-%m-%d") as mydate from sdb_b2c_orders  where createtime>='.$to.' and createtime<'.$from.' and pay_status=\'1\' group by DATE_FORMAT(FROM_UNIXTIME(createtime),"%Y-%m-%d")');
        foreach($rows as $row){
            $data[$row['mydate']] = array('order_amount'=>$row['order_amount'],'order_nums'=>$row['order_nums']);
        }
        //print_r($result);
        $render->pagedata['data'] = $data;
        */
        return $render->fetch('desktop/widgets/exstatistics.html');
    }
    function get_className(){
        
          return " valigntop exstatistics";
    }
    function get_width(){
          
          return "l-2";
        
    }
    
}

?>