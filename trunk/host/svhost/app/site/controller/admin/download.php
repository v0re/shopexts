<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class site_ctl_admin_download extends site_admin_controller 
{
    
    /*
     * workground
     * @var string
     */
    var $workground = 'site_ctl_admin_download';

    public function index() 
    {
        $ident = $this->_request->get_get('ident');
        if(empty($ident))   die('参数错误');

        $imgObj = kernel::single('site_utility_download');
        $ident = $imgObj->dl($ident);
        if($ident){
            echo '<script>success("'.$ident.'");</script>';
        }else{
            echo '<script>failure("下载出错")</script>';
        }
    }//End Function

}//End Class
