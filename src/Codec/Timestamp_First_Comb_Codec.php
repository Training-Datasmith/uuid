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
namespace Ramsey\Uuid\Codec;

use function bin2hex;
use Ramsey\Uuid\Exception\Invalid_Uuid_String_Exception;
use Ramsey\Uuid\Uuid_Interface;
use function sprintf;
use function substr;
use function substr_replace;
/**
 * TimestampFirstCombCodec encodes and decodes COMBs, with the timestamp as the first 48 bits
 *
 * In contrast with the TimestampLastCombCodec, the TimestampFirstCombCodec adds the timestamp to the first 48 bits of
 * the COMB. To generate a timestamp-first COMB, set the TimestampFirstCombCodec as the codec, along with the
 * CombGenerator as the random generator.
 *
 * ```
 * $factory = new UuidFactory();
 *
 * $factory->setCodec(new TimestampFirstCombCodec($factory->getUuidBuilder()));
 *
 * $factory->setRandomGenerator(new CombGenerator(
 *     $factory->getRandomGenerator(),
 *     $factory->getNumberConverter(),
 * ));
 *
 * $timestampFirstComb = $factory->uuid4();
 * ```
 *
 * @deprecated Please migrate to {@link https://uuid.ramsey.dev/en/stable/rfc4122/version7.html Version 7, Unix Epoch Time UUIDs}.
 *
 * @link https://web.archive.org/web/20240118030355/https://www.informit.com/articles/printerfriendly/25862 The Cost of GUIDs as Primary Keys
 *
 * @immutable
 */
class Timestamp_First_Comb_Codec extends String_Codec
{
    /**
     * @return non-empty-string
     */
    public function encode(Uuid_Interface $uuid): string
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        $bytes = $this->swap_bytes($uuid->get_fields()->get_bytes());
        return sprintf('%08s-%04s-%04s-%04s-%012s', bin2hex(substr($bytes, 0, 4)), bin2hex(substr($bytes, 4, 2)), bin2hex(substr($bytes, 6, 2)), bin2hex(substr($bytes, 8, 2)), bin2hex(substr($bytes, 10)));
    }
    /**
     * @return non-empty-string
     */
    public function encode_binary(Uuid_Interface $uuid): string
    {
        /** @phpstan-ignore-next-line PHPStan complains that this is not a non-empty-string. */
        return $this->swap_bytes($uuid->get_fields()->get_bytes());
    }
    /**
     * @throws InvalidUuidStringException
     *
     * @inheritDoc
     */
    public function decode(string $encoded_uuid): Uuid_Interface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        $bytes = $this->get_bytes($encoded_uuid);
        /** @phpstan-ignore possiblyImpure.methodCall */
        return $this->get_builder()->build($this, $this->swap_bytes($bytes));
    }
    public function decode_bytes(string $bytes): Uuid_Interface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        return $this->get_builder()->build($this, $this->swap_bytes($bytes));
    }
    /**
     * Swaps bytes according to the timestamp-first COMB rules
     *
     * @pure
     */
    private function swap_bytes(string $bytes): string
    {
        $first48Bits = substr($bytes, 0, 6);
        $last48Bits = substr($bytes, -6);
        return substr_replace(substr_replace($bytes, $last48Bits, 0, 6), $first48Bits, -6);
    }
}