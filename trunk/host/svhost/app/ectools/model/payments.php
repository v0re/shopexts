<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class ectools_mdl_payments extends dbeav_model{
    
    var $has_many = array(
        'orders'=>'order_bills@ectools:contrast:payment_id^bill_id',
    );
	
	var $defaultOrder = array('t_payed','DESC');
    
    /**
     * 得到唯一的payment id
     * @params null
     * @return string payment id
     */
    public function gen_id(){
        $i = rand(0,9999);
        do{
            if(9999==$i){
                $i=0;
            }
            $i++;
            $payment_id = time().str_pad($i,4,'0',STR_PAD_LEFT);
            $row = $this->dump($payment_id, 'payment_id');
        }while($row);
        return $payment_id;
    }
    
    /**
     * 模板统一保存的方法
     * @params array - 需要保存的支付信息
     * @params boolean - 是否需要强制保存
     * @return boolean - 保存的成功与否的进程
     */
    public function save($data,$mustUpdate = null)
    {
        // 异常处理    
        if (!isset($data) || !$data || !is_array($data))
        {
            trigger_error("支付单信息不能为空！", E_USER_ERROR);exit;
        }
        
        $sdf = array();
       
        // 支付数据列表
        $background = true;//后台 todo

        $payment_data = $data;
        $sdf_payment = parent::dump($data['payment_id'],'*');

        if ($sdf_payment) 
        {
            if($sdf_payment['status'] == $data['status']
                || ($sdf_payment['status'] != 'progress' && $sdf_payment['status'] != 'ready')){
                return true;
            }    
            if($data['currency'] && $sdf_payment['currency'] != $data['currency']){
                $msg = __('#支付货币和订单货币不一致');
                return false;
            }
        }

        if($sdf_payment){
            $sdf = array_merge($sdf_payment, $data);
        }else{
            $sdf = $data;
            $sdf['status'] = $sdf['status'] ? $sdf['status'] : 'ready';
        }
        
        // 保存支付信息（可能是退款信息）
        $is_succ = parent::save($sdf);
        
        return $is_succ;
    }
	
	/**
	 * 得到所有的支付账号
	 * @param null
	 * @return null
	 */
	public function getAccount()
	{
        $query = 'SELECT DISTINCT bank, account FROM ' . $this->table_name(1) .' WHERE status="succ"';
        return $this->db->select($query);
    }
    
    /**
     * delete 方法重载
     *
     * 根据条件删除条目
     * 不可以由pipe控制
     * 可以广播事件
     * 
     * @param mixed $filter 
     * @param mixed $named_action 
     * @access public
     * @return void
     */
    public function delete($filter)
    {
        if ($_POST['payment_id'])
        {
            foreach ($_POST['payment_id'] as $payment_id)
            {
                $arrPayments = array(
                    'payment_id' => $payment_id,
                    'disabled' => 'true',
                );
                
                $is_delete = parent::save($arrPayments);
            }
        }
    }
    
    /**
     * 重载getList方法
     * @params string - 特殊的列名
     * @params array - 限制条件
     * @params 偏移量起始值
     * @params 偏移位移值
     * @params 排序条件
     */
    public function getList($cols='*', $filter=array('disabled' => 'false'), $offset=0, $limit=-1, $orderby=null)
    {
        if ($filter)
            return parent::getList($cols, $filter, $offset, $limit, $orderby);
        else
            return parent::getList($cols, array('disabled' => 'false'), $offset, $limit, $orderby);
    }
}
