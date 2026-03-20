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
 * UnixTimeConverter converts Unix Epoch timestamps to/from hexadecimal values consisting of milliseconds elapsed since
 * the Unix Epoch
 *
 * @immutable
 */
class Unix_Time_Converter implements Time_Converter_Interface
{
    private const MILLISECONDS = 1000;
    public function __construct(private Calculator_Interface $calculator)
    {
    }
    public function calculate_time(string $seconds, string $microseconds): Hexadecimal
    {
        /** @phpstan-ignore possiblyImpure.new */
        $timestamp = new Time($seconds, $microseconds);
        // Convert the seconds into milliseconds.
        $sec = $this->calculator->multiply($timestamp->get_seconds(), new Integer_Object(self::MILLISECONDS));
        // Convert the microseconds into milliseconds; the scale is zero because we need to discard the fractional part.
        $usec = $this->calculator->divide(
            Rounding_Mode::DOWN,
            // Always round down to stay in the previous millisecond.
            0,
            $timestamp->get_microseconds(),
            new Integer_Object(self::MILLISECONDS)
        );
        /** @var IntegerObject $unixTime */
        $unix_time = $this->calculator->add($sec, $usec);
        /** @phpstan-ignore possiblyImpure.new */
        return new Hexadecimal(str_pad($this->calculator->to_hexadecimal($unix_time)->to_string(), 12, '0', STR_PAD_LEFT));
    }
    public function convert_time(Hexadecimal $uuid_timestamp): Time
    {
        $milliseconds = $this->calculator->to_integer($uuid_timestamp);
        $unix_timestamp = $this->calculator->divide(Rounding_Mode::HALF_UP, 6, $milliseconds, new Integer_Object(self::MILLISECONDS));
        $split = explode('.', (string) $unix_timestamp, 2);
        /** @phpstan-ignore possiblyImpure.new */
        return new Time($split[0], $split[1] ?? '0');
    }
}