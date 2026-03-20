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
namespace Ramsey\Uuid\Converter\Time;

use Ramsey\Uuid\Converter\Time_Converter_Interface;
use Ramsey\Uuid\Math\Brick_Math_Calculator;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Time;
/**
 * Previously used to integrate moontoast/math as a bignum arithmetic library, BigNumberTimeConverter is deprecated in
 * favor of GenericTimeConverter
 *
 * @deprecated Please transition to {@see GenericTimeConverter}.
 *
 * @immutable
 */
class Big_Number_Time_Converter implements Time_Converter_Interface
{
    private Time_Converter_Interface $converter;
    public function __construct()
    {
        $this->converter = new Generic_Time_Converter(new Brick_Math_Calculator());
    }
    public function calculate_time(string $seconds, string $microseconds): Hexadecimal
    {
        return $this->converter->calculate_time($seconds, $microseconds);
    }
    public function convert_time(Hexadecimal $uuid_timestamp): Time
    {
        return $this->converter->convert_time($uuid_timestamp);
    }
}