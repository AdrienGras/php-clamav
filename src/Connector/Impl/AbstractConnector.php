<?php

namespace AdrienGras\PhpClamAV\Connector\Impl;

abstract class AbstractConnector implements ConnectorInterface
{
    protected \Socket $socket;

    protected string $dsn;

    protected const MAX_RECV_BUFFER_SIZE = 20000;

    public function sendRecv(string $command): string
    {
        $socket = $this->getSocket();

        socket_send($socket, $command, strlen($command), 0);
        socket_recv($socket, $return, self::MAX_RECV_BUFFER_SIZE, 0);
        socket_close($socket);

        return trim($return ?? '');
    }
}