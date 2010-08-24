<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class stats_desktop_widgets_exstatistics implements desktop_interface_widget
{   
    /**
     * 构造方法，初始化此类的某些对象
     * @param object 此应用的对象
     * @return null
     */
    public function __construct($app)
    {
        $this->app = $app; 
        $this->render =  new base_render(app::get('stats'));  
    }
    
    /**
     * 获取桌面widgets的标题
     * @param null
     * @return null
     */
    public function get_title()
    {            
        return __("统计分析");        
    }
    
    /**
     * 获取桌面widgets的html内容
     * @param null
     * @return string html内容
     */
    public function get_html()
    {
        $render = $this->render;
        $render->pagedata['page_url'] = SHOPEX_STAT_WEBURL;
        $render->pagedata['certi_id'] = base_certificate::certi_id();
        $render->pagedata['sign'] = md5($render->pagedata['certi_id'].base_certificate::token());
        

        return $render->fetch('desktop/widgets/exstatistics.html');
    }
    
    /**
     * 获取页面的当前widgets的classname的名称
     * @param null
     * @return string classname
     */
    public function get_className()
    {        
        return " valigntop exstatistics";
    }
    
    /**
     * 显示的位置和宽度
     * @param null
     * @return string 宽度数据
     */
    public function get_width()
    {          
        return "l-2";        
    }
}