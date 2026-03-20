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
use Ramsey\Uuid\Uuid as BaseUuid;
/**
 * Nonstandard\Uuid is a UUID that doesn't conform to RFC 9562 (formerly RFC 4122)
 *
 * @immutable
 * @pure
 */
final class Uuid extends Base_Uuid
{
    public function __construct(Fields $fields, Number_Converter_Interface $number_converter, Codec_Interface $codec, Time_Converter_Interface $time_converter)
    {
        parent::__construct($fields, $number_converter, $codec, $time_converter);
    }
}