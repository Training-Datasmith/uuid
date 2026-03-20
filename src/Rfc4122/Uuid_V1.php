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

use Ramsey\Uuid\Codec\Codec_Interface;
use Ramsey\Uuid\Converter\Number_Converter_Interface;
use Ramsey\Uuid\Converter\Time_Converter_Interface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\Fields_Interface as Rfc4122FieldsInterface;
use Ramsey\Uuid\Uuid;
/**
 * Gregorian time, or version 1, UUIDs include timestamp, clock sequence, and node values, combined into a 128-bit unsigned integer
 *
 * @link https://www.rfc-editor.org/rfc/rfc9562#section-5.1 RFC 9562, 5.1. UUID Version 1
 *
 * @immutable
 */
final class Uuid_V1 extends Uuid implements Uuid_Interface
{
    use Time_Trait;
    /**
     * Creates a version 1 (Gregorian time) UUID
     *
     * @param Rfc4122FieldsInterface $fields The fields from which to construct a UUID
     * @param NumberConverterInterface $numberConverter The number converter to use for converting hex values to/from integers
     * @param CodecInterface $codec The codec to use when encoding or decoding UUID strings
     * @param TimeConverterInterface $timeConverter The time converter to use for converting timestamps extracted from a
     *     UUID to unix timestamps
     */
    public function __construct(Rfc4122fields_Interface $fields, Number_Converter_Interface $number_converter, Codec_Interface $codec, Time_Converter_Interface $time_converter)
    {
        if ($fields->get_version() !== Uuid::UUID_TYPE_TIME) {
            throw new InvalidArgumentException('Fields used to create a UuidV1 must represent a version 1 (time-based) UUID');
        }
        parent::__construct($fields, $number_converter, $codec, $time_converter);
    }
}