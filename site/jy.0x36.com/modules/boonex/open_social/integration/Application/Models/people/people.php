<?php

/* BoonEx Opensocial integration*/

class peopleModel extends Model {
  public $cachable = array();
  //public $cachable = array('is_friend', 'get_personZ', 'get_person_info', 'get_friends', 'get_friends_count', 'get_friend_requests');

  // if extended = true, it also queries all child tables
  // defaults to false since its a hell of a presure on the database.
  // remove once we add some proper caching
  public function load_get_person($id, $extended = false) {
	//print 'TODO "A"';

    global $db;
    $this->add_dependency('people', $id);
    $id = $db->addslashes($id);

    //$res = $db->query("select * from Profiles where ID = $id");
	$res = $db->query("select `ID` AS id, `Email` AS email, `NickName` AS first_name, '' AS last_name, '' AS thumbnail_url, '' AS profile_url from `Profiles` where `ID` = $id");

    if (! $db->num_rows($res)) {
      throw new Exception("Invalid person");
    }
    $person = $db->fetch_array($res, MYSQLI_ASSOC);

	return $person;
	//return array('person' => $person);
    //TODO missing : person_languages_spoken, need to add table with ISO 639-1 codes
    /*$tables_addresses = array('person_addresses', 'person_current_location');
    $tables_organizations = array('person_jobs', 'person_schools');
    $tables = array('person_activities', 'person_body_type', 'person_books', 'person_cars',
        'person_emails', 'person_food', 'person_heroes', 'person_movies',
        'person_interests', 'person_music', 'person_phone_numbers', 'person_quotes',
        'person_sports', 'person_tags', 'person_turn_offs', 'person_turn_ons',
        'person_tv_shows', 'person_urls');
    foreach ($tables as $table) {
      $person[$table] = array();
      $res = $db->query("select * from $table where person_id = $id");
      while ($data = $db->fetch_array($res, MYSQLI_ASSOC)) {
        $person[$table][] = $data;
      }
    }
    foreach ($tables_addresses as $table) {
      $res = $db->query("select addresses.* from addresses, $table where $table.person_id = $id and addresses.id = $table.address_id");
      while ($data = $db->fetch_array($res)) {
        $person[$table][] = $data;
      }
    }
    foreach ($tables_organizations as $table) {
      $res = $db->query("select organizations.* from organizations, $table where $table.person_id = $id and organizations.id = $table.organization_id");
      while ($data = $db->fetch_array($res)) {
        $person[$table][] = $data;
      }
    }
    return $person;*/
  }

  /*
	 * doing a select * on a large table is way to IO and memory expensive to do
	 * for all friends/people on a page. So this gets just the basic fields required
	 * to build a person expression:
	 * id, email, first_name, last_name, thumbnail_url and profile_url
	 */
  public function load_get_person_info($id) {
    global $db;
    $this->add_dependency('people', $id);
    $id = $db->addslashes($id);
    //$res = $db->query("select id, email, first_name, last_name, thumbnail_url, profile_url from persons where id = $id");
    $res = $db->query("select `ID` AS id, `Email` AS email, `NickName` AS first_name, '' AS last_name, '' AS thumbnail_url, '' AS profile_url from `Profiles` where `ID` = $id");
    if (! $db->num_rows($res)) {
      throw new Exception("Invalid person");
    }
    return $db->fetch_array($res, MYSQLI_ASSOC);
  }

}
