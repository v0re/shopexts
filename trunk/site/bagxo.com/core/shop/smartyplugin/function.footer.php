<?php
function smarty_function_footer($params, &$smarty)
{
    $system = &$GLOBALS['system'];
    $output = &$system->loadModel('system/frontend');
    $theme_dir = $system->base_url().'themes/'.$output->theme;
    $smarty->_smarty_include(array(
            'smarty_include_tpl_file' =>'shop:common/footer.html',
            'smarty_include_vars' =>array('certtext'=>'<a href="http://www.miibeian.gov.cn/ " target="blank">'.$system->getConf('site.certtext').'</a>',
            'mini_cart'=>($system->getConf('site.buy.target')==3),
            'theme_dir'=>$theme_dir,
            'stateString'=>'action='.urlencode($system->request['action']['controller'].':'.
                $system->request['action']['method']).'&p='.urlencode($system->request['action']['args'][0])),
        ));
    $jssrc = (defined('DEBUG_JS') && DEBUG_JS)?
        $smarty->system->mkUrl('sfile','jscript',array('foot'),'js')
        :$system->base_url().((defined('GZIP_JS') && GZIP_JS)?'statics/foot.jgz':'statics/foot.js');

    $js = '';
    if(defined('SHOP_DEVELOPER') && SHOP_DEVELOPER){
        $html .= $system->_debugger['log'];
    }
    if(defined('DEBUG_JS') && DEBUG_JS){
        foreach(find(BASE_DIR.'/statics/footjs','js') as $jsfile){
            $js.='<script type="text/javascript" src="'.$system->base_url().'statics/footjs'.$jsfile."\"></script>";
        }
    }else{
        $js.='<script type="text/javascript" src="'.$system->base_url().((defined('GZIP_JS') && GZIP_JS)?'statics/foot.jgz':'statics/foot.js')."\"></script>";
    }
    if($system->getConf('shopex.wss.show')) {
        $wssjs=$system->getConf('shopex.wss.js');
    }
    if($system->getConf('certificate.channel.status')){
            $channel = $system->getConf('certificate.channel.service').'<a href="'.$system->getConf('certificate.channel.url').'" target="_blank">'.$system->getConf('certificate.channel.name');
            $channel =$channel.'</a>';     
    }
   
    if($system->getConf('site.shopex_certify')==0){
        //站点底部
        $ref = $_SERVER['HTTP_HOST'];
        $check = md5($ref.'ShopEx@Store');
        $str = urlencode($system->getConf('certificate.str'));
        if(!$str){
            $str = urlencode('无');
        }
        if(defined('SAAS_MODE')&&SAAS_MODE){
            $versionStr='';
        }else{
            $versionStr='v'.$system->_app_version;
        }
        
        if($system->use_gzip) {
            $gzip = 'enabled';
        } else {
            $gzip = 'disabled';
        }
        
        $themeFoot='<div class="themefoot">'.stripslashes($system->getConf('system.foot_edit')).'</div>';
        $PoweredStr='<div style="font-family:Verdana;line-height:20px;font-size:11px;text-align:center;">';
        if ($system->getConf('certificate.auth_type')=="commercial"){
            $greencard = $system->getConf('store.greencard');
            if (!isset($greencard)||$greencard)
            $PoweredStr.="<a href='http://service.shopex.cn/show/certinfo.php?certi_id=".$system->getConf('certificate.id')."&url=".rawurlencode($system->base_url())."' target='_blank'><img src='statics/bottom-authorize.gif'></a><br>";
        }
        $PoweredStr.='<a href="http://store.shopex.cn/rating/store_detail.php?ref='.$ref.'&check='.$check.'&str='.$str.'" target="_blank" style="color:#666;text-decoration:none;cursor:pointer">';
        $PoweredStr.='<a href="http://www.shopex.cn" target="_blank" style="color:#C0C0C0;text-decoration:none;cursor:pointer">';
        $PoweredStr.='&nbsp;&nbsp;<b style="color:#C0C0C0"> </b><b style="color:#C0C0C0"> </b>';
        $PoweredStr.='</a>';
        $PoweredStr.='<span style="color:#C0C0C0;font-size:9px;">&nbsp;</span>';
        $PoweredStr.='<span style="color:#C0C0C0;display:none">&nbsp|Gzip '.$gzip.'</span>&nbsp;';
        if($channel){
        $PoweredStr.='<br/><span>'.$channel.'</span>&nbsp;';
        }
        if($wssjs){
        $PoweredStr.='<span style="display:none">'.$wssjs.'</span>';
        }
        if($system->getConf('site.certtext')){
        $PoweredStr.='<div><a href="http://www.miibeian.gov.cn/" target="blank" style="color:#666;text-decoration:none;cursor:pointer;font-size:9px;">'.$system->getConf('site.certtext').'</a></div>';
        }
        $PoweredStr.='</div>';
       
    }

   
    
    return $html.$themeFoot.$PoweredStr.$js;
}

?>
