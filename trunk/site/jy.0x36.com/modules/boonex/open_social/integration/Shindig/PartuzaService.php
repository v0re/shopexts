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

/**
 * Implementation of supported services backed using Partuza's DB Fetcher
 */
class PartuzaService implements ActivityService, PersonService, AppDataService, MessagesService {

  public function getPerson($userId, $groupId, $fields, SecurityToken $token) {
    if (! is_object($userId)) {
      $userId = new UserId('userId', $userId);
      $groupId = new GroupId('self', 'all');
    }
    $person = $this->getPeople($userId, $groupId, new CollectionOptions(), $fields, $token);
    if (is_array($person->getEntry())) {
      $person = $person->getEntry();
      if (is_array($person) && count($person) == 1) {
        return array_pop($person);
      }
    }
    throw new SocialSpiException("Person not found", ResponseError::$BAD_REQUEST);
  }

  public function getPeople($userId, $groupId, CollectionOptions $options, $fields, SecurityToken $token) {
    $ids = $this->getIdSet($userId, $groupId, $token);
    $allPeople = PartuzaDbFetcher::get()->getPeople($ids, $fields, $options, $token);
    $totalSize = $allPeople['totalSize'];
    $people = array();
    foreach ($ids as $id) {
      $person = null;
      if (is_array($allPeople) && isset($allPeople[$id])) {
        $person = $allPeople[$id];
        if (! $token->isAnonymous() && $id == $token->getViewerId()) {
          $person->setIsViewer(true);
        }
        if (! $token->isAnonymous() && $id == $token->getOwnerId()) {
          $person->setIsOwner(true);
        }
        if (! in_array('@all', $fields)) {
          $newPerson = array();
          $newPerson['isOwner'] = $person->isOwner;
          $newPerson['isViewer'] = $person->isViewer;
          $newPerson['displayName'] = $person->displayName;
          // Force these fields to always be present
          $fields[] = 'id';
          $fields[] = 'displayName';
          $fields[] = 'thumbnailUrl';
          $fields[] = 'profileUrl';
          foreach ($fields as $field) {
            if (isset($person->$field) && ! isset($newPerson[$field])) {
              $newPerson[$field] = $person->$field;
            }
          }
          $person = $newPerson;
        }
        array_push($people, $person);
      }
    }
    $sorted = $this->sortPersonResults($people, $options);
    $collection = new RestfulCollection($people, $options->getStartIndex(), $totalSize);
    $collection->setItemsPerPage($options->getCount());
    if (! $sorted) {
      $collection->setSorted(false); // record that we couldn't sort as requested
    }
    if ($options->getUpdatedSince()) {
      $collection->setUpdatedSince(false); // we can never process an updatedSince request
    }
    return $collection;
  }

  public function deletePersonData($userId, GroupId $groupId, $appId, $fields, SecurityToken $token) {
    $ids = $this->getIdSet($userId, $groupId, $token);
    if (count($ids) < 1) {
      throw new InvalidArgumentException("No userId specified");
    } elseif (count($ids) > 1) {
      throw new InvalidArgumentException("Multiple userIds not supported");
    }
    $userId = $ids[0];
    if ($fields == null) {
      if (! PartuzaDbFetcher::get()->deleteAppData($userId, '*', $appId)) {
        throw new SocialSpiException("Internal server error", ResponseError::$INTERNAL_ERROR);
      }
    } else {
      foreach ($fields as $key) {
        if (! self::isValidKey($key) && $key != '*') {
          throw new SocialSpiException("The person app data key had invalid characters", ResponseError::$BAD_REQUEST);
        }
      }
      foreach ($fields as $key) {
        if (! PartuzaDbFetcher::get()->deleteAppData($userId, $key, $appId)) {
          throw new SocialSpiException("Internal server error", ResponseError::$INTERNAL_ERROR);
        }
      }
    }
  }

  public function getPersonData($userId, GroupId $groupId, $appId, $fields, SecurityToken $token) {
    $ids = $this->getIdSet($userId, $groupId, $token);
    $data = PartuzaDbFetcher::get()->getAppData($ids, $fields, $appId);
    // If the data array is empty, return empty DataCollection.
    return new DataCollection($data);
  }

  public function updatePersonData(UserId $userId, GroupId $groupId, $appId, $fields, $values, SecurityToken $token) {
    if ($userId->getUserId($token) == null) {
      throw new SocialSpiException("Unknown person id.", ResponseError::$NOT_FOUND);
    }
    foreach ($fields as $key) {
      if (! self::isValidKey($key)) {
        throw new SocialSpiException("The person app data key had invalid characters", ResponseError::$BAD_REQUEST);
      }
    }
    switch ($groupId->getType()) {
      case 'self':
        foreach ($fields as $key) {
          $value = isset($values[$key]) ? $values[$key] : null;
          if (! PartuzaDbFetcher::get()->setAppData($userId->getUserId($token), $key, $value, $appId)) {
            throw new SocialSpiException("Internal server error", ResponseError::$INTERNAL_ERROR);
          }
        }
        break;
      default:
        throw new SocialSpiException("We don't support updating data in batches yet", ResponseError::$NOT_IMPLEMENTED);
        break;
    }
  }

  public function getActivity($userId, $groupId, $appdId, $fields, $activityId, SecurityToken $token) {
    $activities = $this->getActivities($userId, $groupId, $appdId, null, null, null, null, 0, 20, $fields, array($activityId), $token);
    if ($activities instanceof RestFulCollection) {
      $activities = $activities->getEntry();
      foreach ($activities as $activity) {
        if ($activity->getId() == $activityId) {
          return $activity;
        }
      }
    }
    throw new SocialSpiException("Activity not found", ResponseError::$NOT_FOUND);
  }

  public function getActivities($userIds, $groupId, $appId, $sortBy, $filterBy, $filterOp, $filterValue, $startIndex, $count, $fields, $activityIds, $token) {
    $ids = $this->getIdSet($userIds, $groupId, $token);
    $activities = PartuzaDbFetcher::get()->getActivities($ids, $appId, $sortBy, $filterBy, $filterOp, $filterValue, $startIndex, $count, $fields, $activityIds);
    if ($activities) {
      $totalResults = $activities['totalResults'];
      $startIndex = $activities['startIndex'];
      $count = $activities['count'];
      unset($activities['totalResults']);
      unset($activities['startIndex']);
      unset($activities['count']);
      $ret = new RestfulCollection($activities, $startIndex, $totalResults);
      $ret->setItemsPerPage($count);
      return $ret;
    } else {
      throw new SocialSpiException("Invalid activity specified", ResponseError::$NOT_FOUND);
    }
  }

  public function createActivity($userId, $groupId, $appId, $fields, $activity, SecurityToken $token) {
    try {
      if ($token->getOwnerId() != $token->getViewerId() || $token->getViewerId() != $userId->getUserId($token)) {
        throw new SocialSpiException("Create activity permission denied.", ResponseError::$UNAUTHORIZED);
      }
      PartuzaDbFetcher::get()->createActivity($userId->getUserId($token), $activity, $token->getAppId());
    } catch (SocialSpiException $e) {
      throw $e;
    } catch (Exception $e) {
      throw new SocialSpiException("Invalid create activity request: " . $e->getMessage(), ResponseError::$INTERNAL_ERROR);
    }
  }

  public function deleteActivities($userId, $groupId, $appId, $activityIds, SecurityToken $token) {
    $ids = $this->getIdSet($userId, $groupId, $token);
    if (count($ids) < 1 || count($ids) > 1) {
      throw new SocialSpiException("Invalid user id or count", ResponseError::$BAD_REQUEST);
    }
    if (! PartuzaDbFetcher::get()->deleteActivities($ids[0], $appId, $activityIds)) {
      throw new SocialSpiException("Invalid activity id(s)", ResponseError::$NOT_FOUND);
    }
  }

  public function createMessage($userId, $appId, $message, $optionalMessageId, SecurityToken $token) {
    PartuzaDbFetcher::get()->createMessage($userId->getUserId($token), $appId, $message);
  }

  /**
   * Get the set of user id's from a user or collection of users, and group
   */
  private function getIdSet($user, GroupId $group, SecurityToken $token) {
    $ids = array();
    if ($user instanceof UserId) {
      $userId = $user->getUserId($token);
      if ($group == null) {
        return array($userId);
      }
      switch ($group->getType()) {
        case 'all':
        case 'friends':
        case 'groupId':
          $friendIds = PartuzaDbFetcher::get()->getFriendIds($userId);
          if (is_array($friendIds) && count($friendIds)) {
            $ids = $friendIds;
          }
          break;
        case 'self':
          $ids[] = $userId;
          break;
      }
    } elseif (is_array($user)) {
      $ids = array();
      foreach ($user as $id) {
        $ids = array_merge($ids, $this->getIdSet($id, $group, $token));
      }
    }
    return $ids;
  }

  /**
   * Determines whether the input is a valid key. Valid keys match the regular
   * expression [\w\-\.]+.
   *
   * @param key the key to validate.
   * @return true if the key is a valid appdata key, false otherwise.
   */
  public static function isValidKey($key) {
    if (empty($key)) {
      return false;
    }
    for ($i = 0; $i < strlen($key); ++ $i) {
      $c = substr($key, $i, 1);
      if (($c >= 'a' && $c <= 'z') || ($c >= 'A' && $c <= 'Z') || ($c >= '0' && $c <= '9') || ($c == '-') || ($c == '_') || ($c == '.')) {
        continue;
      }
      return false;
    }
    return true;
  }

  private function sortPersonResults(&$people, $options) {
    if (! $options->getSortBy()) {
      return true; // trivially sorted
    }
    // for now, partuza can only sort by displayName, which also demonstrates returning sorted: false
    if ($options->getSortBy() != 'displayName') {
      return false;
    }
    usort($people, array($this, 'comparator'));
    if ($options->getSortOrder() != CollectionOptions::SORT_ORDER_ASCENDING) {
      $people = array_reverse($people);
    }
    return true;
  }

  private function comparator($person, $person1) {
    $name = ($person instanceof Person ? $person->getDisplayName() : $person['displayName']);
    $name1 = ($person1 instanceof Person ? $person1->getDisplayName() : $person1['displayName']);
    return strnatcasecmp($name, $name1);
  }
}
