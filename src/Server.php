<?php
declare(strict_types = 1);
namespace sockets;

/**
 * Class Server
 * @package sockets/php-stream-socket-server
 */
class Server implements iServer
{
    protected $_callerJobId;
    protected $_callerData = [];

    /**
     * @var StreamSocketServer
     */
    protected $streamSocketServer;

    /**
     * iServer constructor.
     * @param string $callerJobId
     * @param array  $data
     */
    public function __construct(string $callerJobId, array $data = [])
    {
        $this->_callerJobId = $callerJobId;
        $this->_callerData  = $data;
    }

    /**
     * @param StreamSocketServer $streamSocketServer
     * @return Server
     */
    final public function attachStreamSocketServer(StreamSocketServer &$streamSocketServer): Server
    {
        $this->streamSocketServer = &$streamSocketServer;

        return $this;
    }

    /**
     * @return bool
     */
    final public function start(): bool
    {
        return $this->streamSocketServer->start()->isRunning();
    }

    /**
     * @return bool
     */
    final public function stop(): bool
    {
        return $this->streamSocketServer->stop()->isStopped();
    }

    /**
     * @param $data
     * @return bool
     */
    final public function broadcast($data): bool
    {
        return $this->streamSocketServer->broadcast($data, $this->_callerJobId);
    }

    /**
     * @param string $clientId
     * @return bool|Client
     */
    final public function getClient(string $clientId)
    {
        return $this->streamSocketServer->getClientByJobId($clientId);
    }

    /**
     * @param Client $client
     * @return bool
     */
    final public function disconnectClient(Client &$client): bool
    {
        return $this->streamSocketServer->removeClientByJobId($client->getId());
    }

}
