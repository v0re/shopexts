<?php
class ship_fedex{

    var $version=0.1;
    var $logo='http://images.fedex.com/images/shared/shared_fedex_express_logo.gif';
    var $name="联邦快递";

    function getfields(){
        $setItem = array(
                'MemberNo'=>array(
                        'label'=>'电子商务网站会员号',
                        'type'=>'string'
                    ),
                'password'=>array(
                        'lable'=>'密码',
                        'type'=>'string'
                )
            );
    }

    function beforeSaveSetting($setting){
        return true;
    }

    function afterSaveSetting($setting){
        return true;
    }
}
?>
