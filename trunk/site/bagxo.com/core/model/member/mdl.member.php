<?php
include_once('shopObject.php');
class mdl_member extends shopObject {

    var $defaultCols = 'uname,name,mobile,member_lv_id,email,regtime,province,sex,tel,refer_id,remark,remark_type';
    var $idColumn = 'member_id'; //表示id的列
    var $adminCtl = 'member/member';
    var $textColumn = 'uname';
    var $defaultOrder = array('member_id','desc');
    var $tableName = 'sdb_members';
    var $hasTag = true;         
    var $appendCols = 'remark_type';

    function getColumns(){
      $col =  array(
                'member_id'=>array('label'=>'会员用户名','class'=>'span-3','readonly'=>true),    /* 会员id */
                'uname'=>array('label'=>'用户名','class'=>'span-2','required'=>1),    /* 登陆名称 */
                'name'=>array('label'=>'姓名','class'=>'span-2','type'=>'name'),    /* 会员姓名 */
                'member_lv_id'=>array('label'=>'会员等级','class'=>'span-2','type'=>'object:memlevel'),    /* 会员等级id */
                'mobile'=>array('label'=>'手机','class'=>'span-2','fuzzySearch'=>1),    /* 手机 */
                'tel'=>array('label'=>'固定电话','class'=>'span-3','fuzzySearch'=>1),    /* 固定电话 */
                'email'=>array('label'=>'电子邮件','class'=>'span-3','required'=>1,'fuzzySearch'=>1),    /* 电子邮件 */
                'zip'=>array('label'=>'邮编','class'=>'span-3'),    /* 邮编 */
                'addr'=>array('label'=>'地址','class'=>'span-3'),    /* 地址 */
                'order_num'=>array('label'=>'订单数','class'=>'span-3','readonly'=>true),    /* 订单数 */
                'area'=>array('label'=>'地区','class'=>'span-3','type'=>'region','readonly'=>true),    /* 地区 */
                'b_year'=>array('label'=>'生年','class'=>'span-1'),    /* 会员生日 */
                'b_month'=>array('label'=>'生月','class'=>'span-1'),    /* 会员生日 */
                'b_day'=>array('label'=>'生日','class'=>'span-1'),    /* 会员生日 */
                
                'refer_id'=>array('label'=>'来源ID','class'=>'span-2','type'=>'refer'),
                'refer_url'=>array('label'=>'来源网址','class'=>'span-2','type'=>'refer'),
                'sex'=>array('label'=>'性别','class'=>'span-1','type'=>'enum','options'=>array(0=>'女',1=>'男')),    /* 会员性别(0女1男) */
//                'addon'=>array('label'=>'爱好','class'=>'span-3'),    /* 爱好 */
//                'wedlock'=>array('label'=>'婚姻状况','class'=>'span-3','type'=>'enum','options'=>array(0=>'未婚',1=>'已婚')),    /* 婚姻状况 */
//                'education'=>array('label'=>'教育程度','class'=>'span-3'),    /* 教育程度 */
//                'vocation'=>array('label'=>'职业','class'=>'span-3'),    /* 职业 */
//                'interest'=>array('label'=>'扩展信息里的爱好','class'=>'span-3','readonly'=>true),    /* 扩展信息里的爱好 */
                'remark'=>array('label'=>'备注','class'=>'span-2','type'=>'remark','modifier'=>'row'),
                'advance'=>array('label'=>'预存款','class'=>'span-3','type'=>'money','readonly'=>1),    /* 会员账户余额 */
//        'point_freeze'=>array('label'=>'会员当前冻结积分(暂时停用)','class'=>'span-3','readonly'=>true),    /* 会员当前冻结积分(暂时停用) */
//        'point_history'=>array('label'=>'会员历史总积分(暂时停用)','class'=>'span-3','readonly'=>true),    /* 会员历史总积分(暂时停用) */
                'point'=>array('label'=>'积分','class'=>'span-3'),    /* 会员当前积分 */
//                'score_rate'=>array('label'=>'积分折换率','class'=>'span-3'),    /* 积分折换率 */
                'reg_ip'=>array('label'=>'注册IP地址','class'=>'span-3','readonly'=>true),    /* 注册IP地址 */
                'regtime'=>array('label'=>'注册时间','class'=>'span-2','type'=>'time'),    /* 注册时间 */
                'state'=>array('label'=>'会员验证状态','class'=>'span-3','readonly'=>true),    /* 会员验证状态 */
//        'pay_time'=>array('label'=>'上次结算时间','class'=>'span-3'),    /* 上次结算时间 */
//        'biz_money'=>array('label'=>'上次结算后到现在的所有因商业合作（推广人，代理）','class'=>'span-3'),    /* 上次结算后到现在的所有因商业合作（推广人，代理） 而产生的可供结算的金额 */
//        'pw_answer'=>array('label'=>'会员取回密码答案','class'=>'span-3','readonly'=>true),    /* 会员取回密码答案 */
//        'pw_question'=>array('label'=>'会员取回密码问题','class'=>'span-3','readonly'=>true),    /* 会员取回密码问题 */
//        'fav_tags'=>array('label'=>'会员感兴趣的tag','class'=>'span-3'),    /* 会员感兴趣的tag */
                'cur'=>array('label'=>'货币','class'=>'span-3'),    /* 货币 */
                'lang'=>array('label'=>'语言','class'=>'span-3'),    /* 语言 */
                'unreadmsg'=>array('label'=>'未读信息数','class'=>'span-3','readonly'=>true),    /* 未读信息数 */            
                );
                
                $memattr = $this->system->loadModel('member/memberattr');
                $memattrdate = $memattr->getCustomOption();
                for($i=0;$i<count($memattrdate);$i++){
                                   $col['attr__'.$memattrdate[$i]['attr_id']]['label'] = $memattrdate[$i]['attr_name'];
                                   $col['attr__'.$memattrdate[$i]['attr_id']]['class'] ='span-2';
                                   $col['attr__'.$memattrdate[$i]['attr_id']]['custom'] ='yes';
                                          if($memattrdate[$i]['attr_type'] == 'checkbox'){
                                                 $col['attr__'.$memattrdate[$i]['attr_id']]['readonly'] = true;
                                          }
                                          if($memattrdate[$i]['attr_required'] == 'true'){
                                                 $col['attr__'.$memattrdate[$i]['attr_id']]['required'] = true;
                                          }
                                                    
                                          if($memattrdate[$i]['attr_type'] == 'select'){
                                                 $col['attr__'.$memattrdate[$i]['attr_id']]['type'] = 'enum'; 
                                                 $select[''] = "- 请选择 -";    
                                                 $option = unserialize($memattrdate[$i]['attr_option']);    
                                                        for($k=0;$k<count($option);$k++){
                                                        $select[$option[$k]] = $option[$k];
                                                        }    
                                                
                                                 $col['attr__'.$memattrdate[$i]['attr_id']]['options'] = $select;
                                                 unset($select);
                                          }    
                }
                return $col;
    }

       function searchOptions(){
              $search =  array(
                            'uname'=>'用户名',
                            'name'=>'姓名',
                            'remark'=>'备注',
                            'email'=>'E-Mail',
                            'mobile'=>'手机',
                            'tel'=>'电话',
                     );
                     $memattr = $this->system->loadModel('member/memberattr');
                     $filter['attr_group'] = 'contact';
                     $memattrdate = $memattr->getList('*',$filter,0,-1);
                     for($i=0;$i<count($memattrdate);$i++){
                     $search[$memattrdate[$i]['attr_id']] = $memattrdate[$i]['attr_name'];
                     }       
                     return  $search;
       }

    function getList($Cols,$filter,$nStart=0,$nLimit=null,&$count,$orderByType){
        $attr_id ="";
        $searchcountd= 0;

        if(!strstr('member_id',$Cols)){
            $Cols.= ',member_id ';
        }
        
        //处理单个搜索
        if(is_array($filter)){
            foreach($filter as $k => $v){
                $searchtmpdate = explode('___',$k);
                if(is_numeric($k)){
                    $attr_id = $k;
                    $findvalue = $v;
                }
                //处理多个搜索
                if(count($searchtmpdate)==2&&!in_array('_ANY_',$v)){ 
                    $searchoption[$searchcountd]['key'] = $searchtmpdate[1];
                    $searchoption[$searchcountd]['value'] = $v;
                    $searchcountd++;
                }
            }
        }
        
        $tmpcols = explode(",",$Cols);
            foreach($tmpcols as $key =>$value){
                if(strstr($value,'attr__')){
                    $new = explode('__',$value);
                    $custom[] = $new[1];
                }else{
                    $nowcols[] = $value;
                }
        }

        $Cols = implode(",",$nowcols);

        if(preg_match('/__+/',$orderByType[0])){
                $t_attr_id = explode("__",$orderByType[0]);
                $exc_sql = "SELECT a.member_id,b.value FROM sdb_members a LEFT JOIN sdb_member_mattrvalue b ON a.member_id = b.member_id WHERE b.attr_id";
                $exc_sql.="='".$t_attr_id[1]."' ORDER BY b.value ".$orderByType[1];
                $tmppdata = $this->db->select($exc_sql);
                foreach($tmppdata as $k => $v){
                    if($v['value']!=""){
                    $tmpmid[] =  $tmppdata[$k]['member_id'];
                    }
                }
                if($orderby[1]=='desc'){
                    krsort($tmpmid);
                }
                $orderByType[0] = "FIELD(member_id,".implode(",",$tmpmid).")";
         }
         $list = parent::getList($Cols,$filter,$nStart,$nLimit,$count,$orderByType); 
         
    
         foreach($list as $k=>$v){
             foreach($custom as $tk=>$tv){
                 $data = $this->getattrvalue($v['member_id'],$tv);
                 if(count($data<=1)){
                     $list[$k]['attr__'.$tv] = $data[0]['value'];
                     $selsql = "select attr_type from sdb_member_attr where attr_id = ".$tv."";
                     $tmpdata = $this->db->select($selsql);
                     if($tmpdata[0]['attr_type']=='cal'&&$data[0]['value']!=""){
                         $list[$k]['attr__'.$tv] = date('Y-m-d',$data[0]['value']);
                     }
                 }else{
                     foreach($data as $tpk => $tpv){
                         if($tpv['value']!=""){
                             $list[$k]['attr__'.$tv] .= $tpv['value'].";";
                         }
                     }
                 }
             }
         }


         //过滤自定义搜索
        if($attr_id!=''&&$findvalue!=''){
            foreach($list as $k=>$v){
                $data = $this->getattrvalue($v['member_id'],$attr_id); 
                if(!strstr($data[0]['value'],$findvalue)){
                    $delete[] = $k;
                }
            }
            foreach($delete as $k=>$v){
                unset($list[$v]);
            }
        }
        if(isset($searchoption)){ 
            foreach($list as $k=>$v){
                foreach($searchoption as $tk=>$tv){
                    $tmpvalue = $tv['value'];
                    foreach($tmpvalue as $ttk=>$ttv){
                        $countdata =  $this->getallattrvalue($v['member_id'],$tv['key'],$ttv);
                        if(count($countdata)==0){
                            $deletenow[] = $k;
                        }
                    }
                }
            }
        }
        foreach($deletenow as $k => $v){
            unset($list[$v]);
        }
        return $list;
    }
    

    function modifier_name(&$rows){
        foreach($rows as $k => $v){
            $rows[$k] = htmlspecialchars($rows[$k]);
        }
    }

    function modifier_remark($row){
        if($row['remark']!=''){
            return "<span  title=\"".$row['remark']."\"><img src=\"../statics/remark_icons/".$row['remark_type'].".gif\"></span>";
        }
    }

    function getByFilter($info){
        if($info['items']){
            $filter = array('member_id'=>$info['items']);
        }elseif($info['filter']){
            parse_str($info['filter'],$filter);
        }
        $sql = 'select email,mobile,tel,zip,member_id from sdb_members m left join sdb_member_lv l on l.member_lv_id=m.member_lv_id  where '.$this->_filter($filter).' order by member_id desc';
        return $this->db->select($sql);
    }

    function _filter($filter){
        $where=array(1);
        if(is_array($filter['member_lv_id'])){
            foreach($filter['member_lv_id'] as $lv){
                if($lv!='_ANY_'){
                    $member_lv[] = 'member_lv_id='.intval($lv);
                }
            }
            if(count($member_lv)>0){
                $where[] = '('.implode($member_lv,' or ').')';
            }
            $filter['member_lv_id'] = '';
        }

        if(isset($filter['name'])){
            $where[] = 'name LIKE \''.$filter['name'].'%\'';
            $filter['name'] = '';
        }

        if($filter['area']!=""){
            $pos = strrpos($filter['area'],':');
            $filter['area'] = substr($filter['area'],0,$pos);
            $where[] = 'area LIKE \''.$filter['area'].'%\'';
            $filter['area'] = '';
        }

        if(is_array($filter['tag'])){
            foreach($filter['tag'] as $tag){
                if($tag!='_ANY_'){
                    $aTag[] = intval($tag);
                }
            }
            if(count($aTag)>0){
                foreach($this->db->select('SELECT rel_id FROM sdb_tag_rel r
                    LEFT JOIN sdb_tags t ON r.tag_id=t.tag_id
                    WHERE t.tag_type = \'member\' AND r.tag_id IN('.implode(',', $aTag).')') as $rows){
                        $filter['member_id'][] = $rows['rel_id'];
                }
                if(empty($filter['member_id'])) $filter['member_id'][] = -1;
            }
            $filter['tag'] = null;
        }

        if(is_array($filter['point'])){
            foreach($filter['point'] as $point){
                if($point != '_ANY_'){
                    $aPoint = explode('-', $point);
                    $pointSql[] = 'point >= '.$aPoint[0].' AND point <= '.$aPoint[1];
                }
            }
            if(count($pointSql)) $where[] = '('.implode(' OR ', $pointSql).')';
            unset($filter['point']);
        }
        if ($filter['minregtime']&&$filter['maxregtime'])
            $where[] = ' (regtime>='.strtotime($filter['minregtime']).' AND regtime<='.strtotime($filter['maxregtime'].' 23:59:59').') ';
        elseif ($filter['minregtime'])
            $where[] = "regtime>=".strtotime($filter['minregtime']);
        elseif ($filter['maxregtime'])
            $where[] = "regtime<=".strtotime($filter['maxregtime'].' 23:59:59');
        if ($filter['minadvance']&&$filter['maxadvance']){
            if ($filter['minadvance']==$filter['maxadvance']){
                $where[] = 'advance='.$filter['minadvance'];
            }
            elseif ($filter['minadvance']<$filter['maxadvance']){
                $where[] = ' (advance>='.$filter['minadvance'].' AND advance<='.$filter['maxadvance'].') ';
            }
        }
        elseif ($filter['minadvance'])
            $where[] = 'advance>='.$filter['minadvance'];
        elseif ($filter['maxadvance'])
            $where[] = 'advance<='.$filter['maxadvance'];
        return parent::_filter($filter).' AND '.implode($where,' AND ');
    }

    function setLevel($member_lv_id,$finderResult){
        if(count($finderResult['items'])>0){
            $member_id = 'member_id in ('.implode(',',$finderResult['items']).')';
        }else{
            $member_id = '';
        }
        $where = $finderResult['filter']? $this->_filter($finderResult['filter']):$member_id;
        $sql = 'update sdb_members set member_lv_id='.intval($member_lv_id).' where '.$where;
        return $this->db->exec($sql);
    }

    function getFilter(){
        $return = array();
        $memberLv = $this->system->loadModel('member/level');
        $return['member_lv'] = $memberLv->getList('member_lv_id,name');

        $modTag = $this->system->loadModel('system/tag');
        $return['tags'] = $modTag->tagList('member');

        $row = $this->db->selectrow('SELECT max(advance) as advance,max(point) as point,max(regtime) as regtime FROM sdb_members');
        $return['advance'] = steprange(0,$row['advance'],5);
        $return['score'] = steprange(0,$row['point'],5);
        $return['sex']=array(
            array('key'=>1,'val'=>'男'),
            array('key'=>0,'val'=>'女')
        ); 
        //取后台高级搜索项
        $memattr = $this->system->loadModel('member/memberattr');
        $filter['attr_group'] = 'select';
        $filter['attr_search'] = 'true';
        $date = $memattr->getList('*',$filter,0,-1);     
        for($i=0;$i<count($date);$i++){
                     $return['custom'][$i]['name'] = $date[$i]['attr_name'];
                     $return['custom'][$i]['attr_id'] = $date[$i]['attr_id'];
                     $return['custom'][$i]['option'] = unserialize($date[$i]['attr_option']);
        }
        $return['regtime'] = steprange(0,$row['regtime'],5);
        return $return;
    }

   
       function update($data,$filter){
        if(method_exists($this,'pre_update')){
            $this->pre_insert($data);
        }
        if(count($data)==0){
            return true;
        }
        $columnsList = $this->getColumns();

        $result = $this->db->exec('select * from '.$this->tableName.' where 0=1');
        for($i=0;$i<$result->FieldCount();$i++){
            $column = $result->FetchField($i);
            if(isset($data[$column->name])){
                if($column->type=='unknown' && $columnsList[$column->name]['type']=='money'){
                    $column->type = 'real'; //PHP_BUG http://bugs.php.net/bug.php?id=36069
                }
                if($columnsList[$column->name]['required'] && !$data[$column->name]){
                    trigger_error($columnsList[$column->name]['label'].'不能为空。',E_USER_WARNING);
                    $GLOBALS['php_errormsg'] = $php_errormsg;
                    return false;
                }
                $UpdateValues[] ='`'.$column->name.'`='.$this->db->_quotevalue($data[$column->name],$column->type,$this->db->_instance);
            }    
        }  
           $datakeys = array_keys($data);  
           for($i=0;$i<count($datakeys);$i++){
           $tmpdate =  explode('__',$datakeys[$i]);
           if(count($tmpdate)==2){
               $memberupdate['value'] =  $data[$datakeys[$i]];
               $memberupdate['attr_id'] =  $tmpdate[1];
               $memberupdate['member_id'] =  $filter['member_id'];
               $test++;
               $this->updateMemAttr($memberupdate['member_id'],$memberupdate['attr_id'],$memberupdate);
           };  
           }
        if(count($UpdateValues)>0){
            $sql = 'update '.$this->tableName.' set '.implode(',',$UpdateValues).' where '.$this->_filter($filter);
           if($this->db->exec($sql)){
               if($this->db->affect_row()){
                    return $this->db->affect_row();
               }else{
                    return true;
               }
           }else{
                return false;
           }

        }
    }
 /************************ 会员信息-BEGIN ************************/
       function getMemberInfo($nMemberId){
         return $this->db->selectrow("SELECT                  member_id,uname,name,firstname,lastname,sex,b_year,area,b_month,b_day,addr,zip,email,tel,mobile,custom FROM sdb_members WHERE member_id=".$nMemberId);
    }

    function getWelcomeInfo($nMemberId) {
        $arr = $this->db->selectrow("SELECT count(*) as oNum  FROM `sdb_orders` WHERE pay_status=0 and member_id={$nMemberId} and status = 'active' and disabled = 'false'");
        $totalOrder = $this->db->selectrow("SELECT count(*) as totalOrder  FROM `sdb_orders` WHERE member_id={$nMemberId}");
        $arr['totalOrder'] = $totalOrder['totalOrder'];
        $mNum = $this->db->selectrow("SELECT count(*) as mNum FROM `sdb_message` where `to_id`={$nMemberId} and `unread`='0' and to_type=0 and disabled='false' and del_status != '1'");
        $arr['mNum'] = $mNum['mNum'];
        $pNum = $this->db->selectrow("SELECT sum(point) as pNum FROM sdb_members WHERE member_id={$nMemberId}");
        $arr['pNum'] =intval($pNum['pNum']);
        $advance = $this->db->selectrow("SELECT advance FROM sdb_members WHERE member_id={$nMemberId}");
        $arr['aNum'] = $advance['advance'];
        $couponNum = $this->db->selectrow("SELECT count(*) as couponNum FROM sdb_member_coupon WHERE member_id={$nMemberId}");
        $arr['couponNum'] = $couponNum['couponNum'];
        $tmp = $this->db->select_b("SELECT * FROM sdb_member_coupon as mc left join sdb_coupons as c on c.cpns_id=mc.cpns_id        left join sdb_promotion as p on c.pmt_id=p.pmt_id WHERE member_id={$nMemberId} ORDER BY mc.memc_gen_time DESC");
        $now = time();
        $cNum = 0;
        foreach($tmp as $a)
        {
            if(($a['pmt_time_end'] - $now) <= 15*3600*24) {
                $cNum++;
            }
        }
        $arr['cNum'] = $cNum;
        $commentRNum = $this->db->selectrow("SELECT count(*) as commentRNum FROM `sdb_comments` where `author_id`={$nMemberId} and `display`='true' and lastreply>0");
        $arr['commentRNum'] = $commentRNum['commentRNum'];
        $pa = $this->db->select_b("SELECT pmta_name FROM `sdb_promotion_activity` WHERE `pmta_enabled`='true' and `pmta_time_end`>={$now} and `pmta_time_begin`<={$now}");
        $arr['pa'] = $pa;
        return $arr;
    }

    function getMemberByUser($username){
        return $this->db->selectrow("SELECT * FROM sdb_members WHERE uname = ".$this->db->quote($username));
    }
    /**
     * getFieldById
     *
     * @param string $aField
     * @param int $id
     * @access public
     * @return void
     */
    function getTodayResgisterMember(){
        $now=strtotime(date("Y-m-d"));
        $sqlString = 'SELECT count(member_id) as countmember FROM sdb_members WHERE regtime>='.$now. ' and regtime<'.($now+86400).' and disabled="false"';

        $result=$this->db->selectrow($sqlString);
        return $result['countmember'];
    }
    function getBirthdayMember(){
        $month=date("m");
        $day=date("d");
        $sqlString = 'SELECT count(member_id) as countmember FROM sdb_members WHERE  b_month='.$month.' and b_day='.$day.' and disabled="false"';
        $result=$this->db->selectrow($sqlString);
        return $result['countmember'];
    }
    function getFieldById($id, $aField=array('*')) {
        $sqlString = "SELECT ".implode(',', $aField)." FROM sdb_members WHERE member_id=".intval($id);
        return $this->db->selectrow($sqlString);
    }
    //保存会员信息
    function save($nMId,$aData){
        foreach($aData as $key =>$value){
            if( $key == 'addon' )
                continue;
            $aData[$key] = htmlspecialchars($value);
        }
        
        $aRs = $this->db->query("SELECT * FROM sdb_members WHERE member_id=".intval($nMId));
        $sSql = $this->db->getUpdateSql($aRs,$aData);
        return (!$sSql || $this->db->exec($sSql));
    }
    //后台由管理员添加会员
    function addMemberByAdmin($aData){
        if(empty($aData['uname'])){
            trigger_error(__('保存失败：未输入会员名称'), E_USER_ERROR);
            return false;
        }
        $aInfo = $this->db->selectrow("SELECT uname,email FROM sdb_members WHERE uname = ".$this->db->quote($aData['uname'])." OR email = ".intval($aData['email']));
        //----------获得插件
        $pObj=$this->system->loadModel("member/passport");
        if($pObj->_verify()){
            $obj=&$pObj->_load();
        }    
        //--------
        if($aInfo['uname'] == $aData['uname']){
            trigger_error(__('保存失败：存在相同会员名称'), E_USER_ERROR);
            return false;
        }
        if(empty($aData['password'])){
            trigger_error(__('保存失败：密码输入不正确'), E_USER_ERROR);
            return false;
        }elseif (strlen($aData['password'])<4){
            trigger_error(__('保存失败：密码不能小于4位'),E_USER_ERROR);
            return false;
        }
        if(empty($aData['psw_confirm'])){
            trigger_error(__('保存失败：确认密码不能为空'),E_USER_ERROR);
            return false;
        }elseif (strlen($aData['psw_confirm'])<4){
            trigger_error(__('保存失败：确认密码不能小于4位'),E_USER_ERROR);
            return false;
        }
        if($aData['psw_confirm'] != $aData['password']){
            trigger_error(__('保存失败：两次密码输入不一致'), E_USER_ERROR);
            return false;
        }
        if(empty($aData['email'])){
            trigger_error(__('保存失败：Email输入不正确'), E_USER_ERROR);
            return false;
        }
        if ($this->checkusertouc($aData['uname'],$aData['password'],$aData['email'],$uid,$message)){
            if (!empty($message))
                trigger_error($message,E_USER_ERROR);
            else
                $aData['member_id'] = $uid;
        }
        //----------
/*        if($aInfo['email'] == $aData['email']){
            trigger_error(__('保存失败：存在相同Email会员'), E_USER_ERROR);
            return false;
        }*/
        $aData['regtime'] = time();
        $aData['password'] = md5($aData['password']);       
        $aData['reg_ip'] = remote_addr();
        $aRs = $this->db->query("SELECT * FROM sdb_members WHERE 0");
        $sSql = $this->db->getInsertSql($aRs,$aData);
  
  //$aData['member_id'] = $insertID;
  //$MemAttr = $this->db->query("SELECT * FROM sdb_member_attr WHERE 0");
  //$sSql = $this->db->getInsertSql($aRs,$aData);
  
  
        if($this->db->exec($sSql)){              
            $insertID  = $this->db->lastInsertId();
            
            $status = $this->system->loadModel('system/status');
            $status->add('MEMBER_REG');
            
            return $insertID;
        }else{
            return '';
        }
    }
 
 
    function saveMemAttr($data){
        $selsql = "select attr_type from sdb_member_attr where attr_id = ".$data['attr_id']."";
        $tmpdate = $this->db->select($selsql);
            if($tmpdate[0]['attr_type']=='cal'){
              $data['value'] = strtotime($data['value']);
            }
            if(($tmpdate[0]['attr_type']=='checkbox'&&$data['value']!='%no%')||$tmpdate[0]['attr_type']!='checkbox'){
                $aRs = $this->db->query("SELECT * FROM sdb_member_mattrvalue WHERE 0");
                $sSql = $this->db->getInsertSql($aRs,$data);
                if($this->db->exec($sSql)){    
                    return true;
                }else{
                    return false;
                } 
            }

    }

    //取得会员列表
    function getMemList($aKeyword=array()){
        $sSql = '';
        if(isset($aKeyword['issearch']) && $aKeyword['issearch']==1){
            $sSql = '';
            $sSql .= $aKeyword['uname']?"m.uname LIKE '%".$aKeyword['uname']."%' AND ":'';
            $sSql .= ($aKeyword['level'] != 0)?"l.member_lv_id='".$aKeyword['level']."' AND ":'';
            $aKeyword['sex'] = $aKeyword['sex']-1;
            $sSql .= ($aKeyword['sex'] != -1)?"m.sex='".$aKeyword['sex']."' AND ":'';
            $sSql .= $aKeyword['tel']?"m.tel='".$aKeyword['tel']."' AND ":'';
            $sSql .= $aKeyword['city']?"m.uname='".$aKeyword['city']."' AND ":'';
            $sSql .= $aKeyword['email']?"m.email LIKE '%".$aKeyword['email']."%' AND ":'';
            $sSql .= $aKeyword['addr']?"m.addr LIKE '%".$aKeyword['addr']."%' AND ":'';

            $aKeyword['minregtime'] = strtotime($aKeyword['minregtime']);
            $aKeyword['maxregtime'] = strtotime($aKeyword['maxregtime']);
            $sSql .= $aKeyword['minregtime']>0?"m.regtime>='".$aKeyword['minregtime']."' AND ":'';
            $sSql .= $aKeyword['maxregtime']>0?"m.regtime<='".$aKeyword['maxregtime']."' AND ":'';

            $aKeyword['minscore'] = $aKeyword['minscore']+0;
            $aKeyword['maxscore'] = $aKeyword['maxscore']+0;
            $sSql .= $aKeyword['minscore']>0?"m.score>='".$aKeyword['minscore']."' AND ":'';
            $sSql .= $aKeyword['maxscore']>0?"m.score<='".$aKeyword['maxscore']."' AND ":'';

            $aKeyword['minscore'] = $aKeyword['minscore']+0;
            $aKeyword['maxscore'] = $aKeyword['maxscore']+0;
            $sSql .= $aKeyword['minscore']>0?"m.score>='".$aKeyword['minscore']."' AND ":'';
            $sSql .= $aKeyword['maxscore']>0?"m.score<='".$aKeyword['maxscore']."' AND ":'';

            $aKeyword['minadvance'] = $aKeyword['minadvance']+0;
            $aKeyword['maxadvance'] = $aKeyword['maxadvance']+0;
            $sSql .= $aKeyword['minadvance']>0?"m.advance>='".$aKeyword['minadvance']."' AND ":'';
            $sSql .= $aKeyword['maxadvance']>0?"m.advance<='".$aKeyword['maxadvance']."' AND ":'';

            $sSql .= $aKeyword['shoped']?"o.order_id!='' AND ":'';
        }
        if(!$sSql){
            $aData=$this->db->select_b("SELECT m.member_id,m.uname,m.name,m.sex,m.city,m.email,m.score,m.regtime,m.reg_ip,l.name as lv_name
                                            FROM sdb_members m
                                            LEFT JOIN sdb_member_lv l
                                            ON m.member_lv_id=l.member_lv_id",PAGELIMIT);
        }else{
            $sSql = substr($sSql,0,-4);
            $aData=$this->db->select_b("SELECT m.member_id,m.uname,m.name,m.sex,m.city,m.email,m.score,m.regtime,m.reg_ip,l.name as lv_name,o.order_id
                                            FROM sdb_members m
                                            LEFT JOIN sdb_member_lv l
                                            ON m.member_lv_id=l.member_lv_id
                                            LEFT JOIN sdb_orders o
                                            ON m.member_id=o.member_id WHERE ".$sSql,PAGELIMIT);
        }
        return $aData;
    }
    function getInfoById($nMId,$sField=NULL){
        $aFiltrate = array(
                'basic'=>array(
                        'member_id'=>1,'member_lv_id'=>1,'uname'=>1,'password'=>1,'firstname'=>1,'lastname'=>1,'name'=>1,'sex'=>1,
                        'b_year'=>1,'b_month'=>1,'b_day'=>1,'area'=>1,'addr'=>1,'zip'=>1,'email'=>1,'tel'=>1,'mobile'=>1,
                        'regtime'=>1,'reg_ip'=>1,'score_rate','score'=>1,'score_history'=>1,'score_freeze'=>1,'advance'=>1,'biz_money'=>1,
                        'pay_time'=>1,'state'=>1,'pw_question'=>1,'pw_answer'=>1,'fav_tags'=>1
                ),
                'ext'=>array('vocation'=>1,'education'=>1,'wedlock'=>1,'interest'=>1)
        );
        if($aFiltrate['basic'][$sField]){
            return $this->getBasicInfoById($nMId);
        }else if($aFiltrate['ext'][$sField]){
            return $this->getExtInfoById($nMId);
        }else{
            return false;
        }
    }
    function getBasicInfoById($nMId){
        return $this->db->selectRow("SELECT m.*,l.name AS lv_name
                                     FROM sdb_members m
                                     LEFT JOIN sdb_member_lv l
                                     ON m.member_lv_id=l.member_lv_id
                                     WHERE m.member_id=".intval($nMId));
    }
    function getExtInfoById($nMId){
        $aTemp = $this->db->selectRow("SELECT vocation,education,wedlock,interest,custom FROM sdb_members WHERE member_id=".intval($nMId));
        if($aTemp){
            $aCustom = unserialize($aTemp['custom']);
            unset($aTemp['custom']);
            $aExt = is_array($aCustom)?array_merge($aTemp,$aCustom):$aTemp;
            return $aExt;
        }
        return $aTemp;
    }
    function delMember($aMid){
        $sSql = 'DELETE FROM sdb_members WHERE';
        if(count($aMid)>0){
            $sSql .= ' member_id IN ('.implode(',',$aMid).')';
            return $this->db->exec($sSql);
        }
        return false;
    }
    //return members'email
    function toSearchMemEmail($aKeyword,&$msg){
        $sSql = '';
        $sSql .= $aKeyword['uname']?"m.uname LIKE '%".$aKeyword['uname']."%' AND ":'';
        $sSql .= ($aKeyword['level'] != 0)?"l.member_lv_id='".$aKeyword['level']."' AND ":'';
        $aKeyword['sex'] = $aKeyword['sex']-1;
        $sSql .= ($aKeyword['sex'] != -1)?"m.sex='".$aKeyword['sex']."' AND ":'';
        $sSql .= $aKeyword['tel']?"m.tel='".$aKeyword['tel']."' AND ":'';
        $sSql .= $aKeyword['city']?"m.uname='".$aKeyword['city']."' AND ":'';
        $sSql .= $aKeyword['email']?"m.email LIKE '%".$aKeyword['email']."%' AND ":'';
        $sSql .= $aKeyword['addr']?"m.addr LIKE '%".$aKeyword['addr']."%' AND ":'';

        $aKeyword['minregtime'] = strtotime($aKeyword['minregtime']);
        $aKeyword['maxregtime'] = strtotime($aKeyword['maxregtime']);
        $sSql .= $aKeyword['minregtime']>0?"m.regtime>='".$aKeyword['minregtime']."' AND ":'';
        $sSql .= $aKeyword['maxregtime']>0?"m.regtime<='".$aKeyword['maxregtime']."' AND ":'';

        $aKeyword['minscore'] = $aKeyword['minscore']+0;
        $aKeyword['maxscore'] = $aKeyword['maxscore']+0;
        $sSql .= $aKeyword['minscore']>0?"m.score>='".$aKeyword['minscore']."' AND ":'';
        $sSql .= $aKeyword['maxscore']>0?"m.score<='".$aKeyword['maxscore']."' AND ":'';

        $aKeyword['minscore'] = $aKeyword['minscore']+0;
        $aKeyword['maxscore'] = $aKeyword['maxscore']+0;
        $sSql .= $aKeyword['minscore']>0?"m.score>='".$aKeyword['minscore']."' AND ":'';
        $sSql .= $aKeyword['maxscore']>0?"m.score<='".$aKeyword['maxscore']."' AND ":'';

        $aKeyword['minadvance'] = $aKeyword['minadvance']+0;
        $aKeyword['maxadvance'] = $aKeyword['maxadvance']+0;
        $sSql .= $aKeyword['minadvance']>0?"m.advance>='".$aKeyword['minadvance']."' AND ":'';
        $sSql .= $aKeyword['maxadvance']>0?"m.advance<='".$aKeyword['maxadvance']."' AND ":'';

        $sSql .= $aKeyword['shoped']?"o.order_id!='' AND ":'';

        if(!$sSql){
            $aData = false;
            $msg = __('请填写搜索条件！');
        }else{
            $sSql = substr($sSql,0,-5);
            $aData=$this->db->select_b("SELECT m.uname, m.email
                                            FROM sdb_members m
                                            LEFT JOIN sdb_member_lv l
                                            ON m.member_lv_id=l.member_lv_id
                                            LEFT JOIN sdb_orders o
                                            ON m.member_id=o.member_id WHERE ".$sSql);
            $msg = '';
        }
        return $aData;
    }
    /*********************** 会员信息-END***********************/
    /*********************** 收获地址-BEGIN ***********************/
    //获取会员的收货地址列表
    function getMemberAddr($nMemberId){
        return $this->db->select("SELECT * FROM sdb_member_addrs WHERE member_id=".intval($nMemberId)." ORDER BY def_addr DESC");
    }
    //设为默认收获地址
    function setToDef($addrId,$member_id,&$message,$disabled){
        if($addrId){
            $aTemp = array('def_addr'=>1);
            $oldDefId = $this->db->selectrow("SELECT addr_id FROM sdb_member_addrs WHERE def_addr=1 AND addr_id!='".$addrId."' AND member_id=".intval($member_id));
            if(is_array($oldDefId)&&$disabled=='2'){
                $message = __('已存在默认收货地址，不能重复设置');
                return false;
            }
            $aRs = $this->db->query("SELECT def_addr FROM sdb_member_addrs WHERE addr_id=".intval($addrId));
            $sSql = $this->db->getUpdateSql($aRs,$aTemp);
            if(!$sSql || $this->db->exec($sSql)){
                if($disabled){
                    unset($sSql);
                    $aTemp = array('def_addr'=>$disabled==1?0:1);
                    $aRs = $this->db->query("SELECT def_addr FROM sdb_member_addrs WHERE addr_id=".$addrId);
                    $sSql = $this->db->getUpdateSql($aRs,$aTemp);
                    if($sSql) $this->db->exec($sSql);
                }
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
    //依据地址的id获取会员的收货地址
    function getAddrById($nId){
        if($aRet = $this->db->selectrow("SELECT * FROM sdb_member_addrs WHERE addr_id=".intval($nId)))
            return $aRet;
        else
            return false;
    }

    //获取会员的默认收货地址如果没有则取会员信息
    function getDefaultAddr($mid){
        if($mid){
            $aAddr = $this->db->selectrow("SELECT * FROM sdb_member_addrs WHERE member_id=".intval($mid)." AND def_addr = 1");
            if(!$aAddr['addr_id']){
                $aAddr = $this->db->selectrow("SELECT member_id,name,area,zip,tel,mobile,addr FROM sdb_members WHERE member_id=".intval($mid));
            }
        }
        return $aAddr;
    }

    //插入收货人地址
    function insertRec($aData,$nMId,&$message){
        foreach ($aData as $key=>$val){
            $aData[$key] = trim($val);
            if(empty($aData[$key])){
                switch ($key){
                    case 'name':
                        $message = __('姓名不能为空！');
                        return false;
                    break;
                    case 'email':
                        $message = __('E-mail不能为空！');
                        return false;
                    break;
                    case 'addr':
                        $message = __('地址不能为空！');
                        return false;
                    break;
                    case 'zip':
                        $message = __('邮编不能为空！');
                        return false;
                    break;
                    default:
                    break;
                }
            }
        }
        if($aData['tel'] == '' && $aData['mobile'] == ''){
            $message = __('联系电话和手机不能都为空！');
            return false;
        }
        $aData['member_id'] = $nMId;
        $aRs = $this->db->query("SELECT * FROM sdb_member_addrs WHERE 0");
        $sSql = $this->db->getInsertSql($aRs,$aData);
        if(!$sSql || $this->db->query($sSql)){
            $message = __('保存成功！');
            return true;
        }else{
            $message = __('保存失败！');
            return false;
        }
    }

    //保存修改
    function saveRec($aData,$member_id,&$message){
        if($aData['def_addr'] && !$this->setToDef($aData['addr_id'],$member_id,$message,2)){
            return false;
        }

        $rs = $this->db->query('SELECT * FROM sdb_member_addrs WHERE addr_id='.intval($aData['addr_id']));
        $sql = $this->db->GetUpdateSQL($rs, $aData);
        if(!$sql || $this->db->exec($sql)){
            return true;
        }else{
            return false;
        }
    }
    //删除
    function delRec($addrId,&$message){
        if($addrId){
            return $this->db->query("DELETE FROM sdb_member_addrs WHERE addr_id=".intval($addrId));
        }else
            $meesage = __("参数有误");
        return false;
    }
    /************************ 收获地址-END ************************/
    /************************ 会员收藏-BEGIN ************************/
    //新增会员收藏
    function addFav($nMid,$nGid){
        $aRs = $this->db->selectrow("SELECT addon FROM sdb_members WHERE member_id=".intval($nMid));
        if(isset($aRs)){
            $aRs = unserialize($aRs['addon']);
            $aRs['fav'][] =$nGid;
            $aRs['fav'] = array_unique($aRs['fav']);
            return $this->save($nMid, array('addon'=>serialize($aRs)));
        }else{
            return false;
        }
    }

    //获得会员的收藏列表
    function getFavorite($nMemberId,$nPage){
        $oGood = $this->system->loadModel('trading/goods');
        return $oGood->getFavorite($nMemberId,$nPage);
    }
    function delFav($nMid,$nGid){
        $aRs = $this->db->selectrow("SELECT addon FROM sdb_members WHERE member_id=".intval($nMid));
        if($aRs && $aRs['addon'] != ''){
            $aRs = unserialize($aRs['addon']);
            $key = isset($aRs['fav'])?array_search($nGid,$aRs['fav']):false;
            if(is_int($key)) unset($aRs['fav'][$key]);
            return $this->save($nMid, array('addon'=>serialize($aRs)));
        }else{
            return false;
        }
    }

    function delAllFav($nMid){
        return $this->db->query("DELETE addon FROM sdb_members WHERE member_id=".intval($nMid));
    }

    function saveCart($nMid, $sCart){
        $aRs = $this->db->selectrow("SELECT addon FROM sdb_members WHERE member_id=".intval($nMid));
        if(isset($aRs)){
            $aRs = unserialize($aRs['addon']);
            $aRs['cart'] =$sCart;
            return $this->save($nMid, array('addon'=>serialize($aRs)));
        }else{
            return false;
        }
    }

    function getCart($nMid){
        $aRs = $this->db->selectrow("SELECT addon FROM sdb_members WHERE member_id=".intval($nMid));
        if(isset($aRs)){
            $aRs = unserialize($aRs['addon']);
            return $aRs['cart'];
        }else{
            return false;
        }
    }
    /************************ 会员收藏-END ************************/
    /************************ 到货通知-BEGIN ************************/
    //获取到货通知列表
    function getNotify($nMemberId){
        $oGood = $this->system->loadModel('trading/goods');
        $aRet = $oGood->getNotify($nMemberId);
        foreach($aRet['data'] as $k => $rows){
            $rows['pdt_desc'] = unserialize($rows['pdt_desc']);
            if($rows['pdt_desc']){
                if($rows['pdt_desc'][$rows['product_id']]){
                    $rows['pdt_desc'] = $rows['pdt_desc'][$rows['product_id']];
                    $oPdt = $this->system->loadModel('goods/products');
                    $aPdt = $oPdt->getFieldById($rows['product_id'], array('store'));
                    $rows['store'] = $aPdt['store'];
                }else{
                    $rows['pdt_desc'] = '该物品已经下架或者已被删除！';
                    $rows['store'] = -1;
                }
            }else{
                $rows['pdt_desc'] = '';
            }
            $aRet['data'][$k] = $rows;
        }
        return $aRet;
    }
    function insertNotify($aData){
        $aRs = $this->db->query("SELECT * FROM sdb_goods_notify WHERE 0");
        $sSql = $this->db->getInsertSql($aRs,$aData);
        return ($sSql || $this->db->exec($sSql));
    }
    function delNotify($nMid,$id){
        $notify = $this->system->loadModel('goods/goodsNotify');
        $aData = $notify->getFieldById($id);

        $sSql = 'DELETE FROM sdb_gnotify WHERE gnotify_id ='.intval($id).' AND member_id ='.intval($nMid);
        $this->db->exec($sSql);

        $notify->updateGoodsNum($aData['goods_id']);
        return true;
    }
    /************************ 到货通知-END ************************/

    /************************ 商品评论(尚未做) || 商店留言列表-BEGIN 前台************************/
    //在mdl.message.php中
    /************************ 商品评论 || 商店留言列表-END ************************/
    /************************ 会员等级-BEGIN ************************/
    function getLevelList($limit=true) {
        if ($limit) {
            return $this->db->select_b("SELECT * FROM sdb_member_lv WHERE disabled = 'false' ",PAGELIMIT);
        }else {
            return $this->db->select_b("SELECT * FROM sdb_member_lv WHERE disabled = 'false' ");
        }
    }

    function saveLevel($aData,$nLvId){
        if($aData['lv_type'] == 'wholesale'){
            $aData['point'] = 0;
        }
        $aRs = $this->db->query("SELECT * FROM sdb_member_lv WHERE member_lv_id=".intval($nLvId));
        $sSql = $this->db->getUpdateSql($aRs,$aData);
        return (!$sSql || $this->db->query($sSql));
    }

    function insertLevel($aData,&$message){
        if($this->checkField('name','sdb_member_lv','WHERE name=\''.$aData['name'].'\'')){
            $message = __('有同名会员等级存在！');
            return false;
        }else if($aData['default_lv'] == ''){
            if($this->checkField('member_lv_id','sdb_member_lv','WHERE pre_id=0')){
                $message = __('默认等级已经存在！');
                return false;
            }
        }
        if($aData['lv_type'] == 'wholesale'){
            $aData['point'] = 0;
        }
        $aRs = $this->db->query("SELECT * FROM sdb_member_lv WHERE member_lv_id=0");
        $sSql = $this->db->getInsertSql($aRs,$aData);
        return (!$sSql || $this->db->query($sSql));
    }

    function checkField($sField,$sTable,$sWhere=''){
        return $this->db->selectRow("SELECT $sField FROM ".$sTable.' '.$sWhere);

    }
    function checkusertouc($uname,$password,$email,&$uid,&$message){
         //---------判断是否是UCenter插件
         $pObj=$this->system->loadModel('member/passport');
        if ($obj=$pObj->function_judge('checkuser')){
            //-----到Ucenter数据库中检查是否存在该用户名
            $isuser=$obj->checkuser($aData['uname']);
            if ($isuser=='-3'){
                $message = '您开启了UCenter整合，且UCenter中存在该用户名';
            }
            else{
                $uid = $obj->regist_user($uname,$password,$email);
                switch ($uid){
                    case -1:
                        $message = '无效的用户名';
                        break;
                    case -2:
                        $message = '用户名不允许注册';
                        break;
                    case -3:
                        $message = '已经存在一个相同的用户名';
                        break;
                    case -4:
                        $message = '无效的email地址';
                        break;
                    case -5:
                        $message = '邮件不允许';
                        break;
                    case -6:
                        $message = '该邮件地址已经存在';
                        break;
                    default:
                        break;
                }
            }
            return true;
            //-----
        }
        else
            return false;
    }
    function getLevelByPoint($nPoint) {
        $sSql = 'SELECT member_lv_id, name FROM sdb_member_lv WHERE point <= '.$nPoint.' AND lv_type=\'retail\' AND disabled="false" ORDER BY point DESC';
        return $this->db->selectrow($sSql);
    }
    /************************ 会员等级-END ************************/
    
    function isAllowAddr($memid){
        $sql="SELECT count(*) as num FROM sdb_member_addrs WHERE member_id = ".intval($memid);
        $aTmp = $this->db->selectrow($sql);
        if($aTmp['num'] < 5){
            return true;
        }else{
            return false;
        }
    }
    
    function getUserForBBS(){
        $sql="select member_id,uname,email,password from sdb_members";
        $data= $this->db->select($sql);
        return $data;
    }
    
    function getRemark($memid){
        $sql="select remark,remark_type from sdb_members where member_id = ".intval($memid);
        $aData = $this->db->selectrow($sql);
        return $aData;
    }
    
    function addRemark($memid,$in){
        $sql="select remark,remark_type from sdb_members where member_id = ".intval($memid);
        $rs=$this->db->query($sql);
        $sql=$this->db->getUpdateSQL($rs,$in);
        return(!$sql || $this->db->exec($sql));
    }
    
    function updateMemAttr($member_id,$attr_id,$data){

        $selsql = "select attr_type from sdb_member_attr where attr_id = ".$attr_id."";
        $tmpdate = $this->db->select($selsql);
            if($tmpdate[0]['attr_type']==='cal'){
                $data['value'] = strtotime($data['value']);
            }
        $sql="select * from sdb_member_mattrvalue where member_id = ".intval($member_id)." and attr_id = ".intval($attr_id);
        $rs=$this->db->select($sql);
        $searchresult =  $this->db->select('SELECT attr_id FROM sdb_member_attr');
        $tmpdate = array();
        foreach($searchresult as $key => $value){
            if($value['attr_id']>0){
                $tmpdate[] = $value['attr_id'];
            }
        }
        $addsql = "";
        if(count($tmpdate)!=0){
        $addsql = "AND attr_id NOT IN (".implode(',',$tmpdate).")";
        }
        $sqlattr="SELECT * FROM sdb_member_mattrvalue where member_id = ".intval($member_id)." ".$addsql;
        $tmpdate = $this->db->select($sqlattr);
        if(count($tmpdate)!=0){
        for($i=0;$i<count($tmpdate);$i++){
            $deletesql = "delete from sdb_member_mattrvalue where member_id = ".intval($member_id)." and attr_id = ".$tmpdate[$i]['attr_id'];
            $this->db->exec($deletesql);
        }
        }
        if(count($rs)==0){    
            $this->saveMemAttr($data);
        }else{    
            $rs1=$this->db->query($sql);
            $updatesql=$this->db->getUpdateSQL($rs1,$data);
            return(!$updatesql || $this->db->exec($updatesql));
        }
        
    }           
    
    function getContactObject($member_id){
        if($member_id>0){
        $sql="SELECT * FROM sdb_member_mattrvalue AS ma, sdb_member_attr AS at WHERE ma.attr_id = at.attr_id and ma.member_id = '".        $member_id."' and at.attr_group = 'contact' AND attr_show = 'true' order by at.attr_order asc";
        return $this->db->select($sql);
        }
    }
    
    function getMemIdByName($name){
        $sql="SELECT member_id FROM sdb_members where uname = '".$name."'";
        return $this->db->select($sql);
    }
        
    function getMemberAttrvalue($member_id){
        return $this->db->select("SELECT * FROM sdb_member_mattrvalue where member_id = '".$member_id."'");
    }
     
    function getMemberByid($member_id){
        return $this->db->select("SELECT * FROM sdb_members where member_id = '".$member_id."'");
    }
 
    function getattrvalue($member_id,$attr_id){
        return $this->db->select("SELECT * FROM sdb_member_mattrvalue where member_id = '".$member_id."' and attr_id = '".$attr_id."' order by id");
    }
     
    function getallattrvalue($member_id,$attr_id,$value){
        $sql = "SELECT * FROM sdb_member_mattrvalue where member_id = '".$member_id."' and attr_id = '".$attr_id."' and value='".        $value."'";
        return $this->db->select($sql);
    }
     
    function deleteMattrvalues($attr_id,$member_id){
        return $this->db->exec("DELETE FROM sdb_member_mattrvalue where member_id = '".$member_id."' and attr_id = '".$attr_id."'");
    }
       
       function deletememberidattrid($attr_id,$value){
           $sql  = "DELETE FROM sdb_member_mattrvalue where value = '".$value."' and attr_id = '".$attr_id."'";
        return $this->db->exec($sql);
    }
    
    function deleteAllMattrvalues($attr_id,$member_id,$value){
        return $this->db->exec("DELETE FROM sdb_member_mattrvalue where member_id = '".$member_id."' and attr_id = '".$attr_id."'        and value ='".$value."'");
    }       
    
    function delete($filter){
        if(method_exists($this,'pre_delete')){
            $this->pre_delete($filter);
        }
        if(method_exists($this,'post_delete')){
            $this->post_delete($filter);
        }
        
        $this->disabledMark = 'recycle';
        
        $deleteattr = $filter['member_id'];
        for($i=0;$i<count($deleteattr);$i++){
        $sSql = "delete from sdb_member_mattrvalue where member_id = '".$deleteattr[$i]."'";
        $this->db->exec($sSql);
        }    

        $sql = 'delete from '.$this->tableName.' where '.$this->_filter($filter);
        if($this->db->exec($sql)){
            if($this->db->affect_row()){
               return $this->db->affect_row();
            }else{
               return true;
            }
        }else{
             return false;
        }
    }


    function batchEditCols($filter){
        $ret = $this->getColumns($filter);
        foreach($ret as $k=>$col){
           if($ret[$k]['custom']=='yes'){
               unset($ret[$k]);
           }
        }
        if(is_null($this->colsGridEdit)||is_null($this->colsColumnEdit)||is_null($this->colsGridShow)){
            $this->setFinderCols(null,$filter);
        }

        foreach($ret as $k=>$col){
            if(in_array($k,$this->colsColumnEdit)){
                $c[] = "count(DISTINCT $k) as $k";
            }else{
                unset($ret[$k]);
            }
        }

        $r = $this->db->selectrow('select count('.$this->idColumn.') as count from '.$this->tableName.' where '.$this->_filter($filter));
        $rowCount = $r['count'];

        //如果所编辑的条目小于1000，则将获得相同值得列。
        if($rowCount<1000){ 
            $sql = 'select '.implode(',',$c).' from '.$this->tableName.' where '.$this->_filter($filter);
            $c = array();
            
            if($r = $this->db->selectrow($sql)){
                foreach($r as $col=>$count){
                    if($count<2){
                        $c[] = $col;
                    }    
                }
                
                
                foreach($this->db->selectrow('select '.implode(',',$c).' from '.$this->tableName.' where '.$this->_filter($filter)) as $k=>$v){
                    if(substr($ret[$k]['type'],0,5)=='time:'||$ret[$k]['type']=='time'){
                        $options = explode(':',$ret[$k]['type']);
                        array_shift($options);
                        $rows = array($v);
                        $this->modifier_time($rows,$options);
                        $v = $rows[0];
                    }
                    $ret[$k]['value'] = $v;
                }
            }
        }
              
        return array('cols'=>$ret,'count'=>$rowCount);
    }
 
    function checkMemberHasAdvance($filter, $disabled = ''){
        if($disabled)
            $this->disabledMark = $disabled;
        $sql = 'SELECT COUNT(member_id) AS c FROM sdb_members WHERE advance <> 0 AND '.$this->_filter($filter);
        $rs = $this->db->selectrow($sql);
        return $rs['c'];
    }
 
 
 
 
}
?>
