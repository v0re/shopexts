<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
$goods_cat_viewer = '商品分类列表展示信息';
class b2c_site_goods_list_viewer{

    function __construct($app){
        $this->app = $app;
        $this->db = kernel::database();
    }

    function get_view($cat_id,$view,$type_id=null){
            if(!is_array($cat_id)){
                $cat_id=array($cat_id);
            }
            if($type_id){
                //$sqlString = 'SELECT t.props,t.schema_id,t.setting,t.type_id FROM sdb_b2c_goods_type t WHERE type_id ='.$type_id;
                $oGtype = &$this->app->model('goods_type');
                $row = $oGtype->dump( $type_id,'schema_id,setting,type_id' );
            }else{
                if($cat_id[0]){
                    $cat_id='('.implode($cat_id,' OR ').')';
                    $sqlString = 'SELECT c.cat_id,c.cat_name,c.tabs,c.addon,t.setting,t.schema_id,t.setting,t.type_id FROM sdb_b2c_goods_cat c
                        LEFT JOIN sdb_b2c_goods_type t ON c.type_id = t.type_id
                        WHERE cat_id in '.$cat_id;
                }
            }
            if($sqlString) $row = $this->db->selectrow($sqlString);

    //        if($row['spec']) $row['spec'] = unserialize($row['spec']);

            if($row['type_id']){
                $row['brand'] = $this->db->select('SELECT b.brand_id,b.brand_name,brand_url,brand_logo FROM sdb_b2c_type_brand t
                        LEFT JOIN sdb_b2c_brand b ON b.brand_id=t.brand_id
                        WHERE disabled="false" AND t.type_id='.$row['type_id'].' ORDER BY brand_order');
            }else{
                $oBrand = $this->app->model('brand');
                $row['brand'] = $oBrand->getList('*', '', 0, -1);
            }

            $dftList = array(
                    __('图文列表')=>'list',
                    __('橱窗')=>'grid',
                    __('文字')=>'text',
                );
            if(isset($row['setting']['list_tpl']) && is_array($row['setting']['list_tpl']))
                foreach($row['setting']['list_tpl'] as $k=>$tpl){
                    if(!in_array($tpl,$dftList)){
                        if(!file_exists(SCHEMA_DIR.$row['schema_id'].'/view/'.$tpl.'.html')){
                            unset($row['setting']['list_tpl'][$k]);
                        }
                    }
                }
            if(!isset($row['setting']['list_tpl']) || !is_array($row['setting']['list_tpl']) || count($row['setting']['list_tpl'])==0){
                $row['setting']['list_tpl'] = $dftList;
            }
            if($view=='index') $view = current($row['setting']['list_tpl']);

            if(in_array($view,$dftList)){
                    $row['tpl'] = '/site/gallery/type/'.$view.'.html';
            }else{
                $row['tpl'] = realpath(SCHEMA_DIR.$row['schema_id'].'/view/'.$view.'.html');
            }
            $row['dftView'] = $view;
            $row['setting']['list_tpl'][key($row['setting']['list_tpl'])] = 'index';
            return $row;
    }
}
