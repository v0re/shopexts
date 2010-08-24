<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/**
 * 定义order操作的抽象类
 * 主要实现接口ectools_interface_order_operaction的freezeGoods和unfreezeGoods的方法
 * ectools payment operation version 0.1
 */
abstract class ectools_operation implements ectools_interface_operation
{
    // 对应的应用对象
    protected $app;
    
    // 对象实体
    protected $model;
}
