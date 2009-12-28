<?php
$cusmenu['member'] = array('label'=>'会&nbsp;&nbsp;&nbsp;员','link'=>'index.php?ctl=member/member&act=index','keywords'=>array('hy','member'),
    'items'=>array(
            array(
                'type'=>'group',
                'label'=>'购买咨询',
                'items'=>array(
                    array('type'=>'menu',
                        'label'=>'咨询报价',
                        'link'=>'index.php?ctl=member/askpric&act=index'
                    )
                )
            ),
            
    ));
?>