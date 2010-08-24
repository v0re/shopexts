<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_member_pwdlog extends dbeav_model{

    var $validtime = 86400;    
    
    function generate($nMemberId){
        $secret = $this->_randomstring(32);
        $sdf = array(
            'member_id' => $nMemberId,
            'secret' => $secret ,
            'expiretime' => time() + $this->validtime, 
        );
        if($this->save($sdf)){
            return $this->_implode($sdf);
        }
        return false;
    }
    
    function _randomstring($len){
        $source = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%^&*()_+,./;[]<>?:"{}|';
        $ret = '';
        for(;$len>=1;$len--)   {
            $position=rand()%strlen($source);
            $ret .= substr($source,$position,1);        
        }
        return $ret;    
    }
    
    function _implode(&$sdf){
        #字符窜带上id方便查询
        return base64_encode($sdf['secret']).'@'.$sdf['member_repass_id'];
    }
    
    function _explode($string){
        $ret = explode('@',$string);
        $ret[0] = base64_decode($ret[0]);
        return $ret; 
    }
    
    function _isExpired(&$row){
        return time() - $row['expiretime'] < $this->validtime ? true : false;
    }
    
    function isValiad($string){
        $data = $this->_explode($string);
        $row = $this->dump($data[1]);
        if($row['secret'] == $data[0] && $row['is_used'] == 'N' && !$this->_isExpired()  ){
            return true;
        }
        return false;
    }
    
    function setUsed($string){
        $data = $this->_explode($string);
        $sdf['member_repass_id'] = $data[1];
        $sdf['is_used'] = 'Y';
        $this->save($sdf);
    }
    
    function rePass($data){
         $string = $data['secret'];
         $sec = $this->_explode($string);
         $row = $this->dump($sec[1]);
         if($row['secret'] == $sec[0] && $row['is_used'] == 'N' &&  !$this->_isExpired() ){
            $objMember = &$this->app->model('members');
            $sdf['member_id'] = $row['member_id'];
            $sdf['account']['password'] = md5($data['password']);
            if($objMember->save($sdf)){
                $this->setUsed($string);
                return true;
            }
            return false;
        }         
    }

}  
