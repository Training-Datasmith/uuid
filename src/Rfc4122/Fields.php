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
namespace Ramsey\Uuid\Rfc4122;

use function bin2hex;
use function dechex;
use function hexdec;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Fields\Serializable_Fields_Trait;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;
use function sprintf;
use function str_pad;
use const STR_PAD_LEFT;
use function strlen;
use function substr;
use function unpack;
/**
 * RFC 9562 (formerly RFC 4122) variant UUIDs consist of a set of named fields
 *
 * Internally, this class represents the fields together as a 16-byte binary string.
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
     * @throws InvalidArgumentException if the byte string does not represent an RFC 9562 (formerly RFC 4122) UUID
     * @throws InvalidArgumentException if the byte string does not contain a valid version
     */
    public function __construct(private string $bytes)
    {
        if (strlen($this->bytes) !== 16) {
            throw new InvalidArgumentException('The byte string must be 16 bytes long; ' . 'received ' . strlen($this->bytes) . ' bytes');
        }
        if (!$this->is_correct_variant()) {
            throw new InvalidArgumentException('The byte string received does not conform to the RFC 9562 (formerly RFC 4122) variant');
        }
        if (!$this->is_correct_version()) {
            throw new InvalidArgumentException('The byte string received does not contain a valid RFC 9562 (formerly RFC 4122) version');
        }
    }
    /**
     * @pure
     */
    public function get_bytes(): string
    {
        return $this->bytes;
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
    /**
     * Returns the full 60-bit timestamp, without the version
     *
     * For version 2 UUIDs, the time_low field is the local identifier and should not be returned as part of the time.
     * For this reason, we set the bottom 32 bits of the timestamp to 0's. As a result, there is some loss of timestamp
     * fidelity, for version 2 UUIDs. The timestamp can be off by a range of 0 to 429.4967295 seconds (or 7 minutes, 9
     * seconds, and 496,730 microseconds).
     *
     * For version 6 UUIDs, the timestamp order is reversed from the typical RFC 9562 (formerly RFC 4122) order (the
     * time bits are in the correct bit order, so that it is monotonically increasing). In returning the timestamp
     * value, we put the bits in the order: time_low + time_mid + time_hi.
     */
    public function get_timestamp(): Hexadecimal
    {
        return new Hexadecimal(match ($this->get_version()) {
            Uuid::UUID_TYPE_DCE_SECURITY => sprintf('%03x%04s%08s', hexdec($this->get_time_hi_and_version()->to_string()) & 0xfff, $this->get_time_mid()->to_string(), ''),
            Uuid::UUID_TYPE_REORDERED_TIME => sprintf('%08s%04s%03x', $this->get_time_low()->to_string(), $this->get_time_mid()->to_string(), hexdec($this->get_time_hi_and_version()->to_string()) & 0xfff),
            // The Unix timestamp in version 7 UUIDs is a 48-bit number, but for consistency, we will return a 60-bit
            // number, padded to the left with zeros.
            Uuid::UUID_TYPE_UNIX_TIME => sprintf('%011s%04s', $this->get_time_low()->to_string(), $this->get_time_mid()->to_string()),
            default => sprintf('%03x%04s%08s', hexdec($this->get_time_hi_and_version()->to_string()) & 0xfff, $this->get_time_mid()->to_string(), $this->get_time_low()->to_string()),
        });
    }
    public function get_version(): ?int
    {
        if ($this->is_nil() || $this->is_max()) {
            return null;
        }
        /** @var int[] $parts */
        $parts = unpack('n*', $this->bytes);
        return $parts[4] >> 12;
    }
    private function is_correct_variant(): bool
    {
        if ($this->is_nil() || $this->is_max()) {
            return true;
        }
        return $this->get_variant() === Uuid::RFC_4122;
    }
}