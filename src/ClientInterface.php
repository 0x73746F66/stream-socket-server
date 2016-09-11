<?php
namespace sockets;

/**
 * Interface iClient
 * @package sockets/php-stream-socket-server
 */
interface iClient {
  /**
   * iClient constructor.
   * @param ClientStreamSocket $clientStreamSocket
   * @param array              $status
   */
  public function __construct(ClientStreamSocket &$clientStreamSocket, array $status);

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
