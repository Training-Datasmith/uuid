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

use Ramsey\Uuid\Builder\Uuid_Builder_Interface;
use Ramsey\Uuid\Codec\Codec_Interface;
use Ramsey\Uuid\Converter\Number_Converter_Interface;
use Ramsey\Uuid\Converter\Time\Unix_Time_Converter;
use Ramsey\Uuid\Converter\Time_Converter_Interface;
use Ramsey\Uuid\Exception\Unable_To_Build_Uuid_Exception;
use Ramsey\Uuid\Exception\Unsupported_Operation_Exception;
use Ramsey\Uuid\Math\Brick_Math_Calculator;
use Ramsey\Uuid\Rfc4122\Uuid_Interface as Rfc4122UuidInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Uuid_Interface;
use Throwable;
/**
 * UuidBuilder builds instances of RFC 9562 (formerly 4122) UUIDs
 *
 * @immutable
 */
class Uuid_Builder implements Uuid_Builder_Interface
{
    private Time_Converter_Interface $unix_time_converter;
    /**
     * Constructs the DefaultUuidBuilder
     *
     * @param NumberConverterInterface $numberConverter The number converter to use when constructing the Uuid
     * @param TimeConverterInterface $timeConverter The time converter to use for converting Gregorian time extracted
     *     from version 1, 2, and 6 UUIDs to Unix timestamps
     * @param TimeConverterInterface | null $unixTimeConverter The time converter to use for converter Unix Epoch time
     *     extracted from version 7 UUIDs to Unix timestamps
     */
    public function __construct(private Number_Converter_Interface $number_converter, private Time_Converter_Interface $time_converter, ?Time_Converter_Interface $unix_time_converter = null)
    {
        $this->unix_time_converter = $unix_time_converter ?? new Unix_Time_Converter(new Brick_Math_Calculator());
    }
    /**
     * Builds and returns a Uuid
     *
     * @param CodecInterface $codec The codec to use for building this Uuid instance
     * @param string $bytes The byte string from which to construct a UUID
     *
     * @return Rfc4122UuidInterface UuidBuilder returns instances of Rfc4122UuidInterface
     *
     * @pure
     */
    public function build(Codec_Interface $codec, string $bytes): Uuid_Interface
    {
        try {
            /** @var Fields $fields */
            $fields = $this->build_fields($bytes);
            if ($fields->is_nil()) {
                /** @phpstan-ignore possiblyImpure.new */
                return new Nil_Uuid($fields, $this->number_converter, $codec, $this->time_converter);
            }
            if ($fields->is_max()) {
                /** @phpstan-ignore possiblyImpure.new */
                return new Max_Uuid($fields, $this->number_converter, $codec, $this->time_converter);
            }
            return match ($fields->get_version()) {
                /** @phpstan-ignore possiblyImpure.new */
                Uuid::UUID_TYPE_TIME => new Uuid_V1($fields, $this->number_converter, $codec, $this->time_converter),
                Uuid::UUID_TYPE_DCE_SECURITY => new Uuid_V2($fields, $this->number_converter, $codec, $this->time_converter),
                /** @phpstan-ignore possiblyImpure.new */
                Uuid::UUID_TYPE_HASH_MD5 => new Uuid_V3($fields, $this->number_converter, $codec, $this->time_converter),
                /** @phpstan-ignore possiblyImpure.new */
                Uuid::UUID_TYPE_RANDOM => new Uuid_V4($fields, $this->number_converter, $codec, $this->time_converter),
                /** @phpstan-ignore possiblyImpure.new */
                Uuid::UUID_TYPE_HASH_SHA1 => new Uuid_V5($fields, $this->number_converter, $codec, $this->time_converter),
                Uuid::UUID_TYPE_REORDERED_TIME => new Uuid_V6($fields, $this->number_converter, $codec, $this->time_converter),
                Uuid::UUID_TYPE_UNIX_TIME => new Uuid_V7($fields, $this->number_converter, $codec, $this->unix_time_converter),
                /** @phpstan-ignore possiblyImpure.new */
                Uuid::UUID_TYPE_CUSTOM => new Uuid_V8($fields, $this->number_converter, $codec, $this->time_converter),
                default => throw new Unsupported_Operation_Exception('The UUID version in the given fields is not supported by this UUID builder'),
            };
        } catch (Throwable $e) {
            /** @phpstan-ignore possiblyImpure.methodCall, possiblyImpure.methodCall */
            throw new Unable_To_Build_Uuid_Exception($e->get_message(), (int) $e->get_code(), $e);
        }
    }
    /**
     * Proxy method to allow injecting a mock for testing
     *
     * @pure
     */
    protected function build_fields(string $bytes): Fields_Interface
    {
        /** @phpstan-ignore possiblyImpure.new */
        return new Fields($bytes);
    }
}