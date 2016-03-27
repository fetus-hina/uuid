<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace jp3cki\uuid\test;

use jp3cki\uuid\Exception as Except;
use jp3cki\uuid\NS;
use jp3cki\uuid\Uuid;

class UuidTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @expectedException jp3cki\uuid\Exception
     */
    public function testConstructFromBrokenString()
    {
        new Uuid('hoge');
    }

    /**
     * @expectedException jp3cki\uuid\Exception
     */
    public function testConstructFromBrokenNilUuid()
    {
        // 00000000-0000-0000-0000-0000000000ff
        new Uuid(str_repeat(chr(0), 15) . chr(0xff));
    }

    /**
     * @expectedException jp3cki\uuid\Exception
     */
    public function testConstructFromUnexpectedType()
    {
        new Uuid(42);
    }

    /**
     * @expectedException jp3cki\uuid\Exception
     */
    public function testConstructFromInvalidVersion()
    {
        new Uuid('74738ff5-5367-6958-9aee-98fffdcd1876');
        //                     ^^^
    }

    public function testConstructFromOtherInstance()
    {
        $this->assertEquals(
            NS::DNS,
            (new Uuid(new Uuid(NS::DNS)))->__toString()
        );
    }
}
