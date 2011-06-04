<?php

/* BoonEx Opensocial integration*/

class applController extends baseController {

  public function show($params) {
    //$iVisitorID = (defined('BX_PROFILE_PAGE') && (isMember() || isAdmin()) && $_COOKIE['memberID'] > 0) ? (int)$_COOKIE['memberID'] : 0; //TODO

    $iVisitorID = intval($params[3]);
    $app_id = intval($params[4]);

	if (! $app_id) exit;

    $apps = $this->model('applications');

    $application = $apps->get_person_application($iVisitorID, $app_id, 1);

	$this->template('applications/application_view.php', array(
		'application' => $application /*,
		'person' => $person*/
	));
  }

  public function header_once($params) {
	$this->template('common/header_once.php', array());
  }
}