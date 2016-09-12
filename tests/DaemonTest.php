<?php
use PHPUnit\Framework\TestCase;
use sockets\StreamSocketDaemon;

class DaemonTest extends TestCase {

  public function testInit() {
    $daemon = new StreamSocketDaemon();

    $this->assertInternalType('int', $d->parentPID);
  }
}
