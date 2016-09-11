<?php
namespace sockets;

interface iServer {
  public function __construct(StreamSocketServer &$streamSocketServer);
  public function start(): bool;
  public function stop(): bool;
  public function broadcast($data): bool;
  public function disconnectClient(Client &$client): bool;
}
