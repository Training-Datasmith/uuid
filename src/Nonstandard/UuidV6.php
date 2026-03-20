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

use Ramsey\Uuid\Codec\Codec_Interface;
use Ramsey\Uuid\Converter\Number_Converter_Interface;
use Ramsey\Uuid\Converter\Time_Converter_Interface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Lazy\Lazy_Uuid_From_String;
use Ramsey\Uuid\Rfc4122\Fields_Interface as Rfc4122FieldsInterface;
use Ramsey\Uuid\Rfc4122\Time_Trait;
use Ramsey\Uuid\Rfc4122\Uuid_Interface;
use Ramsey\Uuid\Rfc4122\Uuid_V1;
use Ramsey\Uuid\Uuid as BaseUuid;
/**
 * Reordered time, or version 6, UUIDs include timestamp, clock sequence, and node values that are combined into a
 * 128-bit unsigned integer
 *
 * @deprecated Use {@see \Ramsey\Uuid\Rfc4122\UuidV6} instead.
 *
 * @link https://github.com/uuid6/uuid6-ietf-draft UUID version 6 IETF draft
 * @link http://gh.peabody.io/uuidv6/ "Version 6" UUIDs
 * @link https://www.rfc-editor.org/rfc/rfc9562#section-5.6 RFC 9562, 5.6. UUID Version 6
 *
 * @immutable
 */
class Uuid_V6 extends Base_Uuid implements Uuid_Interface
{
    use Time_Trait;
    /**
     * Creates a version 6 (reordered Gregorian time) UUID
     *
     * @param Rfc4122FieldsInterface $fields The fields from which to construct a UUID
     * @param NumberConverterInterface $numberConverter The number converter to use for converting hex values to/from integers
     * @param CodecInterface $codec The codec to use when encoding or decoding UUID strings
     * @param TimeConverterInterface $timeConverter The time converter to use for converting timestamps extracted from a
     *     UUID to unix timestamps
     */
    public function __construct(Rfc4122fields_Interface $fields, Number_Converter_Interface $number_converter, Codec_Interface $codec, Time_Converter_Interface $time_converter)
    {
        if ($fields->get_version() !== Base_Uuid::UUID_TYPE_REORDERED_TIME) {
            throw new InvalidArgumentException('Fields used to create a UuidV6 must represent a version 6 (reordered time) UUID');
        }
        parent::__construct($fields, $number_converter, $codec, $time_converter);
    }
    /**
     * Converts this UUID into an instance of a version 1 UUID
     */
    public function to_uuid_v1(): Uuid_V1
    {
        $hex = $this->get_hex()->to_string();
        $hex = substr($hex, 7, 5) . substr($hex, 13, 3) . substr($hex, 3, 4) . '1' . substr($hex, 0, 3) . substr($hex, 16);
        /** @var LazyUuidFromString $uuid */
        $uuid = Base_Uuid::from_bytes((string) hex2bin($hex));
        return $uuid->to_uuid_v1();
    }
    /**
     * Converts a version 1 UUID into an instance of a version 6 UUID
     */
    public static function from_uuid_v1(Uuid_V1 $uuid_v1): \Ramsey\Uuid\Rfc4122\Uuid_V6
    {
        $hex = $uuid_v1->get_hex()->to_string();
        $hex = substr($hex, 13, 3) . substr($hex, 8, 4) . substr($hex, 0, 5) . '6' . substr($hex, 5, 3) . substr($hex, 16);
        /** @var LazyUuidFromString $uuid */
        $uuid = Base_Uuid::from_bytes((string) hex2bin($hex));
        return $uuid->to_uuid_v6();
    }
}