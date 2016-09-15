<?php
declare(strict_types = 1);
namespace sockets;

/**
 * Class StreamSocketDaemon
 * @package sockets/php-stream-socket-server
 */
class StreamSocketDaemon
{
    /**
     * @var StreamSocketServer
     */
    protected $server;
    /**
     * @var int
     */
    protected $jobsStarted = 0;
    /**
     * @var array
     */
    protected $currentJobs = [];
    /**
     * @var array
     */
    protected $signalQueue = [];
    /**
     * @var int
     */
    protected $parentPID;

    /**
     * StreamSocketDaemon constructor.
     * @param array $serverConfig $config for StreamSocketServer
     */
    public function __construct(array $serverConfig = [])
    {
        $this->parentPID = getmypid();
        pcntl_signal(SIGCHLD, [$this, "childSignalHandler"]);
        $this->server = new StreamSocketServer($serverConfig);
    }

    /**
     * @param \Closure $callback
     * @return StreamSocketDaemon
     */
    public function startStreamSocketServer(\Closure $callback): StreamSocketDaemon
    {
        $this->server->start()->registerCallback($callback)->listen();
        return $this;
    }

    /**
     * @param $signo
     * @param $pid
     * @param $status
     * @return bool
     */
    public function childSignalHandler($signo, $pid = null, $status = null): bool
    {
        if (!$pid) {
            $pid = pcntl_waitpid(-1, $status, WNOHANG);
        }
        //Make sure we get all of the exited children
        while ($pid > 0) {
            if ($pid && isset($this->currentJobs[$pid])) {
                $exitCode = pcntl_wexitstatus($status);
                if ($exitCode != 0) {
                    if ($this->server->getDebug()) {
                        echo "[INFO][" . __CLASS__ . "::" . __FUNCTION__ . "] $pid exited with status $exitCode\n";
                    }
                }
                unset($this->currentJobs[$pid]);
            } elseif ($pid) {
                if ($this->server->getDebug()) {
                    echo "[INFO][" . __CLASS__ . "::" . __FUNCTION__ . "] Adding $pid to the signal queue\n";
                }
                $this->signalQueue[$pid] = $status;
            }
            $pid = pcntl_waitpid(-1, $status, WNOHANG);
        }

        return true;
    }

    public function __destruct()
    {
        if ($this->server->getDebug()) {
            echo "[INFO][" . __CLASS__ . "::" . __FUNCTION__ . "] Thread closing\n";
        }
    }
}
