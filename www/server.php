<?php
header("Content-Type:text/html; charset=utf-8");

/* JIEBA PREPARE */
ini_set('memory_limit', '1024M');
require_once "src/vendor/multi-array/MultiArray.php";
require_once "src/vendor/multi-array/Factory/MultiArrayFactory.php";
require_once "src/class/Jieba.php";
require_once "src/class/Finalseg.php";

use Fukuball\Jieba;
use Fukuball\Finalseg;

$options = array();
$options['mode'] = 'test';
$options['dict'] = 'small';

Jieba::init($options);
Finalseg::init();
/* JIEBA PREPARE */

// create socket
if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Couldn't create socket: [$errorcode] $errormsg \n");
}
 
echo "Socket created <br/>";

// Bind the source address
if( !socket_bind($sock, "127.0.0.1" , 1242) )
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not bind socket : [$errorcode] $errormsg \n");
}
 
echo "Socket bind OK <br/>";

if(!socket_listen ($sock , 10))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not listen on socket : [$errorcode] $errormsg \n");
}

echo "Socket listen OK <br/>";

echo "Waiting for incoming connections... <br/>";

$clients = array();
$count = 0;
socket_set_nonblock($sock);

//Accept incoming connection - This is a blocking call
// $client = socket_accept($sock);

while($count < 1)
{
    if($new_client = socket_accept($sock))
    {
        echo '<br/>';
        echo "Client $new_client has connected <br/>";
        $clients[] = $new_client;

        //display information about the client who is connected
        if(socket_getpeername($new_client , $address , $port))
        {
            echo "Client $address : $port is now connected to us. <br/>";

            //read input sentence from the incoming socket
            $input_sentence = socket_read($new_client, 1024000);

            $t1 = microtime(true);
            $seg_list = Jieba::cut($input_sentence);
            echo "cut cost ".(microtime(true) - $t1)." seconds.<br/>";
            echo '<br/>';
            ob_flush();
            flush();

            $words = "";
            for($i = 0; $i < count($seg_list); $i++) {
                $words .= ($seg_list[$i].'/');
            }

            // Display output  back to client
            socket_write($new_client, $words);
            socket_close($new_client);

            $count++;
        }
    }
}

for($i = 0; $i < count($clients); $i++) {
    echo $clients[$i].'<br/>';
}

socket_close($sock);
echo "Socket close... <br/>";
?>