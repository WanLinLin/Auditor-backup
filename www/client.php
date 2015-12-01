<?php
header("Content-Type:text/html; charset=utf-8");

if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Couldn't create socket: [$errorcode] $errormsg <br/>");
}
 
echo "Socket created <br/>";
 
//Connect socket to remote server
if(!socket_connect($sock , '127.0.0.1' , 1242))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not connect: [$errorcode] $errormsg <br/>");
}
 
echo "Connection established <br/>";

$message = "余尚叡是特級廚師";
 
//Send the message to the server
if( ! socket_send ( $sock , $message , strlen($message) , 0))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not send data: [$errorcode] $errormsg <br/>");
}
 
echo "Message send successfully <br/>";
 
//Now receive reply from server
if(socket_recv ( $sock , $buf , 2045 , MSG_WAITALL ) === FALSE)
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not receive data: [$errorcode] $errormsg <br/>");
}
 
//print the received message
echo $buf;

socket_close($sock);
?>