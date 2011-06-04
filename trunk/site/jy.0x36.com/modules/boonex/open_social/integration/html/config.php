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

$aParsedSiteUrl = parse_url($site['url']);
$sDolpSubFolder = (is_array($aParsedSiteUrl) && $aParsedSiteUrl['path'] != '') ? rtrim($aParsedSiteUrl['path'], '/') : '';

$config = array(
// Language to use, used for gettext / setenv LC_ALL
'language' => 'en_US',

// prefix of where partuza lives, empty means it's /
'web_prefix' => $sDolpSubFolder . '/modules/boonex/open_social/integration/html',

// Container (formaly known as syndicator) to pass in the iframe (defaults to 'default')
// Note: your shindig config/container.js needs to match this key, so if you changed this to 'partuza'
// you need to edit container.js and change the container key there like:
// {"gadgets.container" : ["partuza"],
'container' => 'default',

// gadget server url
'gadget_server' => BX_DOL_URL_ROOT . 'modules/boonex/open_social/shindig/php',

// The url of this partuza instalation, including the (optional) web_prefix
'partuza_url' => BX_DOL_URL_ROOT . 'modules/boonex/open_social/integration/html/',

// Max age of a security token, defaults to one hour
'st_max_age' => 60 * 60,

// Allow plain text tokens, disable this on live systems
'allow_plaintext_token' => true,

// Security token keys, this is a shared secret between shindig and partuza so make sure you set them to the same value in both
'token_cipher_key' => 'INSECURE_DEFAULT_KEY',
'token_hmac_key' => 'INSECURE_DEFAULT_KEY',

// MySql server settings
'db_host' => $db['host'],
'db_user' => $db['user'],
'db_passwd' => $db['passwd'],
'db_database' => $db['db'],
'db_port' => ($db['port'] != '' ? $db['port'] : '3306'),

'data_cache' => 'CacheStorageFile',
// If you use CacheStorageMemcache as caching backend, change these to the memcache server settings
'cache_host' => 'localhost',
'cache_port' => 11211,
'cache_time' => 24 * 60 * 60,

// If you use CacheStorageFile as caching backend, this is the directory where it stores the temporary files
// Right now you should set this to the same directory as shindig, else the cache invalidations won't
// apply to both shindig and partuza
'cache_root' => '/tmp/shindig',

/* No need to edit the settings below in general, unless you modified the directory layout.
 * Note: On production systems it's faster to put absolute paths here instead of using these dynamicly generated ones
*/
'site_root' => realpath(dirname(__FILE__)),
'library_root' => realpath(dirname(__FILE__) . "/../Library"),
'application_root' => realpath(dirname(__FILE__) . "/../Application"),
'views_root' => realpath(dirname(__FILE__) . "/../Application/Views"),
'models_root' => realpath(dirname(__FILE__) . "/../Application/Models"),
'controllers_root' => realpath(dirname(__FILE__) . "/../Application/Controllers")
);


//print_r($config);