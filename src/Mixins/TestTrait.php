<?php

namespace AdrienGras\PhpClamAV\Mixins;

trait TestTrait
{
    public function testPing(): void
    {
        $this->assertTrue(self::$clamAV->ping());
    }

    public function testVersion(): void
    {
        $this->assertTrue(str_starts_with(self::$clamAV->version(), 'ClamAV'));
    }

    public function testReload(): void
    {
        $this->assertTrue(str_starts_with(self::$clamAV->reload(), 'RELOADING'));
    }

    public function testShutdown(): void
    {
        $this->assertTrue(str_starts_with(self::$clamAV->shutdown(), ''));
    }
}