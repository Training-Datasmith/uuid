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
namespace Ramsey\Uuid\Generator;

use function hash;
use Ramsey\Uuid\Exception\Name_Exception;
use Ramsey\Uuid\Uuid_Interface;
use Value_Error;
/**
 * DefaultNameGenerator generates strings of binary data based on a namespace, name, and hashing algorithm
 */
class Default_Name_Generator implements Name_Generator_Interface
{
    /**
     * @pure
     */
    public function generate(Uuid_Interface $ns, string $name, string $hash_algorithm): string
    {
        try {
            return hash($hash_algorithm, $ns->get_bytes() . $name, true);
        } catch (Value_Error $e) {
            throw new Name_Exception(message: sprintf('Unable to hash namespace and name with algorithm \'%s\'', $hash_algorithm), previous: $e);
        }
    }
}