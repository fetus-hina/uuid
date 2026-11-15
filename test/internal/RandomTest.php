<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\test\internal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ValueError;
use jp3cki\uuid\internal\Random;

use function in_array;
use function strlen;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

final class RandomTest extends TestCase
{
    /**
     * @param int<1, max> $length
     */
    #[DataProvider('lengths')]
    public function testBytes(int $length): void
    {
        $processed = [];
        for ($i = 0; $i < 10; ++$i) {
            $value = Random::bytes($length);
            $this->assertEquals($length, strlen($value));
            if ($length >= 4) {
                $this->assertFalse(in_array($value, $processed, true));
            }
            $processed[] = $value;
        }
    }

    #[DataProvider('ranges')]
    public function testInt(int $min, int $max): void
    {
        for ($i = 0; $i < 100; ++$i) {
            $value = Random::int($min, $max);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }

    public function testIntRejectsInvalidRange(): void
    {
        $this->expectException(ValueError::class);
        Random::int(1, 0);
    }

    /**
     * @return array<string, array{int, int}>
     */
    public static function ranges(): array
    {
        return [
            'single value' => [42, 42],
            'ms' => [0, 999],
            '100ns ticks' => [0, 9_999_999],
            'negative' => [-10, 10],
            'full range' => [PHP_INT_MIN, PHP_INT_MAX],
        ];
    }

    /**
     * @return array<string, int<1, max>[]>
     */
    public static function lengths(): array
    {
        return [
            'len=1' => [1],
            'len=8' => [8],
            'len=16' => [16],
        ];
    }
}
