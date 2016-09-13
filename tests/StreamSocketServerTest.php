<?php
/*
 * Untestable due to intended design;
 * StreamSocketServer::listen - It is a recursive function with no input or return value, and also a blocking process.
 * StreamSocketServer::launchJob - forks into a child process, also with no input or return value
 */
use PHPUnit\Framework\TestCase;
use sockets\StreamSocketServer;

class StreamSocketServerTest extends TestCase {
  const EPHEMERAL_PORT = 0;
  protected $streamSocketServer;
  public function __construct($name = null, array $data = [], $dataName = '') {
    parent::__construct($name, $data, $dataName);
    $this->streamSocketServer = new StreamSocketServer([
      'PORT' => self::EPHEMERAL_PORT
    ]);
  }

  public function testInstance() {
    $this->assertInstanceOf('sockets\\StreamSocketServer', $this->streamSocketServer);
  }
  public function testSetDebug() {
    $this->streamSocketServer->setDebug(true);
    $this->assertTrue($this->streamSocketServer->getDebug());
    $this->streamSocketServer->setDebug(false);
    $this->assertFalse($this->streamSocketServer->getDebug());
  }
  public function testDebugOn() {
    $this->streamSocketServer->setDebug(true);
    $this->assertTrue($this->streamSocketServer->getDebug());
    $this->streamSocketServer->setDebug(false); // needed to prevent output deemed risky by phpunit
    $this->assertNotTrue($this->streamSocketServer->getDebug());
  }
  public function testDebugOff() {
    $this->streamSocketServer->setDebug(false);
    $this->assertFalse($this->streamSocketServer->getDebug());
  }
  public function testIsRunning() {
    $this->assertFalse($this->streamSocketServer->isRunning());
  }
  public function testIsStopped() {
    $this->assertTrue($this->streamSocketServer->isStopped());
  }
  public function testStop() {
    $this->assertInstanceOf('sockets\\StreamSocketServer', $this->streamSocketServer->stop());
  }
  public function testStart() {
    $this->assertInstanceOf('sockets\\StreamSocketServer', $this->streamSocketServer->start());
    $this->assertTrue($this->streamSocketServer->isRunning());
    $this->assertFalse($this->streamSocketServer->isStopped());
    $this->streamSocketServer->stop();
    $this->assertFalse($this->streamSocketServer->isRunning());
    $this->assertTrue($this->streamSocketServer->isStopped());
  }
  public function testRegisterCallback() {
    $mockCallback = function(){};
    $this->assertInstanceOf('sockets\\StreamSocketServer', $this->streamSocketServer->registerCallback($mockCallback));
  }
  public function testBroadcast() {
    $mockMessage1 = 'test message';
    $mockMessage2 = ['txt'=>'some message'];
    $mockMessage3 = json_encode($mockMessage2);
    $this->assertInternalType('bool', $this->streamSocketServer->broadcast($mockMessage1));
    $this->assertInternalType('bool', $this->streamSocketServer->broadcast($mockMessage2));
    $this->assertInternalType('bool', $this->streamSocketServer->broadcast($mockMessage3));
  }
  public function testRemoveChild() {
    $mockId = uniqid();
    $this->assertInternalType('bool', $this->streamSocketServer->removeClientByJobId($mockId));
  }
  public function testGetChild() {
    $mockId = uniqid();
    $this->assertInternalType('bool', $this->streamSocketServer->getClientByJobId($mockId));
  }
}
