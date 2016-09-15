<?php
declare(strict_types = 1);
declare(ticks = 1);
namespace sockets;
define('PROJECT_ROOT', realpath(str_replace('/bin', '/src', dirname(__FILE__))) . DIRECTORY_SEPARATOR);
$current = spl_autoload_extensions();
spl_autoload_extensions($current);
spl_autoload_register(function ($file) {
    $ext = '.php';
    $pieces = explode('\\', $file);
    $fileName = end($pieces);
    include_once PROJECT_ROOT . $fileName . $ext;
});
$conf = [];
$conf['DEBUG'] = isset($argv[1]) && $argv[1] === '-d';
$daemon = new StreamSocketDaemon($conf);
$daemon->startStreamSocketServer(function ($data, $client, $server) {
    return $data;
});
exit("server terminated at " . time());
