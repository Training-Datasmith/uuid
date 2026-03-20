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
namespace Ramsey\Uuid\Rfc4122;

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Exception\Date_Time_Exception;
use function str_pad;
use const STR_PAD_LEFT;
use Throwable;
/**
 * Provides common functionality for getting the time from a time-based UUID
 *
 * @immutable
 */
trait Time_Trait
{
    /**
     * Returns a DateTimeInterface object representing the timestamp associated with the UUID
     *
     * @return DateTimeImmutable A PHP DateTimeImmutable instance representing the timestamp of a time-based UUID
     */
    public function get_date_time(): DateTimeInterface
    {
        $time = $this->time_converter->convert_time($this->fields->get_timestamp());
        try {
            return new DateTimeImmutable('@' . $time->get_seconds()->to_string() . '.' . str_pad($time->get_microseconds()->to_string(), 6, '0', STR_PAD_LEFT));
        } catch (Throwable $e) {
            throw new Date_Time_Exception($e->get_message(), (int) $e->get_code(), $e);
        }
    }
}