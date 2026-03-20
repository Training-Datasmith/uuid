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
namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Collection\Abstract_Collection;
use Ramsey\Uuid\Provider\Node_Provider_Interface;
use Ramsey\Uuid\Type\Hexadecimal;
/**
 * A collection of NodeProviderInterface objects
 *
 * @deprecated this class has been deprecated and will be removed in 5.0.0. The use-case for this class comes from a
 *     pre-`phpstan/phpstan` and pre-`vimeo/psalm` ecosystem, in which type safety had to be mostly enforced at runtime:
 *     that is no longer necessary, now that you can safely verify your code to be correct and use more generic types
 *     like `iterable<T>` instead.
 *
 * @extends AbstractCollection<NodeProviderInterface>
 */
class Node_Provider_Collection extends Abstract_Collection
{
    public function get_type(): string
    {
        return Node_Provider_Interface::class;
    }
    /**
     * Re-constructs the object from its serialized form
     *
     * @param string $serialized The serialized PHP string to unserialize into a UuidInterface instance
     */
    public function unserialize($serialized): void
    {
        /** @var array<array-key, NodeProviderInterface> $data */
        $data = unserialize($serialized, ['allowed_classes' => [Hexadecimal::class, Random_Node_Provider::class, Static_Node_Provider::class, System_Node_Provider::class]]);
        /** @phpstan-ignore-next-line */
        $this->data = array_filter($data, fn(\Ramsey\Uuid\Provider\Node_Provider_Interface $unserialized): bool => $unserialized instanceof Node_Provider_Interface);
    }
}