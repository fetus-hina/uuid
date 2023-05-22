<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 *
 * phpcs:disable PSR12.Properties.ConstantVisibility.NotFound
 */

declare(strict_types=1);

namespace jp3cki\uuid;

final class NS
{
    public const NIL = '00000000-0000-0000-0000-000000000000';
    public const MAX = 'ffffffff-ffff-ffff-ffff-ffffffffffff';
    public const DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    public const URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    public const OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    public const X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    public static function nil(): Uuid
    {
        return new Uuid(self::NIL);
    }

    public static function dns(): Uuid
    {
        return new Uuid(self::DNS);
    }

    public static function url(): Uuid
    {
        return new Uuid(self::URL);
    }

    public static function oid(): Uuid
    {
        return new Uuid(self::OID);
    }

    public static function x500(): Uuid
    {
        return new Uuid(self::X500);
    }
}
