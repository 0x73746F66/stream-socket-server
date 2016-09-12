<?php
declare(strict_types = 1);
namespace sockets;

/**
 * Class ClientStreamSocket
 * @package sockets/php-stream-socket-server
 */
final class ClientStreamSocket
{
    const MAGIC                   = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
    const MAX_INCOMING_MSG_LENGTH = 1048576; //1mb
    /**
     * @var string
     */
    public $jobId;
    /**
     * @var string
     */
    private $_SecWebSocketKey;
    /**
     * @var bool
     */
    public $isWebSocket;
    /**
     * @var array
     */
    private   $_status;
    protected $handle;
    /**
     * @var string
     */
    protected $headers;
    /**
     * @var int
     */
    protected $lastDataLength = 0;
    /**
     * @var mixed
     */
    protected $data;
    /**
     * @var Client
     */
    protected $client;

    /**
     * ClientStreamSocket constructor.
     * @param $handle
     */
    final public function __construct(&$handle)
    {
        $type = null;
        try {
            $type = get_resource_type($handle);
        } catch (\Throwable $e) {
            error_log('[ERROR][' . __CLASS__ . '::' . __FUNCTION__ . '] requires a stream resource. exiting');
            exit;
        }
        if ($type !== 'stream') {
            error_log('[ERROR][' . __CLASS__ . '::' . __FUNCTION__ . '] requires a stream resource. exiting');
            exit;
        }
        $this->jobId = uniqid();
        $this->handle = &$handle;
        $this->headers = stream_get_line($this->handle, 65535, "\r\n\r\n");
        $this->peer = stream_socket_get_name($this->handle, true);
        $this->_status = socket_get_status($this->handle);
        $this->client = new Client($this, $this->_status);
    }

    /**
     * @param $response
     * @return bool
     */
    final public function __invoke($response): bool
    {
        if (is_string($response)) {
            return $this->client->sendText($response);
        } elseif (is_array($response)) {
            return $this->client->sendJSON($response);
        }
        return false;
    }

    /**
     * @return bool
     */
    final public function validateWebSocket(): bool
    {
        if (is_string($this->headers) && preg_match('#^Sec-WebSocket-Key: (\S+)#mi', $this->headers, $match)) {
            $this->_SecWebSocketKey = $match[1];
            $this->isWebSocket = true;
            return true;
        }
        return false;
    }

    final public function upgradeWebSocket(): ClientStreamSocket
    {
        fwrite($this->handle, "HTTP/1.1 101 Switching Protocols\r\n"
            . "Upgrade: websocket\r\n"
            . "Connection: Upgrade\r\n"
            . "Sec-WebSocket-Accept: " . base64_encode(sha1($this->_SecWebSocketKey . self::MAGIC, true))
            . "\r\n\r\n");
        return $this;
    }

    /**
     * @return mixed
     */
    final public function getDataRaw()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    final public function getData()
    {
        return json_decode($this->data);
    }

    /**
     * @return Client
     */
    final public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return string
     */
    final public function getJobId(): string
    {
        return $this->jobId;
    }

    /**
     * @return string
     */
    final public function getHeaders(): string
    {
        return $this->headers;
    }

    /**
     * @return int
     */
    final public function getLastDataLength(): int
    {
        return $this->lastDataLength;
    }

    /**
     * @return bool
     */
    final public function pendingMessage(): bool
    {
        $this->_status = socket_get_status($this->handle);
        $this->client->setStatus($this->_status);
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

    /**
     * @return mixed
     */
    final public function getHandle()
    {
        return $this->handle;
    }

    /**
     *
     */
    final public function disconnect()
    {
        fclose($this->handle);
    }

    /**
     * @param string $frame
     * @return string
     */
    final static public function _decode(string $frame): string
    {
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

    /**
     * @param string $text
     * @return string
     */
    final static public function _encode(string $text): string
    {
        $b = 129; // FIN + text frame
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
