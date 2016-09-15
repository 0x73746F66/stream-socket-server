<?php
use PHPUnit\Framework\TestCase;
use sockets\Client;
use sockets\ClientStreamSocket;

class ClientStreamSocketTest extends TestCase
{
    protected $clientStreamSocket;
    private $_data = ['uri'=>'127.0.0.1:8082'];
    private $_headers = "GET /chat HTTP/1.1\r\n"
    . "Host: example.com:8000\r\n"
    . "Upgrade: websocket\r\n"
    . "Connection: Upgrade\r\n"
    . "Sec-WebSocket-Key: dGhlIHNhbXBsZSBub25jZQ==\r\n"
    . "Sec-WebSocket-Version: 13\r\n"
    . "\r\n\r\n";

    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     */
    public function setup()
    {
        $this->clientStreamSocket = new ClientStreamSocket(new Client());
    }

    public function tearDown()
    {
        unset($this->clientStreamSocket);
    }

    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::attachClientHandle()
     * @covers \sockets\ClientStreamSocket::getHandle()
     * @covers \sockets\ClientStreamSocket::__invoke()
     * @covers \sockets\ClientStreamSocket::_encode()
     * @covers \sockets\ClientStreamSocket::_decode()
     */
    public function testInstance()
    {
        $this->assertInstanceOf('sockets\\ClientStreamSocket', $this->clientStreamSocket);
    }

    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::getJobId()
     */
    public function testJobId()
    {
        $this->assertInternalType('string', $this->clientStreamSocket->getJobId());
    }

    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::getHeaders()
     */
    public function testHeaders()
    {
        $this->assertInternalType('string', $this->clientStreamSocket->getHeaders());
    }

    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::getLastDataLength()
     */
    public function testLastDataLength()
    {
        $this->assertInternalType('int', $this->clientStreamSocket->getLastDataLength());
    }

    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::isConnected()
     * @covers \sockets\ClientStreamSocket::isClosed()
     * @covers \sockets\ClientStreamSocket::disconnect()
     * @covers \sockets\ClientStreamSocket::getClient()
     * @covers \sockets\ClientStreamSocket::getHandle()
     */
    public function testIsConnected()
    {
        $this->assertInternalType('bool', $this->clientStreamSocket->disconnect());
        $this->assertInternalType('bool', $this->clientStreamSocket->isConnected());
    }
    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::getStatus()
     * @covers \sockets\ClientStreamSocket::setStatus()
     * @covers \sockets\ClientStreamSocket::getClient()
     * @covers \sockets\ClientStreamSocket::getHandle()
     * @covers \sockets\ClientStreamSocket::isConnected()
     * @covers \sockets\ClientStreamSocket::isClosed()
     */
    public function testStatus()
    {
        $this->clientStreamSocket->setStatus($this->_data);
        $this->assertInternalType('array', $this->clientStreamSocket->getStatus());
    }

    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::validateWebSocket()
     * @covers \sockets\ClientStreamSocket::setHeaders()
     * @covers \sockets\ClientStreamSocket::getHeaders()
     * @covers \sockets\ClientStreamSocket::getClient()
     * @covers \sockets\ClientStreamSocket::getHandle()
     * @covers \sockets\ClientStreamSocket::isConnected()
     * @covers \sockets\ClientStreamSocket::isClosed()
     */
    public function testValidateWebSocket()
    {
        $this->clientStreamSocket->setHeaders($this->_headers);
        $this->assertTrue($this->clientStreamSocket->validateWebSocket());
    }

    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::validateWebSocket()
     * @covers \sockets\ClientStreamSocket::setHeaders()
     * @covers \sockets\ClientStreamSocket::getHeaders()
     * @covers \sockets\ClientStreamSocket::getClient()
     * @covers \sockets\ClientStreamSocket::getHandle()
     * @covers \sockets\ClientStreamSocket::isConnected()
     * @covers \sockets\ClientStreamSocket::isClosed()
     */
    public function testValidateWebSocketFailForTCP()
    {
        $this->clientStreamSocket->setHeaders(json_encode($this->_data));
        $this->assertFalse($this->clientStreamSocket->validateWebSocket());
    }

    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::upgradeWebSocket()
     * @covers \sockets\ClientStreamSocket::getHandle()
     */
    public function testUpgradeWebSocket()
    {
        $this->assertInstanceOf('sockets\\ClientStreamSocket', $this->clientStreamSocket->upgradeWebSocket());
    }
    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::pendingMessage()
     * @covers \sockets\ClientStreamSocket::getClient()
     * @covers \sockets\ClientStreamSocket::getHandle()
     * @covers \sockets\ClientStreamSocket::isConnected()
     * @covers \sockets\ClientStreamSocket::isClosed()
     */
    public function testPendingMessage()
    {
        $this->assertInternalType('bool', $this->clientStreamSocket->pendingMessage());
    }

    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::getHeaders()
     * @covers \sockets\ClientStreamSocket::setData()
     * @covers \sockets\ClientStreamSocket::getDataRaw()
     */
    public function testGetDataRaw()
    {
        $this->clientStreamSocket->setData($this->clientStreamSocket->getHeaders());
        $this->assertInternalType('string', $this->clientStreamSocket->getDataRaw());
    }

    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::setData()
     * @covers \sockets\ClientStreamSocket::getData()
     * @covers \sockets\ClientStreamSocket::getDataRaw()
     */
    public function testGetData()
    {
        $this->clientStreamSocket->setData(json_encode($this->_data));
        $this->assertObjectHasAttribute('uri', $this->clientStreamSocket->getData());
    }
    /**
     * @covers \sockets\ClientStreamSocket::__construct()
     * @covers \sockets\ClientStreamSocket::setPeer()
     * @covers \sockets\ClientStreamSocket::getPeer()
     * @covers \sockets\ClientStreamSocket::getClient()
     * @covers \sockets\ClientStreamSocket::getHandle()
     * @covers \sockets\ClientStreamSocket::isConnected()
     * @covers \sockets\ClientStreamSocket::isClosed()
     */
    public function testGetPeer()
    {
        $this->clientStreamSocket->setPeer($this->_data['uri']);
        $this->assertInternalType('string', $this->clientStreamSocket->getPeer());
    }
}
