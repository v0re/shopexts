<?PHP

$lines = array();

echo "Received file $upfile";
echo "<br>userfile: $userfile";
echo "<br>userfile_name: $userfile_name";
echo "<br>userfile_size: $userfile_size";
echo "<br>userfile_type: $userfile_type";
$lines = file($upfile);

echo "<p><pre>";
reset($lines);
while( $l = next( $lines ) ) {
   echo "$l";
}


echo "</pre>";


?>
