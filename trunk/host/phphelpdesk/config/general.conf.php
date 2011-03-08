<?php	//general configuration

$version 	= "1.0.2 developer release";	//Version Number please confirm changes with the development side under Sourceforge.net
$g_title	= "PHP Helpdesk";	//Title of your helpdesk
$g_helpdesk_email = "admin@domain.com";	//Email of your helpdesk
$g_mailservername = "mailserver.com";	//Mail server
$g_domainmailfrom = "";		//Unless you need this program to send out
				//mail and make it look as if its coming from
				//another server, then leave this empty.
$g_language = "english";	//choose from languages in the /languages/ directory.
				//You would put whatever goes before .lang.php.
				//So for the swedish.lang.php file, you would just type
				//in swedish between the quotes.  
				//Currently Included: 
				//  spanish, english
$g_refreshtime = "1200";	//View Jobs refresh rate in seconds
$g_enable_javascript = 1;	//1 = enable, 0 = don't enable
$g_dept_or_comp = 0;		//This depends on what you are using phphelpdesk for.
				//0 = department.  This would be used if you have
				// multiple departments that you would like to track
				// helpdesk tickets for.
				//1 = company.  This would be used if you had multiple
				// companies that you wanted to track trouble tickets for.
/*
 If you are using http://someaddress.com/phphelpdesk, then you must set the
 base url to "http://someaddress.com/phphelpdesk/"  Notice the '/' at the end
 you must put that in there.  If you are using a virtual host such as
 http://phphelpdesk.somecompany.com, then you can just leave it empty with
 the quotes touching "";
*/
$g_base_url     = "http://localhost/phphelpdesk_new";  //base url location

// you HAVE to set your domain name here, with no HTTP:
// or / at the end or anything...
$g_domain = "http://localhost/";

// this is the path from the last part of3s1 the $g_base_url
// parameter.  If there is no path, set this to "/"; NOTICE
// THE BEGINNING AND ENDING /'s!!
$g_base_path = "/";

$g_currency ="&euro;";  // Only if you use the sotck management system
$g_dep_short_name = "1"; // This changes the department names which are displayed 
								// in the "View Tickets" section. It is recomendable for 
								// low screen solutions the only use these short names 
								// 1 = short names
								// 0 = long names
			// NOT YET TESTED!
$g_include_parts_management = "0"; // Determine here if you would like a small
									// stock managment of you part you need to fix
									// equipment. 	1=on, 0=off
						// IN THIS VERSION NOT INCLUDED!
$debug = "0";           // SQL Debugging Info  1=on, 0=off
?>
