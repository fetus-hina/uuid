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
use jp3cki\uuid\internal\CrockfordBase32;

use function strtolower;
use function strtoupper;

final class CrockfordBase32Test extends TestCase
{
    /**
     * @param int<0, max> $num
     */
    #[DataProvider('integers')]
    public function testEncodeIntegerLower(int $num, string $expected): void
    {
        $encoded = CrockfordBase32::encodeIntegerLower($num);
        $this->assertEquals(strtolower($expected), $encoded);
    }

    /**
     * @param int<0, max> $num
     */
    #[DataProvider('integers')]
    public function testEncodeIntegerUpper(int $num, string $expected): void
    {
        $encoded = CrockfordBase32::encodeIntegerUpper($num);
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
