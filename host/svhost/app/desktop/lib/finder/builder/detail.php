<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_builder_detail extends desktop_finder_builder_prototype{

    function main(){
        if(kernel::single('base_component_request')->is_ajax()&&$_GET['target']=='_self'){ 
            //$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
            
            
            if(isset($this->detail_pages)){
                $tab_header = '<div class="tabs-wrap finder-tabs-wrap clearfix"><ul>';
                foreach($this->detail_pages as $k=>$detail_func){
                    if($k==$_GET['view']){
                        $tab_header.='<li class="tab current"><span>';
                        $tab_header.= $detail_func[0]->$detail_func[1];
                        $detail_html = $detail_func[0]->$detail_func[1]($_GET['id']);
                    }else{                                                                 
                        //if($_GET['view'])unset($_GET['view']);
                        $tab_action = $this->url.'&action=detail&view='.$k;
                        $tab_header.='<li class="tab"><span>';
                        $tab_header.='<a target="{update:\'finder-detail-'.$this->name.'\'}" href="'.$tab_action.'">'.$detail_func[0]->$detail_func[1].'</a>';
                    }

                    $tab_header.='</span></li>';
                }
                $tab_header.='</ul></div>';
            }
            if(isset($this->detail_pages[1])){
                echo $tab_header;
            }
            echo $detail_html;
        }else{
            foreach($this->detail_pages as $k=>$detail_func){
                if(kernel::single('base_component_request')->is_ajax()&&$_GET['view']==$k&&$_GET['target']=='_blank'){
                    $html = $detail_html = $detail_func[0]->$detail_func[1]($_GET['id']);
                $label = $detail_func[0]->$detail_func[1];
echo <<<EOF
<h3>{$label}</h3>    
{$html}
EOF;
exit;
                }
                $detail_html = $detail_func[0]->$detail_func[1]($_GET['id']);

                $this->controller->pagedata['_detail_func'][] = array(
                        'label' => $detail_func[0]->$detail_func[1],
                        'html' => $detail_html,
                    );
            }


            $this->controller->singlepage('common/detail-in-one.html', 'desktop');
        }
    }

}
