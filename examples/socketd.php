<?php
declare(strict_types = 1);
declare(ticks = 1);
// If you have installed this project via composer simply
// uncomment the following line;
// include 'vendor/autoloader.php';
include 'autoloader.php';
$conf = [];
$conf['DEBUG'] = isset($argv[1]) && $argv[1] === '-d';
$daemon = new sockets\StreamSocketDaemon($conf);
$daemon->startStreamSocketServer(function($data){
  // All client messages can be caught here
  // use $server to manage the stream socket server status
  // Clients can also be disconntected via the $server
  // Use $client to send responses or simply return it like this;
  echo "intercepted\n";
  var_dump($data);
  return 'data processed by the app';
});
// this line is reached only when the server is stopped
exit("server terminated at ".time());
