<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\internal;

use function explode;
use function floor;
use function function_exists;
use function microtime;
use function time;

final class Timestamp
{
    public static function currentV1Timestamp(): int
    {
        return self::v1Microtime() ?? self::v1Time();
    }

    public static function v1Microtime(): ?int
    {
        if (!function_exists('microtime')) {
            return null; // @codeCoverageIgnore
        }

        [$usec, $sec] = explode(' ', (string)microtime(false), 2);
        $ts = (int)$sec * 1000 * 1000 * 10; // 100ns tick
        $ts = $ts + (int)floor((float)$usec * 1000 * 1000 * 10);
        return $ts;
    }

    public static function v1Time(): int
    {
        $ts = time() * 1000 * 1000 * 10; // 100ns tick

        // fill sub-sec fields by random value
        return $ts + Random::int(0, 1000 * 1000 * 10 - 1);
    }

    public static function currentUlidTime(): int
    {
        return self::ulidMicrotime() ?? self::ulidTime();
    }

    public static function ulidMicrotime(): ?int
    {
        if (!function_exists('microtime')) {
            return null; // @codeCoverageIgnore
        }

        return (int)floor(microtime(true) * 1000);
    }

    public static function ulidTime(): int
    {
        // fill sub-sec fields by random value
        return time() * 1000 + Random::int(0, 999);
    }
}
