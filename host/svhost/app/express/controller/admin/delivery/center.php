<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * 快递中心快递单据管理
 * copyright shopex开发团队
 * dev@shopex.cn
 * http://www.shopex.cn
 * version 0.1
 */
 
class express_ctl_admin_delivery_center extends desktop_controller
{
    public $workground = 'ectools_ctl_admin_order';
    public $defaultWorkground = "ectools.wrokground.order";
    
    /**
     * 类公开的构造方法
     * @params object app
     * @return null
     */
    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }
    
    /**
     * filter的列表页方法
     * @params null
     * @return null
     */
    public function index()
    {
        $this->finder('express_mdl_dly_center',array(
            'title'=>'发货信息管理',
            'actions'=>array(
                            array('label'=>'添加发货信息','icon'=>'add.gif','href'=>'index.php?app=express&ctl=admin_delivery_center&act=addnew'),
                        ),'use_buildin_set_tag'=>true,'use_buildin_recycle'=>true,'use_buildin_filter'=>true,'use_buildin_export'=>true,
            ));
    }
    
    /**
     * 添加发货信息方法
     * @params null
     * @return null
     */
    public function addnew()
    {
        $this->page('admin/delivery/delivery_edit.html');
    }
    
    /**
     * 建立发货单方法
     * @params null
     * @return null
     */
    public function create()
    {
        if (isset($_POST) && $_POST)
        {
            $arr_deliverys = $_POST['delivery_center'];
            
            // 检查必填项目
            if (!isset($arr_deliverys['name']) || !$arr_deliverys['name'])
            {
                $this->begin();
                $this->end(false,__('出货点名称不能为空！'));
            }
            
            if (!isset($arr_deliverys['address']) || !$arr_deliverys['address'])
            {
                $this->begin();
                $this->end(false,__('出货点地址不能为空！'));
            }
            
            $obj_delivery_center = $this->app->model('dly_center');
            if (isset($arr_deliverys['dly_center_id']) && $arr_deliverys['dly_center_id'])
            {
                if ($obj_delivery_center->save($arr_deliverys))
                {
                    if (isset($_POST['is_default']) && $_POST['is_default'])
                    {
                        $this->app->setConf('system.default_dc', $arr_deliverys['dly_center_id']);
                    }
                
                    $this->begin("index.php?app=express&ctl=admin_delivery_center&act=index");
                    $this->end(true, __('修改成功！'));
                }
                else
                {
                    $this->begin();
                    $this->end(false,__('出货信息不正确！'));
                }
            }
            else
            {
                if ($dly_center_id = $obj_delivery_center->insert($arr_deliverys))
                {
                    if (isset($_POST['is_default']) && $_POST['is_default'])
                    {
                        $this->app->setConf('system.default_dc', $dly_center_id);
                    }
                
                    $this->begin("index.php?app=express&ctl=admin_delivery_center&act=index");
                    $this->end(true, __('添加成功！'));
                }
                else
                {
                    $this->begin();
                    $this->end(false,__('出货信息不正确！'));
                }
            }
        }
    }
    
    public function showEdit($dly_center_id)
    {
        if (!isset($dly_center_id) || !$dly_center_id)
        {
            $this->begin("index.php?app=express&ctl=admin_delivery_center&act=index");
            $this->end(false, __("发货地址不错在！"));
        }
        
        $obj_dly_center = $this->app->model('dly_center');
        $arr_dly_center = $obj_dly_center->dump($dly_center_id);
        $this->pagedata['dly_center'] = $arr_dly_center;
        $this->pagedata['default_dc'] = $this->app->getConf('system.default_dc');
        $this->page('admin/delivery/delivery_edit.html');
    }
    
    public function instance($dly_center_id){
        $obj_delivery_center = $this->app->model('dly_center');
        $aData = $obj_delivery_center->dump($dly_center_id);
        
        $this->pagedata['the_dly_center'] = $aData;
        $this->display('admin/delivery/dly_center.html');
    }
}