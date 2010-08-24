<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class ectools_payment_update
{
	/**
	 * app object
	 */
	public $app;
	
	/**
	 * 构造方法
	 * @param object app
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}
	
	/**
	 * 支付单修改
	 * @param array sdf
	 * @param string message
	 * @return boolean sucess of failure
	 */
	public function generate(&$sdf, &$msg='')
	{
		// 修改支付单是和中心的交互
		$objPayments = $this->app->model('payments');
        $data['payment_id'] = $sdf['payment_id'];
        $data['trade_no'] = $sdf['trade_no'];
        $data['t_payed'] = $sdf['t_payed'];
        $data['status'] = ($sdf['status'] == 'succ' || $sdf['status'] === true || $sdf['status'] == 'progress') ? $sdf['status'] : 'failed';
		
        $is_save = $objPayments->save($data);
		
		if ($is_save)
			return true;
		else
		{
			$msg = '支付单修改失败！';
			return false;
		}
	}
}