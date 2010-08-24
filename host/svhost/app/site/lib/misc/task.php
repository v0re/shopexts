<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class site_misc_task{

    function week(){

    }

    function minute(){
        
    }

    function hour(){

    }

    function day(){
        $this->auto_sitemaps();
    }

    function month(){

    }

    private function auto_sitemaps() 
    {
        kernel::single('site_sitemaps')->create();
    }//End Function 
    
    
    

}
