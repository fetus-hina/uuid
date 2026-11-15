<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use jp3cki\uuid\Exception;
use jp3cki\uuid\Mac;

use function hex2bin;
use function strlen;

final class MacTest extends TestCase
{
    #[DataProvider('fromString')]
    public function testFromString(string $value, bool $success, string $expect): void
    {
        if ($success) {
            $instance = Mac::fromString($value);
            $this->assertEquals($expect, $instance->formatEUI());
        } else {
            $this->expectException(Exception::class);
            Mac::fromString($value);
        }
    }

    /**
     * @return array<int, array{string, bool, string}>
     */
    public static function fromString(): array
    {
        return [
            ['', false, ''],
            [' ', false, ''],
            [(string)hex2bin('08002b010203'), true, '08:00:2b:01:02:03'],
            // raw binary whose edge octets are characters trim() would strip
            [(string)hex2bin('001b213c4d5e'), true, '00:1b:21:3c:4d:5e'],
            [(string)hex2bin('08002b010220'), true, '08:00:2b:01:02:20'],
            [(string)hex2bin('0a002b01020d'), true, '0a:00:2b:01:02:0d'],
            [(string)hex2bin('090000000b00'), true, '09:00:00:00:0b:00'],
            // whitespace-padded but not 6 octets long must not be taken as raw binary
            [' ' . (string)hex2bin('08002b010203') . ' ', false, ''],
            ['08:00:2b:01:02:03', true, '08:00:2b:01:02:03'],
            ['08-00-2b-01-02-03', true, '08:00:2b:01:02:03'],
            ['08002b:010203', true, '08:00:2b:01:02:03'],
            ['08002b-010203', true, '08:00:2b:01:02:03'],
            ['0800.2b01.0203', true, '08:00:2b:01:02:03'],
            ['0800-2b01-0203', true, '08:00:2b:01:02:03'],
            ['08002b010203', true, '08:00:2b:01:02:03'],
            [' 08:00:2b:01:02:03 ', true, '08:00:2b:01:02:03'],
            ['08:00-2b:01-02:03', false, ''],
        ];
    }

    public function testDefaultConstruct(): void
    {
        $instance = new Mac();
        $binary = $instance->getBinary();
        $this->assertEquals(6, strlen($binary));
        // RFC 9562 §6.10: a random node ID must have the multicast bit set
        $this->assertFalse($instance->isUnicast());
        $this->assertTrue($instance->isMulticast());
        $this->assertTrue($instance->isLocal());
        $this->assertFalse($instance->isUniversal());

        // デフォルト構築ではランダム値になるので2回生成すると必ず異なるはず
        $this->assertFalse((new Mac())->getBinary() === $binary);
    }

    public function testCopyConstruct(): void
    {
        $instance1 = new Mac();
        $instance2 = new Mac($instance1);
        $this->assertEquals((string)$instance1, (string)$instance2);
    }

    public function testConstructFromString(): void
    {
        $instance = new Mac('08:00:2b:01:02:03');
        $this->assertEquals('08:00:2b:01:02:03', $instance->formatEUI());
    }

    public function testConstructFromEmptyStringIsRandom(): void
    {
        $instance = new Mac('');
        $this->assertEquals(6, strlen($instance->getBinary()));
        $this->assertTrue($instance->isMulticast());
        $this->assertTrue($instance->isLocal());
    }

    public function testConstructFromBlankStringThrows(): void
    {
        $this->expectException(Exception::class);
        new Mac(' ');
    }

    public function testConstructFromInvalidStringThrows(): void
    {
        $this->expectException(Exception::class);
        new Mac('08:00-2b:01-02:03');
    }

    public function testStringify(): void
    {
        $instance = new Mac('08:00:2b:01:02:03');
        $this->assertEquals('08:00:2b:01:02:03', $instance->formatEUI());
        $this->assertEquals('08-00-2b-01-02-03', $instance->formatHyphen());
        $this->assertEquals('08002b010203', $instance->format());
        $this->assertEquals('08:00:2b:01:02:03', (string)$instance);
    }
}
