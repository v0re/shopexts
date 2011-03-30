<?php
class ship_MountSpring{

    var $version=20071207;
    var $logo='http://www.website-export.com/images/logo/logo.jpg';
    var $name="曼特斯配送计算接口";

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
                    'title'=>'向曼特斯配送计算接口提交请求',
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
                'user_id'=>array(
                        'label'=>'用户代号',
                        'type'=>'string'
                ),
                'origin_zip'=>array(
                        'label'=>'发货地邮编',
                        'type'=>'string'
                ),
                'shipping_method'=>array(
                        'label'=>'运输方式',
                        'type'=>'array',
                        'option'=>array('parcel_post'=>__('平邮'),
                                        'ems_cnps_fd'=>__('邮政快递'),
                                        'ems_i'=>__('快递')),
                ),
                'ems_carrier'=>array(
                        'label'=>'快递公司',
                        'type'=>'string'
                ),
                'shipping_type'=>array(
                        'label'=>'商品种类',
                        'type'=>'array',
                        'option'=>array(1=>__('普通包裹'),2=>__('印刷品'))
                ),
                'origin_city'=>array(
                        'lable'=>'发货城市（印刷品必填）',
                        'type'=>'string'
                ),
                'extra_fee'=>array(
                        'lable'=>'附加费用（包装费等）',
                        'type'=>'string'
                )
            );
    }

}
?>
