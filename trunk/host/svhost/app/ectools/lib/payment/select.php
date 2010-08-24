<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class ectools_payment_select
{	
	function select_pay_method(&$controller, &$sdf, $member_id=0, $is_backend=false){
		$payment_cfg = app::get('ectools')->model('payment_cfgs');
		$currency = app::get('ectools')->model('currency');
		$payments = array();
		$arrPayments = $payment_cfg->getList('*', array('status' => 'true', 'is_frontend' => true));
		$currency = $sdf['cur'] ? $sdf['cur'] : $currency->getDefault();
		
		if (!$member_id)
		{
			if (!$is_backend)
			{
				$arr_members = $controller->get_current_member();
				$member_id = $arr_members['member_id'];
			}
		}		
		
		if ($arrPayments)
		{
			foreach($arrPayments as $key=>$payment)
			{
				if (!$member_id)
				{
					if (trim($payment['app_id']) == 'deposit')
					{
						unset($arrPayments[$key]);
						continue;
					}
				}
				
				if ($currency == 'CNY')
				{
					if ($payment['support_cur'] == '1' || $payment['support_cur'] == '3')
						$payments[] = $payment;
				}
				else
				{
					if ($payment['support_cur'] == '2')
						$payments[] = $payment;
				}
			}
			
			$controller->pagedata['payments'] = &$payments;
			$controller->pagedata['order'] = &$sdf;
			
			return $controller->fetch("site/common/paymethod.html");
		}
	}
}
