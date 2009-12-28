<?php
$cusmenu['member'] = array('label'=>'会&nbsp;&nbsp;&nbsp;员','link'=>'index.php?ctl=member/member&act=index','keywords'=>array('hy','member'),
    'items'=>array(
            array(
                'type'=>'group',
                'label'=>'会员管理',
                'items'=>array(
                    array('type'=>'menu',
                        'label'=>'批量导入',
                        'link'=>'index.php?ctl=member/member&act=import'
                    )
                )
            ),
            
    ));
?>