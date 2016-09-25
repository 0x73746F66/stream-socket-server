<?php
use PHPUnit\Framework\TestCase;
use sockets\Server;

/**
 * Class ServerTest
 * @package sockets/php-stream-socket-server
 */
class ServerTest extends TestCase
{
    /**
     * @var Server
     */
    protected $server;

    public function setup()
    {
        $this->server = new Server();
    }

    public function tearDown()
    {
        unset($this->server);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('sockets\\Server', $this->server);
    }
    /**
     * @covers \sockets\Server::attachStreamSocketServer()
     */
    public function testAttachStreamSocketServer()
    {
        $this->markTestIncomplete(__CLASS__.'::'.__FUNCTION__.' https://github.com/sebastianbergmann/phpunit/issues/2296');
        $mock = $this->getMockBuilder('sockets\StreamSocketServer')
                     ->disableOriginalConstructor()
                     ->disableOriginalClone()
                     ->disableArgumentCloning()
                     ->disallowMockingUnknownTypes()
                     ->getMock();

        $mock->method('getDebug')->willReturn([]);
        $mock->method('__destruct');
        $this->assertInstanceOf('sockets\\Server', $this->server->attachStreamSocketServer($mock));
    }

    /**
     * @covers \sockets\Server::__construct()
     * @covers \sockets\Server::attachStreamSocketServer()
     * @covers \sockets\Server::start()
     */
    public function testStart()
    {
        $this->markTestIncomplete(__CLASS__.'::'.__FUNCTION__.' https://github.com/sebastianbergmann/phpunit/issues/2296');
        $mock = $this->getMockBuilder('sockets\StreamSocketServer')
                     ->disableOriginalConstructor()
                     ->getMock();
        $mock->method('getDebug')
             ->willReturn([]);
        $mock->method('__destruct');
        $mock->method('start')
             ->will($this->returnSelf());
        $mock->method('isRunning')
             ->willReturn(true);
        $this->server->attachStreamSocketServer($mock);

        $this->assertTrue($this->server->start());
    }

    /**
     * @covers \sockets\Server::attachStreamSocketServer()
     * @covers \sockets\Server::stop()
     */
    public function testStop()
    {
        $this->markTestIncomplete(__CLASS__.'::'.__FUNCTION__.' https://github.com/sebastianbergmann/phpunit/issues/2296');
        $mock = $this->getMockBuilder('sockets\StreamSocketServer')
                     ->disableOriginalConstructor()
                     ->getMock();
        $mock->method('getDebug')
             ->willReturn([]);
        $mock->method('__destruct');
        $mock->method('start')
             ->will($this->returnSelf());
        $mock->method('isStopped')
             ->willReturn(true);
        $this->server->attachStreamSocketServer($mock);

        $this->assertTrue($this->server->stop());
    }
}
