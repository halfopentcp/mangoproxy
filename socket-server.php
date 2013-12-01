#!/usr/local/bin/php -q
<?php
error_reporting(E_ALL);
ini_set('display_errors','on');
/* TODO: require 'config.php' // This will hold our global $config variable */
/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush();

$address = '192.168.1.105';
$port = 1080;
$mangle_headers = $config['mangle_headers'] =  	true /* TODO: remove this */;
$block_headers = $config['block_headers'] = 	true /* TODO: remove this */;

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($sock, $address, $port) === false) {
    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

if (socket_listen($sock, 5) === false) {
    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

do {
    if (($msgsock = socket_accept($sock)) === false) {
        echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
        break;
    }
    
    do {
    	
        if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
            echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($msgsock)) . "\n";
            break 2;
        }
        /* Enter GET/POST request stage */
        if( substr($buf,0,3) == 'GET'  ){
        	/* GET request */
        	list($remote_host,$remote_uri,$buffer) = func_request("get");
        }
        
        /* $buf will be set to the mangled request (if mangling occured ) */
        /* func_request() will set a couple flags after processing */
        
        /* The following will be sent to the server */
        socket_write($remote_socket,$remote_request,strlen($remote_request));
        
        /* The following socket_write call is sent to the browser */
        socket_write($msgsock, $talkback, strlen($response));
        echo "$buf\n";
    } while (true);
    socket_close($msgsock);
} while (true);

socket_close($sock);

/* Functions */
function func_open_and_write($host,$uri,$buffer){
	if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
		echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    	}
    	
}
function func_request($type){
	global $buf;
	
	return array($remote_host,$remote_uri,$buffer);
}
?>