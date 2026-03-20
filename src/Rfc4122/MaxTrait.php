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

/**
 * Provides common functionality for max UUIDs
 *
 * @immutable
 */
trait Max_Trait
{
    /**
     * Returns the bytes that comprise the fields
     *
     * @pure
     */
    abstract public function get_bytes(): string;
    /**
     * Returns true if the byte string represents a max UUID
     *
     * @pure
     */
    public function is_max(): bool
    {
        return $this->get_bytes() === "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff";
    }
}