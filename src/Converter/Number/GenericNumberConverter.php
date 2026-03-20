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
use Ramsey\Uuid\Math\Calculator_Interface;
use Ramsey\Uuid\Type\Integer as IntegerObject;
/**
 * GenericNumberConverter uses the provided calculator to convert decimal numbers to and from hexadecimal values
 *
 * @immutable
 */
class Generic_Number_Converter implements Number_Converter_Interface
{
    public function __construct(private Calculator_Interface $calculator)
    {
    }
    /**
     * @pure
     */
    public function from_hex(string $hex): string
    {
        return $this->calculator->from_base($hex, 16)->to_string();
    }
    /**
     * @pure
     */
    public function to_hex(string $number): string
    {
        /** @phpstan-ignore return.type, possiblyImpure.new */
        return $this->calculator->to_base(new Integer_Object($number), 16);
    }
}