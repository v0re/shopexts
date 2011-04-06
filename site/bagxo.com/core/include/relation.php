<?php
//本数组主要是用来确定常量对应的model的名称
$objRelation=array(
        OBJ_PRODUCT =>'goods/product',
        OBJ_ARTICLE  =>'article',
    );
//model体现到view的时候要显示的名称
$objName=array(
        OBJ_PRODUCT =>__('商品类'),
        OBJ_ARTICLE  =>__('文章类'),
    );
?>
