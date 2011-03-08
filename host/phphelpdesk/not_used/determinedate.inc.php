<?php

// This function will determine the number of, for example, Sundays between 2 dates 
function determinedays($startdate,$enddate,$dayofweek) {

  /*
   Example, Count the Thursdays between Friday Feb 2, 2001 - Tuesday Feb 27, 2001
  */

  // Make startdate into mktime format so that we can do computations on the date
  // You have to convert startdate to a timestamp first
  $originalstart = $startdate;
  $startdate = strtotime($startdate);
  $startyear = date("Y",$startdate);
  $startmonth = date("n",$startdate);
  $startday = date("d",$startdate);
  $starthour = date("H",$startdate);
  $startminute = date("i",$startdate);
  $startdate = mktime($starthour,$startminute,$startsecond,$startmonth,$startday,$startyear);
  $nextday = mktime($starthour,$startminute,$startsecond,$startmonth,$startday+1,$startyear);
  
  // Make enddate into mktime format so that we can do computations on the date
  // You have to convert enddate to a timestamp first
  $originalend = $enddate;
  $enddate = strtotime($enddate);
  $endyear = date("Y",$enddate);
  $endmonth = date("n",$enddate);
  $endday = date("d",$enddate);
  $endhour = date("H",$enddate);
  $endminute = date("i",$enddate);
  $enddate = mktime($endhour,$endminute,$endsecond,$endmonth,$endday,$endyear);
  
  $dayofweek = strtolower($dayofweek);		//make sure dayofweek is all lowercase

  $count = 0;					//Number of days counted
  $totaldays = round(((strtotime($originalend) - strtotime($originalstart))/86400) + 0.00000001);
  // count through the total number of days and see how many dayofweeks or in that total
  for ($i=1; $i <= $totaldays; $i++) {
    if (strtolower(date("l",$nextday)) == $dayofweek) {
      $count++;
    }
    $nextday = mktime($starthour,$startminute,$startsecond,$startmonth,$startday+$i,$startyear);
  }
  print "<BR>";
  
  return $count;

}  

?>
