<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class ectools_ctl_regionssetting extends desktop_controller{

    var $workground = 'ectools_ctl_regionssetting';

	public function __construct($app)
	{
		parent::__construct($app);
		header("cache-control: no-store, no-cache, must-revalidate");
	}
	
    function index(){
        $package = kernel::service('ectools_regions.ectools_mdl_regions');
        
        $this->pagedata['package'] = $package->setting;
        $this->pagedata['package']['name'] = $package->name;
        
        $this->pagedata['area_depth'] = $this->app->getConf('system.area_depth');
        
        $model = &$this->app->model('regions');
        $this->pagedata['package']['installed'] = $model->is_installed();

        $ectools_regions_ectools_mdl_regions = &app::get('base')->getConf('service.ectools_regions.ectools_mdl_regions');

        $o = &app::get('base')->model('services');
        $this->pagedata['ectools_regions_ectools_mdl_regions'] = &app::get('base')->getConf('site.ectools_regions.ectools_mdl_regions');
        
        foreach( $o->getList('content_path',array('content_type'=>'service','app_id'=>'ectools','disabled'=>'false','content_name'=>'ectools_regions.ectools_mdl_regions')) as $k => $v ){
            $listItem = array();
            $listItem['content_path'] = $v['content_path'];
            $oItem = kernel::single($v['content_path']);
            $listItem['name'] = $oItem->name;
            $this->pagedata['ectools_regions_ectools_mdl_regions_list'][] = $listItem;
        }
        $this->page('delivery/index.html');
    }

    function save_depth(){
        $this->begin('index.php?app=ectools&ctl=regionssetting&act=index');
        $rs = $this->app->setConf('system.area_depth',$_POST['area_depth']);
        $this->end($rs);
    }

    function install(){
        set_time_limit(0);
        $this->begin('index.php?app=ectools&ctl=regionssetting&act=index');
        $package = kernel::service('ectools_regions.ectools_mdl_regions');
        $rs = $package->install();
        $this->end($rs);
    }
    
    function setDefault(){
        set_time_limit(0);
        $this->begin('index.php?app=ectools&ctl=regionssetting&act=index');
        $model = &$this->app->model('regions');
        $model->clearOldData();
        $package = kernel::service('ectools_regions.ectools_mdl_regions');
        $rs = $package->install();
        $this->end($rs);
    }

    function save_regions_package(){
        $this->begin('index.php?app=ectools&ctl=regionssetting&act=index');
        $rs = app::get('base')->setConf('service.ectools_regions.ectools_mdl_regions' , $_POST['service']['ectools_regions.ectools_mdl_regions']);
        $this->end($rs);
    }
    
    function sel_region($path,$depth)
    {
        header('Content-type: text/html;charset=utf8');
        $region_select = kernel::single('ectools_regions_select');
        echo '&nbsp;-&nbsp;'.$region_select->get_area_select($this->app,$path,array('depth'=>$depth));
        //$regions = $this->app->model('regions');
        //echo '&nbsp;-&nbsp;'.$regions->get_area_select($path,array('depth'=>$depth));
    }
}
