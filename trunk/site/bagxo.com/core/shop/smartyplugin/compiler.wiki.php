<?php

function smarty_compiler_wiki($tag_arg, &$smarty){
    $aAttrs=$smarty->_parse_attrs($tag_arg);
    $system=&$GLOBALS['system'];
    $oPage=$system->loadModel('content/page');
    $aPage=$oPage->getPageByTitle(str_replace('\'','',$aAttrs['title']));
    return 'echo "'.$aPage['page_content'].'"';
    //return 'echo "WZP";';
}

?>