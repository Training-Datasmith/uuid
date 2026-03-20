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

use Ramsey\Uuid\Exception\Name_Exception;
use Ramsey\Uuid\Uuid_Interface;
use function sprintf;
use function uuid_generate_md5;
use function uuid_generate_sha1;
use function uuid_parse;
/**
 * PeclUuidNameGenerator generates strings of binary data from a namespace and a name, using ext-uuid
 *
 * @link https://pecl.php.net/package/uuid ext-uuid
 */
class Pecl_Uuid_Name_Generator implements Name_Generator_Interface
{
    /**
     * @pure
     */
    public function generate(Uuid_Interface $ns, string $name, string $hash_algorithm): string
    {
        $uuid = match ($hash_algorithm) {
            'md5' => uuid_generate_md5($ns->to_string(), $name),
            /** @phpstan-ignore possiblyImpure.functionCall */
            'sha1' => uuid_generate_sha1($ns->to_string(), $name),
            /** @phpstan-ignore possiblyImpure.functionCall */
            default => throw new Name_Exception(sprintf('Unable to hash namespace and name with algorithm \'%s\'', $hash_algorithm)),
        };
        /** @phpstan-ignore possiblyImpure.functionCall */
        return (string) uuid_parse($uuid);
    }
}