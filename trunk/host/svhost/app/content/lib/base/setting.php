<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_base_setting 
{
    function __construct(&$app) 
    {
        $this->app = $app;
        $this->ui = kernel::single("base_component_ui", $this);
    }//End Function

    public function process($all_settings) 
    {
        if(!is_array($all_settings))    return '';
        $setting = new base_setting($this->app);
        $setlib = $setting->source();
        $typemap = array(
            SET_T_STR=>'text',
            SET_T_INT=>'number',
            SET_T_ENUM=>'select',
            SET_T_BOOL=>'bool',
            SET_T_TXT=>'text',
            SET_T_FILE=>'file',
            SET_T_IMAGE=>'image',
            SET_T_DIGITS=>'number',
        );
        $tabs = array_keys($all_settings);
        $html = $this->ui->form_start(array('tabs'=>$tabs));
        foreach($tabs as $tab=>$tab_name){
            foreach($all_settings[$tab_name] as $set){
                $current_set = $this->app->getConf($set);
                if($_POST['set'] && array_key_exists($set,$_POST['set'])){
                    if($current_set!=$_POST['set'][$set]){
                        $current_set = $_POST['set'][$set];
                        $this->app->setConf($set,$_POST['set'][$set]);
                    }
                }
                
                $input_type = $typemap[$setlib[$set]['type']];
                
                $form_input = array(
                    'title'=>$setlib[$set]['desc'],
                    'type'=>$input_type,
                    'name'=>"set[".$set."]",
                    'required'=>true,
                    'tab'=>$tab,
                    'value'=>$current_set,
                    'options'=>$setlib[$set]['options'],
                );
                
                if($input_type=='image'){
                    
                   $form_input = array_merge($form_input,array(
                   
                      'width'=>$setlib[$set]['width'],
                      'height'=>$setlib[$set]['height']
                   
                   ));
                
                }

                $html.=$this->ui->form_input($form_input);
            }
        }
        return $html .= $this->ui->form_end();
    }//End Function

}//End Class
