<?php	//open and print each line of a file

function rfile($textFile) {

  $myFile = fopen("$textFile", "r");
  if(!($myFile)) {
    print("<P><B>Error: </B>");
    print("<i>'$textFile'</i> could not be read\n");
    exit;
  }
  if($myFile) {
    while(!feof($myFile)) {
      $myLine = fgets($myFile, 255);
      print("$myLine <BR>\n");
    }
    fclose($myFile);
  }
}

?>
