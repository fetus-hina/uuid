<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\internal;

use Random\Engine\Secure as SecureRandomEngine;
use Random\Randomizer;
use Throwable;
use ValueError;
use jp3cki\uuid\Exception;

final class Random
{
    /**
     * Returns $length random octets.
     *
     * Only a failure of the random source is converted to Exception; an invalid
     * length ($length < 1) is a programming error and propagates as ValueError.
     *
     * @param int<1, max> $length
     */
    public static function bytes(int $length): string
    {
        $generator = new Randomizer(new SecureRandomEngine());
        try {
            return $generator->getBytes($length);
        } catch (ValueError $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new Exception('No random source', 0, $e);
        }
    }

    /**
     * Returns a uniformly distributed integer in the closed range [$min, $max].
     *
     * Only a failure of the random source is converted to Exception; an invalid
     * range ($min > $max) is a programming error and propagates as ValueError.
     */
    public static function int(int $min, int $max): int
    {
        $generator = new Randomizer(new SecureRandomEngine());
        try {
            return $generator->getInt($min, $max);
        } catch (ValueError $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new Exception('No random source', 0, $e);
        }
    }
}
