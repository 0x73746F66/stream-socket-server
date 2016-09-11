# php-stream-socket-server
Provides a bootstrapable server for WebSockets

## Requirements

- php 7.0.x

## Installation

> This project will strictly follow semantic versions.

Simply add to your composer.json

```
{
  "require": {
    "php": "^7.0",
    "sockets/php-stream-socket-server": "1.0.x"
  }
}
```

and run `composer install`

## Usage

To use, create a bootstrap like this;

```
<?php
declare(ticks = 1);
declare(strict_types = 1);
require 'vendor/autoload.php';
$daemon = new sockets\StreamSocketDaemon(/*$config*/);
$daemon->startStreamSocketServer(function($data, $client, $server){
  // All client messages can be caught here
  // use $server to manage the stream socket server status
  // Clients can also be disconntected via the $server
  // Use $client to send responses or simply return it like this;
  return 'data processed by the app';
});
// this line is reached only when the server is stopped
exit("server terminated at ".time());
```

The callback will run for every client message the socket receives, it is within this Closure you would initialise your app and based on the contents of the message payload send response/s.
