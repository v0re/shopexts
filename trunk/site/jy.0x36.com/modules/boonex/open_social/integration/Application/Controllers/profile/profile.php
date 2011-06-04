<?php

/* BoonEx Opensocial integration*/

class profileController extends baseController {

  public function removeapp($params) {
	$iVisitorID = (isMember() && $_COOKIE['memberID'] > 0) ? (int)$_COOKIE['memberID'] : 0; //TODO
	//$iVisitorID = (int)$params[3];

    if (! $iVisitorID) {
      return;
    }

    $app_id = intval($params[4]);

    $apps = $this->model('applications');
    if ($apps->remove_application($iVisitorID, $app_id, 1)) {
      $message = 'Application removed';
    } else {
      $message = 'Could not remove application, invalid id';
    }
    header("Location: " . BX_DOL_URL_ROOT . 'profile.php?ID=' . $iVisitorID);
  }

  public function application($params) {
	$iVisitorID = (isMember() && $_COOKIE['memberID'] > 0) ? (int)$_COOKIE['memberID'] : 0; //TODO
	//$iVisitorID = (int)$params[3];

    if (! $iVisitorID) {
      return;
    }

    $app_id = intval($params[4]);
    $people = $this->model('people');
    //$person = $people->get_person($iVisitorID, true);
    $apps = $this->model('applications');
    $application = $apps->get_person_application($iVisitorID, $app_id, 1);

    $this->template('applications/application_view.php', array('application' => $application));
  }

  public function member_apps_sett($param) {
	$iVisitorID = (isMember() && $_COOKIE['memberID'] > 0) ? (int)$_COOKIE['memberID'] : 0; //TODO
	//$iVisitorID = (int)$param[3];

    if (! $iVisitorID) {
      return;
    }

    $people = $this->model('people');
    $apps = $this->model('applications');
    $applications = $apps->get_person_applications($iVisitorID);
    $person = $people->get_person($iVisitorID, true);
    $this->template('applications/applications_settings.php', array('person' => $person, 'applications' => $applications));
  }

  public function member_app_sett_by_id($param) {
	$iVisitorID = (isMember() && $_COOKIE['memberID'] > 0) ? (int)$_COOKIE['memberID'] : 0; //TODO
	//$iVisitorID = (int)$param[3];
	$iAppID = (int)$param[4];

    if (! $iVisitorID || ! $iAppID) {
      return;
    }

    $people = $this->model('people');
    $apps = $this->model('applications');
    $applications = $apps->get_person_application_by_id($iVisitorID, $iAppID);
    $person = $people->get_person($iVisitorID, true);
    $this->template('applications/applications_settings.php', array('person' => $person, 'applications' => $applications));
  }

  public function addapp($param) {
	$iVisitorID = ((isMember() || isAdmin()) && $_COOKIE['memberID'] > 0) ? (int)$_COOKIE['memberID'] : 0; //TODO

    if (! $iVisitorID || ! isset($_GET['appUrl'])) {
      return;
    }

    $url = trim(urldecode($_GET['appUrl']));
    $apps = $this->model('applications');
    $ret = $apps->add_application($iVisitorID, $url);
    return;
  }

  public function adminaddapp($param) {
	//$iVisitorID = (isAdmin() && $_COOKIE['memberID'] > 0) ? (int)$_COOKIE['memberID'] : 0; //TODO

    if (! isAdmin() || ! isset($_GET['appUrl'])) {
      return;
    }

    $url = trim(urldecode($_GET['appUrl']));
    $apps = $this->model('applications');
    //$ret = $apps->add_application($iVisitorID, $url);
    $ret = $apps->add_application(0, $url);
  }

  public function appsettings($params) {
	$iVisitorID = (isMember() && $_COOKIE['memberID'] > 0) ? (int)$_COOKIE['memberID'] : 0; //TODO
	//$iVisitorID = (int)$params[3];

    $app_id = intval($params[4]);

	if (! $app_id) return;

    $apps = $this->model('applications');
    $people = $this->model('people');
    $person = $people->get_person($iVisitorID, true);

    $app = $apps->get_person_application($iVisitorID, $app_id, 1);
    $applications = $apps->get_person_applications($iVisitorID);
    if (count($_POST)) {
      $settings = unserialize($app['settings']);
      if (is_object($settings)) {
        foreach ($_POST as $key => $value) {
          // only store if the gadget indeed knows this setting, otherwise it could be abuse..
          if (isset($settings->$key)) {
            $apps->set_application_pref($iVisitorID, $app_id, $key, $value);
          }
        }
      }
	  header("Location: " . BX_DOL_URL_ROOT . 'profile.php?ID=' . $iVisitorID);
	  print 'Saved'; exit;
    }
    $this->template('applications/application_settings.php', array(
		'applications' => $applications, 'application' => $app, 'person' => $person
	));
  }
}