<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class ectools_view_input{

    function input_region($params){
        $app = app::get($params['app']);
        $regions = $app->model('regions');
        $package = kernel::service('ectools_regions.ectools_mdl_regions');
        $package = $package->key;
        if($params['required'] == 'true'){
            $req = ' vtype="area"';
        }else{
            $req = ' vtype='.$params['vtype'];
        }
		
		// region select instance.
		$objRegionsSelect = kernel::single('ectools_regions_select');
        if(!$params['value']){
            return '<span package="'.$package.'" class="span _x_ipt"'.$req.'><input '. ( $params['id']?' id="'.$params['id'].'"  ':'' ) .' type="hidden" name="'.$params['name'].'" />'.$objRegionsSelect->get_area_select($app,null,$params).'</span>';
        }else{
            list($package,$region_name,$region_id) = explode(':',$params['value']);
			$arr_region_name = explode("/", $region_name);
			$depth = count($arr_region_name);
            if(!is_numeric($region_id)){
                return '<span package="'.$package.'" class="span _x_ipt"'.$req.'><input type="hidden" name="'.$params['name'].'" />'.$objRegionsSelect->get_area_select($app,null,$params).'</span>';
            }else{
                $arr_regions = array();
                $ret = '';
                while($region_id && ($region = $regions->dump($region_id,'region_id,local_name,p_region_id'))){
					$params['depth'] = $depth--;
                    array_unshift($arr_regions,$region);
                    if($region_id = $region['p_region_id']){
                        $notice = "-";
                        $data = $objRegionsSelect->get_area_select($app,$region['p_region_id'],$params,$region['region_id']);
                        if(!$data){
                            $notice = "";
                        }
                        $ret = '<span class="x-region-child">&nbsp;'.$notice.'&nbsp'.$objRegionsSelect->get_area_select($app,$region['p_region_id'],$params,$region['region_id']).$ret.'</span>';
                    }else{
                        $ret = '<span package="'.$package.'" class="span _x_ipt"'.$req.'><input type="hidden" value="'.$params['value'].'" name="'.$params['name'].'" />'.$objRegionsSelect->get_area_select($app,null,$params,$region['region_id']).$ret.'</span>';
                    }
                }
                if(!$ret){
                    $ret = '<span package="'.$package.'" class="span _x_ipt"'.$req.'><input type="hidden" value="" name="'.$params['name'].'" />'.$objRegionsSelect->get_area_select($app,null,$params,$region['region_id']).'</span>';
                }
                return $ret;
            }
        }
    }
}
