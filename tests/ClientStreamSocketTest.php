<?php
use PHPUnit\Framework\TestCase;
use sockets\ClientStreamSocket;

class ClientStreamSocketTest extends TestCase {
    protected $clientStreamSocket;
    
    public function __construct() {
        $this->clientStreamSocket = new ClientStreamSocket();
    }

    public function testInstace() {
        $this->assertInstanceOf('sockets\\ClientStreamSocket', $this->clientStreamSocket);
    }
}