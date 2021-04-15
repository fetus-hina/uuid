<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\internal;

use Throwable;
use jp3cki\uuid\Exception;

final class Random
{
    public static function bytes(int $length): string
    {
        $length = (int)$length;
        $methods = [
            function (int $length) {
                return static::byPHP7Random($length);
            },
            function (int $length) {
                return static::byUnixRandom($length); // @codeCoverageIgnore
            },
            function (int $length) {
                return static::byOpenSSLRandom($length); // @codeCoverageIgnore
            },
        ];

        foreach ($methods as $method) {
            try {
                $r = call_user_func($method, $length);
                if (is_string($r) && strlen($r) === $length) {
                    return $r;
                }
            } catch (Throwable $e) { // @codeCoverageIgnore
            }
        }

        throw new Exception('No random source'); // @codeCoverageIgnore
    }

    /** @return ?string */
    public static function byPHP7Random(int $length)
    {
        if (function_exists('random_bytes')) {
            return random_bytes($length);
        }

        return null; // @codeCoverageIgnore
    }

    /** @return ?string */
    public static function byUnixRandom(int $length)
    {
        if (file_exists('/dev/urandom') && is_readable('/dev/urandom')) {
            $tmp = file_get_contents('/dev/urandom', false, null, 0, $length);
            if (is_string($tmp) && strlen($tmp) === $length) {
                return $tmp;
            }
        }

        return null; // @codeCoverageIgnore
    }

    /** @return ?string */
    public static function byOpenSSLRandom(int $length)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $strong = null;
            $r = openssl_random_pseudo_bytes($length, $strong);
            if (is_string($r) && strlen($r) === $length && $strong) {
                return $r;
            }
        }

        return null; // @codeCoverageIgnore
    }
}
