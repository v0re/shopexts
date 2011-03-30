<?php
/**
 * mdl_coupon
 * 
 * @uses shopObject
 * @package trading
 * @version $Id: mdl.coupon.php 2057 2008-04-29 08:38:32Z bryant $
 * @copyright 2003-2007 ShopEx
 * @author bryant.yan <bryant@zovatech.com> 
 * @license Commercial
 */
include_once('shopObject.php');
class mdl_coupon extends shopObject{
    var $idColumn = 'cpns_id'; //表示id的列 
    var $textColumn = 'cpns_name';
    var $defaultCols = 'cpns_name,cpns_prefix,pmt_time_begin,pmt_time_end,cpns_id_c,cpns_type,cpns_status,cpns_gen_quantity,cpns_point';
    var $adminCtl = 'sale/coupon';
    var $defaultOrder = array('cpns_id','desc');
    var $tableName = 'sdb_coupons';

    function getColumns(){
        return array(
            'cpns_id'=>array('label'=>'序号','class'=>'span-1'),    /* 优惠券方案id */
            'cpns_id_c'=>array('label'=>'下载','class'=>'span-1','type'=>'download'),    /* 优惠券方案id */
            'cpns_name'=>array('label'=>'优惠券名称','class'=>'span-4'),    /* 优惠券名称 */
            'cpns_prefix'=>array('label'=>'优惠券号码','class'=>'span-3','readonly'=>'true'),    /* 生成优惠券前缀 */
            'cpns_gen_quantity'=>array('label'=>'已发','class'=>'span-1'),    /* 优惠券已经生成的总数量(原cpns_use_quantity) */
            'cpns_status'=>array('label'=>'状态','class'=>'span-1','type'=>'bool'),    /* 优惠券方案状态 */
            'cpns_type'=>array('label'=>'类型','class'=>'span-3','type'=>'cpns_type'),    /* 优惠券类型 0全局 1用户 2外部优惠券 */
            'cpns_point'=>array('label'=>'兑换积分','class'=>'span-3'),    /* 兑换优惠券积分 */
            'pmt_time_begin'=>array('label'=>'开始时间','class'=>'span-2','type'=>'time:FDATE'),    /* 优惠券起始时间 */
            'pmt_time_end'=>array('label'=>'结束时间','class'=>'span-2','type'=>'time:FDATE'),    /* 优惠券截止时间 */
        );
    }


    function getList($cols,$filter,$start=0,$limit=20,&$count,$orderType=null){
        $cols = str_replace('cpns_id_c', 'CONCAT(cpns_id,\'-\',cpns_type) as cpns_id_c', $cols);
        $sql = 'select '.$cols.',c.pmt_id as pmt_id from sdb_coupons as c
            left join sdb_promotion as p on c.pmt_id=p.pmt_id
            where '.$this->_filter($filter);


        if($orderType)$sql.=' order by '.implode($orderType,' ');
        $count = $this->db->_count($sql);
        return $this->db->selectLimit($sql,$limit,$start);
    }


    function modifier_bool(&$rows,$options=array()){
        foreach($rows as $i=>$publish){
            $rows[$i] = $publish?'是':'否';
        }
    }

    function modifier_cpns_type(&$rows,$options=array()){
        foreach($rows as $i=>$type){
            switch($type) {
            case 0:
                $rows[$i] = '一张可无限使用';
                break;
            case 1:
                $rows[$i] = '多张只适用一次';
                break;
            case 2:
                $rows[$i] = 'ShopEx优惠券';
                break;
            }
        }
    }

    function modifier_download(&$rows,$options=array()) {
        foreach($rows as $i=>$key) {            
            $aTmp = explode('-', $key);
            $id = $aTmp[0];
            $type = $aTmp[1];
            if ($type==1) {
                $rows[$i] = '<span onclick="var i=parseInt(prompt(\'请输入需要下载优惠券的数量：\',50));if(i)window.open(\'index.php?ctl=sale/coupon&act=download&p[0]='.(string)$id.'&p[1]=\'+i,\'download\')" class="lnk">下载</span>';
            }else{
                $rows[$i] = '';
            }
        }
    }

    /*
        <td><{$item.cpns_name}></td>
        <td><{$item.cpns_prefix}></td>
        <td><{$item.pmt_time_begin|cdate}></td>
        <td><{$item.pmt_time_end|cdate}></td>
        <td><{if $item.cpns_type=='0'}>共用优惠券<{elseif $item.cpns_type=='1'}>个人优惠券<{elseif $item.cpns_type=='2'}>shopex优惠券<{/if}></td>
        <td><{if $item.cpns_status=='1'}>是<{else}>否<{/if}></td>
        <td>
        <!--        <a href="index.php?ctl=sale/coupon&act==statistic&p[0]=<{$item[$_finder.id]}>">查看统计</a>-->
        <{if $item.cpns_type=='1'}>
        <span onclick="var i=parseInt(prompt('下载数量'));if(i)window.open('index.php?ctl=sale/coupon&act=download&p[0]=<{$item[$_finder.id]}>&p[1]='+i)" class="lnk">下载</span>
    <{/if}>*/    
    function searchOptions(){
        return array('cpns_name'=>'优惠券名称');
    }




    function _filter($filter){
        $where=array(1);
        if ($filter['cpns_name']) {
            $where[] = 'cpns_name like\'%'.$filter['cpns_name'].'%\'';
        }

        $where[] = 'pmt_type=\'1\'';

        if(is_array($filter['cpns_id'])){
            foreach($filter['cpns_id'] as $cpns_id){
                if($cpns_id!='_ANY_'){
                    $coupons[] = 'c.cpns_id='.intval($cpns_id);
                }
            }
            if(count($coupons)>0){
                $where[] = '('.implode($coupons,' or ').')';
            }
        }

        if(!empty($filter['cpns_type']) && is_string($filter['cpns_type'])){
            $filter['cpns_type'] = explode(',', $filter['cpns_type']);
        }
        if(is_array($filter['cpns_type'])){
            foreach($filter['cpns_type'] as $type){
                if($type!='_ANY_'){
                    $cpns_type[] = 'c.cpns_type=\''.intval($type).'\'';
                }
            }
            if(count($cpns_type)>0){
                $where[] = '('.implode($cpns_type,' or ').')';
            }
        }

        if (isset($filter['ifvalid'])) {
            if ($filter['ifvalid']===1){
                $curTime = time();
                $where[] = 'cpns_status=\'1\' and pmt_time_begin <= '.$curTime.' and pmt_time_end >'.$curTime;
            }
        }


        return parent::_filter($filter,'c').' and '.implode($where,' and ');
    }
    //------------------------------------------------------------------------------------------
    function getCouponList($limit=true) {//个人用户优惠券列表
        $sSql = 'select * from sdb_coupons as c 
            inner join sdb_promotion as p on c.pmt_id=p.pmt_id and c.cpns_type=\'1\'';

        return $this->db->select_b($sSql, $limit?PAGELIMIT:null);
    }
    function checkPrefix($prefix){
        if($this->db->select('SELECT cpns_id from sdb_coupons WHERE cpns_prefix="'.$prefix.'" limit 1')){
            return true;
        }else{
            return false;
        }
    }
    function getCouponByIds($aCoupon) {
        if (is_array($aCoupon) && !empty($aCoupon)) {
            $sSql = 'SELECT * FROM sdb_coupons WHERE cpns_id in ('.implode(',', $aCoupon).')';
            $aTemp = $this->db->select($sSql);
            return $aTemp;
        }else{
            return false;
        }
    }

    function getUserCouponArr() {
        return $this->db->select('SELECT cpns_id,cpns_name FROM sdb_coupons WHERE cpns_type=\'1\' and cpns_point is null ORDER BY cpns_id desc');
    }

    function exchange($userId, $cpnsId) {
        $sSql = 'select cpns_point from sdb_coupons where cpns_status=\'1\' and cpns_type=\'1\' and cpns_point is not null and cpns_id='.intval($cpnsId);
        if ($aCoupon = $this->db->selectRow($sSql)) {
            $nPoint = $aCoupon['cpns_point'];
            $oCoupon = $this->system->loadModel('trading/coupon');
            //客户为本.先发优惠券，成功之后再扣用户积分
            $oMemberPoint = $this->system->loadModel('trading/memberPoint');
            if ($oMemberPoint->chgPoint($userId, -abs($nPoint), 'exchange_coupon')) {
//                echox('wocao',$cpnsId);exit;
                return $oCoupon->generateCoupon($cpnsId, $userId, 1);
            }else{
                return false;
            }
        }else {
            return false;
        }
    }

    function getMemberCoupon($userId,$nPage){
        $aData = $this->db->select_f('SELECT * FROM sdb_member_coupon as mc 
                                            left join sdb_coupons as c on c.cpns_id=mc.cpns_id
                                            left join sdb_promotion as p on c.pmt_id=p.pmt_id 
                                            WHERE member_id='.$userId.' ORDER BY mc.memc_gen_time DESC',$nPage,PERPAGE);
        return $aData;
    }

    function isLevelAllowUse($pmtId, $mLvId) {
        echox('select pmt_id from sdb_pmt_member_lv where member_lv_id='.intval($mLvId).' and pmt_id='.intval($pmtId));
        if ($this->db->select('select pmt_id from sdb_pmt_member_lv where member_lv_id='.intval($mLvId).' and pmt_id='.intval($pmtId))) {
            return true;
        }else{
            return false;
        }
    }

    function getCouponById($cpnsId) {
        return $this->db->selectrow('SELECT *,c.pmt_id as pmt_id,c.cpns_id as cpns_id FROM sdb_coupons as c
            left join sdb_promotion as p on c.pmt_id=p.pmt_id and pmt_type=\'1\'
            WHERE c.cpns_id='.intval($cpnsId));
    }

    function getPromotionByCpnsId($cpnsId) {
        return $this->db->selectrow('SELECT * FROM sdb_promotion where cpns_id='.intval($cpnsId).' and pmt_type=\'1\'');
    }

    function getCouponGenerateByCpnsId($cpnsId) {
        return $this->db->selectrow('SELECT * FROM sdb_promotion where cpns_id='.intval($cpnsId).' and pmt_type=\'2\'');
    }

    function getCouponGenerateList($cpnsId) {
        return $this->db->select_b('SELECT * FROM sdb_promotion where pmt_type=\'2\' and cpns_id='.intval($cpnsId),PAGELIMIT);
    }

    function addCoupon($aData) {
        switch($aData['cpns_type']) {
        case 0:
            $flag = 'A';
            break;
        case 1:
            $flag = 'B';
            break;
        case 2:
            break;
        }
        $aData['cpns_prefix'] = $flag.$aData['cpns_prefix'];
        if ($aData['cpns_id']){
            $aRs = $this->db->query('SELECT * FROM sdb_coupons WHERE cpns_id='.$aData['cpns_id']);
            $sSql = $this->db->getUpdateSql($aRs,$aData);
            return (!$sSql || $this->db->exec($sSql));
        }else{
            $aData['cpns_key'] = $this->generate_key();
            $aData['cpns_gen_quantity'] = intval($aData['cpns_gen_quantity']);
            $aRs = $this->db->query('SELECT * FROM sdb_coupons WHERE 0');
            $sSql = $this->db->getInsertSql($aRs,$aData);
            if ($this->db->exec($sSql)){
                return $this->db->lastInsertId();
            }else{
                return false;
            }
        }
    }

    function generate_key()
    {
        $n = rand(4,7);
        $str = '';
        for ($j=0; $j<$n; ++$j)
        {
            $str .= chr(rand(21,126));
        }
        return $str;
    }

    //function useUserCoupons($memc_code, $userid, $gidArr, $execMethod, $orderid='', $orderprice=0)
    //-------------------------------------------------------------
    function _verifyCouponType($couponFlag) {
        //A：通用优惠券 B:使用一次优惠券 S:ShopEx优惠券
        $_allCouponType = array('A', 'B', 'S');
        return in_array($couponFlag, $_allCouponType);
    }



    function generateCoupon($cpnsId, $userId, $nums,$orderId='') {
        //原则,只要能使用就允许生成,
        $curTime = time();
        $sSql = 'select * from sdb_coupons as c
            left join sdb_promotion as p on c.pmt_id=p.pmt_id
            where cpns_status=\'1\' and cpns_type=\'1\' and c.cpns_id='.$cpnsId.' and
            pmt_time_begin <= '.$curTime.' and pmt_time_end >'.$curTime;

        if ($aCoupon = $this->db->selectRow($sSql)) {
            for($i=1; $i<=$nums; $i++) {
                if ($couponCode = $this->_makeCouponCode($aCoupon['cpns_gen_quantity']+$i, $aCoupon['cpns_prefix'], $aCoupon['cpns_key'])) {
                    $aData = array('memc_code' => $couponCode,
                        'cpns_id' => $cpnsId,
                        'member_id' => $userId,
                        'memc_gen_orderid' => $orderId,
                        'memc_gen_time' => time());


                    $rRs = $this->db->query('SELECT * FROM sdb_member_coupon WHERE 0=1');
                    $sSql = $this->db->GetInsertSQL($rRs, $aData);
                    $this->db->exec($sSql);

                    $aData = array('cpns_gen_quantity' => $aCoupon['cpns_gen_quantity']+$i);
                    $rRs = $this->db->query('SELECT * FROM sdb_coupons WHERE cpns_id='.intval($cpnsId));
                    $sSql = $this->db->GetUpdateSQL($rRs, $aData);
                    if ($sSql) {
                        $this->db->exec($sSql);
                    }
                }else{
                    return false;
                }
            }
            return true;
        }else{
            return false;
        }
    }

    function downloadCoupon($cpnsId, $nums){
        $curTime = time();
        $aRes = array();

        $sSql = 'select * from sdb_coupons as c
            left join sdb_promotion as p on c.pmt_id=p.pmt_id
            where cpns_status=\'1\' and cpns_type=\'1\' and c.cpns_id='.$cpnsId.' and
            pmt_time_begin <= '.$curTime.' and pmt_time_end >'.$curTime;
        if ($aCoupon = $this->db->selectRow($sSql)) {
            for($i=1; $i<=$nums; $i++) {
                if ($couponCode = $this->_makeCouponCode($aCoupon['cpns_gen_quantity']+$i, $aCoupon['cpns_prefix'], $aCoupon['cpns_key'])) {
                    $aRes[] = array($couponCode);
                }else{
                    return false;
                }
            }
            $aData = array('cpns_gen_quantity' => $aCoupon['cpns_gen_quantity']+$nums);
            $rRs = $this->db->query('SELECT * FROM sdb_coupons WHERE cpns_id='.intval($cpnsId));
            $sSql = $this->db->GetUpdateSQL($rRs, $aData);
            if($sSql)$this->db->exec($sSql);
            return $aRes;
        }else{
            return false;
        }        
    }

    function getCouponByPrefix($prefix) {
        return $this->db->selectRow('select * from sdb_coupons where cpns_prefix=\''.trim($prefix).'\'');
    }


    //确定优惠券代码有效性
    function getPrefixFromCouponCode($couponCode) {
        $prefix = substr($couponCode, 0, strlen($couponCode)-($this->system->getConf('coupon.code.count_len')+$this->system->getConf('coupon.code.encrypt_len')));
        return $prefix;
    }

    function _verifyCouponCode($couponCode) {
        $couponFlag = $this->getFlagFromCouponCode($couponCode);
        if ($this->_verifyCouponType($couponFlag)) {
            switch ($couponFlag) {
            case 'A':
            case 'S':
                return true;
                break;
            case 'B':
                $prefix = $this->getPrefixFromCouponCode($couponCode);
                if ($aCoupon = $this->getCouponByPrefix($prefix)) {
                    $serial_number = substr($couponCode, -$this->system->getConf('coupon.code.count_len'));
                    $check_number = substr($couponCode, strlen($prefix), $this->system->getConf('coupon.code.encrypt_len'));
                    $new_check_number = strtoupper(substr(md5($aCoupon['cpns_key'].$serial_number.$prefix),0, $this->system->getConf('coupon.code.encrypt_len')));            
                    if ($check_number == $new_check_number ) {
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
    }

    function _makeCouponCode($iNo, $prefix, $key) {
        if ($this->system->getConf('coupon.code.count_len') >= strlen(strval($iNo))) {
            $iNo = str_pad(dec2b36($iNo), $this->system->getConf('coupon.code.count_len'), '0', STR_PAD_LEFT);
            $checkCode = md5($key.$iNo.$prefix);
            $checkCode = strtoupper(substr($checkCode, 0, $this->system->getConf('coupon.code.encrypt_len')));
            $memberCoupon = $prefix.$checkCode.$iNo;
            return $memberCoupon;
        }else{
            return false;
        }
    }


    function getFlagFromCouponCode($couponCode) {
        return substr($couponCode, 0, 1);
    }


    //后台会员优惠券应用
    function applyMemberCoupon($cpnsId, $couponCode, $orderId, $userId) {
        //todo验证
        //1.验证是否是匿名用户 1.验证是否 有效优惠券 2.判断是何种优惠券,分别处理
        if (!$userId) {
            return false;
        }

        $couponFlag = $this->getFlagFromCouponCode($couponCode);
        if (!$this->_verifyCouponCode($couponCode)) {
            return false;
        }

        switch ($couponFlag) {
        case 'A':
            break;
        case 'B':
            $aMeberCoupon = $this->db->selectRow('select *  from sdb_member_coupon where memc_code=\''.$couponCode.'\'');

            if ($aMeberCoupon) {
                if ($aMeberCoupon['memc_enabled']=='true'&&$aMeberCoupon['memc_used_times']<$this->system->getConf('coupon.mc.use_times')) {
                    $aRs = $this->db->query('SELECT * FROM sdb_member_coupon where memc_code=\''.$couponCode.'\'');
                    $aData['memc_used_times'] = $aMeberCoupon['memc_used_times']+1;
                    $sSql = $this->db->getUpdateSql($aRs,$aData);
                    return (!$sSql || $this->db->exec($sSql));
                }else{
                    trigger_error('此优惠券已被取消/使用次数已经用满',E_USER_NOTICE);
                    return false;
                }
            }else{
                $aData['memc_code'] = $couponCode;
                $aData['cpns_id'] = $cpnsId;
                $aData['member_id'] = $userId;                    
                $aData['memc_used_times'] = 1;
                $aData['memc_gen_time'] = time();
                $aRs = $this->db->query('SELECT * FROM sdb_member_coupon WHERE 0');

                $sSql = $this->db->getInsertSql($aRs,$aData);
                return (!$sSql || $this->db->exec($sSql));
            }
            break;
        case 'S':
            break;
        }    
    }

    //使用Coupon
    function verifyGoodsCoupon($couponCode, $mlvid, $goods_ids, $brand_ids, $cat_ids, $orderPrice) {
        if ($this->useMemberCoupon($couponCode, $mlvid, $goods_ids, $brand_ids, $cat_ids, $orderPrice)) {
            return true;
        }else{
            return false;
        }

    }

    //前台会员使用优惠券
    function useMemberCoupon($couponCode, $mlvid, $goods_ids, $brand_ids, $cat_ids, $orderPrice=0) {
        $couponFlag = $this->getFlagFromCouponCode($couponCode);
        if (!$this->_verifyCouponCode($couponCode)) {
            trigger_error('The coupon is invalid. Please check and try again', E_USER_WARNING);
            return false;
        }
        $curTime = time();
        //匿名用户只可以使用全局优惠券
        switch ($couponFlag) {
        case 'A':
            $prefix = $couponCode;
            $cpnsType = 0;
            break;
        case 'B':
            if (!$mlvid) return false;
            $prefix = $this->getPrefixFromCouponCode($couponCode);
            $cpnsType = 1;
            break;
        case 'S':
            if (!$mlvid) return false;
            $cpnsType = 2;
            break;
        }

        //                            c.order_money>'.$price_f.' and c.order_money<='.$price_t.' //todo 需要更改限制范围，和数量范围

        /*
         * 代码需要仔细测试
         * */
        if(defined('DB_OLDVERSION') && DB_OLDVERSION){
            $sSql = 'select * from sdb_coupons as c
                left join sdb_promotion as p on c.pmt_id=p.pmt_id
                where cpns_prefix=\''.$prefix.'\' and cpns_status=\'1\' and cpns_type=\''.$cpnsType.'\' and 
                pmt_time_begin <= '.$curTime.' and pmt_time_end >'.$curTime;
            $rows = $this->db->select($sSql);
            $m=array();
            foreach($rows as $k=>$r){
                $m[$r['pmt_id']] = $r[$k];
            }
            $sn=array();
            $aCoupon = false;
            foreach($this->db->select('select member_lv_id from sdb_pmt_member_lv where pmt_id in ('.implode(',',array_keys($m)).')') as $sr){
                if(isset($m[$sr['pmt_id']])){
                    $aCoupon = $m[$sr['pmt_id']];
                    break;
                }
            }

        }else{
            $sSql = 'select * from sdb_coupons as c
                left join sdb_promotion as p on c.pmt_id=p.pmt_id
                where cpns_prefix=\''.$prefix.'\' and cpns_status=\'1\' and cpns_type=\''.$cpnsType.'\' and 
                ('.intval($mlvid).' in (select member_lv_id from sdb_pmt_member_lv where pmt_id=p.pmt_id)) and pmt_time_begin <= '.$curTime.' and pmt_time_end >'.$curTime;
            $aCoupon = $this->db->selectRow($sSql);
        }

        if ($aCoupon) {
            switch ($couponFlag) {
            case 'B':
                if ($aTmp = $this->db->selectRow('select memc_used_times,memc_enabled from sdb_member_coupon where memc_code=\''.$couponCode.'\'')){
                    if ($aTmp['memc_used_times']>=$this->system->getConf('coupon.mc.use_times')||$aTmp['memc_enabled']=='false') {
                        $this->setError('30000');
                        trigger_error('The coupon is invalid. Please check and try again',E_USER_WARNING);
                        return false;
                    }
                }
                break;
            case 'A':
            case 'S':
                break;
            }            

            $oPromotion = $this->system->loadModel('trading/promotion');
            if ($aCoupon['pmt_basic_type'] == 'goods') {
                if ($aPmtGoods = $oPromotion->filter_goods($aCoupon['pmt_id'], $aCoupon['pmt_bond_type'], $goods_ids, $brand_ids, $cat_ids)) {
                    $aResult[$couponCode]['type'] = 'goods';
                    $aResult[$couponCode]['pmt_id'] = $aCoupon['pmt_id'];
                    $aResult[$couponCode]['goods_ids'] = $aPmtGoods;
                    $aResult[$couponCode]['cpns_id'] = $aCoupon['cpns_id'];
                    $aResult[$couponCode]['cpns_type'] = $aCoupon['cpns_type'];
                }else{
                    trigger_error('No item for this coupon at shopping bag',E_USER_WARNING);
                    return false;
                }
            } else if ($aCoupon['pmt_basic_type'] == 'order') {
                if ($aCoupon['order_money_from'] <= $orderPrice && $aCoupon['order_money_to'] > $orderPrice) {
                    $aResult[$couponCode]['type'] = 'order';
                    $aResult[$couponCode]['pmt_id'] = $aCoupon['pmt_id'];
                    $aResult[$couponCode]['cpns_id'] = $aCoupon['cpns_id'];
                    $aResult[$couponCode]['cpns_type'] = $aCoupon['cpns_type'];
                }else{
                    trigger_error('Order amount does not comply with restrictions on the amount of coupon',E_USER_WARNING);
                    return false;
                }
            }else {
                trigger_error('Types of temporary absence of such concessions.',E_USER_WARNING);
                return false;
            }
            return $aResult;
        } else {
            trigger_error('The coupon is invalid. Please check and try again',E_USER_WARNING);
            return false;
        }
    }

    function recycle($filter) {
        $arrId = $filter['cpns_id'];
        if ($arrId) {
            $sSql = 'select pmt_id from sdb_coupons where cpns_id in ('.implode($arrId, ',').')';
            $aData = $this->db->select('select pmt_id from sdb_coupons where cpns_id in ('.implode($arrId, ',').')');            
            if ($aData) {
                $aPmtIds = array_item($aData, 'pmt_id');
                $this->db->exec('delete from sdb_promotion where pmt_id in ('.implode($aPmtIds, ',').')');
            }

            $sSql = 'delete from sdb_coupons where  cpns_id in ('.implode($arrId, ',').')';
            if ($this->db->exec($sSql)) {
                $related_tables = array('sdb_member_coupon', 'sdb_pmt_gen_coupon');
                foreach($related_tables as $table) {
                    $this->db->exec('delete from '.$table.' where  cpns_id in ('.implode($arrId, ',').')');                    
                }
                return true;
            } else {
                $msg = __('数据删除失败！');
                return false;
            }
        }else{            
            $msg = 'no select';
            return false;
        }
    }
}
