# php-stream-socket-server
Provides a bootstrapable server for WebSockets

## requirements

- php 7.0.x

## installation

Simply add to your composer.json

```
{
  "require": {
    "php": "^7.0",
    "sockets/php-stream-socket-server": "dev-master"
  }
}
```

## Usage

To use, create a bootstrap like this;

```
<?php
declare(ticks = 1);
declare(strict_types = 1);
namespace sockets;
require 'autoload.php';
$daemon = new StreamSocketDaemon();
$daemon->startStreamSocketServer(function($data){
  echo "intercepted\n";
  var_dump($data);
  return 'data processed by the app';
});
exit("server failed at ".time());
```

The callback will run for every client message the socket recieves, it is within here you would initialise your app based on the contents of the message payload. There is a 1 time response using the return value of the lambda.

Furutre releases will expose a way that you might be able to create a server push message to all clients through a broadcast.


