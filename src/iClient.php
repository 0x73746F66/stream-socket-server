<?php
namespace sockets;

/**
 * Interface iClient
 * @package sockets/php-stream-socket-server
 */
interface iClient
{
    /**
     * iClient constructor.
     */
    public function __construct();

    /**
     * @param ClientStreamSocket $clientStreamSocket
     * @return Client
     */
    public function attachClientStreamSocket(ClientStreamSocket &$clientStreamSocket): Client;

    /**
     * @param $response
     * @return bool
     */
    public function __invoke($response): bool;

    /**
     * @param $response
     * @return bool
     */
    public function sendText($response) : bool;

    /**
     * @param array $response
     * @return bool
     */
    public function sendJSON(array $response): bool;

    /**
     * @return array
     */
    public function getStatus(): array;

    /**
     * @param array $status
     * @return Client
     */
    public function setStatus(array $status): Client;
}
