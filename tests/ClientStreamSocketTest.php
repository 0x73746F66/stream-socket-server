<?php
use PHPUnit\Framework\TestCase;
use sockets\ClientStreamSocket;

class ClientStreamSocketTest extends TestCase
{
    protected $clientStreamSocket;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->clientStreamSocket = new ClientStreamSocket();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('sockets\\ClientStreamSocket', $this->clientStreamSocket);
    }

    public function testJobId()
    {
        $this->assertInternalType('string', $this->clientStreamSocket->getJobId());
    }

    public function testData()
    {
        $this->assertNotNull($this->clientStreamSocket->getData());
        $this->assertNotFalse($this->clientStreamSocket->getData());
    }

    public function testHeaders()
    {
        $this->assertInternalType('string', $this->clientStreamSocket->getHeaders());
    }

    public function testLastDataLength()
    {
        $this->assertInternalType('int', $this->clientStreamSocket->getLastDataLength());
    }

    public function testStatus()
    {
        $this->assertInternalType('array', $this->clientStreamSocket->getStatus());
    }
}
