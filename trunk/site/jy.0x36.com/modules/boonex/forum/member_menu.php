<?php

if ($_GET['orca_integration'] && preg_match('/^[0-9a-z]+$/', $_GET['orca_integration'])) {
    define('BX_ORCA_INTEGRATION', $_GET['orca_integration']);
} else {
    define('BX_ORCA_INTEGRATION', 'dolphin');
}

$aPathInfo = pathinfo(__FILE__);
require_once( $aPathInfo['dirname'] . '/inc/header.inc.php' );
if ( !class_exists( 'Thing' ) )
    require_once( $GLOBALS['gConf']['dir']['classes'] . 'Thing.php' );
require_once( $GLOBALS['gConf']['dir']['classes'] . 'ThingPage.php' );
require_once( $GLOBALS['gConf']['dir']['classes'] . 'Mistake.php' );
require_once( $GLOBALS['gConf']['dir']['classes'] . 'BxXslTransform.php' );
require_once( $GLOBALS['gConf']['dir']['classes'] . 'BxDb.php' );
require_once( $GLOBALS['gConf']['dir']['classes'] . 'DbForum.php' );

$oFDB = new DbForum ();

$oMemberMenu = bx_instance('BxDolMemberMenu');

$aLinkInfo = array(
    'item_img_src'  => getTemplateIcon ('modules/boonex/forum/|orca.gif'),
    'item_img_alt'  => _t( '_bx_forums' ),
    'item_link'     => $GLOBALS['site']['url'] . 'forum/',
    'item_onclick'  => null,
    'item_title'    => _t( '_bx_forums' ),
    'extra_info'    => $oFDB->getUserPosts(getNickName()),
);

return $oMemberMenu -> getGetExtraMenuLink($aLinkInfo);

?>
