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
namespace Ramsey\Uuid\Nonstandard;

use function bin2hex;
use function dechex;
use function hexdec;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Fields\Serializable_Fields_Trait;
use Ramsey\Uuid\Rfc4122\Fields_Interface;
use Ramsey\Uuid\Rfc4122\Variant_Trait;
use Ramsey\Uuid\Type\Hexadecimal;
use function sprintf;
use function str_pad;
use const STR_PAD_LEFT;
use function strlen;
use function substr;
/**
 * Nonstandard UUID fields do not conform to the RFC 9562 (formerly RFC 4122) standard
 *
 * Since some systems may create nonstandard UUIDs, this implements the {@see FieldsInterface}, so that functionality of
 * a nonstandard UUID is not degraded, in the event these UUIDs are expected to contain RFC 9562 (formerly RFC 4122) fields.
 *
 * Internally, this class represents the fields together as a 16-byte binary string.
 *
 * @immutable
 */
final class Fields implements Fields_Interface
{
    use Serializable_Fields_Trait;
    use Variant_Trait;
    /**
     * @param string $bytes A 16-byte binary string representation of a UUID
     *
     * @throws InvalidArgumentException if the byte string is not exactly 16 bytes
     */
    public function __construct(private string $bytes)
    {
        if (strlen($this->bytes) !== 16) {
            throw new InvalidArgumentException('The byte string must be 16 bytes long; received ' . strlen($this->bytes) . ' bytes');
        }
    }
    public function get_bytes(): string
    {
        return $this->bytes;
    }
    public function get_clock_seq(): Hexadecimal
    {
        $clock_seq = hexdec(bin2hex(substr($this->bytes, 8, 2))) & 0x3fff;
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
    public function get_time_hi_and_version(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 6, 2)));
    }
    public function get_time_low(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 0, 4)));
    }
    public function get_time_mid(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 4, 2)));
    }
    public function get_timestamp(): Hexadecimal
    {
        return new Hexadecimal(sprintf('%03x%04s%08s', hexdec($this->get_time_hi_and_version()->to_string()) & 0xfff, $this->get_time_mid()->to_string(), $this->get_time_low()->to_string()));
    }
    public function get_version(): ?int
    {
        return null;
    }
    public function is_nil(): bool
    {
        return false;
    }
    public function is_max(): bool
    {
        return false;
    }
}