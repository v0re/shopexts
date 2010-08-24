<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
//*******************************************************************
//  订单促销规则控制器
//  $ 2010-04-07 16:27 $
//*******************************************************************
class b2c_ctl_admin_sales_order extends desktop_controller{

    public $workground = 'b2c_ctl_admin_sales_coupon';

    public function index(){
        $this->finder('b2c_mdl_sales_rule_order',array(
            'title'=>__('订单促销规则'),
            'actions'=>array(
                            array('label'=>'添加规则','href'=>'index.php?app=b2c&ctl=admin_sales_order&act=add','target'=>'_blank'),
                        ),
            'base_filter'=>array('rule_type'=>'N')
            ));
    }

    /**
     * 添加新规则
     */
    public function add() {
        $this->_editor();
    }

    /**
     * 修改规则
     *
     * @param int $rule_id
     */
    public function edit($rule_id) {
        $mOrderPromotion = $this->app->model("sales_rule_order");
        $aRule = $mOrderPromotion->dump($rule_id,'*','default');

        ///////////////////////////// 规则信息 ////////////////////////////
        $aRule['member_lv_ids'] = empty($aRule['member_lv_ids'])? null :explode(',',$aRule['member_lv_ids']);
        $aRule['conditions'] = empty($aRule['conditions'])? null : ($aRule['conditions']);
        $aRule['action_conditions'] = empty($aRule['conditions'])? null : ($aRule['action_conditions']);
        $aRule['action_solutions'] = empty($aRule['action_solutions'])? null : ($aRule['action_solutions']);
        $this->pagedata['rule'] = $aRule;

        ///////////////////////////// 过滤条件 ////////////////////////////
        $oSOP = kernel::single('b2c_sales_order_process');
        $aHtml = $oSOP->getTemplate($aRule['c_template'],$aRule);
        $this->_block($aHtml);

        ///////////////////////////// 优惠方案 ////////////////////////////
        $aRule['action_solution'] = empty($aRule['action_solution'])? null : ($aRule['action_solution']);
        $oSSP = kernel::single('b2c_sales_solution_process');
        $this->pagedata['solution_type'] = $oSSP->getType($aRule['action_solution'], $aRule['s_template']);
        $html = $oSSP->getTemplate($aRule['s_template'],$aRule['action_solution'], $this->pagedata['solution_type']);
        $this->pagedata['action_solution_name'] = $aRule['s_template'];
        $this->pagedata['action_solution'] = $html;

        $this->_editor( $rule_id );
    }

    /**
     * add & edit 公共部分
     *
     */
    private function _editor( $rule_id=0 ) {
        //排斥状态显示优先级项  默认加载 addtime:14:09 2010-8-19
        $time = time();
        $filter = array('from_time|sthan'=>$time, 'to_time|bthan'=>$time, 'status'=>'true', 'rule_type'=>'N');
        if( $rule_id ) $filter['rule_id|noequal'] = $rule_id;
        $arr = $this->app->model('sales_rule_order')->getList( 
                                                        'name,sort_order', 
                                                        $filter,
                                                        0,-1,'sort_order DESC'
                                                    );
        $this->pagedata['sales_list'] = $arr;
        $arr = null;
        //end  
        
        
        $this->pagedata['promotion_type'] = 'order'; // 规则类型 用于公用模板

        ////////////////////////////  模块  ////////////////////////////////
        $this->pagedata['sections'] = $this->_sections();

        //////////////////////////// 会员等级 //////////////////////////////
        $mMemberLevel = &$this->app->model('member_lv');
        $this->pagedata['member_level'] = $mMemberLevel->getList('member_lv_id,name', array(), 0, -1, 'member_lv_id ASC');

        //////////////////////////// 过滤条件模板 //////////////////////////////
        $oSOP = kernel::single('b2c_sales_order_process');
        $this->pagedata['pt_list'] = $oSOP->getTemplateList();

        //////////////////////////// 优惠方案模板 //////////////////////////////
        $oSSP = kernel::single('b2c_sales_solution_process');
        $this->pagedata['stpl_list'] = $oSSP->getTemplateList();

        header("Cache-Control:no-store");
        $this->singlepage('admin/sales/promotion/frame.html');
    }

    private function _sections() {
       return  array(
                 'basic'=> array(
                             'label'=>__('基本信息'),
                             'options'=>'',
                             'file'=>'admin/sales/promotion/basic.html',
                           ), // basic
               'conditions'=> array(
                                'label'=>__('过滤条件'),
                                'options'=>'',
                                'file'=>'admin/sales/promotion/conditions.html',
                              ), // conditions
               'solution'=> array(
                              'label'=>__('优惠方案'),
                              'options'=>'',
                              'file'=>'admin/sales/promotion/solution.html',
                            ), // solutions
             );
    }

    public function toAdd() {
        $this->begin('index.php?app=b2c&ctl=admin_sales_order&act=index');
        $aData = $this->_prepareRuleData($_POST);
        $mSRO = $this->app->model("sales_rule_order");
        $bResult = $mSRO->save($aData);

        $this->end($bResult,'操作成功');
    }

    /**
     * 这个MS可以放入model里处理
     */
    private function _prepareRuleData($aData) {
        ///////////////////////////// 基本信息 //////////////////////////////////
        $aResult = $aData['rule'];

        // 开始时间&结束时间
        foreach ($aData['_DTIME_'] as $val) {
            $temp['from_time'][] = $val['from_time'];
            $temp['to_time'][] = $val['to_time'];
        }
        $aResult['from_time'] = strtotime($aData['from_time'].' '. implode(':', $temp['from_time']));
        $aResult['to_time'] = strtotime($aData['to_time'].' '. implode(':', $temp['from_time']));
        
        // 会员等级
        $aResult['member_lv_ids'] = empty($aResult['member_lv_ids'])? null : implode(',',$aResult['member_lv_ids']);

        // 创建时间 (修改时不处理)
        if(empty($aResult['rule_id'])) $aResult['create_time'] = time();

        ////////////////////////////// 过滤规则 //////////////////////////////////
        $aResult['conditions'] = empty($aData['conditions'])? ( array('type'=>'combine','conditions'=>array())) : ($aData['conditions']);
        //if(is_null($aResult['conditions'])) $aResult['c_template'] = null;
        $aResult['action_conditions'] = empty($aData['action_conditions'])? ( array('type'=>'product_combine','conditions'=>array())) : ($aData['action_conditions']);

        ////////////////////////////// 优惠方案 //////////////////////////////////
        $aResult['action_solution'] = empty($aData['action_solution'])? null : ($aData['action_solution']);
        //$aResult['action_solution'] = empty($aData['action_solution'])? null : ($aData['action_solution']);
        
        if( empty($aResult['sort_order']) && $aResult['sort_order']!==0 )
            $aResult['sort_order'] = 1;

        return $aResult;
    }

    private function _block($aHtml) {
        if((empty($aHtml)) || ( is_array($aHtml) && (empty($aHtml['conditions']) || empty($aHtml['action_conditions']))) ) die("<b align=\"center\">".__("模板生成失败")."</b>");
        if(is_array($aHtml)) {
            $this->pagedata['conditions'] = $aHtml['conditions'];
            $this->pagedata['action_conditions'] = $aHtml['action_conditions'];
            $this->pagedata['multi_conditions'] = true;
        } else {
            $this->pagedata['multi_conditions'] = false;
            $this->pagedata['conditions'] = $aHtml;
        }
    }

    /**
     * 获取指定模板
     */
    public function template(){
        $oSOP = kernel::single('b2c_sales_order_process');
        // 只载入模板 有值的话也是没什么用的
        $aHtml = $oSOP->getTemplate($_POST['template']);

        $this->_block($aHtml);
        $this->display('admin/sales/promotion/order_rule.html');
    }



    /**
     * 用于优惠方案获取模板
     */
    public function solution() {
        $oSSP = kernel::single('b2c_sales_solution_process');
        // 只载入模板 这里只是选择模板
        $html = $oSSP->getTemplate($_POST['template'], array(), $_POST['type']);
        if(empty($html)) die("<b align=\"center\">".__("模板生成失败")."</b>");

        $this->pagedata['conditions'] = $html;
        $this->display('admin/sales/promotion/goods_rule.html');
    }



    /**
     * 选择条件
     *
     */
    public function conditions(){
        // 传入的值为空的处理
        if(empty($_POST)) exit;

        // vpath
        $_POST['path'] .= '[conditions]['.$_POST['position'].']';
        $_POST['level'] += 1;

        $oSOP = kernel::single('b2c_sales_order_process');
        echo $oSOP->makeCondition($_POST);
    }
}
