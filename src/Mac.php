<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace jp3cki\uuid;

use Override;
use Stringable;
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

final class Mac implements Stringable
{
    private string $binary;

    public function __construct(self|string|null $address = null)
    {
        $this->binary = match (true) {
            $address instanceof self => $address->binary,
            $address === null, $address === '' => self::random(),
            is_string($address) => self::parse($address),
            // default => throw new Exception('Could not create instance of Mac class.'),
        };
    }

    #[Override]
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
        $hex = bin2hex($this->binary);
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
        // The constructor treats '' as "generate a random address", so reject it here.
        if ($value === '') {
            throw new Exception('Given string is not a valid Mac address.');
        }

        return new self($value);
    }

    private static function parse(string $value): string
    {
        // Check for raw 6-octet binary before trimming: octets such as 0x00, 0x20 or 0x0a
        // are valid at either end of a MAC address and must not be stripped.
        // No textual format is 6 characters long, so this cannot misclassify text input.
        if (strlen($value) === 6) {
            return $value;
        }

        $value = trim($value);
        if ($value === '') {
            throw new Exception('Given string is not a valid Mac address.');
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
                return $tmp;
            }
        }

        throw new Exception('Given string is not a Mac address.');
    }

    /**
     * Generates a random locally administered multicast address.
     *
     * The multicast bit (0x01) is set as RFC 9562 §6.10 requires for a random UUID
     * node ID: no IEEE 802 address assigned to a network card has it set, so the
     * result can never collide with real hardware. The local bit (0x02) is set too.
     */
    private static function random(): string
    {
        $binary = Random::bytes(6);
        $binary[0] = chr(ord($binary[0]) | 0x03); // set multicast & locally administered bits
        return $binary;
    }
}
