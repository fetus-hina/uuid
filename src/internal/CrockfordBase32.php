<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\internal;

use LogicException;
use RangeException;

use function array_reverse;
use function floor;
use function implode;
use function rtrim;
use function strlen;
use function strtolower;
use function strtoupper;
use function substr;

final class CrockfordBase32
{
    private const CHARACTERS_FOR_ENCODING = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

    /**
     * @param non-negative-int $value
     */
    public static function encodeIntegerLower(int $value): string
    {
        return strtolower(self::encodeIntegerUpper($value));
    }

    /**
     * @param non-negative-int $value
     */
    public static function encodeIntegerUpper(int $value): string
    {
        // @phpstan-ignore-next-line
        if ($value < 0) {
            throw new RangeException('CrockfordBase32::encodeInteger*() requires a non-negative-int value');
        }

        if ($value === 0) {
            return '0';
        }

        /**
         * @var string[] $digits REVERSED digits of base32 number
         */
        $digits = [];

        $charCount = strlen(self::CHARACTERS_FOR_ENCODING);
        while ($value > 0) {
            $digits[] = substr(self::CHARACTERS_FOR_ENCODING, $value % $charCount, 1);
            $value = (int)floor($value / $charCount);
        }

        return implode('', array_reverse($digits));
    }

    public static function fromStandardBase32(string $base32): string
    {
        $base32 = rtrim($base32, '=');
        $length = strlen($base32);
        for ($i = 0; $i < $length; ++$i) {
            $base32[$i] = match (strtoupper(substr($base32, $i, 1))) {
                'A' => '0',
                'B' => '1',
                'C' => '2',
                'D' => '3',
                'E' => '4',
                'F' => '5',
                'G' => '6',
                'H' => '7',
                'I' => '8',
                'J' => '9',
                'K' => 'A',
                'L' => 'B',
                'M' => 'C',
                'N' => 'D',
                'O' => 'E',
                'P' => 'F',
                'Q' => 'G',
                'R' => 'H',
                'S' => 'J',
                'T' => 'K',
                'U' => 'M',
                'V' => 'N',
                'W' => 'P',
                'X' => 'Q',
                'Y' => 'R',
                'Z' => 'S',
                '2' => 'T',
                '3' => 'V',
                '4' => 'W',
                '5' => 'X',
                '6' => 'Y',
                '7' => 'Z',
                default => throw new LogicException('Unexpected character in base32 string'),
            };
        }
        return $base32;
    }
}
