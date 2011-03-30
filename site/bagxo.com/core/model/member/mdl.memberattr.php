<?php
include_once('shopObject.php');
class mdl_memberattr extends shopObject {

    var $defaultCols ='attr_name,attr_tyname,attr_required,attr_search,attr_option,attr_show,attr_picture,attr_url,attr_order';
    var $idColumn = 'attr_id'; //表示id的列
    var $adminCtl = 'member/memberattr';
    var $textColumn = 'attr_name';
    var $defaultOrder = array('attr_order','desc');
    var $tableName = 'sdb_member_attr';
    var $hasTag = true;
    
    function mdl_memberattr(){
        parent::shopObject();    
        $data = $this->db->select("select attr_id from sdb_member_attr where attr_group = 'defalut' ");
        if(count($data)==0){
            $this->import_defalutattr();
        }
        
    }

    function import_defalutattr(){
            $attrarray = array(
                        array('attr_name'=>'地区','attr_type'=>'area','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'true','attr_valtype'=>'','attr_tyname'=>'系统默认','attr_order'=>'4','attr_group'=>'defalut'),
                        array('attr_name'=>'联系地址','attr_type'=>'addr','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'true','attr_valtype'=>'','attr_tyname'=>'系统默认','attr_order'=>'5','attr_group'=>'defalut'),
                        array('attr_name'=>'姓名','attr_type'=>'name','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'true','attr_valtype'=>'','attr_tyname'=>'系统默认','attr_order'=>'1','attr_group'=>'defalut'),
                        array('attr_name'=>'移动电话','attr_type'=>'mobile','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'true','attr_valtype'=>'number','attr_tyname'=>'系统默认','attr_order'=>'7','attr_group'=>'defalut'),
                        array('attr_name'=>'固定电话','attr_type'=>'tel','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'true','attr_valtype'=>'number','attr_tyname'=>'系统默认','attr_order'=>'8','attr_group'=>'defalut'),
                        array('attr_name'=>'邮编','attr_type'=>'zip','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'true','attr_valtype'=>'number','attr_tyname'=>'系统默认','attr_order'=>'6','attr_group'=>'defalut'),    
                        array('attr_name'=>'性别','attr_type'=>'sex','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'true','attr_valtype'=>'','attr_tyname'=>'系统默认','attr_order'=>'2','attr_group'=>'defalut'),
                        array('attr_name'=>'出生日期','attr_type'=>'date','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'true','attr_valtype'=>'','attr_tyname'=>'系统默认','attr_order'=>'3','attr_group'=>'defalut'),
                        array('attr_name'=>'安全问题','attr_type'=>'pw_question','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'true','attr_valtype'=>'','attr_tyname'=>'系统默认','attr_order'=>'9','attr_group'=>'defalut'),
                        array('attr_name'=>'回答','attr_type'=>'pw_answer','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'true','attr_valtype'=>'','attr_tyname'=>'系统默认','attr_order'=>'10','attr_group'=>'defalut'),
                        array('attr_name'=>'QQ','attr_type'=>'text','attr_required'=>'false','attr_search'=>'true','attr_option'=>'','attr_show'=>'false','attr_valtype'=>'','attr_tyname'=>'QQ','attr_order'=>'11','attr_group'=>'contact'),
                        array('attr_name'=>'MSN','attr_type'=>'text','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'false','attr_valtype'=>'email','attr_tyname'=>'MSN','attr_order'=>'12','attr_group'=>'contact'),
                        array('attr_name'=>'Skype','attr_type'=>'text','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'false','attr_valtype'=>'alphaint','attr_tyname'=>'Skype','attr_order'=>'13','attr_group'=>'contact'),
                        array('attr_name'=>'旺旺','attr_type'=>'text','attr_required'=>'false','attr_search'=>'false','attr_option'=>'','attr_show'=>'false','attr_valtype'=>'','attr_tyname'=>'旺旺','attr_order'=>'14','attr_group'=>'contact'),
                        );
             foreach($attrarray as $k => $v){
                $this->save($v);
             }
    }
    function getColumns(){
        return array(
            'attr_id'=>array('label'=>'选项ID','class'=>'span-3','readonly'=>true),    /* 选项ID */
            'attr_name'=>array('label'=>'选项名称','class'=>'span-3'),
            'attr_tyname'=>array('label'=>'选项类型','class'=>'span-3'),
            'attr_order'=>array('label'=>'排序','class'=>'span-3'),
            'attr_show'=>array('label'=>'显示','class'=>'span-3'),
            'attr_group'=>array('label'=>'选项类别','class'=>'span-3'),
            'attr_search'=>array('label'=>'搜索','class'=>'span-3'),
        );                                                    
    }                                                                
 
 
 
    function setVisibility($attr_id,$status){
        $sql = "update sdb_member_attr set attr_show = '".($status?'true':'false')."' where attr_id = '".$attr_id."'";
        return $this->db->exec($sql);
    }
 
    
    function save($data){
        $aRs = $this->db->query('SELECT * FROM sdb_member_attr WHERE 1=1');
        $sql = $this->db->getInsertSql($aRs,$data);
        $result = $this->db->exec($sql);
        if($result){
            return $this->db->lastInsertId();
        }else{
        return '';
        }
    }
    
    function Remove($attr_id){
        return $this->db->exec("delete from sdb_member_attr where attr_id = '".$attr_id."'");
    }
    
        
    function getFieldById($attr_id){
        return $this->db->selectRow("SELECT * FROM sdb_member_attr WHERE attr_id=".intval($attr_id));
    }
    
    
    function updatememattr($data,$attr_id){
        $member = $this->system->loadModel('member/member');
        if($data['attr_type']=='select'){
            $memberdate = $member->getList('member_id','',0,-1);
            for($i=0;$i<count($memberdate);$i++){
               $valuedate = $member->getattrvalue($memberdate[$i]['member_id'],$attr_id);
                   if(count($valuedate)>1){
                       for($k=1;$k<count($valuedate);$k++){                   
                           $member->deleteAllMattrvalues($attr_id,$memberdate[$i]['member_id'],$valuedate[$k]['value']); 
                       }                  
                   } 
            }    
        }
        if($data['attr_type']=='select'||$data['attr_type']=='checkbox'){
          $olddate = unserialize($data['attr_option']);
          $tmpdate = $this->getFieldById($attr_id);
          $newdate = unserialize($tmpdate['attr_option']);
          sort($olddate);
          sort($newdate);
              if($olddate!=$newdate){
                $result = array_diff($newdate,$olddate);
                sort($result);
                for($i=0;$i<count($result);$i++){
                    $member->deletememberidattrid($attr_id,$result[$i]);
                }
            }
        }
        $aRs = $this->db->query("SELECT * FROM sdb_member_attr WHERE attr_id=".intval($attr_id));
        $sSql = $this->db->getUpdateSql($aRs,$data);
        return (!$sSql || $this->db->query($sSql));
    }
    
    
    function getAlloption($member_id){
        return $this->db->select("SELECT * FROM sdb_member_mattrvalue AS ma, sdb_member_attr AS at WHERE ma.attr_id = at.attr_id and        ma.member_id = '".$member_id."' order by at.attr_order asc;");
    }
    
    
    function updateorder($order,$attr_id){
        $sql = "update sdb_member_attr set  attr_order = ".$order." where attr_id = '".$attr_id."' ";
        $this->db->exec($sql);    
    }
    
    
    function getMaxOrder(){
        $sql = "select max(attr_order) as attr_order from sdb_member_attr";
        return $this->db->select($sql);
    }
    
    
    function getCustomOption(){
        $sql = "select * from sdb_member_attr where attr_group != 'defalut'";
        return $this->db->select($sql);
    }

    function getCustomValueById($memid){
        $sql = "SELECT * FROM sdb_member_attr WHERE attr_show = 'true' AND attr_group != 'defalut' ORDER BY attr_order ";
        return $this->db->select($sql);
    }








 




}
?>
