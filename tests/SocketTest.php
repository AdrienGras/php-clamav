<?php


use AdrienGras\PhpClamAV\ClamAV;
use AdrienGras\PhpClamAV\Mixins\TestTrait;
use PHPUnit\Framework\TestCase;

class SocketTest extends TestCase
{
    use TestTrait;

    protected static ClamAV $clamAV;

    public static function setUpBeforeClass(): void
    {
        $socketPath = __DIR__ . "/../clamav/socket/clamd.sock";
        $socketRealPath = realpath($socketPath);
        self::$clamAV = ClamAV::fromDSN('unix:/' . $socketRealPath);
    }

    public function testShutdown(): void
    {
        $this->markTestSkipped('Duplicate test, server can be shutdown only once and will be shut down with TCP test');
    }
}
