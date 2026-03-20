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

use function bin2hex;
use function dechex;
use function hex2bin;
use function hexdec;
use Ramsey\Uuid\Exception\Random_Source_Exception;
use Ramsey\Uuid\Provider\Node_Provider_Interface;
use Ramsey\Uuid\Type\Hexadecimal;
use function str_pad;
use const STR_PAD_LEFT;
use function substr;
use Throwable;
/**
 * RandomNodeProvider generates a random node ID
 *
 * @link https://www.rfc-editor.org/rfc/rfc9562#section-6.10 RFC 9562, 6.10. UUIDs That Do Not Identify the Host
 */
class Random_Node_Provider implements Node_Provider_Interface
{
    public function get_node(): Hexadecimal
    {
        try {
            $node_bytes = random_bytes(6);
        } catch (Throwable $exception) {
            throw new Random_Source_Exception($exception->get_message(), (int) $exception->get_code(), $exception);
        }
        // Split the node bytes for math on 32-bit systems.
        $node_msb = substr($node_bytes, 0, 3);
        $node_lsb = substr($node_bytes, 3);
        // Set the multicast bit; see RFC 9562, section 6.10.
        $node_msb = hex2bin(str_pad(dechex(hexdec(bin2hex($node_msb)) | 0x10000), 6, '0', STR_PAD_LEFT));
        return new Hexadecimal(str_pad(bin2hex($node_msb . $node_lsb), 12, '0', STR_PAD_LEFT));
    }
}