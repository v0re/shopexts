<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class ectools_refund_create
{
	/**
     * 共有构造方法
     * @params app object
     * @return null
     */
    public function __construct($app)
    {        
        $this->app = $app;
    }
	
	/**
     * 退款单标准数据生成
     * @params array - 订单数据
     * @params string - 唯一标识
     * @return boolean - 成功与否
     */
    public function generate(&$sdf)
    {
		// 退款单创建是和中心的交互
		$obj_refund = $this->app->model('refunds');
		$is_save = $obj_refund->save($sdf);
		
		if (!$is_save)
		{
			return false;
		}
		
		return true;
	}
}