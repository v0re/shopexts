<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_setting extends desktop_controller{

    var $require_super_op = true;

    function __construct($app){
        parent::__construct($app);
        $this->ui = new base_component_ui($this);
        $this->app = $app;
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    function index(){
        $this->basic();
    }

    function basic(){
        $all_settings = array(
            '商店基本设置'=>array(
                'site.logo',
                'system.shopname',
              //  'store.shop_url',
                // 'system.enable_network',
            ),
            '店家信息'=>array(
                'store.site_owner',
                'store.contact',
                'store.telephone',
                'store.mobile',
                'store.email',
                'store.qq',
                'store.wangwang',
                'store.address',
                'store.zip_code',
            ),
            '购物设置'=>array(
                'security.guest.enabled',
                //'site.storage.enabled',
                'site.buy.target',
                'system.money.decimals',
                'system.money.operation.carryset',
                'site.trigger_tax',
                'site.tax_ratio',
                // 'site.delivery_time',
                // 'site.rsc_rpc',
                //'system.goods.fastbuy',
                'site.get_policy.method',
                'site.get_rate.method',
                'site.level_switch',
                'site.level_point',
                'site.min_order_amount',             
            ),
            '购物显示设置'=>array(
                'site.login_type',
                'site.register_valide',
                'site.login_valide',
                'gallery.default_view',
                // 'system.category.showgoods',
                // 'site.show_storage',
                'site.show_mark_price',
                'site.market_price',
                'site.market_rate',
                'site.save_price',
                'site.member_price_display',
                //'site.retail_member_price_display',
               // 'site.wholesale_member_price_display',
                // 'selllog.display.switch',
                // 'selllog.display.limit',
                // 'selllog.display.listnum',
                'goodsbn.display.switch',
                'storeplace.display.switch',
                'goodsprop.display.switch',
                'goodsprop.display.position',
                'gallery.display.listnum',
                'gallery.display.grid.colnum',
            ),
             '其他设置'=>array(
                // 'site.certtext',
                'system.product.alert.num',
                'system.goods.freez.time',
                //'system.admin_verycode',
                // 'system.upload.limit',
                //'system.product.zendlucene',
            ),
        );
       // echo '<span class="head-title">系统设置</span>';
              $html = $this->_process($all_settings);
                echo $html;
//        echo $this->sidePanel();
    }

    function _process($all_settings){
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
        $input_style = false;
        foreach($tabs as $tab=>$tab_name){
            foreach($all_settings[$tab_name] as $set){
                $current_set = $this->app->getConf($set);
                if($_POST['set'] && array_key_exists($set,$_POST['set'])){
                    if($current_set!==$_POST['set'][$set]){
                        $current_set = $_POST['set'][$set];
                        $this->app->setConf($set,$_POST['set'][$set]);
                    }
                }

                $input_type = $typemap[$setlib[$set]['type']];
                if ($set == 'site.get_policy.method' && $current_set != '2')
                {
                    $input_style = true;
                }

                $form_input = array(
                    'title'=>$setlib[$set]['desc'],
                    'type'=>$input_type,
                    'name'=>"set[".$set."]",
                    'tab'=>$tab,
                    'value'=>$current_set,
                    'options'=>$setlib[$set]['options'],
                    'required' => ($input_type=='select'?true:false)
                );

                if ($input_style && $set == 'site.get_rate.method')
                {
                    $form_input['style'] = "display:none;";
                    $input_style = false;
                }

                if($input_type=='image'){

                   $form_input = array_merge($form_input,array(

                      'width'=>$setlib[$set]['width'],
                      'height'=>$setlib[$set]['height']

                   ));

                }

                $html.=$this->ui->form_input($form_input);
            }
        }
        
        $this->pagedata['_PAGE_CONTENT'] = $html .= $this->ui->form_end() . '<script type="text/javascript">window.addEvent(\'domready\',function(){
          
            $$(\'input[name^=set[site.trigger_tax]\',\'input[name^=set[site.show_mark_price]\').addEvent(\'click\',function(e){
                    var row=this.getParent(\'tr\');                     
                    if(this.checked&&this.get(\'value\')==\'true\'){
                        row.getNext(\'tr\').show();
                        if(this.name==\'set[site.show_mark_price]\')
                        row.getNext(\'tr\').getNext(\'tr\').show();
                    }
                    if(this.checked&&this.get(\'value\')==\'false\'){
                        row.getNext(\'tr\').hide();
                        if(this.name==\'set[site.show_mark_price]\')
                        row.getNext(\'tr\').getNext(\'tr\').hide();
                    }
           });            
            $$(\'input[name^=set[site.trigger_tax]\',\'input[name^=set[site.show_mark_price]\').each(function(el){
                el.fireEvent(\'click\');                
            })
        

            $$(\'select.inputstyle\').each(function(e){
                if (e.get(\'name\') == "set[site.get_policy.method]")
                {
                    e.addEvent(\'change\', function(){
                        if (e.get(\'value\') == \'2\')
                        {
                            $$(\'input\').each(function(el){
                                if (el.get(\'name\') == \'set[site.get_rate.method]\')
                                {
                                    el.getParent().getParent().show();
                                    el.show();
                                    el.set(\'value\', \'\');
                                }
                            });
                        }
                        else
                        {
                            $$(\'input\').each(function(el){
                                if (el.get(\'name\') == \'set[site.get_rate.method]\')
                                {
                                    el.getParent().getParent().hide();
                                    el.hide();
                                    el.set(\'value\', \'\');
                                }
                            });
                        }
                    });
                }
            });
        });</script>';
        $this->page();
    }

    function licence(){
        $this->sidePanel();
        echo '<iframe width="100%" height="100%" src="'.constant('URL_VIEW_LICENCE').'" ></iframe>';
    }

    function imageset(){
        $ctl = new image_ctl_admin_manage($this->app);
        $ctl->imageset();
    }

}

