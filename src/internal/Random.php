<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\internal;

use Exception;
use jp3cki\uuid\Exception as MyException;

class Random
{
    public static function bytes($length)
    {
        $length = (int)$length;
        $methods = [
            'byPHP7Random',
            'byUnixRandom',
            'byOpenSSLRandom',
        ];

        foreach ($methods as $method) {
            try {
                $r = call_user_func([__CLASS__, $method], $length);
                if (is_string($r) && strlen($r) === $length) {
                    return $r;
                }
            } catch (Exception $e) {
            }
        }

        throw new MyException('No random source');
    }

    public static function byPHP7Random($length)
    {
        if (function_exists('random_bytes')) {
            return random_bytes($length);
        }
        return false;
    }

    public static function byUnixRandom($length)
    {
        if (file_exists('/dev/urandom')) {
            return file_get_contents('/dev/urandom', false, null, 0, $length);
        }
        return false;
    }

    public static function byOpenSSLRandom($length)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $strong = null;
            $r = openssl_random_pseudo_bytes($length, $strong);
            if (is_string($r) && strlen($r) === $length && $strong) {
                return $r;
            }
        }
        return false;
    }
}
