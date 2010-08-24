<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_article_single 
{
    function __construct(&$app) 
    {
        $this->layout_res_dir = app::get('content')->res_dir.'/layout';
    }//End Function
    
    public function editor($article_id, $layout) 
    {
        $bodys = kernel::single('content_article_detail')->get_body($article_id, true);
        if(empty($bodys['content'])){
            $data['content'] = file_get_contents($this->layout_res_dir . '/1-column/layout.html');
            app::get('content')->model('article_bodys')->update($data, array('article_id'=>$article_id));
        }else{
            if($layout){
                $data['content'] = file_get_contents($this->layout_res_dir . '/' . $layout . '/layout.html');
                if(app::get('content')->model('article_bodys')->update($data, array('article_id'=>$article_id))){
                    app::get('content')->model('article_indexs')->update(array('uptime'=>time()), array('article_id'=>$article_id));
                    $setting = $this->get_layout($layout);
                    $setting['slotsNum'] = intval($setting['slotsNum']);
                    if($setting['slotsNum']>0){
                        $setting['slotsNum']--;
                        $db = kernel::database();
                        $db->exec("update sdb_site_widgets_instance set core_slot=".$db->quote($setting['slotsNum'])." where core_slot>".intval($setting['slotsNum'])." and core_file='content:".$article_id."'");
                    }
                }
            }
        }
        return true;
    }//End Function

    public function get_layout_list(){
        $handle = opendir($this->layout_res_dir);
        $t = array();

        while(false!==($file=readdir($handle))){
            if(in_array($file, array('.', '..', '.svn')))   continue;

            $layouts[$file] = require($this->layout_res_dir . '/' . $file . '/layout_' . $file . '.php');
        }
        closedir($handle);
        return $layouts;
    }//End Function

    public function get_layout($layout) 
    {
        $layouts = $this->get_layout_list();
        return $layouts[$layout];
    }//End Function
}//End Class
