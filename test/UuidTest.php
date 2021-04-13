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

class UuidTest extends TestCase
{
    public function testDefaultConstruct()
    {
        $this->assertEquals(NS::NIL, (new Uuid())->__toString());
    }

    public function testGenerateV3()
    {
        $this->assertEquals(
            '3d813cbb-47fb-32ba-91df-831e1593ac29',
            Uuid::v3(NS::dns(), 'www.widgets.com')->__toString()
        );
    }

    public function testGenerateV5()
    {
        $this->assertEquals(
            '74738ff5-5367-5958-9aee-98fffdcd1876',
            Uuid::v5(NS::dns(), 'www.example.org')->__toString()
        );
    }

    public function testGenerateV4()
    {
        $this->assertNotEquals(
            Uuid::v4()->__toString(),
            Uuid::v4()->__toString()
        );
    }

    public function testFormatAsString()
    {
        $this->assertEquals(
            '3d813cbb-47fb-32ba-91df-831e1593ac29',
            Uuid::v3(NS::dns(), 'www.widgets.com')->formatAsString()
        );
    }

    public function testFormatAsUri()
    {
        $this->assertEquals(
            'urn:uuid:3d813cbb-47fb-32ba-91df-831e1593ac29',
            Uuid::v3(NS::dns(), 'www.widgets.com')->formatAsUri()
        );
    }

    public function testGetVersion()
    {
        $this->assertEquals(0, NS::nil()->getVersion());
        $this->assertEquals(1, NS::dns()->getVersion());
    }

    public function testConstructFromString()
    {
        $this->assertEquals(NS::DNS, (new Uuid(NS::DNS))->__toString());
    }

    public function testConstructFromBinary()
    {
        $this->assertEquals(
            NS::DNS,
            (new Uuid(hex2bin('6ba7b8109dad11d180b400c04fd430c8')))->__toString()
        );
    }

    public function testConstructFromUri()
    {
        $this->assertEquals(
            NS::DNS,
            (new Uuid('urn:uuid:6ba7b810-9dad-11d1-80b4-00c04fd430c8'))->__toString()
        );
    }

    public function testConstructFromBrokenString()
    {
        $this->expectException(Exception::class);
        new Uuid('hoge');
    }

    public function testConstructFromBrokenNilUuid()
    {
        $this->expectException(Exception::class);

        // 00000000-0000-0000-0000-0000000000ff
        new Uuid(str_repeat(chr(0), 15) . chr(0xff));
    }

    public function testConstructFromUnexpectedType()
    {
        $this->expectException(Exception::class);
        new Uuid(42);
    }

    public function testConstructFromInvalidVersion()
    {
        $this->expectException(Exception::class);
        //                      v
        new Uuid('74738ff5-5367-6958-9aee-98fffdcd1876');
        //                      ^
    }

    public function testConstructFromOtherInstance()
    {
        $this->assertEquals(
            NS::DNS,
            (new Uuid(new Uuid(NS::DNS)))->__toString()
        );
    }
}
