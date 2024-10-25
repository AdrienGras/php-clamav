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
        return "PONG" === $this->connector->sendRecv("PING");
    }

    public function version(): string
    {
        return $this->connector->sendRecv('VERSION');
    }

    public function reload(): string
    {
        return $this->connector->sendRecv('RELOAD');
    }

    public function shutdown(): string
    {
        return $this->connector->sendRecv('SHUTDOWN');
    }

    public function scan(string $path): bool
    {
        $result = $this->connector->sendRecv(sprintf("SCAN %s", $path));

        return str_ends_with($result, "OK");
    }

    public function scanInStream(mixed $file): bool
    {
        $fileResourceHandle = null;

        if (true === is_string($file)) {
            $fileResourceHandle = fopen($file, 'r');
        } else {
            if (false === is_resource($file)) {
                throw new InvalidArgumentException('Argument must be a file path or a resource');
            }
            $fileResourceHandle = $file;
        }

        $clamAVHandle = socket_export_stream($this->connector->getSocket());

        $resourceInfo = stream_get_meta_data($fileResourceHandle);
        $filePath = $resourceInfo['uri'];

        $bytes = filesize($filePath);

        fwrite($clamAVHandle, "zINSTREAM\0");
        fwrite($clamAVHandle, pack("N", $bytes));
        stream_copy_to_stream($fileResourceHandle, $clamAVHandle);
        fwrite($clamAVHandle, pack("N", 0));

        $response = trim(fgets($clamAVHandle));
        fclose($clamAVHandle);
        fclose($fileResourceHandle);

        return $response == 'stream: OK';
    }

    public function continueScan(string $path): array
    {
        $return = [];

        $scanResults = $this->connector->sendRecv('CONTSCAN ' . $path);
        $scanLines = explode("\n", trim($scanResults));

        foreach ($scanLines as $results) {
            [$file, $stats] = explode(':', $results);
            $return[] = ['file' => $file, 'stats' => trim($stats)];
        }

        return $return;
    }
}