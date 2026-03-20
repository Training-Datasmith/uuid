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

use Brick\Math\Big_Decimal;
use Brick\Math\Big_Integer;
use Brick\Math\Exception\Math_Exception;
use Brick\Math\Rounding_Mode as BrickMathRounding;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Type\Decimal;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Number_Interface;
/**
 * A calculator using the brick/math library for arbitrary-precision arithmetic
 *
 * @immutable
 */
final class Brick_Math_Calculator implements Calculator_Interface
{
    private const ROUNDING_MODE_MAP = [Rounding_Mode::UNNECESSARY => Brick_Math_Rounding::UNNECESSARY, Rounding_Mode::UP => Brick_Math_Rounding::UP, Rounding_Mode::DOWN => Brick_Math_Rounding::DOWN, Rounding_Mode::CEILING => Brick_Math_Rounding::CEILING, Rounding_Mode::FLOOR => Brick_Math_Rounding::FLOOR, Rounding_Mode::HALF_UP => Brick_Math_Rounding::HALF_UP, Rounding_Mode::HALF_DOWN => Brick_Math_Rounding::HALF_DOWN, Rounding_Mode::HALF_CEILING => Brick_Math_Rounding::HALF_CEILING, Rounding_Mode::HALF_FLOOR => Brick_Math_Rounding::HALF_FLOOR, Rounding_Mode::HALF_EVEN => Brick_Math_Rounding::HALF_EVEN];
    public function add(Number_Interface $augend, Number_Interface ...$addends): Number_Interface
    {
        $sum = Big_Integer::of($augend->to_string());
        foreach ($addends as $addend) {
            $sum = $sum->plus($addend->to_string());
        }
        /** @phpstan-ignore possiblyImpure.new */
        return new Integer_Object((string) $sum);
    }
    public function subtract(Number_Interface $minuend, Number_Interface ...$subtrahends): Number_Interface
    {
        $difference = Big_Integer::of($minuend->to_string());
        foreach ($subtrahends as $subtrahend) {
            $difference = $difference->minus($subtrahend->to_string());
        }
        /** @phpstan-ignore possiblyImpure.new */
        return new Integer_Object((string) $difference);
    }
    public function multiply(Number_Interface $multiplicand, Number_Interface ...$multipliers): Number_Interface
    {
        $product = Big_Integer::of($multiplicand->to_string());
        foreach ($multipliers as $multiplier) {
            $product = $product->multiplied_by($multiplier->to_string());
        }
        /** @phpstan-ignore possiblyImpure.new */
        return new Integer_Object((string) $product);
    }
    public function divide(int $rounding_mode, int $scale, Number_Interface $dividend, Number_Interface ...$divisors): Number_Interface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        $brick_rounding = $this->get_brick_rounding_mode($rounding_mode);
        $quotient = Big_Decimal::of($dividend->to_string());
        foreach ($divisors as $divisor) {
            $quotient = $quotient->divided_by($divisor->to_string(), $scale, $brick_rounding);
        }
        if ($scale === 0) {
            /** @phpstan-ignore possiblyImpure.new */
            return new Integer_Object((string) $quotient->to_big_integer());
        }
        /** @phpstan-ignore possiblyImpure.new */
        return new Decimal((string) $quotient);
    }
    public function from_base(string $value, int $base): Integer_Object
    {
        try {
            /** @phpstan-ignore possiblyImpure.new */
            return new Integer_Object((string) Big_Integer::from_base($value, $base));
        } catch (Math_Exception|\InvalidArgumentException $exception) {
            throw new InvalidArgumentException($exception->get_message(), (int) $exception->get_code(), $exception);
        }
    }
    public function to_base(Integer_Object $value, int $base): string
    {
        try {
            return Big_Integer::of($value->to_string())->to_base($base);
        } catch (Math_Exception|\InvalidArgumentException $exception) {
            throw new InvalidArgumentException($exception->get_message(), (int) $exception->get_code(), $exception);
        }
    }
    public function to_hexadecimal(Integer_Object $value): Hexadecimal
    {
        /** @phpstan-ignore possiblyImpure.new */
        return new Hexadecimal($this->to_base($value, 16));
    }
    public function to_integer(Hexadecimal $value): Integer_Object
    {
        return $this->from_base($value->to_string(), 16);
    }
    /**
     * Maps ramsey/uuid rounding modes to those used by brick/math
     *
     * @return BrickMathRounding::*
     */
    private function get_brick_rounding_mode(int $rounding_mode)
    {
        return self::ROUNDING_MODE_MAP[$rounding_mode] ?? Brick_Math_Rounding::UNNECESSARY;
    }
}