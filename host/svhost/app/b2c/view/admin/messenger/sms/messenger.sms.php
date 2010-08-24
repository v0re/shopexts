<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class messenger_sms{

    var $name = '手机短信'; //名称
    var $iconclass="sysiconBtn sms"; //操作区图标
    var $name_show = '发短信'; //列表页操作区名称
    var $version='$ver$'; //版本
    var $updateUrl=false;  //新版本检查地址
    var $isHtml = false; //是否html消息
    var $hasTitle = false; //是否有标题
//    var $maxtitlelength =300; //最多字符
    var $maxtime = 300; //发送超时时间 ,单位:秒
    var $maxbodylength =300; //最多字符
    var $allowMultiTarget=false; //是否允许多目标
//  var $targetSplit = ','; //多目标分隔符
    var $withoutQueue = false;
    var $dataname='mobile';
    var $sms_service_ip='124.74.193.222';
    var $sms_service='http://idx.sms.shopex.cn/service.php';


    /**
     * send
     * 必有方法,发送时调用
     *
     * config参数为getOptions取得的所有项的配置结果
     *
     * @param mixed $to
     * @param mixed $message
     * @param mixed $config
     * @access public
     * @return void
     */
    function messenger_sms(){
      

}
}
?>
