[![PHP version](https://badge.fury.io/ph/sockets%2Fphp-stream-socket-server.svg)](https://badge.fury.io/ph/sockets%2Fphp-stream-socket-server)
[![Build Status](https://travis-ci.org/chrisdlangton/php-stream-socket-server.svg?branch=master)](https://travis-ci.org/chrisdlangton/php-stream-socket-server)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/52af35bac9b44ab890f4d6a08d4e65c9)](https://www.codacy.com/app/chrislangton84/php-stream-socket-server?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=chrisdlangton/php-stream-socket-server&amp;utm_campaign=Badge_Grade)
![Package Size](https://reposs.herokuapp.com/?path=chrisdlangton/php-stream-socket-server&style=flat)
[![Dependency Status](https://www.versioneye.com/user/projects/57d6475c87b0f6003c14c523/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/57d6475c87b0f6003c14c523)
[![PHPPackages Rank](http://phppackages.org/p/sockets/php-stream-socket-server/badge/rank.svg)](http://phppackages.org/p/sockets/php-stream-socket-server)
[![PHPPackages Referenced By](http://phppackages.org/p/sockets/php-stream-socket-server/badge/referenced-by.svg)](http://phppackages.org/p/sockets/php-stream-socket-server)

# php-stream-socket-server
Provides a bootstrapable server for WebSockets

## Requirements

- php 7.0.x

## Features

- Support for WebSocket clients (via the W3c spec on HTTP/1.1 UPGRADE)
- Accepts a standard TCP socket client connection
- Capable of broadcasting to all client WebSockets from a cli input
- Your PHP application can intercept all client messages and respond
- Capable of server push, on-demand to any connected client
- Exposes all server and client functionality to the PHP application

## Installation

> This project will strictly follow semantic versions.

Simply add to your composer.json

```
{
  "require": {
    "php": "^7.0",
    "sockets/php-stream-socket-server": "^1.0"
  }
}
```

and run `composer install`

## Configuration

> All configuration arguments can be set using environment variables

The `StreamSocketDaemon` constructor takes an array of arguments that will configure the following;

- DEBUG: output will be displayed to help with debugging
    - defaults: `false`
- IP: defines the current machines public IP that a client will try to establish a socket connection to the server
    - default: `127.0.0.1`
- PORT: defines a port to bind to, to receive client socket requests
    - default: `8082`
- HOSTNAME: define a fully qualified domain name for this machine that a client will try to establish a socket connection to the server
    - default: `localhost`

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
  // Clients can also be disconnected via the $server
  // Use $client to send responses or simply return it like this;
  return $data;
});
// this line is reached only when the server is stopped
exit("server terminated at ".time());
```

The callback will run for every client message the socket receives, it is within this Closure you would initialise your app and based on the contents of the message payload send response/s.

## Road map

- Close all connected client sockets manually on `Server::stop` instead of trusting PHP object destruct which may not fire at the expected moment
- Add capability to establish a UDP socket
- Add an example of a standard JavaScript `WebSocket` client
- Expose a method return a Client construct for a `Client::id`
