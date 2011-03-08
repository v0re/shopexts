<?php  //Function that I found on phpbuilder to make html graphs
       //since I can't find any gd libraries :(  which would be nice.
include("includes/class-graphs.inc.php");

Function GraphIt($names,$values,$title) {
$counter=count($names);
/*
Can choose any color you wish
*/
for ($i = 0; $i < $counter; $i++) {
  $bars[$i]="#DEDEEE";
}

$counter=count($values);

/*
 Figure the max_value passed in, so scale can be determined
*/

$max_value=0.00; 

for ($i = 0; $i < $counter; $i++) {
  if ($values[$i] > $max_value) {
    $max_value=$values[$i];
  }
}

if ($max_value < 1) {
  $max_value=1;
}

/*
 I want my graphs all to be 800 pixels wide, so that is my divisor
*/
//$scale = (int) (600/$max_value);
$scale = (200/$max_value);

/*
I create a wrapper table around the graph that holds the title
*/
echo "<TABLE BGCOLOR=\"NAVY\" CELLSPACING=2 CELLPADDING=3>";
echo "<TR><TD BGCOLOR=\"NAVY\"><FONT COLOR=WHITE><B>$title</TD></TR><TR><TD>";

/*
Create an associatve array to pass in. I leave most of it blank
*/
$vals =  array(
  "vlabel"=>"",
  "hlabel"=>"",
  "type"=>"0",
  "cellpadding"=>"",
  "cellspacing"=>"",
  "border"=>"",
  "width"=>"",
  "background"=>"",
  "vfcolor"=>"",
  "hfcolor"=>"",
  "vbgcolor"=>"",
  "hbgcolor"=>"",
  "vfstyle"=>"",
  "hfstyle"=>"",
  "noshowvals"=>"",
  "scale"=>"$scale",
  "namebgcolor"=>"",
  "valuebgcolor"=>"",
  "namefcolor"=>"",
  "valuefcolor"=>"",
  "namefstyle"=>"",
  "valuefstyle"=>"",
  "doublefcolor"=>"");

/*
This is the actual call to the HTML_Graphs class
*/
html_graph($names,$values,$bars,$vals);

echo "</TD></TR></TABLE>";
}
?> 
