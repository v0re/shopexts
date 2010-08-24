<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_finder_members{
    
    var $detail_basic = '会员信息';
    function detail_basic($member_id){
        $app = app::get('b2c');
        $member_model = $app->model('members');
        $mem = $member_model->dump($member_id);
        $a_mem = $member_model->dump($member_id,'*',array( ':account@pam'=>array('*')));
        $mem_schema = $member_model->_columns();
        #$attr = $this->app->model('member_attr')->getList();
        $attr =array();
            foreach($app->model('member_attr')->getList() as $item){
            if($item['attr_show'] == "true" && $item['attr_group']!='defalut') $attr[] = $item; //筛选显示项
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
            #$attr[$key]['attr_value'] = $mem_flat[$sdfpath];
            if($item['attr_group'] == 'defalut'){ 
             switch($attr[$key]['attr_column']){
                    case 'area':
                    $attr[$key]['attr_value'] = $mem['contact']['area'];
                    break;
                     case 'birthday':
                    $attr[$key]['attr_value'] = $mem['profile']['birthday'];
                    break;
                    case 'name':
                    $attr[$key]['attr_value'] = $mem['contact']['name'];
                    break;
                    case 'mobile':
                    $attr[$key]['attr_value'] = $mem['contact']['phone']['mobile'];
                    break;
                    case 'tel':
                    $attr[$key]['attr_value'] = $mem['contact']['phone']['telephone'];
                    break;
                    case 'zip':
                    $attr[$key]['attr_value'] = $mem['contact']['zipcode'];
                    break;
                    case 'addr':
                    $attr[$key]['attr_value'] = $mem['contact']['addr'];
                    break;
                    case 'sex':
                    $attr[$key]['attr_value'] = $mem['profile']['gender'];
                    break;
                    case 'pw_answer':
                    $attr[$key]['attr_value'] = $mem['account']['pw_answer'];
                    break;
                    case 'pw_question':
                    $attr[$key]['attr_value'] = $mem['account']['pw_question'];
                    break;
                   }
           }
          if($item['attr_group'] == 'contact'||$item['attr_group'] == 'input'||$item['attr_group'] == 'select'){
              $attr[$key]['attr_value'] = $mem['contact'][$attr[$key]['attr_column']];
              if($item['attr_sdfpath'] == ""){
              $attr[$key]['attr_value'] = $mem[$attr[$key]['attr_column']];
              if($attr[$key]['attr_type'] =="checkbox"){
              $value = unserialize($mem[$attr[$key]['attr_column']]);
              foreach((array)$value as $val){
                  $v.= ($val.';');
                  }
                  if($v == ';') $v = '';
                  $attr[$key]['attr_value'] = $v;
              }
          }
          }
         
          $attr[$key]['attr_column'] = $name;
          if($attr[$key]['attr_column']=="birthday"){
              $attr[$key]['attr_column'] = "profile[birthday]";
          }
          if($attr[$key]['attr_type'] =="select" || $attr[$key]['attr_type'] =="checkbox"){
              $attr[$key]['attr_option'] = unserialize($attr[$key]['attr_option']);
          }
        }
        $render = $app->render();
        $render->pagedata['attr'] = $attr;
        $render->pagedata['mem'] = $a_mem;
        $render->pagedata['member_id'] = $member_id;
        // 判断是否使用了推广服务
        $is_bklinks = 'false';
        $obj_input_helpers = kernel::servicelist("html_input");
        if (isset($obj_input_helpers) && $obj_input_helpers)
        {
            foreach ($obj_input_helpers as $obj_bdlink_input_helper)
            {
                if (get_class($obj_bdlink_input_helper) == 'bdlink_input_helper')
                {
                    $is_bklinks = 'true';
                }
            }
        }
        $render->pagedata['is_bklinks'] = $is_bklinks;
        return $render->fetch('admin/member/detail.html');
    }  
    
    var $detail__edit = '编辑会员';
    function detail__edit($member_id){
        /*$app = app::get('b2c');
        $member = $app->model('members');
        if($_POST){
            $member->edit($member_id,$_POST);    
        }
        
        $a_mem = $member->dump($member_id,'*',array('contact'=>array('*')));    
        $member_lv=$app->model("member_lv");
        foreach($member_lv->get_level() as $row){
            $options[$row['member_lv_id']] = $row['name'];
        }
        $a_mem['lv']['options'] = is_array($options) ? $options : array(__('请添加会员等级')) ;
        $a_mem['lv']['value'] = $a_mem['member_lv']['member_group_id'];
        
        $render = $app->render();
        $render->pagedata['mem'] = $a_mem;
        return $render->fetch('admin/member/edit.html');*/
        $app = app::get('b2c');
        $member_model = $app->model('members');
        if($_POST){
            $_POST['member_id'] = $member_id;
            foreach($_POST as $key=>$val){
            if(strpos($key,"box:") !== false){
                $aTmp = explode("box:",$key);
                $_POST[$aTmp[1]] = serialize($val);
            }
        }
            $member_model->save($_POST);   
        }
        $mem = $member_model->dump($member_id);
        $a_mem = $member_model->dump($member_id,'*',array( ':account@pam'=>array('*')));
        $member_lv=$app->model("member_lv");
        foreach($member_lv->get_level() as $row){
            $options[$row['member_lv_id']] = $row['name'];
        }
        $a_mem['lv']['options'] = is_array($options) ? $options : array(__('请添加会员等级')) ;
        $a_mem['lv']['value'] = $a_mem['member_lv']['member_group_id'];
        $mem_schema = $member_model->_columns();
        #$attr = $this->app->model('member_attr')->getList();
        $attr =array();
            foreach($app->model('member_attr')->getList() as $item){
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
            #$attr[$key]['attr_value'] = $mem_flat[$sdfpath];
            if($item['attr_group'] == 'defalut'){ 
             switch($attr[$key]['attr_column']){
                    case 'area':
                    $attr[$key]['attr_value'] = $mem['contact']['area'];
                    break;
                     case 'birthday':
                    $attr[$key]['attr_value'] = $mem['profile']['birthday'];
                    break;
                    case 'name':
                    $attr[$key]['attr_value'] = $mem['contact']['name'];
                    break;
                    case 'mobile':
                    $attr[$key]['attr_value'] = $mem['contact']['phone']['mobile'];
                    break;
                    case 'tel':
                    $attr[$key]['attr_value'] = $mem['contact']['phone']['telephone'];
                    break;
                    case 'zip':
                    $attr[$key]['attr_value'] = $mem['contact']['zipcode'];
                    break;
                    case 'addr':
                    $attr[$key]['attr_value'] = $mem['contact']['addr'];
                    break;
                    case 'sex':
                    $attr[$key]['attr_value'] = $mem['profile']['gender'];
                    break;
                    case 'pw_answer':
                    $attr[$key]['attr_value'] = $mem['account']['pw_answer'];
                    break;
                    case 'pw_question':
                    $attr[$key]['attr_value'] = $mem['account']['pw_question'];
                    break;
                   }
           }
          if($item['attr_group'] == 'contact'||$item['attr_group'] == 'input'||$item['attr_group'] == 'select'){
              $attr[$key]['attr_value'] = $mem['contact'][$attr[$key]['attr_column']];
              if($item['attr_sdfpath'] == ""){
              $attr[$key]['attr_value'] = $mem[$attr[$key]['attr_column']];
              if($attr[$key]['attr_type'] =="checkbox"){
              $attr[$key]['attr_value'] = unserialize($mem[$attr[$key]['attr_column']]);
              }
          }
          }
         
          $attr[$key]['attr_column'] = $name;
          if($attr[$key]['attr_column']=="birthday"){
              $attr[$key]['attr_column'] = "profile[birthday]";
          }
          if($attr[$key]['attr_type'] =="select" || $attr[$key]['attr_type'] =="checkbox"){
              $attr[$key]['attr_option'] = unserialize($attr[$key]['attr_option']);
          }
        }
        $render = $app->render();
        $render->pagedata['attr'] = $attr;
        $render->pagedata['mem'] = $a_mem;
        $render->pagedata['member_id'] = $member_id;
        return $render->fetch('admin/member/edit.html');
        #$this->pagedata['attr'] = $attr;
    }    
    
    var $detail_advance = '预存款';
    function detail_advance($member_id){
        $app = app::get('b2c');
        $member = $app->model('members');
        $mem_adv =  $app->model('member_advance');
        
        if($_POST){
            $mem_adv->adj_amount($member_id,$_POST);
        }
        
        $items_adv = $mem_adv->get_list_bymemId($member_id );
        
        $render = $app->render();
        $render->pagedata['items_adv'] = $items_adv;    
        $render->pagedata['member'] = $member->dump($member_id,'advance');
        return $render->fetch('admin/member/advance_list.html');
    }

    var $detail_experience = '经验值';
    function detail_experience($member_id){
        $app = app::get('b2c');
        $member = $app->model('members');
        
        $aMem = $member->dump($member_id,'*',array('contact'=>array('*'))); 
       //exit;   
            if($_POST){
               $member->change_exp($member_id,$_POST['experience']); 
            }
            $aMem = $member->dump($member_id,'*',array('contact'=>array('*'))); 
            $render = $app->render();
            $render->pagedata['mem'] = $aMem;
            return $render->fetch('admin/member/experience.html');
    }

    var $detail_point = '积分';
    function detail_point($member_id){
        $app = app::get('b2c');
        $member = $app->model('members');
        $mem_point = $app->model('member_point');
        
        if($_POST){
            if($mem_point->adj_amount($member_id,$_POST,$msg)){
                #echo "<p><font color='#33CC00'>".$msg."</font></p>";
            }
            else{
                echo "<p><font color='#FF0000'>".$msg."</font></p>";
            }
        }
        $data = $member->dump($member_id,'*',array('score/event'=>array('*')));
        
        $render = $app->render();
        $render->pagedata['member'] = $data;
        $render->pagedata['event'] = $data['score/event'];
        
        return $render->fetch('admin/member/point_list.html');
    }

    var $detail_order = '订单';
    function detail_order($member_id){
        $app = app::get('b2c');
        $member = $app->model('members');
         $orders = $member->getOrderByMemId($member_id);
        // print_r($orders);exit;
         #翻译订单状态值
         $order =  $app->model('orders');
         foreach($orders as $key=>$order1){
             $orders[$key]['status'] = $order->trasform_status('status',$orders[$key]['status']);
             $orders[$key]['pay_status'] = $order->trasform_status('pay_status',$orders[$key]['pay_status'] );
             $orders[$key]['ship_status'] = $order->trasform_status('ship_status', $orders[$key]['ship_status']);      
         }
         
         $render = $app->render();
         $render->pagedata['orders'] = $orders; 
         return $render->fetch('admin/member/order.html');
    }

    var $detail_msg = '信件';
    function detail_msg($member_id){
        $app = app::get('b2c');
        $obj_msg = kernel::single('b2c_message_msg');
        #$msgs_to = $obj_msg->get_msg_by_id($member_id);
        $row = $obj_msg->getList('*',array('to_id' => $member_id,'for_comment_id' => 'all','has_sent' => 'true'));
        $msgs_to['data'] = $row;
        $msgs_to['total'] = count($row);
        $row = $obj_msg->getList('*',array('author_id' => $member_id,'has_sent' => 'true'));
        $aData = array();
        foreach($row as $v){
            if($v['to_id'] == 1) $v['mem_read_status'] = $v['adm_read_status'];
            $aData[] = $v;
        }
        $msgs_from['data'] = $aData;
        $msgs_from['total'] = count($aData); 
        $total = $msgs_to['total'] + $msgs_from['total'];
        $next_total = $msgs_to['total'];
        $render = $app->render();
        $render->pagedata['next_total'] =  $next_total;
        $render->pagedata['msgs_to'] =  $msgs_to['data'];
        $render->pagedata['msgs_from'] =  $msgs_from['data'];
        return $render->fetch('admin/member/member_msg.html');
    }

    var $detail_remark = '会员备注';
    function detail_remark($member_id){
        $app = app::get('b2c');
        $member = $app->model('members');
        if($_POST){
            $sdf = $member->dump($member_id,'*');
            $sdf['remark'] = $_POST['remark'];
            $sdf['remark_type'] = $_POST['remark_type'];
            //$_POST['member_id'] = $member_id ;
            $member->save($sdf);  
        }
        $remark = $member->getRemarkByMemId($member_id);
        
        $render = $app->render();
        $render->pagedata['remark_type'] = $remark['remark_type'];   
        $render->pagedata['remark'] =  $remark['remark'];
        $render->pagedata['res_url'] = $app->res_url;
        return $render->fetch('admin/member/remark.html');
    }

    
}
