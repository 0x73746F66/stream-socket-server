<?php
declare(strict_types = 1);
namespace sockets;

/**
 * Class StreamSocketServer
 * @package sockets
 */
class StreamSocketServer {
  protected $clients = [];
  protected $server;
  private $_config = [];
  private $_callback;

  /**
   * StreamSocketServer constructor.
   * @param array $config
   */
  function __construct(array $config = []) {
    $this->_config = array_merge(static::_default_config(), $this->_config, $config);
  }

  final static protected function _default_config(): array {
    $port = getenv('C9_PORT');
    if (!is_numeric($port)) {
      $port = getenv('PORT');
    }
    if (!is_numeric($port)) {
      $port = 8082;
    }
    $ip = getenv('C9_IP');
    if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
      $ip = getenv('IP');
    }
    if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
      $ip = '127.0.0.1';
    }
    $hostname = getenv('C9_HOSTNAME');
    if (filter_var($hostname, FILTER_VALIDATE_URL) === false) {
      $hostname = getenv('HOSTNAME');
    }
    if (filter_var($hostname, FILTER_VALIDATE_URL) === false) {
      $hostname = 'localhost';
    }

    return [
      'SOCKET_PROTO'            => 'tcp',
      'IP'                      => $ip,
      'HOSTNAME'                => $hostname,
      'SOCKET_PORT'             => $port
    ];
  }

  /**
   * @return StreamSocketServer
   */
  public function start(): StreamSocketServer {
    echo "[INFO][".__CLASS__."::".__FUNCTION__."] " . strtoupper($this->_config['SOCKET_PROTO']) . " listening on "
         . $this->_config['HOSTNAME'] . ":" . $this->_config['SOCKET_PORT'] . "\n";
    $this->server = stream_socket_server(strtolower($this->_config['SOCKET_PROTO']) . "://" . $this->_config['IP'] . ":" . $this->_config['SOCKET_PORT'],
                                         $errno,
                                         $errorMessage);
    if ($this->server === false) {
      error_log("[ERROR][".__CLASS__."::".__FUNCTION__."] Could not bind to socket: $errorMessage");
    }
    return $this;
  }

  /**
   * @param \Closure $callback
   * @return StreamSocketServer
   */
  public function registerCallback(\Closure $callback): StreamSocketServer {
    if (is_callable($callback)) {
      $this->_callback = $callback;
    }
    return $this;
  }

  /**
   * Listens for any new incoming client streams
   */
  public function listen() {
    if ($h = @stream_socket_accept($this->server, -1)) {
      $client = new ClientStreamSocket($h);
      echo "[INFO][".__CLASS__."::".__FUNCTION__."] received client socket {$client->jobId}\n";
      if ($client->validateWebSocket()) {
        $client->upgradeWebSocket();
      }
      $this->clients[] = &$client;
      $this->launchJob($client);
    }
    $this->listen();
  }

  /**
   * @param ClientStreamSocket $client
   */
  protected function launchJob(ClientStreamSocket &$client) {
    $pid = pcntl_fork();
    if ($pid == -1) {
      // Problem launching the job
      error_log("[ERROR][".__CLASS__."::".__FUNCTION__."] Could not launch new job, exiting\n");
      $client->disconnect();
    } elseif ($pid) {
      //echo "[WARN] encountered parent process in StreamSocketServer::launchJob\n";
    } else {
      //Forked child
      echo "[INFO][".__CLASS__."::".__FUNCTION__."] Doing something fun in pid " . getmypid() . "\n";
      $this->await($client);
    }
  }

  /**
   * @param $data
   */
  public function broadcast($data) {
    echo "[INFO][".__CLASS__."::".__FUNCTION__."] " . time() . "\n";
    foreach ($this->clients as $client) {
      $client($data);
    }
  }

  /**
   * @param array              $data
   * @param ClientStreamSocket $client
   */
  protected function processMessage(array $data, ClientStreamSocket &$client) {
    echo "[INFO][".__CLASS__."::".__FUNCTION__."] " . time() . "\n";
    $client( call_user_func($this->_callback, $data, $client) );
  }

  /**
   * @param ClientStreamSocket $client
   */
  protected function await(ClientStreamSocket &$client) {
    usleep(20);
    echo "[INFO][".__CLASS__."::".__FUNCTION__."] " . time() . "\n";
    if ($client->pendingMessage()){
      if ($client->isWebSocket) {
        $data = $client->getData();
        $this->processMessage($data, $client);
        $this->await($client);
      } else {
        $data = $client->getDataRaw();
        $this->broadcast($data);
        $client->disconnect();
      }
    }
  }

  /**
   *
   */
  function __destruct() {
    echo "[INFO][".__CLASS__."::".__FUNCTION__."] Thread closing\n";
    exit(0);
  }
}
