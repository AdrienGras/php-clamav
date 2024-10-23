<?php

namespace AdrienGras\PhpClamAV\Connector;

use AdrienGras\PhpClamAV\Connector\Impl\AbstractConnector;
use InvalidArgumentException;
use RuntimeException;

class SocketConnector extends AbstractConnector
{
    // official docker image for clamav uses /tmp/clamd.sock for the unix socket
    // @see https://docs.clamav.net/manual/Installing/Docker.html#unix-sockets
    public const DEFAULT_CLAMAV_UNIX_SOCKET_DSN = "unix:///tmp/clamd.sock";

    public function __construct(string $dsn = self::DEFAULT_CLAMAV_UNIX_SOCKET_DSN)
    {
        if (false === filter_var($dsn, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(sprintf("Invalid DSN provided: %s", $dsn));
        }

        $parts = parse_url($dsn);
        $scheme = $parts["scheme"];

        if ("unix" !== $scheme) {
            throw new InvalidArgumentException(sprintf("Invalid scheme provided: expected unix, found %s", $scheme));
        }

        $this->dsn = $dsn;

        $this->getSocket();
    }

    public function getSocket(): \Socket
    {
        $path = substr($this->dsn, strlen("unix:/"));

        $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
        $status = socket_connect($socket, $path);

        if (false === $status) {
            throw new RuntimeException(sprintf('Unable to connect to ClamAV server with socket %s', $this->dsn));
        }

        return $socket;
    }

}