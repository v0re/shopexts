<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_ctl_admin_shoprelation extends desktop_controller
{
    var $workground = 'desktop_other';
    
    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }
    
    function index()
    {
        $this->finder('b2c_mdl_shop',array(
            'title'=>'网店绑定关系',
            'actions' => array(
                    array('label'=>'新建绑定关系','icon'=>'add.gif','href'=>'index.php?app=b2c&ctl=admin_shoprelation&act=addnew','target'=>'_blank'),
                    array('label'=>'查看绑定情况','icon'=>'add.gif','onclick'=>'new Dialog(\'index.php?ctl=shoprelation&act=index&p[0]=accept&p[1]=' . $this->app->app_id . '\',{title:\'查看绑定情况\'})'),
                ),
            ));
    }
    
    public function addnew()
    {
        if ($_POST['shop'])
        {
            $this->begin();
            
            if ($_POST['shop']['shop_id'])
                $arr_data['shop_id'] = $_POST['shop']['shop_id'];
            $arr_data['name'] = $_POST['shop']['name'];
            foreach ($_POST['shop'] as $key=>$value)
            {
                $arr_data[$key] = $value;
            }
            $obj_shop = $this->app->model('shop');
            
            if ($obj_shop->save($arr_data))
            {
                $this->end(true, __('添加成功！'));
            }
            else
            {
                $this->end(false, __('添加失败！'));
            }
        }
        else
        {
            $this->singlepage('admin/page_shoprelation.html');
        }
    }
    
    public function showEdit($shop_id=0)
    {
        $obj_shop = $this->app->model('shop');
        $arr_shop = $obj_shop->dump($shop_id);
        
        if ($arr_shop)
        {
            $this->pagedata['shoprelation'] = $arr_shop;
        }
        
        $this->singlepage('admin/page_shoprelation.html');
    }
}