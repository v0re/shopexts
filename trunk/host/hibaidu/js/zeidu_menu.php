<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: zeidu_menu.php 2010-01-24 16:17:18Z anjel $
*/

require_once("../global.php");
require_once("../cache/s_cate_menu_array.php");
?>
function onLoadHandler() 
{
<?php
 foreach($nav_cate_menu_array as $key=>$value)
 { 
?>
var Desc_<?php echo $key;?> = new Object();
Desc_<?php echo $key;?>.menuDiv = document.getElementById("Memu_Desc_<?php echo $key;?>");
Desc_<?php echo $key;?>.menuLink = document.getElementById("Memu_Desc_<?php echo $key;?>_link");
Desc_<?php echo $key;?>.display = false;
Desc_<?php echo $key;?>.clickHandler = function(item) { window.open(item.link, "_parent"); };
<?php
$items="";
foreach($value as $key_2=>$value_2){$items=$items."{\"link\":\"s/?wd=".urlencode($value_2)."&s=".$key."\", \"text\":\"".$value_2."\"},";}$items=rtrim($items,",");?>
Desc_<?php echo $key;?>.items =[<?php echo $items;?>];
popupMenu.createMenu(Desc_<?php echo $key;?>);
<?php
  }
?>
}
document.writeln("<script type=\"text\/javascript\" src=\"\/js\/popupmenu-2.js\"><\/script>");