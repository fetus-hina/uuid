<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid;

use jp3cki\uuid\internal\Random;
use jp3cki\uuid\internal\Timestamp;

use function bin2hex;
use function chr;
use function floor;
use function hash;
use function hash_algos;
use function hex2bin;
use function implode;
use function in_array;
use function is_string;
use function microtime;
use function ord;
use function pack;
use function preg_match;
use function preg_replace;
use function random_int;
use function str_repeat;
use function strlen;
use function strtolower;
use function substr;
use function trim;

final class Uuid
{
    private const BINARY_OCTETS = 16;

    private string $binary = '';

    public static function nil(): self
    {
        return self::fromString(NS::NIL);
    }

    public static function maxUuid(): self
    {
        return self::fromString(NS::MAX);
    }

    public static function v1(Mac|string|null $mac = null): self
    {
        $timestamp = static::v1Timestamp();
        $mac = new Mac($mac);
        $instance = new self();
        $instance->binary = pack(
            'Nnn',
            $timestamp & 0xffffffff,
            ($timestamp >> 32) & 0xffff,
            ($timestamp >> 48) & 0xffff,
        );
        $instance->binary .= Random::bytes(2) . $mac->getBinary();
        $instance->fix(1);
        return $instance;
    }

    public static function v3(self|string $namespace, string $value): self
    {
        return static::hashedUuid('md5', $namespace, $value);
    }

    public static function v4(): self
    {
        $instance = new self();
        $instance->binary = Random::bytes(self::BINARY_OCTETS); // 128 bits
        $instance->fix(4);
        return $instance;
    }

    public static function v5(self|string $namespace, string $value): self
    {
        return static::hashedUuid('sha1', $namespace, $value);
    }

    public static function v7(): self
    {
        static $seqNo = null;
        if ($seqNo === null) {
            // The initial value of the sequence number is created at random.
            // The maximum value of this number may be 0xfff, but it may wrap up with a small number of generation.
            // So, a little margin is given to ensure the number of IDs generated is not a problem in practical use.
            $seqNo = random_int(0x0000, 0x0f00);
        }

        $seqNo = ($seqNo + 1) % 0x1000;

        $unixTsMs = (int)floor(microtime(true) * 1000);
        $unixTsMsBin64 = pack('J', $unixTsMs);
        $tsBin = substr($unixTsMsBin64, -6) . pack('n', $seqNo);

        $instance = new self();
        $instance->binary = $tsBin . Random::bytes(self::BINARY_OCTETS - (6 + 2));
        $instance->fix(7);
        return $instance;
    }

    public static function v8(string $binary): self
    {
        if (strlen($binary) !== self::BINARY_OCTETS) {
            throw new Exception('The argument must be a binary string of exactly ' . self::BINARY_OCTETS . ' octets.');
        }

        $instance = new self();
        $instance->binary = $binary;
        $instance->fix(8);
        return $instance;
    }

    public static function sha256(self|string $namespace, string $value): self
    {
        return static::hashedUuid('sha256', $namespace, $value);
    }

    public static function fromString(string $value): self
    {
        $instance = new self();
        $value = trim($value);
        if ($value === '') {
            throw new Exception('Given string is not a valid UUID.');
        }

        if (strlen($value) === self::BINARY_OCTETS) {
            $instance->binary = $value;
            if (!$instance->isValid()) {
                throw new Exception('Given string is not a valid UUID.');
            }
            return $instance;
        }

        $value = (string)preg_replace('/(?:urn|uuid):|[{}-]/', '', $value);
        if (preg_match('/^[0-9A-Fa-f]{32}$/', $value)) {
            $tmp = hex2bin($value);
            if ($tmp !== false) {
                $instance->binary = $tmp;
                if ($instance->isValid()) {
                    return $instance;
                }
            }

            throw new Exception('Given string is not a valid UUID.');
        }

        throw new Exception('Given string is not a UUID.');
    }

    /**
     * @param 'md5'|'sha1'|'sha256' $hash
     */
    private static function hashedUuid(string $hash, self|string $ns, string $value): self
    {
        if (!in_array($hash, hash_algos(), true)) {
            throw new Exception("The hash function {$hash} is not supported on this system.");
        }

        $instance = new self();
        $instance->binary = substr(
            hash(
                $hash,
                ($ns instanceof self ? $ns : new self($ns))->binary . $value,
                true,
            ),
            0,
            self::BINARY_OCTETS,
        );
        $instance->fix(
            match ($hash) {
                'md5' => 3,
                'sha1' => 5,
                'sha256' => 8, // custom
            },
        );
        return $instance;
    }

    public function __construct(self|string|null $uuid = null)
    {
        $this->binary = match (true) {
            $uuid instanceof self => $uuid->binary,
            $uuid === null, $uuid === '' => self::nilUuidBinary(),
            is_string($uuid) => static::fromString($uuid)->binary,
            // default => throw new Exception('Could not create instance of UUID.'),
        };
    }

    public function __toString(): string
    {
        return $this->formatAsString();
    }

    public function formatAsString(): string
    {
        $hex = strtolower(bin2hex($this->binary));
        return implode('-', [
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12),
        ]);
    }

    public function formatAsUri(): string
    {
        return 'urn:uuid:' . $this->formatAsString();
    }

    public function getVersion(): int
    {
        return (ord($this->binary[6]) & 0xf0) >> 4;
    }

    public function isValid(): bool
    {
        return match ($this->getVersion()) {
            0 => $this->binary === self::nilUuidBinary(),
            1, 2, 3, 4, 5, 6, 7, 8 => true,
            15 => $this->binary === self::maxUuidBinary(),
            default => false,
        };
    }

    private function fix(int $version): void
    {
        $manip = function (int $offset, int $mask, int $add): void {
            $mask = $mask & 0xff;
            $add = $add & 0xff;
            $value = ord($this->binary[$offset]);
            $value = ($value & $mask) | $add;
            $this->binary[$offset] = chr($value);
        };

        $manip(6, 0x0f, ($version & 0x0f) << 4);
        $manip(8, 0x3f, 0x80);
    }

    private static function v1Timestamp(): int
    {
        // $baseUnixTime = gmmktime(0, 0, 0, 10, 15, 1582); // 1582-10-15T00:00:00+00:00, -12219292800
        // if ($baseUnixTime === false) {
        //     throw new Exception('Could not create UUID v1. Failed to calc timestamp');
        // }
        $baseTimestamp = -12219292800 * 1000 * 1000 * 10;
        $currentTimestamp = Timestamp::currentV1Timestamp();
        return $currentTimestamp - $baseTimestamp;
    }

    private static function nilUuidBinary(): string
    {
        return str_repeat(chr(0), self::BINARY_OCTETS);
    }

    private static function maxUuidBinary(): string
    {
        return str_repeat(chr(0xff), self::BINARY_OCTETS);
    }
}
