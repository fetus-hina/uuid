<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\internal;

use Throwable;
use jp3cki\uuid\Exception;

use function file_exists;
use function file_get_contents;
use function function_exists;
use function is_readable;
use function is_string;
use function openssl_random_pseudo_bytes;
use function random_bytes;
use function strlen;

final class Random
{
    /**
     * @param int<1, max> $length
     */
    public static function bytes(int $length): string
    {
        return self::byPHP7Random($length)
            ?? self::byUnixRandom($length)
            ?? self::byOpenSSLRandom($length)
            ?? throw new Exception('No random source');
    }

    /**
     * @param int<1, max> $length
     */
    public static function byPHP7Random(int $length): ?string
    {
        if (function_exists('random_bytes')) {
            try {
                $tmp = random_bytes($length);
                if (strlen($tmp) === $length) {
                    return $tmp;
                }
            } catch (Throwable $e) {
            }
        }

        return null; // @codeCoverageIgnore
    }

    /**
     * @param int<1, max> $length
     */
    public static function byUnixRandom(int $length): ?string
    {
        if (@file_exists('/dev/urandom') && @is_readable('/dev/urandom')) {
            $tmp = @file_get_contents('/dev/urandom', false, null, 0, $length);
            if (is_string($tmp) && strlen($tmp) === $length) {
                return $tmp;
            }
        }

        return null; // @codeCoverageIgnore
    }

    /**
     * @param int<1, max> $length
     */
    public static function byOpenSSLRandom(int $length): ?string
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $strong = null;
            $r = @openssl_random_pseudo_bytes($length, $strong);
            if (is_string($r) && strlen($r) === $length && $strong) {
                return $r;
            }
        }

        return null; // @codeCoverageIgnore
    }
}
