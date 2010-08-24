<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_members extends dbeav_model{
    var $has_tag = true;
    var $defaultOrder = array('regtime','DESC');
    var $has_many = array(
        'contact/other'=>'member_addrs:append',
        'advance/event'=>'member_advance:append',
        'score/event'=>'member_point:append',
    );
    var $has_parent = array(
        'pam_account' => 'account@pam'
    );
    
    var $subSdf = array(
        'default' => array(
            'pam_account:account@pam' => array('*'),
         )
    );
    
    function __construct($app){
        parent::__construct($app);
        $this->use_meta();  //member中的扩展属性将通过meta系统进行存储
    }
    
    function save(&$sdf,$mustUpdate=null){
        if(isset($sdf['member_id']) && !isset($sdf['pam_account']['account_id'] )){
            $sdf['pam_account']['account_id'] = $sdf['member_id'];
        }
        if(isset($sdf['profile']['gender'])){
            if($sdf['profile']['gender']=='male'){
            $sdf['profile']['gender']=1;
            }elseif($sdf['profile']['gender']=='female'){
               $sdf['profile']['gender']=0;
            }else{
                unset($sdf['profile']['gender']);
            }
        }
        if(isset($sdf['profile']['birthday'])){
              $data = explode('-',$sdf['profile']['birthday']);
              $sdf['b_year']=intval($data[0]);$sdf['b_month']=intval($data[1]);$sdf['b_day']=intval($data[2]);
            unset($sdf['profile']['birthday']);
        }
        $sdf['contact']['addr'] = htmlspecialchars($sdf['contact']['addr']);
        parent::save($sdf);
        return true;
    }
    
        
    function dump($filter,$field = '*',$subSdf = null){
        if($ret = parent::dump($filter,$field,$subSdf)){
           $ret['profile']['birthday'] = $ret['b_year'].'-'.$ret['b_month'].'-'.$ret['b_day'];  
           $ret['profile']['gender'] = $ret['profile']['gender'] == 1 ? 'male' : 'female';
      }
        return $ret;
    }
    
    function edit($nMemberId,$aMemInfo){
        $sdf=$this->dump($nMemberId,'*');
        $sdf['profile']['gender'] = $aMemInfo['gender'];
        $sdf['contact']['name'] = $aMemInfo['name'];
        $sdf['contact']['area'] = $aMemInfo['area'];
        $sdf['contact']['addr'] = $aMemInfo['addr'];
        $sdf['contact']['zipcode'] = $aMemInfo['zipcode'];
        $sdf['contact']['email'] = $aMemInfo['email'];
        $sdf['contact']['phone']['telephone'] = $aMemInfo['telephone'];
        $sdf['contact']['phone']['mobile'] = $aMemInfo['mobile'];
        $sdf['member_lv']['member_group_id'] = $aMemInfo['member_group_id'];
        $sdf['account']['pw_question'] = $aMemInfo['pw_question'];
        $sdf['account']['pw_answer'] = $aMemInfo['pw_answer'];
        if(is_numeric($aMemInfo['birthday'])){
            $aMemInfo['birthday'] = date('Y-m-d',$aMemInfo['birthday']);
        }
        $sdf['profile']['birthday'] = $aMemInfo['birthday'];

        return $this->save($sdf);      
    
    }
    
     //密码修改
    function save_security($nMemberId,$aData,&$msg){  
        
        $aMem = $this->dump($nMemberId,'*',array(':account@pam'=>array('*')));
        if(!$aMem){
            $msg='无效的用户Id';
            return false;
        }
        $member_sdf['member_id'] = $nMemberId;
        //如果密码是空的则进入安全问题修改过程
        if(empty($aData['passwd'])){
            if( !$aData['pw_answer'] || !$aData['pw_question'] ){
                $msg='安全问题修改失败！';
                return false;
            }
            $member_sdf = $this->dump($nMemberId,'*');
            $member_sdf['account']['pw_question'] = $aData['pw_question'];
            $member_sdf['account']['pw_answer'] = $aData['pw_answer'];
             $msg='安全问题修改成功';
            return $this->save($member_sdf);
        } else{  
            if(md5($aData['old_passwd']) != $aMem['pam_account']['login_password']){
                $msg='输入的旧密码与原密码不符！';
                return false;
            }

            if($aData['passwd'] != $aData['passwd_re']){
                $msg='两次输入的密码不一致！';
                return false;    
            }
            
            if( strlen($aData['passwd']) <  4 ){
                 $msg='密码长度不能小于4';
                 return false;
             }
                
             if( strlen($aData['passwd']) > 20 ){
                 $msg='密码长度不能大于20';
                 return false;                 
             }   

             //$member_sdf['account']['password'] =  md5($aData['passwd']);
             $aMem['pam_account']['login_password'] = md5($aData['passwd_re']);
             $aMem['pam_account']['account_id'] = $nMemberId;
             if($this->save($aMem)){
                $aData = array_merge($aMem,$aData);
                $data['email'] = $aMem['contact']['email'];
                $data['uname'] = $aMem['pam_account']['login_name'];
                $data['passwd'] = $aData['passwd_re'];
                $obj_account=&$this->app->model('member_account');
                   $obj_account->fireEvent('chgpass',$data,$nMemberId);
                $msg = "密码修改成功";
                 return true;
             }else{
                $msg='密码修改失败！';
                return false;                
             }
         }
     }
     
    function getMemberByUser($uname)    {
        if($ret = $this->getList('*',array('pam_account'=>array('login_name'=>$uname)) )){
            return $ret[0];
        }       
        return false;
     }
     
     /*根据查询字符串返回UNMAE 数组
       litie@shopex.cn
     */
     function getUserNameLikeStr($str,$dataType='json'){
        
         if(!$str||$str !=''){
            $sql  = 'select uname from '.$this->tableName.' where uname like "'.$str.'%" and disabled=false';         
         }else if($str == '_ALL_'){
            $sql  = 'select uname from '.$this->tableName.' where disabled=false';
         }
         $data = $this->db->select($sql);
         
         if($dataType!='json')return $data;
         
         return json_decode($data,true);

     }
     
     
     function getMemberAddr($nMemberId){
            $objMemberAddr = $this->app->model('member_addrs');
            return $objMemberAddr->getList('*',array('member_id'=>$nMemberId));            
     }
     
     function getAddrById($nAddrId){
            $objMemberAddr = $this->app->model('member_addrs');
            return $objMemberAddr->dump($nAddrId);
     }
     
      function isAllowAddr($nMemberId){
         $objMemberAddr = $this->app->model('member_addrs');
         $aAddr = $objMemberAddr->getList('addr_id',array('member_id'=>$nMemberId));
         if(count($aAddr) < 5){
            return true;
        }else{
            return false;
        }
    }
    
     //插入收货人地址
    function insertRec($aData,$nMemberId,&$message){
       // print_r($aData);exit;
        foreach ($aData as $key=>$val){
            if(is_string($val))
            $aData[$key] = trim($val);
            if(empty($aData[$key])){
                switch ($key){
                case 'name':
                    $message = __('姓名不能为空！');
                    return false;
                    break;
                default:
                    break;
                }
            }
        }
        if($aData['phone']['telephone'] == '' && $aData['phone']['mobile'] == ''){
            $message = __('联系电话和手机不能都为空！');
            return false;
        }

        $aData['member_id'] = $nMemberId;
        $at = explode(':',$aData['area']);
        $area['area_type'] = $at[0];
        $area['sar'] = explode('/',$at[1]);
        $area['id'] = $at[2];
        $aData['area'] = $area;
        
        $objMemberAddr = $this->app->model('member_addrs');
        if($objMemberAddr->save($aData)){
            $message = __('保存成功！');
            return true;
        }else{
            $message = __('保存失败！');
            return false;
        }
    }
    
      //设为默认收获地址
    function set_to_def($addrId,$nMemberId,&$message,$disabled){
        $disabled = intval($disabled);
        if($addrId){
           $objMemberAddr = $this->app->model('member_addrs');
           if( $objMemberAddr->getList('*',array('member_id'=>$nMemberId,'def_addr'=>1)) and $disabled === 2){
                $message = __('已存在默认收货地址，不能重复设置');
                return false;
            }    
            $data['def_addr'] = $disabled === 2 ? 1 : 0;
            $filter = array('addr_id'=> $addrId);
            if($objMemberAddr->update($data,$filter)){
                return true;
            }else{
               $message = __('设置失败！');
                return false;
            }
        }else{
            return false;
            $message = __('参数错误！');
        }
    }
    
      //保存修改
    function save_rec($aData,$nMemberId,&$message){
        #print_r($aData);exit;
        $objMemberAddr = $this->app->model('member_addrs');
        if($aData['default'] ){           
             $row = $objMemberAddr->getList('addr_id',array('member_id'=>$nMemberId,'def_addr'=>1));
             $defaultAddrId = $row['0']['addr_id'];
             //关闭当前默认地址
             if($defaultAddrId != $aData['addr_id']){
                $addr_sdf['addr_id'] = $defaultAddrId;
                $addr_sdf['default'] = 0;
                $objMemberAddr->save($addr_sdf);  
             }
        }
        $at = explode(':',$aData['area']);
        $area['area_type'] = $at[0];
        $area['sar'] = explode('/',$at[1]);
        $area['id'] = $at[2];
        $aData['area'] = $area;
         if($objMemberAddr->save($aData)){
            return true;
        }else{
            return false;
        }
    }
    //删除
    function del_rec($addrId,&$message){
        if($addrId){
            $member_addr = &$this->app->model('member_addrs');
             $filter = array('addr_id'=>$addrId);
             $member_addr->delete($filter);
               $meesage = __("删除成功");
               return true;
        }else{
            $meesage = __("参数有误");
             return false;
        }
       
    }
    function checkUname($uname,&$message){
        $uname = trim($uname);
        $len = strlen($uname);
        if($len<3){
            $message = __('用户名过短!');
            return false;
        }elseif($len>20){
            $message = __('用户名过长!');
            return false;
        }elseif(!preg_match('/^([@\.]|[^\x00-\x2f^\x3a-\x40]){2,20}$/i', $uname)){
            $message = __('用户名包含非法字符!');
            return false;
        }else{
            $row = $this->db->selectrow("select uname from sdb_b2c_members where uname='{$uname}'");
            if($row['uname']){
                $message = __('重复的用户名!');
                return false;
            }else{
                return true;
            }
        }
    }
    
    function get_id_by_uname($uname){
        $pam_account = app::get('pam')->model('account');
        if($ret = $pam_account->getList('account_id',array('login_name'=>$uname)) ){
            return $ret[0]['account_id'];
        }
       
        return false;
    }
        

    function getOrderByMemId($nMemberId){
        $objOrder = $this->app->model('orders');
        $aOrderList = $objOrder->getList('order_id,status,pay_status,ship_status,total_amount,createtime ',array('member_id'=>$nMemberId ));
        return $aOrderList;   
    }
    
    function  getRemarkByMemId($nMemberId){
        $row = $this->getList('remark,remark_type',array('member_id'=>$nMemberId ));
        return $row[0];
    }
    
   ##注册会员检查
    function validate(&$data,&$msg){
        $flg = 1;
        $unamelen = strlen($data['pam_account']['login_name']);
        if($unamelen < 3){
            $msg = __('长度不能小于3');
            $flg = 0;
            #trigger_error(__('长度不能小于3'),E_USER_ERROR);
        }
        if($this->is_exists($data['pam_account']['login_name'])){
            $msg = __('该用户名已经存在');
            $flg = 0;
            #trigger_error(__('该用户名已经存在'),E_USER_ERROR);
        }
       if(!preg_match('/\S+@\S+/',$data['contact']['email'])){
               $msg = __('邮件格式不正确');
               $flg = 0;
            #trigger_error(__('邮件格式不正确'),E_USER_ERROR);
        }
        $passwdlen = strlen($data['pam_account']['login_password']);
        if($passwdlen<4){
            
            $msg = __('密码长度不能小于4');
            $flg = 0;
            #trigger_error(__('密码长度不能小于4'),E_USER_ERROR);
        }
        if($passwdLen>20){
            $msg = __('密码长度不能大于20');
            $flg = 0;
            #trigger_error(__('密码长度不能大于20'),E_USER_ERROR);
        }        
        if($data['pam_account']['login_password'] != $data['pam_account']['psw_confirm']){
            $msg = __('输入的密码不一致');
            $flg = 0;
           #trigger_error(__('输入的密码不一致'),E_USER_ERROR);
        } 
        if($data['contact']['name']&&!preg_match('/^([@\.]|[^\x00-\x2f^\x3a-\x40]){2,20}$/i', $data['contact']['name'])){
            $msg = __('用户姓名含非法字符');
            $flg = 0;
        }
        return $flg;               
    } 
    
    function gen_secret_str($member_id){
        $row=app::get('pam')->model('account')->dump($member_id);
        $row['login_name'] = md5($row['login_name']);
        $row['login_password'] = md5($row['login_password'].STORE_KEY);
        return $member_id.'-'.utf8_encode($row['login_name']).'-'.$row['login_password'].'-'.time();
    }
    
    function create($data){
        $arrDefCurrency = app::get('ectools')->model('currency')->getDefault();
        $data['currency'] = $arrDefCurrency['cur_code'];
        $data['pam_account']['login_password'] = md5(trim($data['pam_account']['login_password']));
        $data['pam_account']['account_type'] = pam_account::get_account_type($this->app->app_id);
        $data['pam_account']['createtime'] = time();
        $data['reg_ip'] = base_request::get_remote_addr(); 
        $data['regtime'] = time();
        $data['pam_account']['login_name'] = strtolower($data['pam_account']['login_name']);
        $this->save($data);
        return $data['member_id'];
    }
    
    function is_exists($uname){
        $account_type = pam_account::get_account_type($this->app->app_id);
        $obj_pam_account = new pam_account($account_type);
        return $obj_pam_account->is_exists($uname);
    }
    ####修改经验值
    function change_exp($member_id,$experience){
        $aMem = $this->dump($member_id,'*',array('contact'=>array('*'))); 
        if($experience<0){
            if($aMem['experience']<-$experience){
                echo "经验值不足";return false;
            }
        }
        $experience += $aMem['experience'];
        $aMem['experience'] = $experience; 
        if($this->app->getConf('site.level_switch')==1){
            $aMem['member_lv']['member_group_id'] = $this->member_lv_chk($experience);
        }
        $aMem['member_id'] = $member_id;
        if($aMem['member_id'] && $this->save($aMem)){
                return true;
        }else{
                return false;
         }
        }
        
     ###根据经验值修改会员等级
    
    function member_lv_chk($experience){
        $objmember_lv = $this->app->model('member_lv');
        $sdf_lv = $objmember_lv->getList('*');
        $member_lv_id = $objmember_lv->get_default_lv();
        foreach($sdf_lv as $sdf){
         if($experience>=$sdf['experience']) {$member_lv_id = $sdf['member_lv_id'];
          }
          else{
              
          }
        }
        return $member_lv_id;
    }   
    ##进回收站前操作
     function pre_recycle($data){
        $falg = true;
        $obj_pam = app::get('pam')->model('account');
        foreach($data as $val){
            if($val['advance']>0) 
            { 
                $this->recycle_msg = '会员存在预存款,不能删除';
                $falg = false;
            break;
            }
        }
        return $falg;
   }
   
    function pre_restore(&$data,$restore_type='add'){ 
         if(!($this->is_exists($data['pam_account']['login_name']))){
             return true;
         }
         else{

             if($restore_type == 'add'){
                    $new_name = $data['pam_account']['login_name'].'_1';
                    while($this->is_exists($new_name)){
                        $new_name = $new_name.'_1';
                    }
                    $data['pam_account']['login_name'] = $new_name;
                    $data['need_delete'] = true;
                 return true;    
             }
             if($restore_type == 'none'){
                 $data['need_delete'] = false;
                 return true;
             }
         }
    }

       
    
}  
