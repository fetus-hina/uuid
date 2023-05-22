<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\test;

use PHPUnit\Framework\TestCase;
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
     * @return array<string, string[]>
     */
    public static function rfc4122Namespaces(): array
    {
        return [
            'nil' => ['nil', 'NIL', '00000000-0000-0000-0000-000000000000'],
            'dns' => ['dns', 'DNS', '6ba7b810-9dad-11d1-80b4-00c04fd430c8'],
            'url' => ['url', 'URL', '6ba7b811-9dad-11d1-80b4-00c04fd430c8'],
            'oid' => ['oid', 'OID', '6ba7b812-9dad-11d1-80b4-00c04fd430c8'],
            'x500' => ['x500', 'X500', '6ba7b814-9dad-11d1-80b4-00c04fd430c8'],
        ];
    }
}
