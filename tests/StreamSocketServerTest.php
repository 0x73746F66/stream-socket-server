<?php
use PHPUnit\Framework\TestCase;
use sockets\StreamSocketServer;

class StreamSocketServerTest extends TestCase {

  public function testInstance() {
    $streamSocketServer = new StreamSocketServer();

    $this->assertInstanceOf('StreamSocketServer', $streamSocketServer);
  }
}
