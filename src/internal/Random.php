<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\internal;

use Random\Engine\Secure as SecureRandomEngine;
use Random\Randomizer;
use Throwable;
use jp3cki\uuid\Exception;

final class Random
{
    /**
     * @param int<1, max> $length
     */
    public static function bytes(int $length): string
    {
        $generator = new Randomizer(new SecureRandomEngine());
        try {
            return $generator->getBytes($length);
        } catch (Throwable $e) {
            throw new Exception('No random source', 0, $e);
        }
    }

    /**
     * @deprecated
     * @param int<1, max> $length
     */
    public static function byPHP7Random(int $length): ?string
    {
        return self::bytes($length);
    }

    /**
     * @deprecated
     * @param int<1, max> $length
     */
    public static function byUnixRandom(int $length): ?string
    {
        return self::bytes($length);
    }

    /**
     * @deprecated
     * @param int<1, max> $length
     */
    public static function byOpenSSLRandom(int $length): ?string
    {
        return self::bytes($length);
    }
}
