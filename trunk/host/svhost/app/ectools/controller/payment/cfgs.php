<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class ectools_ctl_payment_cfgs extends desktop_controller{

    var $workground = 'ectools_ctl_payment_cfgs';
	
	public function __construct($app)
	{
		parent::__construct($app);
		header("cache-control: no-store, no-cache, must-revalidate");
	}
	
    function index(){
        $this->finder('ectools_mdl_payment_cfgs',array(
            'title'=>'支付方式',
            'actions'=>array(
					array('label'=>'添加支付方式','icon'=>'add.gif','href'=>'index.php?app=desktop&ctl=appmgr&act=index'),
				),'use_buildin_recycle'=>false,
		));
    }
    
    function setting($pkey){
        if(!$pkey){
            return false;
        }
        
        if ($_POST['setting'])
		{
			$this->begin('index.php?app=ectools&ctl=payment_cfgs&act=index');
			$payment = new $pkey($this->app);
			$setting = $payment->setting();
			
			foreach ($setting as $key=>$setting_item)
			{
				if ($setting_item['type'] == 'pecentage')
					$_POST['setting'][$key] = $_POST['setting'][$key] * 0.01;
			}
            $data['setting'] = $_POST['setting'];
            $data['status'] = $_POST['status'];
			$data['pay_type'] = $_POST['pay_type'];
            $this->app->setConf($pkey,serialize($data));
            //$html.='<div>设置已经保存</div>';
			$this->end(true, __('支付方式修改成功！'));
        }
		else
		{
			$payment = new $pkey($this->app);
			$setting = $payment->setting();
			if($setting){
				$val = $this->app->getConf($pkey);
				$val = unserialize($val);
				$render = $this->app->render();
				$render->pagedata['admin_info'] = $payment->admin_intro();
				$render->pagedata['settings'] = $setting;
				foreach ($setting as $k=>$v)
				{
					$render->pagedata['settings'][$k]['value'] = $val['setting'][$k] ? $val['setting'][$k] : $val[$k];
					if ($v['type'] == 'pecentage')
						$render->pagedata['settings'][$k]['value'] = $render->pagedata['settings'][$k]['value'] * 100;
				}				
				$render->pagedata['classname'] = $pkey;
				$render->display('payments/cfgs/cfgs.html');
			}else{
				echo '<div class="note">不需要设置参数</div>';
			}
        }        
    }
}
