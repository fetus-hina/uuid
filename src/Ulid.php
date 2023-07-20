<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid;

use ParagonIE\ConstantTime\Base32;
use Stringable;
use jp3cki\uuid\internal\CrockfordBase32;
use jp3cki\uuid\internal\Random;
use jp3cki\uuid\internal\Timestamp;

use function assert;
use function implode;
use function str_repeat;
use function strtolower;
use function substr;

final class Ulid implements Stringable
{
    private int $timestamp = -1;
    private string $random = '';

    public function __construct()
    {
        $this->timestamp = Timestamp::currentUlidTime();
        $this->random = Random::bytes(10); // 80 bits
    }

    public function __toString(): string
    {
        return $this->formatAsString();
    }

    public function formatAsString(): string
    {
        assert($this->timestamp >= 0);

        return strtolower(
            implode('', [
                substr(
                    str_repeat('0', 10) . CrockfordBase32::encodeIntegerLower($this->timestamp),
                    -10,
                ),
                CrockfordBase32::fromStandardBase32(
                    Base32::encodeUnpadded(
                        $this->random,
                    ),
                ),
            ]),
        );
    }
}
