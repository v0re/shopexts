<?php 

ini_set("output_buffering",false);

$newKB=0; 
$numKB = (int) isset($_GET['kb'])?$_GET['kb']:512; 

if($numKB<56) { $newKB=56; } 
if($numKB>2048) { $newKB=2048; } 
if($newKB) { header( "Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?kb=$newKB" ); exit; } 

?> 

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head><title>Webspeed test</title> 
<meta http-equiv="content-type" content="text/html;charset=utf-8" /> 
<style type="text/css"> 
html { background: #fff; color: #000; } 
</style> 
</head><body> 
<h1 id="h1">Please wait</h1> 
<h2>Measuring your bandwidth...</h2> 
<div>You are connected to a server computer in Dallas TX.</div> 
<p style="border-bottom: thin solid black;">Transferring <?php echo $numKB; ?> <span class="abbr" title="kilobyte">KB</span>...</p> 

<!-- 
<?php 
function getmicrotime() {  
    list($usec, $sec) = explode(' ', microtime()); 
    return ((float)$usec + (float)$sec); 
} 
flush(); 
$timeStart = getmicrotime(); 
$nlLength = strlen("\n"); 
for($i = 0; $i < $numKB; $i++) { 
    echo str_pad('', 1024 - $nlLength, '/*\\*') . "\n"; 
    flush(); 
} 
$timeEnd = getmicrotime(); 
$timeDiff = round($timeEnd - $timeStart, 3); 
$result= ($timeDiff <= 1) ? ('more than '.$numKB) : ('<span style="color:red;">'.round($numKB / $timeDiff, 3).'</span>'); 
?> 
--> 

<script type="text/javascript">//<![CDATA[ 
if( document.getElementById && document.getElementById('h1') ) { document.getElementById('h1').firstChild.nodeValue='Done'; } 
//]]></script> 

<p id="done"> 
<b>Transferred <?php echo $numKB;?> <span class="abbr" title="kilobyte">KB</span> in <?php echo $timeDiff;?> seconds, <?php echo $result;?> <span class="abbr" title="kilobytes per second">KB/s</span>.</b></p> 
<p class="no"> 
[<a href="<?php echo "http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?kb=$numKB&amp;r=$timeEnd";?>">Again</a>] 
[<a href="<?php echo "http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?kb=".($numKB*2)."&amp;r=$timeEnd";?>">More</a>] 
</p> 

