<?php
require_once('shopObject.php');

class mdl_dly_printer extends shopObject{

    var $idColumn = 'prt_tmpl_id'; //表示id的列 
    var $textColumn = 'prt_tmpl_title';
    var $defaultCols = 'prt_tmpl_title,shortcut';
    var $adminCtl = 'trading/delivery_printer';
    var $defaultOrder = array('prt_tmpl_id','asc');
    var $tableName = 'sdb_print_tmpl';

    function getColumns(){
        return array(
            'prt_tmpl_id'=>array('label'=>'单据id','class'=>'span-2'),
            'prt_tmpl_title'=>array('label'=>'单据名称','class'=>'span-10','unique'=>true),
            'shortcut'=>array('label'=>'是否已启用','class'=>'span-3','type'=>'bool'),
        );
    }

    function insert($data){
        if(has_unsafeword($data['prt_tmpl_title'])){
            trigger_error('无法保存，标题含有非法字符',E_USER_ERROR);
        }
        $sql = 'select prt_tmpl_id from sdb_print_tmpl where prt_tmpl_title="'.$this->db->quote($data['prt_tmpl_title']).'"';
        if($this->db->selectrow($sql)){
            trigger_error('无法保存，存在同名模板',E_USER_ERROR);
        }
        return parent::insert($data);
    }

    function delete($filter){
        $this->disabledMark=false;
        foreach($this->getList('prt_tmpl_id',$filter) as $r){
            unlink(HOME_DIR.'/upload/dly_bg_'.$r['prt_tmpl_id'].'.jpg');
        }
        parent::delete($filter);
    }

    function update($data,$filter){
        if($data['prt_tmpl_title']){
            if(has_unsafeword($data['prt_tmpl_title'])){
                trigger_error('无法保存，标题含有非法字符',E_USER_ERROR);
                return false;
            }
            if(!$filter['prt_tmpl_id']){
                trigger_error('无法保存，模板名称不能重复',E_USER_ERROR);
                return false;
            }
            $sql = 'select prt_tmpl_id from sdb_print_tmpl where prt_tmpl_id!='.intval($filter['prt_tmpl_id']).' and prt_tmpl_title="'.$this->db->quote($data['prt_tmpl_title']).'"';
            if($r = $this->db->selectrow($sql)){
                trigger_error('无法保存，存在同名模板',E_USER_ERROR);
                return false;
            }
        }
        return parent::update($data,$filter);
    }

    function getElements(){
        $elements = array(
            'ship_name'=>'收货人-姓名',

            'ship_area_0'=>'收货人-地区1级',
            'ship_area_1'=>'收货人-地区2级',
            'ship_area_2'=>'收货人-地区3级',

            'ship_addr'=>'收货人-地址',
            'ship_tel'=>'收货人-电话',
            'ship_mobile'=>'收货人-手机',
            'ship_zip'=>'收货人-邮编',
            'dly_name'=>'发货人-姓名',

            'dly_area_0'=>'发货人-地区1级',
            'dly_area_1'=>'发货人-地区2级',
            'dly_area_2'=>'发货人-地区3级',

            'dly_address'=>'发货人-地址',
            'dly_tel'=>'发货人-电话',
            'dly_mobile'=>'发货人-手机',
            'dly_zip'=>'发货人-邮编',
            'date_y'=>'当日日期-年',
            'date_m'=>'当日日期-月',
            'date_d'=>'当日日期-日',
            'order_id'=>'订单-订单号',
            'order_price'=>'订单总金额',
            'order_weight'=>'订单物品总重量',
            'order_count'=>'订单-物品数量',
            'order_memo'=>'订单-备注',
            'ship_time'=>'订单-送货时间',
            'shop_name'=>'网店名称',
            'tick'=>'对号 - √',
            'text'=>'自定义内容',
        );
        return $elements;
    }

}
?>
