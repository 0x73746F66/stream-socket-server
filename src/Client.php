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
    public $data;
    /**
     * @var string
     */
    public $id;
    /**
     * @var ClientStreamSocket
     */
    protected $clientStreamSocket;

    /**
     * @param ClientStreamSocket $clientStreamSocket
     * @return Client
     */
    final public function attachClientStreamSocket(ClientStreamSocket &$clientStreamSocket): Client
    {
        $this->clientStreamSocket = &$clientStreamSocket;
        $this->id                 = $clientStreamSocket->getJobId();

        return $this;
    }

    /**
     * @param array|object|string $response
     * @param bool                $includeMeta
     * @return bool
     */
    final public function __invoke($response, bool $includeMeta = true): bool
    {
        if (is_string($response) && self::isJson($response)) {
            return $this->sendJSON(json_decode($response, true));
        } elseif (is_string($response)) {
            return $this->sendText($response);
        } elseif (is_array($response)) {
            return $this->sendJSON($response);
        }

        return false;
    }

    /**
     * @param mixed $string
     * @return bool
     */
    public static function isJson($string): bool
    {
        return !empty($string) && is_string($string) && is_array(json_decode($string, true)) && json_last_error() == 0;
    }

    /**
     * @param string $response
     * @param bool   $includeMeta
     * @return bool
     */
    final public function sendText(string $response, bool $includeMeta = true): bool
    {
        $data = $includeMeta ? [
            '@meta'   => [
                '_key' => $this->getId(),
            ],
            'message' => $response,
        ]: $response;
        if ($this->isJson($this->getDataRaw()) && array_key_exists('@meta', $this->getData())) {
            $meta = $this->getData();
            if (array_key_exists('_id', $meta['@meta'])) {
                $data['@meta']['_id'] = $meta['@meta']['_id'];
            }
            if (array_key_exists('_getrusage', $meta['@meta'])) {
                $ru  = getrusage();
                $rus = $meta['@meta']['_getrusage'];
                unset($meta['@meta']['_getrusage']);
                $data['@meta']['_timings'] = [
                    'process' => ($ru["ru_utime.tv_sec"] * 1000 + intval($ru["ru_utime.tv_usec"] / 1000))
                                 - ($rus["ru_utime.tv_sec"] * 1000 + intval($rus["ru_utime.tv_usec"] / 1000)),
                    'system'  => ($ru["ru_stime.tv_sec"] * 1000 + intval($ru["ru_stime.tv_usec"] / 1000))
                                 - ($rus["ru_stime.tv_sec"] * 1000 + intval($rus["ru_stime.tv_usec"] / 1000)),
                ];
            }
        }
        if (@stream_socket_sendto(
                $this->clientStreamSocket->getHandle(),
                ClientStreamSocket::_encode(
                    $includeMeta ? json_encode(
                        $data,
                        JSON_ERROR_INF_OR_NAN |
                        JSON_NUMERIC_CHECK |
                        JSON_PRESERVE_ZERO_FRACTION
                    ): $data
                )
            ) === -1
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param array $response
     * @param bool  $includeMeta
     * @return bool
     */
    final public function sendJSON(array $response, bool $includeMeta = true): bool
    {
        $data = $response;
        if ($includeMeta) {
            $meta = $response['@meta'] ?? [];
            $data = [
                '@meta'   => array_merge($meta, [
                    '_key' => $this->getId(),
                ]),
                'message' => $response,
            ];
            unset($response['@meta']);
        }
        if ($this->isJson($this->getDataRaw()) && array_key_exists('@meta', $this->getData())) {
            $meta = $this->getData();
            if (array_key_exists('_id', $meta['@meta'])) {
                $data['@meta']['_id'] = $meta['@meta']['_id'];
            }
            if (array_key_exists('_getrusage', $meta['@meta'])) {
                $ru  = getrusage();
                $rus = $meta['@meta']['_getrusage'];
                unset($meta['@meta']['_getrusage']);
                $data['@meta']['_timings'] = [
                    'process' => ($ru["ru_utime.tv_sec"] * 1000 + intval($ru["ru_utime.tv_usec"] / 1000))
                                 - ($rus["ru_utime.tv_sec"] * 1000 + intval($rus["ru_utime.tv_usec"] / 1000)),
                    'system'  => ($ru["ru_stime.tv_sec"] * 1000 + intval($ru["ru_stime.tv_usec"] / 1000))
                                 - ($rus["ru_stime.tv_sec"] * 1000 + intval($rus["ru_stime.tv_usec"] / 1000)),
                ];
            }
        }
        if (@stream_socket_sendto(
                $this->clientStreamSocket->getHandle(),
                ClientStreamSocket::_encode(
                    json_encode(
                        $data,
                        JSON_ERROR_INF_OR_NAN |
                        JSON_NUMERIC_CHECK |
                        JSON_PRESERVE_ZERO_FRACTION |
                        JSON_OBJECT_AS_ARRAY
                    )
                )
            ) === -1
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
     * @return array|object|string
     */
    public function getData(): array
    {
        return json_decode($this->getDataRaw(), true) ?? [];
    }

    /**
     * @return string
     */
    public function getDataRaw(): string
    {
        return $this->data ?? '';
    }

    /**
     * @param string $data
     * @return Client
     */
    public function setData(string $data): Client
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @param array $status
     * @return Client
     */
    public function setStatus(array $status): Client
    {
        $this->status = $status;

        return $this;
    }
}
