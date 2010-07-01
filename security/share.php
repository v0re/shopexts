<?php

mysql_connect('localhost','root','shopex');
mysql_select_db('security');
mysql_query('set names utf8');
	

if (get_magic_quotes_gpc() ) {
    function stripslashes_deep($value) {
        $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
        return $value;
    }
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}

if (false) {
    function addslashes_deep($value) {
        $value = is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
        return $value;
    }
    $_POST = array_map('addslashes_deep', $_POST);
    $_GET = array_map('addslashes_deep', $_GET);
    $_COOKIE = array_map('addslashes_deep', $_COOKIE);
}