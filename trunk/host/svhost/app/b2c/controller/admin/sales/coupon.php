<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_sales_coupon extends desktop_controller{

    var $workground = 'b2c.workground.sale';
    var $cup = array(0=>"A",1=>"B");

    function index() {
        $this->finder('b2c_mdl_coupons', array(
                'title'=>'优惠券',
                'actions'=>array(
                                array('label'=>'添加优惠券','href'=>'index.php?app=b2c&ctl=admin_sales_coupon&act=add','target'=>'_blank'),
                            )
                ));
    }
    /**
     * 添加coupon
     */
    function add() {
        $this->pagedata['rule']['sort_order'] = 10000;
        $this->_editor();
    }

    /**
     * 修改coupon
     */
    function edit($coupon_id) {
        //////////////////////////// 优惠劵信息 //////////////////////////////
        $mCoupon = $this->app->model('coupons');
        $aCoupon = $mCoupon->dump($coupon_id);
        if(empty($aCoupon)) $this->splash('fail','index.php?app=b2c&ctl=admin_sales_coupon',__('数据错误'));
        $aCoupon['cpns_prefix'] = substr($aCoupon['cpns_prefix'],1);
        $this->pagedata['coupon'] = $aCoupon;

        ////////////////////////// 订单促销规则信息 ///////////////////////////
        $mSRO = $this->app->model('sales_rule_order');
        $aRule = $mSRO->dump($aCoupon['rule']['rule_id']);

        $aRule['member_lv_ids'] = empty($aRule['member_lv_ids'])? null :explode(',',$aRule['member_lv_ids']);
        $aRule['conditions'] = empty($aRule['conditions'])? null : $aRule['conditions'];
        $aRule['conditions'] = is_null($aRule['conditions'])? null : $aRule['conditions']['conditions'][1];
        $aRule['action_conditions'] = empty($aRule['conditions'])? null : ($aRule['action_conditions']);
        $aRule['action_solutions'] = empty($aRule['action_solutions'])? null : ($aRule['action_solutions']);
        $this->pagedata['rule'] = $aRule;

        ///////////////////////////// 过滤条件 ///////////////////////////////
        $oSOP = kernel::single('b2c_sales_order_process');
        $aHtml = $oSOP->getTemplate($aRule['c_template'],$aRule);
        if((empty($aHtml)) || ( is_array($aHtml) && (empty($aHtml['conditions']) || empty($aHtml['action_conditions']))) ) {
            $this->pagedata['multi_conditions'] = false;
            $this->pagedata['conditions'] = "<b align=\"center\">".__("模板生成失败")."</b>";
        }
        if(is_array($aHtml)) {
            $this->pagedata['conditions'] = $aHtml['conditions'];
            $this->pagedata['action_conditions'] = $aHtml['action_conditions'];
            $this->pagedata['multi_conditions'] = true;
        } else {
            $this->pagedata['multi_conditions'] = false;
            $this->pagedata['conditions'] = $aHtml;
        }

        ///////////////////////////// 优惠方案 ///////////////////////////////
        $aRule['action_solution'] = empty($aRule['action_solution'])? null : ($aRule['action_solution']);
        $oSSP = kernel::single('b2c_sales_solution_process');
        $this->pagedata['solution_type'] = $oSSP->getType($aRule['action_solution'], $aRule['s_template']);
        $this->pagedata['action_solution_name'] = $aRule['s_template'];
        
        $html = $oSSP->getTemplate($aRule['s_template'],$aRule['action_solution'], $this->pagedata['solution_type']);
        $this->pagedata['action_solution'] = $html;
        $this->_editor();
    }

    /**
     * 添加修改coupon共用部分
     */
    function _editor(){
        //////////////////////////// 会员等级 //////////////////////////////
        $mMemberLevel = &$this->app->model('member_lv');
        $this->pagedata['member_level'] = $mMemberLevel->getList('member_lv_id,name', array(), 0, -1, 'member_lv_id ASC');

        //////////////////////////// 过滤条件模板 //////////////////////////////
        $this->pagedata['promotion_type'] = 'order'; // 促销规则过滤条件模板类型
        $oSOP = kernel::single('b2c_sales_order_process');
        $this->pagedata['pt_list'] = $oSOP->getTemplateList();

        //////////////////////////// 优惠方案模板 //////////////////////////////
        $oSSP = kernel::single('b2c_sales_solution_process');
        $this->pagedata['stpl_list'] = $oSSP->getTemplateList();
        
        
        header("Cache-Control:no-store");
        $this->singlepage('admin/sales/coupon/frame.html');
    }

    /**
     * 添加&修改(post)
     *
     */
    function toAdd() {
        $this->begin('index.php?app=b2c&ctl=admin_sales_coupon');
        $aData = $this->_prepareData($_POST);

        /////////////////////////////  保存促销规则  ///////////////////////////////
        $aRule = $aData['rule'];
        $mSRO = $this->app->model('sales_rule_order');
        $mSRO->save($aRule);
        //////////////////////////////  保存优惠劵 ////////////////////////////////
        $aCoupon = $aData['coupon'];
        $aCoupon['rule']['rule_id'] = $aRule['rule_id'];
        $oCoupon = $this->app->model('coupons');

        $this->end($oCoupon->save($aCoupon),'操作成功');
    }

    function _prepareData($aData) {
        $this->_checkData($aData);
        $aResult = array();
        ///////////////////////////////// coupon ///////////////////////////////////
        $aResult['coupon'] = $aData['coupon'];
        if(isset($aResult['coupon']['cpns_prefix'])) { // 修改的时候这个是没有的 编辑的话只显示不提交到这里
            $aResult['coupon']['cpns_prefix'] = $this->cup[$aData['coupon']['cpns_type']].$aData['coupon']['cpns_prefix'];
        } else {
            $arr_coupon_info = $this->app->model('coupons')->dump($aResult['coupon']['cpns_id']);
            $aResult['coupon']['cpns_prefix'] = $arr_coupon_info['cpns_prefix'];
        }


        ///////////////////////////////// order rule ///////////////////////////////////
        $aResult['rule'] = $aData['rule'];
        $aResult['rule']['rule_id'] = $aData['coupon']['rule_id'];

        // 启用状态
        $aResult['rule']['status'] = empty($aData['coupon']['cpns_status'])?'false' : 'true'; // 和优惠劵的状态一致
        $aResult['rule']['rule_type'] = 'C';            // 规则类型
        $aResult['rule']['name'] = __("优惠劵规则-").$aData['coupon']['cpns_name']; // 名称

        // 开始时间&结束时间
        foreach ($aData['_DTIME_'] as $val) {
            $temp['from_time'][] = $val['from_time'];
            $temp['to_time'][] = $val['to_time'];
        }
        $aResult['rule']['from_time'] = strtotime($aData['from_time'].' '. implode(':', $temp['from_time']));
        $aResult['rule']['to_time'] = strtotime($aData['to_time'].' '. implode(':', $temp['from_time']));

        // 会员等级
        $aResult['rule']['member_lv_ids'] = empty($aData['rule']['member_lv_ids'])? null : implode(',',$aData['rule']['member_lv_ids']);

        // 创建时间 (修改时不处理)
        if(empty($aResult['rule']['rule_id'])) $aResult['rule']['create_time'] = time();

        ////////////////////////////// 过滤规则 //////////////////////////////////
        $aResult['rule']['conditions'] = empty($aData['conditions'])? array('type'=>'b2c_sales_order_aggregator_combine','conditions'=>array()) : $aData['conditions'];
        $aResult['rule']['conditions'] = array(
                                            'type' => 'b2c_sales_order_aggregator_combine',
                                            'aggregator' => 'all',
                                            'value' => 1,
                                            'conditions' => array(
                                                               array( // 0
                                                                     'type' => 'b2c_sales_order_item_coupon',
                                                                     'attribute' => 'coupon',
                                                                     'operator' => '=',
                                                                     'value' => $aResult['coupon']['cpns_prefix']
                                                               ),
                                                               $aResult['rule']['conditions'], // 1 将订单的'conditions'放到这里
                                             )
                                         );

        $aResult['rule']['action_conditions'] = empty($aData['action_conditions'])? array('type'=>'b2c_sales_order_aggregator_item','conditions'=>array()) : $aData['action_conditions'];

        ////////////////////////////// 优惠方案 //////////////////////////////////
        $aResult['rule']['action_solution'] = empty($aData['action_solution'])? array() : ($aData['action_solution']);
        //$aResult['rule']['sort_order'] = 10000;

        return $aResult;
    }

    /**
     * 检测数据
     */
    function _checkData($aData) {
        // POST数据为空
        if(empty($aData)) $this->splash('fail','index.php?app=b2c&ctl=admin_sales_coupon&act=add',__('数据错误'));

        // 添加的时候检测是否已存在相同的coupon 这个可以放在第一步的ajax验证中处理...
        $oCoupon = $this->app->model('coupons');
        if(empty($aData['coupon']['cpns_id'])) {
            if($oCoupon->checkPrefix($this->cup[$aData['coupon']['cpns_type']].$aData['coupon']['cpns_prefix'])){
                trigger_error(__('优惠劵号码已经存在'),E_USER_ERROR);
                //$this->begin('fail','index.php?app=b2c&ctl=admin_sales_coupon&act=add',__('优惠劵号码已经存在'));
            }
        }
    }

    /*
     * 下载优惠券
     */
    function download($cpnsId,$nums){
        $exporter = kernel::single("b2c_sales_csv");
        $mCoupon = $this->app->model('coupons');
        
        if ($list = $mCoupon->downloadCoupon($cpnsId,$nums)) {
            $exporter->download(__('优惠券代码'),'coupon',$nums, $list);
        }else{
            header("Content-type: text/html; charset=UTF-8");
            echo __('<script>alert("当前优惠券未发布/时间未到,暂时不能下载")</script>');
        }
        //*/
    }
    
    
    

}
?>
