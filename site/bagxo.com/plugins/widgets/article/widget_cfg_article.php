<?php
function widget_cfg_article($system){
    $o=$system->loadModel('content/article');
    return $o->getCategorys();
}
?>