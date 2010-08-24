<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class ectools_refund_update
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
     * 退款当标准数据修改
     * @params array - 订单数据
     * @params string - 唯一标识
     * @return boolean - 成功与否
     */
    public function generate(&$sdf)
    {
		// 退款单修改是和中心的交互
		$objRefunds = $this->app->model('refunds');
        $data['refund_id'] = $sdf['refund_id'];
        $data['trade_no'] = $sdf['trade_no'];
        $data['t_payed'] = $sdf['t_payed'];
        $data['status'] = ($sdf['status'] == 'succ' || $sdf['status'] === true) ? 'succ' : 'failed';
		
        $is_save = $objRefunds->save($data);
		
		if ($is_save)
		{
			return true;
		}
		else
		{
			$msg = '支付单修改失败！';
			return false;
		}
		
	}
}