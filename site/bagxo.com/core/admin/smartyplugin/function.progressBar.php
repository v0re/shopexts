<?php
function smarty_function_progressBar($params, &$smarty){

    $legend = $params['title']?("<legend>".__($params['title'])."</legend>"):'';

    $html=<<<EOF
<fieldset class="progressBar" name="{$params['name']}">
    {$legend}    
    <div class="base">
        <div class="body" style="width:{$params['current']}%"></div>    
    </div>
</fieldset>
EOF;
    return $html;
}

?>
