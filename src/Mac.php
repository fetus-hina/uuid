<?php

/**
 * @copyright Copyright (C) 2016-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid;

use jp3cki\uuid\internal\Random;

use function bin2hex;
use function chr;
use function hex2bin;
use function implode;
use function is_string;
use function ord;
use function preg_match;
use function preg_replace;
use function strlen;
use function substr;
use function trim;

final class Mac
{
    /** @var string */
    protected $binary;

    public function __construct(self|string|null $address = null)
    {
        if ($address instanceof self) {
            $this->binary = $address->binary;
        } elseif ($address === null || $address === '') {
            $this->binary = static::random();
        } elseif (is_string($address)) {
            $this->binary = static::fromString($address)->binary;
        } else {
            throw new Exception('Could not create instance of Mac class.');
        }
    }

    public function __toString(): string
    {
        return $this->formatEUI();
    }

    public function format(): string
    {
        return $this->formatImpl('');
    }

    public function formatEUI(): string
    {
        return $this->formatImpl(':');
    }

    public function formatHyphen(): string
    {
        return $this->formatImpl('-');
    }

    private function formatImpl(string $sep): string
    {
        $hex = (string)bin2hex((string)$this->binary);
        return implode($sep, [
            substr($hex, 0, 2),
            substr($hex, 2, 2),
            substr($hex, 4, 2),
            substr($hex, 6, 2),
            substr($hex, 8, 2),
            substr($hex, 10, 2),
        ]);
    }

    public function getBinary(): string
    {
        return $this->binary;
    }

    public function isUnicast(): bool
    {
        return (ord($this->binary[0]) & 0x01) === 0;
    }

    public function isMulticast(): bool
    {
        return !$this->isUnicast();
    }

    public function isUniversal(): bool
    {
        return (ord($this->binary[0]) & 0x02) === 0;
    }

    public function isLocal(): bool
    {
        return !$this->isUniversal();
    }

    public static function fromString(string $value): self
    {
        $instance = new self();
        $value = trim($value);
        if ($value === '') {
            throw new Exception('Given string is not a valid Mac address.');
        }

        if (strlen($value) === 6) {
            $instance->binary = $value;
            return $instance;
        }

        if (
            preg_match('/^[0-9a-fA-F]{2}(?::[0-9a-fA-F]{2}){5}$/', $value) || // 08:00:2b:01:02:03
            preg_match('/^[0-9a-fA-F]{2}(?:-[0-9a-fA-F]{2}){5}$/', $value) || // 08-00-2b-01-02-03
            preg_match('/^[0-9a-fA-F]{6}:[0-9a-fA-F]{6}$/', $value) || // 08002b:010203
            preg_match('/^[0-9a-fA-F]{6}-[0-9a-fA-F]{6}$/', $value) || // 08002b-010203
            preg_match('/^[0-9a-fA-F]{4}(?:\.[0-9a-fA-F]{4}){2}$/', $value) || // 0800.2b01.0203
            preg_match('/^[0-9a-fA-F]{4}(?:-[0-9a-fA-F]{4}){2}$/', $value) || // 0800-2b01-0203
            preg_match('/^[0-9a-fA-F]{12}$/', $value) // 08002b010203
        ) {
            $value = (string)preg_replace('/[^0-9a-fA-F]+/', '', $value); // remove non-hexadecimal chars
            $tmp = hex2bin($value);
            if ($tmp !== false) {
                $instance->binary = $tmp;
                return $instance;
            }
        }

        throw new Exception('Given string is not a Mac address.');
    }

    private static function random(): string
    {
        $binary = Random::bytes(6);
        $binary[0] = chr((ord($binary[0]) & 0xfc) | 0x02); // drop multicast flag & set non global
        return $binary;
    }
}
