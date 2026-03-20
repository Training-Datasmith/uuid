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

use function hex2bin;
use function in_array;
use function pack;
use Ramsey\Uuid\Converter\Number_Converter_Interface;
use Ramsey\Uuid\Exception\Dce_Security_Exception;
use Ramsey\Uuid\Provider\Dce_Security_Provider_Interface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Uuid;
use function str_pad;
use const STR_PAD_LEFT;
use function strlen;
use function substr_replace;
/**
 * DceSecurityGenerator generates strings of binary data based on a local domain, local identifier, node ID, clock
 * sequence, and the current time
 */
class Dce_Security_Generator implements Dce_Security_Generator_Interface
{
    private const DOMAINS = [Uuid::DCE_DOMAIN_PERSON, Uuid::DCE_DOMAIN_GROUP, Uuid::DCE_DOMAIN_ORG];
    /**
     * Upper bounds for the clock sequence in DCE Security UUIDs.
     */
    private const CLOCK_SEQ_HIGH = 63;
    /**
     * Lower bounds for the clock sequence in DCE Security UUIDs.
     */
    private const CLOCK_SEQ_LOW = 0;
    public function __construct(private Number_Converter_Interface $number_converter, private Time_Generator_Interface $time_generator, private Dce_Security_Provider_Interface $dce_security_provider)
    {
    }
    public function generate(int $local_domain, ?Integer_Object $local_identifier = null, ?Hexadecimal $node = null, ?int $clock_seq = null): string
    {
        if (!in_array($local_domain, self::DOMAINS)) {
            throw new Dce_Security_Exception('Local domain must be a valid DCE Security domain');
        }
        if ($local_identifier && $local_identifier->is_negative()) {
            throw new Dce_Security_Exception('Local identifier out of bounds; it must be a value between 0 and 4294967295');
        }
        if ($clock_seq > self::CLOCK_SEQ_HIGH || $clock_seq < self::CLOCK_SEQ_LOW) {
            throw new Dce_Security_Exception('Clock sequence out of bounds; it must be a value between 0 and 63');
        }
        switch ($local_domain) {
            case Uuid::DCE_DOMAIN_ORG:
                if ($local_identifier === null) {
                    throw new Dce_Security_Exception('A local identifier must be provided for the org domain');
                }
                break;
            case Uuid::DCE_DOMAIN_PERSON:
                if ($local_identifier === null) {
                    $local_identifier = $this->dce_security_provider->get_uid();
                }
                break;
            case Uuid::DCE_DOMAIN_GROUP:
            default:
                if ($local_identifier === null) {
                    $local_identifier = $this->dce_security_provider->get_gid();
                }
                break;
        }
        $identifier_hex = $this->number_converter->to_hex($local_identifier->to_string());
        // The maximum value for the local identifier is 0xffffffff, or 4,294,967,295. This is 8 hexadecimal digits, so
        // if the length of hexadecimal digits is greater than 8, we know the value is greater than 0xffffffff.
        if (strlen($identifier_hex) > 8) {
            throw new Dce_Security_Exception('Local identifier out of bounds; it must be a value between 0 and 4294967295');
        }
        $domain_byte = pack('n', $local_domain)[1];
        $identifier_bytes = (string) hex2bin(str_pad($identifier_hex, 8, '0', STR_PAD_LEFT));
        if ($node instanceof Hexadecimal) {
            $node = $node->to_string();
        }
        // Shift the clock sequence 8 bits to the left, so it matches 0x3f00.
        if ($clock_seq !== null) {
            $clock_seq = $clock_seq << 8;
        }
        $bytes = $this->time_generator->generate($node, $clock_seq);
        // Replace bytes in the time-based UUID with DCE Security values.
        $bytes = substr_replace($bytes, $identifier_bytes, 0, 4);
        return substr_replace($bytes, $domain_byte, 9, 1);
    }
}