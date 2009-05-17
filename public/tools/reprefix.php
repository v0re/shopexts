<?php

$aDZDbName = array(
'access',
'activities',
'activityapplies',
'adminactions',
'admingroups',
'adminnotes',
'adminsessions',
'advertisements',
'announcements',
'attachments',
'attachpaymentlog',
'attachtypes',
'banned',
'bbcodes',
'buddys',
'caches',
'campaigns',
'creditslog',
'crons',
'debateposts',
'debates',
'failedlogins',
'faqs',
'favorites',
'forumfields',
'forumlinks',
'forumrecommend',
'forums',
'imagetypes',
'invites',
'itempool',
'magiclog',
'magicmarket',
'magics',
'medals',
'memberfields',
'membermagics',
'members',
'memberspaces',
'moderators',
'modworks',
'myposts',
'mythreads',
'onlinelist',
'onlinetime',
'orders',
'paymentlog',
'pluginhooks',
'plugins',
'pluginvars',
'pms',
'pmsearchindex',
'polloptions',
'polls',
'posts',
'profilefields',
'projects',
'promotions',
'ranks',
'ratelog',
'regips',
'relatedthreads',
'rewardlog',
'rsscaches',
'searchindex',
'sessions',
'settings',
'smilies',
'spacecaches',
'stats',
'statvars',
'styles',
'stylevars',
'subscriptions',
'tags',
'templates',
'threads',
'threadsmod',
'threadtags',
'threadtypes',
'tradecomments',
'tradelog',
'tradeoptionvars',
'trades',
'typemodels',
'typeoptions',
'typeoptionvars',
'typevars',
'usergroups',
'validating',
'videos',
'videotags',
'words'
);

include("config.inc.php");

mysql_connect($dbhost,$dbuser,$dbpw);
mysql_select_db($dbname);

foreach($aDZDbName as $tbname){
	$old_tbname = "sdb_".$tbname;
	$new_tbname = "cdb_".$tbname;

	$sql = "RENAME TABLE `{$old_tbname}` TO `{$new_tbname}`";
	if(mysql_query($sql)){
		echo "Rename $old_tbname to $new_tbname ok!<br>";
	}else{
		echo "Rename $old_tbname to $new_tbname fail!<br>";
	}
}


 ?>