<?php

define ('BX_SECURITY_EXCEPTIONS', true);
$aBxSecurityExceptions = array ();
$aBxSecurityExceptions[] = 'POST.Check';
$aBxSecurityExceptions[] = 'REQUEST.Check';
$aBxSecurityExceptions[] = 'POST.Values';
$aBxSecurityExceptions[] = 'REQUEST.Values';


require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolPFM.php' );
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

send_headers_page_changed();

$logged['admin'] = member_auth( 1, true, true );

switch(bx_get('action')) {
	case 'getArea':
		genAreaJSON((int)bx_get('id'));
		break;
	case 'createNewBlock':
		createNewBlock();
		break;
	case 'createNewItem':
		createNewItem();
		break;
	case 'savePositions':
		savePositions((int)bx_get('id'));
		break;
	case 'loadEditForm':
		showEditForm((int)bx_get('id'), (int)bx_get('area'));
		break;
	case 'dummy':
		echo 'Dummy!';
		break;
	case 'Save'://save item
		saveItem((int)bx_get('area'), $_POST);
		break;
	case 'Delete'://delete item
		deleteItem((int)bx_get('id'), (int)bx_get('area'));
		break;
}

function createNewBlock() {
	$oFields = new BxDolPFM( 1 );
	$iNewID = $oFields -> createNewBlock();
	header('Content-Type:text/javascript');
	echo '{id:' . $iNewID . '}';
}

function createNewItem() {
	$oFields = new BxDolPFM( 1 );
	$iNewID = $oFields -> createNewField();

	bx_import('BxDolInstallerUtils');
	$oInstallerUtils = new BxDolInstallerUtils();
	$oInstallerUtils->updateProfileFieldsHtml();
	
	header('Content-Type:text/javascript');
	echo '{id:' . $iNewID . '}';
}

function genAreaJSON( $iAreaID ) {
	$oFields = new BxDolPFM( $iAreaID );
	
	header( 'Content-Type:text/javascript' );
	echo $oFields -> genJSON();
}

function savePositions( $iAreaID ) {
	$oFields = new BxDolPFM( $iAreaID );
	
	header( 'Content-Type:text/javascript' );
	$oFields -> savePositions( $_POST );

	$oCacher = new BxDolPFMCacher();
	$oCacher -> createCache();
}

function saveItem( $iAreaID, $aData ) {
	$oFields = new BxDolPFM( $iAreaID );
	$oFields -> saveItem( $_POST );

	bx_import('BxDolInstallerUtils');
	$oInstallerUtils = new BxDolInstallerUtils();
	$oInstallerUtils->updateProfileFieldsHtml();

	$oCacher = new BxDolPFMCacher();
	$oCacher -> createCache();
}

function deleteItem( $iItemID, $iAreaID ) {
	$oFields = new BxDolPFM( $iAreaID );
	$oFields -> deleteItem( $iItemID );

	bx_import('BxDolInstallerUtils');
	$oInstallerUtils = new BxDolInstallerUtils();
	$oInstallerUtils->updateProfileFieldsHtml();

	$oCacher = new BxDolPFMCacher();
	$oCacher -> createCache();
}

function showEditForm( $iItemID, $iAreaID ) {
	$oFields = new BxDolPFM( $iAreaID );
	
	ob_start();
	?>
	<form name="fieldEditForm" method="post" action="<?=$GLOBALS['site']['url_admin'] . 'fields.parse.php'; ?>" target="fieldFormSubmit" onsubmit="clearFormErrors( this )">
        <div class="edit_item_table_cont">
            <?=$oFields -> genFieldEditForm( $iItemID ); ?>
        </div>
	</form>

	<iframe name="fieldFormSubmit" style="display:none;"></iframe>
	<?
	$sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => ob_get_clean()));

	echo PopupBox('pf_edit_popup', _t('_adm_fields_box_cpt_field'), $sResult);
}

?>
