<?php

namespace AdrienGras\PhpClamAV\Connector;

use AdrienGras\PhpClamAV\Connector\Impl\AbstractConnector;
use InvalidArgumentException;
use RuntimeException;

class TcpConnector extends AbstractConnector
{
    private string $host;
    private string $port;

    public function __construct(string|array $dsnParts)
    {
        $dsn = $dsnParts;

        if (true === is_array($dsnParts)) {
            $host = $dsnParts[0] ?? "localhost";
            $port = $dsnParts[1] ?? 3310;

            $dsn = sprintf("tcp://%s:%s", $host, $port);
        }

        if (false === filter_var($dsn, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(sprintf("Invalid DSN provided: %s", $dsn));
        }

        $this->dsn = $dsn;

        $this->getSocket();
    }

    public function getSocket(): \Socket
    {
        $uriParts = parse_url($this->dsn);

        if ("tcp" !== $uriParts["scheme"]) {
            throw new InvalidArgumentException(sprintf("Invalid scheme provided: expected tcp, found %s", $uriParts["scheme"]));
        }

        $this->host = $uriParts["host"];
        $this->port = $uriParts["port"];

        $socket = @socket_create(AF_INET, SOCK_STREAM, 0);
        $status = @socket_connect($socket, $this->host, $this->port);

        if (false === $status) {
            throw new RuntimeException(sprintf('Unable to connect to ClamAV server at %s', $this->dsn));
        }

        return $socket;
    }
}