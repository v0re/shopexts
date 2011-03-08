<?php   //Determines how many minutes are actually within the hours of operation

function minutesopened($openhour,$closedhour,$startdate,$enddate) {
  
  /*
   eg. hoursopened("0800","1700","2001-01-20","2001-02-4")
  */
  
  // Determine totaldays
  $totaldays = round(((strtotime($originalend) - strtotime($originalstart))/86400) + 0.00000001);
  
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
  $nextminute = mktime($starthour,$startminute+1,$startsecond,$startmonth,$startday,$startyear);
  
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
    
  // 
  $nextday = mktime($starthour,$startminute+$i,$startsecond,$startmonth,$startday,$startyear);
  
  return $totalminutes;
}

?>
