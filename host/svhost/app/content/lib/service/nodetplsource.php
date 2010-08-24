<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_service_nodetplsource 
{
    public function last_modified($id) 
    {
        $info = kernel::single('content_article_node')->get_node($id);
        return $info['uptime'];
    }//End Function

    public function get_file_contents($id) 
    {
        $info = kernel::single('content_article_node')->get_node($id);
        return $info['content'];
    }//End Function 
}//End Class 18:55 2010-6-9
