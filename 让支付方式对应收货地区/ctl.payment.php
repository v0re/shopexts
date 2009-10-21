<?php
/**
 * ctl_payment
 *
 * @uses pageFactory
 * @package
 * @version $Id: ctl.payment.php 1867 2008-04-23 04:00:24Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @author Likunpeng <leoleegood@zovatech.com>
 * @license Commercial
 */
include_once('objectPage.php');
class ctl_payment extends objectPage {

    var $name='支付';
    var $workground ='setting';
    var $actionView = 'payment/finder_action.html';
    var $object = 'trading/paymentcfg';
    var $editMode = true;
    
    var $disableGridEditCols = "id";
    var $disableColumnEditCols = "id";
    var $disableGridShowCols = "id";
    /**
    * main
    *
    * @access public
    * @return void
    */
    
    
    function detail($id){
        $this->path[] = array('text'=>'友情链接编辑');
        $oPay = $this->system->loadModel('trading/payment');
        
        $aPay = $oPay->getPaymentById($id);
        $this->pagedata['pay'] = $aPay;
        #############################################
        $this->pagedata['pay_info'] = $this->_getPayOpt($aPay['pay_type'], $aPay['custom_name'], $aPay['fee'], $aPay['config'],$aPay['areaname_group']);

        $oPlu = $oPay->loadMethod($aPay['pay_type']);
        if($oPlu){
            $this->pagedata['html'] =  $oPlu->infoPad();
        }

        $this->pagedata['pay_id'] = $id;
        $this->pagedata['order'] = $aPay['orderlist'];
        $this->pagedata['old_pay_type'] = $aPay['pay_type'];
        $this->pagedata['pay_des'] = $aPay['des'];
        $this->pagedata['pay_name'] = $aPay['custom_name'];
        $this->pagedata['paylist'] = $oPay->getPluginsArr(true);
        $this->setView('payment/pay_edit.html');
        $this->output();
    }
    /**
    * main
    *
    * @access public
    * @return void
    */
    function getPayList(){
        $this->path[] = array('text'=>'支付方式');
        $oPay = $this->system->loadModel('trading/payment');
        $this->pagedata['items'] = $oPay->getMethods();
        $this->page('payment/pay_list.html');
    }
    
    function _getHtmlString($key,$val,$rs=array(),&$eventScripts){
        $sJS = '';
        switch($val['type']){
            case 'string':
                $sJS .= '<tr><th>'.$val['label'].'：</th><td><input type="text" name="'.$key.'"'.($rs[$key]?' value="'.$rs[$key].'"':'').' /></td></tr>';
            break;
            case 'select':
                $sJS .= '<tr><th>'.$val['label'].'：</th><td><select name="'.$key.'">';
                foreach($val['options'] as $k=>$v){

                    $sJS .= '<option value="'.$k.'" '.(($rs[$key]==$k)?'selected':'').'>'.$v.'</option>';
                }
                $sJS .= '</select></td></tr>';
            break;
            case 'number':
                $sJS .= '<tr><th>'.$val['label'].'：</th><td><input type="text" name="'.$key.'"'.($rs[$key]?',value="'.$rs[$key].'"':'').' /></td></tr>';
            break;
            case 'file':
                $sJS .= '<tr><th>'.$val['label'].'：</th><td><input type="file" name="'.$key.'" /></td></tr>';
            break;
            case 'radio':
                $sJS .='<tr><th>'.$val['label'].'：</th><td>';
                foreach($val['options'] as $k => $v){
                    $checked="";
                    if ($rs[$key]==$k)
                        $checked="checked";
                    if ($val['event'])
                        $sJS.="<input type=radio name=$key value=$k onclick='".$val['event']."(this);' ".$checked.">".$v;
                    else
                        $sJS.="<input type=radio name=$key value=$k ".$checked.">".$v;
                }
                $sJS.="</td></tr>";
                if ($val['extendcontent']){
                    foreach($val['extendcontent'] as $ck => $cv){
                        $scripts.="<script>";
                        if (isset($rs[$key])){
                           if (intval($rs[$key])>0)
                               $scripts.="$('".$cv['property']['extconId']."').show();";
                           else
                               $scripts.="$('".$cv['property']['extconId']."').hide();";
                        }else{
                            if ($cv['property']['display'])
                                $scripts.="$('".$cv['property']['extconId']."').show();";
                            else
                                $scripts.="$('".$cv['property']['extconId']."').hide();";
                        } 
                        $scripts.="</script>";
                        $sJS.="<tr id=".$cv['property']['extconId']."><th></th><td>";
                        $sJS.="<table>";
                        $i=0;
                        $type=$cv['property']['type'];
                        $name=$cv['property']['name'];
                        $size=$cv['property']['size']?$cv['property']['size']:4;
                        foreach($cv['value'] as $csk => $csv){ 
                            if ($i==0||$i%$size==0)
                                $sJS.="<tr>";
                            $sJS.="<td>";
                            $checked="";
                            if (!$rs)
                                $checked="checked";
                            if (in_array($csv['value'],$rs[$name]))
                                $checked="checked";
                            $sJS.="<input type=$type name=".$name."[] value=".$csv['value']." ".$checked.">";
                            $sJS.=$csv['imgname']?"<img src=".$this->system->base_url()."plugins/payment/images/".$csv['imgname'].">":$csv['name'];
                            $sJS.="</td>";
                            if ($i%$size==0&&$i==count($csv['value']))
                                $sJS.="</tr>";
                            $i++;
                        }
                        $sJS.="</table>";
                        $sJS.="</td></tr>";
                    }
                    
                }
                if ($val['eventscripts'])
                    $eventScripts=$val['eventscripts'].$scripts;
                break;
            default:
                $sJS .= '<tr><th>'.$val['label'].'：</th><td><input type="text" name="'.$key.'"'.($rs[$key]?',value="'.$rs[$key].'"':'').' /></td></tr>';
            break;
        }
        return $sJS;
    }

    /**
    * savePayment
    *
    * @access public
    * @return void
    */
    function savePayment(){
        if($_POST['pay_id']){
            $this->begin('index.php?ctl=trading/payment&act=detail&p[0]='.$_POST['pay_id']);
        }else{
            $this->begin('index.php?ctl=trading/payment&act=index');
        }
        $oPay = $this->system->loadModel('trading/payment');
        //$_POST['fee'] = $_POST['fee'] / 100;
        if ($_FILES){//是否有文件上传
            $file=$this->system->loadModel("system/sfile");
            foreach($_FILES as $key => $val){
                if (intval($val['size'])>0){
                    $_POST[$key]=$val['name'];
                    switch ($_POST['pay_type']){
                        case "ICBC"://工商银行
                            if ($key=="keyFile"){//商户私钥文件
                                if(substr($val['name'],strrpos($val['name'],".")+1,strlen($val['name']))!="key"){
                                    trigger_error(__('商户私钥文件格式有误,请上传key格式文件'),E_USER_ERROR);
                                    exit;
                                }
                            }
                            elseif ($key == "certFile"||$key =="icbcFile"){//商户公钥文件
                                if(substr($val['name'],strrpos($val['name'],".")+1,strlen($val['name']))!="crt"){
                                    if($key=="certFile")
                                        trigger_error(__('商户公钥文件格式有误,请上传crt格式文件'),E_USER_ERROR);
                                    else
                                        trigger_error(__('工行公钥文件格式有误,请上传crt格式文件'),E_USER_ERROR);
                                    exit;
                                }
                            }
                            break;
                        case "HYL"://广东好易联
                            if ($key == "keyFile"){//私钥文件
                                if(substr($val['name'],strrpos($val['name'],".")+1,strlen($val['name']))!="pem"){
                                    trigger_error(__('私钥文件格式有误,请上传key格式文件'),E_USER_ERROR);
                                    exit;
                                }
                            }
                            elseif ($key == "certFile"){//公钥文件
                                if(substr($val['name'],strrpos($val['name'],".")+1,strlen($val['name']))!="cer"){
                                    trigger_error(__('公钥文件格式有误,请上传cer格式文件'),E_USER_ERROR);
                                    exit;
                                }
                            }
                            break;
                        default:
                            break;

                    }
                    $file->UploadPaymentFile($val,$_POST['pay_type']);//上传支付相关文件
                }
            }
        }
		//error_log(print_r($_POST,true),3,'123.log');
        $this->end($oPay->updatePay($_POST), __('保存成功！'));
    }

    /**
    * addpayment
    *
    * @access public
    * @return void
    */
    function addPayment(){
        $this->begin('index.php?ctl=trading/payment&act=index');
        $oPay = $this->system->loadModel('trading/payment');
       // $_POST['fee'] = $_POST['fee'] / 100;
        if ($_FILES){//是否有文件上传
            $file=$this->system->loadModel("system/sfile");
            foreach($_FILES as $key => $val){
                if (intval($val['size'])>0){
                    $_POST[$key]=$val['name'];
                    switch ($_POST['pay_type']){
                        case "ICBC"://工商银行
                            if ($key=="keyFile"){//商户私钥文件
                                if(substr($val['name'],strrpos($val['name'],".")+1,strlen($val['name']))!="key"){
                                    trigger_error(__('文件格式有误,请上传key格式文件'),E_USER_ERROR);
                                    exit;
                                }
                            }
                            elseif ($key == "certFile"||$key =="icbcFile"){//商户公钥文件
                                if(substr($val['name'],strrpos($val['name'],".")+1,strlen($val['name']))!="crt"){
                                    trigger_error(__('文件格式有误,请上传crt格式文件'),E_USER_ERROR);
                                    exit;
                                }
                            }
                            break;
                        case "HYL"://广东好易联
                            if ($key == "keyFile"){//私钥文件
                                if(substr($val['name'],strrpos($val['name'],".")+1,strlen($val['name']))!="pem"){
                                    trigger_error(__('文件格式有误,请上传pem格式文件'),E_USER_ERROR);
                                    exit;
                                }
                            }
                            elseif ($key == "certFile"){//公钥文件
                                if(substr($val['name'],strrpos($val['name'],".")+1,strlen($val['name']))!="cer"){
                                    trigger_error(__('文件格式有误,请上传cer格式文件'),E_USER_ERROR);
                                    exit;
                                }
                            }
                            break;
                        case "skypay":
                            if ($key=="keyFile" || $key =="certFile"){//私钥文件
                                if(substr($val['name'],strrpos($val['name'],".")+1,strlen($val['name']))!="key"){
                                    trigger_error(__('文件格式有误,请上传key格式文件'),E_USER_ERROR);
                                    exit;
                                }
                            }
                            break;
                        default:
                            break;

                    }
                    $file->UploadPaymentFile($val,$_POST['pay_type']);//上传支付相关文件
                }
            }
        }
        $this->end($oPay->insertPay($_POST,$msg),$msg);
    }

    /**
    * addpayment
    *
    * @access public
    * @return void
    */
    function delPayment($sId){
        $this->begin('index.php?ctl=trading/payment&act=index');
        $oPay = $this->system->loadModel('trading/payment');
        $this->end($oPay->deletePay($sId),__('删除成功！'));
    }
    /**
    * newPayment
    *
    * @access public
    * @return void
    */
    function newPayment(){
        $this->path[] = array('text'=>'添加支付方式');
        $oPay = $this->system->loadModel('trading/payment');
        $this->pagedata['paylist'] = $oPay->getPluginsArr(true);
        $this->page('payment/pay_new.html');
    }
    
    /**
    * detailPayment
    *
    * @access public
    * @return void
    */
    function detailPayment($id){
        $this->path[] = array('text'=>'支付方式配置');
        $oPay = $this->system->loadModel('trading/payment');
        //$oPay->getPluginsArr(true);
        $aPay = $oPay->getPaymentById($id);
        $this->pagedata['pay'] = $aPay;
		################################
        $this->pagedata['pay_info'] = $this->_getPayOpt($aPay['pay_type'], $aPay['custom_name'], $aPay['fee'], $aPay['config'],$aPay['areaname_group']);
        $this->pagedata['pay_id'] = $id;
        $this->pagedata['order'] = $aPay['orderlist'];
        $this->pagedata['old_pay_type'] = $aPay['pay_type'];
        $this->pagedata['pay_des'] = $aPay['des'];
        $this->pagedata['pay_name'] = $aPay['custom_name'];
        $this->pagedata['paylist'] = $oPay->getPluginsArr(true);
        $this->page('payment/pay_edit.html');
    }
    
    /**
    * getPayOpt
    *
    * @access public
    * @return void
    */
    function getPayOpt($sType, $sPayName=''){
        header('Content-Type: text/html;charset=utf-8');
        if(!$sType){
            echo ' ';
        }else{

            echo $this->_getPayOpt($sType, $sPayName);
        }
    }
    
    function _getPayOpt($sType, $sPayName='', $nFee='', $config='',$areaname_group=''){
        $sStr = '';
        $sHtml = '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><th>支付方式名称：</th><td><input type="text" name="custom_name" value="'.$sPayName.'" /></td>';
        $oPay = $this->system->loadModel('trading/payment');
        $oPlu = $oPay->loadMethod($sType);
        if($aThisPayCur = $oPay->getSupportCur($oPlu)){
            if($aThisPayCur['DEFAULT']){
                $sStr = '商店默认货币';
            }else{
                $oCur = $this->system->loadModel('system/cur');
                $aCurLang = $oCur->getSysCur();
                if($aThisPayCur['ALL']){
                    $aThisPayCur = $aCurLang;
                }
                foreach($aThisPayCur as $k=>$v){
                    $sStr .= $aCurLang[$k].",&nbsp;";
                }
            }
        }
        $sHtml .= '<tr><th>支持交易货币：</th><td>'.($sStr?rtrim($sStr,',&nbsp;'):'').'</td></tr>';
        if($oPlu){
            $aTemp = unserialize($config);
            if($aTemp){
                foreach($aTemp as $key=>$val){
                    $aPay[$key]=$val;
                }
            }
            $aField = $oPlu->getfields();
            foreach($aField as $key=>$val){
                $sHtml .= $this->_getHtmlString($key,$val,$aPay,$eventScripts);
            }
        }
		 $sHtml .='<tr><th>支付方式对应的地区：</th><td> <div class="deliverycity" id="def_area_dexp"> <input style="width:300px;" type="text" name="areaname_group" readonly=true required="flase" value="'.$areaname_group.'" class="_x_ipt" vtype="required" caution="支付方式对应地区不能为空" onclick="regionSelect(this);"><input type="hidden" name="areaid_group" value=""/></div></td></tr></table>';
        $sHtml.='<script type="text/javascript" src="view/delivery/yanzheng.js"></script>';
        if ($eventScripts)
            $sHtml.=$eventScripts;
        return $sHtml;
    }

    function infoPad($pid){
        header('Content-Type: text/html;charset=utf-8');
        if(!$pid){
            echo ' ';
        }else{
            $oPay = $this->system->loadModel('trading/payment');
            $oPlu = $oPay->loadMethod($pid);
            $infoPad = '';
            if($oPlu){
                $infoPad = $oPlu->infoPad();
            }
            echo $infoPad;
        }
    }
}
?>
