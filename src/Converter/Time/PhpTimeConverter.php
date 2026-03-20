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

use function count;
use function dechex;
use function explode;
use function is_float;
use function is_int;
use Ramsey\Uuid\Converter\Time_Converter_Interface;
use Ramsey\Uuid\Math\Brick_Math_Calculator;
use Ramsey\Uuid\Math\Calculator_Interface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;
use function str_pad;
use const STR_PAD_LEFT;
use const STR_PAD_RIGHT;
use function strlen;
use function substr;
/**
 * PhpTimeConverter uses built-in PHP functions and standard math operations available to the PHP programming language
 * to provide facilities for converting parts of time into representations that may be used in UUIDs
 *
 * @immutable
 */
class Php_Time_Converter implements Time_Converter_Interface
{
    /**
     * The number of 100-nanosecond intervals from the Gregorian calendar epoch to the Unix epoch.
     */
    private const GREGORIAN_TO_UNIX_INTERVALS = 0x1b21dd213814000;
    /**
     * The number of 100-nanosecond intervals in one second.
     */
    private const SECOND_INTERVALS = 10000000;
    /**
     * The number of 100-nanosecond intervals in one microsecond.
     */
    private const MICROSECOND_INTERVALS = 10;
    private int $php_precision;
    private Calculator_Interface $calculator;
    private Time_Converter_Interface $fallback_converter;
    public function __construct(?Calculator_Interface $calculator = null, ?Time_Converter_Interface $fallback_converter = null)
    {
        if ($calculator === null) {
            $calculator = new Brick_Math_Calculator();
        }
        if ($fallback_converter === null) {
            $fallback_converter = new Generic_Time_Converter($calculator);
        }
        $this->calculator = $calculator;
        $this->fallback_converter = $fallback_converter;
        $this->php_precision = (int) ini_get('precision');
    }
    public function calculate_time(string $seconds, string $microseconds): Hexadecimal
    {
        $seconds = new Integer_Object($seconds);
        /** @phpstan-ignore possiblyImpure.new */
        $microseconds = new Integer_Object($microseconds);
        /** @phpstan-ignore possiblyImpure.new */
        // Calculate the count of 100-nanosecond intervals since the Gregorian calendar epoch
        // for the given seconds and microseconds.
        $uuid_time = (int) $seconds->to_string() * self::SECOND_INTERVALS + (int) $microseconds->to_string() * self::MICROSECOND_INTERVALS + self::GREGORIAN_TO_UNIX_INTERVALS;
        // Check to see whether we've overflowed the max/min integer size.
        // If so, we will default to a different time converter.
        // @phpstan-ignore function.alreadyNarrowedType (the integer value might have overflowed)
        if (!is_int($uuid_time)) {
            return $this->fallback_converter->calculate_time($seconds->to_string(), $microseconds->to_string());
        }
        /** @phpstan-ignore possiblyImpure.new */
        return new Hexadecimal(str_pad(dechex($uuid_time), 16, '0', STR_PAD_LEFT));
    }
    public function convert_time(Hexadecimal $uuid_timestamp): Time
    {
        $timestamp = $this->calculator->to_integer($uuid_timestamp);
        // Convert the 100-nanosecond intervals into seconds and microseconds.
        $split_time = $this->split_time(($timestamp->to_string() - self::GREGORIAN_TO_UNIX_INTERVALS) / self::SECOND_INTERVALS);
        if (count($split_time) === 0) {
            return $this->fallback_converter->convert_time($uuid_timestamp);
        }
        /** @phpstan-ignore possiblyImpure.new */
        return new Time($split_time['sec'], $split_time['usec']);
    }
    /**
     * @param float | int $time The time to split into seconds and microseconds
     *
     * @return string[]
     *
     * @pure
     */
    private function split_time(float|int $time): array
    {
        $split = explode('.', (string) $time, 2);
        // If the $time value is a float but $split only has 1 element, then the float math was rounded up to the next
        // second, so we want to return an empty array to allow use of the fallback converter.
        if (is_float($time) && count($split) === 1) {
            return [];
        }
        if (count($split) === 1) {
            return ['sec' => $split[0], 'usec' => '0'];
        }
        // If the microseconds are less than six characters AND the length of the number is greater than or equal to the
        // PHP precision, then it's possible that we lost some precision for the microseconds. Return an empty array so
        // that we can choose to use the fallback converter.
        if (strlen($split[1]) < 6 && strlen((string) $time) >= $this->php_precision) {
            return [];
        }
        $microseconds = $split[1];
        // Ensure the microseconds are no longer than 6 digits. If they are,
        // truncate the number to the first 6 digits and round up, if needed.
        if (strlen($microseconds) > 6) {
            $rounding_digit = (int) substr($microseconds, 6, 1);
            $microseconds = (int) substr($microseconds, 0, 6);
            if ($rounding_digit >= 5) {
                $microseconds++;
            }
        }
        return ['sec' => $split[0], 'usec' => str_pad((string) $microseconds, 6, '0', STR_PAD_RIGHT)];
    }
}