<?php

//define("PB_CRYPT_LINKS" , 1);
define("_LIBPATH","./lib/");
require_once _LIBPATH . "site.php";

$site = new CSite("./site.xml",true);
$site->Run();

?>
