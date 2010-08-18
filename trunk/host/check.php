<?php

$ret_header = file_get_contents("result.txt");
if(preg_match('/200 OK/i',$ret_header,$ret)){
    echo "OK";
}else{
    echo "NO";
}