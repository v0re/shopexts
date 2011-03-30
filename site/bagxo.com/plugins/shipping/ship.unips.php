<?php
class ship_unips{

    var $version=20070615;
    var $logo='http://www.unips.com.cn/image/IndexLogo.gif';
    var $name="发网";

    function mkprice($weight,$from,$to){
        return 10;
    }

    function local(){
        return array('beijing','shanghai','tianjin');
    }

    function afterShipping($order){
        $actions = array(
                array(
                    'method'=>'get',
                    'url'=>'http://www.unips.com.cn/OrderTrack.aspx',
                    'title'=>'查询配送情况',
                    'params'=>array(
                            'MemberNo'=>$this->getConf('MemberNo'),
                            'OrderCode'=>$order->id,
                        )
                    )
            );
        return $actions;
    }

    function beforeShipping($order){
        $actions = array(
                array(
                    'method'=>'post',
                    'url'=>'',
                    'title'=>'向发网提交配送请求',
                    'params'=>array(
                        'MemberNo'=>$this->getConf('MemberNo'),        //电子商务企业在发网的唯一编号
                        'OrderID'=>$order->id,        //电子商务网站形成的订单号，同一家电子商务网站订单号不能重复，建议使用流水号
                        'LoadName'=>$order->name,        //提交的货物名称
                        'LoadMemberNo'=>$order->name,        //货主在发网的唯一编号；
                        'MemberURL'=>$order->url,        //(Get，Webservice方式无需此参数)电子商务企业接收发网配送结果的URL
                        'LoadDisUrl'=>$order->url,        //在发网浏览该货物的同时，可以链接到您的网站查看信息
                        'LoadPrice'=>$order->price,        //货币型或整型
                        'OrgProvince'=>$order->OrgProvince,        //货物出发地所在省份
                        'OrgCity'=>$order->OrgCity,        //货物出发地所在城市(地级市)
                        'OrgAddress'=>$order->OrgAddress,        //货物所在地址
                        'DestProvince'=>$order->DestProvince,        //到达地所在省份
                        'DestCity'=>$order->DestCity,        //到达地所在城市
                        'DestAddress'=>$order->DestAddress,        //到达详细地址
                        'TransFee'=>$order->TransFee,        //货币型或整型
                        'DeliveryDate'=>$order->DeliveryDate,        //yyyy-MM-dd
                        'Contact'=>$this->getConf('system.contact.name'),
                        'Phone'=>$this->getConf('system.contact.phone'),        //电话，手机必选填一项
                        'MobilePhone'=>$this->getConf('system.contact.mobile'),        //电话，手机必选填一项
                        'Email'=>$this->getConf('system.contact.email'),
                        'Consignee'=>$order->Consignee,
                        'ConsigneePhone'=>$order->ConsigneePhone,
                        'ConsigneeMobilePhone'=>$order->ConsigneeMobilePhone,
                        'ConsigneeEmail'=>$order->ConsigneeEmail,
                    )
                )
            );
        return $actions;
    }

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

}
?>
