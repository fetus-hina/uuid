<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\test\internal;

use PHPUnit\Framework\TestCase;
use jp3cki\uuid\internal\CrockfordBase32;

use function is_string;
use function strtolower;
use function strtoupper;

final class CrockfordBase32Test extends TestCase
{
    /**
     * @dataProvider integers
     * @param int<0, max> $num
     */
    public function testEncodeIntegerLower(int $num, string $expected): void
    {
        $encoded = CrockfordBase32::encodeIntegerLower($num);
        $this->assertTrue(is_string($encoded));
        $this->assertEquals(strtolower($expected), $encoded);
    }

    /**
     * @dataProvider integers
     * @param int<0, max> $num
     */
    public function testEncodeIntegerUpper(int $num, string $expected): void
    {
        $encoded = CrockfordBase32::encodeIntegerUpper($num);
        $this->assertTrue(is_string($encoded));
        $this->assertEquals(strtoupper($expected), $encoded);
    }

    public function testFromStandardBase32(): void
    {
        $this->assertEquals(
            '0123456789ABCDEFGHJKMNPQRSTVWXYZ',
            CrockfordBase32::fromStandardBase32('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567===='),
        );
    }

    /**
     * @return array{int<0, max>, string}[]
     */
    public static function integers(): array
    {
        return [
            [0, '0'],
            [1, '1'],
            [32, '10'],
            [1234, '16J'],
        ];
    }
}
