<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid;

use jp3cki\uuid\internal\Random;
use jp3cki\uuid\internal\Timestamp;

final class Uuid
{
    /** @var string */
    protected $binary;

    /**
     * @param Mac|string|null $mac
     */
    public static function v1($mac = null): self
    {
        $timestamp = static::v1Timestamp();
        $mac = new Mac($mac);
        $instance = new self();
        $instance->binary = pack(
            'Nnn',
            $timestamp & 0xffffffff,
            ($timestamp >> 32) & 0xffff,
            ($timestamp >> 48) & 0xffff
        );
        $instance->binary .= Random::bytes(2) . $mac->getBinary();
        $instance->fix(1);
        return $instance;
    }

    /**
     * @param self|string $namespace
     */
    public static function v3($namespace, string $value): self
    {
        return static::hashedUuid('md5', 3, $namespace, $value);
    }

    public static function v4(): self
    {
        $instance = new self();
        $instance->binary = Random::bytes(16); // 128 bits
        $instance->fix(4);
        return $instance;
    }

    /**
     * @param self|string $namespace
     */
    public static function v5($namespace, string $value): self
    {
        return static::hashedUuid('sha1', 5, $namespace, $value);
    }

    public static function fromString(string $value): self
    {
        $instance = new self();
        $value = trim($value);
        if ($value === '') {
            throw new Exception('Given string is not a valid UUID.');
        }

        if (strlen($value) === 16) {
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
     * @param self|string $ns
     */
    protected static function hashedUuid(string $hash, int $version, $ns, string $value): self
    {
        $nsUuid = ($ns instanceof self) ? $ns : new self($ns);
        $instance = new self();
        $instance->binary = substr(hash($hash, $nsUuid->binary . $value, true), 0, 16);
        $instance->fix($version);
        return $instance;
    }

    /**
     * @param self|string|null $uuid
     */
    public function __construct($uuid = null)
    {
        if ($uuid instanceof self) {
            $this->binary = $uuid->binary;
        } elseif ($uuid === null || $uuid === '') {
            $this->binary = str_repeat(chr(0), 16);
        } elseif (is_string($uuid)) {
            $this->binary = static::fromString($uuid)->binary;
        } else {
            throw new Exception('Could not create instance of UUID.');
        }
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
        switch ($this->getVersion()) {
            case 0:
                return $this->binary === str_repeat(chr(0), 16);

            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
                return true;

            default:
                return false;
        }
    }

    /**
     * @return void
     */
    protected function fix(int $version)
    {
        $manip = function (int $offset, int $mask, int $add) {
            $mask = $mask & 0xff;
            $add  = $add  & 0xff;
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
}
