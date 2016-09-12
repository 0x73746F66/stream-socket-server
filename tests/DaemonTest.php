<?php
use PHPUnit\Framework\TestCase;
use sockets\StreamSocketDaemon;

class DaemonTest extends TestCase {

  public function testInit() {
    $d = new StreamSocketDaemon();

    $this->assertInternalType('int', $d->parentPID);
  }
}
