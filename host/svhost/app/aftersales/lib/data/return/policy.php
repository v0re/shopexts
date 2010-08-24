<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class aftersales_data_return_policy extends b2c_api_rpc_request
{
    /**
     * return model object
     */
    public $rProduct;
    
    /**
     * return product list item status
     */
    private $arr_status = array(
        '1' => '申请中',
        '2' => '审核中',
        '3' => '接受申请',
        '4' => '完成',
        '5' => '拒绝',
    );
    
    /**
     * 构造方法 
     * @param object application
     * @return null
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->rProduct = &$this->app->model('return_product');
    }
    
    /**
     * 得到本类应用的配置参数
     * @params array 参数数组 - 取地址
     * @return boolean true or false
     */
    public function get_conf_data(&$arr_settings)
    {
        $arr_settings['is_open_return_product'] = $this->app->getConf('site.is_open_return_product');
        $arr_settings['return_product_comment'] = $this->app->getConf('site.return_product_comment');
        
        return ($arr_settings && is_array($arr_settings)) ? true : false;
    }
    
    /**
     * 得到满足条件的售后申请列表
     * @param string database table columns
     * @param array conditions
     * @param int page code
     * @return array 结果数组
     */
    public function get_return_product_list($clos='*', $filter = array(), $nPage=1)
    {
        $arr_return_products = array();
        
        $aData = $this->rProduct->getList($clos,$filter,($nPage-1)*10,10,'add_time DESC');
        $count = $this->rProduct->count($filter);
        
        return $arr_return_products = array(
            'data' => $aData,
            'total' => $count,
        );
    }
    
    public function change_status(&$sdf)
    {
        $is_changed = $this->rProduct->change_status($sdf);
        
        if ($is_changed)
        {
            $arr_data = $this->rProduct->dump($sdf['return_id']);
            $sdf['return_id'] = $arr_data['return_id'];
            $sdf['order_id'] = $arr_data['order_id'];
            $sdf['status'] = $arr_data['status'];
            
            return $sdf['status'];
        }
		else
		{
			return false;
		}
    }
    
    /**
     * 保存售后申请单的信息
     * @param array sdf 标准数组
     * @param string 保存消息
     * @return boolean true or false
     */
    public function save_return_product(&$sdf, &$msg='')
    {
        $sdf['return_id'] = $this->rProduct->gen_id();
        $is_save = $this->rProduct->save($sdf);
        
        if (!$is_save)
        {
            $msg = '数据保存失败！';
            
            return false;
        }        
        
        return true;
    }
    
    /**
     * 得到特定的售后申请单的信息
     * @param string 售后申请单编号
     * @return array 
     */
    public function get_return_product_by_return_id($return_id=0)
    {
        if (!$return_id)
            return array();
        
        $arr_data = $this->rProduct->dump($return_id);
        
        if ($arr_data)
        {
            $arr_data['product_data'] = unserialize($arr_data['product_data']);
            $arr_data['comment'] = unserialize($arr_data['comment']);
            $arr_data['status'] = $this->arr_status[$arr_data['status']];
        }
        return $arr_data;
    }
    
    public function file_download($return_id=0)
    {
        if ($return_id)
        {
            $rp = &$this->app->model('return_product');

            $info = $rp->dump($return_id);
            $filename = $info['image_file'];
            $obj_images = app::get('image')->model('image');
            $arr_image = $obj_images->dump($filename);
            $filename = ROOT_DIR . '/' . $arr_image['url'];
            
            if ($filename)
            {
                $file = fopen($filename,"r");
                Header("Content-type: application/octet-stream");
                Header("Accept-Ranges: bytes");
                Header("Accept-Length: ".filesize($filename));
                Header("Content-Disposition: attachment; filename=".basename($filename));
                echo fread($file,filesize($filename));
                fclose($file);
            }
        }
    }
}