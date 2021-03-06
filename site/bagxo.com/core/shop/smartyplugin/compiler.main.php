<?php 
/* 
 * Smarty plugin 
 * ------------------------------------------------------------- 
 * File:     compiler.tplheader.php 
 * Type:     compiler 
 * Name:     tplheader 
 * Purpose:  Output header containing the source file name and 
 *           the time it was compiled. 
 * ------------------------------------------------------------- 
 */ 
function smarty_compiler_main($tag_args, &$smarty) 
{ 
    $attrs = $smarty->_parse_attrs($tag_args);
    if (isset($assign_var)) {
        $output .= "ob_start();\n";
    }
    $output .= "\$_smarty_tpl_vars = \$this->_tpl_vars;\n";

    $_params = "array('smarty_include_tpl_file' => \$this->template_exists('user:'.\$this->theme.'/view/'.\$_smarty_tpl_vars['_MAIN_'])?'user:'.\$this->theme.'/view/'.\$_smarty_tpl_vars['_MAIN_']:'shop:'.\$_smarty_tpl_vars['_MAIN_'], 'smarty_include_vars' => array())";
    $output .= "\$this->_smarty_include($_params);\n" .
        "\$this->_tpl_vars = \$_smarty_tpl_vars;\n" .
        "unset(\$_smarty_tpl_vars);\n";

    if (isset($assign_var)) {
        $output .= "\$this->assign(" . $assign_var . ", ob_get_contents()); ob_end_clean();\n";
    }

    return $output;
}
?>
