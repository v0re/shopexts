<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
define('PLUGIN_DIR_LIB', ROOT_DIR.'/app/b2c/lib');
class b2c_mdl_member_messenger {

    var $plugin_type = 'dir';
    var $plugin_name = 'messenger';
    var $prefix = 'messenger.';
    var $db;
function __construct(&$app){
        $this->app = $app;
        $this->db = kernel::database();
        }
    
function getList($filter=array(), $ifMethods=true,$withDesc=false){
        $handle = opendir(PLUGIN_DIR_LIB.'/'.$this->plugin_name);
        $t = array();

        while(false!==($file=readdir($handle))){
            if($file{0} != '.') {
                    if($this->plugin_case==LOWER_CASE){
                        if($file!=strtolower($file)){
                            continue;
                        }
                    }elseif($this->plugin_case==UPPER_CASE){
                        if($file!=strtoupper($file)){
                            continue;
                        }
                    }
                $params = null;
                if ($this->plugin_type=='dir') {
                    $item = $file;
                    if(is_dir(PLUGIN_DIR_LIB.'/'.$this->plugin_name.'/'.$file) && $this->getFile($item)){
                        $params = $this->getParams($item, $ifMethods,$withDesc);
                    }
                }else{ //file

                    if(preg_match('/^'.($this->prefix!==false?str_replace('.','\.',$this->prefix):$this->plugin_name).'([a-z0-9\_]+)\.php/i',$file, $match)) {
                        $item = $match[1];
                        $params = $this->getParams($item, $ifMethods,$withDesc);
                        $params['item'] = $item;
                    }
                }
                if($params){
                    $params['file'] = 'plugins/'.$this->plugin_name.'/'.$file;
                    $t[$item] = $params;
                }
            }
        }
        closedir($handle);
        ksort($t);
        if($filter) {
            $this->_filter = $filter;
            return array_filter($t,array(&$this, 'filter'));
        }else{
            return $t;
        }
    }
     function _getClassName($item) {
        return preg_replace('/[\.-]+/','_',($this->prefix?$this->prefix:$this->plugin_name).$item);
    }


    function &_load($sender){
        #print_r($this->_sender[$sender]);exit;
        if(!$this->_sender[$sender]){
            $obj = $this->load($sender);
           # print_r($obj);exit;
            $this->_sender[$sender] = &$obj;
            if(method_exists($obj,'getOptions')||method_exists($obj,'getoptions'))
                $obj->config = $this->getOptions($sender,true);
            if(method_exists($obj,'outgoingConfig')||method_exists($obj,'outgoingconfig'))
                $obj->outgoingOptions = $this->outgoingConfig($sender,true);
        }else{
            $obj = &$this->_sender[$sender];
        }
        return $obj;
    }

    function _ready(&$obj){
        if(!$obj->_isReady){
            if(method_exists($obj,'ready')) $obj->ready($obj->config);
            if(method_exists($obj,'finish')){
                if(!$this->_finishCall){
                    register_shutdown_function(array(&$this,'_finish'));
                    $this->_finishCall=array();
                }
                $this->_finishCall[] = &$obj;
            }
            $obj->_isReady = true;
        }
    }

    function _send($sendMethod,$tmpl_name,$target,$data,$type,$title=null){
        $sender = &$this->_load($sendMethod);
        $this->_ready($sender);
        if(!$this->_systmpl){
            $this->_systmpl = &$this->app->model('member_systmpl');
        }
        $content = $this->_systmpl->fetch($tmpl_name,$data);
        $to = $this->get_send_type(get_class($sender),$data,$data['member_id']);
        $tile = $this->loadTitle($type,$sendMethod,'',$data);
        if($tile=='') $tile = app::get('site')->getConf('site.name');
       /* $ret = $sender->hasTitle?$sender->send($target,
            $title?$title:$this->loadTitle($type,$sendMethod,'',$data)
                ,$content,$sender->config):$sender->send($target,$content,$sender->config);*/
                #$sender->config['shopname'] = app::get('b2c')->getConf('shopname');
                $sender->config['shopname'] = app::get('site')->getConf('site.name');
        $sender->send($target,$tile,$content,$sender->config);
        return ($ret || !is_bool($ret));


    }
    
    ##获取发送对象的联系方式 /email,ID,phone
    
    function get_send_type($type,$data,$member_id){
        $type_msg = 'messenger_'.$type;
        $obj_member = $this->app->model('members');
        $sdf = $obj_member->dump($member_id);
        if($type_msg == "messenger_msgbox") {
        $target = $member_id; 
        }
        if($type_msg == "messenger_email"){
        $target = $sdf['contact']['email'];
        if(!$target) $target = $data['email'];
        }
        if($type_msg == "messenger_sms") {
       $target = $sdf['contact']['phone']['mobile'];
        }
        return $target;
    }

    function _finish(){
        foreach($this->_finishCall as $obj){
            $obj->finish($obj->config);
        }
    }

    function _target($sender,$contectInfo,$member_id){
        $obj = &$this->_load($sender);
        if(($dataname = $obj->dataname) && $contectInfo[$dataname]){
            return $contectInfo[$dataname];
        }else{
            $row = $this->db->selectrow('select email,member_id,name,custom,mobile from sdb_b2c_members where member_id='.intval($member_id));
            if($dataname){
                return $row[$dataname];
            }elseif($custom = unserialize($row['custom'])){
                return $custom[$sender];
            }else{
                return false;
            }
        }
    }

    /**
     * actionSend
     *
     * @param mixed $type 类型
     * @param mixed $contectInfo  联系数组
     * @param mixed $member_id 会员id
     * @param mixed $data 信息
     * @access public
     * @return void
     */
    function actionSend($type,$data,$member_id=null){
        $actions = $this->actions();
        $senders = $this->getSenders($type); //email/msbox/sms
        $level = $actions[$type]['level'];
        $desc = $actions[$type]['label'];
        foreach($senders as $sender){
            $tmpl_name = 'messenger:'.$sender.'/'.$type;
            $contractInfo = $data;
           #$q = $this->_target($sender,$contractInfo,$member_id);
           # if($sender && !($target = $this->_target($sender,$contractInfo,$member_id))){
             if($sender && ($target = $this->get_send_type($sender,$data,$member_id))){
                if($level < 9){ //队列
                    $this->addQueue($sender,$target,$desc,$data,$tmpl_name,$level,$type);
                }else{ //直接发送 print
                    #print_r($target);exit;
                    $this->_send($sender,$tmpl_name,$target,$data,$type);
                }
            }
        }
    }

   

    function getSenders($act){
        $ret = $this->app->getConf('messenger.actions.'.$act);
        return explode(',',$ret);
    }

    function saveActions($actions){

        foreach($this->actions() as $act=>$info){
            if(!$actions[$act]){
                $actions[$act] = array();
            }
        }

        foreach($actions as $act=>$call){
            $this->app->setConf('messenger.actions.'.$act,implode(',',array_keys($call)));
        }
        return true;
    }

    /**
     * actions
     * 所有自动消息发送列表，只要触发匹配格式的事件就会发送
     *
     * 格式：
     *            对象-事件 => array(label=>名称 , level=>紧急程度)
     *
     * 如果不存在匹配的事件，则需要手动通过send()方法发送
     *
     * @access public
     * @return void
     */
    function actions(){
        $actions = array(
            'account-lostPw'=>array('label'=>__('会员找回密码'),'level'=>9,'varmap'=>__('用户名&nbsp;<{$uname}>&nbsp;&nbsp;&nbsp;&nbsp;密码&nbsp;<{$passwd}>&nbsp;&nbsp;&nbsp;&nbsp;姓名&nbsp;<{$name}>')),
            'orders-shipping'=>array('label'=>__('订单发货时'),'level'=>9,'varmap'=>__('订单号&nbsp;<{$order_id}>&nbsp;&nbsp;&nbsp;&nbsp;实际费用&nbsp;<{$delivery.money}>&nbsp;&nbsp;&nbsp;&nbsp;配送方式&nbsp;<{$delivery.delivery}><br>物流公司&nbsp;<{$ship_corp}>&nbsp;&nbsp;&nbsp;&nbsp;物流单号&nbsp;<{$ship_billno}>&nbsp;&nbsp;&nbsp;&nbsp;收货人姓名&nbsp;<{$delivery.ship_name}><br>收货人地址&nbsp;<{$delivery.ship_addr}>&nbsp;&nbsp;&nbsp;&nbsp;收货人邮编&nbsp;<{$delivery.ship_zip}>&nbsp;&nbsp;&nbsp;&nbsp;收货人电话&nbsp;<{$delivery.ship_tel}><br>收货人手机&nbsp;<{$delivery.ship_mobile}>&nbsp;&nbsp;&nbsp;&nbsp;收货人Email&nbsp;<{$delivery.ship_email}>&nbsp;&nbsp;&nbsp;&nbsp;操作者&nbsp;<{$delivery.op_name}><br>备注&nbsp;<{$delivery.memo}>')),
            'orders-create'=>array('label'=>__('订单创建时'),'level'=>9,'varmap'=>__('订单号&nbsp;<{$order_id}>&nbsp;&nbsp;&nbsp;&nbsp;总价&nbsp;<{$total_amount}>&nbsp;&nbsp;&nbsp;&nbsp;配送方式&nbsp;<{$shipping_id}><br>收货人手机&nbsp;<{$ship_mobile}>&nbsp;&nbsp;&nbsp;&nbsp;收货人电话&nbsp;<{$ship_tel}>&nbsp;&nbsp;&nbsp;&nbsp;收货人地址&nbsp;<{$ship_addr}><Br>收货人Email&nbsp;<{$ship_email}>&nbsp;&nbsp;&nbsp;&nbsp;收货人邮编&nbsp;<{$ship_zip}>&nbsp;&nbsp;&nbsp;&nbsp;收货人姓名&nbsp;<{$ship_name}>')),
            'orders-payed'=>array('label'=>__('订单付款时'),'level'=>9,'varmap'=>__('订单号&nbsp;<{$order_id}>&nbsp;&nbsp;&nbsp;&nbsp;付款时间&nbsp;<{$pay_time}>&nbsp;&nbsp;&nbsp;&nbsp;付款金额&nbsp;<{$money}>')),
            'orders-returned'=>array('label'=>__('订单退货时'),'level'=>9,'varmap'=>__('订单号&nbsp;<{$order_id}>')),
            'orders-refund'=>array('label'=>__('订单退款时'),'level'=>9,'varmap'=>__('订单号&nbsp;<{$order_id}>')),
             'goods-notify'=>array('label'=>__('商品到货通知'),'level'=>6,'varmap'=>__('商品名称&nbsp;<{$goods_name}>&nbsp;&nbsp;&nbsp;&nbsp;会员名称&nbsp;<{$username}>')),        
               /*             'goods-replay'=>array('label'=>'商品评论回复','level'=>9), todo */
            'account-register'=>array('label'=>__('会员注册时'),'level'=>9,'varmap'=>__('用户名&nbsp;<{$uname}>&nbsp;&nbsp;&nbsp;&nbsp;email&nbsp;<{$email}>&nbsp;&nbsp;&nbsp;&nbsp;密码&nbsp;<{$passwd}>')),
            'account-chgpass'=>array('label'=>__('会员更改密码时'),'level'=>9,'varmap'=>__('密码&nbsp;<{$passwd}>&nbsp;&nbsp;&nbsp;&nbsp;登录名&nbsp;<{$uname}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;email&nbsp;<{$email}>')),
            /*             'comment-replay'=>array('label'=>'留言回复时','level'=>9,'varmap'=>''), todo */
            /*             'indexorder-pay'=>array('label'=>'前台订单支付','level'=>9), */
            /*             'comment-new'=>array('label'=>'订单生成通知商家','level'=>9), */
            'orders-cancel'=>array('label'=>__('订单作废'),'level'=>9,'varmap'=>__('订单号&nbsp;<{$order_id}>')),
        );

        return $actions;
    }


    function loadTmpl($action,$msg,$lang=''){
        $systmpl = &$this->app->model('member_systmpl');
        return $systmpl->get('messenger:'.$msg.'/'.$action);
    }

    function loadTitle($action,$msg,$lang='',$data=""){

        $tmpArr=$data;
        $title = $this->app->getConf('messenger.title.'.$action.'.'.$msg);

        if($data!=""){
            preg_match_all('/<\{\$(\S+)\}>/iU', $title, $result);

            foreach($result[1] as $k => $v){
               $v=explode('.',$v);
               $data=$tmpArr;

               foreach($v as $key => $val){

                     $data=$data[$val];

                     if(is_array($data))
                     continue ;
                     else{

                         $title = str_replace($result[0][$k],$data,$title);

                     }

                 }
             }

         }

        return $title;
    }

   

   

   
    function saveContent($action,$msg,$data){

        $systmpl = &$this->app->model('member_systmpl');      
        $info = $this->getParams($msg);
 
        if($info['hasTitle']) $this->app->setConf('messenger.title.'.$action.'.'.$msg,$data['title']);
        return $systmpl->set('messenger:'.$msg.'/'.$action,$data['content']);
    }
    
    
       function getParams($item, $ifMethods=true,$withDesc = false){
        $t = array('name'=>$item);
        
        $file = $this->getFile($item);
        include_once($file);
        $className = $this->_getClassName($item);
        $t['class'] = $className;
        if(class_exists($className)){
            $o = new $className;
            $t =array_merge($t, get_object_vars($o));
            if ($ifMethods) {
                $t['methods'] = get_class_methods($className);
            }

            //for PHP4/PHP5 Compatibility
            $t['hasOptions'] = in_array('getoptions',$t['methods'])||in_array('getOptions',$t['methods']);
            if(in_array('extravars',$t['methods'])||in_array('extraVars',$t['methods'])){
                $obj = new $className;
                $t = array_merge($t,$obj->extraVars());
            }
        }
        if($withDesc){
            $t['desc'] = $this->getHeader($file);
        }
        return $t;
    }
        function getFile($item) {

        $file_name = ($this->plugin_type=='dir')?
            PLUGIN_DIR_LIB.'/'.$this->plugin_name.'/'.$item.'/'.($this->prefix!==false?$this->prefix:$this->plugin_name).$item.'.php':
            PLUGIN_DIR_LIB.'/'.$this->plugin_name.'/'.($this->prefix!==false?$this->prefix:$this->plugin_name).$item.'.php';
        if (is_file($file_name)) {
            return $file_name;
        }else{
            return false;
        }
    }

   /*
   plugin 中的方法
       */
        function &load($item){

        if (!$this->_plugin_obj[$item]) {

            if ($file_name = $this->getFile($item)){
                include_once($file_name);
                $className = $this->_getClassName($item);
                $obj = new $className;
                return $obj;
            }else{
                return null;
                #trigger_error('plugin file error', E_USER_ERROR);
            }
        }
        return $this->_plugin_obj[$item];
    }
     /*
   plugin 中的方法
       */
function getOptions($item,$valueOnly = false){
        $obj = $this->load($item);
        #print_r($item);exit;
        if(method_exists($obj,'getOptions')||method_exists($obj,'getoptions')){
            $options = $obj->getOptions();      #print_r($options);exit;
                      foreach($options as $key=>$value){
            $app = app::get('desktop');
               # $v = $this->app->getConf('plugin.'.$this->plugin_name.'.'.$item.'.config.'.$key);
               $v = $app->getConf('email.config.'.$key);
               #print_r($v);exit;
                if($valueOnly){
                    $options[$key] = (is_null($v))?$options[$key]:$v;
                }else{
                    $options[$key]['value'] = (is_null($v))?$options[$key]['value']:$v;
                }
            }
            return $options;
        }
    }

}



?>
