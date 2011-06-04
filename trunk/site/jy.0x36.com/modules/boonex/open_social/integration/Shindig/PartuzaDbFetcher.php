<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

class PartuzaDbFetcher {
  private $db;
  private $url_prefix;

  // Singleton
  private static $fetcher;

  private function connectDb() {
    // one of the class paths should point to partuza's document root, abuse that fact to find our config
	$aPathInfo = pathinfo(__FILE__);
	require_once ($aPathInfo['dirname'] . '/../../../../../inc/header.inc.php');

	$this->db = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    mysqli_select_db($this->db, DATABASE_NAME);

    $this->url_prefix = BX_DOL_URL_ROOT;

    $this->url_prefix = $config['partuza_url'];
    if (substr($this->url_prefix, strlen($this->url_prefix) - 1, 1) == '/') {
      // prevent double //'s in the profile and thumbnail urls by forcing the prefix to end without a tailing /
      $this->url_prefix = substr($this->url_prefix, 0, strlen($this->url_prefix) - 1);
    }
  }

  private function __construct() {  // Not currently used
  }

  private function checkDb() {
    if (! is_object($this->db)) {
      $this->connectDb();
    }
  }

  private function __clone() {// private, don't allow cloning of a singleton
}

  static function get() { // This object is a singleton
    if (! isset(PartuzaDbFetcher::$fetcher)) {
      PartuzaDbFetcher::$fetcher = new PartuzaDbFetcher();
    }
    return PartuzaDbFetcher::$fetcher;
  }

  public function createMessage($from, $appId, $message) {
    /* A $message looks like:
    * [id] => {msgid}
    * [title] => You have an invitation from Joe
    * [body] => Click <a href="http://app.example.org/invites/{msgid}">here</a> to review your invitation.
    * [recipients] => Array
    *      (
    *          [0] => UserId1
    *          [1] => UserId2
    *      )
    */
    $this->checkDb();
    $from = mysqli_real_escape_string($this->db, $from);
    if (empty($from)) {
      throw new Exception("Invalid person id");
    }
    //$created = time();
    $title = mysqli_real_escape_string($this->db, trim($message['title']));
    if (empty($title)) {
      throw new Exception("Can't send a message with an empty title");
    }
    $body = mysqli_real_escape_string($this->db, trim($message['body']));
    if (! isset($message['recipients'])) {
      throw new Exception("Invalid recipients");
    }
    if (! is_array($message['recipients'])) {
      $message['recipients'] = array($message['recipients']);
    }
    foreach ($message['recipients'] as $to) {
      //TODO should verify here if this is a valid user id, and if it's a friend
      $to = mysqli_real_escape_string($this->db, $to);
      //mysqli_query($this->db, "insert into messages (`from`, `to`, title, body, created) values ($from, $to, '$title', '$body', $created)");

	  // 'A' will hope that From / To is Integer
	  $from = (int)$from;
	  $to = (int)$to;
	  $sNewMailSQL = "
		INSERT INTO `sys_messages`
		(`Sender`, `Recipient`, `Subject`, `Text`, `Date`) VALUES
		({$from}, {$to}, '{$title}', '{$body}', NOW())
	  ";
      mysqli_query($this->db, $sNewMailSQL);
    }
  }

  public function createActivity($person_id, $activity, $app_id = '0') {
	return true; //TODO 'A' we haven`t activity in dolphin

    /*$this->checkDb();
    $app_id = intval($app_id);
    $person_id = intval($person_id);
    $title = trim(isset($activity['title']) ? $activity['title'] : '');
    if (empty($title)) {
      throw new Exception("Invalid activity: empty title");
    }
    $body = isset($activity['body']) ? $activity['body'] : '';
    $title = mysqli_real_escape_string($this->db, $title);
    $body = mysqli_real_escape_string($this->db, $body);
    $time = time();
    mysqli_query($this->db, "insert into activities (id, person_id, app_id, title, body, created) values (0, $person_id, $app_id, '$title', '$body', $time)");
    if (! ($activityId = mysqli_insert_id($this->db))) {
      return false;
    }
    $mediaItems = isset($activity['mediaItems']) ? $activity['mediaItems'] : array();
    if (count($mediaItems)) {
      foreach ($mediaItems as $mediaItem) {
        $type = isset($mediaItem['type']) ? $mediaItem['type'] : '';
        $mimeType = isset($mediaItem['mimeType']) ? $mediaItem['mimeType'] : '';
        $url = isset($mediaItem['url']) ? $mediaItem['url'] : '';
        $type = mysqli_real_escape_string($this->db, trim($type));
        $mimeType = mysqli_real_escape_string($this->db, trim($mimeType));
        $url = mysqli_real_escape_string($this->db, trim($url));
        if (! empty($mimeType) && ! empty($type) && ! empty($url)) {
          mysqli_query($this->db, "insert into activity_media_items (id, activity_id, mime_type, media_type, url) values (0, $activityId, '$mimeType', '$type', '$url')");
          if (! mysqli_insert_id($this->db)) {
            return false;
          }
        } else {
          return false;
        }
      }
    }
    return true;*/
  }

  public function getActivities($ids, $appId, $sortBy, $filterBy, $filterOp, $filterValue, $startIndex, $count, $fields, $activityIds) {
	return array(); //'A' we haven`t activities yet

	/*
    //TODO add support for filterBy, filterOp and filterValue
    $this->checkDb();
    $activities = array();
    $ids = array_map('intval', $ids);
    $ids = implode(',', $ids);
    if (isset($activityIds) && is_array($activityIds)) {
      $activityIds = array_map('intval', $activityIds);
      $activityIdQuery = " and activities.id in (" . implode(',', $activityIds);
    } else {
      $activityIdQuery = '';
    }
    $appIdQuery = $appId ? " and activities.app_id = " . intval($appId) : '';

    // return a proper totalResults count
    $res = mysqli_query($this->db, "select count(id) from activities where activities.person_id in ($ids) $activityIdQuery $appIdQuery");
    if ($res !== false) {
      list($totalResults) = mysqli_fetch_row($res);
    } else {
      $totalResults = '0';
    }
    $startIndex = (! is_null($startIndex) && $startIndex !== false && is_numeric($startIndex)) ? intval($startIndex) : '0';
    $count = (! is_null($count) && $count !== false && is_numeric($count)) ? intval($count) : '20';
    $activities['totalResults'] = $totalResults;
    $activities['startIndex'] = $startIndex;
    $activities['count'] = $count;
    $query = "
			select
				activities.person_id as person_id,
				activities.id as activity_id,
				activities.title as activity_title,
				activities.body as activity_body,
				activities.created as created
			from
				activities
			where
				activities.person_id in ($ids)
				$activityIdQuery
				$appIdQuery
			order by
				created desc
			limit
				$startIndex, $count
			";
    $res = mysqli_query($this->db, $query);
    if ($res) {
      if (@mysqli_num_rows($res)) {
        while ($row = @mysqli_fetch_array($res, MYSQLI_ASSOC)) {
          $activity = new Activity($row['activity_id'], $row['person_id']);
          $activity->setStreamTitle('activities');
          $activity->setTitle($row['activity_title']);
          $activity->setBody($row['activity_body']);
          $activity->setPostedTime($row['created']);
          $activity->setMediaItems($this->getMediaItems($row['activity_id']));
          $activities[] = $activity;
        }
      } elseif (isset($activityIds) && is_array($activityIds)) {
        // specific activity id was specified, return a not found flag
        return false;
      }
      return $activities;
    } else {
      return false;
    }*/
  }

  public function deleteActivities($userId, $appId, $activityIds) {
	return true; //'A' we haven`t activities yet

	/*
    $this->checkDb();
    $activityIds = array_map('intval', $activityIds);
    $activityIds = implode(',', $activityIds);
    $userId = intval($userId);
    $appId = intval($appId);
    mysqli_query($this->db, "delete from activities where person_id = $userId and app_id = $appId and id in ($activityIds)");
    return (mysqli_affected_rows($this->db) != 0);
	*/
  }

  private function getMediaItems($activity_id) {
	return array(); //'A' we haven`t activities yet
	/*
    $media = array();
    $activity_id = intval($activity_id);
    $res = mysqli_query($this->db, "select mime_type, media_type, url from activity_media_items where activity_id = $activity_id");
    while (list($mime_type, $type, $url) = @mysqli_fetch_row($res)) {
      $media[] = new MediaItem($mime_type, $type, $url);
    }
    return $media;
	*/
  }

  public function getFriendIds($person_id) {
    $this->checkDb();
    $ret = array();
    $person_id = intval($person_id);

	$sqlQuery = "
		SELECT p.*
		FROM `Profiles` AS p
		LEFT JOIN `sys_friend_list` AS f1 ON (f1.`ID` = p.`ID` AND f1.`Profile` ='{$person_id}' AND `f1`.`Check` = 1)
		LEFT JOIN `sys_friend_list` AS f2 ON (f2.`Profile` = p.`ID` AND f2.`ID` ='{$person_id}' AND `f2`.`Check` = 1)
		WHERE 1
		AND (f1.`ID` IS NOT NULL OR f2.`ID` IS NOT NULL)
	";

	if ($vProfiles = mysqli_query($this->db, $sqlQuery)) {
	    while ($aProfiles = mysqli_fetch_assoc($vProfiles)) {
			$ret[] = (int)$aProfiles['ID'];
	    }

	    mysqli_free_result($vProfiles);
	}
    return $ret;
  }

  public function setAppData($person_id, $key, $value, $app_id) {
    $this->checkDb();
    $person_id = intval($person_id);
    $key = mysqli_real_escape_string($this->db, $key);
    $value = mysqli_real_escape_string($this->db, $value);
    $app_id = intval($app_id);
    if (empty($value)) {
      // empty key kind of became to mean "delete data" (was an old orkut hack that became part of the spec spec)
      if (! @mysqli_query($this->db, "DELETE FROM `bx_osi_application_settings` WHERE `application_id` = {$app_id} and `person_id` = {$person_id} and `name` = '{$key}'")) {
        return false;
      }
    } else {
      if (! @mysqli_query($this->db, "INSERT INTO `bx_osi_application_settings` (`application_id`, `person_id`, `name`, `value`) values ({$app_id}, {$person_id}, '{$key}', '{$value}') on duplicate key update value = '{$value}'")) {
        return false;
      }
    }
    return true;
  }

  public function deleteAppData($person_id, $key, $app_id) {
    $this->checkDb();
    $person_id = intval($person_id);
    $app_id = intval($app_id);
    if ($key == '*') {
      if (! @mysqli_query($this->db, "DELETE FROM `bx_osi_application_settings` WHERE `application_id` = {$app_id} and `person_id` = {$person_id}")) {
        return false;
      }
    } else {
      $key = mysqli_real_escape_string($this->db, $key);
      if (! @mysqli_query($this->db, "DELETE FROM `bx_osi_application_settings` WHERE `application_id` = {$app_id} and `person_id` = {$person_id} and `name` = '{$key}'")) {
        return false;
      }
    }
    return true;
  }

  public function getAppData($ids, $keys, $app_id) {
    $this->checkDb();
    $data = array();
    $ids = array_map('intval', $ids);
    if (! isset($keys[0])) {
      $keys[0] = '*';
    }
    if ($keys[0] == '*') {
      $keys = '';
    } elseif (is_array($keys)) {
      foreach ($keys as $key => $val) {
        $keys[$key] = "'" . mysqli_real_escape_string($this->db, $val) . "'";
      }
      $keys = "and `name` in (" . implode(',', $keys) . ")";
    } else {
      $keys = '';
    }
    $res = mysqli_query($this->db, "SELECT `person_id`, `name`, `value` FROM `bx_osi_application_settings` WHERE `application_id` = {$app_id} and `person_id` in (" . implode(',', $ids) . ") {$keys}");
    while (list($person_id, $key, $value) = @mysqli_fetch_row($res)) {
      if (! isset($data[$person_id])) {
        $data[$person_id] = array();
      }
      $data[$person_id][$key] = $value;
    }
    return $data;
  }

	function bx_getAge( $sBirthDate ) { // 28/10/1985
		$bd = explode( '/', $sBirthDate );
		foreach ($bd as $i => $v) $bd[$i] = (int)$v;
		
		if ( date('n') > $bd[1] || ( date('n') == $bd[1] && date('j') >= $bd[0] ) )
			$age = date('Y') - $bd[2];
		else
			$age = date('Y') - $bd[2] - 1;
		
		return $age;
	}

  public function getPeople($ids, $fields, $options, $token) {
    $first = $options->getStartIndex();
    $max = $options->getCount();
    $this->checkDb();
    $ret = array();
    $filterQuery = '';

    $options->setFilterBy(null);

	//DateOfBirth
    $query = "
		SELECT * FROM `Profiles` WHERE `ID` IN (" . implode(',', $ids) . ") {$filterQuery} ORDER BY `ID`
	";
/*
		`ID` AS 'id',
		`NickName` AS 'first_name',
		`NickName` AS 'last_name',
		`DescriptionMe` AS 'about_me',
		20 AS 'age',
		`DateOfBirth` AS 'date_of_birth',
		1 AS 'children',
		'' AS 'ethnicity',
		'' AS 'fashion',
		'' AS 'happiest_when',
		'' AS 'humor',
		'' AS 'job_interests' 
*/

    $res = mysqli_query($this->db, $query);
    if ($res) {
      while ($row = @mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $person_id = $row['ID'];
		$sFirstName = (isset($row['FirstName'])) ? $row['FirstName'] : $row['NickName'];
		$sLastName = (isset($row['LastName'])) ? $row['LastName'] : '';
        $name = new Name($sFirstName . ' ' . $sLastName);
        $name->setGivenName($sFirstName);
        $name->setFamilyName($sLastName);
        $person = new Person($person_id, $name);
        $person->setDisplayName($name->getFormatted());
		$sAboutMe = (isset($row['DescriptionMe'])) ? $row['DescriptionMe'] : '';
        $person->setAboutMe($sAboutMe);

		$sDateOfBirth = (isset($row['DateOfBirth'])) ? date('Y-m-d', $row['DateOfBirth']) : '';
		$sAge = ($sDateOfBirth != '') ? $this->bx_getAge($sDateOfBirth) : '';
        $person->setAge($sAge);
		$sChildren = (isset($row['Children'])) ? $row['Children'] : '';
        $person->setChildren($sChildren);
        $person->setBirthday($sDateOfBirth);
		$sEthnicity = (isset($row['Ethnicity'])) ? $row['Ethnicity'] : '';
        $person->setEthnicity($sEthnicity);
		$sFashion = (isset($row['Fashion'])) ? $row['Fashion'] : '';
        $person->setFashion($sFashion);
		$sHappiestWhen = (isset($row['HappiestWhen'])) ? $row['HappiestWhen'] : '';
        $person->setHappiestWhen($sHappiestWhen);
		$sHumor = (isset($row['Humor'])) ? $row['Humor'] : '';
        $person->setHumor($sHumor);
		$sJobInterests = (isset($row['JobInterests'])) ? $row['JobInterests'] : '';
        $person->setJobInterests($sJobInterests);
		$sLivingArrangement = (isset($row['LivingArrangement'])) ? $row['LivingArrangement'] : '';
        $person->setLivingArrangement($sLivingArrangement);
		$sLookingFor = (isset($row['LookingFor'])) ? $row['LookingFor'] : '';
        $person->setLookingFor($sLookingFor);
		$sNickName = (isset($row['NickName'])) ? $row['NickName'] : '';
        $person->setNickname($sNickName);
		$sPets = (isset($row['Pets'])) ? $row['Pets'] : '';
        $person->setPets($sPets);
		$sPoliticalViews = (isset($row['PoliticalViews'])) ? $row['PoliticalViews'] : '';
        $person->setPoliticalViews($sPoliticalViews);
		$sProfileSong = (isset($row['ProfileSong'])) ? $row['ProfileSong'] : '';
        $person->setProfileSong($sProfileSong);
        $person->setProfileUrl($this->url_prefix . '/profile/' . $person_id); //'A' TODO
		$sProfileVideo = (isset($row['ProfileVideo'])) ? $row['ProfileVideo'] : '';
        $person->setProfileVideo($sProfileVideo);
		$sRelationshipStatus = (isset($row['RelationshipStatus'])) ? $row['RelationshipStatus'] : '';
        $person->setRelationshipStatus($sRelationshipStatus);
		$sReligion = (isset($row['Religion'])) ? $row['Religion'] : '';
        $person->setReligion($sReligion);
		$sRomance = (isset($row['Romance'])) ? $row['Romance'] : '';
        $person->setRomance($sRomance);
		$sScaredOf = (isset($row['ScaredOf'])) ? $row['ScaredOf'] : '';
        $person->setScaredOf($sScaredOf);
		$sSexualOrientation = (isset($row['SexualOrientation'])) ? $row['SexualOrientation'] : '';
        $person->setSexualOrientation($sSexualOrientation);
        $person->setStatus($row['UserStatus']);
        $person->setThumbnailUrl(! empty($row['thumbnail_url']) ? $this->url_prefix . $row['thumbnail_url'] : ''); //'A' TODO
        if (! empty($row['thumbnail_url'])) {
          // also report thumbnail_url in standard photos field (this is the only photo supported by partuza)
          $person->setPhotos(array(new Photo($this->url_prefix . $row['thumbnail_url'], 'thumbnail', true)));
        }
		$sUtcOffset = (isset($row['TimeZone'])) ? $row['TimeZone'] : "-00:00";
        $person->setUtcOffset(sprintf('%+03d:00', $sUtcOffset)); // force "-00:00" utc-offset format
        if (! empty($row['Drinker'])) {
          $person->setDrinker($row['Drinker']);
        }
        if (! empty($row['Sex'])) {
          $person->setGender(strtolower($row['Sex']));
        }
        if (! empty($row['Smoker'])) {
          $person->setSmoker($row['Smoker']);
        }

        /* the following fields require additional queries so are only executed if requested */
        if (isset($fields['activities']) || in_array('@all', $fields)) {
          $activities = array(); //'A' we haven`t activities yet
          /*$res2 = mysqli_query($this->db, "select activity from person_activities where person_id = " . $person_id);
          while (list($activity) = @mysqli_fetch_row($res2)) {
            $activities[] = $activity;
          }*/
          $person->setActivities($activities);
        }
        if (isset($fields['addresses']) || in_array('@all', $fields)) {
          $addresses = array();
          //$res2 = mysqli_query($this->db, "select addresses.* from person_addresses, addresses where addresses.id = person_addresses.address_id and person_addresses.person_id = " . $person_id);

		  /*$sAddrSQL = "
			select 
			'' AS 'unstructured_address',
			'' AS 'street_address',
			`City` AS 'region',
			`Country` AS 'country',
			'' AS 'latitude',
			'' AS 'longitude',
			'' AS 'locality',
			`zip` AS 'postal_code',
			'' AS 'address_type'
			from `Profiles`
			where `Profiles`.`ID` = {$person_id}
			";
		  $res2 = mysqli_query($this->db, $sAddrSQL);

          while ($row = @mysqli_fetch_array($res2, MYSQLI_ASSOC))*/ {
			$sCountry = (isset($row['Country'])) ? $row['Country'] : '';
			$sRegion = (isset($row['City'])) ? $row['City'] : ''; //'A'  region -> city
			$sZip = (isset($row['zip'])) ? $row['zip'] : '';
            if (empty($row['unstructured_address'])) {
              $row['unstructured_address'] = trim($row['street_address'] . " " . $sRegion . " " . $sCountry);
            }
            $addres = new Address($row['unstructured_address']);
            $addres->setCountry($sCountry);
			if (! empty($row['latitude'])) {
				$addres->setLatitude($row['latitude']);
			}
			if (! empty($row['longitude'])) {
				$addres->setLongitude($row['longitude']);
			}
			if (! empty($row['locality'])) {
				$addres->setLocality($row['locality']);
			}
            $addres->setPostalCode($sZip);
            $addres->setRegion($sRegion);
			if (! empty($row['street_address'])) {
				$addres->setStreetAddress($row['street_address']);
			}
			if (! empty($row['street_address'])) {
				$addres->setType($row['street_address']);
			}
            //FIXME quick and dirty hack to demo PC
            $addres->setPrimary(true);
            $addresses[] = $addres;
          }
          $person->setAddresses($addresses);
        }
        if (isset($fields['bodyType']) || in_array('@all', $fields)) { //'A' we haven`t bodyType at all
          /*$res2 = mysqli_query($this->db, "select * from person_body_type where person_id = " . $person_id);
          if (@mysqli_num_rows($res2)) {
            $row = @mysql_fetch_array($res2, MYSQLI_ASSOC);
            $bodyType = new BodyType();
            $bodyType->setBuild($row['build']);
            $bodyType->setEyeColor($row['eye_color']);
            $bodyType->setHairColor($row['hair_color']);
            $bodyType->setHeight($row['height']);
            $bodyType->setWeight($row['weight']);
            $person->setBodyType($bodyType);
          }*/
        }
        if (isset($fields['books']) || in_array('@all', $fields)) { //'A' we haven`t books at all
          /*$books = array();
          $res2 = mysqli_query($this->db, "select book from person_books where person_id = " . $person_id);
          while (list($book) = @mysqli_fetch_row($res2)) {
            $books[] = $book;
          }
          $person->setBooks($books);*/
        }
        if (isset($fields['cars']) || in_array('@all', $fields)) { //'A' we haven`t cars at all
          /*$cars = array();
          $res2 = mysqli_query($this->db, "select car from person_cars where person_id = " . $person_id);
          while (list($car) = @mysqli_fetch_row($res2)) {
            $cars[] = $car;
          }
          $person->setCars($cars);*/
        }
        if (isset($fields['currentLocation']) || in_array('@all', $fields)) { //'A' we haven`t currentLocation at all
         /*$addresses = array();
          $res2 = mysqli_query($this->db, "select addresses.* from person_current_location, person_addresses, addresses where addresses.id = person_current_location.address_id and person_addresses.person_id = " . $person_id);
          if (@mysqli_num_rows($res2)) {
            $row = mysqli_fetch_array($res2, MYSQLI_ASSOC);
            if (empty($row['unstructured_address'])) {
              $row['unstructured_address'] = trim($row['street_address'] . " " . $row['region'] . " " . $row['country']);
            }
            $addres = new Address($row['unstructured_address']);
            $addres->setCountry($row['country']);
            $addres->setLatitude($row['latitude']);
            $addres->setLongitude($row['longitude']);
            $addres->setLocality($row['locality']);
            $addres->setPostalCode($row['postal_code']);
            $addres->setRegion($row['region']);
            $addres->setStreetAddress($row['street_address']);
            $addres->setType($row['address_type']);
            $person->setCurrentLocation($addres);
          }*/
        }
        if (isset($fields['emails']) || in_array('@all', $fields)) {
          $emails = array();
		  //'A' we haven`t multi emails, only single
          /*$res2 = mysqli_query($this->db, "select address, email_type from person_emails where person_id = " . $person_id);
          while (list($address, $type) = @mysqli_fetch_row($res2)) {
            $emails[] = new Email(strtolower($address), $type); // TODO: better email canonicalization; remove dups
          }
          $person->setEmails($emails);*/

          $sEmail = (isset($row['Email'])) ? $row['Email'] : '';
		  $emails[] = new Email(strtolower($sEmail), 'main');
		  $person->setEmails($emails);
        }
        if (isset($fields['food']) || in_array('@all', $fields)) { //'A' we haven`t food at all
          /*$foods = array();
          $res2 = mysqli_query($this->db, "select food from person_foods where person_id = " . $person_id);
          while (list($food) = @mysqli_fetch_row($res2)) {
            $foods[] = $food;
          }
          $person->setFood($foods);*/
        }
        if (isset($fields['heroes']) || in_array('@all', $fields)) { //'A' we haven`t heroes at all
          /*$strings = array();
          $res2 = mysqli_query($this->db, "select hero from person_heroes where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setHeroes($strings);*/
        }
        if (isset($fields['interests']) || in_array('@all', $fields)) { //'A' we haven`t interests at all
         /* $strings = array();
          $res2 = mysqli_query($this->db, "select interest from person_interests where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setInterests($strings);*/
        }
        $organizations = array();
        $fetchedOrg = false;
        if (isset($fields['jobs']) || in_array('@all', $fields)) { //'A' we haven`t jobs at all
          /*$res2 = mysqli_query($this->db, "select organizations.* from person_jobs, organizations where organizations.id = person_jobs.organization_id and person_jobs.person_id = " . $person_id);
          while ($row = mysqli_fetch_array($res2, MYSQLI_ASSOC)) {
            $organization = new Organization();
            $organization->setDescription($row['description']);
            $organization->setEndDate($row['end_date']);
            $organization->setField($row['field']);
            $organization->setName($row['name']);
            $organization->setSalary($row['salary']);
            $organization->setStartDate($row['start_date']);
            $organization->setSubField($row['sub_field']);
            $organization->setTitle($row['title']);
            $organization->setWebpage($row['webpage']);
            $organization->setType('job');
            if ($row['address_id']) {
              $res3 = mysqli_query($this->db, "select * from addresses where id = " . $row['address_id']);
              if (mysqli_num_rows($res3)) {
                $row = mysqli_fetch_array($res3, MYSQLI_ASSOC);
                if (empty($row['unstructured_address'])) {
                  $row['unstructured_address'] = trim($row['street_address'] . " " . $row['region'] . " " . $row['country']);
                }
                $addres = new Address($row['unstructured_address']);
                $addres->setCountry($row['country']);
                $addres->setLatitude($row['latitude']);
                $addres->setLongitude($row['longitude']);
                $addres->setLocality($row['locality']);
                $addres->setPostalCode($row['postal_code']);
                $addres->setRegion($row['region']);
                $addres->setStreetAddress($row['street_address']);
                $addres->setType($row['address_type']);
                $organization->setAddress($address);
              }
            }
            $organizations[] = $organization;
          }
          $fetchedOrg = true;*/
        }
        if (isset($fields['schools']) || in_array('@all', $fields)) { //'A' we haven`t schools at all
          /*$res2 = mysqli_query($this->db, "select organizations.* from person_schools, organizations where organizations.id = person_schools.organization_id and person_schools.person_id = " . $person_id);
          while ($row = mysqli_fetch_array($res2, MYSQLI_ASSOC)) {
            $organization = new Organization();
            $organization->setDescription($row['description']);
            $organization->setEndDate($row['end_date']);
            $organization->setField($row['field']);
            $organization->setName($row['name']);
            $organization->setSalary($row['salary']);
            $organization->setStartDate($row['start_date']);
            $organization->setSubField($row['sub_field']);
            $organization->setTitle($row['title']);
            $organization->setWebpage($row['webpage']);
            $organization->setType($row['school']);
            if ($row['address_id']) {
              $res3 = mysqli_query($this->db, "select * from addresses where id = " . $row['address_id']);
              if (mysqli_num_rows($res3)) {
                $row = mysqli_fetch_array($res3, MYSQLI_ASSOC);
                if (empty($row['unstructured_address'])) {
                  $row['unstructured_address'] = trim($row['street_address'] . " " . $row['region'] . " " . $row['country']);
                }
                $addres = new Address($row['unstructured_address']);
                $addres->setCountry($row['country']);
                $addres->setLatitude($row['latitude']);
                $addres->setLongitude($row['longitude']);
                $addres->setLocality($row['locality']);
                $addres->setPostalCode($row['postal_code']);
                $addres->setRegion($row['region']);
                $addres->setStreetAddress($row['street_address']);
                $addres->setType($row['address_type']);
                $organization->setAddress($address);
              }
            }
            $organizations[] = $organization;
          }
          $fetchedOrg = true;*/
        }
        if ($fetchedOrg) {
          $person->setOrganizations($organizations);
        }
        //TODO languagesSpoken, currently missing the languages / countries tables so can't do this yet
        if (isset($fields['movies']) || in_array('@all', $fields)) { //'A' possible after
          /*$strings = array();
          $res2 = mysqli_query($this->db, "select movie from person_movies where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setMovies($strings);*/
        }
        if (isset($fields['music']) || in_array('@all', $fields)) { //'A' possible after
          /*$strings = array();
          $res2 = mysqli_query($this->db, "select music from person_music where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setMusic($strings);*/
        }
        if (isset($fields['phoneNumbers']) || in_array('@all', $fields)) { //'A' we haven`t phoneNumbers at all
          /*$numbers = array();
          $res2 = mysqli_query($this->db, "select number, number_type from person_phone_numbers where person_id = " . $person_id);
          while (list($number, $type) = @mysqli_fetch_row($res2)) {
            $numbers[] = new Phone($number, $type);
          }
          $person->setPhoneNumbers($numbers);*/
        }
        if (isset($fields['ims']) || in_array('@all', $fields)) { //'A' we haven`t ims at all
          /*$ims = array();
          $res2 = mysqli_query($this->db, "select value, value_type from person_ims where person_id = " . $person_id);
          while (list($value, $type) = @mysqli_fetch_row($res2)) {
            $ims[] = new Im($value, $type);
          }
          $person->setIms($ims);*/
        }
        if (isset($fields['accounts']) || in_array('@all', $fields)) { //'A' we haven`t accounts at all
          /*$accounts = array();
          $res2 = mysqli_query($this->db, "select domain, userid, username from person_accounts where person_id = " . $person_id);
          while (list($domain, $userid, $username) = @mysqli_fetch_row($res2)) {
            $accounts[] = new Account($domain, $userid, $username);
          }
          $person->setAccounts($accounts);*/
        }
        if (isset($fields['quotes']) || in_array('@all', $fields)) { //'A' we haven`t quotes at all
          /*$strings = array();
          $res2 = mysqli_query($this->db, "select quote from person_quotes where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setQuotes($strings);*/
        }
        if (isset($fields['sports']) || in_array('@all', $fields)) { //'A' we haven`t sports at all
          /*$strings = array();
          $res2 = mysqli_query($this->db, "select sport from person_sports where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setSports($strings);*/
        }
        if (isset($fields['tags']) || in_array('@all', $fields)) {
          $strings = array();
		  if (! empty($row['latitude'])) {
			$aProfileTags = preg_split("/[;,\s]/", $_REQUEST['tags']);
			foreach ($aProfileTags as $sTag)
				$strings[] = $sTag;
          /*$res2 = mysqli_query($this->db, "select tag from person_tags where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }*/
			$person->setTags($strings);
		  }
        }

        if (isset($fields['turnOns']) || in_array('@all', $fields)) { //'A' we haven`t turnOns at all
          /*$strings = array();
          $res2 = mysqli_query($this->db, "select turn_on from person_turn_ons where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setTurnOns($strings);*/
        }
        if (isset($fields['turnOffs']) || in_array('@all', $fields)) { //'A' we haven`t turnOffs at all
          /*$strings = array();
          $res2 = mysqli_query($this->db, "select turn_off from person_turn_offs where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setTurnOffs($strings);*/
        }
        if (isset($fields['urls']) || in_array('@all', $fields)) { //'A' we haven`t urls at all
          /*$strings = array();
          $res2 = mysqli_query($this->db, "select url from person_urls where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = new Url($data, null, null);
          }
          $strings[] = new Url($this->url_prefix . '/profile/' . $person_id, null, 'profile'); // always include profile URL
          $person->setUrls($strings);*/
        }
        $ret[$person_id] = $person;
      }
    }
    try {
      $ret = $this->filterResults($ret, $options);
      $ret['totalSize'] = count($ret);
    } catch (Exception $e) {
      $ret['totalSize'] = count($ret) - 1;
      $ret['filtered'] = 'false';
    }
    if ($first !== false && $max !== false && is_numeric($first) && is_numeric($max) && $first >= 0 && $max > 0) {
      $count = 0;
      $result = array();
      foreach ($ret as $id => $person) {
        if ($id == 'totalSize' || $id == 'filtered') {
          $result[$id] = $person;
          continue;
        }
        if ($count >= $first && $count < $first + $max) {
          $result[$id] = $person;
        }
        ++ $count;
      }
      return $result;
    } else {
      return $ret;
    }
  }

  private function filterResults($peopleById, $options) {
    if (! $options->getFilterBy()) {
      return $peopleById; // no filtering specified
    }
    $filterBy = $options->getFilterBy();
    $op = $options->getFilterOperation();
    if (! $op) {
      $op = CollectionOptions::FILTER_OP_EQUALS; // use this container-specific default
    }
    $value = $options->getFilterValue();
    $filteredResults = array();
    $numFilteredResults = 0;
    foreach ($peopleById as $id => $person) {
      if ($person instanceof Person) {
        if ($this->passesFilter($person, $filterBy, $op, $value)) {
          $filteredResults[$id] = $person;
          $numFilteredResults ++;
        }
      } else {
        $filteredResults[$id] = $person; // copy extra metadata verbatim
      }
    }
    if (! isset($filteredResults['totalSize'])) {
      $filteredResults['totalSize'] = $numFilteredResults;
    }
    return $filteredResults;
  }

  private function passesFilter($person, $filterBy, $op, $value) {
    $fieldValue = $person->getFieldByName($filterBy);
    if ($fieldValue instanceof ComplexField) {
      $fieldValue = $fieldValue->getPrimarySubValue();
    }
    if (! $fieldValue || (is_array($fieldValue) && ! count($fieldValue))) {
      return false; // person is missing the field being filtered for
    }
    if ($op == CollectionOptions::FILTER_OP_PRESENT) {
      return true; // person has a non-empty value for the requested field
    }
    if (! $value) {
      return false; // can't do an equals/startswith/contains filter on an empty filter value
    }
    // grab string value for comparison
    if (is_array($fieldValue)) {
      // plural fields match if any instance of that field matches
      foreach ($fieldValue as $field) {
        if ($field instanceof ComplexField) {
          $field = $field->getPrimarySubValue();
        }
        if ($this->passesStringFilter($field, $op, $value)) {
          return true;
        }
      }
    } else {
      return $this->passesStringFilter($fieldValue, $op, $value);
    }

    return false;
  }

  private function passesStringFilter($fieldValue, $op, $filterValue) {
    switch ($op) {
      case CollectionOptions::FILTER_OP_EQUALS:
        return $fieldValue == $filterValue;
      case CollectionOptions::FILTER_OP_CONTAINS:
        return stripos($fieldValue, $filterValue) !== false;
      case CollectionOptions::FILTER_OP_STARTSWITH:
        return stripos($fieldValue, $filterValue) === 0;
      default:
        throw new Exception('unrecognized filterOp');
    }
  }
}
