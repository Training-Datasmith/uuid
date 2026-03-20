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

use Ramsey\Uuid\Builder\Uuid_Builder_Interface;
use Ramsey\Uuid\Codec\Codec_Interface;
use Ramsey\Uuid\Converter\Number_Converter_Interface;
use Ramsey\Uuid\Converter\Time_Converter_Interface;
use Ramsey\Uuid\Exception\Unable_To_Build_Uuid_Exception;
use Ramsey\Uuid\Uuid_Interface;
use Throwable;
/**
 * Nonstandard\UuidBuilder builds instances of Nonstandard\Uuid
 *
 * @immutable
 */
class Uuid_Builder implements Uuid_Builder_Interface
{
    /**
     * @param NumberConverterInterface $numberConverter The number converter to use when constructing the Nonstandard\Uuid
     * @param TimeConverterInterface $timeConverter The time converter to use for converting timestamps extracted from a
     *     UUID to Unix timestamps
     */
    public function __construct(private Number_Converter_Interface $number_converter, private Time_Converter_Interface $time_converter)
    {
    }
    /**
     * Builds and returns a Nonstandard\Uuid
     *
     * @param CodecInterface $codec The codec to use for building this instance
     * @param string $bytes The byte string from which to construct a UUID
     *
     * @return Uuid The Nonstandard\UuidBuilder returns an instance of Nonstandard\Uuid
     *
     * @pure
     */
    public function build(Codec_Interface $codec, string $bytes): Uuid_Interface
    {
        try {
            /** @phpstan-ignore possiblyImpure.new */
            return new Uuid($this->build_fields($bytes), $this->number_converter, $codec, $this->time_converter);
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
    protected function build_fields(string $bytes): Fields
    {
        /** @phpstan-ignore possiblyImpure.new */
        return new Fields($bytes);
    }
}