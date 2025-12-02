<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid;

use UnexpectedValueException;

final class NS
{
    public const NIL = '00000000-0000-0000-0000-000000000000';
    public const MAX = 'ffffffff-ffff-ffff-ffff-ffffffffffff';
    public const DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    public const URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    public const OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    public const X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    // https://www.ietf.org/archive/id/draft-ietf-uuidrev-rfc4122bis-03.txt
    // Appendix B.  Some Hash Space IDs
    public const HASH_SPACE_SHA2_224 = '59031ca3-fbdb-47fb-9f6c-0f30e2e83145';
    public const HASH_SPACE_SHA2_256 = '3fb32780-953c-4464-9cfd-e85dbbe9843d';
    public const HASH_SPACE_SHA2_384 = 'e6800581-f333-484b-8778-601ff2b58da8';
    public const HASH_SPACE_SHA2_512 = '0fde22f2-e7ba-4fd1-9753-9c2ea88fa3f9';
    public const HASH_SPACE_SHA2_512_224 = '003c2038-c4fe-4b95-a672-0c26c1b79542';
    public const HASH_SPACE_SHA2_512_256 = '9475ad00-3769-4c07-9642-5e7383732306';
    public const HASH_SPACE_SHA3_224 = '9768761f-ac5a-419e-a180-7ca239e8025a';
    public const HASH_SPACE_SHA3_256 = '2034d66b-4047-4553-8f80-70e593176877';
    public const HASH_SPACE_SHA3_384 = '872fb339-2636-4bdd-bda6-b6dc2a82b1b3';
    public const HASH_SPACE_SHA3_512 = 'a4920a5d-a8a6-426c-8d14-a6cafbe64c7b';
    public const HASH_SPACE_SHAKE_128 = '7ea218f6-629a-425f-9f88-7439d63296bb';
    public const HASH_SPACE_SHAKE_256 = '2e7fc6a4-2919-4edc-b0ba-7d7062ce4f0a';

    public static function nil(): Uuid
    {
        return new Uuid(self::NIL);
    }

    public static function max(): Uuid
    {
        return new Uuid(self::MAX);
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

    public static function hashSpace(string $algo): Uuid
    {
        return new Uuid(
            match ($algo) {
                'sha224' => self::HASH_SPACE_SHA2_224,
                'sha256' => self::HASH_SPACE_SHA2_256,
                'sha384' => self::HASH_SPACE_SHA2_384,
                'sha512' => self::HASH_SPACE_SHA2_512,
                'sha512/224' => self::HASH_SPACE_SHA2_512_224,
                'sha512/256' => self::HASH_SPACE_SHA2_512_256,
                'sha3-224' => self::HASH_SPACE_SHA3_224,
                'sha3-256' => self::HASH_SPACE_SHA3_256,
                'sha3-384' => self::HASH_SPACE_SHA3_384,
                'sha3-512' => self::HASH_SPACE_SHA3_512,
                // 'shake128' => self::HASH_SPACE_SHAKE_128,
                // 'shake256' => self::HASH_SPACE_SHAKE_256,
                default => throw new UnexpectedValueException("Hash algorithm `{$algo}` is not supported."),
            },
        );
    }
}
