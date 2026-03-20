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

use function dechex;
use function hexdec;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Provider\Node_Provider_Interface;
use Ramsey\Uuid\Type\Hexadecimal;
use function str_pad;
use const STR_PAD_LEFT;
use function substr;
/**
 * StaticNodeProvider provides a static node value with the multicast bit set
 *
 * @link https://www.rfc-editor.org/rfc/rfc9562#section-6.10 RFC 9562, 6.10. UUIDs That Do Not Identify the Host
 */
class Static_Node_Provider implements Node_Provider_Interface
{
    private Hexadecimal $node;
    /**
     * @param Hexadecimal $node The static node value to use
     */
    public function __construct(Hexadecimal $node)
    {
        if (strlen($node->to_string()) > 12) {
            throw new InvalidArgumentException('Static node value cannot be greater than 12 hexadecimal characters');
        }
        $this->node = $this->set_multicast_bit($node);
    }
    public function get_node(): Hexadecimal
    {
        return $this->node;
    }
    /**
     * Set the multicast bit for the static node value
     */
    private function set_multicast_bit(Hexadecimal $node): Hexadecimal
    {
        $node_hex = str_pad($node->to_string(), 12, '0', STR_PAD_LEFT);
        $first_octet = substr($node_hex, 0, 2);
        $first_octet = str_pad(dechex(hexdec($first_octet) | 0x1), 2, '0', STR_PAD_LEFT);
        return new Hexadecimal($first_octet . substr($node_hex, 2));
    }
}