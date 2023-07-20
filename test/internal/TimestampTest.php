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
use function time;

final class TimestampTest extends TestCase
{
    public function testCurrentV1Timestamp(): void
    {
        $expectSec = time();
        $v = Timestamp::currentV1Timestamp();
        $this->assertTrue(is_int($v));

        // 秒精度で一致するはず
        $timeSec = (int)($v / (1000 * 1000 * 10));
        $this->assertEqualsWithDelta($expectSec, $timeSec, 1);
    }

    public function testV1Microtime(): void
    {
        $expectSec = time();
        $v = Timestamp::v1Microtime();
        $this->assertTrue(is_int($v));

        // 秒精度で一致するはず
        assert(is_int($v));
        $timeSec = (int)($v / (1000 * 1000 * 10));
        $this->assertEqualsWithDelta($expectSec, $timeSec, 1);
    }

    public function testV1Time(): void
    {
        $expectSec = time();
        $v = Timestamp::v1Time();
        $this->assertTrue(is_int($v));

        // 秒精度で一致するはず
        $timeSec = (int)($v / (1000 * 1000 * 10));
        $this->assertEqualsWithDelta($expectSec, $timeSec, 1);
    }

    public function testCurrentUlidTime(): void
    {
        $expectSec = time();
        $v = Timestamp::currentUlidTime();
        $this->assertTrue(is_int($v));

        // 秒精度で一致するはず
        $timeSec = (int)($v / 1000);
        $this->assertEqualsWithDelta($expectSec, $timeSec, 1);
    }

    public function testUlidMicrotime(): void
    {
        $expectSec = time();
        $v = Timestamp::ulidMicrotime();
        $this->assertTrue(is_int($v));

        // 秒精度で一致するはず
        assert(is_int($v));
        $timeSec = (int)($v / 1000);
        $this->assertEqualsWithDelta($expectSec, $timeSec, 1);
    }

    public function testUlidTime(): void
    {
        $expectSec = time();
        $v = Timestamp::ulidTime();
        $this->assertTrue(is_int($v));

        // 秒精度で一致するはず
        $timeSec = (int)($v / 1000);
        $this->assertEqualsWithDelta($expectSec, $timeSec, 1);
    }
}
