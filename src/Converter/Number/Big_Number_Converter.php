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
namespace Ramsey\Uuid\Converter\Number;

use Ramsey\Uuid\Converter\Number_Converter_Interface;
use Ramsey\Uuid\Math\Brick_Math_Calculator;
/**
 * Previously used to integrate moontoast/math as a bignum arithmetic library, BigNumberConverter is deprecated in favor
 * of GenericNumberConverter
 *
 * @deprecated Please transition to {@see GenericNumberConverter}.
 *
 * @immutable
 */
class Big_Number_Converter implements Number_Converter_Interface
{
    private Number_Converter_Interface $converter;
    public function __construct()
    {
        $this->converter = new Generic_Number_Converter(new Brick_Math_Calculator());
    }
    /**
     * @pure
     */
    public function from_hex(string $hex): string
    {
        return $this->converter->from_hex($hex);
    }
    /**
     * @pure
     */
    public function to_hex(string $number): string
    {
        return $this->converter->to_hex($number);
    }
}