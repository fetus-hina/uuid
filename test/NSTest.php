<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\test;

use PHPUnit\Framework\TestCase;
use UnexpectedValueException;
use jp3cki\uuid\NS;
use jp3cki\uuid\Uuid;

use function call_user_func;
use function constant;
use function is_string;
use function sprintf;
use function strtolower;

final class NSTest extends TestCase
{
    /**
     * @dataProvider rfc4122Namespaces
     */
    public function testRfc4122Namespaces(string $method, string $unused1, string $expect): void
    {
        $nsUuid = call_user_func([NS::class, $method]); // @phpstan-ignore-line
        $this->assertInstanceOf(Uuid::class, $nsUuid);
        $this->assertEquals($expect, (string)$nsUuid);
    }

    /**
     * @dataProvider rfc4122Namespaces
     */
    public function testConstants(string $unused1, string $name, string $expect): void
    {
        $value = constant(sprintf('%s::%s', NS::class, $name));
        $this->assertTrue(is_string($value));
        $this->assertEquals(strtolower($expect), strtolower($value));
    }

    /**
     * @dataProvider rfc4122HashSpaces
     */
    public function testHashSpaces(string $algo, string $constName, string $expect): void
    {
        $nsUuid = NS::hashSpace($algo);
        $this->assertInstanceOf(Uuid::class, $nsUuid);
        $this->assertEquals($expect, (string)$nsUuid);
    }

    public function testInvalidHashSpaces(): void
    {
        $this->expectException(UnexpectedValueException::class);
        NS::hashSpace('md4');
    }

    /**
     * @dataProvider rfc4122HashSpaces
     */
    public function testHashConstants(?string $algo, string $constName, string $expect): void
    {
        $value = constant(sprintf('%s::%s', NS::class, $constName));
        $this->assertTrue(is_string($value));
        $this->assertEquals(strtolower($expect), strtolower($value));
    }

    /**
     * @return array<string, string[]>
     */
    public static function rfc4122Namespaces(): array
    {
        return [
            'nil' => ['nil', 'NIL', '00000000-0000-0000-0000-000000000000'],
            'max' => ['max', 'MAX', 'ffffffff-ffff-ffff-ffff-ffffffffffff'],
            'dns' => ['dns', 'DNS', '6ba7b810-9dad-11d1-80b4-00c04fd430c8'],
            'url' => ['url', 'URL', '6ba7b811-9dad-11d1-80b4-00c04fd430c8'],
            'oid' => ['oid', 'OID', '6ba7b812-9dad-11d1-80b4-00c04fd430c8'],
            'x500' => ['x500', 'X500', '6ba7b814-9dad-11d1-80b4-00c04fd430c8'],
        ];
    }

    /**
     * @return array<string, array{?string, string, string}>
     */
    public static function rfc4122HashSpaces(): array
    {
        return [
            'sha224' => ['sha224', 'HASH_SPACE_SHA2_224', '59031ca3-fbdb-47fb-9f6c-0f30e2e83145'],
            'sha256' => ['sha256', 'HASH_SPACE_SHA2_256', '3fb32780-953c-4464-9cfd-e85dbbe9843d'],
            'sha384' => ['sha384', 'HASH_SPACE_SHA2_384', 'e6800581-f333-484b-8778-601ff2b58da8'],
            'sha512' => ['sha512', 'HASH_SPACE_SHA2_512', '0fde22f2-e7ba-4fd1-9753-9c2ea88fa3f9'],
            'sha512/224' => ['sha512/224', 'HASH_SPACE_SHA2_512_224', '003c2038-c4fe-4b95-a672-0c26c1b79542'],
            'sha512/256' => ['sha512/256', 'HASH_SPACE_SHA2_512_256', '9475ad00-3769-4c07-9642-5e7383732306'],
            'sha3-224' => ['sha3-224', 'HASH_SPACE_SHA3_224', '9768761f-ac5a-419e-a180-7ca239e8025a'],
            'sha3-256' => ['sha3-256', 'HASH_SPACE_SHA3_256', '2034d66b-4047-4553-8f80-70e593176877'],
            'sha3-384' => ['sha3-384', 'HASH_SPACE_SHA3_384', '872fb339-2636-4bdd-bda6-b6dc2a82b1b3'],
            'sha3-512' => ['sha3-512', 'HASH_SPACE_SHA3_512', 'a4920a5d-a8a6-426c-8d14-a6cafbe64c7b'],
            // 'shake128' => [null, 'HASH_SPACE_SHAKE_128', '7ea218f6-629a-425f-9f88-7439d63296bb'],
            // 'shake256' => [null, 'HASH_SPACE_SHAKE_256', '2e7fc6a4-2919-4edc-b0ba-7d7062ce4f0a'],
        ];
    }
}
