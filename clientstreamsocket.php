<?php
declare(strict_types = 1);
namespace sockets;

class ClientStreamSocket {
  const MAGIC = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
  const MAX_INCOMING_MSG_LENGTH = 1048576;
  public $jobId;
  private $_SecWebSocketKey;
  public $isWebSocket;
  private $_status;
  protected $handle;
  protected $headers;
  protected $lastDataLength = 0;
  protected $data;

  function __construct(&$handle) {
    $type = null;
    try {
      $type = get_resource_type($handle);
    } catch (\Throwable $e) {
      error_log('[ERROR]['.__CLASS__.'::'.__FUNCTION__.'] requires a stream resource. exiting');
      exit;
    }
    if ($type !== 'stream') {
      error_log('[ERROR]['.__CLASS__.'::'.__FUNCTION__.'] requires a stream resource. exiting');
      exit;
    }
    $this->jobId = uniqid();
    $this->handle = &$handle;
    $this->headers = stream_get_line($this->handle, 65535, "\r\n\r\n");
    $this->peer = stream_socket_get_name($this->handle, true);
    $this->_status = socket_get_status($this->handle);
  }

  public function __invoke($response) {
    stream_socket_sendto($this->handle, static::_encode($response));
  }

  public function validateWebSocket() {
    if (!preg_match('#^Sec-WebSocket-Key: (\S+)#mi', $this->headers, $match)) {
      return false;
    }
    $this->_SecWebSocketKey = $match[1];
    $this->isWebSocket = true;
    return true;
  }

  public function upgradeWebSocket() {
    fwrite($this->handle, "HTTP/1.1 101 Switching Protocols\r\n"
                          . "Upgrade: websocket\r\n"
                          . "Connection: Upgrade\r\n"
                          . "Sec-WebSocket-Accept: " . base64_encode(sha1($this->_SecWebSocketKey . self::MAGIC, true))
                          . "\r\n\r\n");

  }

  public function send(array $response) {
    stream_socket_sendto($this->handle,
                         static::_encode(json_encode($response,
                                                     JSON_ERROR_INF_OR_NAN |
                                                     JSON_NUMERIC_CHECK |
                                                     JSON_PRESERVE_ZERO_FRACTION)));
  }

  public function getDataRaw() {
    return $this->data;
  }
  public function getData() {
    return json_decode($this->data);
  }

  public function pendingMessage() {
    $this->_status = socket_get_status($this->handle);
    if ($this->isWebSocket) {
      $stream = stream_socket_recvfrom($this->handle, self::MAX_INCOMING_MSG_LENGTH);
      $data = static::_decode($stream);
      if (strlen($data) !== $this->lastDataLength) {
        $this->data = $data;
        return true;
      }
    } else {
      $data = $this->headers;
      stream_socket_sendto($this->handle, $this->peer . " ☚ (<‿<)☚\r\n");
      $this->data = $data;
      return true;
    }
    return false;
  }

  public function disconnect(){
    fclose($this->handle);
    exit(0);
  }

  final static private function _decode($frame) {
    $len = ord($frame[1]) & 127;
    if ($len === 126) {
      $ofs = 8;
    } elseif ($len === 127) {
      $ofs = 14;
    } else {
      $ofs = 6;
    }
    $text = '';
    for ($i = $ofs; $i < strlen($frame); $i++) {
      $text .= $frame[$i] ^ $frame[$ofs - 4 + ($i - $ofs) % 4];
    }

    return $text;
  }

  final static private function _encode($text) {
    $b   = 129; // FIN + text frame
    $len = strlen($text);
    if ($len < 126) {
      return pack('CC', $b, $len) . $text;
    } elseif ($len < 65536) {
      return pack('CCn', $b, 126, $len) . $text;
    } else {
      return pack('CCNN', $b, 127, 0, $len) . $text;
    }
  }
}
