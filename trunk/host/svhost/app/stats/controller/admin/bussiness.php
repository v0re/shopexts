<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class stats_ctl_admin_bussiness extends desktop_controller
{
	public $workground = 'b2c_ctl_admin_sale';
	
	public $certi_id = "";
	
	public $token = "";
	
	/**
     * 췽
     * @params object app object
     * @return null
     */
    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
		$this->certi_id = base_certificate::certi_id();
        $this->token = base_certificate::token();
    }
	
	public function index()
	{        
        if (!$this->token){
            $this->begin('index.php?app=desktop&ctl=default&act=workground&wg=b2c.wrokground.sale');
			$this->end(false, __('LISSENCE不正确！'));
        }
		
        $sign = md5($this->certi_id.$this->token);
        $shoex_stat_webUrl = SHOPEX_STAT_WEBURL."?site_id=".$this->certi_id."&sign=".$sign;
		
        $this->pagedata['shoex_stat_webUrl'] = $shoex_stat_webUrl;
        $this->page('admin/bussiness/index.html');
	}
}