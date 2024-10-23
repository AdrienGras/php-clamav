<?php

namespace AdrienGras\PhpClamAV;

use AdrienGras\PhpClamAV\Connector\Impl\ConnectorInterface;
use AdrienGras\PhpClamAV\Connector\SocketConnector;
use AdrienGras\PhpClamAV\Connector\TcpConnector;
use InvalidArgumentException;

class ClamAV
{
    private ConnectorInterface $connector;

    private function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    public static function fromDSN(string $dsnParts): self
    {
        $scheme = parse_url($dsnParts, PHP_URL_SCHEME);

        return match($scheme) {
            "unix" => new self(new SocketConnector($dsnParts)),
            "tcp" => new self(new TcpConnector($dsnParts)),
            default => throw new InvalidArgumentException(sprintf('Unsupported scheme "%s"', $scheme)),
        };
    }

    public static function fromParts(string $host, string $port): self
    {
        return new self(new TcpConnector([$host, $port]));
    }

    public function ping(): bool
    {
        return "PONG" === $this->connector->sendSimpleCommand("PING");
    }

    public function version(): string
    {
        return $this->connector->sendSimpleCommand('VERSION');
    }

    public function reload(): string
    {
        return $this->connector->sendSimpleCommand('RELOAD');
    }

    public function shutdown(): string
    {
        return $this->connector->sendSimpleCommand('SHUTDOWN');
    }
}