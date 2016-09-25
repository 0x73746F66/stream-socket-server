<?php
namespace sockets;

/**
 * Interface iClient
 * @package sockets/php-stream-socket-server
 */
interface iClient
{
    /**
     * @param $string
     * @return bool
     */
    public static function isJson($string): bool;

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
     * @param string $response
     * @return bool
     */
    public function sendText(string $response) : bool;

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

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param string $id
     * @return Client
     */
    public function setId(string $id): Client;

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @return string
     */
    public function getDataRaw(): string;

    /**
     * @param string $data
     * @return Client
     */
    public function setData(string $data): Client;
}
