<?php
function smarty_modifier_barcode($data){
    $system = &$GLOBALS['system'];
    $bcode = $system->loadModel('utility/barcode');
    return $bcode->get($data);
}
?>