<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_service_tplsource 
{
    public function last_modified($article_id) 
    {
        $indexs = kernel::single('content_article_detail')->get_index($article_id);
        return $indexs['uptime'];
    }//End Function

    public function get_file_contents($article_id) 
    {
        $detail = kernel::single('content_article_detail')->get_detail($article_id);
        if($detail['indexs']['type'] == 3){
            $this->parse_custom_tag($detail['bodys']['content']);
        }//如果是自定义页面，去掉头尾标签
        return $detail['bodys']['content'];
    }//End Function

    public function parse_custom_tag(&$content) 
    {
        if(preg_match('/^\[header\]/', trim($content))){
            $content = preg_replace('/^\[header\]/', '', $content);
        }
        if(preg_match('/\[footer\]$/', trim($content))){
            $content = preg_replace('/\[footer\]$/', '', $content);
        }
    }//End Function

}//End Class
