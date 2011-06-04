-- tables
DROP TABLE IF EXISTS `[db_prefix]units`;

-- injection
DELETE FROM `sys_injections` WHERE `sys_injections`.`name` = 'quotes_injection';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'Quotes';
