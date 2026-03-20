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
namespace Ramsey\Uuid\Builder;

use Ramsey\Uuid\Codec\Codec_Interface;
use Ramsey\Uuid\Converter\Number_Converter_Interface;
use Ramsey\Uuid\Converter\Time\Degraded_Time_Converter;
use Ramsey\Uuid\Converter\Time_Converter_Interface;
use Ramsey\Uuid\Degraded_Uuid;
use Ramsey\Uuid\Rfc4122\Fields as Rfc4122Fields;
use Ramsey\Uuid\Uuid_Interface;
/**
 * @deprecated DegradedUuid instances are no longer necessary to support 32-bit systems. Please transition to {@see DefaultUuidBuilder}.
 *
 * @immutable
 */
class Degraded_Uuid_Builder implements Uuid_Builder_Interface
{
    private Time_Converter_Interface $time_converter;
    /**
     * @param NumberConverterInterface $numberConverter The number converter to use when constructing the DegradedUuid
     * @param TimeConverterInterface|null $timeConverter The time converter to use for converting timestamps extracted
     *     from a UUID to Unix timestamps
     */
    public function __construct(private Number_Converter_Interface $number_converter, ?Time_Converter_Interface $time_converter = null)
    {
        $this->time_converter = $time_converter ?: new Degraded_Time_Converter();
    }
    /**
     * Builds and returns a DegradedUuid
     *
     * @param CodecInterface $codec The codec to use for building this DegradedUuid instance
     * @param string $bytes The byte string from which to construct a UUID
     *
     * @return DegradedUuid The DegradedUuidBuild returns an instance of Ramsey\Uuid\DegradedUuid
     *
     * @phpstan-impure
     */
    public function build(Codec_Interface $codec, string $bytes): Uuid_Interface
    {
        return new Degraded_Uuid(new Rfc4122Fields($bytes), $this->number_converter, $codec, $this->time_converter);
    }
}