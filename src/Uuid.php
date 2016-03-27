<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/uuid/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace jp3cki\uuid;

class Uuid
{
    protected $binary;

    public static function v3($namespace, $value)
    {
        return static::hashedUuid('md5', 3, $namespace, $value);
    }

    public static function v4()
    {
        $instance = new static();
        $instance->binary = internal\Random::bytes(16); // 128 bits
        $instance->fix(4);
        return $instance;
    }

    public static function v5($namespace, $value)
    {
        return static::hashedUuid('sha1', 5, $namespace, $value);
    }

    public static function fromString($value)
    {
        $instance = new static();
        $value = trim((string)$value);
        if ($value === '') {
            goto done;
        }
        if (strlen($value) === 16) {
            $instance->binary = $value;
            goto done;
        }
        $value = preg_replace('/(?:urn|uuid):|[{}-]/', '', $value);
        if (preg_match('/^[0-9A-Fa-f]{32}$/', $value)) {
            $instance->binary = hex2bin($value);
            goto done;
        }

        throw new Exception('Given string is not a UUID.');

        done:
        if (!$instance->isValid()) {
            throw new Exception('Given string is not a valid UUID.');
        }
        return $instance;
    }

    protected static function hashedUuid($hash, $version, $ns, $value)
    {
        $nsUuid = ($ns instanceof static) ? $ns : new static($ns);
        $instance = new static();
        $instance->binary = substr(hash($hash, $nsUuid->binary . $value, true), 0, 16);
        $instance->fix($version);
        return $instance;
    }

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

    public function __toString()
    {
        return $this->formatAsString();
    }

    public function formatAsString()
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

    public function formatAsUri()
    {
        return 'urn:uuid:' . $this->formatAsString();
    }

    public function getVersion()
    {
        return (ord($this->binary[6]) & 0xf0) >> 4;
    }

    public function isValid()
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

    protected function fix($version)
    {
        $manip = function ($offset, $mask, $add) {
            $mask = $mask & 0xff;
            $add  = $add  & 0xff;
            $value = ord($this->binary[$offset]);
            $value = ($value & $mask) | $add;
            $this->binary[$offset] = chr($value);
        };
        $manip(6, 0x0f, ($version & 0x0f) << 4);
        $manip(8, 0x3f, 0x80);
    }
}
