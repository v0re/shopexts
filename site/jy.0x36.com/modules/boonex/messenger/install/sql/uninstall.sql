DELETE FROM `sys_objects_actions` WHERE `Icon`='modules/boonex/messenger/|action_im.png';

DELETE FROM `sys_injections` WHERE `name`='messenger_invitation';

DELETE FROM `sys_acl_actions` WHERE `Name`='use messenger';