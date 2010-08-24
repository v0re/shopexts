<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class ectools_payment_app
{
	// attribute array
	protected $fields = array();
	// app object.
	protected $app;
	// callback_url
	protected $callback_url;
	//submit_url
	protected $submit_url;
	//submit_method
	protected $submit_method;
	//submit_charset
	protected $submit_charset;
	
	/**
	 * 构造方法
	 * @params string - app id
	 * @return null
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}
	
	/**
	 * 设置属性
	 * @params string key
	 * @params string value
	 * @return null
	 */
	protected function add_field($key, $value='')
	{
		if (!$key)
		{
			trigger_error("Key不能为空！", E_USER_ERROR);exit;
		}
		
		$this->fields[$key] = $value;
	}
	
	/**
	 * 得到配置参数
	 * @params string key
	 * @payment api interface class name
	 */
	protected function getConf($key, $pkey)
	{		
        $val = app::get('ectools')->getConf($pkey);
        $val = unserialize($val);
		
		return $val['setting'][$key];
	}
	
	/**
	 * 生成支付方式提交的表单的请求
	 * @params null
	 * @return string
	 */
	protected function get_html()
	{
		// 简单的form的自动提交的代码。
		$strHtml = '<form action="' . $this->submit_url . '" method="' . $this->submit_method . '" name="pay_form" id="pay_form">';
		
		// Generate all the hidden field.
		foreach ($this->fields as $key=>$value)
		{
			$strHtml .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
		}
		
		$strHtml .= '<input type="submit" name="btn_purchase" value="购买" style="display:none;" />';
		$strHtml .= '</form><script type="text/javascript">
						document.pay_form.submit();						
					</script>';
		
		return $strHtml;
	}
}
