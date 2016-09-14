<?php
declare(strict_types = 1);
declare(ticks = 1);
namespace sockets;
// If you have installed this project via composer simply
// uncomment the following line;
// include 'vendor/autoload.php';
include 'autoload.php'; // and remove this line
$conf = [];
$conf['DEBUG'] = isset($argv[1]) && $argv[1] === '-d';
$daemon = new StreamSocketDaemon($conf);
$daemon->startStreamSocketServer(function ($data, $client, $server) {
    // All client messages can be caught here
    // use $server to manage the stream socket server status
    // Clients can also be disconnected via the $server
    // Use $client to send responses or simply return it like this;
    return $data;
});
// this line is reached only when the server is stopped
exit("server terminated at " . time());
