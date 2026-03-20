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

use Ramsey\Uuid\Exception\Node_Exception;
use Ramsey\Uuid\Provider\Node_Provider_Interface;
use Ramsey\Uuid\Type\Hexadecimal;
/**
 * FallbackNodeProvider retrieves the system node ID by stepping through a list of providers until a node ID can be obtained
 */
class Fallback_Node_Provider implements Node_Provider_Interface
{
    /**
     * @param iterable<NodeProviderInterface> $providers Array of node providers
     */
    public function __construct(private iterable $providers)
    {
    }
    public function get_node(): Hexadecimal
    {
        $last_provider_exception = null;
        foreach ($this->providers as $provider) {
            try {
                return $provider->get_node();
            } catch (Node_Exception $exception) {
                $last_provider_exception = $exception;
                continue;
            }
        }
        throw new Node_Exception(message: 'Unable to find a suitable node provider', previous: $last_provider_exception);
    }
}