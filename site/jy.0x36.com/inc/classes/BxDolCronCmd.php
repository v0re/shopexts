<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php' );
require_once('BxDolCron.php');

class BxDolCronCmd extends BxDolCron {
    
    // - Functions -
    
    function finish()
    {
        global $site;
        global $MODE;
    
        if ( $MODE != "_LIVE_" )
        {
            $output = ob_get_contents();
            ob_end_clean();

            if ( $MODE == "_MAIL_" && $output)
            {
                sendMail ($site['email'], "{$site['title']}: Periodic Report", $output, 0, array(), 'text');
            }
        }
    }
    
    function clean_database()
    {
        $db_clean_vkiss = (int) getParam("db_clean_vkiss");
        $db_clean_profiles = (int) getParam("db_clean_profiles");
        $db_clean_msg = (int) getParam("db_clean_msg");
        $db_clean_visits = (int) getParam("db_clean_members_visits");
        $db_clean_banners_info = (int) getParam("db_clean_banners_info");	

        //clear from `sys_banners_shows` 
		if (db_res("DELETE FROM `sys_banners_shows` WHERE `Date` < UNIX_TIMESTAMP( NOW() - INTERVAL $db_clean_banners_info DAY )"))
            db_res("OPTIMIZE TABLE `sys_banners_shows`");

		//clear from `sys_banners_clicks` 
		if (db_res("DELETE FROM `sys_banners_clicks` WHERE `Date` < UNIX_TIMESTAMP( NOW() - INTERVAL $db_clean_banners_info DAY )"))
            db_res("OPTIMIZE TABLE `sys_banners_clicks`");

        // clear from `sys_messages`
        if (db_res("DELETE FROM `sys_messages` WHERE FIND_IN_SET('sender', `Trash`) AND FIND_IN_SET('recipient', `Trash`)"))
            db_res("OPTIMIZE TABLE `sys_messages`");

        //clear from `sys_ip_members_visits` 
		if (db_res("DELETE FROM `sys_ip_members_visits` WHERE `DateTime` < NOW() - INTERVAL $db_clean_visits DAY"))
            db_res("OPTIMIZE TABLE `sys_ip_members_visits`");

	    // clear ban table
	    if (db_res("DELETE FROM `sys_admin_ban_list` WHERE `DateTime` + INTERVAL `Time` SECOND < NOW()"))
		    db_res("OPTIMIZE TABLE `sys_admin_ban_list`");

        // profile_delete
        if ( $db_clean_profiles > 0)
        {
            $res = db_res("SELECT `ID` FROM `Profiles` WHERE (`DateLastNav` < NOW() - INTERVAL $db_clean_profiles DAY) AND (`Couple` > `ID` OR `Couple` = 0)");
            if ( $res )
            {
                $db_clean_profiles_num = mysql_num_rows($res);
                while ( $arr = mysql_fetch_array($res) )
                {
                    profile_delete($arr['ID']);
                }
                db_res("OPTIMIZE TABLE `Profiles`");
            }
        }

        if ( $db_clean_vkiss > 0 )
        {
            $res = db_res("DELETE FROM `sys_greetings` WHERE `When` < NOW() - INTERVAL $db_clean_vkiss DAY");
            if ( $res ) {
                $db_clean_vkiss_num = db_affected_rows();
                db_res("OPTIMIZE TABLE `sys_greetings`");
            }
        }
 
        if ( $db_clean_msg > 0 )
        {
        	$res = db_res("DELETE FROM `sys_messages` WHERE `Date` < NOW() - INTERVAL $db_clean_msg DAY");
            if ( $res ) {
                $db_clean_msg_num = db_affected_rows();
                db_res("OPTIMIZE TABLE `sys_messages`");
            }
        }

        //--- Clean sessions ---//
        bx_import('BxDolSession');
		$oSession = BxDolSession::getInstance();
		$iSessions = $oSession->oDb->deleteExpired();

        // clean expired ip bans
        bx_import('BxDolAdminIpBlockList');
        $oBxDolAdminIpBlockList = new BxDolAdminIpBlockList();
        $iIps = $oBxDolAdminIpBlockList->deleteExpired();

        // clean old views
        bx_import('BxDolViews');
        $oBxViews = new BxDolViews('', 0);
        $iDeletedViews = $oBxViews->maintenance ();

        // clean old votes
        bx_import('BxDolVoting');
        $oBxVotes = new BxDolVoting('', 0);
        $iDeletedVotes = $oBxVotes->maintenance ();

        echo "\n- Database cleaning -\n";
        echo "Deleted profiles: $db_clean_profiles_num\n";
        echo "Deleted virtual kisses: $db_clean_vkiss_num\n";
        echo "Deleted messages: $db_clean_msg_num\n";
        echo "Deleted sessions: $iSessions\n";
        echo "Deleted records from ip block list: $iIps\n";
        echo "Deleted views: $iDeletedViews\n";
        echo "Deleted votes: $iDeletedVotes\n";
    }
    
    function del_old_all_files()
    {
        global $dir;
    
        $num_tmp = 0;
        $num_del = 0;
    
        $file_life = 86400;  // one day
        $dirToClean = array();
        $dirToClean[] = $dir['tmp'];
        $dirToClean[] = $dir['cache'];
            
        foreach( $dirToClean as $value )
        {
            if ( !( $lang_dir = opendir( $value ) ) )
            {
                continue;
            }
            else
            {
                while ($lang_file = readdir( $lang_dir ))
                {
                    $diff = time() - filectime( $value . $lang_file);
                    if ( $diff > $file_life && '.' != $lang_file && '..' != $lang_file && '.htaccess' !== $lang_file )
                    {
                        @unlink ($value . $lang_file);
                        ++$num_del;
                    }
                    ++$num_tmp;
                }
                closedir( $lang_dir );
            }
        }
        
        echo "\n- Temporary files check -\n";
    
        echo "Total temp files: $num_tmp\n";
        echo "Deleted temp files: $num_del\n";
    }
    
    function processing() {
        
        global $MODE;

        // - Defaults -
        $MODE   = "_MAIL_";
        //$MODE = "_LIVE_";
        $DAY    = "_OBEY_";
        //$DAY  = "_FORCE_";
        define('NON_VISUAL_PROCESSING', 'YES');
        
        
        // - Always finish
        set_time_limit( 36000 );
        ignore_user_abort();
        
        // - Parameters check -
        for ( $i = 0; strlen( $argv[$i] ); $i++ )
        {
            switch( $argv[$i] )
            {
                case "--live": $MODE = "_LIVE_"; break;
                case "--mail": $MODE = "_MAIL_"; break;
                case "--force-day": $DAY = "_FORCE_"; break;
                case "--obey-day": $DAY = "_OBEY_"; break;
            }
        }
        
        if ( $MODE != "_LIVE_" )
            ob_start();
        
        $day = date( "d" );
        if ( getParam( "cmdDay" ) == $day && $DAY == "_OBEY_" )
        {
            echo "Already done today, bailing out\n";
            $this->finish();
            return;
        }

        setParam( "cmdDay", $day );
        
        
        //========================================================================================================================
        
        // - Membership check -
        echo "\n- Membership expiration letters -\n";
        
        $expire_notification_days = getParam("expire_notification_days");
        $expire_notify_once = getParam("expire_notify_once");
        
        $expire_letters = 0;
        
        $exp_res = db_res( "SELECT `ID` FROM `Profiles`", 0 );
        
        while( $row = mysql_fetch_array( $exp_res ) )
        {
            $current_membership_arr = getMemberMembershipInfo( $row['ID'] );
            // If expire_notification_days is -1 then notify after expiration
            if ( $current_membership_arr['ID'] == MEMBERSHIP_ID_STANDARD && $expire_notification_days == -1 )
            {
                // Calculate last UNIX Timestamp
                $last_timestamp = time() - 24 * 3600;
                $last_membership_arr = getMemberMembershipInfo( $row['ID'], $last_timestamp );
                if ( $current_membership_arr['ID'] != $last_membership_arr['ID'] )
                {
                    if ($further_membership_arr['ID'] == MEMBERSHIP_ID_STANDARD)        
                    {
                        $mail_ret = mem_expiration_letter($row['ID'], $last_membership_arr['Name'], -1);
                        if ( $mail_ret )
                            $expire_letters++;
                    }
                }
            }
            // If memberhip is not standard then check if it will change
            elseif ( $current_membership_arr['ID'] != MEMBERSHIP_ID_STANDARD )
            {
                // Calculate further UNIX Timestamp
                $further_timestamp = time() + $expire_notification_days * 24 * 3600;
                $further_membership_arr = getMemberMembershipInfo( $row['ID'], $further_timestamp );
                if ( $current_membership_arr['ID'] != $further_membership_arr['ID']
                    && $further_membership_arr['ID'] == MEMBERSHIP_ID_STANDARD )
                {
                    if ( !$expire_notify_once || abs($further_timestamp - $current_membership_arr['DateExpires']) < 24 * 3600 )
                    {
                        $mail_ret = mem_expiration_letter( $row['ID'], $current_membership_arr['Name'],
                            (int)( ($current_membership_arr['DateExpires'] - time()) / (24 * 3600) ) );
                        if ( $mail_ret )
                            $expire_letters++;
                    }
                }
            }
        }
        
        echo "Send membership expire letters: $expire_letters letters\n";
        
        //========================================================================================================================
        
        // clear tmp folder --------------------------------------------------------------------------
        
        $this->del_old_all_files();
        
        // ----------------------------------------------------------------------------------
        $this->clean_database();
        
        $this->finish();
    }
}

?>
