<?php
function smarty_function_widgets($params, &$smarty){
    if(!$smarty->widgets_mdl){
        $system = &$GLOBALS['system'];
        $smarty->widgets_mdl = &$system->loadModel('content/widgets');
    }
    $i = intval($smarty->_wgbar[$s]++);
    echo '<div class="shopWidgets_panel" base_file="'.$smarty->files[0].'" base_slot="'.$i.'" base_id="'.$params['id'].'"  >';
    $smarty->widgets_mdl->adminLoad($smarty->files[0],$i);
    echo '</div>';
}
?>
