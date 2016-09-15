<?php
declare(strict_types = 1);
namespace sockets;

/**
 * Class Client
 * @package sockets/php-stream-socket-server
 */
class Client implements iClient
{
    /**
     * @var array
     */
    public $status;
    /**
     * @var string
     */
    public $id;
    /**
     * @var ClientStreamSocket
     */
    protected $clientStreamSocket;

    final public function __construct() { }

    /**
     * @param ClientStreamSocket $clientStreamSocket
     * @return Client
     */
    final public function attachClientStreamSocket(ClientStreamSocket &$clientStreamSocket): Client
    {
        $this->clientStreamSocket = &$clientStreamSocket;
        $this->id = $clientStreamSocket->getJobId();

        return $this;
    }

    /**
     * @param $response
     * @return bool
     */
    final public function __invoke($response): bool
    {
        if (is_string($response)) {
            return $this->sendText($response);
        } elseif (is_array($response)) {
            return $this->sendJSON($response);
        }
        return false;
    }

    /**
     * @param $response
     * @return bool
     */
    final public function sendText($response): bool
    {
        if (@stream_socket_sendto($this->clientStreamSocket->getHandle(),
                ClientStreamSocket::_encode($response)) === -1
        ) {
            return false;
        }
        return true;
    }

    /**
     * @param array $response
     * @return bool
     */
    final public function sendJSON(array $response): bool
    {
        if (@stream_socket_sendto($this->clientStreamSocket->getHandle(),
                ClientStreamSocket::_encode(json_encode($response,
                    JSON_ERROR_INF_OR_NAN |
                    JSON_NUMERIC_CHECK |
                    JSON_PRESERVE_ZERO_FRACTION))) === -1
        ) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Client
     */
    public function setId(string $id): Client
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return array
     */
    final public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @param array $status
     * @return Client
     */
    final public function setStatus(array $status): Client
    {
        $this->status = $status;
        return $this;
    }
}
