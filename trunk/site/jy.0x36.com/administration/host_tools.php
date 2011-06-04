<?php

require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );

if ( false != bx_get('get_phpinfo') ) {
    echo phpInfo();
    exit;
}

bx_import('BxDolAdminTools');

$logged['admin'] = member_auth( 1, true, true );

$oAdmTools = new BxDolAdminTools();
$sResult = $oAdmTools->GenCommonCode();

switch(bx_get('action')) {
	case 'cache_engines':
		$sResult .= $oAdmTools->GenCacheEnginesTable();
		break;
	case 'perm_table':
		$sResult .= $oAdmTools->GenPermTable();
		break;
	case 'main_params':
		$sResult .= $oAdmTools->GenMainParamsTable();
		break;
	case 'main_page':
		$sResult .= $oAdmTools->GenTabbedPage();
		break;
	default:
		$sResult .= $oAdmTools->GenTabbedPage();
		break;
}

//'_adm_at_title' => 'Admin Tools',
bx_import('BxTemplFormView');
$oForm = new BxTemplFormView($_page);
$iNameIndex = 9;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('common.css'),
    'header' => _t('_adm_at_title'),
    'header_text' => _t('_adm_at_title')
);

$_page_cont[$iNameIndex]['page_main_code'] = $sResult . $oForm->getCode() . adm_hosting_promo();

PageCodeAdmin();

?>
