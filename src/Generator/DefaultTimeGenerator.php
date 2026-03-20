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

use function dechex;
use function hex2bin;
use function is_int;
use function pack;
use function preg_match;
use Ramsey\Uuid\Converter\Time_Converter_Interface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\Random_Source_Exception;
use Ramsey\Uuid\Exception\Time_Source_Exception;
use Ramsey\Uuid\Provider\Node_Provider_Interface;
use Ramsey\Uuid\Provider\Time_Provider_Interface;
use Ramsey\Uuid\Type\Hexadecimal;
use function sprintf;
use function str_pad;
use const STR_PAD_LEFT;
use function strlen;
use Throwable;
/**
 * DefaultTimeGenerator generates strings of binary data based on a node ID, clock sequence, and the current time
 */
class Default_Time_Generator implements Time_Generator_Interface
{
    public function __construct(private Node_Provider_Interface $node_provider, private Time_Converter_Interface $time_converter, private Time_Provider_Interface $time_provider)
    {
    }
    /**
     * @throws InvalidArgumentException if the parameters contain invalid values
     * @throws RandomSourceException if random_int() throws an exception/error
     *
     * @inheritDoc
     */
    public function generate($node = null, ?int $clock_seq = null): string
    {
        if ($node instanceof Hexadecimal) {
            $node = $node->to_string();
        }
        $node = $this->get_valid_node($node);
        if ($clock_seq === null) {
            try {
                // This does not use "stable storage"; see RFC 9562, section 6.3.
                $clock_seq = random_int(0, 0x3fff);
            } catch (Throwable $exception) {
                throw new Random_Source_Exception($exception->get_message(), (int) $exception->get_code(), $exception);
            }
        }
        $time = $this->time_provider->get_time();
        $uuid_time = $this->time_converter->calculate_time($time->get_seconds()->to_string(), $time->get_microseconds()->to_string());
        $time_hex = str_pad($uuid_time->to_string(), 16, '0', STR_PAD_LEFT);
        if (strlen($time_hex) !== 16) {
            throw new Time_Source_Exception(sprintf('The generated time of \'%s\' is larger than expected', $time_hex));
        }
        $time_bytes = (string) hex2bin($time_hex);
        return $time_bytes[4] . $time_bytes[5] . $time_bytes[6] . $time_bytes[7] . $time_bytes[2] . $time_bytes[3] . $time_bytes[0] . $time_bytes[1] . pack('n*', $clock_seq) . $node;
    }
    /**
     * Uses the node provider given when constructing this instance to get the node ID (usually a MAC address)
     *
     * @param int | string | null $node A node value that may be used to override the node provider
     *
     * @return string 6-byte binary string representation of the node
     *
     * @throws InvalidArgumentException
     */
    private function get_valid_node(int|string|null $node): string
    {
        if ($node === null) {
            $node = $this->node_provider->get_node();
        }
        // Convert the node to hex if it is still an integer.
        if (is_int($node)) {
            $node = dechex($node);
        }
        if (!preg_match('/^[A-Fa-f0-9]+$/', (string) $node) || strlen((string) $node) > 12) {
            throw new InvalidArgumentException('Invalid node value');
        }
        return (string) hex2bin(str_pad((string) $node, 12, '0', STR_PAD_LEFT));
    }
}