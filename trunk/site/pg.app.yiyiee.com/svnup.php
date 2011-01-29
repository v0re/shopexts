<?php
$repos = 'https://shopexts.googlecode.com/svn/trunk/site/pg.app.yiyiee.com';
$work_dir = dirname(__FILE__);

if($ret = svn_update ($work_dir)){
    echo "update to rev $ret";
}else{
    echo "update fail!";
}