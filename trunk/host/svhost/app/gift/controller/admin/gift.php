<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
//*******************************************************************
//  赠品控制器
//  $ 2010-04-07 16:27 $
//*******************************************************************
class gift_ctl_admin_gift extends desktop_controller{

    public $workground = 'gift_ctl_admin_gift';
    
    
    
    public function index(){
         $this->finder('gift_mdl_goods',array(
            'title'=>'赠品',
            'actions'=>array(
                            array('label'=>'添加赠品','icon'=>'add.gif','href'=>'index.php?app=gift&ctl=admin_gift&act=add', 'target'=>"_blank"),
                        ),//'finder_aliasname'=>'gift_mdl_goods','finder_cols'=>'cat_id',
            ));
    }


    /**
     * 添加新规则
     */
    public function add() {
        $this->get_subcat_list();
        
        //////////////////////////// 会员等级 //////////////////////////////
        $oMemberLevel = &$this->app->model('member_lv');
        $this->pagedata['member_level'] = $oMemberLevel->getList('*', array(), 0, 10000, 'member_lv_id ASC');
        $this->pagedata['image_dir'] = &app::get('image')->res_url;
        $this->singlepage('admin/gift/add.html');
    }

    /**
     * 修改规则
     *
     * @param int $rule_id
     */
    public function edit() {
        $this->begin(app::get('desktop')->router()->gen_url(array('app'=>'gift', 'ctl'=>'admin_gift', 'act'=>'index')));
        if(($id=$_GET['gift_id'])) {
            $arr_info = $this->app->model('goods')->dump($id,'*','default');
            if(!isset($arr_info)) {
                $this->end(false, '操作失败！信息为空！');
            } else {
                
                $this->pagedata['goods'] = $arr_info;
                $arr_member_lv_info = $this->app->model('member_ref')->getList('*', array('goods_id'=>$id));
                $arr_member_lv_info = $arr_member_lv_info[0];
                if(!empty($arr_member_lv_info['member_lv_ids'])) {
                    $arr_member_lv_info['member_lv_ids'] = explode(',', $arr_member_lv_info['member_lv_ids']);
                }
                $this->pagedata['memberlv'] = $arr_member_lv_info;
                //print_r($this->pagedata);exit;
                $this->add();
            }
        } else {
            $this->end(false, '赠品id不能为空！');
        }
    }
    
    
    public function get_subcat_list() {
        $objCat = &$this->app->model('cat');
        $cat_path = $objCat->getList();
        $this->pagedata['cat_path'] = $cat_path;
    }

    public function toAdd() {
        $aData = $this->_prepareGoodsData($_POST);

        $obj = $this->app->model("goods");

        
        if( strlen($aData['brief']) > 255 ){
        	$this->begin('index.php?app=gift&ctl=admin_gift&act=index');
            $this->end(false,__( '赠品介绍请不要超过255字节' ));
        }

        foreach( $aData as $key => $val ) {
            if( $val=='' ) unset($aData[$key]);
        }
       
        $flag = $obj->save($aData);

        header('Content-Type:text/jcmd; charset=utf-8');
        echo '{success:"'. ($flag ?  '成功: 操作成功!' : '失败: 操作失败！') .'",_:null,goods_id:"'.$aData['goods_id'].'"}';
    }
    
    function _prepareGoodsData( &$data ){
        
        $goods = $data['goods'];
        $goods['image_default_id'] = $data['image_default'];
        $images = array();
        foreach( (array)$goods['images'] as $imageId ){
            $images[] = array(
                'target_type'=>'goods',
                'image_id'=>$imageId,
                );
        }
        $goods['images'] = $images;
        
        unset($images);
        
        // 开始时间&结束时间
        foreach ($data['_DTIME_'] as $val) {
            $temp['from_time'][] = $val['from_time'];
            $temp['to_time'][] = $val['to_time'];
        }
        
        $goods['uptime'] = strtotime($data['from_time'].' '. implode(':', $temp['from_time']));
        $goods['downtime'] = strtotime($data['to_time'].' '. implode(':', $temp['from_time']));
        
        
        $goods['type']['type_id'] = 1;

        
        $goods['goods_type'] = 'gift';
        $goods['image_default_id'] = $data['image_default'];
        $goods['member_ref'] = array(
                                        'ifrecommend' => $data['memberlv']['ifrecommend'],
                                        'member_lv_ids' => ($data['memberlv']['member_lv_ids'] ? implode(',', $data['memberlv']['member_lv_ids']) : ''),
                                );
        
        !empty($goods['product'][0]['price']['price']['price']) or $goods['product'][0]['price']['price']['price'] = 0;
        !empty($goods['product'][0]['weight']) or $goods['product'][0]['weight']=0;

        return $goods;
    }

}
