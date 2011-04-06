<?php

$hooks = array(
    'order'=>array(
        'pay'=>array(
                array('label'=>'发放优惠券','class'=>'hook_coupon','func'=>'toPayed'),
                array('label'=>'得积分','class'=>'hook_getPoint','func'=>'toPayed'),
            ),

        'delivery'=>array(
                array('label'=>'发送赠品','class'=>'hook_gift','func'=>'toConsign'),
            ),
        'create'=>array(
            ),

        'reship'=>array(
            ),
        'refund'=>array(
                array('label'=>'退还积分','class'=>'hook_getPoint','func'=>'toRefund'),
            ),
        'confirm'=>array(
            ),
        'cancel'=>array(
                array('label'=>'退还积分','class'=>'hook_gift','func'=>'toCancel'),            
            ),
        'remove'=>array(
                array('label'=>'退还积分','class'=>'hook_gift','func'=>'toRemove'),    
            ),
        ),
    'account'=>array(
        'register'=>array(
            ),
        'chgpass'=>array(
            )

        )
    );

?>
