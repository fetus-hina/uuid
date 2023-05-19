<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
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
use function method_exists;
use function preg_match;
use function str_repeat;
use function time;

final class UuidTest extends TestCase
{
    /** @return void */
    public function testDefaultConstruct()
    {
        $this->assertEquals(NS::NIL, (new Uuid())->__toString());
    }

    /** @return void */
    public function testGenerateV1()
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
        if (method_exists($this, 'assertEqualsWithDelta')) {
            $this->assertEqualsWithDelta($now, $uuidTS, 1);
        } else {
            $this->assertEquals((float)$now, (float)$uuidTS, '', 1.0); // @phpstan-ignore-line
        }
    }

    /** @return void */
    public function testGenerateV3()
    {
        $this->assertEquals(
            '3d813cbb-47fb-32ba-91df-831e1593ac29',
            Uuid::v3(NS::dns(), 'www.widgets.com')->__toString(),
        );
    }

    /** @return void */
    public function testGenerateV5()
    {
        $this->assertEquals(
            '74738ff5-5367-5958-9aee-98fffdcd1876',
            Uuid::v5(NS::dns(), 'www.example.org')->__toString(),
        );
    }

    /** @return void */
    public function testGenerateV4()
    {
        $this->assertNotEquals(
            Uuid::v4()->__toString(),
            Uuid::v4()->__toString(),
        );
    }

    /** @return void */
    public function testFormatAsString()
    {
        $this->assertEquals(
            '3d813cbb-47fb-32ba-91df-831e1593ac29',
            Uuid::v3(NS::dns(), 'www.widgets.com')->formatAsString(),
        );
    }

    /** @return void */
    public function testFormatAsUri()
    {
        $this->assertEquals(
            'urn:uuid:3d813cbb-47fb-32ba-91df-831e1593ac29',
            Uuid::v3(NS::dns(), 'www.widgets.com')->formatAsUri(),
        );
    }

    /** @return void */
    public function testGetVersion()
    {
        $this->assertEquals(0, NS::nil()->getVersion());
        $this->assertEquals(1, NS::dns()->getVersion());
    }

    /** @return void */
    public function testConstructFromString()
    {
        $this->assertEquals(NS::DNS, (new Uuid(NS::DNS))->__toString());
    }

    /** @return void */
    public function testConstructFromBinary()
    {
        $this->assertEquals(
            NS::DNS,
            (new Uuid((string)hex2bin('6ba7b8109dad11d180b400c04fd430c8')))->__toString(),
        );
    }

    /** @return void */
    public function testConstructFromUri()
    {
        $this->assertEquals(
            NS::DNS,
            (new Uuid('urn:uuid:6ba7b810-9dad-11d1-80b4-00c04fd430c8'))->__toString(),
        );
    }

    /** @return void */
    public function testConstructFromBrokenString()
    {
        $this->expectException(Exception::class);
        new Uuid('hoge');
    }

    /** @return void */
    public function testConstructFromBrokenNilUuid()
    {
        $this->expectException(Exception::class);

        // 00000000-0000-0000-0000-0000000000ff
        new Uuid(str_repeat(chr(0), 15) . chr(0xff));
    }

    /** @return void */
    public function testConstructFromInvalidVersion()
    {
        $this->expectException(Exception::class);
        //                      v
        new Uuid('74738ff5-5367-6958-9aee-98fffdcd1876');
        //                      ^
    }

    /** @return void */
    public function testConstructFromOtherInstance()
    {
        $this->assertEquals(
            NS::DNS,
            (new Uuid(new Uuid(NS::DNS)))->__toString(),
        );
    }

    /** @return void */
    public function testFromStringEmpty()
    {
        $this->expectException(Exception::class);
        Uuid::fromString('');
    }

    /** @return void */
    public function testFromStringInvalidBinary()
    {
        $this->expectException(Exception::class);
        Uuid::fromString((string)hex2bin('74738ff5536769589aee98fffdcd1876'));
    }
}
