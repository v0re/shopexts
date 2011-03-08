<?php  	 // PHP Helpdesk  -  MAIN PROGRAM
session_start(); 
header("Cache-control: private"); //IE 6
header("Cache-control: no-store"); 
// INCLUDE CONFIGURATION FILES AND FREQUENTLY USED INCLUDE FILES
include("config/general.conf.php");
include("config/db.conf.php");
include("config/html.conf.php");
include("includes/functions.inc.php");
include_once("includes/vars.inc.php"); //inicalize some variables this we would need a extension there is the desire to declare all varibales 
// Load language file.  Always load english, so if a variable is
// missing from the translation file, at least the user will see
// the english version instead of nothing.
include("languages/english.lang.php");
if( $g_language != "english" && isset( $g_language ) )
    include("languages/$g_language.lang.php");
$ip_addr= $_SERVER['REMOTE_ADDR']; // ip address to be printed for the users information
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<script type="text/javascript">
function openwizz() {
<!--
F1 = window.open("wiz_start.php","Fenster1","scrollbars=yes,width=500,height=400,left=100,top=100");
}
//-->
</script>
  <meta http-equiv="Content-Language" content="en-us">
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <? if ($whattodo == "viewjobs") { ?>
		<META HTTP-EQUIV="Refresh" CONTENT="<?echo $g_refreshtime;?>;URL=
		<?echo $g_base_url;?>/index.php?whattodo=viewjobs">
      <? } ?>
<title><? print $g_title ?></title>
<SCRIPT LANGUAGE="JavaScript">
//move the left corner of the browser window to the top
//left corner of the screen
window.moveTo(0,0);
//resize the browser to the monitor's resolution
window.resizeTo(window.screen.width,window.screen.height); 
</SCRIPT>
	<LINK REL=StyleSheet HREF="css/elements.css" TYPE="text/css" MEDIA=screen>
	<LINK REL=StyleSheet HREF="css/main.css" TYPE="text/css" MEDIA=screen>
	<script type='text/javascript' src='javascript/menu9.js'></script>
</head>
<body class = "main" topmargin ="0" leftmargin ="0">
<?PHP
// CHECK IF USER LOGGED OUT
if (isset($logout)) {
  	session_destroy();  //kill the session
	unset($status);	unset($user);	unset($authentication);	unset($laston);	unset($logout); // make sure really everything is deleted
}
// CHECK IF THE USER LIKES TO AUTHENTICATE
if (isset($status) && !isset($authentication)) { 
  include("includes/checkuser.inc.php");
   $_SESSION['status'] = $status;
   $_SESSION['user'] = $user;
   $_SESSION['authentication'] = "YES";
   $_SESSION['laston'] = $laston;
}
// OK THE USER IS AUTHENTICATED, CAN GO INTO THE SYSTEM
if (isset($authentication)) { // if user is allowed to enter the system get the users permissions
	include("includes/permissions.inc.php");
	include("layout/elements/mainwindow.inc.php");   
	include ("layout/adminpages.layout.php"); // do all the layout and the header menu.
	include("layout/elements/log_out.inc.php");// includes the login elements 
	print "<div style='position:absolute; top:260px; left:18px; z-index:2;'>"; // position of left menu
	include("layout/elements/leftmenu.inc.php");// includes the left navigation menu
} else { 
	// AUTHENTICATION NOT DONE OR WASN'T SUCCESSFULL SO PRINT THE LANDING PAGE AFAIN
	include ("layout/landingpage.layout.php"); // do all the layout and the header menu.
	include("layout/elements/login_form.inc.php");// includes the login elements 
	print "<div style='position:absolute; top:380px; left:18px; z-index:2;'>"; // position of left menu
	include("layout/elements/leftmenu.inc.php");// includes the left navigation menu 
}
?>
</body></html>
