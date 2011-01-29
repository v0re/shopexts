<?php
$repos = 'https://shopexts.googlecode.com/svn/trunk/site/pg.app.yiyiee.com';
$work_dir = dirname(__FILE__);

if(svn_update ($work_dir)){
    echo "update ok";
}esle{
    echo "update fail!";
}