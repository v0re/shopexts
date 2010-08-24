<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class ectools_mdl_payment_cfgs {

    function __construct(&$app){
        $this->app = $app;
        $this->columns = array(
                        'app_name'=>array('label'=>'支付方式','width'=>200,'is_title'=>true,'pkey'=>true),
                        'app_staus'=>array('label'=>'状态','width'=>100),
                        'app_version'=>array('label'=>'应用程序版本','width'=>200),
                   );

        $this->schema = array(
                'default_in_list'=>array_keys($this->columns),
                'in_list'=>array_keys($this->columns),
                'idColumn'=>'app_id',
                'textColumn'=>'app_name',
                'columns'=>&$this->columns
            );
    }
	
	/**
	 * suffix of model
	 * @params null
	 * @return string table name
	 */
	public function table_name()
	{
		return 'payment_cfgs';
	}
    
    function get_schema(){
        return $this->schema;
    }
    
    function count($filter=''){
        $arrServicelist = kernel::servicelist('ectools_payment.ectools_mdl_payment_cfgs');
        foreach($arrServicelist as $class_name => $object){
            $i++;
        }
        return $i+1;
    }
	
	/**
	 * 取到服务列表 - 1条或者多条
	 * @params string - 特殊的列名
	 * @params array - 限制条件
	 * @params 偏移量起始值
	 * @params 偏移位移值
	 * @params 排序条件
	 */
    public function getList($cols='*', $filter=array('status' => 'false'), $offset=0, $limit=-1, $orderby=null){
        //todo fitler;
		$arrServicelist = kernel::servicelist('ectools_payment.ectools_mdl_payment_cfgs');
        foreach($arrServicelist as $class_name => $object){
            $strPaymnet = $this->app->getConf($class_name);
            $arrPaymnet = unserialize($strPaymnet);


            $row['app_name'] = $object->name;
            $row['app_staus'] = (($arrPaymnet['status']===true||$arrPaymnet['status']==='true') ? '开启' : '关闭');
            $row['app_version'] = $object->ver;
            $row['app_id'] = $object->app_key;
            $row['app_class'] = $class_name;
            $row['app_des'] = isset($arrPaymnet['setting']['pay_desc']) ? $arrPaymnet['setting']['pay_desc'] : "";
            $row['app_pay_type'] = $arrPaymnet['pay_type'];
            $row['app_display_name'] = $arrPaymnet['display_name'];
            $row['app_info'] = $object->intro();
            $row['support_cur'] = $arrPaymnet['setting']['support_cur'];
            $row['pay_fee'] = $arrPaymnet['setting']['pay_fee'];

            if($filter['app_id']){
                $app_id = is_array($filter['app_id'])?$filter['app_id'][0]:$filter['app_id'];
                return array($this->getPaymentInfo($app_id));
            }
                    
            
			if (isset($filter) && $filter)
			{
				if (isset($filter['is_frontend']) && !$filter['is_frontend'])
				{
					if ($filter['status'] == 'false' || $arrPaymnet['status'] === true || $arrPaymnet['status'] === 'true' || !isset($arrPaymnet['status']))
					{
						$data[] = $row;
					}
				}
				else
				{
					if (!isset($filter['is_frontend']))
					{
						$data[] = $row;
					}
					else
					{
						if (isset($arrPaymnet['status']) && $arrPaymnet['status'] === 'true')
						{
							$data[] = $row;
						}
					}					
				}
			}
			else
			{
                if($filter['app_id']){
                
                }
				$data[] = $row;
			}
        }
		
        return $data;
    }
	
	/**
	 * 取到特定的支付方式
	 * @params string - 字符方式的名称
	 * @return array - 支付方式的结果数组
	 */
	public function getPaymentInfo($pay_app_id='alipay')
	{
		if ($pay_app_id != '货到付款')
		{
			//$class_name = "ectools_payment_plugin_" . $pay_app_id;
			$class_name = "";
			$obj_app_plugins = kernel::servicelist("ectools_payment.ectools_mdl_payment_cfgs");
			foreach ($obj_app_plugins as $obj_app)
			{
				$app_class_name = get_class($obj_app);
				$arr_class_name = explode('_', $app_class_name);
				if (isset($arr_class_name[count($arr_class_name)-1]) && $arr_class_name[count($arr_class_name)-1])
				{
					if ($arr_class_name[count($arr_class_name)-1] == $pay_app_id)
					{
						$pay_app_ins = $obj_app;
						$class_name = $app_class_name;
					}
				}
				else
				{
					if ($app_class_name == $sdf['pay_app_id'])
					{
						$pay_app_ins = $obj_app;
						$class_name = $app_class_name;
					}
				}
			}
			$strPayment = $this->app->getConf($class_name);
			$arrPaymnet = unserialize($strPayment);
			$objPayment = kernel::single($class_name);
			
			$row = array(
				'app_name' => $objPayment->name,
				'app_staus' => (($arrPaymnet['status']===true||$arrPaymnet['status']==='true') ? '开启' : '关闭'),
				'app_version' => $objPayment->ver,
				'app_id' => $objPayment->app_key,
				'app_class' => $class_name,
				'app_des' => $arrPaymnet['setting']['pay_desc'],
				'app_pay_type' => $arrPaymnet['pay_type'],
				'app_display_name' => $objPayment->display_name,
				'app_info' => $objPayment->intro(),
				'support_cur' => $arrPaymnet['setting']['support_cur'],
				'pay_fee' => $arrPaymnet['setting']['pay_fee'],
			);
		}
		else
		{
			$row = array(
				'app_name' => 'COD',
				'app_staus' => '开启',
				'app_version' => '1.0',
				'app_id' => 'COD',
				'app_class' => 'COD',
				'app_des' => '货到付款',
				'app_pay_type' => 'offline',
				'app_display_name' => '货到付款',
				'app_info' => '货到付款',
				'support_cur' => '1',
			);
		}
		
		return $row;
	}
}
