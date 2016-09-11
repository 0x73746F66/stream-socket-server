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
  echo "intercepted\n";
  var_dump($data);
  return 'data processed by the app';
});
exit("server failed at ".time());
