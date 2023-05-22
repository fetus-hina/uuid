<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\test\internal;

use PHPUnit\Framework\TestCase;
use jp3cki\uuid\internal\Timestamp;

use function assert;
use function is_int;
use function method_exists;
use function time;

final class TimestampTest extends TestCase
{
    /** @return void */
    public function testCurrentV1Timestamp()
    {
        $expectSec = time();
        $v = Timestamp::currentV1Timestamp();
        $this->assertTrue(is_int($v));

        // 秒精度で一致するはず
        $timeSec = (int)($v / (1000 * 1000 * 10));
        if (method_exists($this, 'assertEqualsWithDelta')) {
            $this->assertEqualsWithDelta($expectSec, $timeSec, 1);
        } else {
            $this->assertEquals((float)$expectSec, (float)$timeSec, '', 1.0); // @phpstan-ignore-line
        }
    }

    /** @return void */
    public function testV1Microtime()
    {
        $expectSec = time();
        $v = Timestamp::v1Microtime();
        $this->assertTrue(is_int($v));

        // 秒精度で一致するはず
        assert(is_int($v));
        $timeSec = (int)($v / (1000 * 1000 * 10));
        if (method_exists($this, 'assertEqualsWithDelta')) {
            $this->assertEqualsWithDelta($expectSec, $timeSec, 1);
        } else {
            $this->assertEquals((float)$expectSec, (float)$timeSec, '', 1.0); // @phpstan-ignore-line
        }
    }

    /** @return void */
    public function testV1Time()
    {
        $expectSec = time();
        $v = Timestamp::v1Time();
        $this->assertTrue(is_int($v));

        // 秒精度で一致するはず
        $timeSec = (int)($v / (1000 * 1000 * 10));
        if (method_exists($this, 'assertEqualsWithDelta')) {
            $this->assertEqualsWithDelta($expectSec, $timeSec, 1);
        } else {
            $this->assertEquals((float)$expectSec, (float)$timeSec, '', 1.0); // @phpstan-ignore-line
        }
    }
}
