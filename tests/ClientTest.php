<?php
use PHPUnit\Framework\TestCase;
use sockets\Client;

/**
 * Class ClientTest
 * @package sockets/php-stream-socket-server
 */
class ClientTest extends TestCase
{
    protected $client;
    /**
     * @covers \sockets\Client::__construct()
     */
    public function setup()
    {
        $this->client = new Client();
    }

    public function tearDown()
    {
        unset($this->client);
    }

    /**
     * @covers \sockets\Client::__construct()
     */
    public function testInstance()
    {
        $this->assertInstanceOf('sockets\\Client', $this->client);
    }
}