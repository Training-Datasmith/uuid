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
namespace Ramsey\Uuid\Math;

use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Number_Interface;
/**
 * A calculator performs arithmetic operations on numbers
 *
 * @immutable
 */
interface Calculator_Interface
{
    /**
     * Returns the sum of all the provided parameters
     *
     * @param NumberInterface $augend The first addend (the integer being added to)
     * @param NumberInterface ...$addends The additional integers to a add to the augend
     *
     * @return NumberInterface The sum of all the parameters
     *
     * @pure
     */
    public function add(Number_Interface $augend, Number_Interface ...$addends): Number_Interface;
    /**
     * Returns the difference of all the provided parameters
     *
     * @param NumberInterface $minuend The integer being subtracted from
     * @param NumberInterface ...$subtrahends The integers to subtract from the minuend
     *
     * @return NumberInterface The difference after subtracting all parameters
     *
     * @pure
     */
    public function subtract(Number_Interface $minuend, Number_Interface ...$subtrahends): Number_Interface;
    /**
     * Returns the product of all the provided parameters
     *
     * @param NumberInterface $multiplicand The integer to be multiplied
     * @param NumberInterface ...$multipliers The factors by which to multiply the multiplicand
     *
     * @return NumberInterface The product of multiplying all the provided parameters
     *
     * @pure
     */
    public function multiply(Number_Interface $multiplicand, Number_Interface ...$multipliers): Number_Interface;
    /**
     * Returns the quotient of the provided parameters divided left-to-right
     *
     * @param int $roundingMode The RoundingMode constant to use for this operation
     * @param int $scale The scale to use for this operation
     * @param NumberInterface $dividend The integer to be divided
     * @param NumberInterface ...$divisors The integers to divide $dividend by, in the order in which the division
     *     operations should take place (left-to-right)
     *
     * @return NumberInterface The quotient of dividing the provided parameters left-to-right
     *
     * @pure
     */
    public function divide(int $rounding_mode, int $scale, Number_Interface $dividend, Number_Interface ...$divisors): Number_Interface;
    /**
     * Converts a value from an arbitrary base to a base-10 integer value
     *
     * @param string $value The value to convert
     * @param int $base The base to convert from (i.e., 2, 16, 32, etc.)
     *
     * @return IntegerObject The base-10 integer value of the converted value
     *
     * @pure
     */
    public function from_base(string $value, int $base): Integer_Object;
    /**
     * Converts a base-10 integer value to an arbitrary base
     *
     * @param IntegerObject $value The integer value to convert
     * @param int $base The base to convert to (i.e., 2, 16, 32, etc.)
     *
     * @return string The value represented in the specified base
     *
     * @pure
     */
    public function to_base(Integer_Object $value, int $base): string;
    /**
     * Converts an Integer instance to a Hexadecimal instance
     *
     * @pure
     */
    public function to_hexadecimal(Integer_Object $value): Hexadecimal;
    /**
     * Converts a Hexadecimal instance to an Integer instance
     *
     * @pure
     */
    public function to_integer(Hexadecimal $value): Integer_Object;
}