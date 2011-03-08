<?php	// HELP SCRIPT

print "<UL>\n";
print "  <LI><a href=\"$g_base_url/index.php3?whattodo=help&help=general\">General Help</a></LI>\n";
print "  <LI><a href=\"$g_base_url/index.php3?whattodo=help&help=loggingin\">Logging In</a></LI>\n";
print "  <LI><a href=\"$g_base_url/index.php3?whattodo=help&help=addingusers\">Adding Users</a></LI>\n";
print "  <LI><a href=\"$g_base_url/index.php3?whattodo=help&help=addingcategories\">Adding Categories</a></LI>\n";
print "  <LI><a href=\"$g_base_url/index.php3?whattodo=help&help=addingjobs\">Adding Jobs</a></LI>\n";
print "  <LI><a href=\"$g_base_url/index.php3?whattodo=help&help=viewingjobs\">Viewing Jobs</a></LI>\n";
print "</UL>\n";

if (isset($help)) {
  if ($help == "general") {
    readfile("scripts/helpgeneral.html");
  }
  if ($help == "loggingin") {
  }
  if ($help == "addingusers") {
  }
  if ($help == "addingcategories") {
  }
  if ($help == "addingjobs") {
  }
  if ($help == "viewingjobs") {
  }
}

?>
