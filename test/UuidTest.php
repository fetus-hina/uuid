<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\test;

use PHPUnit\Framework\TestCase;
use jp3cki\uuid\Exception;
use jp3cki\uuid\NS;
use jp3cki\uuid\Uuid;

use function chr;
use function floor;
use function gmmktime;
use function hex2bin;
use function intval;
use function microtime;
use function preg_match;
use function str_repeat;
use function time;

final class UuidTest extends TestCase
{
    public function testDefaultConstruct(): void
    {
        $this->assertEquals(NS::NIL, (new Uuid())->__toString());
    }

    public function testGenerateNil(): void
    {
        $this->assertEquals(NS::NIL, (string)Uuid::nil());
    }

    public function testGenerateUuid(): void
    {
        $this->assertEquals(NS::MAX, (string)Uuid::max());
    }

    public function testGenerateV1(): void
    {
        $now = time();

        $uuid = Uuid::v1('08:00:2b:01:02:03')->__toString();
        $this->assertTrue((bool)preg_match(
            '/^([0-9a-f]{8})-([0-9a-f]{4})-1([0-9a-f]{3})-([0-9a-f]{4})-([0-9a-f]{12})$/',
            $uuid,
            $match,
        ));

        $this->assertEquals('08002b010203', $match[5]);

        $uuidTS = (intval($match[3], 16) << 48) |
            (intval($match[2], 16) << 32) |
            intval($match[1], 16);
        $uuidTS += gmmktime(0, 0, 0, 10, 15, 1582) * 1000 * 1000 * 10;
        $uuidTS = (int)floor($uuidTS / (1000 * 1000 * 10));
        $this->assertEqualsWithDelta($now, $uuidTS, 1);
    }

    public function testGenerateV6(): void
    {
        $now = time();

        $uuid = Uuid::v6('08:00:2b:01:02:03')->__toString();
        $this->assertTrue((bool)preg_match(
            '/^([0-9a-f]{8})-([0-9a-f]{4})-6([0-9a-f]{3})-([0-9a-f]{4})-([0-9a-f]{12})$/',
            $uuid,
            $match,
        ));

        $this->assertEquals('08002b010203', $match[5]);

        $uuidTS = (intval($match[1], 16) << 28) |
            (intval($match[2], 16) << 12) |
            intval($match[3], 16);
        $uuidTS += gmmktime(0, 0, 0, 10, 15, 1582) * 1000 * 1000 * 10;
        $uuidTS = (int)floor($uuidTS / (1000 * 1000 * 10));
        $this->assertEqualsWithDelta($now, $uuidTS, 1);
    }

    public function testGenerateV3(): void
    {
        $this->assertEquals(
            '3d813cbb-47fb-32ba-91df-831e1593ac29',
            Uuid::v3(NS::dns(), 'www.widgets.com')->__toString(),
        );
    }

    public function testGenerateV5(): void
    {
        $this->assertEquals(
            '74738ff5-5367-5958-9aee-98fffdcd1876',
            Uuid::v5(NS::dns(), 'www.example.org')->__toString(),
        );
    }

    public function testGenerateSha256(): void
    {
        // https://www.ietf.org/archive/id/draft-ietf-uuidrev-rfc4122bis-03.txt
        // C.8.  Example of a UUIDv8 Value (name-based)
        $this->assertEquals(
            '401835fd-a627-870a-873f-ed73f2bc5b2c',
            Uuid::sha256(NS::dns(), 'www.example.com')->__toString(),
        );
    }

    public function testGenerateV4(): void
    {
        $this->assertNotEquals(
            Uuid::v4()->__toString(),
            Uuid::v4()->__toString(),
        );
    }

    public function testGenerateV7(): void
    {
        $now = (int)floor(microtime(true) * 1000);

        $uuid = Uuid::v7()->__toString();
        $this->assertTrue((bool)preg_match(
            '/^([0-9a-f]{8})-([0-9a-f]{4})-7([0-9a-f]{3})-([0-9a-f]{4})-([0-9a-f]{12})$/',
            $uuid,
            $match,
        ));

        $uuidTS = (intval($match[1], 16) << 16) | intval($match[2], 16);
        $this->assertEqualsWithDelta($now, $uuidTS, 1000);
    }

    public function testGenerateV8(): void
    {
        $this->assertEquals(
            '00000000-0000-8000-8000-000000000000',
            Uuid::v8(str_repeat(chr(0), 16))->__toString(),
        );

        $this->assertEquals(
            'ffffffff-ffff-8fff-bfff-ffffffffffff',
            Uuid::v8(str_repeat(chr(0xff), 16))->__toString(),
        );
    }

    public function testGenerateV8WithBrokenBinary1(): void
    {
        $this->expectException(Exception::class);
        Uuid::v8(str_repeat(chr(0), 15));
    }

    public function testGenerateV8WithBrokenBinary2(): void
    {
        $this->expectException(Exception::class);
        Uuid::v8(str_repeat(chr(0), 17));
    }

    public function testFormatAsString(): void
    {
        $this->assertEquals(
            '3d813cbb-47fb-32ba-91df-831e1593ac29',
            Uuid::v3(NS::dns(), 'www.widgets.com')->formatAsString(),
        );
    }

    public function testFormatAsUri(): void
    {
        $this->assertEquals(
            'urn:uuid:3d813cbb-47fb-32ba-91df-831e1593ac29',
            Uuid::v3(NS::dns(), 'www.widgets.com')->formatAsUri(),
        );
    }

    public function testGetVersion(): void
    {
        $this->assertEquals(0, NS::nil()->getVersion());
        $this->assertEquals(1, NS::dns()->getVersion());
    }

    public function testConstructFromString(): void
    {
        $this->assertEquals(NS::DNS, (new Uuid(NS::DNS))->__toString());
    }

    public function testConstructFromBinary(): void
    {
        $this->assertEquals(
            NS::DNS,
            (new Uuid((string)hex2bin('6ba7b8109dad11d180b400c04fd430c8')))->__toString(),
        );
    }

    public function testConstructFromUri(): void
    {
        $this->assertEquals(
            NS::DNS,
            (new Uuid('urn:uuid:6ba7b810-9dad-11d1-80b4-00c04fd430c8'))->__toString(),
        );
    }

    public function testConstructFromBrokenString(): void
    {
        $this->expectException(Exception::class);
        new Uuid('hoge');
    }

    public function testConstructFromBrokenNilUuid(): void
    {
        $this->expectException(Exception::class);

        // 00000000-0000-0000-0000-0000000000ff
        new Uuid(str_repeat(chr(0), 15) . chr(0xff));
    }

    public function testConstructFromInvalidVersion(): void
    {
        $this->expectException(Exception::class);
        //                      v
        new Uuid('74738ff5-5367-e958-9aee-98fffdcd1876');
        //                      ^
    }

    public function testConstructFromOtherInstance(): void
    {
        $this->assertEquals(
            NS::DNS,
            (new Uuid(new Uuid(NS::DNS)))->__toString(),
        );
    }

    public function testFromStringEmpty(): void
    {
        $this->expectException(Exception::class);
        Uuid::fromString('');
    }

    public function testFromStringInvalidBinary(): void
    {
        $this->expectException(Exception::class);
        Uuid::fromString((string)hex2bin('74738ff55367e9589aee98fffdcd1876'));
    }
}
