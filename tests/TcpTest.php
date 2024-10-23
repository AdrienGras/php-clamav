<?php


use AdrienGras\PhpClamAV\ClamAV;
use AdrienGras\PhpClamAV\Mixins\TestTrait;
use PHPUnit\Framework\TestCase;

class TcpTest extends TestCase
{
    use TestTrait;

    protected static ClamAV $clamAV;

    public static function setUpBeforeClass(): void
    {
        self::$clamAV = ClamAV::fromDSN('tcp://localhost:3310');
    }
}
