<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * 订单操作的接口方法的说明
 * 定义订单处理的基础方法
 * 现在主要的订单处理行动（支付，发货，完成，退款，退货和作废）
 */
interface ectools_interface_operation
{    
    /**
     * 统一处理方法入口
     * @params array - 订单数据
     * @params object - 控制器对象
     * @params string - 支付单生成的记录
     * @return boolean - 创建成功与否
     */
    public function generate(&$sdf, &$controller=null, &$msg='');
}
