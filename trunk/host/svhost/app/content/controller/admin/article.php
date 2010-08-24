<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

/*
 * @package content
 * @subpackage article
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license 
 */

class content_ctl_admin_article extends content_admin_controller 
{
    
    var $workground = 'site.wrokground.theme';

    public function index() 
    {
        $this->finder('content_mdl_article_indexs', array(
            'title'=>'页面列表',
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'actions'=>array(
                            array('label'=>'添加文章','icon'=>'add.gif','href'=>'index.php?app=content&ctl=admin_article_detail&act=add&type=1','target'=>'_blank'),
                            array('label'=>'添加单独页','icon'=>'add.gif','href'=>'index.php?app=content&ctl=admin_article_detail&act=add&type=2','target'=>'_blank'),
                            array('label'=>'添加自定义页','icon'=>'add.gif','href'=>'index.php?app=content&ctl=admin_article_detail&act=add&type=3','target'=>'_blank'),
                        )
            ));
    }//End Function

}//End Class
