<?php
/**
 * mdl_account
 * 账户相关类，只有账户层次的操作才会用到此类
 *
 * @uses modelFactory
 * @package member
 * @version $Id: mdl.account.php 1965 2008-04-26 15:21:50Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@zovatech.com>
 * @license Commercial
 */
include_once("shopObject.php");
class mdl_account extends shopObject {

    function check_uname($uname,&$message){
        $uname = trim($uname);
        $len = strlen($uname);
        if($len<3){
            $message = '用户名过短!';
            return false;
        }elseif($len>20){
            $message = '用户名过长!';
            return false;
        }elseif(!preg_match('/^([@\.]|[^\x00-\x2f^\x3a-\x40]){2,20}$/i', $uname)){
            $message = '用户名包含非法字符!';
            return false;
        }else{
            $row = $this->db->selectrow("select uname from sdb_members where uname='{$uname}'");
            if($row['uname']){
                $message = '重复的用户名!';
                return false;
            }else{
                if ($this->check_name_inuc($uname)==1)
                    return true;
                else
                    return false;
            }
        }
    }

    function check_email($email,&$message){
        if(!(eregi('^.+@.+$',$email))){
            $message = '邮箱输入有误！';
            return false;
        }else{
            return true;
        }
    }

    /**
     * 创建账户
     *
     * @param mixed $data
     * @param mixed $message
     * @access public
     * @return void
     */
    function create($data,&$message){

        $data['uname'] = trim(strtolower($data['uname']));
        $data['email'] = trim(strtolower($data['email']));
        $data['reg_ip'] = remote_addr();
        $data['regtime'] = time();

        if(!$this->check_uname($data['uname'],$message)){
            return false;
        }
        if(!$this->check_email($data['email'],$message)){
            return false;
        }

        if($data['passwd']!=$data['passwd_r']){
            $message = '两次密码输入不一致！';
            return false;
        }
        $row = $this->db->selectrow('select * from sdb_member_lv where default_lv="1"');
        $data['member_lv_id'] = $row['member_lv_id']?$row['member_lv_id']:0;

        $defcur = $this->db->selectrow('select cur_code from sdb_currency where def_cur="true"');
        $data['cur'] = $defcur['cur_code'];
        $rs = $this->db->exec('select * from sdb_members where uname='.$this->db->quote($data['uname']));

        //判断用户是否存在，返回falas或者getInsertSQL
        if(!$rs || $this->db->getRows($rs)){
            trigger_error('存在重复的用户id',E_USER_ERROR);
            return false;
        }
        $data['password'] = md5($data['passwd']);
        getRefer($data);
        $sql = $this->db->getInsertSQL($rs,$data);;
        if($this->db->exec($sql)){
            $userId = $this->db->lastInsertId();
            $status = $this->system->loadModel('system/status');
            $status->add('MEMBER_REG');
            $this->init($userId);
            $this->fireEvent('register',$data,$userId);        //会员注册成功事件
            $sql = 'select member_id,member_lv_id,email,uname,password,unreadmsg,cur,lang,point from sdb_members where member_id='.$userId;
            $row = $this->db->selectrow($sql);
            $row['secstr'] = $this->cookieValue($userId);
            return $row;
        }else{
            return false;
        }
    }
    function cookieValue($memberID){
        $row=$this->db->selectrow('select uname,password from sdb_members where member_id='.$memberID);
        $row['uname']=md5($row['uname']);
        return $memberID.'-'.utf8_encode($row['uname']).'-'.md5($row['password'].STORE_KEY).'-'.time();
    }
    function checkMember($data){
        // OR email="'.$data['email'].'"
        $row=$this->db->selectrow('select member_id,uname,email from sdb_members where uname="'.$data['uname'].'"');
        if($row['member_id'] && $row['uname'] == $data['uname']){
                return true;
        }else{
            return false;
        }
    }

    function verify($memberId,$code){
        $row = $this->db->selectrow('select member_id,member_lv_id,email,uname,b_year,b_month,b_day,password,unreadmsg,cur,lang,point from sdb_members where member_id='.intval($memberId));
        if($row && md5($row['password'].STORE_KEY)==$code){
            $oMsg = $this->system->loadModel('resources/msgbox');
            $row['unreadmsg'] = $oMsg->getNewMessageNum($memberId);
            unset($row['password']);
            return $row;
        }else{
            return false;
        }
    }

    function init($memberId){
        if($member = $this->db->selectrow( 'select * from sdb_members where member_id='.intval($memberId))){
            foreach($this->listFilters($member) as $filter){
                $this->applyFilter($member,$filter);
            }
        }else{
            return false;
        }
    }

    function verifyLogin($login,$passwd,&$message,$passport=null){
        $login = trim(strtolower($login));
        if(!$passport){
            if(strlen($login)==0){
                $message = '请填写登录信息。';
                return false;
            }else{
                $sql = 'select member_id,member_lv_id,email,uname,b_year,b_month,b_day,password,unreadmsg,cur,lang,point from sdb_members where uname='.$this->db->quote($login).' and password='.$this->db->quote(md5($passwd))." and disabled='false'";
            }
            if($row = $this->db->selectrow($sql)){
                $row['secstr'] = $this->cookieValue($row['member_id']);
                $oMsg = $this->system->loadModel('resources/msgbox');
                $row['unreadmsg'] = $oMsg->getNewMessageNum($row['member_id']);
                return    $row;
            }else{
                return false;
            }
        }else{//passport登录验证
            $objPasspt = $this->system->loadModel('member/passport');
            $objPasspt->verifyLogin($passport,$login,$passwd);
        }
    }
    /**
    *
    */
    function verifyPassportLogin($member){
            $sql = 'select member_id,member_lv_id,email,uname,password,unreadmsg,cur,lang,point from sdb_members where uname='.$this->db->quote($member['username']);
            $row = $this->db->selectrow($sql);
            if($row){
                $sql = 'update sdb_members set password='.$this->db->quote($member['password']).' where uname='.$this->db->quote($member['username']);
                $this->db->exec($sql);
                return $row;
            }
            return false;
    }
    function toLogin($member){
        if(empty($member['username'])){
            return false;
        }
        $sql = 'select member_id,member_lv_id,email,uname,password,unreadmsg,cur,lang,point from sdb_members where uname='.$this->db->quote($member['username']);
        $row = $this->db->selectrow($sql);
        $row['secstr'] = $this->cookieValue($row['member_id']);
        return $row;
    }

    function createPassport($member){
        $row = $this->db->selectrow('select * from sdb_member_lv where default_lv="1"');
        $member['member_lv_id'] = $row['member_lv_id']?$row['member_lv_id']:0;
        $sql = "insert into sdb_members (member_lv_id,uname,password,email,reg_ip,regtime) values ('".$member['member_lv_id']."','".$member['username']."','".$member['password']."','".$member['email']."','".$member['regip']."','".$member['regdate']."')";
        if(!$this->db->exec($sql)){
            return false;
        }
        return $member['username'];
    }


    function passportCallback($passport){//passport登录，回叫登录
        $objPasspt = $this->system->loadModel('member/passport');
        $memberInfo = $objPasspt->decode($passort,array_merge($_GET,$_POST));
        $sql = 'select member_id,uname from sdb_members where user='.$this->db->quote($memberId['login']).' and passport='.$this->db->quote($memberId['login']);
        if($row = $this->db->selectrow($sql)){
            return $this->cookieValue($row['member_id']);
        }else{
            $memberInfo['password_r'] = $memberInfo['password'] = substr(md5(rand(time())),0,6);
            return $this->create($memberInfo);
        }
    }

    //密码修改
    function saveSecurity($nMemberId,$aData){
        if(!($aTemp = $this->db->selectrow("SELECT password,pw_question,pw_answer,uname,name,email FROM sdb_members WHERE  member_id=".intval($nMemberId)))){
            trigger_error('无效的用户Id', E_USER_ERROR);
            return false;
        }

        if(empty($aData['passwd'])){
            if( !$aData['pw_answer'] || !$aData['pw_question'] ){
                trigger_error('安全问题修改失败！', E_USER_ERROR);
                return false;
            }
            return $this->db->exec("UPDATE sdb_members SET pw_answer = '".$aData['pw_answer']."' ,pw_question = '".$aData['pw_question']."' WHERE member_id = ".intval($nMemberId));
        }
        else{   // if(($aData['pw_question'] == $aTemp['pw_question']) && ($aData['pw_answer'] == $aTemp['pw_answer']))
            $pObj=$this->system->loadModel('member/passport');
            if ($obj=$pObj->function_judge('edituser')){
                $res = $obj->edituser($aTemp['uname'],$aData['old_passwd'],$aData['passwd'],$aTemp['email']);
                if ($res>0){
                    return true;
                }
                else{
                    trigger_error('输入的旧密码与原密码不符！', E_USER_ERROR);
                    return false;
                }
            }
            else{
                //$passwdLen=strlen($aData['passwd']);
                if(md5($aData['old_passwd']) == $aTemp['password']){
                    if($aData['passwd'] == $aData['passwd_re']){
                        if(isset($aData['passwd']{3})){
                            if(!isset($aData['passwd']{20})){
                        $aSet['password'] = md5($aData['passwd']);
                        $aRs = $this->db->query("SELECT password FROM sdb_members WHERE  member_id=".intval($nMemberId));
                        $sSql = $this->db->getUpdateSql($aRs,$aSet);
                                if(!$sSql || $this->db->exec($sSql)){
                                    $aData = array_merge($aTemp,$aData);
                                    $this->fireEvent('chgpass',$aData,$nMemberId);        //会员更改密码事件
                                    $this->system->setCookie('MEMBER',$this->cookieValue($nMemberId));
            //                        $message = __('密码修改成功！');
                                    return true;
                                }else{
                                    trigger_error('密码修改失败！', E_USER_ERROR);
                                    return false;
                                }

                            }else{
                                trigger_error('密码长度不能大于20', E_USER_ERROR);
                                return false;
                            }
                        }else{
                            trigger_error('密码长度不能小于4', E_USER_ERROR);
                            return false;
                        }

                    }else{
                        trigger_error('两次输入的密码不一致！', E_USER_ERROR);
                        return false;
                    }
                }else{
                    trigger_error('输入的旧密码与原密码不符！', E_USER_ERROR);
                    return false;
                }
            }
        }


    }

    function remove($memberId){
        return $this->db->exec('delete from sdb_members where member_id='.intval($memberId));
    }

    function addFilter($who,$what){
        return $filterId;
    }

    function applyFilter(&$who,&$filter){
    }

    function listFilters($who=null){
        return array();
    }

    function getFilter($filterId){
    }

    function delFilter($filterId){
    }

    //载入member实体
    function load($memberId){
        $member = $this->system->loadModel('member/member');
        return $member->load($memberId)?$member:false;
    }

    function lock($memberId){

    }

    /**
     * 合并帐户
     *
     * @access public
     * @return void
     */
    function merge(){
        $accounts = func_get_args();
        $to = array_shift($accounts);
        foreach($accounts as $acc){
            $this->_merge($to,$acc);
        }
    }

    function _merge($to,$from){
    }

    function getMemberList($sUser) { ; }/*{{{*/

    function getPointList() { ; }

    function getMemberById($member_id) { ; }

    function getFieldById($fieldname='', $id){ ; }

    function getMemberByUser($user) { ; }

    function getPointByUserid($member_id) { ; }

    function getMemberByMemberId($nMemberId) { ; }

    function delMember($strId) { ; }

    function editMember($data, $level, $member_id, $username) { ; }

    function editMemberByLevel($data, $levelid) { ; }

    function editMemberByUserid($data, $member_id) { ; }

    function addOperate($dat) { ; }

    function getMemberAgreement() { ; }

    function editMemberAgreement($data) { ; }

    function getMemberLevelList($isPagination=true) { ; }

    function getSpecialLevel() { ; }

    function addMemberLevel($data, $chgmember, $chggoods) { ; }

    function editMemberLevel($data, $levelid, $chgMember, $chggoods) { ; }

    function delMemberLevel($strId) { ; }

    function getMemberLevelById($levelid) { ; }

    function getDefaultLevel() { ; }

    function chgMember() { ; }

    function chgGoods($levelid, $tmpDiscount, $type) { ; }

    function getLevel($point) { ; }

    function getGoodsList() { ; }

    function addMemberPrice($data) { ; }

    function getGoodsPropGrp() { ; }

    function addGoodsPropGrp($data) { ; }

    function delByLevel($levelid) { ; }

    function getRecsts($levelid='') { ; }

    function addAdvance($data) { ; }

    function toPayAdvance($data) { ; }

    function editMemberAdvance($nAdvance, $userID) { ; }

    function getLevelByPoint($point) { ; }

    function upLevel($memberid, $levelid=0){
        $aData['member_lv_id'] = $this->getNextLevel($levelid);
        $aData['member_id'] = $memberid;
        $objMember = $this->system->loadModel("member/member");
        return $objMember->saveMemberInfo($aData);
    }

    function downLevel($memberid, $levelid=0){
        $aData['member_lv_id'] = $this->getPreLevel($levelid);
        $aData['member_id'] = $memberid;
        $objMember = $this->system->loadModel("member/member");
        return $objMember->saveMemberInfo($aData);
    }

    function getNextLevel($levelid=0){
        $aRet = $this->db->selectrow('SELECT * FROM sdb_member_lv WHERE pre_id='.intval($levelid));
        return $aRet['member_lv_id'];
    }

    function getPreLevel($levelid=0){
        $aRet = $this->db->selectrow('SELECT * FROM sdb_member_lv WHERE levelid='.intval($levelid));
        return $aRet['member_lv_id'];
    }

    function getOrderList($memberid){
        return $this->db->select('SELECT * FROM sdb_orders WHERE member_id='.intval($memberid));
    }

    function getAdvanceList($memberid){
        return $this->db->select('SELECT * FROM sdb_member_deposit WHERE member_id='.intval($memberid));
    }/*}}}*/
    function check_name_inuc($uname){
        $passport = $this->system->loadModel('member/passport');
        if ($obj=$passport->function_judge('checkuser')){
            return $obj->checkuser($uname);
        }
        else{
            return true;
        }
    }
     function getMemberPluginUser($username){
        $row = $this->db->selectrow("SELECT * FROM sdb_members WHERE uname = ".$this->db->quote($username));
        if ($row){
            $row['secstr'] = $this->cookieValue($row['member_id']);
            return $row;
        }
        else{
            return false;
        }
    }
    function createUserFromPluin($data,&$message,$uid,$email=''){
        if ($data['passwd_r']){
            if($data['passwd']!=$data['passwd_r']){
                $message = '两次密码输入不一致！';
                return false;
            }
        }
        $data['uname'] = trim(strtolower($data['uname']));
        $data['email'] = trim(strtolower($data['email']));
        $data['reg_ip'] = remote_addr();
        $data['regtime'] = time();
        $data['member_id'] = $uid;
        $row = $this->db->selectrow('select * from sdb_member_lv where default_lv="1"');
        $data['member_lv_id'] = $row['member_lv_id']?$row['member_lv_id']:0;
        $defcur = $this->db->selectrow('select cur_code from sdb_currency where def_cur="true"');
        $data['cur'] = $defcur['cur_code'];
        $rs = $this->db->exec('select * from sdb_members where uname='.$this->db->quote($data['uname']).' or email='.$this->db->quote($data['email']));
        $data['password'] = md5($data['passwd']);
        getRefer($data);
        $sql = $this->db->getInsertSQL($rs,$data);
        if($this->db->exec($sql)){
            $userId = $this->db->lastInsertId();
            $status = $this->system->loadModel('system/status');
            $status->add('MEMBER_REG');
            $this->init($userId);
            $this->fireEvent('register',$data,$userId);        //会员注册成功事件
            $sql = 'select member_id,member_lv_id,email,uname,password,unreadmsg,cur,lang,point from sdb_members where member_id='.intval($userId);
            $row = $this->db->selectrow($sql);
            $row['secstr'] = $this->cookieValue($userId);
            return $row;
        }else{
            return false;
        }
    }
    function PlugUserExit(){
         $this->system->setCookie('MEMBER', '', time()-1000);
         $this->system->setCookie('MLV', '', time()-1000);
         $this->system->setCookie('CART', '', time()-1000);
         $this->system->setCookie('UNAME', '', time()-1000);
    }
    function PlugUserSetCookie($row){
        $this->system->setCookie('MEMBER',$row['secstr'],null);
        $this->system->setCookie('UNAME',$row['uname'],null);
        $this->system->setCookie('MLV',$row['member_lv_id'],null);
        $this->system->setCookie('CUR',$row['cur'],null);
        $this->system->setCookie('LANG',$row['lang'],null);
    }
    function PlugUserRegist($userdb='',$memberid='',$username='',$password='',$email=''){
        if (is_array($userdb)){
            $res=$this->db->selectrow('SELECT * FROM sdb_members where uname='.$this->db->quote($userdb['username']));
            if (!$res){
                $data['uname'] = trim($userdb['username']);
                $data['reg_ip'] = remote_addr();
                $data['regtime'] = $userdb['time'];
                $data['password'] = $userdb['password'];
                $data['email'] = $userdb['email'];
                $defcur = $this->db->selectrow('select cur_code from sdb_currency where def_cur="true"');
                $data['cur'] = $defcur['cur_code'];
                getRefer($data);
                $row = $this->db->selectrow('select * from sdb_member_lv where default_lv="1"');
                $data['member_lv_id'] = $row['member_lv_id']?$row['member_lv_id']:0;
                $rs = $this->db->exec('select * from sdb_members where 0=1');
                $sql = $this->db->getInsertSQL($rs,$data);
                if (!$sql || $this->db->exec($sql)){
                    $userId=$this->db->lastInsertId();
                    $status = $this->system->loadModel('system/status');
                    $status->add('MEMBER_REG');
                    $this->init($userId);
                    $this->fireEvent('register',$data,$userId);        //会员注册成功事件
                }
            }
            else
                $this->PlugUserUpdate($userdb);
            $username = $userdb['username'];
        }
        else{
            $res=$this->db->selectrow('SELECT * FROM sdb_members where member_id='.$memberid);
            if (!$res){
                $data['member_id'] = $memberid;
                $data['uname'] = trim(strtolower($username));
                $data['reg_ip'] = remote_addr();
                $data['regtime'] = trim(time());
                $data['password'] = md5('123456');
                $data['email'] = $email;
                $defcur = $this->db->selectrow('select cur_code from sdb_currency where def_cur="true"');
                $data['cur'] = $defcur['cur_code'];
                getRefer($data);
                $row = $this->db->selectrow('select * from sdb_member_lv where default_lv="1"');
                $data['member_lv_id'] = $row['member_lv_id']?$row['member_lv_id']:0;
                $rs = $this->db->exec('select * from sdb_members where 0=1');
                $sql = $this->db->getInsertSQL($rs,$data);
                if (!$sql || $this->db->exec($sql)){
                    $userId=$this->db->lastInsertId();
                    $this->init($userId);
                    $this->fireEvent('register',$data,$userId);        //会员注册成功事件
                }
            }
        }
        $sql = 'select member_id,member_lv_id,email,uname,b_year,b_month,b_day,password,unreadmsg,cur,lang,point from sdb_members where uname='.$this->db->quote($username);
        if($row = $this->db->selectrow($sql)){
            $row['secstr'] = $this->cookieValue($row['member_id']);
            $oMsg = $this->system->loadModel('resources/msgbox');
            $row['unreadmsg'] = $oMsg->getNewMessageNum($row['member_id']);
            $this->PlugUserSetCookie($row);
        }
        return false;
    }
    function PlugUserUpdate($userdb){
        $data['password'] = $userdb['password'];
        $data['email'] = $userdb['email'];
        $data['reg_ip'] = remote_addr();
        $data['regtime'] = $userdb['time'];
        $rs = $this->db->exec('SELECT * FROM sdb_members where uname='.$this->db->quote($userdb['username']));
        $sql = $this->db->getUpdateSQL($rs,$data);
        if (!$sql || $this->db->exec($sql)){}else return false;
    }
    function PlugUserDelete($param){
        if($param){
            $sql="delete from sdb_members where member_id in ($param)";
            $this->db->exec($sql);
        }
    }
    function setPlugCookie($k,$v){
        $this->system->setCookie($k,$v);
    }
    function getPlugCookie($k){
        return $_COOKIE[$k];
    }
    function adminUpdateMemberPassword($nMId,$aData,$sendemail){
        $rs = $this->db->exec("select password from sdb_members where member_id='".$nMId."'");
        $sql = $this->db->getUpdateSQL($rs,$aData);
        if (!$sql || $this->db->exec($sql)){
            if ($sendemail)
                $this->fireEvent('chgpass',$aData,$nMId);
            return true;
        }
        else return false;
    }
}
?>
