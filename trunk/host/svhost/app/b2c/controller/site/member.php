<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class b2c_ctl_site_member extends b2c_frontpage{

    function __construct(&$app){
        parent::__construct($app);
        $this->header .= '<meta name="robots" content="noindex,noarchive,nofollow" />';
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->title=__('会员中心');
        $this->verify_member();
        $this->pagesize = 10;
        $this->action = $this->_request->get_act_name();
        if(!$this->action) $this->action = 'index';
        $this->action_view = $this->action.".html";
        $this->load_info();
    }

    private function get_cpmenu(){
        $arr_bases = array(
            array('label'=>__('交易记录'),
            'mid'=>0,
            'items'=>array(
                array('label'=>__('我的订单'),'link'=>'orders'),
                array('label'=>__('我的积分'),'link'=>'point_history'),
                 array('label'=>__('积分兑换优惠券'),'link'=>'couponExchange'),
                  array('label'=>__('我的优惠券'),'link'=>'coupon')
            )
        ),
        array('label'=>__('收藏夹'),
            'mid'=>1,
            'items'=>array(
                array('label'=>__('商品收藏'),'link'=>'favorite'),
                array('label'=>__('缺货登记'),'link'=>'notify')
            ),
        ),
         array('label'=>__('商店留言'),
            'mid'=>2,
            'items'=>array(
                array('label'=>__('评论与咨询'),'link'=>'comment'),
            ),
        ),
        array('label'=>__('个人设置'),
            'mid'=>3,
            'items'=>array(
                array('label'=>__('个人信息'),'link'=>'setting'),
                array('label'=>__('修改密码'),'link'=>'security'),
                array('label'=>__('收货地址'),'link'=>'receiver'),
            ),
        ),
        array('label'=>__('预存款'),
            'mid'=>4,
            'items'=>array(
                array('label'=>__('我的预存款'),'link'=>'balance'),
                array('label'=>__('预存款充值'),'link'=>'deposit'),
            ),
         ),
         array('label'=>__("站内消息(").$this->member['un_readmsg'].")",
            'mid'=>5,
            'items'=>array(
                array('label'=>__('发送消息'),'link'=>'send'),
                array('label'=>__('收件箱'),'link'=>'inbox'),
                array('label'=>__('草稿箱'),'link'=>'outbox'),
                array('label'=>__('发件箱'),'link'=>'track'),
                array('label'=>__('给管理员发消息'),'link'=>'message'),
            )
         ),
        );
        $arr_extends = array();
                
        $app_after_sales = app::get('aftersales');
        if (isset($app_after_sales) && is_object($app_after_sales) && $app_after_sales)
        {
            if ($app_after_sales->is_installed())
            {
                $obj_return_policy = kernel::service("aftersales.return_policy");
                if (isset($obj_return_policy) && $obj_return_policy && is_object($obj_return_policy))
                {
                    if ($app_after_sales->getConf('site.is_open_return_product'))
                        $arr_extends = array(
                            array('label' => __('售后服务'),
                                'mid'=>6,
                                'items' => array(
                                    array('label' => __('申请售后服务'),'link'=>'return_policy'),
                                ),
                            )
                        );
                }
            }
        }
        
        return array_merge($arr_bases, $arr_extends);
    }

    public function return_policy()
    {
        $this->begin($this->gen_url(array('app' => 'b2c', 'ctl' => 'site_member')));
        $obj_return_policy = kernel::service("aftersales.return_policy");
        $arr_settings = array();
        
        if (!isset($obj_return_policy) || !is_object($obj_return_policy))
        {
            $this->end(false, __("售后服务应用不存在！"));
        }
        
        if (!$obj_return_policy->get_conf_data($arr_settings))
        {
            $this->end(false, __("售后服务信息没有取到！"));
        }
        
        if(!$arr_settings['is_open_return_product']){
            $this->end(false, __("售后服务信息没有开启！"));
        }
        
        $this->pagedata['is_open_return_product'] = $arr_settings['is_open_return_product'];
        $this->pagedata['comment'] = $arr_settings['return_product_comment'];
        $this->output();
    }
    
    public function return_list($nPage=1) 
    {
        $this->begin($this->gen_url(array('app' => 'b2c', 'ctl' => 'site_member')));
        $obj_return_policy = kernel::service("aftersales.return_policy");
        $arr_settings = array();
        
        if (!isset($obj_return_policy) || !is_object($obj_return_policy))
        {
            $this->end(false, __("售后服务应用不存在！"));
        }
        
        if (!$obj_return_policy->get_conf_data($arr_settings))
        {
            $this->end(false, __("售后服务信息没有取到！"));
        }
        
        if(!$arr_settings['is_open_return_product']){
            $this->end(false, __("售后服务信息没有开启！"));
        }
        
        $clos = "return_id,order_id,title,add_time,status";
        $filter = array();
        $filter["member_id"] = $this->member['member_id'];
        if( $_POST["title"] != "" ){
            $filter["title"] = $_POST["title"];
        }

        if( $_POST["status"] != "" ){
            $filter["status"] = $_POST["status"];
        }

        if( $_POST["order_id"] != "" ){
            $filter["order_id"] = $_POST["order_id"];
        }
        
        $aData = $obj_return_policy->get_return_product_list($clos, $filter, $nPage);
        if (isset($aData['data']) && $aData['data'])
            $this->pagedata['return_list'] = $aData['data'];
            
        $arrPager = $this->get_start($nPage, $aData['total']);
        $this->pagination($nPage, $arrPager['maxPage'], 'return_list');

        $this->output();
    }
    
    public function return_order_list($nPage=1)
    {
        $this->begin($this->gen_url(array('app' => 'b2c', 'ctl' => 'site_member')));
        $obj_return_policy = kernel::service("aftersales.return_policy");
        $arr_settings = array();
        
        if (!isset($obj_return_policy) || !is_object($obj_return_policy))
        {
            $this->end(false, __("售后服务应用不存在！"));
        }
        
        if (!$obj_return_policy->get_conf_data($arr_settings))
        {
            $this->end(false, __("售后服务信息没有取到！"));
        }
        
        if(!$arr_settings['is_open_return_product']){
            $this->end(false, __("售后服务信息没有开启！"));
        }
        
        $obj_orders = $this->app->model('orders');
        $clos = "order_id,createtime,final_amount,currency";
        $filter = array();
        if( $_POST['order_id'] )
        {
            $filter['order_id'] = $_POST['order_id'];
        }
        $filter['member_id'] = $this->member['member_id'];
        $filter['pay_status'] = 1;
        $filter['ship_status'] = 1;
         
        $aData = $obj_orders->getList($clos, $filter, ($nPage-1)*10, 10);
        if (isset($aData) && $aData)
            $this->pagedata['orders'] = $aData;        
        $total = $obj_orders->count($filter);
        
        $arrPager = $this->get_start($nPage, $total);
        $this->pagination($nPage, $arrPager['maxPage'], 'return_order_list');
        
        $this->output();
    }
    
    public function return_add($order_id,$page=1)
    {
        $this->begin($this->gen_url(array('app' => 'b2c', 'ctl' => 'site_member')));
        $obj_return_policy = kernel::service("aftersales.return_policy");
        $arr_settings = array();
        
        if (!isset($obj_return_policy) || !is_object($obj_return_policy))
        {
            $this->end(false, __("售后服务应用不存在！"));
        }
        
        if (!$obj_return_policy->get_conf_data($arr_settings))
        {
            $this->end(false, __("售后服务信息没有取到！"));
        }
        
        if(!$arr_settings['is_open_return_product']){
            $this->end(false, __("售后服务信息没有开启！"));
        }
        
        $limit = 10;
        $objOrder = &$this->app->model('orders');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $this->pagedata['order'] = $objOrder->dump($order_id, '*', $subsdf);
        
        // 校验订单的会员有效性.
        $is_verified = ($this->_check_verify_member($this->pagedata['order']['member_id'])) ? $this->_check_verify_member($this->pagedata['order']['member_id']) : false;
        
        // 校验订单的有效性.
        if ($_COOKIE['ST_ShopEx-Order-Buy'] != md5($this->app->getConf('certificate.token').$order_id) && !$is_verified)
        {
            $this->end(false,  __('订单无效！'), array('app'=>'site','ctl'=>'default','act'=>'index'));
        }
        
        $this->pagedata['orderlogs'] = $objOrder->getOrderLogList($order_id, $page, $limit);
        
        if(!$this->pagedata['order'])
        {
            $this->end(false,  __('订单无效！'), array('app'=>'site','ctl'=>'default','act'=>'index'));
        }
        
        $order_items = array();
        foreach ($this->pagedata['order']['order_objects'] as $k=>$arrOdr_object)
        {
            $index = 0;
            $index_adj = 0;
            $index_gift = 0;
            if ($arrOdr_object['obj_type'] == 'goods')
            {
                foreach($arrOdr_object['order_items'] as $key => $item)
                {      
                    $objGoods = $this->app->model('goods');
                    $arrGoods = $objGoods->dump($item['goods_id'], 'goods_id,cat_id,score,price,name,udfimg,thumbnail_pic,small_pic,big_pic,image_default_id');
                    $objGoodsCat = $this->app->model('goods_cat');
                    $arrGoodsCat = $objGoodsCat->dump($arrGoods['category']['cat_id'], 'cat_name');
                                  
                    if ($item['item_type'] != 'gift')
                    {
                        $gItems[$k]['addon'] = unserialize($item['addon']);
                        if($item['minfo'] && unserialize($item['minfo'])){
                            $gItems[$k]['minfo'] = unserialize($item['minfo']);
                        }else{
                            $gItems[$k]['minfo'] = array();
                        }
                        
                        if ($item['item_type'] == 'product')
                        {  
                            $order_items[$k] = $item;
                            $order_items[$k]['thumbnail_pic'] = $arrGoods['image_default_id'];
                            $order_items[$k]['is_type'] = $arrOdr_object['obj_type'];
                            $order_items[$k]['item_type'] = $arrGoodsCat['cat_name'];
                            
                            if (isset($item['products']['spec_info']) && $item['products']['spec_info'])
                            {
                                $order_items[$k]['name'] = $item['products']['name'] . '(' . $item['products']['spec_info'] . ')';
                            }
                            else
                            {
                                $order_items[$k]['name'] = $item['products']['name'];
                            }
                        }                        
                    }                    
                }
            }
        }
        
        $this->pagedata['order_id'] = $order_id;
        $this->pagedata['order']['items'] = array_slice($order_items,($page-1)*$limit,$limit);
        $count = count($order_items);
        $arrMaxPage = $this->get_start($page, $count);
        $this->pagination($page, $arrMaxPage['maxPage'], 'return_add', array($order_id));
        
        //$this->pagedata['url'] = $this->system->mkUrl('member','return_order_items',array($order_id));
        $this->pagedata['url'] = $this->gen_url(array('app' => 'b2c', 'ctl' => 'site_member', 'act' => 'return_order_items', 'arg' => array($order_id)));
        $this->output();
    }
    
    public function return_save()
    {
        $this->begin($this->gen_url(array('app' => 'b2c', 'ctl' => 'site_member')));
        $obj_return_policy = kernel::service("aftersales.return_policy");
        $arr_settings = array();
        
        if (!isset($obj_return_policy) || !is_object($obj_return_policy))
        {            
            $this->end(false, __("售后服务应用不存在！"));
        }
        
        if (!$obj_return_policy->get_conf_data($arr_settings))
        {
            $this->end(false, __("售后服务信息没有取到！"));
        }
        
        if(!$arr_settings['is_open_return_product'])
        {
            $this->end(false, __("售后服务信息没有开启！"));
        }
        
        $upload_file = "";
        if ( $_FILES['file']['size'] > 314572800 )
        {
            $com_url = $this->gen_url(array('app' => 'b2c', 'ctl' => 'site_member', 'act' => 'return_add', 'arg' => array($_POST['order_id'])));
            $this->end(false, __("上传文件不能超过300M"), $com_url);
        }
        
        if ( $_FILES['file']['name'] != "" )
        {
            $type=array("jpg","gif","bmp","jpeg","rar","zip");
            
            if(!in_array(strtolower($this->fileext($_FILES['file']['name'])), $type))
            {
                $text = implode(",", $type);
                $com_url = $this->gen_url(array('app' => 'b2c', 'ctl' => 'site_member', 'act' => 'return_add', 'arg' => array($_POST['order_id'])));
                $this->end(false, __("您只能上传以下类型文件: ") . $text . "<br>", $com_url);
            }
            
            $mdl_img = app::get('image')->model('image');
            $image_name = $_FILES['file']['name'];
            $image_id = $mdl_img->store($_FILES['file']['tmp_name'],null,null,$image_name);
            $mdl_img->rebuild($image_id,array('L','M','S'));
            
            if (isset($_REQUEST['type']))
            {
                $type = $_REQUEST['type'];
            }
            else
            {
                $type = 's';
            }
            $image_src = base_storager::image_path($image_id, $type);
            
            /*$file_type = strtolower($this->fileext($_FILES['file']['name']));
            $file_path = HOME_DIR."/upload/";
            $file_name = time().rand(0,15);
            $upload_file = $file_path.$file_name.".".$file_type;
            if(move_uploaded_file($_FILES['file']['tmp_name'],$upload_file)){
                $upload_file = realpath($upload_file);
            }*/
        }
        
        $obj_filter = kernel::single('b2c_site_filter');
        $_POST = $obj_filter->check_input($_POST);
        
        $product_data = array();
        foreach ($_POST['product_bn'] as $key => $val)
        {
            $item = array();
            $item['bn'] = $val;
            $item['name'] = $_POST['product_name'][$key];
            $item['num'] = intval($_POST['product_nums'][$key]);
            $product_data[] = $item;
        }
        
        $aData['order_id'] = $_POST['order_id'];
        $aData['title'] = $_POST['title'];
        $aData['add_time'] = time();
        $aData['image_file'] = $image_id;
        $aData['member_id'] = $this->member['member_id'];
        $aData['product_data'] = serialize($product_data);
        $aData['content'] = $_POST['content'];
        $aData['status'] = 1;
        
        $msg = "";
        $obj_aftersales = kernel::service("api.aftersales.request");
        if ($obj_aftersales->generate($aData, $msg))
        {
            $this->end(true, __('提交成功！'), $this->gen_url(array('app' => 'b2c', 'ctl' => 'site_member', 'act' => 'return_list')));
        }
        else
        {
            $this->end(false, $msg, $this->gen_url(array('app' => 'b2c', 'ctl' => 'site_member', 'act' => 'return_list')));
        }
    }
    
    public function return_details($return_id)
    {
        $this->begin($this->gen_url(array('app' => 'b2c', 'ctl' => 'site_member')));
        $obj_return_policy = kernel::service("aftersales.return_policy");
        $arr_settings = array();
        
        if (!isset($obj_return_policy) || !is_object($obj_return_policy))
        {            
            $this->end(false, __("售后服务应用不存在！"));
        }
        
        if (!$obj_return_policy->get_conf_data($arr_settings))
        {
            $this->end(false, __("售后服务信息没有取到！"));
        }
        
        if(!$arr_settings['is_open_return_product'])
        {
            $this->end(false, __("售后服务信息没有开启！"));
        }
        
        $this->pagedata['return_item'] =  $obj_return_policy->get_return_product_by_return_id($return_id);
        $this->pagedata['return_id'] = $return_id;
        if( !($this->pagedata['return_item']) )
        {
           $this->begin($this->gen_url(array('app' => 'b2c', 'ctl' => 'site_member', 'act' => 'return_list')));
           $this->end(false, $this->app->_("售后服务申请单不存在！"));
        }
        
        $this->output();
    }
    
    public function file_download($return_id)
    {
        $obj_return_policy = kernel::service("aftersales.return_policy");
        $obj_return_policy->file_download($return_id);
    }
    
    private function fileext($filename)
    {
        return substr(strrchr($filename, '.'), 1);
    }
    
    private function output(){
        $this->pagedata['member'] = $this->member;
        $this->pagedata['cpmenu'] = $this->get_cpmenu();
        $this->pagedata['current'] = $this->action;
        if( $this->pagedata['_PAGE_'] ){
            $this->pagedata['_PAGE_'] = 'site/member/'.$this->pagedata['_PAGE_'];
        }else{
           $this->pagedata['_PAGE_'] = 'site/member/'.$this->action_view;      
        }
        
        $this->pagedata['_MAIN_'] = 'site/member/main.html';
        #var_dump($this->pagedata);
        $this->set_tmpl('member');
        $this->page('site/member/main.html');
    }
    
   private function load_info(){
       #获取会员基本信息
        $obj_member = &$this->app->model('members');
        $member_sdf = $obj_member->dump($this->app->member_id,"*",array(':account@pam'=>array('*'))); 
        $this->member['member_id'] = $member_sdf['pam_account']['account_id'];
        $this->member['uname'] =  $member_sdf['pam_account']['login_name'];
        $this->member['name'] = $member_sdf['contact']['name'];
        $this->member['sex'] =  $member_sdf['profile']['gender'];
        $this->member['point'] = $member_sdf['score']['total'];
        $this->member['experience'] = $member_sdf['experience'];
        $this->member['email'] = $member_sdf['contact']['email'];
        $this->member['member_lv'] = $member_sdf['member_lv']['member_group_id'];
        #获取会员等级
        $obj_mem_lv = &$this->app->model('member_lv');
        $levels = $obj_mem_lv->dump($member_sdf['member_lv']['member_group_id']);
        if($levels['disabled']=='false'){
            $this->member['levelname'] = $levels['name'];
        }
        #获取站内信
        $mem_msg = kernel::single('b2c_message_msg');
        $aData = $mem_msg->getList('*',array('to_id' => $this->member['member_id'],'for_comment_id' => 'all','has_sent' => 'true','mem_read_status' => 'false'));
        $this->member['un_readmsg'] = count($aData);

    }

    function pagination($current,$totalPage,$act,$arg=''){ //本控制器公共分页函数
        if (!$arg)
            $this->pagedata['pager'] = array(
                'current'=>$current,
                'total'=>$totalPage,
                'link' =>$this->gen_url(array('app'=>'b2c', 'ctl'=>'site_member','act'=>$act,'args'=>array(($tmp = time())))),
                'token'=>$tmp,
                );
        else
        {
            $arg = array_merge($arg, array(($tmp = time())));
            $this->pagedata['pager'] = array(
                'current'=>$current,
                'total'=>$totalPage,
                'link' =>$this->gen_url(array('app'=>'b2c', 'ctl'=>'site_member','act'=>$act,'args'=>$arg)),
                'token'=>$tmp,
                );
        }
    }
    
    function get_start($nPage,$count){
        $maxPage = ceil($count / $this->pagesize);
        if($nPage > $maxPage) $nPage = $maxPage;
        $start = ($nPage-1) * $this->pagesize;
        $start = $start<0 ? 0 : $start;
        $aPage['start'] = $start;
        $aPage['maxPage'] = $maxPage;
        return $aPage;
    }

    function setting(){
        $member_model = &$this->app->model('members');
        $mem = $member_model->dump($this->app->member_id);
        $cur_model = app::get('ectools')->model('currency');
        $cur = $cur_model->curAll();
        foreach((array)$cur as $item){
           $options[$item['cur_code']] = $item['cur_name'];
        }
        $cur['options'] = $options;
        $cur['value'] = $mem['currency'];
        $this->pagedata['currency'] = $cur;
        #utils::array_to_flat($mem,$mem_flat);
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
          
          if($attr[$key]['attr_type'] =="select" ||$attr[$key]['attr_type'] =="checkbox"){
              $attr[$key]['attr_option'] = unserialize($attr[$key]['attr_option']);
          }
          
        }
        $this->pagedata['attr'] = $attr;
        $this->pagedata['email'] = $mem['contact']['email'];
        $this->output();
    }
    
    function save_setting(){
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>"site_member",'act'=>"setting"));
        $member_model = &$this->app->model('members');
        $_POST['member_id'] = $this->app->member_id;
        foreach($_POST as $key=>$val){
            if(strpos($key,"box:") !== false){
                $aTmp = explode("box:",$key);
                $_POST[$aTmp[1]] = serialize($val);
            }
        }
        $_POST = $this->check_input($_POST);
        if($member_model->save($_POST)){
            $this->splash('success', $url , __('提交成功'));
        }else{
            $this->splash('failed',$url , __('提交失败'));
        }
    }
    
    /**
     * Member order list datasource
     * @params int equal to 1
     * @return null
     */
    public function orders($nPage=1)
    {
        $order = &$this->app->model('orders');
        $aData = $order->fetchByMember($this->app->member_id,$nPage-1);
        
        $this->get_order_details($aData);
        $this->pagedata['orders'] = $aData['data'];
        $this->pagination($nPage,$aData['pager']['total'],'orders');
        //$this->pagedata['pager'] = $aData['pager'];
        
        $this->output();
    }
    
    /**
     * 得到订单列表详细
     * @param array 订单详细信息
     * @return null
     */
    private function get_order_details(&$aData)
    {
        if (isset($aData['data']) && $aData['data'])
        {
            foreach ($aData['data'] as &$arr_data_item)
            {
                $arr_data_item['goods_items'] = array();
                $obj_specification = $this->app->model('specification');
                $obj_spec_values = $this->app->model('spec_values');
                $obj_goods = $this->app->model('goods');
                $index = 0;
                if (isset($arr_data_item['order_objects']) && $arr_data_item['order_objects'])
                {
                    foreach ($arr_data_item['order_objects'] as $arr_objects)
                    {
                        if ($arr_objects['obj_type'] == 'goods')
                        {
                            foreach ($arr_objects['order_items'] as $arr_items)
                            {
                                if ($arr_items['item_type'] == 'product')
                                {
                                    $arr_data_item['goods_items'][$index] = $arr_items;
                                    $arr_goods = $obj_goods->dump($arr_items['goods_id'], 'image_default_id');
                                    $arr_data_item['goods_items'][$index]['thumbnail_pic'] = $arr_goods['image_default_id'];
                                    if ($arr_items['addon'])
                                    {
                                        $arr_data_item['goods_items'][$index]['minfo'] = unserialize($arr_items['addon']);
                                    }
                                    if ($arr_items['products']['spec_desc'])
                                    {
                                        if ($arr_items['products']['spec_desc']['spec_value_id'])
                                        {
                                            foreach ($arr_items['products']['spec_desc']['spec_value_id'] as $str_spec_value_id)
                                            {
                                                $arr_spec_value = $obj_spec_values->dump($str_spec_value_id);
                                                $arr_specification = $obj_specification->dump($arr_spec_value['spec_id']);
                                                
                                                $arr_data_item['goods_items'][$index]['attr'] = $arr_specification['spec_name'] . $this->app->_(":") . $arr_spec_value['spec_value'] . " ";
                                            }
                                        }
                                    }
                                    
                                    $index++;
                                }
                                elseif ($arr_items['item_type'] == 'adjunct')
                                {
                                    if (!$arr_data_item['goods_items'][$index-1]['adjname'])
                                        $arr_data_item['goods_items'][$index-1]['adjname'] .= "(";
                                        
                                    $arr_data_item['goods_items'][$index-1]['adjname'] .= $arr_items['name'] . ",";
                                }
                                else
                                {
                                    // product gift.
                                    if (!$arr_data_item['goods_items'][$index-1]['giftname'])
                                        $arr_data_item['goods_items'][$index-1]['giftname'] .= "(";
                                        
                                    $arr_data_item['goods_items'][$index-1]['giftname'] .= $arr_items['name'] . ",";
                                }
                            }
                            
                            if (isset($arr_data_item['goods_items'][0]['attr']) && $arr_data_item['goods_items'][0]['attr'])
                            {
                                if (strpos($arr_data_item['goods_items'][0]['attr'], " ") !== false)
                                {
                                    $arr_data_item['goods_items'][0]['attr'] = substr($arr_data_item['goods_items'][0]['attr'], 0, strrpos($arr_data_item['goods_items'][0]['attr'], " "));
                                }
                            }
                            
                            if (isset($arr_data_item['goods_items'][0]['adjname']) && $arr_data_item['goods_items'][0]['adjname'])
                            {
                                if (strpos($arr_data_item['goods_items'][0]['adjname'], ",") !== false)
                                {
                                    $arr_data_item['goods_items'][0]['adjname'] = substr($arr_data_item['goods_items'][0]['adjname'], 0, strrpos($arr_data_item['goods_items'][0]['adjname'], ","));
                                    $arr_data_item['goods_items'][0]['adjname'] .= ")";
                                }
                            }
                            
                            if (isset($arr_data_item['goods_items'][0]['giftname']) && $arr_data_item['goods_items'][0]['giftname'])
                            {
                                if (strpos($arr_data_item['goods_items'][0]['giftname'], ",") !== false)
                                {
                                    $arr_data_item['goods_items'][0]['giftname'] = substr($arr_data_item['goods_items'][0]['giftname'], 0, strrpos($arr_data_item['goods_items'][0]['giftname'], ","));
                                    $arr_data_item['goods_items'][0]['giftname'] .= ")";
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Generate the order detail
     * @params string order_id
     * @return null
     */
    public function orderdetail($order_id=0)
    {
        if (!isset($order_id) || !$order_id)
        {
            $this->begin(array('app' => 'b2c','ctl' => 'site_member', 'act'=>'index'));
            $this->end(false, __('订单编号不能为空！'));
        }
        
        $objOrder = &$this->app->model('orders');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))), 'order_pmt'=>array('*'));
        $sdf_order = $objOrder->dump($order_id, '*', $subsdf);
        $objMath = kernel::single("ectools_math");

        if(!$sdf_order||$this->app->member_id!=$sdf_order['member_id']){
            app::get('site')->controller('default')->page404();
            exit;
        }
        if($sdf_order['member_id']){
            $member = &$this->app->model('members');
            $aMember = $member->dump($sdf_order['member_id'], 'email');
            $sdf_order['receiver']['email'] = $aMember['email'];
        }
        /*if ($sdf_order['pay_extend']){
            $payment=$this->app->model('trading/payment');
            $sdf_order['extendCon'] = $payment->getExtendCon($sdf_order['pay_extend'],$sdf_order['payment']);
        }*/
        $this->pagedata['order'] = $sdf_order;
        
        $order_items = array();
        $gift_items = array();
        if ($sdf_order['order_objects'])
        {
            $app_gift = app::get('gift');
            $gift_is_installed = false;
            if ($app_gift->is_installed())
            {
                $gift_is_installed = true;
                $objGiftGoods = $app_gift->model('goods');
            }
            $objGoods = $this->app->model('goods');
            
            foreach ($sdf_order['order_objects'] as $k=>$v)
            {
                //$order_items = array_merge($order_items,$v['order_items']);
                $index = 0;
                $index_adj = 0;
                $index_gift = 0;
                if ($v['obj_type'] == 'goods')
                {
                    foreach($v['order_items'] as $key => $item)
                    {     
                        $arrGoods = $objGoods->dump($item['goods_id'], 'goods_id,cat_id,score,price,name,udfimg,thumbnail_pic,small_pic,big_pic,image_default_id');
                        $objGoodsCat = $this->app->model('goods_cat');
                        $arrGoodsCat = $objGoodsCat->dump($arrGoods['category']['cat_id'], 'cat_name');           
                        if ($item['item_type'] != 'gift')
                        {
                            $gItems[$k]['addon'] = unserialize($item['addon']);
                            if($item['minfo'] && unserialize($item['minfo'])){
                                $gItems[$k]['minfo'] = unserialize($item['minfo']);
                            }else{
                                $gItems[$k]['minfo'] = array();
                            }
                            
                            if ($item['item_type'] == 'product')
                            {  
                                $order_items[$k] = $item;
                                $order_items[$k]['thumbnail_pic'] = $arrGoods['image_default_id'];
                                $order_items[$k]['is_type'] = $v['obj_type'];
                                $order_items[$k]['item_type'] = $arrGoodsCat['cat_name'];
                                
                                if (isset($item['products']['spec_info']) && $item['products']['spec_info'])
                                {
                                    $order_items[$k]['name'] = $item['products']['name'] . '(' . $item['products']['spec_info'] . ')';
                                }
                                else
                                {
                                    $order_items[$k]['name'] = $item['products']['name'];
                                }
                            }
                            else
                            {
                                $order_items[$k]['adjunct'][$index_adj] = $item;
                                $order_items[$k]['adjunct'][$index_adj]['thumbnail_pic'] = $arrGoods['image_default_id'];
                                $order_items[$k]['adjunct'][$index_adj]['is_type'] = $v['obj_type'];
                                $order_items[$k]['adjunct'][$index_adj]['item_type'] = $arrGoodsCat['cat_name'];
                                
                                if (isset($item['products']['spec_info']) && $item['products']['spec_info'])
                                {
                                    $order_items[$k]['adjunct'][$index_adj]['name'] = $item['products']['name'] . '(' . $item['products']['spec_info'] . ')';
                                }
                                else
                                    $order_items[$k]['adjunct'][$index_adj]['name'] = $item['products']['name'];
                                
                                $index_adj++;
                            }
                        }
                        else
                        {
                            if ($gift_is_installed)
                            {
                                $arrGoods = $objGiftGoods->dump($item['goods_id'], '*');
                                
                                $order_items[$k]['gifts'][$index_gift] = $item;
                                $order_items[$k]['gifts'][$index_gift]['thumbnail_pic'] = $arrGoods['image_default_id'];
                                $order_items[$k]['gifts'][$index_gift]['is_type'] = $v['obj_type'];
                                $order_items[$k]['gifts'][$index_gift]['item_type'] = $arrGoods['category']['cat_name'];
                                
                                if (isset($item['products']['spec_info']) && $item['products']['spec_info'])
                                {
                                    $order_items[$k]['gifts'][$index_gift]['name'] = $item['products']['name'] . '(' . $item['products']['spec_info'] . ')';
                                }
                                else
                                    $order_items[$k]['gifts'][$index_gift]['name'] = $item['name'];
                                    
                                $index_gift++;
                            }
                        }
                    }
                }
                else
                {
                    if ($gift_is_installed)
                    {
                        foreach ($v['order_items'] as $gift_key => $gift_item)
                        {
                            if (isset($gift_items[$gift_item['goods_id']]) && $gift_items[$gift_item['goods_id']])
                                $gift_items[$gift_item['goods_id']]['nums'] = $objMath->number_plus(array($gift_items[$gift_item['goods_id']]['nums'], $gift_item['quantity']));
                            else
                            {
                                $arrGoods = $objGiftGoods->dump($gift_item['goods_id'], '*');
                                
                                $gift_items[$gift_item['goods_id']] = array(
                                    'goods_id' => $gift_item['goods_id'],
                                    'bn' => $gift_item['bn'],
                                    'nums' => $gift_item['quantity'],
                                    'name' => $gift_item['name'],
                                    'item_type' => $arrGoods['category']['cat_name'],
                                    'price' => $gift_item['price'],
                                    'quantity' => $gift_item['quantity'],
                                    'sendnum' => $gift_item['sendnum'],
                                    'thumbnail_pic' => $arrGoods['image_default_id'],
                                    'is_type' => $v['obj_type'],
                                    'amount' => $gift_item['amount'],
                                );
                            }
                        }
                    }
                }
            }
        }
        
        // 得到订单留言.
        $this->pagedata['order']['items'] = $order_items;
        $this->pagedata['order']['gift'] = $gift_items;
        $oMsg = &kernel::single("b2c_message_order");
        $arrOrderMsg = $oMsg->getList('*', array('order_id' => $order_id, 'object_type' => 'order'), $offset=0, $limit=-1, 'time DESC');
        
        $this->pagedata['ordermsg'] = $arrOrderMsg;
        $this->pagedata['res_url'] = $this->app->res_url;
        
        // 生成订单日志明细
        $oLogs =&$this->app->model('order_log');
        $arr_order_logs = $oLogs->getList('*', array('rel_id' => $order_id));
        
        // 支付方式的解析变化
        if ($this->pagedata['order']['payinfo']['pay_app_id'] != '货到付款')
        {
            $obj_payments_cfgs = app::get('ectools')->model('payment_cfgs');
            $arr_payments_cfg = $obj_payments_cfgs->getPaymentInfo($this->pagedata['order']['payinfo']['pay_app_id']); 
            $this->pagedata['order']['payinfo']['pay_app_id'] = $arr_payments_cfg['app_display_name'];
        }
        
        $this->pagedata['orderlogs'] = $arr_order_logs;
        
        $this->output();
    }
    
    /**
     * 会员中心订单提交页面
     * @params string order id
     * @params boolean 支付方式的选择
     */
    public function orderPayments($order_id, $selecttype=false)
    {
        $objOrder = &$this->app->model('orders');
        $sdf = $objOrder->dump($order_id);
        $objMath = kernel::single("ectools_math");
        if(!$sdf){
            exit;
        }
        $sdf['cur_amount'] = $objMath->number_minus(array($sdf['cur_amount'], $sdf['payed']));
        $sdf['total_amount'] = $objMath->number_multiple(array($sdf['cur_amount'], $sdf['cur_rate']));

        $this->pagedata['order'] = $sdf;

        if($selecttype){
            $selecttype = 1;
        }else{
            $selecttype = 0;
        }
        $this->pagedata['order']['selecttype'] = $selecttype;

        //        $objCur = app::get('ectools')->model('currency');
        //        $aCur = $objCur->getDefault();
        $opayment = app::get('ectools')->model('payment_cfgs');
        $this->pagedata['payments'] = $opayment->getList('*', array('status' => 'true'));
        
        $system_money_decimals = $this->app->getConf('system.money.decimals');
        $system_money_operation_carryset = $this->app->getConf('system.money.operation.carryset');
        foreach ($this->pagedata['payments'] as $arrPayments)
        {
            if (!$sdf['member_id'])
            {
                if (trim($arrPayments['app_id']) == 'deposit')
                {
                    unset($this->pagedata['payments'][$key]);
                    continue;
                }
            }
            
            if ($arrPayments['app_id'] == $this->pagedata['order']['payinfo']['pay_app_id'])
            {
                $this->pagedata['order']['payinfo']['pay_name'] = $arrPayments['app_name'];
                $arrPayments['cur_money'] = $objMath->formatNumber($this->pagedata['order']['cur_amount'], $system_money_decimals, $system_money_operation_carryset);
                $arrPayments['total_amount'] = $objMath->formatNumber($this->pagedata['order']['total_amount'], $system_money_decimals, $system_money_operation_carryset);
            }
            else
            {
                $arrPayments['cur_money'] = $objMath->number_minus(array($this->pagedata['order']['cur_amount'], $this->pagedata['order']['payed']));
                if ($this->pagedata['order']['payinfo']['cost_payment'] > 0)
                {
                    $cost_payments_rate = $objMath->number_div(array($arrPayments['cur_money'], $this->pagedata['order']['cur_amount']));
                    $cost_payment = $objMath->number_multiple(array($this->pagedata['order']['payinfo']['cost_payment'], $cost_payments_rate));
                    $arrPayments['cur_money'] = $objMath->number_minus(array($arrPayments['cur_money'], $cost_payment));
                    $arrPayments['cur_money'] = $objMath->number_plus(array($arrPayments['cur_money'], $objMath->number_multiple(array($arrPayments['cur_money'], $arrPayments['pay_fee']))));
                }
                else
                {
                    $cost_payment = $objMath->number_multiple(array($arrPayments['cur_money'], $arrPayments['pay_fee']));
                    $arrPayments['cur_money'] = $objMath->number_plus(array($arrPayments['cur_money'], $cost_payment));
                }
                
                $arrPayments['total_amount'] = $objMath->formatNumber($objMath->number_multiple(array($arrPayments['cur_money'], $this->pagedata['order']['cur_rate'])), $system_money_decimals, $system_money_operation_carryset);
                $arrPayments['cur_money'] = $objMath->formatNumber($arrPayments['cur_money'], $system_money_decimals, $system_money_operation_carryset);
            }
        }
        
        if ($this->pagedata['order']['payinfo']['pay_app_id'] == '货到付款')
        {
            $this->pagedata['order']['payinfo']['pay_app_id'] = '-1';
            $this->pagedata['order']['payinfo']['pay_name'] = '货到付款';
        }
        
        $objCur = app::get('ectools')->model('currency');
        $aCur = $objCur->getFormat($this->pagedata['order']['currency']);
        $this->pagedata['order']['cur_def'] = $aCur['sign'];
        
        $this->pagedata['return_url'] = $this->gen_url(array('app'=>'b2c','ctl'=>'site_paycenter','act'=>'result'));
        $this->pagedata['res_url'] = $this->app->res_url;
        
        $this->output();
    }

    function deposit(){
        
        $oCur = app::get('ectools')->model('currency');
        $currency = $oCur->getDefault();
        $this->pagedata['currencys'] = $currency;
        $this->pagedata['currency'] = $currency['cur_code'];
        $opay = app::get('ectools')->model('payment_cfgs');
       // $this->pagedata['payment_html'] = $opay->callfunc('select_pay_method',$this);
        $aOld = $opay->getList('*', array('status' => 'true', 'is_frontend' => true));
        $aData = array();
        foreach($aOld as $val){
        if(($val['app_id']!='deposit') && ($val['app_id']!='offline'))$aData[] = $val;
        }
        $this->pagedata['payments'] = $aData;
        $this->pagedata['member_id'] = $this->app->member_id;
        $this->pagedata['return_url'] = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'balance'));

        $this->output();
    }

   public function balance($nPage=1)
   {        
        $member = &$this->app->model('members');
        $mem_adv = &$this->app->model('member_advance');
        $items_adv = $mem_adv->get_list_bymemId($this->app->member_id);
        $count = count($items_adv);
        $aPage = $this->get_start($nPage,$count);
        $params['data'] = $mem_adv->getList('*',array('member_id' => $this->member['member_id']),$aPage['start'],$this->pagesize);
        $params['page'] = $aPage['maxPage'];
        $this->pagination($nPage,$params['page'],'balance');
        $this->pagedata['advlogs'] = $params['data'];
        $data = $member->dump($this->app->member_id,'advance');
        $this->pagedata['total'] = $data['advance']['total'];
        // errorMsg parse.
        $this->pagedata['errorMsg'] = json_decode($_GET['errorMsg']);
        $this->output();
    }


    function pointHistory($nPage=1) {
        $userId = $this->app->member_id;
        $oPointHistory = &$this->app->model('point_history');
        $obj_memberberPoint = &$this->app->model('trading/memberPoint');
        $this->pagedata['historys'] = $aData['data'];
        $this->pagination($nPage,$aData['page'],'pointHistory');
        $this->output();
    }

    function favorite($nPage=1){
        $obj_member = &$this->app->model('member_goods');
        $objProduct = $this->app->model('products');
        $oGoodsLv = &$this->app->model('goods_lv_price');
        $oMlv = &$this->app->model('member_lv');
        $mlv = $oMlv->db_dump( $this->member['member_lv'],'dis_count' );
        $aData = $obj_member->get_favorite($this->app->member_id,$nPage);
        $aProduct = $aData['data'];
        if($aProduct){
            foreach ($aProduct as &$val) {
            $temp = $objProduct->getList('product_id, spec_info, price, freez, store, goods_id',array('goods_id'=>$val['goods_id']));
            if( $this->member['member_lv'] ){
                $tmpGoods = array();
                foreach( $oGoodsLv->getList( 'product_id,price',array('goods_id'=>$val['goods_id'],'level_id'=> $this->member['member_lv'] ) ) as $k => $v ){
                    $tmpGoods[$v['product_id']] = $v['price'];
                }
                foreach( $temp as &$tv ){
                    $tv['price'] = (isset( $tmpGoods[$tv['product_id']] )?$tmpGoods[$tv['product_id']]:( $mlv['dis_count']*$tv['price'] ));
                }
                $val['price'] = $tv['price'];
            }
            $val['spec_desc_info'] = $temp;
            }
    }
        $this->pagedata['favorite'] = $aProduct;
        
        $this->pagination($nPage,$aData['page'],'favorite');
        $imageDefault = app::get('image')->getConf('image.set');
        $this->pagedata['defaultImage'] = $imageDefault['S']['default_image'];
        $setting['buytarget'] = $this->app->getConf('site.buy.target');
        $this->pagedata['setting'] = $setting;
        $this->output();
    }

    function index() {
        $oMem = &$this->app->model('members');
        $oRder = &$this->app->model('orders');
        $order = $oRder->getList('*',array('member_id' => $this->app->member_id));
        $order_total = count($order);
        $aInfo = $oMem->dump($this->app->member_id);
        $order = &$this->app->model('orders');
        $aData = $order->fetchByMember($this->app->member_id,$nPage-1);
        $this->get_order_details($aData);
        #获取咨询评论回复
        $obj_mem_msg = kernel::single('b2c_message_disask');
        $this->member['unreadmsg'] = $obj_mem_msg->calc_unread_disask($this->member['member_id']);
        $this->pagedata['orders'] = $aData['data'];
        $this->pagedata['pager'] = $aData['pager'];
        $this->pagedata['member'] = $this->member;
        $this->pagedata['total_order'] = $order_total;
        $this->pagedata['aNum']=$aInfo['advance']['total'];$this->set_tmpl('member');
        $obj_member = &$this->app->model('member_goods');
        $aData_fav = $obj_member->get_favorite($this->app->member_id);
        $this->pagedata['favorite'] = $aData_fav['data'];
        $imageDefault = app::get('image')->getConf('image.set');
        $this->pagedata['defaultImage'] = $imageDefault['S']['default_image'];
        $rule = kernel::single('b2c_member_solution');
        $this->pagedata['wel'] = $rule->get_all_to_array($this->member['member_lv']);
        $this->pagedata['res_url'] = $this->app->res_url;
        $this->output();
    }

    function del_fav($nGid,$delAll=false){
        $obj_member = &$this->app->model('members');
        if($delAll){
            $obj_member->delAllFav($this->app->member_id);
        }else{
            if($obj_member->delFav($this->app->member_id,$nGid)){
                $this->redirect(array('app'=>'b2c','ctl'=>'site_member','act'=>'favorite'));
            }else{
                echo __('删除失败！');
            }
        }
        $this->output();
    }

    function ajax_add_fav($nGid){
        if(!$this->app->member_id){
            $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'index'));
            #echo $url;
        }
        if($nGid){
            $obj_member = &$this->app->model('member_goods');
            $obj_member->add_fav($this->app->member_id,$nGid);
        }
    }

    function ajax_del_fav($nGid=null,$delAll=false){
        if(!$this->app->member_id){
            $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'index'));
            #echo $url;
        }
        if(!$delAll){
            if($nGid){
                $obj_member = &$this->app->model('member_goods');
                $obj_member->delFav($this->app->member_id,$nGid);
            }
        }else{
            $obj_member->delAllFav($this->app->member_id);
        }
    }

    //收件箱
    function inbox($nPage=1) {
        $oMsg = kernel::single('b2c_message_msg');
        $row = $oMsg->getList('*',array('to_id' => $this->app->member_id,'has_sent' => 'true','for_comment_id' => 'all'));
        $aData['data'] = $row;
        $aData['total'] = count($row);
        $count = count($row);
        $aPage = $this->get_start($nPage,$count);
        $params['data'] = $oMsg->getList('*',array('to_id' => $this->app->member_id,'has_sent' => 'true','for_comment_id' => 'all'),$aPage['start'],$this->pagesize);
        $params['page'] = $aPage['maxPage'];
        $this->pagedata['message'] = $params['data'];
        $this->pagedata['total_msg'] = $aData['total'];
        $this->pagination($nPage,$params['page'],'inbox');
        $this->output();
    }

    //草稿箱
    function outbox($nPage=1) {
        $oMsg = kernel::single('b2c_message_msg');
        $row = $oMsg->getList('*',array('has_sent' => 'false','author_id' => $this->app->member_id));
        $aData['data'] = $row;
        $aData['total'] = count($row);
        $count = count($row);
        $aPage = $this->get_start($nPage,$count);
        $params['data'] = $oMsg->getList('*',array('has_sent' => 'false','author_id' => $this->app->member_id),$aPage['start'],$this->pagesize);
        $params['page'] = $aPage['maxPage'];
        $this->pagedata['message'] = $params['data'];
        $this->pagedata['total_msg'] = $aData['total'];
        $this->pagination($nPage,$params['page'],'outbox');
        $this->output();
    }

    //已发送
    function track($nPage=1) {
        $oMsg = kernel::single('b2c_message_msg');
        $row = $oMsg->getList('*',array('author_id' => $this->app->member_id,'has_sent' => 'true'));
        $aData['data'] = $row;
        $aData['total'] = count($row);
        $count = count($row);
        $aPage = $this->get_start($nPage,$count);
        $params['data'] = $oMsg->getList('*',array('author_id' => $this->app->member_id,'has_sent' => 'true'),$aPage['start'],$this->pagesize);
        $params['page'] = $aPage['maxPage'];
        $this->pagedata['message'] = $params['data'];
        $this->pagedata['total_msg'] = $aData['total'];
        $this->pagination($nPage,$params['page'],'track');
        $this->output();
    }

    function view_msg($nMsgId){
        $objMsg = kernel::single('b2c_message_msg');
        $aMsg = $objMsg->getList('comment',array('comment_id' => $nMsgId));
        $objMsg->setReaded($nMsgId);
        echo  $aMsg[0]['comment'];
        
    }
    
    function viewMsg($nMsgId){
        $objMsg = kernel::single('b2c_message_msg');
        $objMsg->type = 'msg';
        $aMsg = $objMsg->getList('comment',array('comment_id' => $nMsgId));
        echo $aMsg[0]['comment'];
    
    }

    function del_in_box_msg(){
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'inbox'));
        if(!empty($_POST['delete'])){
            $objMsg = kernel::single('b2c_message_msg');
            $objMsg->delete(array('object_type' => 'msg','comment_id' => $_POST['delete']));
            #$objMsg->del_inbox_msg($_POST['delete']);
            $this->splash('success',$url,__('删除成功！'));
        }else{
             $this->splash('failed',$url,__('删除失败: 没有选中任何记录！！'));
        }
    }

    function del_track_msg() {
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'track'));
        if(!empty($_POST['deltrack'])){
            $objMsg = kernel::single('b2c_message_msg');
            $objMsg->delete(array('object_type' => 'msg','comment_id' => $_POST['deltrack']));
            $this->splash('success',$url,__('删除成功！'));
        }else{
            $this->splash('failed',$url,__('删除失败: 没有选中任何记录！！'));
        }
    }

    function del_out_box_msg() {
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'outbox'));
        if(!empty($_POST['deloutbox'])){
            $objMsg = kernel::single('b2c_message_msg');
            $objMsg->delete(array('object_type' => 'msg','comment_id' =>$_POST['deloutbox']));
            $this->splash('success',$url,__('删除成功！'));
        }else{
            $this->splash('failed',$url,__('删除失败: 没有选中任何记录！！'));
        }
    }

    function send($nMsgId=false,$type='') {
        if($nMsgId){
            $objMsg = kernel::single('b2c_message_msg');
            $init =  $objMsg->dump($nMsgId);
            if($type == 'reply'){
                $objMsg->setReaded($nMsgId);
                $init['to_uname'] = $init['author'];
                $init['subject'] = "Re:".$init['title']; 
                $init['comment'] = '';     
                $this->pagedata['is_reply'] = true;    
            }
            else{
                $init['subject'] = $init['title']; 
            }
            $this->pagedata['init'] = $init;
            $this->pagedata['comment_id'] = $nMsgId;
        }

        $this->output();
    }

    function message($nMsgId=false, $status='send') { //给管理员发信件
        if($nMsgId){
            $objMsg = kernel::single('b2c_message_msg');
            $init =  $objMsg->dump($nMsgId);
            $this->pagedata['init'] = $init;
            $this->pagedata['msg_id'] = $nMsgId;
        }
        if($status === 'reply'){
            $this->pagedata['reply'] = 1;
        }
        $this->output();
    }

    function msgtoadmin(){
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'send'));
        $_POST['msg_to'] = 0;
        if($_POST['subject'] && $_POST['comment']) {
            if(1){
                $objMessage = kernel::single('b2c_message_msg');
                $_POST['has_sent'] = $_POST['has_sent'] == 'false' ? 'false' : 'true';
                $_POST['member_id'] = $this->app->member_id;
                $_POST['uname'] = $this->member[uname];
                $_POST['to_type'] = 'admin';
                $_POST['contact'] = $this->member['email'];
                $_POST['ip'] = $_SERVER["REMOTE_ADDR"];
                $_POST['has_sent'] = $_POST['has_sent'] == 'false' ? 'false' : 'true';
                if( $objMessage->send($_POST) ) {
                if($_POST['has_sent'] == 'false'){
                    $this->splash('success',$url,__('保存到草稿箱成功！'));
                }else{
                    $this->splash('success',$url,__('发送成功！'));
                }
                } else {
                $this->splash('failed',$url,__('发送失败！！'));              
                }
            } 
            else {
                $this->splash('failed',$url,__('找不到你填写的用户！！')); 
            }
        } 
        else {
            $this->splash('failed',$url,__('必填项不能为空！！'));       
        }     
    }

    function send_msg(){
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'send'));
        if($_POST['msg_to'] && $_POST['subject'] && $_POST['comment']) {
            $obj_member = &$this->app->model('members');
            //var_dump($_POST);exit;
            if($to_id = $obj_member->get_id_by_uname($_POST['msg_to'])){
                $objMessage = kernel::single('b2c_message_msg');
                $_POST['member_id'] = $this->app->member_id;
                $_POST['uname'] = $this->member[uname];
                $_POST['has_sent'] = $_POST['has_sent'] == 'false' ? 'false' : 'true';
                $_POST['to_id'] = $to_id;
                if($_POST['comment_id']){
                    $data['comment_id'] = $_POST['comment_id'];
                }
    
                if( $objMessage->send($_POST) ) {
                    if($_POST['has_sent'] == 'false'){
                         $this->splash('success',$url,__('保存到草稿箱成功！！')); 
                    }else{
                         $this->splash('success',$url,__('发送成功！！')); 
                    }
                 } else {
                     $this->splash('failed',$url,__('发送失败！！'));            
                 }
            } else {
                $this->splash('failed',$url,__('找不到你填写的用户！！'));  
            }
        } else {
               $this->splash('failed',$url,__('必填项不能为空！！')); 
           
        }
    }

    function security($type = ''){
        //$passport = &$this->app->model('passport');
        $obj_member = &$this->app->model('members');
        $this->pagedata['mem'] = $obj_member->dump($this->app->member_id);
        $this->pagedata['type'] = $type;
        $this->output();
    }

    function save_security(){
       $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'security'));
        $obj_member = &$this->app->model('members');
        $result = $obj_member->save_security($this->app->member_id,$_POST,$msg);
        if($result){
            $data['member_id'] = $this->app->member_id;
            $data['uname'] = $this->member['uname'];
            $data['passwd'] = $_POST['passwd_re'];
            $data['email'] = $this->member['email'];
            $obj_account = $this->app->model('member_account');
            $obj_account->fireEvent('chgpass',$data,$this->app->member_id);
        $this->splash('success',$url,$msg); 
        }
        else{
             $this->splash('failed',$url,$msg); 
        }
    }

    function save_security_issue(){
       $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'security'));
        $obj_member = &$this->app->model('members');
       // $this->end($obj_member->saveSecurity($this->app->member_id,$_POST),__('安全问题修改成功'));
       if($obj_member->save_security($this->app->member_id,$_POST,$msg)){
           $this->splash('success',$url,$msg); 
       }
       else{
           $this->splash('failed',$url,$msg); 
       }
    }

    function receiver(){
        $objMem = &$this->app->model('members');
        $this->pagedata['receiver'] = $objMem->getMemberAddr($this->app->member_id);
        $this->pagedata['is_allow'] = (count($this->pagedata['receiver'])<5 ? 1 : 0);
        $this->output();
    }

    //添加收货地址
    function add_receiver(){
        $obj_member = &$this->app->model('members');
        if($obj_member->isAllowAddr($this->app->member_id)){
            $this->output();
        }else{
            echo __('不能新增收货地址');
        }
    }

    function insert_rec(){
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'receiver'));
        $obj_member = &$this->app->model('members');
        if(!$obj_member->isAllowAddr($this->app->member_id)){
             $this->splash('failed',$url,__('不能新增收货地址')); 
        }
        $aData = $this->check_input($_POST);
        if($obj_member->insertRec($aData,$this->app->member_id,$message)){
             $this->splash('success',$url,$message); 
            }
        else{
            $this->splash('failed',$url,$message); 
        }
        
    }

    //设置和取消默认地址，$disabled 2为设置默认1为取消默认
    function set_default($addrId,$disabled){
      $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'receiver'));
        $obj_member = &$this->app->model('members');
        $member_id = $this->app->member_id;
        if($obj_member->set_to_def($addrId,$member_id,$message,$disabled)){
        $this->splash('success',$url,$message);
    }
    else{
        $this->splash('failed',$url,$message); 
    }
    }
    //修改收货地址
    function modify_receiver($addrId){
        $obj_member = &$this->app->model('members');
        if($aRet = $obj_member->getAddrById($addrId)){
            $aRet['defOpt'] = array('0'=>__('否'), '1'=>__('是'));
             $this->pagedata = $aRet;
        }else{
            $this->_response->set_http_response_code(404);
            exit;
        }

        $this->output();
    }

    function save_rec(){
       $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'receiver'));
        $obj_member = &$this->app->model('members');
        $aData = $this->check_input($_POST);
        if($obj_member->save_rec($aData,$this->app->member_id,$message)){
          $this->splash('success',$url,$message);
        }
       else{
            $this->splash('failed',$url,$message); 
       }
    }

    //删除收货地址
    function del_rec($addrId){
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'receiver'));
        $obj_member = &$this->app->model('members');
        if($obj_member->del_rec($addrId,$message)){
           $this->splash('success',$url,$message);
        }
        else{
          $this->splash('failed',$url,$message);   
        }
       
    }

    function score(){
        $this->output();
    }





    function exchange($cpnsId) {
        $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'couponExchange'));
        $oExchangeCoupon = kernel::single('b2c_coupon_mem');
        $memberId = intval($this->app->member_id);//会员id号
        if ($memberId){
            if ($oExchangeCoupon->exchange($cpnsId,$memberId,$this->member['point'])){
                $o = $this->app->model('coupons');
                $member_point = $this->app->model('member_point');
                $arr = $o->dump($cpnsId);
                $cpns_point = $arr['cpns_point'];
                if($member_point->change_point($this->member['member_id'],-$cpns_point,$msg,'exchange_coupon',2)){
                    $this->splash('success',$url,__('兑换成功'));
                }
                else{
                    $this->splash('failed',$url,$msg);  
                }
                   
            }
            }
        else {
                rigger_error(__('没有登录'),E_USER_ERROR);
            }
        $this->splash('failed',$url,__('兑换失败,原因:积分不足/兑换购物券无效...'));   
     }

    function download_ddvanceLog(){
        $charset = kernel::single('ectools_charset_utfconvert');
        $obj_member = &$this->app->model('member_advance');
        $aData = $obj_member->get_list_bymemId($this->app->member_id);
        header('Pragma: no-cache, no-store');
        header("Expires: Wed, 26 Feb 1997 08:21:57 GMT");
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=advance_".date("Ymd").".csv");
        $out = __("事件,存入金额,支出金额,当前余额,时间\n");
        foreach($aData as $v){
            $out .= $v['message'].",".$v['import_money'].",".$v['explode_money'].",".$v['member_advance'].",".date("Y-m-d H:m",$v['mtime'])."\n";
        }
        echo $charset->utf2local($out,'zh');
        exit;
    }
    
    /**
     * 添加留言
     * @params string order_id 
     * @params string message type
     */
    public function add_order_msg( $order_id , $msgType = 0 ){
        $objOrder = $this->app->model('orders');
        $aOrder = $objOrder->dump($order_id );
        
        $timeHours = array();
        for($i=0;$i<24;$i++){
            $v = ($i<10)?'0'.$i:$i;
            $timeHours[$v] = $v;
        }
        $timeMins = array();
        for($i=0;$i<60;$i++){
            $v = ($i<10)?'0'.$i:$i;
            $timeMins[$v] = $v;
        }
        $this->pagedata['orderId'] = $order_id;
        $this->pagedata['msgType'] = $msgType;
        $this->pagedata['timeHours'] = $timeHours;
        $this->pagedata['timeMins'] = $timeMins;
        
        $this->output();
    }
    
    /**
     * 订单留言提交
     * @params null
     * @return null
     */
    public function toadd_order_msg()
    {        
        $this->begin($this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'orderdetail','arg' => $_POST['msg']['orderid'])));
        
        $obj_filter = kernel::single('b2c_site_filter');
        $_POST = $obj_filter->check_input($_POST);

        $_POST['to_type'] = 'admin';
        $_POST['author_id'] = $this->member['member_id'];
        $_POST['author'] = $this->member['uname'];
        $is_save = true;
        
        //$obj_api_order = kernel::service("api.b2c.order");
        $obj_order_message = kernel::single("b2c_order_message");
        if ($obj_order_message->create($_POST))
            $this->end(true,__('留言成功!'));
        else 
            $this->end(false,__('留言失败!'));
    }
    
    
    function point_history($nPage=1){

        $member = $this->app->model('members');
        $member_point = $this->app->model('member_point');
        $data = $member->dump($this->app->member_id,'*',array('score/event'=>array('*')));
        $count = count($data['score/event']);
        $aPage = $this->get_start($nPage,$count);
        $params['data'] = $member_point->getList('*',array('member_id' => $this->member['member_id']),$aPage['start'],$this->pagesize);
        $params['page'] = $aPage['maxPage'];
        $this->pagination($nPage,$params['page'],'point_history');
        $this->pagedata['total'] = $data['score']['total'];
        $this->pagedata['historys'] = $params['data'];
        
        $this->output();
    }
   
    function attr_page(){
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
        }
        $this->pagedata['attr'] = $attr;
        $this->page("site/member/attr.html");
    }
    /*
        过滤POST来的数据,基于安全考虑,会把POST数组中带HTML标签的字符过滤掉
    */
    function check_input($data){
        $aData = $this->arrContentReplace($data);
        return $aData;
    }
   
    function arrContentReplace($array){
        if (is_array($array)){
            foreach($array as $key=>$v){
                $array[$key] =     $this->arrContentReplace($array[$key]);
            }
        }
        else{
            $array = strip_tags($array);
        }
        return $array;
    }
  
    function save_attr(){
        $member_model = &$this->app->model('members');
        $_POST['pam_account']['account_id'] = $this->member['member_id'];
        if(!$_POST['profile']['birthday']) unset($_POST['profile']['birthday']);
        foreach($_POST as $key=>$val){
            if(strpos($key,"box:") !== false){
                $aTmp = explode("box:",$key);
                $_POST[$aTmp[1]] = serialize($val);
            }
        }
        
         if($_POST['contact']['name']&&!preg_match('/^([@\.]|[^\x00-\x2f^\x3a-\x40]){2,20}$/i', $_POST['contact']['name'])){
            $this->splash('failed',$this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'attr_page')),__('姓名包含非法字符'));
        }
        $_POST = $this->check_input($_POST);
        if($member_model->save($_POST)){
            $this->splash('success',$this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'index')),__('会员信息更新成功'));
        }
        $this->splash('failed',$this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'attr_page')),__('会员信息更新失败'));
    }
    
    function comment($nPage=1){
        $comment = kernel::single('b2c_message_disask');
        $aData = $comment->get_member_comments($this->app->member_id,$nPage);
        $this->pagedata['commentList'] = $aData['data'];
        $this->pagination($nPage,$aData['page'],'comment');
        $this->output();
    }
    
     ##缺货登记
    function notify($nPage=1){
        
        $oMem = &$this->app->model('member_goods');
        $aData = $oMem->get_gnotify($this->app->member_id,$nPage);
        $this->pagedata['notify'] = $aData['data'];
        $this->pagination($nPage,$aData['page'],'notify');
        $setting['buytarget'] = $this->app->getConf('site.buy.target');
        $imageDefault = app::get('image')->getConf('image.set');
        $this->pagedata['defaultImage'] = $imageDefault['S']['default_image'];
        $this->pagedata['setting'] = $setting;
        $this->pagedata['member_id'] = $this->app->member_id;
        $this->output();
    }
    
    ##添加缺货登记
    
    function add_gnotify($gid,$pid){
        $member_good = $this->app->model('member_goods');
        if($member_good->add_gnotify($this->app->member_id,$gid,$pid,'sanow@126.com')){
            echo "成功";
        }
        else{
            echo "失败";
        }
    }
    
    ##删除缺货登记
    
    function del_notify($pid,$member_id){
         $url = $this->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'notify'));
        $member_goods= $this->app->model('member_goods');
        if($member_goods->delete(array('product_id'=>$pid,'member_id'=>$member_id))){
            $this->splash('success',$url,__('删除成功'));
        }
        else{
            $this->splash('failed',$url,__('删除失败: 没有选中任何记录！！'));
        }
    }
    
    function coupon($nPage=1) {
        $oCoupon = kernel::single('b2c_coupon_mem');
        $aData = $oCoupon->get_list_m($this->member['member_id']);
        if ($aData) {
            foreach ($aData as $k => $item) {
                if ($item['coupons_info']['cpns_status']==1) {
                    $member_lvs = explode(',',$item['time']['member_lv_ids']);
                    if (in_array($this->member['member_lv'],(array)$member_lvs)) {
                        $curTime = time();
                        if ($curTime>=$item['time']['from_time'] && $curTime<$item['time']['to_time']) {
                            if ($item['memc_used_times']<$this->app->getConf('coupon.mc.use_times')){
                                if ($item['coupons_info']['cpns_status']){
                                    $aData[$k]['memc_status'] = __('可使用'); 
                                }else{
                                    $aData[$k]['memc_status'] = __('本优惠券已作废');
                                }
                            }else{
                                $aData[$k]['memc_status'] = __('本优惠券次数已用完');
                            }
                        }else{
                            $aData[$k]['memc_status'] = __('还未开始或已过期');
                        }
                    }else{
                        $aData[$k]['memc_status'] = __('本级别不准使用');
                    }
                }else{
                    $aData[$k]['memc_status'] == __('此种优惠券已取消');
                }
            }
        }
        $this->pagedata['mc_use_times'] = $this->app->getConf('coupon.mc.use_times');
        $this->pagedata['coupons'] = $aData;
        #$this->pagination($nPage,$aData['page'],'coupon');
        $this->output();
    }
    function couponExchange($page=1) {
        $pageLimit = 10;
        $oExchangeCoupon = kernel::single('b2c_coupon_mem');
        $filter = array('ifvalid'=>1);
        if ($aExchange = $oExchangeCoupon->get_list()) {
            //$counter = $oExchangeCoupon->count($filter);
            $this->pagedata['couponList'] = $aExchange;
        }
       /*
        if (is_array($this->pagedata['couponList'])) {
            $coupon = &$this->system->loadModel('trading/coupon');
            foreach($this->pagedata['couponList'] as $key => $val){
                if ($coupon->isLevelAllowUse($val['pmt_id'],$GLOBALS['runtime']['member_lv'],$val['cpns_point'])){
                    $this->pagedata['couponList'][$key]['use_status'] = 1;
                }
                else{
                    $this->pagedata['couponList'][$key]['use_status'] = 0;
                }
            }
        }
        $this->pagedata['pager'] = array(
            'current'=>$page,
            'total'=>ceil($counter/$pageLimit),
            'link'=>$this->system->mkUrl('member','couponExchange',array($tmp = time())),
            'token'=>$tmp);*/
        $this->output();
    }
    
}
