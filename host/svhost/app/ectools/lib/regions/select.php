<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
  

class ectools_regions_select
{
	/**
	 * 通过p_region_id，区域层级来得到地区的信息
	 * @params object app object
	 * @params string p_region_id
	 * @params array 参数数组 - depth
	 * @params string 当前激活的regions id
	 */
	public function get_area_select(&$app, $path, $params, $selected_id=null)
	{
		$params['depth'] = $params['depth']?$params['depth']:1;
        $html = '<select onchange="selectArea(this,this.value,'.($params['depth']+1).')">';
        $html.=__('<option value="_NULL_">请选择...</option>');

        if ($rows = $app->model('regions')->getList('*', array('region_grade' =>$params['depth'],'p_region_id'=>$path)))
		{
            foreach ($rows as $item)
			{
                if ($item['region_grade']<=$app->getConf('system.area_depth'))
				{
                    $selected = $selected_id == $item['region_id']?'selected="selected"':'';
					
                    if ($params['depth']<$app->getConf('system.area_depth'))
					{
                        $html.= '<option has_c="true" value="'.$item['region_id'].'" '.$selected.'>'.$item['local_name'].'</option>';
                    }
					else 
					{
                        $html.= '<option value="'.$item['region_id'].'" '.$selected.'>'.$item['local_name'].'</option>';
                    }
                }
				else
				{
                    $no = true;
                }
            }
			
            $html.='</select>';
            if($no) $html="";
			
            return $html;
        }
		else
		{
            return false;
        }
	}
}
