<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\internal;

use function call_user_func;
use function explode;
use function floor;
use function function_exists;
use function is_int;
use function microtime;
use function ord;
use function time;

final class Timestamp
{
    public static function currentV1Timestamp(): int
    {
        $methods = [
            fn () => static::v1Microtime(), // @codeCoverageIgnore
        ];

        foreach ($methods as $method) {
            $r = call_user_func($method);
            if (is_int($r)) {
                return $r;
            }
        }

        return static::v1Time(); // @codeCoverageIgnore
    }

    /** @return ?int */
    public static function v1Microtime()
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
        while (true) {
            $rBin = Random::bytes(3);
            $r = (ord($rBin[0]) << 16) | (ord($rBin[1]) << 8) | ord($rBin[2]);
            if ($r < 1000 * 1000 * 10) {
                break;
            }
        }
        return $ts + $r;
    }
}
