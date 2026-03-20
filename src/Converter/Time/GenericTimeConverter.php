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

use function explode;
use Ramsey\Uuid\Converter\Time_Converter_Interface;
use Ramsey\Uuid\Math\Calculator_Interface;
use Ramsey\Uuid\Math\Rounding_Mode;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;
use function str_pad;
use const STR_PAD_LEFT;
/**
 * GenericTimeConverter uses the provided calculator to calculate and convert time values
 *
 * @immutable
 */
class Generic_Time_Converter implements Time_Converter_Interface
{
    /**
     * The number of 100-nanosecond intervals from the Gregorian calendar epoch to the Unix epoch.
     */
    private const GREGORIAN_TO_UNIX_INTERVALS = '122192928000000000';
    /**
     * The number of 100-nanosecond intervals in one second.
     */
    private const SECOND_INTERVALS = '10000000';
    /**
     * The number of 100-nanosecond intervals in one microsecond.
     */
    private const MICROSECOND_INTERVALS = '10';
    public function __construct(private Calculator_Interface $calculator)
    {
    }
    public function calculate_time(string $seconds, string $microseconds): Hexadecimal
    {
        /** @phpstan-ignore possiblyImpure.new */
        $timestamp = new Time($seconds, $microseconds);
        // Convert the seconds into a count of 100-nanosecond intervals.
        $sec = $this->calculator->multiply($timestamp->get_seconds(), new Integer_Object(self::SECOND_INTERVALS));
        // Convert the microseconds into a count of 100-nanosecond intervals.
        $usec = $this->calculator->multiply($timestamp->get_microseconds(), new Integer_Object(self::MICROSECOND_INTERVALS));
        /**
         * Combine the intervals of seconds and microseconds and add the count of 100-nanosecond intervals from the
         * Gregorian calendar epoch to the Unix epoch. This gives us the correct count of 100-nanosecond intervals since
         * the Gregorian calendar epoch for the given seconds and microseconds.
         *
         * @var IntegerObject $uuidTime
         * @phpstan-ignore possiblyImpure.new
         */
        $uuid_time = $this->calculator->add($sec, $usec, new Integer_Object(self::GREGORIAN_TO_UNIX_INTERVALS));
        /**
         * PHPStan considers CalculatorInterface::toHexadecimal, Hexadecimal:toString impure.
         *
         * @phpstan-ignore possiblyImpure.new
         */
        return new Hexadecimal(str_pad($this->calculator->to_hexadecimal($uuid_time)->to_string(), 16, '0', STR_PAD_LEFT));
    }
    public function convert_time(Hexadecimal $uuid_timestamp): Time
    {
        // From the total, subtract the number of 100-nanosecond intervals from the Gregorian calendar epoch to the Unix
        // epoch. This gives us the number of 100-nanosecond intervals from the Unix epoch, which also includes the microtime.
        $epoch_nanoseconds = $this->calculator->subtract($this->calculator->to_integer($uuid_timestamp), new Integer_Object(self::GREGORIAN_TO_UNIX_INTERVALS));
        // Convert the 100-nanosecond intervals into seconds and microseconds.
        $unix_timestamp = $this->calculator->divide(Rounding_Mode::HALF_UP, 6, $epoch_nanoseconds, new Integer_Object(self::SECOND_INTERVALS));
        $split = explode('.', (string) $unix_timestamp, 2);
        /** @phpstan-ignore possiblyImpure.new */
        return new Time($split[0], $split[1] ?? 0);
    }
}