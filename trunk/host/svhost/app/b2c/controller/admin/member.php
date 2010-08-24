<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_admin_member extends desktop_controller{

    var $workground = 'b2c_ctl_admin_member';

   function index(){
        $this->update_member();
        $this->finder('b2c_mdl_members',array(
        'title'=>'会员列表',
        'allow_detail_popup'=>true,
        'actions'=>array(
                        array('label'=>'添加会员','href'=>'index.php?app=b2c&ctl=admin_member&act=add_page','target'=>"dialog::{title:'添加会员',width:560,height:300}"),
                        array('label'=>'群发邮件','submit'=>'index.php?app=b2c&ctl=admin_member&act=send_email','target'=>'dialog::{title:\'群发邮件\',width:700,height:500}'),
                        array('label'=>'群发站内信','submit'=>'index.php?app=b2c&ctl=admin_member&act=send_msg','target'=>'dialog::{title:\'群发站内信\',width:500,height:300}'),
                    ),'use_buildin_set_tag'=>true,'use_buildin_filter'=>true,'use_view_tab'=>true,
        ));
    }

    function update_member(){
        $obj_member = $this->app->model('members');
        $obj_order = $this->app->model('orders');
        $msg = kernel::single('b2c_message_msg');
        $aData = $obj_member->getList('member_id');
        foreach($aData as $val){
            $sdf = $obj_member->dump($val['member_id']);
            $sdf['order_num'] = count($obj_order->getList('order_id',array('member_id' => $val['member_id'])));
            $sdf['unreadmsg'] = count($msg->getList('*',array('to_id' => $val['member_id'],'has_sent' => 'true','for_comment_id' => 'all','mem_read_status' => 'false')));
            $obj_member->save($sdf);
        }
    }
   function _views(){
        $mdl_member = $this->app->model('members');
        //今日新增会员
        $today_filter = array(
                    '_regtime_search'=>'between',
                    'regtime_from'=>date('Y-m-d'),
                    'regtime_to'=>date('Y-m-d'),
                    'regtime' => date('Y-m-d'),
                    '_DTIME_'=>
                        array(
                            'H'=>array('regtime_from'=>'00','regtime_to'=>date('H')),
                            'M'=>array('regtime_from'=>'00','regtime_to'=>date('i'))
                        )
                );
        $today_reg = $mdl_member->count($today_filter);
        $sub_menu[0] = array('label'=>__('今日新增会员'),'optional'=>true,'filter'=>$today_filter,'addon'=>$today_reg,'href'=>'index.php?app=b2c&ctl=admin_member&act=index&view=0&view_from=dashboard');
         
        //昨日新增
        $date = strtotime('yesterday');
        $yesterday_filter = array(
                    '_regtime_search'=>'between',
                    'regtime_from'=>date('Y-m-d',$date),
                    'regtime_to'=>date('Y-m-d',$date),
                    'regtime' => date('Y-m-d',$date),
                    '_DTIME_'=>
                        array(
                            'H'=>array('regtime_from'=>'00','regtime_to'=>date('H',$date)),
                            'M'=>array('regtime_from'=>'00','regtime_to'=>date('i',$date))
                        )
                );
        $yesterday_reg = $mdl_member->count($yesterday_filter);
        $sub_menu[1] = array('label'=>__('昨日新增会员'),'optional'=>true,'filter'=>$yesterday_filter,'addon'=>$yesterday_reg,'href'=>'index.php?app=b2c&ctl=admin_member&act=index&view=1&view_from=dashboard');
         
         foreach($sub_menu as $k=>$v){
            if($v['optional']==false){
            }elseif(($_GET['view_from']=='dashboard')&&$k==$_GET['view']){
                $show_menu[$k] = $v;
            }
        }
        return $show_menu;       
    }
    function add_page(){
        /*
        $html  = $this->ui()->form_start('index.php?app=b2c&ctl=admin_member&act=add');
        $html .= $this->ui()->form_input(array('title'=>'用户名','type'=>'text','name'=>'pam_account[login_name]','required'=>true));
        $html .= $this->ui()->form_input(array('title'=>'密码','type'=>'password','name'=>'pam_account[login_password]','required'=>true));
        $html .= $this->ui()->form_input(array('title'=>'确认密码','type'=>'password','name'=>'pam_account[psw_confirm]','required'=>true));
        $html .= $this->ui()->form_input(array('title'=>'E-mail','type'=>'text','name'=>'contact[email]','required'=>true));
        $lv_model = $this->app->model("member_lv");
        foreach($lv_model->get_level() as $row){
            $options[$row['member_lv_id']] = $row['name'];
        }
        $value = $lv_model->get_default_lv();     
        $html .= $this->ui()->form_input(array('title'=>'会员等级','type'=>'select','name'=>'member_lv[member_group_id]','required'=>true,'value'=>$value,'options'=>$options));
        $html .= $this->app->model('member_attr')->gen_form();
        $html .= $this->ui()->form_end();
        echo $html;*/
       
        $member_lv=$this->app->model("member_lv");
        foreach($member_lv->get_level() as $row){
            $options[$row['member_lv_id']] = $row['name'];
        }
        $a_mem['lv']['options'] = is_array($options) ? $options : array(__('请添加会员等级')) ;
        $a_mem['lv']['value'] = $a_mem['member_lv']['member_group_id'];
         $member_model = $this->app->model('members');
        $mem_schema = $member_model->_columns();
        #$attr = $this->app->model('member_attr')->getList();
         $attr =array();
            foreach($this->app->model('member_attr')->getList() as $item){
            if($item['attr_show'] == "true") $attr[] = $item; //筛选显示项
        }
        foreach((array)$attr as $key=>$item){
            $sdfpath = $mem_schema[$item['attr_column']]['sdfpath'];
            if($sdfpath){
                $a_temp = explode("/",$sdfpath);
                if(count($a_temp) > 1){
                    $name = array_shift($a_temp);
                    if(count($a_temp))
                    foreach($a_temp  as $value){
                        $name .= '['.$value.']';
                    }
                }
            }else{
                $name = $item['attr_column'];
            } 
              if($attr[$key]['attr_type'] == 'select' ||$attr[$key]['attr_type'] == 'checkbox'){
                $attr[$key]['attr_option'] = unserialize($attr[$key]['attr_option']);
            }
            
            $attr[$key]['attr_column'] = $name;
             if($attr[$key]['attr_column']=="birthday"){
              $attr[$key]['attr_column'] = "profile[birthday]";
          }
            #unset($name);
        }
        $this->pagedata['attr'] = $attr;
        $this->pagedata['mem'] = $a_mem;
       $this->display('admin/member/new.html');
    }

    function add(){
         foreach($_POST as $key=>$val){
            if(strpos($key,"box:") !== false){
                $aTmp = explode("box:",$key);
                $_POST[$aTmp[1]] = serialize($val);
            }
        }
        //$this->begin('index.php?app=b2c&ctl=admin_member&act=index');
        $this->begin();
        $mem_model = &$this->app->model("members");       
        if($mem_model->validate($_POST,$message)){
        $id = $mem_model->create($_POST);
        if($id!=''&&$id){
            $data['member_id'] = $id;;
            $data['uname'] = $_POST['pam_account']['login_name'];
            $data['passwd'] = $_POST['pam_account']['psw_confirm'];
            $data['email'] = $_POST['contact']['email'];
            $data['is_frontend'] = false;
            $obj_account=&$this->app->model('member_account');
            $obj_account->fireEvent('register',$data,$id);
            $this->end(true, __('添加成功！'));
        }else{
            $this->end(false, __('添加失败！'));
        }
        }
        else{
            $this->end(false, $message);
        }             
    }
    
    function regitem(){
            $this->display('member/member_regitem.html');
        }
    
    function send_email(){
        $aMember = $_POST['member_id'];
        $aEmail = array();
        $obj_member = app::get('b2c')->model('members');
        foreach( $aMember as $mid){
            $aEmail[] = $this->get_email($mid);
            }
         //$_SESSION['email'] = $aEmail;
         $this->pagedata['aEmail'] = json_encode($aEmail);
         $this->page('admin/messenger/write_email.html');
    }
    
    function send_msg(){
        $aMember = $_POST['member_id'];
        //$_SESSION['aMember_id'] = $aMember;
        $this->pagedata['aMember'] = json_encode($aMember);
        $this->page('admin/messenger/write_msg.html');
    }
    
    function msg_queue(){
       $this->begin('index.php?app=b2c&ctl=admin_member&act=index');
       $queue = app::get('base')->model('queue');
       $member_obj = $this->app->model('members');
       if (!get_magic_quotes_gpc()) {
            $aMember = json_decode($_POST['arrMember']);
        } 
        else{
            $aMember = json_decode(stripcslashes($_POST['arrMember']));
        }
        unset($_POST['arrMember']);
       //$aMember = $_SESSION['aMember_id'];
       //unset($_SESSION['aMember_id']);
       foreach($aMember as $key=>$val){
            $member_sdf = $member_obj->dump($val,'*',array(':account@pam'=>array('login_name')));
            $login_name = $member_sdf['pam_account']['login_name'];
            $data = array(
            'queue_title'=>'发站内信',
            'start_time'=>time(),
            'params'=>array(
            'member_id'=>$val,
            'data' =>$_POST,
            'name' => $login_name,
            ),
            'worker'=>'b2c_queue.send_msg',
        );
       if(!$queue->insert($data)){
            $this->end(false,__('操作失败！'));
        }
       }
            $this->end(true,__('操作成功！'));
    }
    function insert_queue(){
        
       $this->begin('index.php?app=b2c&ctl=admin_member&act=index'); 
       $queue = app::get('base')->model('queue');
       if (!get_magic_quotes_gpc()) {
            $aEmail = json_decode($_POST['aEmail']);
        } 
        else{
            $aEmail = json_decode(stripcslashes($_POST['aEmail']));
        }
       //$aEmail = $_SESSION['email'];
       //unset($_SESSION['email']);
       foreach($aEmail as $key=>$val){
           $data = array(
            'queue_title'=>'发邮件'.$key,
            'start_time'=>time(),
            'params'=>array(
            'acceptor'=>$val,
            'body' =>$_POST['content'],
            'title' =>$_POST['title'],
            ),
            'worker'=>'b2c_queue.send_mail',
        );
        if(!$queue->insert($data)){
        $this->end(false,__('操作失败！'));
        }
    }
       $this->end(true,__('操作成功！'));
  
    
  }
   
   function get_email($member_id){
      
       $obj_member = app::get('b2c')->model('members');
       $sdf = $obj_member->dump($member_id);
       return $sdf['contact']['email'];
  }
  
  function chkpassword($member_id=null){
    $member = $this->app->model('members');
    $aMem = $member->dump($member_id,'*',array( ':account@pam'=>array('*')));
    if($_POST){
        $this->begin();
        $member_id = $_POST['member_id'];
        if($_POST['newPassword']!= $_POST['confirmPassword']){
            $this->end(false,__('两次密码不一致'));
        }
        $sdf = $member->dump($member_id,'*',array( ':account@pam'=>array('*')));
        $sdf['pam_account']['login_password'] = md5(trim($_POST['newPassword']));
        if($member->save($sdf)){
            if($_POST['sendemail']){
            $data['member_id'] = $this->app->member_id;
            $data['uname'] = $sdf['pam_account']['login_name'];
            $data['passwd'] = $_POST['newPassword'];
            $data['email'] = $sdf['contact']['email'];
            $obj_account = $this->app->model('member_account');
            $obj_account->fireEvent('chgpass',$data,$member_id);
            }
            $this->end(true,__('修改成功'));
        }
        else{
            $this->end(false,__('修改失败'));
        }
    }
    $this->pagedata['member_id'] = $member_id;
    $this->pagedata['email'] = $aMem['contact']['email'];
    $this->page('admin/member/chkpass.html');
  }
    

}
