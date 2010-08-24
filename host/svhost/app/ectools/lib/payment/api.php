<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class ectools_payment_api
{
	// 应用对象的实例。
	private $app;
	
	public function __construct($app)
	{
		$this->app = $app;
	}
	
	/**
	 * 支付返回后的同意支付处理
	 * @params array - 页面参数
	 * @return null
	 */
	public function parse($params='')
	{		
		// 取到内部系统参数
		$pathInfo = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "parse/") + 6); 
		$objShopApp = $this->getAppName($pathInfo);
		$innerArgs = explode('/', $pathInfo);
		$class_name = array_shift($innerArgs);
		$class_name = array_shift($innerArgs);
		$method = array_shift($innerArgs);
		
		$arrStr = array();
		$arrSplits = array();
		$arrQueryStrs = array();
		// QUERY_STRING
		if (isset($innerArgs) && $innerArgs)
		{
			$querystring = array_shift($innerArgs);
			if ($querystring)
			{
				$querystring = substr($querystring, 1);
				$arrStr = explode("&", $querystring);
				
				foreach ($arrStr as $str)
				{
					$arrSplits = explode("=", $str);
					$arrQueryStrs[urldecode($arrSplits[0])] = urldecode($arrSplits[1]);
				}
			}
		}
		
		$payments_bill = new $class_name($objShopApp);
		$ret = $payments_bill->$method($arrQueryStrs);
		
		// 支付结束，回调服务.
		if ($ret['status'] != 'failed')
		{
			$obj_payments = app::get('ectools')->model('payments');
			$sdf = $obj_payments->dump($ret['payment_id'], '*', '*');
			$sdf['account'] = $ret['account'];
			$sdf['bank'] = $ret['bank'];
			$sdf['pay_account'] = $ret['pay_account'];
			$sdf['currency'] = $ret['currency'];
			//$sdf['money'] = $ret['money'];
			//$sdf['paycost'] = $ret['paycost'];
			//$sdf['cur_money'] = $ret['cur_money'];
			$sdf['trade_no'] = $ret['trade_no'];
			$sdf['t_payed'] = $ret['t_payed'];
			$sdf['pay_app_id'] = $ret['pay_app_id'];
			$sdf['pay_type'] = $ret['pay_type'];			
			$sdf['memo'] = $ret['memo'];
			
			//$obj_api_payment = kernel::service("api.ectools.payment");
			$obj_payment_update = kernel::single('ectools_payment_update');
			$obj_payment_update->generate($ret, $msg);
			
			$obj_pay_lists = kernel::servicelist("order.pay_finish");
			foreach ($obj_pay_lists as $order_pay_service_object)
			{
				$class_name = get_class($order_pay_service_object);
				if (strpos($class_name, $objShopApp->app_id . '_') !== false)
				{
					$order_pay_service_object->order_pay_finish($sdf, $ret['status'], 'font');	
				}
			}
			/*$objOrders = $this->app->model('orders');
			$objOrders->order_pay_finish($ret, $ret['status']);*/			
		}
	}
	
	/** 
	 * 得到实例应用名
	 * @params string - 请求的url
	 * @return object - 应用实例
	 */
	private function getAppName($strUrl='')
	{
		//todo.
		if (strpos($strUrl, '/') !== false)
		{
			$arrUrl = explode('/', $strUrl);
		}
		return app::get($arrUrl[0]);
	}
}
