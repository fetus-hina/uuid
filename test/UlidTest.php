<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid\test;

use PHPUnit\Framework\TestCase;
use jp3cki\uuid\Ulid;

use function strlen;

final class UlidTest extends TestCase
{
    public function testUlid(): void
    {
        $obj = new Ulid();
        $value = (string)$obj;
        $this->assertEquals(26, strlen($value));
        $this->assertMatchesRegularExpression('/^[0123456789ABCDEFGHJKMNPQRSTVWXYZ]+$/i', $value);
    }
}
