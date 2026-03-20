<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
declare (strict_types=1);
namespace Ramsey\Uuid\Guid;

use function bin2hex;
use function dechex;
use function hexdec;
use function pack;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Fields\Serializable_Fields_Trait;
use Ramsey\Uuid\Rfc4122\Fields_Interface;
use Ramsey\Uuid\Rfc4122\Max_Trait;
use Ramsey\Uuid\Rfc4122\Nil_Trait;
use Ramsey\Uuid\Rfc4122\Variant_Trait;
use Ramsey\Uuid\Rfc4122\Version_Trait;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;
use function sprintf;
use function str_pad;
use const STR_PAD_LEFT;
use function strlen;
use function substr;
use function unpack;
/**
 * GUIDs consist of a set of named fields, according to RFC 9562 (formerly RFC 4122)
 *
 * @see Guid
 *
 * @immutable
 */
final class Fields implements Fields_Interface
{
    use Max_Trait;
    use Nil_Trait;
    use Serializable_Fields_Trait;
    use Variant_Trait;
    use Version_Trait;
    /**
     * @param string $bytes A 16-byte binary string representation of a UUID
     *
     * @throws InvalidArgumentException if the byte string is not exactly 16 bytes
     * @throws InvalidArgumentException if the byte string does not represent a GUID
     * @throws InvalidArgumentException if the byte string does not contain a valid version
     */
    public function __construct(private string $bytes)
    {
        if (strlen($this->bytes) !== 16) {
            throw new InvalidArgumentException('The byte string must be 16 bytes long; received ' . strlen($this->bytes) . ' bytes');
        }
        if (!$this->is_correct_variant()) {
            throw new InvalidArgumentException('The byte string received does not conform to the RFC 9562 (formerly RFC 4122) ' . 'or Microsoft Corporation variants');
        }
        if (!$this->is_correct_version()) {
            throw new InvalidArgumentException('The byte string received does not contain a valid version');
        }
    }
    public function get_bytes(): string
    {
        return $this->bytes;
    }
    public function get_time_low(): Hexadecimal
    {
        // Swap the bytes from little endian to network byte order.
        /** @var string[] $hex */
        $hex = unpack('H*', pack('v*', hexdec(bin2hex(substr($this->bytes, 2, 2))), hexdec(bin2hex(substr($this->bytes, 0, 2)))));
        return new Hexadecimal($hex[1] ?? '');
    }
    public function get_time_mid(): Hexadecimal
    {
        // Swap the bytes from little endian to network byte order.
        /** @var string[] $hex */
        $hex = unpack('H*', pack('v', hexdec(bin2hex(substr($this->bytes, 4, 2)))));
        return new Hexadecimal($hex[1] ?? '');
    }
    public function get_time_hi_and_version(): Hexadecimal
    {
        // Swap the bytes from little endian to network byte order.
        /** @var string[] $hex */
        $hex = unpack('H*', pack('v', hexdec(bin2hex(substr($this->bytes, 6, 2)))));
        return new Hexadecimal($hex[1] ?? '');
    }
    public function get_timestamp(): Hexadecimal
    {
        return new Hexadecimal(sprintf('%03x%04s%08s', hexdec($this->get_time_hi_and_version()->to_string()) & 0xfff, $this->get_time_mid()->to_string(), $this->get_time_low()->to_string()));
    }
    public function get_clock_seq(): Hexadecimal
    {
        if ($this->is_max()) {
            $clock_seq = 0xffff;
        } elseif ($this->is_nil()) {
            $clock_seq = 0x0;
        } else {
            $clock_seq = hexdec(bin2hex(substr($this->bytes, 8, 2))) & 0x3fff;
        }
        return new Hexadecimal(str_pad(dechex($clock_seq), 4, '0', STR_PAD_LEFT));
    }
    public function get_clock_seq_hi_and_reserved(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 8, 1)));
    }
    public function get_clock_seq_low(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 9, 1)));
    }
    public function get_node(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 10)));
    }
    public function get_version(): ?int
    {
        if ($this->is_nil() || $this->is_max()) {
            return null;
        }
        /** @var int[] $parts */
        $parts = unpack('n*', $this->bytes);
        return $parts[4] >> 4 & 0xf;
    }
    private function is_correct_variant(): bool
    {
        if ($this->is_nil() || $this->is_max()) {
            return true;
        }
        $variant = $this->get_variant();
        return $variant === Uuid::RFC_4122 || $variant === Uuid::RESERVED_MICROSOFT;
    }
}