<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

interface base_interface_kvstore{

    function store($key, $value, $ttl=0);

    function fetch($key, &$value, $timeout_version=null);

    function delete($key);
}
