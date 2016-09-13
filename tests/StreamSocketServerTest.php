<?php
use PHPUnit\Framework\TestCase;
use sockets\StreamSocketServer;

class StreamSocketServerTest extends TestCase {
  /*
   * Untestable due to intended design;
   * StreamSocketServer::listen - It is a recursive function with no input or return value, and also a blocking process.
   * StreamSocketServer::launchJob - forks into a child process, also with no input or return value
   */
  public function testInstance() {
    $streamSocketServer = new StreamSocketServer();
    $this->assertInstanceOf('sockets\\StreamSocketServer', $streamSocketServer);
  }
  public function testSetDebug() {
    $streamSocketServer = new StreamSocketServer();
    $streamSocketServer->setDebug(true);
    $this->assertTrue($streamSocketServer->getDebug());
    $streamSocketServer->setDebug(false);
    $this->assertFalse($streamSocketServer->getDebug());
  }
  public function testDebugOn() {
    $mochConfig1 = ['DEBUG' => true];
    $streamSocketServer = new StreamSocketServer($mochConfig1);
    $this->assertTrue($streamSocketServer->getDebug());
    $streamSocketServer->setDebug(false); // needed to prevent output deemed risky by phpunit
    $this->assertNotTrue($streamSocketServer->getDebug());
  }
  public function testDebugOff() {
    $mochConfig1 = ['DEBUG' => false];
    $streamSocketServer = new StreamSocketServer($mochConfig1);
    $this->assertFalse($streamSocketServer->getDebug());
  }
  public function testIsRunning() {
    $streamSocketServer = new StreamSocketServer();
    $this->assertFalse($streamSocketServer->isRunning());
  }
  public function testIsStopped() {
    $streamSocketServer = new StreamSocketServer();
    $this->assertTrue($streamSocketServer->isStopped());
  }
  public function testStop() {
    $streamSocketServer = new StreamSocketServer();
    $this->assertInstanceOf('sockets\\StreamSocketServer', $streamSocketServer->stop());
  }
  public function testStart() {
    $streamSocketServer = new StreamSocketServer();
    $this->assertInstanceOf('sockets\\StreamSocketServer', $streamSocketServer->start());
    $this->assertTrue($streamSocketServer->isRunning());
    $this->assertFalse($streamSocketServer->isStopped());
    $streamSocketServer->stop();
    $this->assertFalse($streamSocketServer->isRunning());
    $this->assertTrue($streamSocketServer->isStopped());
  }
  public function testRegisterCallback() {
    $mockCallback = function(){};
    $streamSocketServer = new StreamSocketServer();
    $this->assertInstanceOf('sockets\\StreamSocketServer', $streamSocketServer->registerCallback($mockCallback));
  }
  public function testBroadcast() {
    $mockMessage1 = 'test message';
    $mockMessage2 = ['txt'=>'some message'];
    $mockMessage3 = json_encode($mockMessage2);
    $streamSocketServer = new StreamSocketServer();
    $this->assertInternalType('bool', $streamSocketServer->broadcast($mockMessage1));
    $this->assertInternalType('bool', $streamSocketServer->broadcast($mockMessage2));
    $this->assertInternalType('bool', $streamSocketServer->broadcast($mockMessage3));
  }
  public function testRemoveChild() {
    $mockId = uniqid();
    $streamSocketServer = new StreamSocketServer();
    $this->assertInternalType('bool', $streamSocketServer->removeClientByJobId($mockId));
  }
  public function testGetChild() {
    $mockId = uniqid();
    $streamSocketServer = new StreamSocketServer();
    $this->assertInternalType('bool', $streamSocketServer->getClientByJobId($mockId));
  }
  
}
