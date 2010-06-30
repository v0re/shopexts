

<form method=post>
<textarea name=string></textarea>
<br/>
<input type=submit value='submit'>
</form>

<?php
require("../share.php");
if($_POST){
	$string = stripslashes($_POST['string']);
	for($i=0;$i<strlen($string);$i++){
		echo ord($string{$i}).",";
	}
	
}
?>