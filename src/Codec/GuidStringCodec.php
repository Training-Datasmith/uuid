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
namespace Ramsey\Uuid\Codec;

use function bin2hex;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Uuid_Interface;
use function sprintf;
use function substr;
/**
 * GuidStringCodec encodes and decodes globally unique identifiers (GUID)
 *
 * @see Guid
 *
 * @immutable
 */
class Guid_String_Codec extends String_Codec
{
    public function encode(Uuid_Interface $uuid): string
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        $hex = bin2hex($uuid->get_fields()->get_bytes());
        /** @var non-empty-string */
        return sprintf('%02s%02s%02s%02s-%02s%02s-%02s%02s-%04s-%012s', substr($hex, 6, 2), substr($hex, 4, 2), substr($hex, 2, 2), substr($hex, 0, 2), substr($hex, 10, 2), substr($hex, 8, 2), substr($hex, 14, 2), substr($hex, 12, 2), substr($hex, 16, 4), substr($hex, 20));
    }
    public function decode(string $encoded_uuid): Uuid_Interface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        $bytes = $this->get_bytes($encoded_uuid);
        /** @phpstan-ignore possiblyImpure.methodCall, possiblyImpure.methodCall */
        return $this->get_builder()->build($this, $this->swap_bytes($bytes));
    }
    public function decode_bytes(string $bytes): Uuid_Interface
    {
        // Call parent::decode() to preserve the correct byte order.
        return parent::decode(bin2hex($bytes));
    }
    /**
     * Swaps bytes according to the GUID rules
     */
    private function swap_bytes(string $bytes): string
    {
        return $bytes[3] . $bytes[2] . $bytes[1] . $bytes[0] . $bytes[5] . $bytes[4] . $bytes[7] . $bytes[6] . substr($bytes, 8);
    }
}