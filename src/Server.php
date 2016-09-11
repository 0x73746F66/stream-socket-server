<?php
declare(strict_types = 1);
namespace sockets;

/**
 * Class Server
 * @package sockets/php-stream-socket-server
 */
final class Server implements iServer {
  /**
   * @var StreamSocketServer
   */
  protected $streamSocketServer;

  /**
   * Server constructor.
   * @param StreamSocketServer $streamSocketServer
   */
  final public function __construct(StreamSocketServer &$streamSocketServer) {
    $this->streamSocketServer = $streamSocketServer;
  }

  /**
   * @return bool
   */
  final public function start(): bool {
    return $this->streamSocketServer->start()->isRunning();
  }

  /**
   * @return bool
   */
  final public function stop(): bool {
    return $this->streamSocketServer->stop()->isStopped();
  }

  /**
   * @param $data
   * @return bool
   */
  final public function broadcast($data): bool {
    echo "[INFO][".__CLASS__."::".__FUNCTION__."] " . time() . "\n";
    return $this->streamSocketServer->broadcast($data);
  }

  /**
   * @param Client $client
   * @return bool
   */
  final public function disconnectClient(Client &$client): bool {
    return $this->streamSocketServer->removeClientByJobId($client->getId());
  }
}
