<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_builder_packet extends desktop_finder_builder_prototype{

    function main(){
        
        $this->controller->pagedata['data'] = $this->controller->_views();
        $this->controller->display('finder/view/packet.html','desktop');
            
    }

}
