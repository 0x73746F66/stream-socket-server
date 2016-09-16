<?php
use PHPUnit\Framework\TestCase;
use sockets\Server;

/**
 * Class ServerTest
 * @package sockets/php-stream-socket-server
 */
class ServerTest extends TestCase
{
    protected $server;
    /**
     * @covers \sockets\Server::__construct()
     */
    public function setup()
    {
        $this->server = new Server();
    }

    public function tearDown()
    {
        unset($this->server);
    }

    /**
     * @covers \sockets\Server::__construct()
     */
    public function testInstance()
    {
        $this->assertInstanceOf('sockets\\Server', $this->server);
    }
    /**
     * @covers \sockets\Server::__construct()
     * @covers \sockets\Server::attachStreamSocketServer()
     */
    public function testAttachStreamSocketServer()
    {
        $mock = $this->getMockBuilder('sockets\StreamSocketServer')
                     ->disableOriginalConstructor()
                     ->disableOriginalClone()
                     ->disableArgumentCloning()
                     ->disallowMockingUnknownTypes()
                     ->getMock();

        $this->assertInstanceOf('sockets\\Server', $this->server->attachStreamSocketServer($mock));
    }
    /**
     * @covers \sockets\Server::__construct()
     * @covers \sockets\Server::attachStreamSocketServer()
     * @covers \sockets\Server::start()
     */
    public function testStart()
    {
        $mock = $this->getMockBuilder('sockets\StreamSocketServer')
                     ->getMock();
        $mock->method('start')
             ->will($this->returnSelf());
        $mock->method('isRunning')
             ->willReturn(true);
        $this->server->attachStreamSocketServer($mock);
        
        $this->assertTrue($this->server->start());
    }
    /**
     * @covers \sockets\Server::__construct()
     * @covers \sockets\Server::attachStreamSocketServer()
     * @covers \sockets\Server::stop()
     */
    public function testStop()
    {
        $mock = $this->getMockBuilder('sockets\StreamSocketServer')
                     ->getMock();
        $mock->method('start')
             ->will($this->returnSelf());
        $mock->method('isStopped')
             ->willReturn(true);
        $this->server->attachStreamSocketServer($mock);
        
        $this->assertTrue($this->server->stop());
    }
}
