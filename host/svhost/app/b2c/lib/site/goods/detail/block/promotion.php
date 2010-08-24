<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_site_goods_detail_block_promotion {
    
    public function __construct( $app ) {
        $this->app = $app;
    }
    
    public function get_blocks($params = array()) {
        $goods_id = $params['promotion']['goods_id'];
        if(!$goods_id) return false;

        $time = time();
        
        $aResult = $this->app->model('goods_promotion_ref')->getList('*', array('goods_id'=>$goods_id, 'from_time|sthan'=>$time, 'to_time|bthan'=>$time,'status'=>'true' ) );

        if(!$aResult) return false;
        $arr_member_info = kernel::single('b2c_frontpage')->get_current_member();
        if( empty($arr_member_info) ) $m_lv = -1;
        else $m_lv = $arr_member_info['member_lv'];

        foreach($aResult as $row) {
            if( empty($row['member_lv_ids']) ) continue;
            if( !in_array( $m_lv, explode(',',$row['member_lv_ids']) ) ) continue;
            $arr[] = $row;
            if( $row['stop_rules_processing'] ) break;
        }

        $return = array();
        foreach( (array)$arr as $row ) {
            $temp = is_array($row['action_solution']) ? $row['action_solution'] : @unserialize($row['action_solution']);
            foreach($temp as $key => $val) {
                $obj = kernel::single($key);
                $obj->setString($val);
                $return[] = ( $row['description'] ? $row['description'] : $obj->getString() );
            }
        }
        return implode('<br>', $return);;
    }
}
