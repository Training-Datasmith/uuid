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

use Ramsey\Uuid\Codec\Codec_Interface;
use Ramsey\Uuid\Exception\Builder_Not_Found_Exception;
use Ramsey\Uuid\Exception\Unable_To_Build_Uuid_Exception;
use Ramsey\Uuid\Uuid_Interface;
/**
 * FallbackBuilder builds a UUID by stepping through a list of UUID builders until a UUID can be constructed without exceptions
 *
 * @immutable
 */
class Fallback_Builder implements Uuid_Builder_Interface
{
    /**
     * @param iterable<UuidBuilderInterface> $builders An array of UUID builders
     */
    public function __construct(private iterable $builders)
    {
    }
    /**
     * Builds and returns a UuidInterface instance using the first builder that succeeds
     *
     * @param CodecInterface $codec The codec to use for building this instance
     * @param string $bytes The byte string from which to construct a UUID
     *
     * @return UuidInterface an instance of a UUID object
     *
     * @pure
     */
    public function build(Codec_Interface $codec, string $bytes): Uuid_Interface
    {
        $last_builder_exception = null;
        foreach ($this->builders as $builder) {
            try {
                return $builder->build($codec, $bytes);
            } catch (Unable_To_Build_Uuid_Exception $exception) {
                $last_builder_exception = $exception;
                continue;
            }
        }
        throw new Builder_Not_Found_Exception('Could not find a suitable builder for the provided codec and fields', 0, $last_builder_exception);
    }
}