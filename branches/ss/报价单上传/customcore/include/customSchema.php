<?php
$cusmenu['goods'] = array('label'=>'商&nbsp;&nbsp;&nbsp;品','link'=>'index.php?ctl=goods/product&act=index',
    'items'=>array(
            array(
                'type'=>'group',
                'label'=>'商品管理',
                'items'=>array(
                    array('type'=>'menu',
                        'label'=>'报价单列表',
                        'link'=>'index.php?ctl=goods/askpric&act=index'
                    )
                )
            ),
            
    ));
?>