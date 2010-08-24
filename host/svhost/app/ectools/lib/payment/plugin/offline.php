<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

final class ectools_payment_plugin_offline extends ectools_payment_app implements ectools_interface_payment_app {
	public $name = '线下支付';
    public $app_name = '线下支付接口';
    public $app_key = 'offline';
    public $display_name = '线下支付';
    public $curname = 'CNY';
    public $ver = '1.0';
	
	//构造支付接口基本信息
    public function __construct($app){
		parent::__construct($app);
		
        //$this->callback_url = $this->app->base_url(true)."/apps/".basename(dirname(__FILE__))."/".basename(__FILE__);
		$this->callback_url = "";
        $this->submit_url = '';
        $this->submit_method = 'POST';
        $this->submit_charset = 'utf-8';
    }
	
	/**
	 * 显示支付接口表单基本信息
	 * @params null
	 * @return string - description include account.
	 */
    public function admin_intro(){
        return '线下支付后台自定义描述';
    }
	
	public function intro(){
        return '线下支付客户自定义描述';
    }
	
	/**
	 * 显示支付接口表单选项设置
	 * @params null
	 * @return array - 字段参数
	 */
    public function setting(){
        return array(
                    'pay_name'=>array(
                        'title'=>'支付方式名称',
                        'type'=>'string'
                    ),
					'support_cur'=>array(
                        'title'=>'支持币种',
                        'type'=>'text hidden',
						'options'=>array('1'=>'人民币','2'=>'其他','3'=>'商店默认货币')
                    ),
                    'pay_desc'=>array(
                        'title'=>'描述',
                        'type'=>'html',
						'includeBase' => true,
                    ),
					'pay_type'=>array(
						 'title'=>'支付类型(是否在线支付)',
						 'type'=>'hidden',
						 'name' => 'pay_type',
					),
					'status'=>array(
						'title'=>'是否开启此支付方式',
						'type'=>'radio',
						'options'=>array('false'=>'否','true'=>'是'),
						'name' => 'status',
					),
                );
    }
	
	//支付接口表单提交方式
    public function dopay($payment)
	{
        // 线下支付，直接修改支付单据
    }
	
	//验证提交表单参数有效性
    public function is_fields_valiad(){
        return true;    
    }
	
	public function callback(&$recv)
	{
	}
	
	public function gen_form()
	{
		return '';
	}
}
