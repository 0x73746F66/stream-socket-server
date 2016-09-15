<?php
use PHPUnit\Framework\TestCase;
use sockets\StreamSocketServer;

class StreamSocketServerTest extends TestCase
{
    const EPHEMERAL_PORT = 0;
    protected $streamSocketServer;

    /**
     * Untestable due to intended design;
     * StreamSocketServer::listen - It is a recursive function with no input or return value, and also a blocking process.
     * StreamSocketServer::launchJob - forks into a child process, also with no input or return value
     * @covers \sockets\StreamSocketServer::listen()
     * @covers \sockets\StreamSocketServer::launchJob()
     * @covers \sockets\StreamSocketServer::processMessage()
     * @covers \sockets\StreamSocketServer::__construct()
     * @covers \sockets\StreamSocketServer::_default_config()
     */
    public function setup()
    {
        $this->streamSocketServer = new StreamSocketServer([
            'PORT' => self::EPHEMERAL_PORT
        ]);
    }

    /**
     * @covers \sockets\StreamSocketServer::__destruct()
     */
    public function tearDown()
    {
        unset($this->streamSocketServer);
    }

    /**
     * @covers \sockets\StreamSocketServer::__construct()
     * @covers \sockets\StreamSocketServer::_default_config()
     * @covers \sockets\StreamSocketServer::setDebug()
     * @covers \sockets\StreamSocketServer::getDebug()
     * @covers \sockets\StreamSocketServer::__destruct()
     */
    public function testInstance()
    {
        $this->assertInstanceOf('sockets\\StreamSocketServer', $this->streamSocketServer);
    }

    /**
     * @covers \sockets\StreamSocketServer::__construct()
     * @covers \sockets\StreamSocketServer::_default_config()
     * @covers \sockets\StreamSocketServer::setDebug()
     * @covers \sockets\StreamSocketServer::getDebug()
     * @covers \sockets\StreamSocketServer::__destruct()
     */
    public function testSetDebug()
    {
        $this->streamSocketServer->setDebug(true);
        $this->assertTrue($this->streamSocketServer->getDebug());
        $this->streamSocketServer->setDebug(false);
        $this->assertFalse($this->streamSocketServer->getDebug());
    }

    /**
     * @covers \sockets\StreamSocketServer::__construct()
     * @covers \sockets\StreamSocketServer::_default_config()
     * @covers \sockets\StreamSocketServer::setDebug()
     * @covers \sockets\StreamSocketServer::getDebug()
     * @covers \sockets\StreamSocketServer::__destruct()
     */
    public function testDebug()
    {
        $this->streamSocketServer->setDebug(true);
        $this->assertTrue($this->streamSocketServer->getDebug());
        $this->streamSocketServer->setDebug(false);
        $this->assertFalse($this->streamSocketServer->getDebug());
    }

    /**
     * @covers \sockets\StreamSocketServer::__construct()
     * @covers \sockets\StreamSocketServer::_default_config()
     * @covers \sockets\StreamSocketServer::setDebug()
     * @covers \sockets\StreamSocketServer::getDebug()
     * @covers \sockets\StreamSocketServer::isRunning()
     * @covers \sockets\StreamSocketServer::isStopped()
     * @covers \sockets\StreamSocketServer::__destruct()
     */
    public function testIsRunning()
    {
        $this->assertFalse($this->streamSocketServer->isRunning());
    }

    /**
     * @covers \sockets\StreamSocketServer::__construct()
     * @covers \sockets\StreamSocketServer::_default_config()
     * @covers \sockets\StreamSocketServer::setDebug()
     * @covers \sockets\StreamSocketServer::getDebug()
     * @covers \sockets\StreamSocketServer::isStopped()
     * @covers \sockets\StreamSocketServer::__destruct()
     */
    public function testIsStopped()
    {
        $this->assertTrue($this->streamSocketServer->isStopped());
    }

    /**
     * @covers \sockets\StreamSocketServer::__construct()
     * @covers \sockets\StreamSocketServer::_default_config()
     * @covers \sockets\StreamSocketServer::setDebug()
     * @covers \sockets\StreamSocketServer::getDebug()
     * @covers \sockets\StreamSocketServer::isRunning()
     * @covers \sockets\StreamSocketServer::isStopped()
     * @covers \sockets\StreamSocketServer::stop()
     * @covers \sockets\StreamSocketServer::__destruct()
     */
    public function testStop()
    {
        $this->assertInstanceOf('sockets\\StreamSocketServer', $this->streamSocketServer->stop());
    }

    /**
     * @covers \sockets\StreamSocketServer::__construct()
     * @covers \sockets\StreamSocketServer::_default_config()
     * @covers \sockets\StreamSocketServer::setDebug()
     * @covers \sockets\StreamSocketServer::getDebug()
     * @covers \sockets\StreamSocketServer::isRunning()
     * @covers \sockets\StreamSocketServer::isStopped()
     * @covers \sockets\StreamSocketServer::start()
     * @covers \sockets\StreamSocketServer::stop()
     * @covers \sockets\StreamSocketServer::__destruct()
     */
    public function testStart()
    {
        $this->assertInstanceOf('sockets\\StreamSocketServer', $this->streamSocketServer->start());
        $this->assertTrue($this->streamSocketServer->isRunning());
        $this->assertFalse($this->streamSocketServer->isStopped());
        $this->streamSocketServer->stop();
        $this->assertFalse($this->streamSocketServer->isRunning());
        $this->assertTrue($this->streamSocketServer->isStopped());
    }

    /**
     * @covers \sockets\StreamSocketServer::__construct()
     * @covers \sockets\StreamSocketServer::_default_config()
     * @covers \sockets\StreamSocketServer::setDebug()
     * @covers \sockets\StreamSocketServer::getDebug()
     * @covers \sockets\StreamSocketServer::registerCallback()
     * @covers \sockets\StreamSocketServer::__destruct()
     */
    public function testRegisterCallback()
    {
        $mockCallback = function () { };
        $this->assertInstanceOf('sockets\\StreamSocketServer',
            $this->streamSocketServer->registerCallback($mockCallback));
    }

    /**
     * @covers \sockets\StreamSocketServer::__construct()
     * @covers \sockets\StreamSocketServer::_default_config()
     * @covers \sockets\StreamSocketServer::setDebug()
     * @covers \sockets\StreamSocketServer::getDebug()
     * @covers \sockets\StreamSocketServer::isRunning()
     * @covers \sockets\StreamSocketServer::isStopped()
     * @covers \sockets\StreamSocketServer::broadcast()
     * @covers \sockets\StreamSocketServer::__destruct()
     */
    public function testBroadcast()
    {
        $mockMessage1 = 'test message';
        $mockMessage2 = ['txt' => 'some message'];
        $mockMessage3 = json_encode($mockMessage2);
        $this->assertInternalType('bool', $this->streamSocketServer->broadcast($mockMessage1));
        $this->assertInternalType('bool', $this->streamSocketServer->broadcast($mockMessage2));
        $this->assertInternalType('bool', $this->streamSocketServer->broadcast($mockMessage3));
    }

    /**
     * @covers \sockets\StreamSocketServer::__construct()
     * @covers \sockets\StreamSocketServer::_default_config()
     * @covers \sockets\StreamSocketServer::setDebug()
     * @covers \sockets\StreamSocketServer::getDebug()
     * @covers \sockets\StreamSocketServer::removeClientByJobId()
     * @covers \sockets\StreamSocketServer::removeClient()
     * @covers \sockets\StreamSocketServer::__destruct()
     */
    public function testRemoveChild()
    {
        $mockId = uniqid();
        $this->assertInternalType('bool', $this->streamSocketServer->removeClientByJobId($mockId));
    }

    /**
     * @covers \sockets\StreamSocketServer::__construct()
     * @covers \sockets\StreamSocketServer::_default_config()
     * @covers \sockets\StreamSocketServer::setDebug()
     * @covers \sockets\StreamSocketServer::getDebug()
     * @covers \sockets\StreamSocketServer::getClientByJobId()
     * @covers \sockets\StreamSocketServer::__destruct()
     */
    public function testGetChild()
    {
        $mockId = uniqid();
        $this->assertInternalType('bool', $this->streamSocketServer->getClientByJobId($mockId));
    }
}
