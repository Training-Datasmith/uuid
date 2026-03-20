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
namespace Ramsey\Uuid\Provider\Time;

use Ramsey\Uuid\Provider\Time_Provider_Interface;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;
/**
 * FixedTimeProvider uses a known time to provide the time
 *
 * This provider allows the use of a previously generated, or known, time when generating time-based UUIDs.
 */
class Fixed_Time_Provider implements Time_Provider_Interface
{
    public function __construct(private Time $time)
    {
    }
    /**
     * Sets the `usec` component of the time
     *
     * @param IntegerObject | int | string $value The `usec` value to set
     */
    public function set_usec($value): void
    {
        $this->time = new Time($this->time->get_seconds(), $value);
    }
    /**
     * Sets the `sec` component of the time
     *
     * @param IntegerObject | int | string $value The `sec` value to set
     */
    public function set_sec($value): void
    {
        $this->time = new Time($value, $this->time->get_microseconds());
    }
    public function get_time(): Time
    {
        return $this->time;
    }
}