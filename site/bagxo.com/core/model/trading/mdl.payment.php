<?php
define('PAY_FAILED',-1);
define('PAY_TIMEOUT',0);
define('PAY_SUCCESS',1);
define('PAY_CANCEL',2);
define('PAY_ERROR',3);
define('PAY_PROGRESS',4);
define('PAY_INVALID',5);
define('PAY_MANUAL',0);

###IP 要用ip2long转化后存储，反之取出时要用long2ip还原###

require_once('shopObject.php');

class mdl_payment extends shopObject{
    //var $__setting;
    var $M_OrderId;        //    订单的id---支付流水号
    var $M_OrderNO;        //    订单号
    var $M_Amount;        //    订单金额        小数点后保留两位，如10或12.34
    var $M_Def_Amount;        //    订单本位币金额        小数点后保留两位，如10或12.34
    var $M_Currency;    //    支付币种
    var $M_Remark;        //    订单备注
    var $M_Time;        //    订单生成时间
    var $M_Language;    //    语言选择        表示商家使用的页面语言
    var $R_Name;        //    收货人姓名    订单支付成功后货品收货人的姓名
    var $R_Address;        //    收货人住址    订单支付成功后货品收货人的住址
    var $R_Postcode;    //    收货人邮政编码    订单支付成功后货品收货人的住址所在地的邮政编码
    var $R_Telephone;    //    收货人联系电话    订单支付成功后货品收货人的联系电话
    var $R_Mobile;        //    收货人移动电话    订单支付成功后货品收货人的移动电话
    var $R_Email;        //    收货人电子邮件地址    订单支付成功后货品收货人的邮件地址
    var $P_Name;        //    付款人姓名    支付时消费者的姓名
    var $P_Address;        //    付款人住址    进行订单支付的消费者的住址
    var $P_PostCode;    //    付款人邮政编码        进行订单支付的消费者住址的邮政编码
    var $P_Telephone;    //    付款人联系电话     进行订单支付的消费者的联系电话
    var $P_Mobile;        //    付款人移动电话     进行订单支付的消费者的移动电话
    var $P_Email;        //    付款人电子邮件地址     进行订单支付的消费者的电子邮件地址

//START
    var $adminCtl = 'order/payment';
    var $idColumn='payment_id';
    var $textColumn = 'payment_id';
    var $defaultCols = 'payment_id,money,currency,order_id,paymethod,member_id,account,bank,pay_account,status';
    var $defaultOrder = array('payment_id','DESC');
    var $tableName = 'sdb_payments';
    var $plugin_case = LOWER_CASE;

    function getColumns(){
        return array(
                    'payment_id'=>array('label'=>'支付单号','class'=>'span-3'),    /* 支付流水号 */
                    'order_id'=>array('label'=>'订单号','class'=>'span-3'),    /* 订单号 */
                    'member_id'=>array('label'=>'会员用户名','class'=>'span-2','type'=>'object:member'),    /* 会员id */
                    'account'=>array('label'=>'收款账户','class'=>'span-3'),    /* 收款账户 */
                    'bank'=>array('label'=>'收款银行','class'=>'span-3'),    /* 支付银行 */
                    'pay_account'=>array('label'=>'支付账户','class'=>'span-3'),    /* 支付账户 */
                    'currency'=>array('label'=>'支付币别','class'=>'span-2','type'=>'currency'),    /* 支付币别 */
                    'money'=>array('label'=>'支付金额','class'=>'span-2','type'=>'money'),    /* 本次支付金额 */
                    'paycost'=>array('label'=>'支付网关费用','class'=>'span-3','type'=>'money'),    /* 支付花费 */
                    'pay_type'=>array('label'=>'支付类型','class'=>'span-3','type'=>'ptype'),    /* 支付类型 */
                    'paymethod'=>array('label'=>'支付方式','class'=>'span-3'),    /* 支付方式名称冗余 */
                    'op_id'=>array('label'=>'操作员','class'=>'span-3','type'=>'object:operator'),    /* 管理员id */
                    'ip'=>array('label'=>'支付时ip地址','class'=>'span-3','type'=>'ipaddr'),    /* 支付时ip地址 */
                    't_begin'=>array('label'=>'开始支付时间','class'=>'span-3','type'=>'time'),    /* 开始支付时间 */
                    't_end'=>array('label'=>'支付完成时间','class'=>'span-3','type'=>'time'),    /* 支付结束时间 */
                    'status'=>array('label'=>'支付状态','class'=>'span-2','type'=>'status')    /* succ 支付成功
                                                            failed 支付失败
                                                            cancel 未支付
                                                            error 参数异常
                                                            progress 处理中
                                                            timeout 超时
                                                            ready 准备中 */
                );
    }

    function modifier_status(&$rows){
        $status = array('succ'=>'支付成功',
                    'failed'=>'支付失败',
                    'cancel'=>'未支付',
                    'error'=>'参数异常',
                    'progress'=>'处理中',
                    'timeout'=>'超时',
                    'ready'=>'准备中',
                    );
        foreach($rows as $k=>$v){
            $rows[$k] = $status[$v];
        }
    }

    function modifier_ptype(&$rows){
        $status = array('online'=>'在线支付',
                    'offline'=>'线下支付',
                    'deposit'=>'预存款支付',
                    'recharge'=>'预存款充值',
                    );
        foreach($rows as $k=>$v){
            $rows[$k] = $status[$v];
        }
    }

    function getFilter($p){
        $return['payment']=$this->getMethods();
        return $return;
    }

    function edit($aDetail){
        $rPayment=$this->db->query('select * from sdb_payments where payment_id='.$aDetail['payment_id']);
        unset($aDetail['payment_id']);
        $sSql=$this->db->GetUpdateSQL($rPayment,$aDetail);
        return (!$sSql || $this->db->exec($sSql));
    }

    function getOrderBillList($orderid){
        return $this->db->select('SELECT * FROM sdb_payments WHERE order_id = '.$orderid);
    }

//END

    function getMethods($type=''){
        if($type=="online"){
            $sql = ' AND pay_type NOT IN(\'OFFLINE\',\'DEPOSIT\')';
        }
        return $this->db->select('SELECT * FROM sdb_payment_cfg WHERE disabled = \'false\''.$sql.' order by orderlist desc',PAGELIMIT);
    }

    function setValue($nPayId,$sField,$sValue){
        if($sField && $sValue && $nMemId){
            $aRs = $this->db->query('SELECT * FROM sdb_payment_cfg WHERE id='.$nPayId);
            $sSql = $this->db->GetUpdateSql($aRs,array($sField=>$sValue));
            return (!$sSql || $this->db->exec($sSql));
        }
        return false;
    }

    function loadMethod($payPlugin){
        require_once(PLUGIN_DIR.'/payment/pay.'.$payPlugin.'.php');

        $className = 'pay_'.$payPlugin;
        $method = new $className($this->system);
        return $method;
    }

    function getPlugins(){
        $dir = PLUGIN_DIR.'/payment/';
        $disabled = 0;
        if (file_exists($dir.'disabled_payments.txt')){
            $disabledPayment = file($dir.'disabled_payments.txt');
            if (count($disabledPayment)>0){
                foreach($disabledPayment as $k => $v){
                    $disabledPayment[$k]=trim($v);
                }
                $disabled=1;
            }
        }
        if ($handle = opendir($dir)) {
            $i=50000;
            while (false !== ($file = readdir($handle))) {
                if(is_file($dir.$file) && substr($file,0,4)=='pay.' && substr($file,-10,6)!='server' ){
                    $payName = substr($file,4,-4);
                    if($payName == strtolower($payName)){
                        include_once($dir.$file);
                        $class_vars = 'pay_'.$payName;
                        $o = new $class_vars;
                        $class_vars = get_object_vars($o);
                        unset($class_vars['system']);
                        $key = $class_vars['orderby']?$class_vars['orderby']:$i;
                        if ($disabled){
                            if (!in_array(trim($payName),$disabledPayment)){
                               $return[$key] = $class_vars;
                               $return[$key]['payment_id'] = $payName;
                            }
                        }
                        else{
                            $return[$key] = $class_vars;
                            $return[$key]['payment_id'] = $payName;
                        }
                        $i++;
                    }
                }
            }
            closedir($handle);
        }
        ksort($return);
        reset($return);
        return $return;
    }

    function getSupportCur(&$oPayType){
        if(!is_object($oPayType)) return false;
        return $oPayType->supportCurrency;
    }

    function getByCur($cur=-1, $type=''){ //注：以前的 getList 现在改为了 getPlugins
        if($cur == -1 || empty($cur)){
            $defaultMark = 1;
            $cur = -1;
        }else{
            $oCur = $this->system->loadModel('system/cur');
            $aCur = $oCur->getcur($cur, true);
            if($aCur['def_cur'] == "true"){
                $defaultMark = 1;
            }else{
                $defaultMark = 0;
            }
        }
        if($type=="online"){
            $sql = ' AND pay_type NOT IN(\'OFFLINE\',\'DEPOSIT\')';
        }
        $rows = $this->db->select('SELECT * FROM sdb_payment_cfg WHERE disabled = \'false\''.$sql.' ORDER BY orderlist desc');
        $dir = PLUGIN_DIR.'/payment/';
        foreach($rows as $k=>$row){
            if(is_file($dir.'pay.'.$row['pay_type'].'.php')){
                include_once($dir.'pay.'.$row['pay_type'].'.php');
                $class_name = 'pay_'.$row['pay_type'];
                $o = new $class_name;
                $pInfo = get_object_vars($o);
                unset($pInfo['system']);
                if($cur!=-1 && is_array($pInfo['supportCurrency'])){
                    $sptCur = array();
                    foreach($pInfo['supportCurrency'] as $s_cur=>$s){
                        $sptCur[strtolower($s_cur)] = 1;
                    }
                    if(!isset($sptCur[strtolower($cur)]) && !isset($sptCur['all'])){
                        if($defaultMark && isset($sptCur['default'])){;}else{
                            unset($rows[$k]);
                        }
                        continue;
                    }
                }
                $rows[$k] = array_merge($rows[$k],$pInfo);
                $rows[$k]['custom_name'] = $rows[$k]['custom_name']?$rows[$k]['custom_name']:$rows[$k]['name'];
                $i++;
            }else{
                unset($rows[$k]);
            }
        }
        return $rows;
    }

    function getPluginsArr($strKey=false){//由插件的缩写，和插件的名称组成的一个二维数组
        $aTemp = $aPlugin = array();
        $aTemp = $this->getPlugins();
        if($aTemp){
            if(!$strKey){
                foreach($aTemp as $val){
                    $aPlugin[] = array('pid'=>$val['payment_id'],'name'=>$val['name'],'cur'=>$val['supportCurrency']);
                }
            }else{
                foreach($aTemp as $val){
                    $aPlugin[] = array($val['payment_id'],$val['name']);
                }
            }
        }
        return $aPlugin;
    }

    function gen_id(){
        $i = rand(0,9999);
        do{
            if(9999==$i){
                $i=0;
            }
            $i++;
            $payment_id = time().str_pad($i,4,'0',STR_PAD_LEFT);
            $row = $this->db->selectrow('select payment_id from sdb_payments where payment_id =\''.$payment_id.'\'');
        }while($row);
        return $payment_id;
    }

    //生成付款单
    function toCreate(){
        $this->payment_id = $this->gen_id();
        $this->t_begin = time();
        $this->t_end = time();
        $this->ip = remote_addr();

        //如何网关实际是不支付外币交易的，但又选择了外币支付，则支付单中的实际支付金额，就是本位币金额。
        if(!$this->cur_trading && $this->currency != 'CNY'){
            $this->cur_money = $this->money;
        }

        $oCur = $this->system->loadModel('system/cur');
        if($payCfg = $this->db->selectrow('SELECT pay_type,fee,custom_name FROM sdb_payment_cfg WHERE id='.intval($this->payment))){
            $this->paycost = $this->money * $payCfg['fee'] / (1+$payCfg['fee']);
            $this->paycost = $oCur->formatNumber($this->paycost);
            $this->paymethod = $payCfg['custom_name'];
        }
        $aRs = $this->db->query('SELECT * FROM sdb_payments WHERE 0=1');
        $sSql = $this->db->GetInsertSQL($aRs,$this);
        if($this->db->exec($sSql)){
            return $this->payment_id;
        }else{
            return false;
        }
    }

    function getById($paymentId){
        $aTemp = $this->db->selectrow('SELECT * FROM sdb_payments WHERE payment_id=\''.$paymentId.'\'');
        if($aTemp['payment_id']) return $aTemp;
        else return false;
    }

    //设置支付单的状态（包括前台支付，充值，后台支付）
    function setPayStatus($paymentId,$status,&$payInfo){
        if(!$paymentId){
            $this->setError(10001);
            trigger_error(__('单据号传递出错'),E_USER_ERROR);
            return false;
            exit;
        }
        $aPayInfo = $this->getById($paymentId);
        if(!$aPayInfo){
            $this->setError(10001);
            trigger_error(__('支付记录不存在，可能参数传递出错'),E_USER_ERROR);
            return false;
            exit;
        }
        if($aPayInfo['status'] == 'succ'){    //如果已经支付成功，则返回;##防止重复刷新提交
            return true;
        }
        if($aPayInfo['status'] == 'progress' && $status == PAY_PROGRESS){    //如果已经支付中，则返回;
            return true;
        }
        if($aPayInfo['pay_type'] == 'recharge' && $aPayInfo['bank'] == 'deposit'){    //如果用预存款支付，充值预存款的情况;
            $payInfo['memo'] .= __('#不能用预存款支付来充值预存款！');
            $status = PAY_FAILED;
        }
        if($payInfo['cur_money'] && $aPayInfo['cur_money'] != $payInfo['money']){
            $status = PAY_ERROR;
            $payInfo['memo'] .= __('#实际支付金额与支付单中的金额不一致！');
        }

        switch($status){
            case PAY_FAILED:
                $payInfo['status'] = 'failed';    //支付网关传回的状态为支付失败状态
                break;
            case PAY_TIMEOUT:
                $payInfo['status'] = 'timeout';    //
                break;
            case PAY_PROGRESS:    //处理中，类似于支付到支付宝; 已经支付到中间结构，现在还没有已发货通知接口
                $aPayInfo['pay_assure'] = true;     //支付到担保交易标识
                if($this->onSuccess($aPayInfo, $payInfo['memo'])){
                    $payInfo['status'] = 'progress';
                }else{
                    $payInfo['status'] = 'error';
                }
                break;
            case PAY_SUCCESS:
                if($this->onSuccess($aPayInfo, $payInfo['memo'])){
                    $payInfo['status'] = 'succ';        //支付网关返回支付成功标识
                }else{
                    $payInfo['status'] = 'error';
                }
                break;
            case PAY_CANCEL:
                $payInfo['status'] = 'cancel';    //
                break;
            case PAY_ERROR:
                $payInfo['status'] = 'error';    //除了PAY_FAILED的都是错误
                break;
            case PAY_REFUND_SUCCESS:
                $Rs=$this->db->selectrow('select order_id from sdb_payments where payment_id=\''.$paymentId.'\'');
                if ($Rs){
                    $_POST['order_id'] = $Rs['order_id'];
                    if ($this->op->opid){
                        $_POST['opid'] = $this->op->opid;
                        $_POST['opname'] = $this->op->loginName;
                    }
                    else{
                        $opeRs = $this->db->selectrow('select op_id,username from sdb_operators where status=1 and super=1');
                        $_POST['opid'] = $opeRs['op_id'];
                        $_POST['opname'] = $opeRs['username'];
                    }
                    $order=$this->system->loadModel('trading/order');
                    if ($order->refund($_POST)){
                        $this->setError(10001);
                        return true;
                    }
                    else{
                        $this->setError(10002);
                        return false;
                    }
                }
                else
                    return false;
                break;
        }

        $payInfo['t_end'] = time();
        $aRs = $this->db->query('SELECT * FROM sdb_payments WHERE payment_id=\''.$paymentId.'\'');
        $sSql = $this->db->GetUpdateSql($aRs,$payInfo);
        if(!$sSql || $this->db->exec($sSql)){
            return true;
        }else{
            return false;
        }
    }

    function onSuccess($info, &$message){
        if($info['pay_type'] =='recharge'){
            $oCur = $this->system->loadModel('system/cur');
            $aCur = $oCur->getcur($info['currency']);
            $info['money'] = $info['money'] - $info['paycost'];
            if($aCur['def_cur'] == 'false'){
                $info['money'] /= $aCur['cur_rate'];
            }
            $info['money'] = $oCur->formatNumber($info['money']);
            $message .= '预存款充值：支付单号{'.$info['payment_id'].'}';
            $advance = $this->system->loadModel('member/advance');
            if(!$info['pay_assure'])//非担保交易状态
                return $advance->add($info['member_id'],$info['money'],$message,$message, $info['payment_id'], '' ,$info['paymethod'] , '在线充值');
            else
                return true;
        }else{
            $order = $this->system->loadModel('trading/order');
            return $order->payed($info, $message);
        }
    }

    function getAccount(){
        $query = 'SELECT DISTINCT bank, account FROM sdb_payments WHERE status="succ"';
        return $this->db->select($query);
    }

    //wzp(2007-9-12)
    function orderbill($nStart,$nLimit,$aParame){
        if(!$limit)$limit = 20;
        foreach($aParame as $k=>$v){
            if($k=='t_begin' && $v!='')$sTmp.=' and '.$k.'>="'.$v.'"';
            elseif($k=='t_end' && $v!='')$sTmp.=' and '.$k.'<="'.$v.'"';
            elseif($v!='')$sTmp.=' and '.$k.'="'.$v.'"';
        }
        $aData=$this->db->selectRow('select count(*) as total from sdb_payments p,sdb_members m where p.member_id=m.member_id and type="orderpay"'.$sTmp);
        $aData['main']=$this->db->selectLimit('select p.*,m.name as m_name from sdb_payments p,sdb_members m where p.member_id=m.member_id and type="orderpay"'.$sTmp,intval($nLimit),intval($nStart),false,true);
        return $aData;
    }
    function billDetail($nID){
        return $this->db->selectRow('select p.*,m.name as m_name from sdb_payments p,sdb_members m where p.member_id=m.member_id and type="orderpay" and payment_id=\''.$nID.'\'');
    }
    function billDetailEdit($nID,$aData){
        $aRs = $this->db->query('SELECT * FROM sdb_payments WHERE type="orderpay" and payment_id=\''.$nID.'\'');
        $sSql = $this->db->GetUpdateSql($aRs, $aData);
        return (!$sSql || $this->db->exec($sSql));
    }
    function refund($nStart,$nLimit,$aParame){
        if(!$limit)$limit = 20;
        foreach($aParame as $k=>$v){
            if($k=='t_begin' && $v!='')$sTmp.=' and '.$k.'>="'.$v.'"';
            elseif($k=='t_end' && $v!='')$sTmp.=' and '.$k.'<="'.$v.'"';
            elseif($v!='')$sTmp.=' and '.$k.'="'.$v.'"';
        }
        $aData=$this->db->selectRow('select count(*) as total from sdb_payments p,sdb_members m where p.member_id=m.member_id and type="orderrefund"'.$sTmp);
        $aData['main']=$this->db->selectLimit('select p.*,m.name as m_name from sdb_payments p,sdb_members m where p.member_id=m.member_id and type="orderrefund"'.$sTmp,intval($nLimit),intval($nStart),false,true);
        return $aData;
    }
    function refundDetail($nID){
        return $this->db->selectRow('select p.*,m.name as m_name from sdb_payments p,sdb_members m where p.member_id=m.member_id and type="orderrefund" and payment_id=\''.$nID.'\'');
    }
    function refundDetailEdit($nID,$aData){
        $aRs = $this->db->query('SELECT * FROM sdb_payments WHERE type="orderrefund" and payment_id=\''.$nID.'\'');
        $sSQL = $this->db->GetUpdateSql($aRs, $aData);
        if (!$sSQL || $this->db->exec($sSQL)) {
            return true;
        } else {
            return false;
        }
    }
    //后台管理部分
    function getPaymentById($id){
        return $this->db->selectrow('SELECT * FROM sdb_payment_cfg WHERE id='.intval($id));
    }
    function insertPay($aData,&$msg){
        if($aData['pay_type']){
            $obj = $this->loadMethod($aData['pay_type']);
            if($obj){
                $aField = $obj->getfields();
                $aTemp = array();
                foreach($aField as $key=>$val){
                    $aTemp[$key] = trim($aData[$key]);
                    foreach($val['extendcontent'] as $k => $v){
                        $aTemp[$v['property']['name']]=$aData[$v['property']['name']];
                    }
                }
                $aData['config'] = serialize($aTemp);
            }
            $aRs = $this->db->query('SELECT * FROM sdb_payment_cfg WHERE 0');
            $sSql = $this->db->GetInsertSql($aRs,$aData);
            if (!$sSql || $this->db->exec($sSql)){
                $msg = __("保存成功！");
                return true;
            }else{
                $msg = __("数据库操作失败！");
                return false;
            }
        }else{
            $msg = __('参数丢失，请选择支付类型！');
            return false;
        }
    }
    function updatePay($aData,&$msg){
        if(!$aData['pay_id']){
            $msg = __('参数丢失');
            return false;
        }
        $obj = $this->loadMethod($aData['pay_type']);
        if($obj){
            $aField = $obj->getfields();
            $aTemp = array();
            $d = $this->db->selectrow('SELECT * FROM sdb_payment_cfg WHERE id='.$aData['pay_id']);
            $d_config = unserialize($d['config']);
            foreach($aField as $key=>$val){
                if ($aData[$key]<>''){
                    if(strstr(strtolower($key), 'file') && !$aData[$key] && $d_config[$key]){
                        $aTemp[$key] = trim($d_config[$key]);
                    }else{
                        $aTemp[$key] = trim($aData[$key]);
                    }
                }
                else
                    $aTemp[$key]=trim($d_config[$key]);
                if ($val['extendcontent']){
                    foreach($val['extendcontent'] as $k => $v){
                        if ($aData[$v['property']['name']]){
                            $aTemp[$v['property']['name']]=$aData[$v['property']['name']];
                        }
                        else{
                           $aTemp[$v['property']['name']]=$dt_config[$v['property']['name']];
                        }
                    }
                }

            }
            $aData['config'] = serialize($aTemp);
        }
        $aRs = $this->db->query('SELECT * FROM sdb_payment_cfg WHERE id='.$aData['pay_id']);
        $sSql = $this->db->GetUpdateSql($aRs,$aData);
        return (!$sSql || $this->db->exec($sSql));
    }
    function deletePay($sId=null){
        if($sId){
            $sSql = 'DELETE FROM sdb_payment_cfg WHERE id in ('.$sId.')';
            return (!$sSql || $this->db->exec($sSql));
        }
        return false;
    }

    function getEnum(){
        $sSQL='select id,custom_name from sdb_payment_cfg order by orderlist desc';
        return $this->db->select($sSQL);
    }

  function getPaymentInfo($method=''){
        $o = $this->system->loadModel('trading/order');
        $m = $this->system->loadModel('member/member');
        $order = $o->instance($this->order_id);
        $member = $m->instance($order['member_id']);
        

        $payment['M_OrderId'] = $this->payment_id;        //    订单的id---支付流水号
        $payment['M_OrderNO'] = $method=="recharge"?"充值".date("YmdHis"):$this->order_id;        //    订单号
        $payment['M_Amount'] = $this->money;        //    本次支付金额        小数点后保留两位，如10或12.34
        $payment['M_Def_Amount'] = $this->money;        //    本次支付本位币金额        小数点后保留两位，如10或12.34
        $payment['M_Currency'] = $this->currency;    //    支付币种
        $payment['M_Remark'] = $order['memo'];        //    订单备注
        $payment['M_Time'] = $order['createtime'];        //    订单生成时间
        $payment['M_Goods'] = $order['tostr'];        //    订单中商品描述
        $payment['M_Language'] = 'zh_CN';    //    语言选择        表示商家使用的页面语言
        $payment['R_Name'] = $order['ship_name'];        //    收货人姓名    订单支付成功后货品收货人的姓名
        $payment['R_Address'] = $order['ship_addr'];        //    收货人住址    订单支付成功后货品收货人的住址
        $payment['R_Postcode'] = $order['ship_zip'];    //    收货人邮政编码    订单支付成功后货品收货人的住址所在地的邮政编码
        $payment['R_Telephone'] = $order['ship_tel'];    //    收货人联系电话    订单支付成功后货品收货人的联系电话
        $payment['R_Mobile'] = $order['ship_mobile'];        //    收货人移动电话    订单支付成功后货品收货人的移动电话
        $payment['R_Email'] = $order['ship_email'];        //    收货人电子邮件地址    订单支付成功后货品收货人的邮件地址
        $payment['P_Name'] = $member['name'];        //    付款人姓名    支付时消费者的姓名
        $payment['P_Address'] = $member['addr'];        //    付款人住址    进行订单支付的消费者的住址
        $payment['P_PostCode'] = $member['zip'];    //    付款人邮政编码        进行订单支付的消费者住址的邮政编码
        $payment['P_Telephone'] = $member['tel'];    //    付款人联系电话     进行订单支付的消费者的联系电话
        $payment['P_Mobile'] = $member['mobile'];        //    付款人移动电话     进行订单支付的消费者的移动电话
        $payment['P_Email'] = $member['email'];        //    付款人电子邮件地址     进行订单支付的消费者的电子邮件地址
        $payment['K_key'] = $this->system->getConf('certificate.token');    //商店Key
        $payment['payExtend'] = unserialize($order['extend']);
        if ($this->pay_type=="recharge"){ //预存款充值
            $member=$m->instance($this->member_id);
            $payment['R_Name']=$member['name']?$member['name']:$member['uname'];
            $payment['R_Telephone']=$member['mobile']?$member['mobile']:($member['tel']?$member['tel']:'13888888888');
        }
        $configinfo = $this->getPaymentById($order['payment']);
        $pma=$this->getPaymentFileName($configinfo['config'],$configinfo['pay_type']);
        if (is_array($pma)){
            foreach($pma as $key => $val){
                $payment[$key]=$val;
            }
        }
        return $payment;
  }

    function doPay($method=''){
        $payObj = $this->loadMethod($this->type);
        $pay_vars = get_object_vars($payObj);
        $this->cur_trading = $pay_vars['cur_trading'];
        if($this->toCreate()){
            if ($payObj->head_charset)
                header("Content-Type: text/html;charset=".$payObj->head_charset);

            $html ="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
                \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
                <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-US\" lang=\"en-US\" dir=\"ltr\">
                <head>
</header><body><div>Redirecting...</div>";
//            $this->money += $this->paycost;（money中 已经包含paycost）
            $payObj->_payment = $this->payment;
            $toSubmit = $payObj->toSubmit($this->getPaymentInfo($method));
            if('utf8' != strtolower($payObj->charset)){
                $charset = $this->system->loadModel('utility/charset');
                foreach($toSubmit as $k=>$v){
                    if(!is_numeric($v)){
                        $toSubmit[$k] = $charset->utf2local($v,'zh');
                    }
                }
            }

            $html .= '<form id="payment" action="'.$payObj->submitUrl.'" method="'.$payObj->method.'">';
            foreach($toSubmit as $k=>$v){
                if ($k<>"ikey"){
                    $html.='<input name="'.urldecode($k).'" type="hidden" value="'.htmlspecialchars($v).'" />';
                    if ($v){
                        $buffer.=urldecode($k)."=".$v."&";
                    }
                }
            }
            if (strtoupper($this->type)=="TENPAYTRAD"){
                $buffer=substr($buffer,0,strlen($buffer)-1);
                $md5_sign=strtoupper(md5($buffer."&key=".$toSubmit['ikey']));

                $url=$payObj->submitUrl."?".$buffer."&sign=".$md5_sign;
                echo "<script language='javascript'>";
                echo "window.location.href='".$url."';";
                echo "</script>";
            }
            $html.='
</form>
<script language="javascript">
document.getElementById(\'payment\').submit();
</script>
</html>';
        }else{
            $html=<<<EOF
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\"/>
<script language="javascript">
alert('创建支付流水号错误！');
//location.href=document.referrer;
</script>
</html>
EOF;
        }
        echo $html;
        $this->system->_succ = true;
        exit;
    }

    /**
     * progress
     * 前台支付成功的处理
     *
     * @param mixed $paymentId
     * @param mixed $status
     * @param mixed $money
     * @access public
     * @return void
     */
    function progress($paymentId,$status,$info){
        $system = &$GLOBALS['system'];
        $system->request['base_url'] = dirname(dirname(dirname($_SERVER["REQUEST_URI"]))).'/';
         $url = $system->base_url().$system->mkUrl('paycenter',$act='result');
        $payStatus = $this->setPayStatus($paymentId,$status,$info);
        $html="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
                \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
                <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-US\" lang=\"en-US\" dir=\"ltr\">
                <head></header><body>Redirecting...";
        $html .= '<form id="payment" action="'.$url.'" method="post"><input type="hidden" name="payment_id" value="'.$paymentId.'">';
        $html.=<<<EOF
</form>
<script language="javascript">
document.getElementById('payment').submit();
</script>
</html>
EOF;
        echo $html;
    }
    function getPaymentFileByType($type){
        $tmp_ary=$this->db->selectrow('SELECT * FROM sdb_payment_cfg WHERE pay_type='.$type);
        $payment=$this->getPaymentFileName($tmp_ary['config'],$type);
        return $payment;
    }
    function getPaymentFileName($config,$ptype){//获取支付所需文件，如密钥文件、公钥文件
        if(!empty($config)){//添加
            $pmt=$this->loadMethod($ptype);
            $field=$pmt->getfields();
            $config=unserialize($config);
            if (is_array($config)){
                foreach($field as $k => $v){
                    if (strtoupper($v['type'])=="FILE"||$k=="keyPass")//判断支付网关是否有文件或者是私钥保护密码
                        $payment[$k] = $config[$k];
                }
            }
        }
        return $payment;
    }
    function getPaymentIdByOrderNO($orderno){
        return $this->db->selectrow('SELECT payment_id FROM sdb_payments WHERE order_id=\''.$orderno.'\'');
    }
    function isPayBillSuccess($payment_id){
        $row = $this->db->selectrow('select payment_id from sdb_payments WHERE payment_id=\''.$payment_id.'\' and status=\'succ\'');
        if ($row)
            return true;
        else
            return false;
    }
    function getSuccOrderBillList($orderid){
        return $this->db->select('SELECT * FROM sdb_payments WHERE order_id = '.$orderid.' and status IN (\'succ\',\'progress\')');
    }
    function showPayExtendCon(&$payments,&$payExtend){//在前台显示二级内容
        if ($payExtend)
            $payExtend=unserialize($payExtend);
        if ($payments){
            foreach($payments as $key => $val){
                $config=unserialize($val['config']);
                if (intval($config['ConnectType'])>0){
                    $fields=$this->getPlugFields($val['pay_type']);
                    foreach($fields as $k => $v){
                        if ($v['extendcontent']){
                            if ($config[$k]){
                                foreach($v['extendcontent'] as $extk => $extv){
                                    if($config[$extv['property']['name']]){
                                        $tmpValue=array();
                                        foreach($config[$extv['property']['name']] as $conk=>$conv){
                                            foreach($extv['value'] as $evk => $evv){
                                                if ($conv==$evv['value']){
                                                    $evv['imgurl']=$evv['imgname']?"<img src=".$this->system->base_url().'plugins/payment/images/'.$evv['imgname'].">":"";
                                                    if ($payExtend){
                                                        if (is_array($payExtend[$extv['property']['name']])){
                                                            if (in_array($evv['value'],$payExtend[$extv['property']['name']]))
                                                                $evv['checked'] = 'checked';
                                                        }
                                                        elseif ($payExtend[$extv['property']['name']]==$evv['value'])
                                                            $evv['checked']='checked';
                                                    }
                                                    $tmpValue[]=$evv;
                                                    break;
                                                } 
                                            }
                                        } 
                                        $payments[$key]['extend'][]=array("name"=>$extv['property']['name'],"fronttype"=>$extv['property']['fronttype'],"frontsize"=>$extv['property']['frontsize'],"value"=>$tmpValue,"extconId"=>$extv['property']['frontname']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    function recgextend(&$data,&$postInfo,&$extendInfo){
        $paymentcfg=$this->system->loadModel('trading/paymentcfg');
        $cfg=$paymentcfg->instance($data['payment'],'pay_type');
        $fields=$this->getPlugFields($cfg['pay_type']);
        if(is_array($fields)){
            foreach($fields as $fkey => $fval){
                if ($fval['extendcontent']){
                    foreach($fval['extendcontent'] as $ffkey => $ffval){
                        if (isset($postInfo[$ffval['property']['name']])){
                            $extendInfo[$ffval['property']['name']]=$postInfo[$ffval['property']['name']];
                        }
                    }
                }
            }
        }
    }
    function OrdMemExtend(&$order,&$extendInfo){
        $order['pay_extend']=unserialize($order['pay_extend']);
        if (is_array($order['pay_extend'])){
            $fields=$this->getPlugFields($order['paytype']);
            $paymentcfg=$this->system->loadModel('trading/paymentcfg');
            $cfg=$paymentcfg->instance($order['payment'],'config');
            if(is_array($fields)){
                $config=unserialize($cfg['config']);

                foreach($fields as $fkey => $fval){
                    if ($fval['extendcontent']){
                        foreach($fval['extendcontent'] as $ffkey => $ffval){
                            $tmp=array();
                            if (isset($config[$ffval['property']['name']])){
                                foreach($ffval['value'] as $fffkey => $fffval){
                                    $fffval['imgname']=$fffval['imgname']?"<img src=".$this->system->base_url().'plugins/payment/images/'.$fffval['imgname'].">":"";
                                    if (in_array($fffval['value'],$config[$ffval['property']['name']])){
                                        if (is_array($order['pay_extend'][$ffval['property']['name']])){
                                            if (in_array($fffval['value'],$order['pay_extend'][$ffval['property']['name']]))
                                                $fffval['checked']='checked';
                                        }
                                        elseif ($fffval['value']==$order['pay_extend'][$ffval['property']['name']])
                                            $fffval['checked']='checked';
                                        $tmp[]=$fffval;
                                    }
                                }
                                $extendInfo[$ffval['property']['name']]=array('type'=>$ffval['property']['fronttype'],'value'=>$tmp);
                            }
                        }
                    }
                }
            }
        }
    }
    function getExtendOfPlug($payid='',$paytype='',&$extfields){
        $fields=$this->getPlugFields($paytype,$payid);
        foreach($fields as $k => $v){
            if ($v['extendcontent']){
                foreach($v['extendcontent'] as $key => $val){
                    $extfields[]=$val['property']['name'];
                }
            }
        }
    }
    function getPlugFields($paytype='',$payid=''){
        if(!$paytype){
            $paymentcfg=$this->system->loadModel('trading/paymentcfg');
            $cfg=$this->getPaymentById($payid);
            $paytype=$cfg['pay_type'];
        }
        $method=$this->loadMethod($paytype);
        $fields=$method->getfields();
        return $fields;
    }
    function getExtendCon($config,$payid){
        $config=is_array($config)?$config:unserialize($config);
        if ($config){
            $fields = $this->getPlugFields('',$payid);
            $this->getExtendOfPlug($payid,'',$extfields);
            if ($extfields){
                foreach($fields as $key => $val){
                    if ($extendContent=$val['extendcontent']){
                        foreach($extfields as $extk => $extv){
                            if ($extendContent[$extk]['value']){
                                foreach($extendContent[$extk]['value'] as $sk => $sv){
                                    if ($sv['value']==$config[$extv])
                                        $extendCon[]=$sv['imgname']?"<img src='".$this->system->base_url().'plugins/payment/images/'.$sv['imgname']."' tip='".$sv['name']."' alt='".$sv['name']."'>":$sv['name'];
                                }
                            }
                        }
                    }
                }
            }
            return $extendCon;
        }
    }
}