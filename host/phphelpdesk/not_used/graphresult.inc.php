<?
include("includes/graphit.inc.php");

Function GraphResult($result,$title) {

// Takes a database result set.
// The first column should be the name,
// and the second column should be the values

$rows = mysql_num_rows($result);

if ((!$result) || ($rows < 1)) {
  echo "None Found.\n";
}
else {

  for ($j=0; $j < mysql_num_rows($result); $j++) {
    if (mysql_result($result, $j, 0) != "" && mysql_result($result, $j, 1) != "" ) {
      $names[$j]= mysql_result($result, $j, 0);
      $values[$j]= mysql_result($result, $j, 1);
    }
  }

  //This is another function detailed below
  GraphIt($names,$values,$title);

}
}
?>
