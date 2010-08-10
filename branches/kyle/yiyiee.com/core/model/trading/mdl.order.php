<?php
define('OLIST_ACT',0);
define('OLIST_ALL',1);
define('OLIST_STARRED',2);
define('ORDER_PRINT_CART',1); //购物清单
define('ORDER_PRINT_SHEET',2); //配货单
define('ORDER_PRINT_MERGE',4); //联合打印
define('ORDER_PRINT_DLY',8); //快递单打印
include_once('delivercorp.php');
include_once('shopObject.php');
class mdl_order extends shopObject{

    var $idColumn = 'order_id';
    var $textColumn = 'order_id';
    var $defaultCols = 'order_id,order_tips,createtime,total_amount,ship_name,pay_status,ship_status,shipping,print_status,payment,member_id,refer_id';
    var $appendCols = 'pay_status,ship_status,status,mark_type,is_has_remote_pdts';
    var $adminCtl = 'order/order';
    var $defaultOrder = array('createtime','DESC');
    var $tableName='sdb_orders';
    var $hasTag = true;

    function searchOptions(){
        return array(
                'order_id'=>'订单号',
                'bn'=>'货号',
                'goods_name'=>'商品名称',
                'logi_no'=>'物流单号',
                'member_name'=>'会员用户名',
                'ship_name'=>'收货人姓名',
                'ship_addr'=>'收货人地址',
                'ship_tel'=>'收货人电话',
                'mark_text'=>'订单备注'
            );
    }

    function getColumns(){
        return array(
            'order_id'=>array('label'=>'订单号','class'=>'span-3', 'primary' => true ),
            'order_tips'=>array('label'=>'提示','class'=>'span-1','sql'=>'order_id' , 'type'=>'order_tips' ),
            'createtime'=>array('label'=>'下单日期','class'=>'span-3','type'=>'time:SDATE_STIME'),
            'acttime'=>array('label'=>'最后更新','class'=>'span-3','type'=>'time:SDATE_STIME'),
            'print_status'=>array('label'=>'打印','class'=>'span-4','type'=>'print_status'),
            'total_amount'=>array('label'=>'订单总额','class'=>'span-2','type'=>'money'),
            'ship_name'=>array('label'=>'收货人','class'=>'span-2','type'=>'ship_name'),
            'pay_status'=>array('label'=>'付款状态','class'=>'span-2','type'=>'pay_status'),
            'ship_status'=>array('label'=>'发货状态','class'=>'span-2','type'=>'ship_status'),
            'shipping'=>array('label'=>'配送方式','class'=>'span-2'),
            'cost_freight'=>array('label'=>'配送费用','class'=>'span-2','type'=>'money'),
            'payment'=>array('label'=>'支付方式','class'=>'span-2','type'=>'pp'),
            'member_id'=>array('label'=>'会员用户名','class'=>'span-2','type'=>'object:member'),
            'status'=>array('label'=>'订单状态','class'=>'span-2','type'=>'status'),
            'confirm'=>array('label'=>'确认状态','class'=>'span-2','type'=>'confirm'),
            'ship_tel'=>array('label'=>'收货人电话','class'=>'span-2'),
            'ship_area'=>array('label'=>'收货地区','class'=>'span-5','type'=>'region'),
            'ship_addr'=>array('label'=>'收货地址','class'=>'span-5'),
            'refer_id'=>array('label'=>'订单来源ID','class'=>'span-2','type'=>'refer'),
            'refer_url'=>array('label'=>'订单来源网址','class'=>'span-4','type'=>'refer'),
            'mark_text'=>array('label'=>'订单备注','class'=>'span-4','type'=>'mark_text','modifier'=>'row'),
            'mark_type'=>array('label'=>'订单备注图标','class'=>'span-4'),
        );
    }

    /**
     * is_highlight
     * 高亮finder的行
     *
     * @param mixed $row
     * @access public
     * @return void
     */
    function getLastestOrder($rowlimit=10){

        $sql='select order_id,ship_name,createtime,total_amount,sex from sdb_orders od
              Left Join sdb_members me on od.member_id =me.member_id where od.ship_status=1 and od.pay_status =1 order by od.createtime desc limit 0,'.intval($rowlimit);

        return $this->db->select($sql);
    }
    function is_highlight($row){
        return $row['status'] == 'active' && $row['pay_status'] == 0 && $row['ship_status'] == 0;
    }

    function modifier_order_tips(&$rows){
        foreach( $rows as $rk=>$rv) {
            $rows[$rk] = '';
        }

        $msg = $this->db->select('SELECT rel_order AS order_id ,msg_type, count(*) AS c FROM sdb_message WHERE unread = "0" AND rel_order IN ('.implode(',', array_keys($rows)).') GROUP BY rel_order , msg_type ORDER BY msg_type');

        foreach( $msg as $mkey=>$mval) {
            if( $mval['c'] > 0 ){
                $alertjs = 'void function(tid){
                    var turnToOrderMsg = function(oid){
                        var msgDom = $E(\'div[item-id=\'+oid+\']\').getElement(\'span[url^=index.php?ctl=order/order&act=detail_msg]\');
                        var msgDetail = $E(\'div[item-id=\'+oid+\']\').getElement(\'div[class=infoPanel]\');
                        if(!msgDom || ( msgDetail && msgDetail.style.display == \'none\' )){
                            var timeFunc = function(toid){setTimeout(function(toid){turnToOrderMsg(toid);},200);};
                            timeFunc(oid);
                        }else{
                           msgDom.onclick();
                        }
                    };
                    turnToOrderMsg(tid);


                    }($(this).getParent(\'div[item-id]\').get(\'item-id\'));';
                if( $mval['msg_type'] == 'payment' )
                    $rows[$mval['order_id']] = $rows[$mval['order_id']].' <img src="./images/icon_payment.gif" width="15" height="15"  onclick="'.$alertjs.'"/> ';
                else if( $mval['msg_type'] == 'default')
                    $rows[$mval['order_id']] = $rows[$mval['order_id']].' <img src="./images/icon_leave.gif" width="15" height="15" onclick="'.$alertjs.'"/> ';

            }
        }

        $remark=$this->db->select('SELECT order_id,mark_text FROM sdb_orders WHERE order_id IN ('.implode(',', array_keys($rows)).') ');
        /*
        foreach($remark as $k=>$v){
            if($remark[$k]['mark_text']){
                $rows[$v['order_id']] = $rows[$v['order_id']].'<span class=\'memberinfo\' title=\''.$remark[$k]['mark_text'].'\'>&nbsp;</span>';
            }
        }*/
        return $rows['order_tips'];

    }

    function modifier_pp(&$rows){
        $status = array(0=>'线下支付',
                    -1=>'货到付款' );
        foreach($rows as $k => $v){
            if($v < 1) $rows[$k] = $status[$v];
        }
        foreach($this->db->select('SELECT id,custom_name FROM sdb_payment_cfg WHERE id IN ('.implode(',', array_keys($rows)).')') as $r){
            $rows[$r['id']] = $r['custom_name'];
        }
    }

    function modifier_ship_name(&$rows){
        foreach($rows as $k => $v){
            $rows[$k] = htmlspecialchars($rows[$k]);
        }
    }

    function modifier_print_status(&$rows){
        foreach($rows as $k => $v){
            $rows[$k] = '<img src="images/print-icon.gif" width="16px" height="16px" style="margin:3px 0 0 2px" />';
            $rows[$k] .= '&nbsp;<span class="'.((ORDER_PRINT_CART==(ORDER_PRINT_CART & $v))?'p_prted':'p_prt').'" onclick="orderPrint(this,'.ORDER_PRINT_CART.')" title="购物清单打印">购</span>';
            $rows[$k] .= '&nbsp;<span class="'.((ORDER_PRINT_SHEET==(ORDER_PRINT_SHEET & $v))?'p_prted':'p_prt').'" onclick="orderPrint(this,'.ORDER_PRINT_SHEET.')" title="配货单打印">配</span>';
            $rows[$k] .= '&nbsp;<span class="'.((ORDER_PRINT_MERGE==(ORDER_PRINT_MERGE & $v))?'p_prted':'p_prt').'" onclick="orderPrint(this,'.ORDER_PRINT_MERGE.')" title="联合打印">合</span>';
            $rows[$k] .= '&nbsp;<span class="'.((ORDER_PRINT_DLY==(ORDER_PRINT_DLY & $v))?'p_prted':'p_prt').'" onclick="orderPrint(this,'.ORDER_PRINT_DLY.')" title="快递单打印">递</span>';
        }
    }

    function exporter_print_status(&$rows){;}

    function modifier_pay_status(&$rows){
        $status = array(0=>'未支付',
                    1=>'已支付',
                    2=>'已付款至担保方',
                    3=>'部分付款',
                    4=>'部分退款',
                    5=>'已退款' );
        foreach($rows as $k => $v){
            $rows[$k] = $status[$v];
        }
    }

    function modifier_ship_status(&$rows){
        $status = array(0=>'未发货',
                    1=>'已发货',
                    2=>'部分发货',
                    3=>'部分退货',
                    4=>'已退货' );
        foreach($rows as $k => $v){
            $rows[$k] = $status[$v];
        }
    }

    function modifier_refer(&$rows){
        ;
    }

    function modifier_cuttxt(&$rows){
        foreach($rows as $k => $v){
            $rows[$k] = '<span class="memberinfo" title="'.$rows[$k].'">&nbsp;</span>';
        }
    }

    function modifier_mark_text($row){
        if($row['mark_text']!=''){
            return "<span  title=\"".$row['mark_text']."\"><img src=\"../statics/remark_icons/".$row['mark_type'].".gif\"></span>";
        }
    }

    function getFilter($p){
        $delivery = $this->system->loadModel('trading/delivery');
        $return['areas'] = $delivery->getDlAreaList();
        $return['delivery'] = $delivery->getDlTypeList();
        $payment = $this->system->loadModel('trading/payment');
        $return['payment'] = $payment->getMethods();
        return $return;
    }

    function getViews(){
        return array(
            '未付款'=>array('pay_status'=>array(0,3),'ship_status'=>array('_ANY_'),'status'=>'_ANY_'),
            '已付款待发货'=>array('pay_status'=>array(1),'ship_status'=>array(0),'status'=>'_ANY_'),
            '处理中订单'=>array('pay_status'=>array('_ANY_'),'ship_status'=>array('_ANY_'),'status'=>'active'),
            '已退款订单'=>array('pay_status'=>array(5),'ship_status'=>array('_ANY_'),'status'=>'_ANY_')
        );
    }

    function _filter($filter){
        $where=array(1);
        if(is_array($filter['status'])){
            foreach($filter['status'] as $state){
                if($state!='_ANY_'){
                    $aState[] = $state;
                }
                if(count($aState)>0){
                    $where[] = 'status in (\''.implode($aState,'\',\'').'\')';
                }
            }
            unset($filter['status']);
        }

        if(is_array($filter['pay_status'])){
            foreach($filter['pay_status'] as $paystate){
                if($paystate!=='_ANY_'){
                    $aPaystate[] = intval($paystate);
                }
            }
            if(count($aPaystate) > 0){
                $where[] = 'pay_status IN ('.implode($aPaystate,',').')';
            }
            unset($filter['pay_status']);
        }

        if(is_array($filter['ship_status'])){
            foreach($filter['ship_status'] as $shipstate){
                if($shipstate!=='_ANY_'){
                    $aShipstate[] = intval($shipstate);
                }
            }
            if(count($aShipstate)>0){
                $where[] = 'ship_status IN ('.implode($aShipstate,',').')';
            }
            unset($filter['ship_status']);
        }

        if(is_array($filter['areas'])){
            foreach($filter['areas'] as $area){
                if($area!='_ANY_'){
                    $aArea[] = 'shipping_area = "'.$area.'"';
                }
            }
            if(count($aArea)>0){
                $where[] = '('.implode($aArea,' OR ').')';
            }
            unset($filter['areas']);
        }

        if(is_array($filter['delivery'])){
            foreach($filter['delivery'] as $delivery){
                if($delivery!='_ANY_'){
                    $aDelivery[] = 'shipping_id = '.intval($delivery);
                }
            }
            if(count($aDelivery)>0){
                $where[] = '('.implode($aDelivery,' OR ').')';
            }
            unset($filter['delivery']);
        }

        if(is_array($filter['payment'])){
            foreach($filter['payment'] as $payment){
                if($payment!='_ANY_'){
                    $aPayment[] = 'payment = '.intval($payment);
                }
            }
            if(count($aPayment)>0){
                $where[] = '('.implode($aPayment,' OR ').')';
            }
            unset($filter['payment']);
        }

        if(isset($filter['order_id'])){
            if(is_array($filter['order_id'])){
                if(!isset($filter['order_id'][1])){
                    $where[] = 'order_id LIKE \''.$filter['order_id'][0].'%\'';
                }else{
                    $aOrder = array();
                    foreach($filter['order_id'] as $order_id){
                        $aOrder[] = 'order_id="'.$order_id.'"';
                    }
                    $where[] = '('.implode(' OR ',$aOrder).')';
                    unset($aOrder);
                }
            }else{
                $where[] = 'order_id LIKE \''.$filter['order_id'].'%\'';
            }
            unset($filter['order_id']);
        }


        if(isset($filter['bn']) && $filter['bn'] !== ''){
            $aId = array(0);
            foreach($this->db->select('SELECT order_id FROM sdb_order_items WHERE bn LIKE \''.$filter['bn'].'%\'') as $rows){
                $aId[] = 'order_id = \''.$rows['order_id'].'\'';
            }
            $where[] = '('.implode(' OR ', $aId).')';
            unset($filter['bn']);
        }

        if(isset($filter['goods_name']) && $filter['goods_name'] !== ''){
            $aId = array(0);
            foreach($this->db->select('SELECT order_id FROM sdb_order_items WHERE name LIKE \'%'.$filter['goods_name'].'%\'') as $rows){
                $aId[] = 'order_id = \''.$rows['order_id'].'\'';
            }
            $where[] = '('.implode(' OR ', $aId).')';
            unset($filter['goods_name']);
        }

        if(isset($filter['logi_no']) && $filter['logi_no'] !== ''){
            $objShipping = $this->system->loadModel('trading/shipping');
            $aOrder = $objShipping->getOrdersByLogino($filter['logi_no']);
            $where[] = 'order_id IN (\''.implode("','",$aOrder).'\')';
        }

        if(isset($filter['ship_name'])){

            $where[] = 'ship_name LIKE \'%'.$filter['ship_name'].'%\'';
            unset($filter['ship_name']);
        }

        if(isset($filter['createtime'])){
            $where[] = 'createtime > \''.$filter['createtime'].'\'';
            unset($filter['createtime']);
        }

        if(isset($filter['ship_addr'])){
            $where[] = 'ship_addr LIKE \'%'.$filter['ship_addr'].'%\'';
            unset($filter['ship_addr']);
        }

        if(isset($filter['ship_tel'])){
            $where[] = 'ship_tel LIKE \'%'.$filter['ship_tel'].'%\'';
            unset($filter['ship_tel']);
        }

        if(isset($filter['member_name']) && $filter['member_name'] !== ''){
            $aId = array(0);
            foreach($this->db->select('SELECT member_id FROM sdb_members WHERE uname = \''.$filter['member_name'].'\'') as $rows){
                $aId[] = $rows['member_id'];
            }
            $where[] = 'member_id IN ('.implode(',', $aId).')';
            unset($filter['member_name']);
        }

        if(isset($filter['return_order_id']) && $filter['return_order_id'] != ""){
            $where[] = 'order_id like "'.$filter['return_order_id'].'%"';
            unset($filter['return_order_id']);
        }

        if(isset($filter['mark_text'])){
            $where[] = 'mark_text LIKE \'%'.$filter['mark_text'].'%\'';
            unset($filter['mark_text']);
        }
        return parent::_filter($filter).' and '.implode($where,' AND ');
    }

    function load($order_id){
        if($row = $this->db->selectrow('SELECT * from sdb_orders where order_id ='.$order_id)){
            $this->update_last_modify($order_id);
            $this->_info['order_id'] = $row['order_id'];        //会员id
            $this->_info['member_id'] = $row['member_id'];        //会员id
            $this->_info['confirm'] = $row['confirm']=='Y';
            $this->_info['status'] = $row['status'];        //状态：active:活动,  dead:死单, finish:完成
            $this->_info['pay_status'] = $row['pay_status'];        //是否支付(0 未支付 1 已支付 2 处理中 3 部分付款 4 部分退款 5 全额退款)
            $this->_info['ship_status'] = $row['ship_status'];        //发货状态：0未发货，1已发货，2部分发货，3部分退货，4已退货
            $this->_info['user_status'] = $row['user_status'];        //用户交互状态(null:无；payed:已支付；shipped:已收到货)
            $this->_info['is_delivery'] = $row['is_delivery'];        //是否实体配送Y;N
            $this->_info['weight'] = $row['weight'];        //重量
            $this->_info['tostr'] = $row['tostr'];        //文字描述
            $this->_info['acttime'] = $row['acttime'];        //活动时间
            $this->_info['createtime'] = $row['createtime'];        //创建时间
            $this->_info['itemnum'] = $row['itemnum'];        //货物总数量
            $this->_info['ip'] = $row['ip'];        //下单ip地址
            $this->_info['currency'] = $row['currency'];        //货币
            $this->_info['cur_rate'] = $row['cur_rate'];        //货币汇率
            $this->_info['payment'] = $row['payment'];        //支付方式
            $this->_info['memo'] = $row['memo'];        //订单备注
            $this->_info['receiver']['name'] = $row['ship_name'];        //收货人姓名
            $this->_info['receiver']['area'] = $row['ship_area'];        //收货人地qu
            $this->_info['receiver']['addr'] = $row['ship_addr'];        //收货人地址
            $this->_info['receiver']['zip'] = $row['ship_zip'];        //收货人邮编
            $this->_info['receiver']['tel'] = $row['ship_tel'];        //收货人固定电话
            $this->_info['receiver']['email'] = $row['ship_email'];        //收货人Email
            $this->_info['receiver']['mobile'] = $row['ship_mobile'];        //收货人移动电话
            $this->_info['shipping']['id'] = $row['shipping_id'];        //配送方式
            $this->_info['shipping']['time'] = $row['ship_time'];        //要求到货时间
            $this->_info['shipping']['method'] = $row['shipping'];        //配送方式
            $this->_info['shipping']['cost'] = $row['cost_freight'];        //配送价格
            $this->_info['shipping']['is_protect'] = $row['is_protect'];        //保价价格
            $this->_info['shipping']['protect'] = $row['cost_protect'];        //保价价格
            $this->_info['shipping']['area'] = $row['shipping_area'];        //配送地区
            $this->_info['basic']['totalPrice'] = $row['cost_item'];        //商品价格
            $this->_info['is_tax'] = $row['is_tax'];        //税 (仅商品)
            $this->_info['cost_tax'] = $row['cost_tax'];        //税 (仅商品)
            $this->_info['tax_company'] = $row['tax_company'];        //税 (仅商品)
            $this->_info['use_pmt'] = $row['use_pmt'];
            $this->_info['discount'] = $row['discount'];
            $this->_info['use_pmt'] = $row['use_pmt'];
            $this->_info['score_g'] = $row['score_g'];
            $this->_info['score_u'] = $row['score_u'];
            $this->_info['advance'] = $row['advance'];        //返点到预存款
            $this->_info['amount']['total'] = $row['total_amount'];
            $this->_info['amount']['final'] = $row['final_amount'];
            $this->_info['amount']['payed'] = $row['payed'];        //现已支付金额：应等于payments表本order所有money的和
            $this->_info['amount']['cost_payment'] = $row['cost_payment'];
            $this->_info['amount']['pmt_amount'] = $row['pmt_amount'];
            $this->_info['pay_extend'] = $row['extend'];
            $this->_info['last_change_time']=$row['last_change_time'];
            $this->_inDatabase = true;

            switch($row['payment']){
                case 0:
                $this->_info['paymethod'] = __('线下支付');
                $this->_info['paytype'] = 'OFFLINE';
                break;
                case -1:
                $this->_info['paymethod'] = __('货到付款');
                $this->_info['paytype'] = 'PAYAFT';
                break;
                default:
                $payment = $this->system->loadModel('trading/payment');
                $aPayment = $payment->getPaymentById($row['payment']);
                $this->_info['paymethod'] = $aPayment['custom_name'];
                $this->_info['paytype'] = $aPayment['pay_type'];
                break;
            }

            $oCur = $this->system->loadModel('system/cur');
            $aCur = $oCur->getSysCur();
            $this->_info['cur_name'] = $aCur[$row['currency']];

            return $this->_info;
        }return false;
    }

    function getFieldById($orderId, $aField=array('*')){
        return $this->db->selectrow("SELECT ".implode(",", $aField)." FROM sdb_orders WHERE order_id='{$orderId}'");
    }

    function sumOrder($member_id){
        return $this->db->selectrow("SELECT SUM(payed) AS sum_pay, COUNT(order_id) AS sum FROM sdb_orders WHERE status = 'finish' AND member_id = ".$member_id." GROUP BY member_id");
    }

    //检测实体商品配送信息的合法性
    function checkOrderDelivery($aGoods, &$aDelivery, $otherPhysical=false, $is_member=false){
        if(count($aGoods) == 0 && !$otherPhysical) return 'N';
        $gtype = $this->system->loadModel('goods/gtype');
        $deliverInfo = $gtype->deliveryInfo($aGoods);
        if($deliverInfo['physical'] || $otherPhysical){
            if(trim($aDelivery['ship_name']) == ''
                || trim($aDelivery['ship_area']) == ''
                || (!$is_member && !(preg_match('/.+@.+$/',$aDelivery['ship_email'])))
                || (trim($aDelivery['ship_tel']) == '' && trim($aDelivery['ship_mobile']) == '')
                || trim($aDelivery['ship_addr']) == ''){
                return false;
            }else{
                return 'Y';
            }
        }else{
            return 'N';
        }
    }

    function checkPoint($memberid, $data){
        //+积分处理------------------------------------------------------------
        $oMemberPoint = $this->system->loadModel('trading/memberPoint');
        $oGift = $this->system->loadModel('trading/gift');
        $data['score_u'] = intval($data['totalConsumeScore']);
        if ($data['score_u']>0) {
            if ($data['score_u'] > $oMemberPoint->getMemberPoint($memberid)) {
                trigger_error('用户积分不足',E_USER_ERROR);
                return false;
            }else{
                //+赠品处理------------------------------------------------------------
                if (is_array($data['gift_e']) && count($data['gift_e'])) {
                    foreach($data['gift_e'] as $giftId => $v) {
                        $aGift = $oGift->getFieldById($v['gift_id'], array('storage','freez'));
                        if ($aGift['storage'] - $aGift['freez'] < $v['nums']) {//兑换赠品缺货
                            trigger_error('兑换赠品'.$v['name'].'缺货',E_USER_ERROR);//中断
                            return false;
                        }
                    }
                }
            }
            return true;
        }else{
            return true;
        }
    }

    function checkGift($gift_p){
        if (is_array($gift_p) && count($gift_p)){
            $oGift = $this->system->loadModel('trading/gift');
            foreach($gift_p as $v) {
                $giftId = $v['gift_id'];
                if (!$oGift->freezStock($v['gift_id'], $v['nums'])) {//兑换赠品缺货
                    trigger_error('赠送赠品'.$v['name'].'缺货',E_USER_ERROR);//提示 ,E_USER_ERROR继续
                    return false;
                }
            }
            return true;
        }else{
            return true;
        }
    }

    function _saveAddr($memberid, $aData){
        if($memberid && $aData['is_save']){
            $member = $this->system->loadModel('member/member');
            $aAddr['member_id'] = $memberid;
            $aAddr['name'] = $aData['ship_name'];
            $aAddr['area'] = $aData['ship_area'];
            $aAddr['addr'] = $aData['ship_addr'];
            $aAddr['zip'] = $aData['ship_zip'];
            $aAddr['tel'] = $aData['ship_tel'];
            $aAddr['mobile'] = $aData['ship_mobile'];
            if($aData['addr_id']){
                $aAddr['addr_id'] = $aData['addr_id'];
                $member->saveRec($aAddr);
            }else{
                $member->insertRec($aAddr, $aAddr['member_id']);
            }
        }
        return true;
    }

    function create(&$aCart,&$aMember,&$aDelivery,&$aPayment,&$minfo,&$postInfo ){
        $oSale = $this->system->loadModel('trading/sale');
        $trading = $oSale->getCartObject($aCart,$aMember['member_lv_id'],true,true);

        //保存收货人地址
        $this->_saveAddr($aMember['member_id'], $aDelivery);

        $iProduct = 0;
        if (is_array($trading['products']) && count($trading['products'])){
            $objGoods = $this->system->loadModel('trading/goods');    //生成订单前检查库存
            $arr = array();
            $aLinkId = array();
            foreach($trading['products'] as $k => $p){
                $aStore = $objGoods->getFieldById($p['goods_id'], array('marketable','disabled'));
                if($aStore['marketable'] == 'false' || $aStore['disabled'] == 'true'){
                    /**
                     * trigger Smarty error
                     *
                     * @param string $error_msg
                     * @param integer $error_type
                     */
                    trigger_error($p['name'].'商品未发布不能下单。',E_USER_ERROR);
                    return false;
                    exit;
                }
/*                $gStore = intval($aStore['store']) - intval($aStore['freez']);
                if(!is_null($aStore['store']) && $gStore < $p['nums']){
                    trigger_error("商品“".$aStore['name']."”库存不足",E_USER_ERROR);
                    return false;
                    exit;
                }

                //判断配件库存to检查变量
                if(count($p['adjList'])){
                    $objCart = $this->system->loadModel('trading/cart');
                    foreach($p['adjList'] as $pid => $num){
                        if(!$objCart->_checkStore($pid, $num*$p['nums'])){
                            trigger_error("商品配件库存不足",E_USER_ERROR);
                            return false;
                            exit;
                        }
                    }
                }*/
                $arr[] = $p['name'].'('.$p['nums'].')';
                $this->itemnum+=$p['nums'];
                $aLinkId[] = $p['goods_id'];
                $trading['products'][$k]['addon']['minfo'] = $minfo[$p['product_id']];    //将商品用户信息存入addon
                $trading['products'][$k]['minfo'] = $minfo[$p['product_id']];    //将商品用户信息存入addon

                if($p['goods_id']) $aP[] = $p['goods_id'];
                $iProduct++;
            }
        }
        if($trading['package'] || $trading['gift_e']) $otherPhysical = true;
        else $otherPhysical = false;
        if(count($aP) || $otherPhysical){
            $return = $this->checkOrderDelivery($aP, $aDelivery, $otherPhysical, $aMember['member_id']);    //检测实体商品配送信息的合法性
            if($return){
                $aDelivery['is_delivery'] = $return;
                if($return == 'Y' && empty($aDelivery['shipping_id'])){
                    trigger_error("提交不成功，请选择配送方式",E_USER_ERROR);
                    return false;
                    exit;
                }
            }else{
                trigger_error("对不起，请完整填写配送信息",E_USER_ERROR);
                return false;
                exit;
            }
        }

        $iPackage = 0;
        if (is_array($trading['package']) && count($trading['package'])){
            $objCart = $this->system->loadModel('trading/cart');
            foreach ($trading['package'] as $v) {
                if (!$objCart->_checkStore($v['goods_id'], $v['nums'])) {
                    trigger_error("捆绑商品库存不足",E_USER_ERROR);
                    return false;
                    exit;
                }
                $iPackage++;
                $arr[] = $v['name'].'('.$v['nums'].')';
            }
        }
        if(is_array($trading['gift_e']) && count($trading['gift_e'])){
            foreach ($trading['gift_e'] as $v){
                $arr[] = $v['name'].'('.$v['nums'].')';
            }
        }
        if($iProduct + $iPackage + count($trading['gift_p']) + count($trading['gift_e']) == 0){
            trigger_error("购物车中无有效商品!",E_USER_ERROR);
            return false;
        }

//        $objProduct->updateRate($aLinkId);    //更新商品推荐度
        $oCur = $this->system->loadModel('system/cur');
        $tdelivery = explode( ':' , $aDelivery['ship_area'] );
        $area_id = $tdelivery[count($tdelivery)-1];
        $oDelivery = $this->system->loadModel('trading/delivery');
        $rows = $oDelivery->getDlTypeByArea($area_id,$trading['weight'],$aDelivery['shipping_id']);
        if($trading['exemptFreight'] == 1){    //[exemptFreight] => 1免运费
            $aDelivery['cost_freight']=0;
        }else{
            $trading['cost_freight'] = $oCur->formatNumber(cal_fee($rows[0]['expressions'],$trading['weight'],$trading['pmt_b']['totalPrice'],$rows[0]['price']));
        }
        $trading['cost_freight'] = is_null($trading['cost_freight'])?0:$trading['cost_freight'];
        if($aDelivery['is_protect'][$aDelivery['shipping_id']] && $rows[0]['protect']==1){
            $aDelivery['cost_protect'] = $oCur->formatNumber(max($trading['totalPrice']*$rows[0]['protect_rate'],$rows[0]['minprice']));
            $aDelivery['is_protect'] = 'true';
        }else{
            $aDelivery['cost_protect']=0;
            $aDelivery['is_protect'] = 'false';
        }
        if($aPayment['payment'] > 0 || $aPayment['payment'] == -1){
            $oPayment = $this->system->loadModel('trading/payment');
            $aPay = $oPayment->getPaymentById($aPayment['payment']);
            if($aPay['pay_type'] == 'DEPOSIT' && $aMember['member_id'] == ""){
                trigger_error("未登录客户不能选择预存款支付!",E_USER_ERROR);
                return false;
            }
            $aPayment['fee'] = $aPay['fee'];
        }else{
            trigger_error("提交不成功，未选择支付方式!",E_USER_ERROR);
            return false;
        }
        $currency = $oCur->getcur($aPayment['currency']);

        if(!$this->checkPoint($aMember['member_id'], $trading)){
            return false;
        }
        if(!$this->checkGift($trading['gift_p'])){
            return false;
        }

        $orderInfo = $trading;
        $orderInfo['order_id'] = $this->gen_id();
        $orderInfo['cur_rate'] = ($currency['cur_rate']>0 ? $currency['cur_rate']:1);
        $orderInfo['tostr'] = addslashes(implode(',',$arr));
        $orderInfo['itemnum'] = $this->itemnum;
        getRefer($orderInfo);    //推荐下单
        $aDelivery['ship_time'] = ($aDelivery['day']=='specal' ? $aDelivery['specal_day'] : $aDelivery['day']).' '.$aDelivery['time'];
        $orderInfo = array_merge($orderInfo,$aDelivery, $aPayment);
        if( $aMember ){
            $orderInfo = array_merge($orderInfo,$aMember);
        }
        return $this->save($orderInfo, true,$postInfo);
    }

    function gen_id(){
        $i = rand(0,9999);
        do{
            if(9999==$i){
                $i=0;
            }
            $i++;
            $order_id = mydate('YmdH').str_pad($i,4,'0',STR_PAD_LEFT);
            $row = $this->db->selectrow('SELECT order_id from sdb_orders where order_id ='.$order_id);
        }while($row);
        return $order_id;
    }

    /**
     * save
     * 保存订单，用于新建或者修改
     *
     * @param mixed $doCreate 是否为新建订单
     * @access public
     * @return void todo:目前只适用于添加！！！
     */
    function save(&$trading, $doCreate=false,&$postInfo){
        $data = $trading;
        $objDelivery = $this->system->loadModel('trading/reship');
        $oCur = $this->system->loadModel('system/cur');
        $aShipping = $objDelivery->getDlTypeById($trading['shipping_id']);
        $aArea = $objDelivery->getDlAreaById($trading['area']);
        $data['shipping'] = addslashes($aShipping['dt_name']);
        $data['shipping_area'] = addslashes($aArea['name']);
        $data['acttime'] = time();
        $data['createtime'] = time();
        $data['ip'] = remote_addr();
        $trading['totalPrice'] = $oCur->formatNumber($trading['totalPrice']);
        $trading['pmt_b']['totalPrice'] = $oCur->formatNumber($trading['pmt_b']['totalPrice']);
        $data['cost_item'] = $trading['totalPrice'];
        $data['total_amount'] = $trading['totalPrice']+$trading['cost_freight']+$trading['cost_protect'];
        $data['pmt_amount'] = $trading['pmt_b']['totalPrice'] - $trading['totalPrice'];
        if($trading['is_tax'] && $this->system->getConf('site.trigger_tax')){
            $data['is_tax'] = 'true';
            $data['cost_tax'] = $trading['totalPrice'] * $this->system->getConf('site.tax_ratio');
            $data['cost_tax'] = $oCur->formatNumber($data['cost_tax']);
            $data['total_amount'] += $data['cost_tax'];
        }
        if($trading['payment'] > 0){
            $data['cost_payment'] = $data['fee'] * $data['total_amount'];
            $data['cost_payment'] = $oCur->formatNumber($data['cost_payment']);
            $data['total_amount'] += $data['cost_payment'];
        }

        $newNum = $this->getOrderDecimal($data['total_amount']);
        $data['discount'] = floatval($data['total_amount'] - $newNum);
        $data['total_amount'] = $newNum;
        $data['final_amount'] = $data['total_amount'] * $data['cur_rate'];
        $data['final_amount'] = $oCur->formatNumber($data['final_amount']);
        $data['score_g'] = intval($data['totalGainScore']);
        $data['score_u'] = intval($data['totalConsumeScore']);
        $data['last_change_time'] = time();
        if ($trading['payment']!="-1"){
            //----检测该支付方式是否还有子选项，如快钱选择银行
            $payment=$this->system->loadModel('trading/payment');
            $payment->recgextend($data,$postInfo,$extendInfo);
            $data['extend']=serialize($extendInfo);
            //------------------------------------------------
        }
        //+判断是否有远端商品
        if(true || $this->system->getConf('certificate.distribute')){ //检测付款前的订单状态,如果是刚付款立即发货
            if (!empty($trading['products']) && is_array($trading['products'])) {
                foreach($trading['products'] as $product){
                    $_where_bns[] = sprintf('\'%s\'',addslashes($product['bn']));
                }
                $_sql = sprintf('select local_bn,supplier_id
                                 from sdb_supplier_pdtbn
                                 where local_bn in(%s) and `default`=\'true\'', implode(',', $_where_bns));
                $_remote_product = $this->db->select($_sql);
                $_remote_product = array_change_key($_remote_product, 'local_bn');
                if($_remote_product){
                    $data['is_has_remote_pdts'] = 'true';
                }
            }
        }

        //----------------
        $rs = $this->db->exec('SELECT * FROM sdb_orders WHERE order_id='.$data['order_id']);
        $sql = $this->db->getUpdateSql($rs,$data,$doCreate);
        $this->_info['order_id'] = $data['order_id'];        //会员id
        if(!$this->db->exec($sql)){
            return false;
        }elseif($doCreate){
            $this->addLog('订单创建', $this->op_id?$this->op_id:null, $this->op_name?$this->op_name:null , '添加' );
        }
        $status = $this->system->loadModel('system/status');
        $status->add('ORDER_NEW');
        $status->count_order_to_pay();
        $status->count_order_new();

        //+商品------------------------------------------------------------
        if (!empty($trading['products']) && is_array($trading['products'])) {

            $objGoods = $this->system->loadModel('trading/goods');
            foreach ($trading['products'] as $product) {
                $product['order_id'] = $data['order_id'];
                $product['bn'] = addslashes($product['bn']);
                $product['name'] = addslashes($product['name']);
                $product['addon'] = addslashes(serialize($product['addon']));
                $product['minfo'] = addslashes(serialize($product['minfo']));
                $product['supplier_id'] = $_remote_product[$product['bn']]['supplier_id'];

                $rs = $this->db->query('SELECT * FROM sdb_order_items WHERE 0=1');
                $sqlString = $this->db->GetInsertSQL($rs, $product);
                if($sqlString) $this->db->exec($sqlString);
                $objGoods->updateRank($product['goods_id'], 'buy_count', $product['nums']);    //购买次数统计
                //冻结库存
                $this->db->exec("UPDATE sdb_products SET freez = freez + ".intval($product['nums'])." WHERE product_id = ".intval($product['product_id']));
                $this->db->exec("UPDATE sdb_products SET freez = ".intval($product['nums'])." WHERE product_id = ".intval($product['product_id'])." AND freez IS NULL");

            }

        }

        //+捆绑商品------------------------------------------------------------
        if (is_array($trading['package']) && count($trading['package'])) {
            foreach ($trading['package'] as $pkgData) {
                $pkgData['order_id'] = $data['order_id'];
                $pkgData['bn'] = addslashes($pkgData['bn']);
                $pkgData['name'] = addslashes($pkgData['name']);
                $pkgData['product_id'] = $pkgData['goods_id'];
                $pkg[] = $pkgData['goods_id'];
                $pkgData['is_type'] = 'pkg';
                $pkgData['addon'] = addslashes(serialize($pkgData['addon']));
                $rs = $this->db->query('SELECT * FROM sdb_order_items WHERE order_id='.$pkgData['order_id'].' AND is_type = \'pkg\' AND product_id='.intval($pkgData['goods_id']));
                $sqlString = $this->db->GetUpdateSQL($rs, $pkgData,true);
//                $this->db->exec("UPDATE sdb_products SET freez = freez + ".intval($aData['nums'])." WHERE product_id = ".intval($aData['product_id']));
//                $this->db->exec("UPDATE sdb_products SET freez = ".intval($aData['nums'])." WHERE product_id = ".intval($aData['product_id'])." AND freez IS NULL");
                $this->db->exec($sqlString);
            }
            $this->db->exec('DELETE FROM sdb_order_items WHERE order_id='.$pkgData['order_id'].' AND is_type = \'pkg\' AND product_id NOT IN('.implode(',',$pkg).')');
        }

        //+促销信息------------------------------------------------------------
        if ($trading['pmt_o']['pmt_ids']) {//促销
            $sSql = 'INSERT INTO sdb_order_pmt (pmt_id,pmt_describe,order_id) select pmt_id,pmt_describe,\''.$data['order_id'].'\' FROM sdb_promotion WHERE pmt_id in('.implode(',',$trading['pmt_o']['pmt_ids']).')';
            $this->db->exec($sSql);
            foreach($trading['pmt_o']['pmt_ids'] as $k=>$pmtId) {
                $sSql = 'UPDATE sdb_order_pmt SET pmt_amount='.$trading['pmt_o']['pmt_money'][$k].' WHERE pmt_id='.intval($pmtId).' AND order_id='.$data['order_id'];
                $this->db->exec($sSql);
            }
        }
        if ($trading['products']) {
            $pre_pmtOrder = array();
            foreach ($trading['products'] as $v) {
                if ($v['pmt_id']){
                    $pre_pmtOrder[$v['pmt_id']] += $v['price'] - $v['_pmt']['price'];
                }
            }
            $aPmtIds = array_keys($pre_pmtOrder);
            if(!empty($aPmtIds)){
                $sSql = 'SELECT pmt_id,pmt_describe FROM sdb_promotion WHERE pmt_id IN('.implode(',', $aPmtIds).')';
                $aPmtOrder = $this->db->select($sSql);
                foreach($aPmtOrder as $k=>$v) {
                    $v['pmt_amount'] = $pre_pmtOrder[$v['pmt_id']];
                    $v['order_id'] = $data['order_id'];

                    $rs = $this->db->query('select * from sdb_order_pmt where 0=1');
                    $sqlString = $this->db->GetInsertSQL($rs, $v);
                    $this->db->exec($sqlString);
                }
            }
        }

        //+积分处理------------------------------------------------------------
        $oMemberPoint = $this->system->loadModel('trading/memberPoint');
        $oGift = $this->system->loadModel('trading/gift');
        $aGiftData = array();
        if ($data['score_u']>=0) {
            if (!$oMemberPoint->payAllConsumePoint($data['member_id'],$data['order_id'])) {
                ;
            }else{
                //+赠品处理------------------------------------------------------------
                if (is_array($trading['gift_e']) && count($trading['gift_e'])) {
                    foreach($trading['gift_e'] as $giftId => $v) {
                        $giftId = $v['gift_id'];
                        $aGiftData[$giftId] = array(
                            'gift_id' => $giftId,
                            'name' => $v['name'],
                            'nums' => $v['nums'],
                            'point' => $v['point']);
                        if (!$oGift->freezStock($v['gift_id'], $v['nums'])) {//兑换赠品缺货
                            ;
                        }
                    }
                }
            }
        }
        if (is_array($trading['gift_p']) && count($trading['gift_p'])){
            foreach($trading['gift_p'] as $v) {
                $giftId = $v['gift_id'];
                if (isset($aGiftData[$giftId])) {
                    $aGiftData[$giftId]['nums'] += $v['nums'];
                }else {
                    $aGiftData[$giftId] = array(
                            'gift_id' => $giftId,
                            'name' => $v['name'],
                            'nums' => $v['nums'],
                            'point' => $v['point']);
                }
            }
        }
        if($aGiftData) {
            foreach($aGiftData as $item) {
                $oGift = $this->system->loadModel('trading/gift');
                $item['order_id'] = $data['order_id'];
                $rs = $this->db->query('select * from sdb_gift_items where 0=1');
                $sqlString = $this->db->GetInsertSQL($rs, $item);
                $this->db->exec($sqlString);
            }
        }

        //+优惠券------------------------------------------------------------
        if (is_array($trading['coupon_u']) && !empty($trading['coupon_u'])) {
            $oCoupon = $this->system->loadModel('trading/coupon');
            foreach ($trading['coupon_u'] as $code => $v) {
                $aTmp = $this->db->selectRow('select cpns_name from sdb_coupons where cpns_id='.intval($v['cpns_id']));
                $aData = array(
                    'order_id' => $data['order_id'],
                    'cpns_id' => $v['cpns_id'],
                    'memc_code' => $code,
                    'cpns_name' => $aTmp['cpns_name'],
                    'cpns_type' => $v['cpns_type']);
                $rs = $this->db->query('select * from sdb_coupons_u_items where 0=1');
                $sqlString = $this->db->GetInsertSQL($rs, $aData);
                $this->db->exec($sqlString);

//                echox($v['cpns_id'], $code, $data['order_id'], $data['member_id']);exit;

                $oCoupon->applyMemberCoupon($v['cpns_id'], $code, $data['order_id'], $data['member_id']);
            }
        }

        if (is_array($trading['coupon_p']) && !empty($trading['coupon_p'])) {
            foreach ($trading['coupon_p'] as $code => $v) {
                $aData = array(
                    'order_id' => $data['order_id'],
                    'cpns_id' => $v['cpns_id'],
                    'cpns_name' => $v['cpns_name'],
                    'nums' => $v['nums']);
                $rs = $this->db->query('select * from sdb_coupons_p_items where 0=1');
                $sqlString = $this->db->GetInsertSQL($rs, $aData);
                $this->db->exec($sqlString);
            }
        }
        $this->fireEvent('create',$data,$data['member_id']);        //订单生成成功事件
        return $data['order_id'];
    }

    function checkOrderStatus($act, &$aOrder){
        /**
        *    @params:
        *        @$aOrder['status']        :    订单状态
        *            @values
        *                active    :激活
        *        @$aOrder['ship_status']:订单发货状态
        *            @values:
        *                    0:未发货
        *                    4:已退货
        *        @$aOrder['pay_status']    :
        *            @values
        *                1:已支付
        *                2:处理中(在线支付)
        *                5:全额退款
        */
        switch($act){
            case 'pay':
                if($aOrder['status'] != 'active' || $aOrder['pay_status'] == '1' || $aOrder['pay_status'] == '2' || $aOrder['pay_status'] == '4' || $aOrder['pay_status'] == '5'){
                    return false;
                    exit;
                }
            break;
            case 'refund':
                if($aOrder['status'] != 'active' || $aOrder['pay_status'] == '0' || $aOrder['pay_status'] == '5'){
                    return false;
                    exit;
                }
            break;
            case 'delivery':
                if($aOrder['status'] != 'active' || $aOrder['ship_status'] == '1'){
                    return false;
                    exit;
                }
            break;
            case 'reship':
                if($aOrder['status'] != 'active' || $aOrder['ship_status'] == '0' || $aOrder['ship_status'] == '4'){
                    return false;
                    exit;
                }
            break;
            case 'cancel':
                if($aOrder['status'] != 'active' || $aOrder['pay_status'] > 0 || $aOrder['ship_status'] > 0){
                    return false;
                    exit;
                }
            break;
        }
        return true;
    }
    function update_last_modify($order_id){
        return $this->db->query('update set last_change_time='.time().' where order_id='.intval($order_id));
    }
    /**
     * payed
     * 订单支付，前后台支付都是调用这个方法，进行付款或后续发货动作
     *
     * @param mixed $money 实际支付金额(扣除支付所花费用)
     * @access public
     * @return void
     */

    function toPayed($aData, $createBill=false){
        $aOrder = $this->load($aData['order_id']);
        if(!$aOrder){
            $this->system->error(501);
            return false;
            exit;
        }
        if(!$this->checkOrderStatus('pay', $aOrder)){
            $this->setError(10001);
            trigger_error(__('订单状态锁定'),E_USER_ERROR);
            return false;
            exit;
        }

        /**
        *    @params
        *        @$nonPay    :此次未付费金额
        *        @$aOrder['amount']['total'] 订单总金额
        *        @$aOrder['amount']['payed'] 订单已付金额
        */
        $nonPay = $aOrder['amount']['total'] - $aOrder['amount']['payed'];
        if(isset($aData['money'])){    //从收款单提交进入
            if($aData['money'] > $nonPay || $aData['money'] <= 0){
                $this->setError(10001);
                trigger_error(__('支付总金额不在订单金额范围'),E_USER_ERROR);
                return false;
                exit;
            }
            $paymentId = $aData['payment'];
            $payMethod = $aData['payment'];
            $payMoney = $aData['money'];
        }else{    //未从收款单提交进入
            /**
            *    @branch:未填写付款数额按照全额处理
            */
            $paymentId = $aOrder['payment'];
            switch($aOrder['paytype']){
                case 'DEPOSIT':
                $aData['pay_type'] = 'deposit';
                break;
                case 'OFFLINE':
                $aData['pay_type'] = 'offline';
                break;
                default:
                $aData['pay_type'] = 'online';
                break;
            }
            $payMethod = $aOrder['paymethod'];
            $payMoney = $nonPay;
        }
        $oCur = $this->system->loadModel('system/cur');
        $payMoney = $oCur->formatNumber($payMoney);
        if($aData['pay_type'] == 'deposit'){
            $oAdvance = $this->system->loadModel("member/advance");
            if(!$oAdvance->checkAccount($aOrder['member_id'], $payMoney, $message)){
                trigger_error(__('支付失败：').$message,E_USER_ERROR);
                return false;
                exit;
            }
        }

        /**
        *    @params:
        *    values:$createBill是否生成单据
        */
        $payment = $this->system->loadModel('trading/payment');
        if($createBill){
            //后台收款
            $payment->pay_type = $aData['pay_type'];    //订单付款 在线，线下，预付款
            $payment->op_id = intval($aData['opid']);
            $payment->order_id = $aData['order_id'];
            $payment->member_id = $aOrder['member_id'];
            $payment->currency = $aOrder['currency'];
            $payment->money = $payMoney;
            $payment->cur_money = $payMoney;    //后台手工支付的币别金额都取本位币
            $payment->pay_account = $aData['pay_account'];
            $payment->payment = intval($paymentId);
            $payment->paymethod = $payMethod;
            $payment->account = $aData['account'];
            $payment->bank = $aData['bank'];
            $payment->status = 'ready';

            $pay_id = $payment->toCreate();
            if(!$pay_id){
                $this->setError(10001);
                trigger_error(__('支付单不能正常生成'),E_USER_ERROR);
                return false;
                exit;
            }
        }else{
            //前台支付返回不需要生成支付单据.
            $pay_id = $aData['payment_id'];
        }
        /*
        * @function addLog():添加订单LOG
        */
        $o = $this->system->loadModel('trading/payment');
        $aPay['memo'] = ($aData['memo'] ? $aData['memo'].'#' : '').'后台'.$aData['opname'].'支付成功！';
        return $o->setPayStatus($pay_id, PAY_SUCCESS, $aPay);
    }

    /**
      *    @params:
      *    values:$data=array(
      *                     order_id
      *                     payment_id  //支付单号
      *                     pay_type    //支付单类型：（deposit预存款）
      *                     money       //支付金额(已折算本位币)
      *                     currency    //支付货币
      *                     member_id   //支付会员
      *                     paymethod   //支付方式名称
      *                     status      //支付前的支付单状态
      *                     pay_assure  //是否担保交易 true/false
      *                     pay_account //发邮件时的付款人
      *                         )
      */
    function payed($data, &$message){
        if(empty($data['order_id'])){
            $message .= '支付单：订单号{'.$info['payment_id'].'}没有对应订单号';
            return false;
        }
        $aOrder = $this->getFieldById($data['order_id'], array('total_amount','payed','pay_status','ship_status','status'));
        $aOrder['order_id'] = $data['order_id'];
        if($aOrder['pay_status'] == 0 || $aOrder['pay_status'] == 3 || ($aOrder['pay_status'] == 2 && !$data['pay_assure'])){    //如何是未支付或者部分支付或者支付中
            if($data['pay_type'] =='deposit' && ($aOrder['pay_status'] == 0 || $aOrder['pay_status'] == 3)){  //预存款付款
                $message .= '预存款支付：订单号{'.$data['order_id'].'}';
                $oAdvance = $this->system->loadModel("member/advance");
                if(!$oAdvance->deduct($data['member_id'], $data['money'], $message, $message, '', $data['order_id'] ,'' , '预存款支付')){
                    return false;
                }
            }
        }

        if($aOrder['total_amount'] - $aOrder['payed'] <= $data['money']){
            /**
            *    @branch:全额付款
            */
            $aOrder['pay_status']= ($data['pay_assure'] ? 2:1);    //如果是担保交易则2，否则已支付1
            $aOrder['payed'] = $aOrder['total_amount'];
        }else{  //部分付款
            $aOrder['pay_status'] = 3;
            $aOrder['payed'] = $aOrder['payed'] + $data['money'];

/*            if($aData['pay_status'] == 1){
                $lastMoney = $nonPay - $payMoney;
                $this->addLog(__('更改订单金额:减少').$lastMoney);
                $aUpdate['pay_status'] = 1;
                $aUpdate['discount'] += $lastMoney;
            }else{
                $aUpdate['pay_status'] = 3;
            }*/
        }
        $aOrder['acttime'] = time();
        $aOrder['last_change_time'] = time();
        if(!$this->toEdit($data['order_id'], $aOrder)){
            $message .= __('更新订单失败');
            return false;
        }

        $this->addLog('订单'.$aOrder['order_id'].'付款'.($data['pay_assure'] ? '（担保交易）':'').$data['money'], $this->op_id?$this->op_id:null, $this->op_name?$this->op_name:null , '付款');
        if($aOrder['status'] != 'active'){  //死单被支付的情况
            return true;
        }

        if($aOrder['pay_status']==1 || $aOrder['pay_status']==2){
            if($this->system->getConf('system.auto_delivery')){ //检测付款前的订单状态,如果是刚付款立即发货
                $this->delivery($aOrder, false);
            }
        }

        if ($aOrder['pay_status'] == 1){
            $aPara = $aOrder;
            $aOrder['money'] = $data['money'];
            $aOrder['pay_account'] = $data['pay_account'];
            $s = $this->fireEvent('pay', $aOrder, $data['member_id']);  //给积分

            $status = $this->system->loadModel('system/status');
            if($data['order_id'] && ($aOrder['pay_status'] == 1 || $aOrder['pay_status'] == 2)){
                if($aOrder['ship_status'] == 1){
                    $status->add('ORDER_SUCC');
                    $status->add('REVENUE', $aOrder['total_amount']);
                }else{
                    $status->count_order_to_dly();
                }
            }
            $status->count_order_to_pay();
        }

        return $aOrder['pay_status'];
    }

    //退款操作
    function refund($aData){
        $aOrder = $this->load($aData['order_id']);
        if(!$aOrder){
            $this->system->error(501);
            return false;
            exit;
        }
        if(!$this->checkOrderStatus('refund', $aOrder)){
            $this->setError(10001);
            trigger_error(__('退款失败: 订单状态锁定'),E_USER_ERROR);
            return false;
            exit;
        }

        $payMoney = $aOrder['amount']['payed'] - $aOrder['amount']['cost_payment'];
        $aUpdate['pay_status']= 5;    //预设订单状态
        $aUpdate['payed'] = $aOrder['amount']['cost_payment'];    //预设订单支付金额
        if(isset($aData['money'])){    //从退款单提交进入
            if($aData['money'] > $payMoney || $aData['money'] <= 0){
                $this->setError(10001);
                trigger_error(__('退款金额不在订单已支付金额范围'),E_USER_ERROR);
                return false;
            }

            if($payMoney > $aData['money']){
                $aUpdate['pay_status'] = 4;
                $aUpdate['payed'] = $aOrder['amount']['payed'] - $aData['money'];
            }
            $paymentId = $aData['payment'];
            $payMethod = $aData['payment'];
            $payMoney = $aData['money'];
        }else{    //未从退款单提交进入
            $paymentId = $aOrder['payment'];
            $payMethod = __("手工");
            switch($aOrder['paytype']){
                case 'DEPOSIT':
                $aData['pay_type'] = 'deposit';
                break;
                case 'OFFLINE':
                $aData['pay_type'] = 'offline';
                break;
                default:
                $aData['pay_type'] = 'online';
                break;
            }
        }

        if($aData['pay_type'] == 'deposit'){
            $oAdvance = $this->system->loadModel("member/advance");
            if(!$oAdvance->checkAccount($aOrder['member_id'], 0, $message)){
                trigger_error(__('支付失败：').$message,E_USER_ERROR);
                return false;
                exit;
            }
        }

        $aRefund['money'] = $payMoney;
        $aRefund['order_id'] = $aData['order_id'];
        $aRefund['send_op_id'] = intval($aData['opid']);
        $aRefund['pay_type'] = $aData['pay_type'];
        $aRefund['member_id'] = $aOrder['member_id'];
        $aRefund['account'] = $aData['account'];
        $aRefund['pay_account'] = $aData['pay_account'];
        $aRefund['bank'] = $aData['bank'];
        $aRefund['title'] = 'title';
        $aRefund['currency'] = $aOrder['currency'];
        $aRefund['payment'] = $paymentId;
        $aRefund['paymethod'] = $payMethod;
        $aRefund['status'] = 'sent';
        $aRefund['memo'] = ($aData['memo'] ? $aData['memo'].'#' : '').'管理员后台退款产生';

        $oRefund = $this->system->loadModel('trading/refund');
        $refund_id = $oRefund->create($aRefund);
        if(!$refund_id){
            $this->setError(10001);
            trigger_error(__('退款单不能正常生成'),E_USER_ERROR);
            return false;
        }
        $this->addLog(__('订单退款').$payMoney, $this->op_id?$this->op_id:null, $this->op_name?$this->op_name:null , '退款');

        $aUpdate['acttime'] = time();
        /**
        *    @function    toEdit():编辑订单
        */
        if(!$this->toEdit($aData['order_id'], $aUpdate)){
            $this->setError(10001);
            trigger_error(__('更新订单状态失败'),E_USER_ERROR);
            return false;
        }

        if($aData['pay_type'] =='deposit'){    //预存款付款
            $message .= '预存款退款：#O{'.$aData['order_id'].'}#';
            if(!$oAdvance->add($aOrder['member_id'], $payMoney, $message, $message, '', $aData['order_id'] ,'' ,'预存款退款')){
                return false;
            }
        }

        $aPara['pay_status'] = $aUpdate['pay_status'];
        $aPara['order_id'] = $aData['order_id'];
        $aPara['return_score'] = $aData['return_score'];
        $s = $this->fireEvent('refund', $aPara,$aOrder['member_id']);

        return $aPara['pay_status'];
    }

    /**
     * 有发货函数的不同类型商品生成不同的发货单
     * 发货不成功的商品生成失败的发货单
     * 订单会转移到非活动订单
     *
     * @access public
     * @return void
     */
    function delivery($aData, $manual=true){
        $aOrder = $this->load($aData['order_id']);
        if(!$aOrder){
            $this->system->error(501);
            return false;
        }
        if(!$this->checkOrderStatus('delivery', $aOrder)){
            $this->setError(10001);
            trigger_error(__('发货失败: 订单状态锁定'),E_USER_ERROR);
            return false;
        }

        //读取订单明细
        $rows = $this->db->select('SELECT i.item_id,i.addon,i.minfo,i.nums,i.sendnum,i.product_id,i.bn,i.name,i.is_type,
                    t.type_id,t.setting,t.schema_id,t.is_physical,t.dly_func FROM sdb_order_items i
                    LEFT JOIN sdb_goods_type t ON t.type_id = i.type_id
                    WHERE i.order_id='.$aData['order_id']);
        /**
        *    @aData['send'][id]:            发货数量
        *    @sdb_order_items.addon    :    序列化数量
        *    @error tito check
        *        @note:检查$aData['send'][]数量
        *        @$send = abs($aData['send']);
        */
        if(isset($aData['send'])){
            /**
            *    @function:    经过发货单发货
            *    @params:    $aData['send']为发货货品数组
            *
            */
            $oCorp = $this->system->loadModel('trading/delivery');
            $aCorp = $oCorp->getCorpById($aData['logi_id']);
            if(defined('SAAS_MODE')&&SAAS_MODE){
                $date  = getdeliverycorplist();
                $aCorp = $date[$aData['logi_id']-1];
                if($aData['other_name']!=""&&isset($aData['other_name'])){
                    $aCorp['name'] = $aData['other_name'];
                }
            }

            $delivery = array(
                'money' => floatval($aData['money']) + floatval($aData['cost_protect']),
                'is_protect' => $aData['is_protect'],
                'delivery' => $aData['delivery'],
                'logi_id' => $aData['logi_id'],
                'logi_no' => $aData['logi_no'],
                'logi_name' => $aCorp['name'],
                'ship_name' => $aData['ship_name'],
                'ship_area' => $aData['ship_area'],
                'ship_addr' => $aData['ship_addr'],
                'ship_zip' => $aData['ship_zip'],
                'ship_tel' => $aData['ship_tel'],
                'ship_mobile' => $aData['ship_mobile'],
                'ship_email' => $aData['ship_email'],
                'memo' => $aData['memo'],
                'gift_send' => $aData['gift_send']
            );
        }else{
            /** 未经过发货单发货
            *    @params:
            *        @order_id                :订单号
            *        @member_id                :会员号
            *        @shipping['method']        :发货方式
            */
            $aRet = $this->getGiftItemList($aData['order_id']);
            $aGiftItems = array();
            foreach($aRet as $aRows){
                $aGiftItems[$aRows['gift_id']] = $aRows['nums'] - $aRows['sendnum'];
            }
            $delivery = array(
                'money' => $aOrder['cost_freight']+$aOrder['cost_protect'],
                'is_protect' => $aOrder['is_protect'],
                'delivery' => $aOrder['shipping']['method'],
                'logi_id' => '',
                'logi_no' => '',
                'ship_name' => $aOrder['receiver']['name'],
                'ship_area' => $aOrder['receiver']['area'],
                'ship_addr' => $aOrder['receiver']['addr'],
                'ship_zip' => $aOrder['receiver']['zip'],
                'ship_tel' => $aOrder['receiver']['tel'],
                'ship_mobile' => $aOrder['receiver']['mobile'],
                'ship_email' => $aOrder['receiver']['email'],
                'gift_send' => $aGiftItems
            );
        }
        /**
        *    @function:    订单明细赋值,读取订单详细表sdb_order_items的addon字段
        *    @params:
        *        @$dinfo['addon']:        订单序列化字段，存放订单物品等资料
        *        @$delivery['op_name']:    订单操作人员
        *        @$aUpdate['ship_status']:订单发货状态 1为发货状态
        */
        $delivery['order_id'] = $aData['order_id'];
        $delivery['member_id'] = $aOrder['member_id'];
        $delivery['t_begin'] = time();
        $delivery['op_name'] = $aData['opname'];
        $delivery['type'] = 'delivery';
        $delivery['status'] = 'progress';

        //遍历订单明细
        $aBill = array();
        $nonGoods = 0;    //是否完全发货商品标识
        foreach($rows as $dinfo){
            $dinfo['addon'] = unserialize($dinfo['addon']);
            /**
            *    @$aData['send'][$dinfo['product_id']:需要发送的商品数量
            */
            if(!isset($aData['send']) || (isset($aData['send'][$dinfo['item_id']]) && $aData['send'][$dinfo['item_id']] > 0)){
                if($aData['send'][$dinfo['item_id']] > $dinfo['nums'] - $dinfo['sendnum']){
                    $message .= __('商品：'.$dinfo['name'].'发货超出购买量');
                    $this->setError(10001);
                    trigger_error($message, E_USER_ERROR);
                    return false;
                }

                if(!isset($aData['send']) || $aData['send'][$dinfo['item_id']] == $dinfo['nums']-$dinfo['sendnum']){
//                    $aUpdate['items'][$dinfo['product_id']] = $dinfo['nums'];
                    $dinfo['send'] = $dinfo['nums']-$dinfo['sendnum'];    //本次发货数量
                }else{
                    $nonGoods = 1;
//                    $aUpdate['items'][$dinfo['product_id']] = $dinfo['sendnum'] + $aData['send'][$dinfo['product_id']];
                    $dinfo['send'] = intval($aData['send'][$dinfo['item_id']]);
                }

                /**
                    @params:
                        @$dinfo['is_physical']是否为实体商品
                            @value:
                                1:实体商品
                        @$dinfo['dly_func']????
                */
                if($dinfo['dly_func'] == 1){
                    $aBill['func'][$dinfo['schema_id']][] = $dinfo;
                }else{
                    if($dinfo['is_physical']==1 || empty($dinfo['type_id'])){    //如果读取类型id为空，说明当前商品一件被删除（或捆绑商品），默认为实体商品发货
                        $dinfo['is_physical'] = true;
                        $aBill['nofunc'][] = $dinfo;
                    }else{
                        $aBill['error'][] = $dinfo;    //如果虚拟商品没有发货函数，则需要建立失败发货单
                    }
                }
            }else{
                if($dinfo['nums'] > $dinfo['sendnum']) $nonGoods = 1;
            }
        }
        if(count($rows) && count($aBill) == 0) $nonGoods = -1;    //商品没有发货

        $objShipping = $this->system->loadModel('trading/delivery');
        $schema = $this->system->loadModel('goods/schema');
        if($aBill['func'])    //有发货函数的实体虚拟商品
            foreach($aBill['func'] as $schema_id => $rows){
                $delivery['delivery_id'] = $objShipping->getNewNumber($delivery['type']);
                $delivery['memo'] = $aData['memo'];
                $iLoop = 0;

                foreach($rows as $dinfo){
                    $setting = unserialize($dinfo['setting']);
                    ob_start();
                    //执行发货函数
                    $minfo = array();
                    if($mData = unserialize($dinfo['minfo'])){
                        foreach($mData as $minfo_key=>$minfo_row){
                            $minfo[$minfo_key] = $minfo_row['value'];
                        }
                    }

                    $setting['idata'] = $dinfo['addon']['idata'];
                    unset($setting['data']);
                    $result = $schema->delivery($dinfo['schema_id'],$minfo,$setting,$dinfo['nums'],$logs);
                    $output = ob_get_clean();

                    $delivery['memo'] .= addslashes($logs."\n".$output);
                    if(!$result){    //发货失败
                        $aBill['error'][] = $dinfo;    //todo 是否程序终止有待讨论（现在是继续执行）
                    }else{
                        $item = array(
                                    'order_item_id' => $dinfo['item_id'],
                                    'order_id' => $aData['order_id'],
                                    'delivery_id' => $delivery['delivery_id'],
                                    'item_type' => ($dinfo['is_type']=='pkg' ? $dinfo['is_type'] : 'goods'),
                                    'product_id' => $dinfo['product_id'],
                                    'product_bn' => addslashes($dinfo['bn']),
                                    'product_name' => addslashes($dinfo['name'].$dinfo['addon']['adjname']),
                                    'adjunct' => addslashes($dinfo['addon']['adjinfo']),
                                    'number' => $dinfo['send'] );
                        if(!$objShipping->toInsertItem($item, $dinfo['is_physical'], $delivery['type'], $delivery['status'])){
                            $aBill['error'][] = $dinfo;    //todo 是否程序终止有待讨论（现在是继续执行）
                        }else{
                            $iLoop++;
                        }
                    }
                }
                if($iLoop > 0){
                    if(!$objShipping->toCreate($delivery)){
                        $this->setError(10001);
                        trigger_error('配送单据生成失败', E_USER_ERROR);
                        return false;
                    }
                }
            }

        if($aBill['nofunc']){        //实体商品
            if($manual || (!$manual && $this->system->getConf('system.auto_delivery_physical') != 'no')){
                if(!$manual){
                    $delivery['status'] = ($this->system->getConf('system.auto_delivery_physical')=='yes' ? 'progress' : 'ready');
                }
                $iLoop = 0;
                $delivery['delivery_id'] = $objShipping->getNewNumber($delivery['type']);
                foreach($aBill['nofunc'] as $dinfo){
                    $item = array(
                                'order_item_id' => $dinfo['item_id'],
                                'order_id' => $aData['order_id'],
                                'delivery_id' => $delivery['delivery_id'],
                                'item_type' => ($dinfo['is_type']=='pkg' ? $dinfo['is_type'] : 'goods'),
                                'product_id' => $dinfo['product_id'],
                                'product_bn' => addslashes($dinfo['bn']),
                                'product_name' => addslashes($dinfo['name'].$dinfo['addon']['adjname']),
                                'adjunct' => addslashes($dinfo['addon']['adjinfo']),
                                'number' => $dinfo['send'] );
                    if(!$objShipping->toInsertItem($item, $dinfo['is_physical'], $delivery['type'], $delivery['status'])){
                        $aBill['error'][] = $dinfo;    //todo 是否程序终止有待讨论（现在是继续执行）
                    }else{
                        $iLoop++;
                    }
                }
            }
            if($iLoop > 0){
                if(!$objShipping->toCreate($delivery)){
                    $this->setError(10001);
                    trigger_error('配送单据生成失败', E_USER_ERROR);
                    return false;
                }
                $eventId = $$delivery['delivery_id'];
            }
        }

        if($aBill['error']){
            $nonGoods = 1;
            $iLoop = 0;
            $delivery['delivery_id'] = $objShipping->getNewNumber($delivery['type']);
            $delivery['status'] = 'failed';
            $delivery['money'] = 0;
            foreach($aBill['error'] as $dinfo){
                $item = array(
                            'order_item_id' => $dinfo['item_id'],
                            'order_id' => $aData['order_id'],
                            'delivery_id' => $delivery['delivery_id'],
                            'item_type' => ($dinfo['is_type']=='pkg' ? $dinfo['is_type'] : 'goods'),
                            'product_id' => $dinfo['product_id'],
                            'product_bn' => addslashes($dinfo['bn']),
                            'product_name' => addslashes($dinfo['name'].$dinfo['addon']['adjname']),
                            'adjunct' => addslashes($dinfo['addon']['adjinfo']),
                            'number' => $dinfo['send'] );
                $objShipping->toInsertItem($item, $dinfo['is_physical'], $delivery['type'], $delivery['status']);
                $iLoop++;
            }
            if($iLoop > 0){
                if(!$objShipping->toCreate($delivery)){
                    $this->setError(10001);
                    trigger_error('配送单据生成失败', E_USER_ERROR);
                    return false;
                }
            }
        }

        $aPara['order_id'] = $aData['order_id'];
        $aPara['message'] = array();
        $aPara['ship_status'] = 1;
        $aPara['ship_status_o'] = array();
        $aPara['delivery'] = $delivery;
        $aPara['delivery']['delivery_id'] = $eventId;
        $aPara['ship_billno'] = $aData['logi_no'];
        $aPara['ship_corp'] = $aCorp['name'];
        $s = $this->fireEvent('delivery', $aPara, $aPara['delivery']['member_id']);

        if($nonGoods == -1 && $aPara['ship_status_o'][0] == -1){
            $this->setError(10001);
            trigger_error('没有任何商品发货', E_USER_ERROR);
            $this->addLog('发货失败,没有发送任何商品', $this->op_id?$this->op_id:null, $this->op_name?$this->op_name:null , '发货');
            return false;
        }else{
            //没有完全发货
            if($nonGoods) $aUpdate['ship_status'] = 2;
            else $aUpdate['ship_status'] = 1;
            $aUpdate['order_id'] = $aData['order_id'];
            $aUpdate['ship_status'] = max($aUpdate['ship_status'], $aPara['ship_status_o'][0]);
            $this->setShipStatus($aUpdate);
            if(defined('SAAS_MODE')&&SAAS_MODE){
                include_once('delivercorp.php');
                $tmpdate =  getdeliverycorplist();
                $key = delivercorp_index($aCorp['name']);
                $aCorp['website'] = $tmpdate[$key]['query_interface'];
            }

            $status = $this->system->loadModel('system/status');
            if($aData['order_id'] && $aUpdate['ship_status'] == 1){
                $aOrder = $this->getFieldById($aData['order_id'], array('pay_status', 'total_amount'));
                if($aOrder['pay_status'] == 1 || $aOrder['pay_status'] == 2){
                    $status->add('ORDER_SUCC');
                    $status->add('REVENUE', $aOrder['total_amount']);
                    $status->count_order_to_dly();
                }
            }
            $status->count_order_new();

            //取得发货的具体信息，add by hujianxin
            $message_part1 = "";
            $message = "";

            $ship_status = $aUpdate['ship_status'];

            if($ship_status == 1){   //全部发货
                $message_part1 = "发货完成";
            }else if($ship_status == 2){    //部分发货
                $message_part1 = "已发货";
            }

            $message = "订单<!--order_id=".$aData['order_id']."&delivery_id=".$delivery['delivery_id']."&ship_status=".$ship_status."-->".$message_part1;

            $this->addLog($message.($delivery['logi_no'] ? '，物流公司：<a class="lnk" href="'.$aCorp['website'].'" target="_blank">'.$aCorp['name'].'</a>（可点击进入物流公司网站跟踪配送），物流单号：'.$delivery['logi_no'] : ''), $this->op_id?$this->op_id:null, $this->op_name?$this->op_name:null , '发货');
            return true;
        }
    }

    function setShipStatus($aData){
        $rs = $this->db->query("SELECT * FROM sdb_orders where order_id=".$aData['order_id']);
        $sql = $this->db->getUpdateSql($rs,$aData);
        if($sql) $this->db->exec($sql);
/*
已经在生成发货单里更改了
        if(isset($aData['items'])){
            foreach($aData['items'] as $productId => $sendnum){
                $this->db->exec('UPDATE sdb_order_items SET sendnum = '.$sendnum.' WHERE order_id='.$aData['order_id'].' AND product_id='.$productId);
            }
        }*/
        return true;
    }

    //订单退货
    function toReship($aData,&$message){
        /**
        *    @function:加载订单信息
        *    @params:
        *        @$aData['order_id']:订单编号
        */
        $aOrder = $this->load($aData['order_id']);
        if(!$aOrder){
            $this->system->error(501);
            return false;
            exit;
        }
        if(!$this->checkOrderStatus('reship', $aOrder)){
            $this->setError(10001);
            trigger_error(__('订单状态锁定'),E_USER_ERROR);
            return false;
            exit;
        }

        $rows = $this->db->select('SELECT i.item_id,i.addon,i.minfo,i.nums,i.sendnum,i.product_id,i.bn,i.name,i.is_type,
                    t.type_id,t.setting,t.schema_id,t.is_physical,t.ret_func FROM sdb_order_items i
                    LEFT JOIN sdb_goods_type t ON t.type_id = i.type_id
                    WHERE i.order_id='.$aData['order_id']);
        $schema = $this->system->loadModel('goods/schema');

        /**
        *    @params
        *    @$aData['send']:退货物品
        */
        if(isset($aData['send'])){
            $oCorp = $this->system->loadModel('trading/delivery');
            $aCorp = $oCorp->getCorpById($aData['logi_id']);
            if(defined('SAAS_MODE')&&SAAS_MODE){
                $date  = getdeliverycorplist();
                $aCorp = $date[$aData['logi_id']-1];
                if($aData['other_name']!=""&&isset($aData['other_name'])){
                    $aCorp['name'] = $aData['other_name'];
                }
            }
            $delivery = array(
                'money' => $aData['money'],
                'is_protect' => $aData['is_protect'],
                'delivery' => $aData['delivery'],
                'logi_id' => $aData['logi_id'],
                'logi_no' => $aData['logi_no'],
                'logi_name' => $aCorp['name'],
                'ship_name' => $aData['ship_name'],
                'ship_area' => $aData['ship_area'],
                'ship_addr' => $aData['ship_addr'],
                'ship_zip' => $aData['ship_zip'],
                'ship_tel' => $aData['ship_tel'],
                'ship_mobile' => $aData['ship_mobile'],
                'ship_email' => $aData['ship_email'],
                'memo' => $aData['reason'].$aData['memo'],
            );
        }else{
            $delivery = array(
                'money' => 0,
                'is_protect' => 'false',
                'delivery' => $aOrder['shipping']['method'],
                'logi_id' => '',
                'logi_no' => '',
                'ship_name' => $aOrder['receiver']['name'],
                'ship_area' => $aOrder['receiver']['area'],
                'ship_addr' => $aOrder['receiver']['addr'],
                'ship_zip' => $aOrder['receiver']['zip'],
                'ship_tel' => $aOrder['receiver']['tel'],
                'ship_mobile' => $aOrder['receiver']['mobile'],
                'ship_email' => $aOrder['receiver']['email'],
            );
        }
        /**
        *    @params:
        *        @$delivery['type']            :    发送类型
        *        @$delivery['status']        :    发送状态
        *        @$aUpdate['ship_status']    :
        *            @values:
        ×                4:全部退货
        *
        */
        $delivery['order_id'] = $aData['order_id'];
        $delivery['member_id'] = $aOrder['member_id'];
        $delivery['t_begin'] = time();
        $delivery['op_name'] = $aData['opname'];
        $delivery['type'] = 'return';
        $delivery['status'] = 'progress';

        //遍历订单明细
        $aBill = array();
        $nonGoods = 0;    //是否完全退货商品标识
        foreach($rows as $dinfo){    //订单退货明细
            $dinfo['addon'] = unserialize($dinfo['addon']);
            if(!isset($aData['send']) || (isset($aData['send'][$dinfo['item_id']]) && $aData['send'][$dinfo['item_id']] > 0)){
                //退货数量超出发货数量，则记录出错信息
                if($aData['send'][$dinfo['item_id']] > $dinfo['sendnum']){
                    $message .= __('商品：'.$dinfo['name'].'退货量超出已发货量');
                    $this->setError(10001);
                    trigger_error($message, E_USER_ERROR);
                    return false;
                }

                if(!isset($aData['send']) || $aData['send'][$dinfo['item_id']] == $dinfo['sendnum']){
                    $dinfo['send'] = $dinfo['sendnum'];    //本次退货数量
//                    $aUpdate['items'][$dinfo['product_id']] = 0;
                }else{
                    $nonGoods = 1;
                    $dinfo['send'] = intval($aData['send'][$dinfo['item_id']]);
//                    $aUpdate['items'][$dinfo['product_id']] = $dinfo['sendnum'] - $aData['send'][$dinfo['product_id']];
                }

                if($dinfo['ret_func'] == 1){
                    $aBill['func'][$dinfo['schema_id']][] = $dinfo;
                }else{
                    if($dinfo['is_physical']==1 || empty($dinfo['type_id'])){
                        $dinfo['is_physical'] = true;
                        $aBill['nofunc'][] = $dinfo;
                    }else{
                        $aBill['error'][] = $dinfo;    //如果虚拟商品没有退货函数，则需要建立失败退货单
                    }
                }
            }else{
                if($dinfo['sendnum'] > $aData['send'][$dinfo['item_id']]) $nonGoods = 1;
            }
        }

        $objShipping = $this->system->loadModel('trading/delivery');
        $schema = $this->system->loadModel('goods/schema');
        if($aBill['func'])    //有发货函数的实体虚拟商品
            foreach($aBill['func'] as $schema_id => $rows){
                $delivery['delivery_id'] = $objShipping->getNewNumber($delivery['type']);
                $delivery['memo'] = $aData['memo'];
                $iLoop = 0;
                foreach($rows as $dinfo){
                    ob_start();
                    $result = $schema->toreturn($dinfo['schema_id'],unserialize($dinfo['minfo']),$dinfo['addon'],$dinfo['nums'],$logs);
                    $output = ob_get_clean();
                    $delivery['memo'] = addslashes($logs."\n".$output);
                    if(!$result){
                        $aBill['error'][] = $dinfo;    //todo 是否程序终止有待讨论（现在是继续执行）
                    }else{
                        $item = array(
                                    'order_item_id' => $dinfo['item_id'],
                                    'order_id' => $aData['order_id'],
                                    'delivery_id' => $delivery['delivery_id'],
                                    'item_type' => ($dinfo['is_type']=='pkg' ? $dinfo['is_type'] : 'goods'),
                                    'product_id' => $dinfo['product_id'],
                                    'product_bn' => addslashes($dinfo['bn']),
                                    'product_name' => addslashes($dinfo['name'].$dinfo['addon']['adjname']),
                                    'adjunct' => addslashes($dinfo['addon']['adjinfo']),
                                    'number' => $dinfo['send'] );
                        if(!$objShipping->toInsertItem($item, $dinfo['is_physical'], $delivery['type'], $delivery['status'])){
                            $aBill['error'][] = $dinfo;    //todo 是否程序终止有待讨论（现在是继续执行）
                        }else{
                            $iLoop++;
                        }
                    }
                }
                if($iLoop > 0){
                    if(!$objShipping->toCreate($delivery)){
                        $this->setError(10001);
                        trigger_error('配送单据生成失败', E_USER_ERROR);
                        return false;
                    }
                }
            }

        if($aBill['nofunc']){        //实体商品
            $iLoop = 0;
            $delivery['delivery_id'] = $objShipping->getNewNumber($delivery['type']);
            foreach($aBill['nofunc'] as $dinfo){
                $item = array(
                            'order_item_id' => $dinfo['item_id'],
                            'order_id' => $aData['order_id'],
                            'delivery_id' => $delivery['delivery_id'],
                            'item_type' => ($dinfo['is_type']=='pkg' ? $dinfo['is_type'] : 'goods'),
                            'product_id' => $dinfo['product_id'],
                            'product_bn' => addslashes($dinfo['bn']),
                            'product_name' => addslashes($dinfo['name'].$dinfo['addon']['adjname']),
                            'adjunct' => addslashes($dinfo['addon']['adjinfo']),
                            'number' => $dinfo['send'] );
                if(!$objShipping->toInsertItem($item, $dinfo['is_physical'], $delivery['type'], $delivery['status'])){
                    $aBill['error'][] = $dinfo;    //todo 是否程序终止有待讨论（现在是继续执行）
                }else{
                    $iLoop++;
                }
            }
            if($iLoop > 0){
                if(!$objShipping->toCreate($delivery)){
                    $this->setError(10001);
                    trigger_error('配送单据生成失败', E_USER_ERROR);
                    return false;
                }
            }
        }

        if($aBill['error']){
            $nonGoods = 1;
            $iLoop = 0;
            $delivery['delivery_id'] = $objShipping->getNewNumber($delivery['type']);
            $delivery['status'] = 'failed';
            $delivery['money'] = 0;
            foreach($aBill['error'] as $dinfo){
                $item = array(
                            'order_item_id' => $dinfo['item_id'],
                            'order_id' => $aData['order_id'],
                            'delivery_id' => $delivery['delivery_id'],
                            'item_type' => ($dinfo['is_type']=='pkg' ? $dinfo['is_type'] : 'goods'),
                            'product_id' => $dinfo['product_id'],
                            'product_bn' => addslashes($dinfo['bn']),
                            'product_name' => addslashes($dinfo['name'].$dinfo['addon']['adjname']),
                            'adjunct' => addslashes($dinfo['addon']['adjinfo']),
                            'number' => $dinfo['send'] );
                $objShipping->toInsertItem($item, $dinfo['is_physical'], $delivery['type'], $delivery['status']);
                $iLoop++;
            }
            if($iLoop > 0){
                $objShipping->toCreate($delivery);
            }
        }

        $aPara['order_id'] = $aData['order_id'];
        $aPara['message'] = array();
        $aPara['ship_status'] = 4;
        $s = $this->fireEvent('reship',$aPara,$delivery['member_id']);    //现在没有任何退货Hook操作

        if($nonGoods) $aUpdate['ship_status'] = 3;
        else $aUpdate['ship_status'] = 4;
        $aUpdate['order_id'] = $aData['order_id'];
        $this->setShipStatus($aUpdate);

        //取得退货的具体信息，add by hujianxin
        $message_part1 = "";
        $message = "";

        $ship_status = $aUpdate['ship_status'];

        if($ship_status == 4){   //全部退货
            $message_part1 = "退货完成";
        }else if($ship_status == 3){    //部分退货
            $message_part1 = "已退货";
        }

        $message = "订单<!--order_id=".$aData['order_id']."&delivery_id=".$delivery['delivery_id']."&ship_status=".$ship_status."-->".$message_part1;

        $this->addLog($message, $this->op_id?$this->op_id:null, $this->op_name?$this->op_name:null , '退货');
        return true;
    }

    //订单确认
    function toConfirm($orderid,$op_id=null){
        $sqlString = "UPDATE sdb_orders SET confirm = 'Y',last_change_time='".time()."',acttime='".time()."' WHERE order_id = '".$orderid."'";
        $this->addLog('订单确认', $this->op_id?$this->op_id:null, $this->op_name?$this->op_name:null , '确认');
        if($this->db->exec($sqlString)){
            $this->load($orderid);
            $this->fireEvent('confirm', $this->_info);
            return $this->_info;
        }
    }

    //订单归档
    function toArchive($orderid){
        $aRet = $this->getFieldById($orderid, array('status', 'pay_status', 'ship_status'));
        if ($aRet['status'] == 'active'){
            $sqlString = "UPDATE sdb_orders SET status = 'finish',last_change_time='".time()."' WHERE order_id = '".$orderid."'";
            $this->db->exec($sqlString);
            $this->_info['order_id'] = $orderid;
            $this->addLog('订单完成', $this->op_id?$this->op_id:null, $this->op_name?$this->op_name:null , '完成');
            $this->fireEvent('archive', $orderid);
            return true;
        }else{
            $message = __('操作失败: 订单状态锁定');
            return false;
        }
    }

    function toCancel($orderid){
        $aOrder = $this->load($orderid);
        if(!$aOrder){
            $this->system->error(501);
            return false;
            exit;
        }
        if(!$this->checkOrderStatus('cancel', $aOrder)){
            $this->setError(10001);
            trigger_error(__('订单状态锁定'),E_USER_ERROR);
            return false;
            exit;
        }

        $sqlString = "UPDATE sdb_orders SET status = 'dead',last_change_time='".time()."' WHERE order_id='".$orderid."'";
        $this->db->query($sqlString);

        $this->toUnfreez($orderid);    //冻结库存解冻
        $this->_info['order_id'] = $orderid;
        $this->addLog('订单作废', $this->op_id?$this->op_id:null, $this->op_name?$this->op_name:null , '作废');
        $aPara = array('order_id'=>$orderid);
        $this->fireEvent('cancel', $aPara,$aOrder['member_id']);
        return true;
    }

    //orderid,aItem[product_id]=pid,aItem[freez]=num,,aItem[nums]=allnum,,aItem[sendnum]=sendnum
    function toUnfreez($orderid, $aItem=null){
        if($aItem == null) $aItem = $this->db->select('SELECT * FROM sdb_order_items WHERE order_id = '.$orderid);
        foreach($aItem as $aProduct){
            $store = (isset($aProduct['freez']) ? $aProduct['freez'] : $aProduct['nums'] - $aProduct['sendnum']);
            $aTmp = $this->db->selectrow("SELECT count(*) AS num FROM sdb_products WHERE product_id = ".$aProduct['product_id']
                    ." AND freez <= ".$store);
            if($aTmp['num']){
                $this->db->exec("UPDATE sdb_products SET freez = 0 WHERE product_id = ".$aProduct['product_id']);
            }else{
                $this->db->exec("UPDATE sdb_products SET freez = freez - ".$store
                    ." WHERE product_id = ".$aProduct['product_id']);
            }
        }
    }

    //删除订单，注意：确认后的订单不得删除！！！同时应删除订单相关明细记录。
    function toRemove($orderid, &$message){
        $aOrder = $this->load($orderid);
        if(!$aOrder){
            $this->system->error(501);
            return false;
            exit;
        }
/*        if($aOrder['pay_status'] > 0 || $aOrder['ship_status'] > 0 || $aOrder['confirm'] == 'Y'){
            $message = __('删除订单'.$orderid.'失败: 状态锁定');
            return false;
            exit;
        }*/
        $orderList = array('order_id'=>$orderid);
        $this->fireEvent('remove', $orderList);

//        $this->toUnfreez($orderid);    //冻结库存解冻

        $sqlString = "DELETE FROM sdb_orders WHERE order_id='".$orderid."'";
        $this->db->exec($sqlString);

        $sqlString = "DELETE FROM sdb_order_items WHERE order_id='".$orderid."'";
        $this->db->exec($sqlString);

        $sqlString = "DELETE FROM sdb_order_log WHERE order_id='".$orderid."'";
        $this->db->exec($sqlString);

        $sqlString = "DELETE FROM sdb_payments WHERE order_id='".$orderid."'";
        $this->db->exec($sqlString);

        $sqlString = "DELETE FROM sdb_refunds WHERE order_id='".$orderid."'";
        $this->db->exec($sqlString);

        $sqlString = "DELETE FROM sdb_order_pmt WHERE order_id='".$orderid."'";
        $this->db->exec($sqlString);

        $delivery_id = array(-1);
        $arr = $this->db->select("select delivery_id from  sdb_delivery WHERE order_id='".$orderid."'");
        foreach($arr as $r){
            $delivery_id[] = $r['delivery_id'];
        }

        $sqlString = "DELETE FROM sdb_delivery WHERE order_id='".$orderid."'";
        $this->db->exec($sqlString);

        $sqlString = "DELETE FROM sdb_delivery_item WHERE delivery_id in (".implode(',',$delivery_id).")";
        $this->db->exec($sqlString);

        $status = $this->system->loadModel('system/status');
        $status->count_order_to_pay();
        $status->count_order_new();

        return true;
    }

    function getOrderDecimal($number){
        $decimal_digit = $this->system->getConf('site.decimal_digit');
        $decimal_type = $this->system->getConf('site.decimal_type');
        if($decimal_digit < 3){
            $mul = 1;
            $mul = pow(10, $decimal_digit);
            switch($decimal_type){
                case 0:
                $number = number_format($number, $decimal_digit, '.', '');
                break;
                case 1:
                $number = ceil($number*$mul) / $mul;
                break;
                case 2:
                $number = floor($number*$mul) / $mul;
                break;
            }
        }
        return $number;
    }

    //新增订单留言
    function updateMessage(){
        $sqlString = "select message from sdb_orders
            where order_id = '".$this->orderId;
        $this->db->Query($sqlString);
        $this->db->next_record();
        $tmp_message = do_slash($this->db->f(message)) ;
        if(!empty($tmp_message)){
            $tmp_message .= "<br><br>" ;
        }

        $tmptime = mydate("Y-m-d H:i:s",mktime()) ;
        $sqlString = "UPDATE sdb_orders SET message= '".$tmp_message.$this->message."<br> -- ".$tmptime."',
            userrecsts = 1, recsts = 5, feedbacktime = ".time()."
            WHERE order_id='".$this->orderId."'";
        return $this->db->exec($sqlString);
    }

    //用户更新订单取消
    function updateCancel(){
        $sqlString = "UPDATE sdb_orders SET
            cancel= '".$this->canceltxt."', userrecsts = 1, feedbacktime = ".time()."
            WHERE order_id='".$this->orderId."'";
        return $this->db->exec($sqlString);
    }

    //用户更新订单已收到货
    function updateOver(){
        $sqlString = "SELECT recsts FROM sdb_orders
            WHERE order_id='".$this->orderId."'";
        $this->db->Query($sqlString);
        if ($this->db->next_record())
        {
            $tmpRecsts = $this->db->f("recsts");
            if ($tmpRecsts == 3) $tmpRecsts = 4;
            else $tmpRecsts = 1;

            $sqlString = "UPDATE sdb_orders SET recsts = $tmpRecsts, userrecsts = 1, feedbacktime = ".time()."
                WHERE order_id='".$this->orderId."'";
            return $this->db->exec($sqlString);
        }
    }

    //用户更新订单状态已付款
    function updatePayed(){
        $sqlString = "SELECT recsts FROM sdb_orders
            WHERE order_id='".$this->orderId."'";
        $this->db->Query($sqlString);
        if ($this->db->next_record())
        {
            $tmpRecsts = $this->db->f("recsts");
            if ($tmpRecsts == 1) $tmpRecsts = 4;
            else $tmpRecsts = 3;
            $sqlString = "UPDATE sdb_orders SET recsts = $tmpRecsts, userrecsts = 1, feedbacktime = ".time()."
                WHERE order_id='".$this->orderId."'";
            return $this->db->exec($sqlString);
        }
    }

    //更新用户反馈状态
    function updateUserrecsts($orderid){
        $sqlString = "UPDATE sdb_orders SET userrecsts = 0
            WHERE order_id = '".$orderid."'";
        return $this->db->exec($sqlString);
    }
    function fetchByMember($member_id,$nPage){
        return $this->db->select_f('select * from sdb_orders where disabled="false" AND member_id='.intval($member_id).' order by acttime desc',$nPage,PERPAGE);
    }

    function addLog($message,$op_id=null, $op_name=null , $behavior = '', $result = 'success'){
        if($message){
            $rs = $this->db->query('select * from sdb_order_log where 0=1');
            $sql = $this->db->getInsertSQL($rs,array(
                'order_id'=>$this->_info['order_id'],
                'op_id'=>$op_id,
                'op_name'=>$op_name,
                'behavior'=>$behavior,
                'result'=>$result,
                'log_text'=>addslashes($message),
                'acttime'=>time()
                ));
            return $this->db->exec($sql);
        }else{
            return false;
        }
    }

    function getLogs($order_id){
        return $this->db->select('SELECT * FROM sdb_order_log WHERE order_id = \''.$order_id.'\'');
    }

    function setPrintStatus($order_id,$type){
        $rs = $this->db->exec('select print_status from sdb_orders where order_id = \''.$order_id.'\'');
        $row = $rs->getArray(1);
        $print_status = $row[0]['print_status'];
        $sql = $this->db->GetUpdateSQL($rs,array('print_status'=>(intval($print_status) | intval($type))));
        return $this->db->exec($sql);
    }

    //读取订单明细/*{{{*/
    function getItemList($orderid, $strId='', $is_only_local=false){
        $sqlWhere = '';
        if($strId != ''){
            $sqlWhere .= " AND i.product_id in (".$strId.")";
        }
        if($is_only_local){//b2c-plat bryant 需求,过滤本地商品
            $sqlWhere .= ' and (g.supplier_id is null or g.supplier_id=0)';
        }
        $aGoods = $this->db->select('SELECT i.*,nums-sendnum AS send,sendnum AS resend,g.thumbnail_pic,g.goods_id,g.small_pic,c.cat_name,p.store,g.supplier_id FROM sdb_order_items i
            LEFT JOIN sdb_products p ON i.product_id = p.product_id
            LEFT JOIN sdb_goods g ON g.goods_id = p.goods_id
            LEFT JOIN sdb_goods_cat c ON g.cat_id = c.cat_id
            WHERE order_id = \''.$orderid.'\' AND i.is_type = \'goods\''.$sqlWhere);

        $aPkgs = $this->db->select('SELECT i.*,nums-sendnum AS send,sendnum AS resend,g.thumbnail_pic,g.goods_id,g.small_pic,c.cat_name,g.store FROM sdb_order_items i
            LEFT JOIN sdb_goods g ON i.product_id = g.goods_id
            LEFT JOIN sdb_goods_cat c ON g.cat_id = c.cat_id
            WHERE order_id = \''.$orderid.'\' AND i.is_type = \'pkg\''.$sqlWhere);
        return array_merge($aGoods,$aPkgs);
    }

    function toReply($data){
        $rs = $this->db->query('SELECT * FROM sdb_comments WHERE 0=1');
        $sqlString = $this->db->GetInsertSQL($rs, $data);
        return $this->db->exec($sqlString);
    }

    function getOrderConsignList($orderid){
        return $this->db->select('SELECT * FROM sdb_delivery WHERE order_id = \''.$orderid.'\'');
    }

    function getPmtList($orderid){
        return $this->db->select('SELECT * FROM sdb_order_pmt WHERE order_id = \''.$orderid.'\'');
    }

    function getCatByPid($pid){
        $row = $this->db->selectrow('SELECT cat_name FROM sdb_products p
                LEFT JOIN sdb_goods g ON p.goods_id=g.goods_id
                LEFT JOIN sdb_goods_cat c ON g.cat_id=c.cat_id
                WHERE product_id = '.intval($pid));
        return $row['cat_name'];
    }

    //编辑订单
    function toEdit($order_id, &$aData ){
        $rs = $this->db->query('SELECT * FROM sdb_orders WHERE order_id='.$order_id);
        $this->_info['order_id'] = $order_id;
        $sql = $this->db->GetUpdateSQL($rs, $aData);
        if (!$sql || $this->db->exec($sql)){
            return true;
        }else{
            return false;
        }
    }

    //读取订单明细
    function getGiftItemList($orderid){
        return $this->db->select('SELECT i.*,thumbnail_pic,image_file FROM sdb_gift_items i
                    LEFT JOIN sdb_gift f ON i.gift_id = f.gift_id WHERE order_id = \''.$orderid.'\'');
    }

    //管理员手工编辑订单
    function editOrder(&$aData, $delMark=true){
        if ($aData['order_id'] == ''){
            $orderid = $this->toInsert($aData);
        }else{
            $orderid = $aData['order_id'];
        }

        $addStore = array();
        foreach($aData['aItems'] as $key => $productId){
            $objProduct = $this->system->loadModel('goods/products');    //生成订单前检查库存
            $aStore = $objProduct->getFieldById($productId);
            if($aStore['store'] !== null && $aStore['store'] !== ''){
                $sqlString = "SELECT nums FROM sdb_order_items WHERE order_id='".$orderid."' AND product_id = '".$productId."'";
                $aRet = $this->db->selectrow($sqlString);
                $gStore = intval($aStore['store']) - intval($aStore['freez']) + intval($aRet['nums']);
                if($gStore < $aData['aNum'][$key]){
                    return false;
                }
                $addStore[$productId] = intval($aData['aNum'][$key]) - intval($aRet['nums']);
            }
        }
        reset($aData['aItems']);

        $itemsFund = 0;
        foreach($aData['aItems'] as $key => $productId){
            $aItem = array();
            $aItem['order_id'] = $orderid;
            $aItem['product_id'] = $productId;
            $aItem['price'] = $aData['aPrice'][$key];
            $aItem['nums'] = $aData['aNum'][$key];
            $aItem['amount'] = $aItem['price'] * $aItem['nums'];
            //todo 库存冻结量,库存是否足够 / 商品配件
            if($this->exsitItem($orderid, $productId)){
                $aProduct['edit'][] = $productId;
                $this->editItem($aItem);
            }else{
                $objProduct = $this->system->loadModel('goods/products');
                $aPdtinfo = $objProduct->getgetFieldById($productId, array('goodsid, bn, name, cost, score, weight'));
                $aPdtinfo['weight'] *= $aItem['nums'];

                $objGoods = $this->system->loadModel('trading/goods');
                $aGoodsinfo = $objGoods->getFieldById($aPdtinfo['goodsid'], array('type_id'));
                $aItem = array_merge($aItem, $aPdtinfo, $aGoodsinfo);
                $this->addItem($aItem);
            }

            $itemsFund += $aItem['amount'];
            if(isset($addStore[$productId])){
                $this->db->exec("UPDATE sdb_products SET freez = freez + ".$addStore[$productId]." WHERE product_id = ".$productId);
                $this->db->exec("UPDATE sdb_products SET freez = ".$addStore[$productId]." WHERE product_id = ".$productId." AND freez IS NULL");
            }
        }
        if($delMark){
            $this->execDelItems($orderid, $aProduct['edit']);
        }else{
            $itemsFund = $this->getCostItems($orderid);
        }

        if($aData['is_protect'] != 'true') $aData['cost_protect'] = 0;
        if($aData['is_tax'] != 'true') $aData['cost_tax'] = 0;
        $aData['cost_item'] = $itemsFund;
        $aData['total_amount'] = $itemsFund + $aData['cost_freight'] + $aData['cost_protect'] + $aData['cost_payment'] + $aData['cost_tax'] - $aData['discount'] - $aData['pmt_amount'];
        $rate = $this->getFieldById($orderid, array('cur_rate'));
        $aData['final_amount'] = $aData['total_amount'] * $rate['cur_rate'];
        $shipping = $this->system->loadModel('trading/delivery');
        $aShip = $shipping->getDlTypeById($aData['shipping_id']);
        $aData['shipping'] = $aShip['dt_name'];
        if($this->toEdit($orderid, $aData )){
            $this->addLog('订单编辑', $this->op_id?$this->op_id:null, $this->op_name?$this->op_name:null , '编辑' );
            return true;
        }else{
            return false;
        }
        return $aMsg;
    }

    function exsitItem($orderid, $productid){
        $sqlString = "SELECT * FROM sdb_order_items WHERE order_id='".$orderid."' AND product_id = '".$productid."'";
        $aRet = $this->db->select($sqlString);
        if(count($aRet)) return true;
        else return false;
    }

    function editItem($aData){
        $rs = $this->db->query("SELECT * FROM sdb_order_items WHERE order_id='".$aData['order_id']."' AND product_id = ".intval($aData['product_id']));
        $sqlString = $this->db->GetUpdateSQL($rs, $aData);
        if(!$sqlString || $this->db->exec($sqlString)){
            return true;
        }else{
            return false;
        }
    }

    function addItem($aData){
        $rs = $this->db->query('SELECT * FROM sdb_order_items WHERE 0=1');
        $sqlString = $this->db->GetInsertSQL($rs, $aData);
        $this->db->exec($sqlString);
        return true;
    }

    function execDelItems($orderid, &$aItems){
        $sqlString = "DELETE FROM sdb_order_items WHERE order_id = '".$orderid."' AND product_id NOT IN('".implode("','", $aItems)."')";
        $this->db->exec($sqlString);
    }

    //根据会员ID返回订单数量
    function getOrderNumbyMemberId($member_id=0){
        $sqlString = "SELECT COUNT(*) AS num FROM sdb_orders WHERE member_id = ".$member_id;
        $data = $this->db->selectrow($sqlString);
        return $data['num'];
    }

    //获取会员的定单列表
    function getOrderListByMemberId($nMId){
        return $this->db->select("SELECT order_id,status,pay_status,ship_status,total_amount,createtime FROM sdb_orders WHERE member_id=".$nMId);
    }

    //前台订单支付更换支付方式
    function chgPayment($orderid, $paymentid, $paymoney,$chgpayment=0){
        if($aOrder = $this->getFieldById($orderid, array('cost_protect','cost_freight','cost_tax','cost_payment','cost_item','pmt_amount','discount','payment','total_amount','payed','cost_payment','currency','cur_rate'))){
            if($aOrder['payment'] != $paymentid){
                $payment = $this->system->loadModel('trading/payment');
                if($paymentid > 0){
                    $aPayment = $payment->getPaymentById($paymentid);
                }else{
                    $aPayment['fee'] = 0;
                }
                if (!$chgpayment)
                    $aData['payment'] = $paymentid;

                $total_fee = 0;
                $total_fee = 0;
                $payedamount = $payment->getSuccOrderBillList($orderid);
                if ($payedamount){
                    foreach($payedamount as $pk => $pv){
                        $totalPayedMoney +=$pv['money'];//已支付总金额
                        $totalPayFee +=$pv['paycost'];//已支付费率
                    }
                }
                $chgMoney = $totalPayedMoney - $totalPayFee;
                $amountExceptPay = $aOrder['cost_protect']+$aOrder['cost_freight']+$aOrder['cost_tax']+$aOrder['cost_item']-$aOrder['discount'];
                $amountPayment = ($amountExceptPay - $chgMoney) * $aPayment['fee'];
                $total_amount = $amountExceptPay + $amountPayment + $totalPayFee;

                $aData['cost_payment'] = $totalPayFee + $amountPayment;
                $aData['total_amount'] = $total_amount;
                $aData['final_amount'] = $total_amount * $aOrder['cur_rate'];
                $_POST['cur_money'] = ($aData['total_amount']-$chgMoney) * $aOrder['cur_rate'];
                $rs = $this->db->exec('SELECT * FROM sdb_orders WHERE order_id =\''.$orderid.'\'');
                $sql = $this->db->getUpdateSQL($rs, $aData);
                $this->db->exec($sql);
                return $aData['total_amount']-$chgMoney;
            }else{
                return $paymoney;
            }
        }else{
            return false;
        }
    }

    //新增订单商品
    function insertOrderItem($orderid, $goodsbn, $num){

        $aOrder = $this->getFieldById($orderid, array('member_id'));
        $aProduct = $this->db->selectrow('SELECT p.bn,p.name,g.score,p.product_id,type_id,p.price,p.pdt_desc FROM sdb_products p
                    LEFT JOIN sdb_goods g ON p.goods_id = g.goods_id
                    WHERE p.bn=\''.$goodsbn.'\' AND g.disabled = \'false\' AND g.marketable = \'true\'');
        if(!$aProduct['product_id']) return 'none';
        if($aOrder['member_id']){
            $oMember = $this->system->loadModel('member/member');
            $aMember = $oMember->getFieldById($aOrder['member_id'], array('member_lv_id'));
            $mPrice = $this->db->selectrow('SELECT price AS mprice FROM sdb_goods_lv_price
                    WHERE product_id='.intval($aProduct['product_id']).' AND level_id = '.intval($aMember['member_lv_id']));
            if(!$mPrice['mprice']){
                $mPrice['mprice'] = $aProduct['price'];
            }
        }else{
            $oLevel = $this->system->loadModel('member/level');
            $aLevel = $oLevel->getList('discount', array('default_lv'=>1),0,-1);
            $mPrice['mprice'] = $aProduct['price'] * ($aLevel[0]['discount'] ? $aLevel[0]['discount'] : 1);
        }

        $aData['product_id'] = $aProduct['product_id'];
        $aData['bn'] = $aProduct['bn'];
        $aData['name'] = addslashes($aProduct['name'].($aProduct['pdt_desc'] ? '('.$aProduct['pdt_desc'].')' : ''));
        $aData['price'] = $mPrice['mprice'];
        $aData['order_id'] = $orderid;
        $aData['amount'] = $aData['price'] * $num;
        $aData['nums'] = $num;
        $aData['score'] = $aProduct['score'];
        $aData['type_id'] = $aProduct['type_id'];
        if($this->db->selectrow('SELECT * FROM sdb_order_items WHERE order_id=\''.$orderid.'\' AND product_id='.$aData['product_id'])){
            return 'exist';
        }
        $rs = $this->db->query('select * from sdb_order_items where 0=1');
        $sqlString = $this->db->GetInsertSQL($rs, $aData);
        return $this->db->exec($sqlString);
    }

    function saveMarkText($orderid, $aData){
        $aTmp['mark_text'] = htmlspecialchars($aData['mark_text']);
        $aTmp['mark_type'] = htmlspecialchars($aData['mark_type']);
        $rs = $this->db->exec('SELECT * FROM sdb_orders WHERE order_id='.$orderid);
        $sql = $this->db->getUpdateSql($rs,$aTmp);
        if(!$sql || $this->db->exec($sql)){
            return true;
        }else{
            return false;
        }
    }
    function fireEvent($action,$data,$memberid){
        if (!$data['email'])
            $data['email'] = $data['ship_email'];
        parent::fireEvent($action,$data,$memberid);
    }

    function getOrderLogList($orderid , $page, $pageLimit){
        return $this->db->select_f( 'SELECT * FROM sdb_order_log WHERE order_id = '.$orderid , $page , $pageLimit);
    }

    function recycle($filter){
        $rs = parent::recycle($filter);
        if($rs)
            foreach( $filter['order_id'] as $oid ){
                $this->_info['order_id'] = $oid;
                $this->addLog('订单删除', $this->op_id?$this->op_id:null, $this->op_name?$this->op_name:null , '删除');
            }
        return $rs;
    }

    function active($filter){
        $rs = parent::active($filter);
        if($rs)
            foreach( $filter['order_id'] as $oid ){
                $this->_info['order_id'] = $oid;
                $this->addLog('订单还原', $this->op_id?$this->op_id:null, $this->op_name?$this->op_name:null , '还原');
            }
        return $rs;
    }

    function addOrderMsg($aData){
        $aRs = $this->db->query('SELECT * FROM sdb_message WHERE 0');
        $sSql = $this->db->getInsertSql($aRs,$aData);
        return $this->db->exec($sSql);
    }

    function getCostItems($order_id){
        $aRet = $this->getItemList($order_id);
        $money = 0;
        foreach((array)$aRet as $row){
            $money += $row['amount'];
        }
        return $money;
    }

    //判断订单明细是否还有本地商品：0全部本地，1部分本地，2无本地
    function checkLocalItem(){
        ;
    }

    function alterOrderLog($logs){
        if(!empty($logs)){
            $message_part = "";

            foreach($logs as $k=>$log){
                $prefix = "<!--";
                $postfix = "-->";
                $pattern = $prefix."[\s\S]*?".$postfix;

                $matches = array();
                if(preg_match("/".$pattern."/",$log['log_text'],$matches)){
                    $match_text = $matches[0];
                    $match_text = str_replace($prefix,"",$match_text);
                    $match_text = str_replace($postfix,"",$match_text);

                    parse_str($match_text,$arr);
                    $delivery_id = $arr['delivery_id'];
                    $order_id = $arr['order_id'];
                    $ship_status = $arr['ship_status'];

                    $delivery_item_info = $this->db->select(
                        "SELECT a.number,b.name
                            FROM sdb_delivery_item AS a,sdb_order_items AS b
                            WHERE a.delivery_id=".$delivery_id.
                                " AND b.order_id=".$order_id.
                                " AND a.product_bn=b.bn"
                    );
                    $delivery_item_info = json_encode($delivery_item_info);
                    $delivery_item_info = str_replace("'","&#039;",$delivery_item_info);

                    if($ship_status == "1" || $ship_status == "4"){
                        $message_part = "全部";
                    }else if($ship_status == "2" || $ship_status == "3"){
                        $message_part = "部分";
                    }

                    $log_text = "<a href='javascript:void(0)' onclick='show_delivery_item(this,\"".$delivery_id."\",".($delivery_item_info).")' title='点击查看详细' style='color:#003366; font-weight:bolder; text-decoration:underline;'>".$message_part."商品</a>";

                    $logs[$k]['log_text'] = preg_replace("/".$pattern."/",$log_text,$log['log_text']);
                }
            }

            return $logs;
        }else{
            return array();
        }
    }
    function updateExtend($orderid,$extend){
        $data['extend'] = serialize($extend);
        $rs=$this->db->query('select extend from sdb_orders where order_id=\''.$orderid.'\'');
        $sSql=$this->db->getUpdateSql($rs,$data);
        return $this->db->exec($sSql);
    }
    
    function combine_payment_id(&$data){
    	foreach($data['data'] as $key=>$item ){
    		$array_order_id[$key] = $item['order_id']; 
    	}
    	$string_order_id = implode(',',$array_order_id);
    	$rows = $this->db->select($sql = "select order_id,payment_id from sdb_payments where order_id in($string_order_id) order by order_id,payment_id ASC");
    	foreach((array) $rows as $row){
    		$array_payment_id[$row['order_id']] = $row['payment_id'];
    	}
		foreach($data['data'] as $key=>$item ){
			$data['data'][$key]['payment_id'] = $array_payment_id[$item['order_id']];
		}    	
    }
    
    function combine_logi_no(&$data){
    	foreach($data['data'] as $key=>$item ){
    		$array_order_id[$key] = $item['order_id']; 
    	}
    	$string_order_id = implode(',',$array_order_id);
    	$rows = $this->db->select($sql = "select order_id,logi_no from sdb_delivery where order_id in($string_order_id) order by order_id ASC");
    	foreach((array) $rows as $row){
    		$array_payment_id[$row['order_id']] = $row['logi_no'];
    	}
		foreach($data['data'] as $key=>$item ){
			$data['data'][$key]['logi_no'] = $array_payment_id[$item['order_id']];
		}    	
    }
    
    function front_search($member_id,$key){
    	$key = mysql_escape_string($key);
    	return $this->db->select('select * from sdb_orders where disabled="false" AND member_id='.intval($member_id).' AND ( order_id like \'%'.$key.'%\' OR ship_name like \'%'.$key.'%\')  order by acttime desc');
    }
    
}
?>
