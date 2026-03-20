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
namespace Ramsey\Uuid\Builder;

use Ramsey\Collection\Abstract_Collection;
use Ramsey\Uuid\Converter\Number\Generic_Number_Converter;
use Ramsey\Uuid\Converter\Time\Generic_Time_Converter;
use Ramsey\Uuid\Converter\Time\Php_Time_Converter;
use Ramsey\Uuid\Guid\Guid_Builder;
use Ramsey\Uuid\Math\Brick_Math_Calculator;
use Ramsey\Uuid\Nonstandard\Uuid_Builder as NonstandardUuidBuilder;
use Ramsey\Uuid\Rfc4122\Uuid_Builder as Rfc4122UuidBuilder;
use Traversable;
/**
 * A collection of UuidBuilderInterface objects
 *
 * @deprecated this class has been deprecated and will be removed in 5.0.0. The use-case for this class comes from a
 *     pre-`phpstan/phpstan` and pre-`vimeo/psalm` ecosystem, in which type safety had to be mostly enforced at runtime:
 *     that is no longer necessary, now that you can safely verify your code to be correct, and use more generic types
 *     like `iterable<T>` instead.
 *
 * @extends AbstractCollection<UuidBuilderInterface>
 */
class Builder_Collection extends Abstract_Collection
{
    public function get_type(): string
    {
        return Uuid_Builder_Interface::class;
    }
    public function getIterator(): Traversable
    {
        return parent::getIterator();
    }
    /**
     * Re-constructs the object from its serialized form
     *
     * @param string $serialized The serialized PHP string to unserialize into a UuidInterface instance
     */
    public function unserialize($serialized): void
    {
        /** @var array<array-key, UuidBuilderInterface> $data */
        $data = unserialize($serialized, ['allowed_classes' => [Brick_Math_Calculator::class, Generic_Number_Converter::class, Generic_Time_Converter::class, Guid_Builder::class, Nonstandard_Uuid_Builder::class, Php_Time_Converter::class, Rfc4122uuid_Builder::class]]);
        $this->data = array_filter(
            $data,
            /** @phpstan-ignore instanceof.alwaysTrue */
            fn(\Ramsey\Uuid\Builder\Uuid_Builder_Interface $unserialized): bool => $unserialized instanceof Uuid_Builder_Interface
        );
    }
}