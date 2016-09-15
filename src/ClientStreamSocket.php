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
     * @var bool
     */
    public $isWebSocket;
    /**
     * @var string
     */
    private $peer;
    /**
     * @var string
     */
    private $_SecWebSocketKey;
    /**
     * @var array
     */
    private $_status = [];
    /**
     * @var resource
     */
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
     * @var string
     */
    protected $data;
    /**
     * @var Client
     */
    protected $client;

    /**
     * ClientStreamSocket constructor.
     * @param Client $client
     */
    final public function __construct(Client $client)
    {
        $this->jobId = uniqid();
        $this->client = $client;
    }

    /**
     * @param resource $handle
     * @return ClientStreamSocket
     * @internal param $response
     */
    final public function attachClientHandle(&$handle): ClientStreamSocket
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
        $this->handle = &$handle;
        $this->setHeaders();
        $this->setPeer();
        $this->getClient()->attachClientStreamSocket($this);
        $this->setStatus();

        return $this;
    }

    /**
     * @param $response
     * @return bool
     */
    final public function __invoke($response): bool
    {
        if ($this->isConnected()) {
            if (is_string($response)) {
                return $this->getClient()->sendText($response);
            } elseif (is_array($response)) {
                return $this->getClient()->sendJSON($response);
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    final public function validateWebSocket(): bool
    {
        if (is_string($this->getHeaders()) && preg_match('#^Sec-WebSocket-Key: (\S+)#mi', $this->getHeaders(), $match)
        ) {

            $this->_SecWebSocketKey = $match[1];
            $this->isWebSocket = true;

            return true;
        }

        return false;
    }

    /**
     * @return ClientStreamSocket
     */
    final public function upgradeWebSocket(): ClientStreamSocket
    {
        if (is_resource($this->getHandle())) {
            fwrite(
                $this->getHandle(),
                "HTTP/1.1 101 Switching Protocols\r\n"
                . "Upgrade: websocket\r\n"
                . "Connection: Upgrade\r\n"
                . "Sec-WebSocket-Accept: " . base64_encode(sha1($this->_SecWebSocketKey . self::MAGIC, true))
                . "\r\n\r\n"
            );
        }
        return $this;
    }

    /**
     * @param string $data
     * @return ClientStreamSocket
     */
    final public function setData(string $data): ClientStreamSocket
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    final public function getDataRaw()
    {
        return $this->data??'';
    }

    /**
     * @return array|object|string
     */
    final public function getData()
    {
        return json_decode($this->getDataRaw());
    }

    /**
     * @return Client
     */
    final public function getClient(): Client
    {
        if (!($this->client instanceof Client)) {
            throw new \LogicException('Called ' . __CLASS__ . '::' . __FUNCTION__ . ' before assigning a Client');
        }

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
     * @param string $headersRaw
     * @return ClientStreamSocket
     */
    final public function setHeaders(string $headersRaw = null): ClientStreamSocket
    {
        if ($this->isConnected()) {
            $this->headers = $headersRaw ?? stream_get_line($this->getHandle(), 65535, "\r\n\r\n");
        } else {
            $this->headers = $headersRaw;
        }

        return $this;
    }

    /**
     * @return string
     */
    final public function getHeaders(): string
    {
        return $this->headers??'';
    }

    /**
     * @return int
     */
    final public function getLastDataLength(): int
    {
        return $this->lastDataLength??0;
    }

    /**
     * @return bool
     */
    final public function pendingMessage(): bool
    {
        if (!$this->isConnected()) {
            return false;
        }
        $this->setStatus();
        if ($this->isWebSocket) {
            $stream = stream_socket_recvfrom($this->getHandle(), self::MAX_INCOMING_MSG_LENGTH);
            $data = static::_decode($stream);
            if (strlen($data) !== $this->lastDataLength || $this->getDataRaw() != $data) {
                $this->setData($data);

                return true;
            }
        } else {
            $data = $this->getHeaders();
            if (@stream_socket_sendto($this->getHandle(), $this->getPeer() . " ☚ (<‿<)☚\r\n") === -1) {
                $this->disconnect();
                exit(0);
            }
            $this->data = $data;

            $this->setData($data);
        }

        return false;
    }

    /**
     * @return resource|bool
     */
    final public function getHandle()
    {
        return is_resource($this->handle) ? $this->handle : false;
    }

    /**
     * @param string $peer
     * @return ClientStreamSocket
     */
    public function setPeer(string $peer = null): ClientStreamSocket
    {
        if ($this->isConnected()) {
            $this->peer = $peer ?? stream_socket_get_name($this->getHandle(), true);
        } else {
            $this->peer = $peer;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPeer(): string
    {
        return $this->peer;
    }

    /**
     * @param array $status
     * @return ClientStreamSocket
     */
    public function setStatus(array $status = null): ClientStreamSocket
    {
        if ($this->isConnected()) {
            $this->_status = stream_get_meta_data($this->getHandle());
            $this->getClient()->setStatus($this->_status);
        } else {
            $this->_status = $status;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getStatus(): array
    {
        return $this->_status;
    }

    /**
     * @return bool
     */
    final public function isClosed(): bool
    {
        return !is_resource($this->getHandle());
    }

    /**
     * @return bool
     */
    final public function isConnected(): bool
    {
        if ($this->isClosed() || !($this->getClient() instanceof Client)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    final public function disconnect(): bool
    {
        unset($this->client);
        if ($this->isConnected()) {
            return fclose($this->getHandle());
        }

        return false;
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
