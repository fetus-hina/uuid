<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\test;

use PHPUnit\Framework\TestCase;
use jp3cki\uuid\Exception;
use jp3cki\uuid\Mac;

use function hex2bin;
use function is_string;
use function strlen;

final class MacTest extends TestCase
{
    /**
     * @dataProvider fromString
     * @return void
     */
    public function testFromString(string $value, bool $success, string $expect)
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
    public function fromString(): array
    {
        return [
            ['', false, ''],
            [' ', false, ''],
            [(string)hex2bin('08002b010203'), true, '08:00:2b:01:02:03'],
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

    /** @return void */
    public function testDefaultConstruct()
    {
        $instance = new Mac();
        $binary = $instance->getBinary();
        $this->assertTrue(is_string($binary));
        $this->assertEquals(6, strlen($binary));
        $this->assertTrue($instance->isUnicast());
        $this->assertFalse($instance->isMulticast());
        $this->assertTrue($instance->isLocal());
        $this->assertFalse($instance->isUniversal());

        // デフォルト構築ではランダム値になるので2回生成すると必ず異なるはず
        $this->assertFalse((new Mac())->getBinary() === $binary);
    }

    /** @return void */
    public function testCopyConstruct()
    {
        $instance1 = new Mac();
        $instance2 = new Mac($instance1);
        $this->assertEquals((string)$instance1, (string)$instance2);
    }

    /** @return void */
    public function testConstructFromString()
    {
        $instance = new Mac('08:00:2b:01:02:03');
        $this->assertEquals('08:00:2b:01:02:03', $instance->formatEUI());
    }

    /** @return void */
    public function testStringify()
    {
        $instance = new Mac('08:00:2b:01:02:03');
        $this->assertEquals('08:00:2b:01:02:03', $instance->formatEUI());
        $this->assertEquals('08-00-2b-01-02-03', $instance->formatHyphen());
        $this->assertEquals('08002b010203', $instance->format());
        $this->assertEquals('08:00:2b:01:02:03', (string)$instance);
    }
}
