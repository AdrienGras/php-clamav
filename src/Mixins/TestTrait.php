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

    public function testScanFile(): void
    {
        $this->assertTrue(self::$clamAV->scan("/var/www/clean.txt"), true);
    }

    public function testScanInfectedFile(): void
    {
        $this->assertTrue(self::$clamAV->scan("/var/www/clean.txt"), false);
    }

    public function testScanFolder(): void
    {
        $result = self::$clamAV->scan("/var/www");
        $this->assertEquals($result, false);
    }

    public function testContinueFile(): void
    {
        $result = self::$clamAV->continueScan("/var/www/clean.txt");

        $this->assertEmpty($result);
    }
    public function testContinueFolder(): void
    {
        $result = self::$clamAV->continueScan("/var/www");
        $this->assertNotEmpty($result);
    }

    public function testScanFileInStreamByPath(): void
    {
        $filePath = __DIR__ . "/../../clamav/files/clean.txt";
        $fileRealPath = realpath($filePath);

        $this->assertEquals(self::$clamAV->scanInStream($fileRealPath), true);
    }

    public function testScanInfectedFileInStreamByPath(): void
    {
        $filePath = __DIR__ . "/../../clamav/files/infected.txt";
        $fileRealPath = realpath($filePath);

        $this->assertEquals(self::$clamAV->scanInStream($fileRealPath), false);
    }

    public function testScanFileInStreamByResource(): void
    {
        $filePath = __DIR__ . "/../../clamav/files/clean.txt";
        $fileRealPath = realpath($filePath);

        $handle = fopen($fileRealPath, 'r');

        $this->assertEquals(self::$clamAV->scanInStream($handle), true);
    }

    public function testScanInfectedFileInStreamByResource(): void
    {
        $filePath = __DIR__ . "/../../clamav/files/infected.txt";
        $fileRealPath = realpath($filePath);

        $handle = fopen($fileRealPath, 'r');

        $this->assertEquals(self::$clamAV->scanInStream($handle), false);
    }


    // public function testShutdown(): void
    // {
    //     $this->assertTrue(str_starts_with(self::$clamAV->shutdown(), ''));
    // }

}
