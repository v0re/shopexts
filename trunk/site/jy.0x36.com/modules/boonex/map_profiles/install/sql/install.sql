-- create tables
CREATE TABLE IF NOT EXISTS `[db_prefix]profiles` (
  `id` int(10) unsigned NOT NULL,
  `ts` int(10) unsigned NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `zoom` tinyint(4) NOT NULL default '-1',  
  `type` char(16) NOT NULL,
  `address` varchar(255) NOT NULL,
  `country` varchar(2) NOT NULL,
  `allow_view_location_to` int(11) NOT NULL default '3',
  `failed` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `lat` (`lat`),
  KEY `lng` (`lng`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]cities` (
  `country` varchar(2) NOT NULL,
  `city` varchar(255) NOT NULL,
  `lat` float NOT NULL,
  `lng` float NOT NULL,
  `failed` tinyint(4) NOT NULL,
  PRIMARY KEY  (`country`,`city`),
  KEY `lat` (`lat`),
  KEY `lng` (`lng`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]countries` (
  `country` varchar(2) NOT NULL,
  `lat` float NOT NULL,
  `lng` float NOT NULL,
  `failed` tinyint(4) NOT NULL,
  PRIMARY KEY  (`country`),
  KEY `lat` (`lat`),
  KEY `lng` (`lng`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `[db_prefix]countries` (`country`, `lat`, `lng`, `failed`) VALUES
('AD', 42.5462, 1.60155, 0),('AE', 23.4241, 53.8478, 0),('AF', 33.9391, 67.71, 0),('AG', 17.0608, -61.7964, 0),
('AI', 18.2206, -63.0686, 0),('AL', 41.1533, 20.1683, 0),('AM', 40.0691, 45.0382, 0),('AN', 12.2261, -69.0601, 0),
('AO', -11.2027, 17.8739, 0),('AQ', -75.251, -0.071389, 0),('AR', -38.4161, -63.6167, 0),('AS', -14.271, -170.132, 0),
('AT', 47.5162, 14.5501, 0),('AU', -25.2744, 133.775, 0),('AW', 12.5211, -69.9683, 0),('AZ', 40.1431, 47.5769, 0),
('BA', 43.9159, 17.6791, 0),('BB', 13.1939, -59.5432, 0),('BD', 23.685, 90.3563, 0),('BE', 50.5039, 4.46994, 0),
('BF', 12.2383, -1.56159, 0),('BG', 42.7339, 25.4858, 0),('BH', 25.9304, 50.6378, 0),('BI', -3.37306, 29.9189, 0),
('BJ', 9.30769, 2.31583, 0),('BM', 32.3214, -64.7574, 0),('BN', 4.53528, 114.728, 0),('BO', -16.2902, -63.5887, 0),
('BR', -14.235, -51.9253, 0),('BS', 25.0343, -77.3963, 0),('BT', 27.5142, 90.4336, 0),('BV', -54.4232, 3.41319, 0),
('BW', -22.3285, 24.6849, 0),('BY', 53.7098, 27.9534, 0),('BZ', 17.1899, -88.4977, 0),('CA', 56.1304, -106.347, 0),
('CC', 0, 0, 1),('CD', -4.03833, 21.7587, 0),('CF', 6.61111, 20.9394, 0),('CG', -1.11313, 17.0185, 0),
('CH', 46.8182, 8.22751, 0),('CI', 7.53999, -5.54708, 0),('CK', -21.2367, -159.778, 0),('CL', -35.6751, -71.543, 0),
('CM', 7.36972, 12.3547, 0),('CN', 35.8617, 104.195, 0),('CO', 4.57087, -74.2973, 0),('CR', 9.74892, -83.7534, 0),
('CU', 21.5218, -77.7812, 0),('CV', 16.0021, -24.0132, 0),('CX', -10.4475, 105.69, 0),('CY', 35.1264, 33.4299, 0),
('CZ', 49.8175, 15.473, 0),('DE', 51.1657, 10.4515, 0),('DJ', 11.8251, 42.5903, 0),('DK', 56.2639, 9.50179, 0),
('DM', 15.415, -61.371, 0),('DO', 18.7357, -70.1627, 0),('DZ', 28.0339, 1.65963, 0),('EC', -1.83124, -78.1834, 0),
('EE', 58.5953, 25.0136, 0),('EG', 26.8206, 30.8025, 0),('EH', 24.2155, -12.8858, 0),('ER', 15.1794, 39.7823, 0),
('ES', 40.4637, -3.74922, 0),('ET', 9.145, 40.4897, 0),('FI', 61.9241, 25.7482, 0),('FJ', -16.5782, 179.414, 0),
('FK', -51.7963, -59.5236, 0),('FM', 0, 0, 1),('FO', 61.8926, -6.91181, 0),('FR', 46.2276, 2.21375, 0),
('GA', -0.803689, 11.6094, 0),('GB', 55.3781, -3.43597, 0),('GD', 0, 0, 1),('GE', 0, 0, 1),
('GF', 3.93389, -53.1258, 0),('GH', 7.94653, -1.02319, 0),('GI', 36.1377, -5.34537, 0),('GL', 71.7069, -42.6043, 0),
('GM', 13.4432, -15.3101, 0),('GN', 9.94559, -9.69664, 0),('GP', 16.996, -62.0676, 0),('GQ', 1.6508, 10.2679, 0),
('GR', 39.0742, 21.8243, 0),('GS', -54.4296, -36.5879, 0),('GT', 15.7835, -90.2308, 0),('GU', 13.4443, 144.794, 0),
('GW', 11.8037, -15.1804, 0),('GY', 4.86042, -58.9302, 0),('HK', 22.3964, 114.109, 0),('HM', -53.0818, 73.5042, 0),
('HN', 15.2, -86.2419, 0),('HR', 44.4662, 16.4612, 0),('HT', 18.9712, -72.2852, 0),('HU', 47.1625, 19.5033, 0),
('ID', -0.789275, 113.921, 0),('IE', 53.4129, -8.24389, 0),('IL', 31.0461, 34.8516, 0),('IN', 20.5937, 78.9629, 0),
('IO', -6.34319, 71.8765, 0),('IQ', 33.2232, 43.6793, 0),('IR', 32.4279, 53.688, 0),('IS', 64.9631, -19.0208, 0),
('IT', 41.8719, 12.5674, 0),('JM', 18.1096, -77.2975, 0),('JO', 30.5852, 36.2384, 0),('JP', 36.2048, 138.253, 0),
('KE', -0.023559, 37.9062, 0),('KG', 41.2044, 74.7661, 0),('KH', 12.5657, 104.991, 0),('KI', -3.37042, -168.734, 0),
('KM', -11.875, 43.8722, 0),('KN', 17.3578, -62.783, 0),('KP', 40.3399, 127.51, 0),('KR', 35.9078, 127.767, 0),
('KW', 29.3117, 47.4818, 0),('KY', 19.5135, -80.567, 0),('KZ', 48.0196, 66.9237, 0),('LA', 19.8563, 102.495, 0),
('LB', 33.8547, 35.8623, 0),('LC', 13.9094, -60.9789, 0),('LI', 47.166, 9.55537, 0),('LK', 7.87305, 80.7718, 0),
('LR', 6.42805, -9.4295, 0),('LS', -29.61, 28.2336, 0),('LT', 55.1694, 23.8813, 0),('LU', 49.8153, 6.12958, 0),
('LV', 56.8796, 24.6032, 0),('LY', 26.3351, 17.2283, 0),('MA', 31.7917, -7.09262, 0),('MC', 43.7503, 7.41284, 0),
('MD', 47.4116, 28.3699, 0),('MG', -18.7669, 46.8691, 0),('MH', 7.13147, 171.184, 0),('MK', 41.4097, 21.687, 0),
('ML', 17.5707, -3.99617, 0),('MM', 21.914, 95.9562, 0),('MN', 46.8625, 103.847, 0),('MO', 22.1987, 113.544, 0),
('MP', 17.3308, 145.385, 0),('MQ', 14.6415, -61.0242, 0),('MR', 21.0079, -10.9408, 0),('MS', 16.7425, -62.1874, 0),
('MT', 35.9375, 14.3754, 0),('MU', -20.3484, 57.5522, 0),('MV', 3.20278, 73.2207, 0),('MW', -13.2543, 34.3015, 0),
('MX', 23.6345, -102.553, 0),('MY', 4.21048, 101.976, 0),('MZ', -18.6657, 35.5296, 0),('NA', -22.9576, 18.4904, 0),
('NC', -20.9043, 165.618, 0),('NE', 17.6078, 8.08167, 0),('NF', -29.0408, 167.955, 0),('NG', 9.082, 8.67528, 0),
('NI', 12.8654, -85.2072, 0),('NL', 52.1326, 5.29127, 0),('NO', 60.472, 8.46895, 0),('NP', 28.3949, 84.124, 0),
('NR', -0.522778, 166.932, 0),('NU', -19.0544, -169.867, 0),('NZ', -40.9006, 174.886, 0),('OM', 21.5126, 55.9233, 0),
('PA', 8.53798, -80.7821, 0),('PE', -9.18997, -75.0152, 0),('PF', -17.6797, -149.407, 0),('PG', -6.31499, 143.956, 0),
('PH', 12.8797, 121.774, 0),('PK', 30.3753, 69.3451, 0),('PL', 51.9194, 19.1451, 0),('PM', 46.9419, -56.2711, 0),
('PN', -24.7036, -127.439, 0),('PR', 18.2208, -66.5901, 0),('PS', 0, 0, 1),('PT', 39.3999, -8.22445, 0),
('PW', 6.51083, 133.434, 0),('PY', -23.4425, -58.4438, 0),('QA', 25.3548, 51.1839, 0),('RE', -21.1151, 55.5364, 0),
('RO', 45.9432, 24.9668, 0),('RU', 61.524, 105.319, 0),('RW', -1.94028, 29.8739, 0),('SA', 23.8859, 45.0792, 0),
('SB', -9.64571, 160.156, 0),('SC', -4.67957, 55.492, 0),('SD', 12.8628, 30.2176, 0),('SE', 60.1282, 18.6435, 0),
('SG', 1.35208, 103.82, 0),('SH', -24.1435, -10.0307, 0),('SI', 46.1512, 14.9955, 0),('SJ', 77.5536, 23.6703, 0),
('SK', 48.669, 19.699, 0),('SL', 8.46056, -11.7799, 0),('SM', 43.9424, 12.4578, 0),('SN', 14.4974, -14.4524, 0),
('SO', 5.15215, 46.1996, 0),('SR', 3.91931, -56.0278, 0),('ST', 0.18636, 6.61308, 0),('SV', 13.7942, -88.8965, 0),
('SY', 34.8021, 38.9968, 0),('SZ', -26.5225, 31.4659, 0),('TC', 21.694, -71.7979, 0),('TD', 15.4542, 18.7322, 0),
('TF', -49.2804, 69.3486, 0),('TG', 8.61954, 0.824782, 0),('TH', 15.87, 100.993, 0),('TJ', 38.861, 71.2761, 0),
('TK', -8.96736, -171.856, 0),('TL', -8.87422, 125.728, 0),('TM', 38.9697, 59.5563, 0),('TN', 33.8869, 9.5375, 0),
('TO', -21.179, -175.198, 0),('TR', 38.9637, 35.2433, 0),('TT', 10.6918, -61.2225, 0),('TV', -7.10954, 177.649, 0),
('TW', 0, 0, 1),('TZ', -6.36903, 34.8888, 0),('UA', 48.3794, 31.1656, 0),('UG', 1.37333, 32.2903, 0),
('UM', 0, 0, 1),('US', 37.0902, -95.7129, 0),('UY', -32.5228, -55.7658, 0),('UZ', 41.3775, 64.5853, 0),
('VA', 41.9029, 12.4534, 0),('VC', 12.9843, -61.2872, 0),('VE', 6.42375, -66.5897, 0),('VG', 18.4207, -64.64, 0),
('VI', 18.3358, -64.8963, 0),('VN', 14.0583, 108.277, 0),('VU', -15.3767, 166.959, 0),('WF', -13.7688, -177.156, 0),
('WS', -13.759, -172.105, 0),('YE', 15.5527, 48.5164, 0),('YT', -12.8275, 45.1662, 0),('YU', 0, 0, 1),
('ZA', -30.5595, 22.9375, 0),('ZM', -13.1339, 27.8493, 0),('ZW', -19.0154, 29.1549, 0);

-- page compose pages
SET @iMaxOrder = (SELECT `Order` + 1 FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_map', 'Profiles Map', @iMaxOrder);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_map_edit', 'Profiles Map Edit Location', @iMaxOrder);

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('bx_map', '998px', 'World Map', '_bx_map_title_block_world_map', '1', '0', 'Map', '', '1', '66', 'non,memb', '0'),
    ('bx_map', '998px', 'Profiles Search', '_bx_map_title_block_search', '2', '0', 'Search', '', '1', '34', 'non,memb', '0'),
    ('bx_map_edit', '998px', 'Map', '_bx_map_title_block_edit_location', '1', '0', 'Map', '', '1', '66', 'non,memb', '0'),
    ('bx_map_edit', '998px', 'Info', '_bx_map_title_block_edit_location_info', '2', '0', 'Info', '', '1', '34', 'non,memb', '0'),    
    ('index', '998px', 'World Map', '_bx_map_title_block_homepage', 0, 0, 'PHP', 'return BxDolService::call(''map_profiles'', ''homepage_block'');', 1, 66, 'non,memb', 0),
    ('profile', '998px', 'Map', '_bx_map_title_block_profile', 0, 0, 'PHP', 'return BxDolService::call(''map_profiles'', ''profile_block'', array($this->oProfileGen->_iProfileID));', 1, 66, 'non,memb', 0);

-- permalinks
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=map_profiles/', 'm/map_profiles/', 'bx_map_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Profiles Map General', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('bx_map_key', '', @iCategId, 'Google Maps API key', 'digit', '', '', '0', ''),
('bx_map_address_field', '', @iCategId, 'Profile address field name for exact address geocoding', 'digit', '', '', '0', ''),
('bx_map_per_page', '7', @iCategId, 'Profiles search results to show per page', 'digit', '', '', '0', ''),
('bx_map_permalinks', 'on', 26, 'Enable friendly permalinks in world map', 'checkbox', '', '', '0', '');
    
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Profiles Map Homepage', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('bx_map_homepage_control_type', 'small', @iCategId, 'Map control type', 'select', '', '', '0', 'none,small,large'),
('bx_map_homepage_is_type_control', 'on', @iCategId, 'Display map type controls', 'checkbox', '', '', '0', ''),
('bx_map_homepage_is_scale_control', '', @iCategId, 'Display map scale control', 'checkbox', '', '', '0', ''),
('bx_map_homepage_is_overview_control', '', @iCategId, 'Display map overview control', 'checkbox', '', '', '0', ''),
('bx_map_homepage_is_map_dragable', 'on', @iCategId, 'Is map dragable?', 'checkbox', '', '', '0', '');

INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Profiles Map Separate', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('bx_map_separate_control_type', 'large', @iCategId, 'Map control type', 'select', '', '', '0', 'none,small,large'),
('bx_map_separate_is_type_control', 'on', @iCategId, 'Display map type controls', 'checkbox', '', '', '0', ''),
('bx_map_separate_is_scale_control', '', @iCategId, 'Display map scale control', 'checkbox', '', '', '0', ''),
('bx_map_separate_is_overview_control', 'on', @iCategId, 'Display map overview control', 'checkbox', '', 'normal,satellite,hybrid', '0', ''),
('bx_map_separate_is_map_dragable', 'on', @iCategId, 'Is map dragable?', 'checkbox', '', '', '0', '');

INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Profiles Map Profile', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('bx_map_profile_control_type', 'small', @iCategId, 'Map control type', 'select', '', '', '0', 'none,small,large'),
('bx_map_profile_is_type_control', 'on', @iCategId, 'Display map type controls', 'checkbox', '', '', '0', ''),
('bx_map_profile_is_scale_control', '', @iCategId, 'Display map scale control', 'checkbox', '', '', '0', ''),
('bx_map_profile_is_overview_control', '', @iCategId, 'Display map overview control', 'checkbox', '', '', '0', ''),
('bx_map_profile_is_map_dragable', 'on', @iCategId, 'Is map dragable?', 'checkbox', '', '', '0', ''),
('bx_map_profile_zoom', '10', @iCategId, 'Profile page default zoom', 'digit', '', '', '0', ''),
('bx_map_profile_map_type', 'normal', @iCategId, 'Profile page default map type', 'select', '', '', '0', 'normal,satellite,hybrid');

INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Profiles Map Hidden', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES

('bx_map_homepage_lat', '20', @iCategId, 'Homepage map latitude', 'digit', '', '', '0', ''),
('bx_map_homepage_lng', '70', @iCategId, 'Homepage map longitude', 'digit', '', '', '0', ''),
('bx_map_homepage_zoom', '1', @iCategId, 'Homepage map zoom', 'digit', '', '', '0', ''),
('bx_map_homepage_map_type', 'normal', @iCategId, 'Homepage map type', 'digit', '', '', '0', ''),

('bx_map_separate_lat', '20', @iCategId, 'Separate page map latitude', 'digit', '', '', '0', ''),
('bx_map_separate_lng', '35', @iCategId, 'Separate page map longitude', 'digit', '', '', '0', ''),
('bx_map_separate_zoom', '2', @iCategId, 'Separate page map zoom', 'digit', '', '', '0', ''),
('bx_map_separate_map_type', 'normal', @iCategId, 'Separate page map type', 'digit', '', '', '0', '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'bx_map', '_bx_map', '{siteUrl}modules/?r=map_profiles/administration/', 'World Map module by BoonEx', 'modules/boonex/map_profiles/|map.png', @iMax+1);

-- top menu
SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 6 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 6, 'Profiles Map', '_bx_map_world_map', 'modules/?r=map_profiles/', @iCatProfileOrder, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'bx_people.png', '', 0, '');

-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_map', 'BxMapProfileDeleteResponse', 'modules/boonex/map_profiles/classes/BxMapProfileDeleteResponse.php', '');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'edit', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'join', @iHandler);

-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('map_profiles', 'view_location', '_bx_map_privacy_view_location', '3');

