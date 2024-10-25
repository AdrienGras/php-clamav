<?php

namespace AdrienGras\PhpClamAV\Connector\Impl;

interface ConnectorInterface
{
    public function getSocket(): \Socket;
    public function sendRecv(string $command): string;
}