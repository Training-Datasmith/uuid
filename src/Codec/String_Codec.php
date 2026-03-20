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
use function hex2bin;
use function implode;
use Ramsey\Uuid\Builder\Uuid_Builder_Interface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\Invalid_Uuid_String_Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Uuid_Interface;
use function sprintf;
use function str_replace;
use function strlen;
use function substr;
/**
 * StringCodec encodes and decodes RFC 9562 (formerly RFC 4122) UUIDs
 *
 * @immutable
 */
class String_Codec implements Codec_Interface
{
    /**
     * Constructs a StringCodec
     *
     * @param UuidBuilderInterface $builder The builder to use when encoding UUIDs
     */
    public function __construct(private Uuid_Builder_Interface $builder)
    {
    }
    public function encode(Uuid_Interface $uuid): string
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        $hex = bin2hex($uuid->get_fields()->get_bytes());
        /** @var non-empty-string */
        return sprintf('%08s-%04s-%04s-%04s-%012s', substr($hex, 0, 8), substr($hex, 8, 4), substr($hex, 12, 4), substr($hex, 16, 4), substr($hex, 20));
    }
    /**
     * @return non-empty-string
     */
    public function encode_binary(Uuid_Interface $uuid): string
    {
        /** @phpstan-ignore-next-line PHPStan complains that this is not a non-empty-string. */
        return $uuid->get_fields()->get_bytes();
    }
    /**
     * @throws InvalidUuidStringException
     *
     * @inheritDoc
     */
    public function decode(string $encoded_uuid): Uuid_Interface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        return $this->builder->build($this, $this->get_bytes($encoded_uuid));
    }
    public function decode_bytes(string $bytes): Uuid_Interface
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException('$bytes string should contain 16 characters.');
        }
        return $this->builder->build($this, $bytes);
    }
    /**
     * Returns the UUID builder
     */
    protected function get_builder(): Uuid_Builder_Interface
    {
        return $this->builder;
    }
    /**
     * Returns a byte string of the UUID
     */
    protected function get_bytes(string $encoded_uuid): string
    {
        $parsed_uuid = str_replace(['urn:', 'uuid:', 'URN:', 'UUID:', '{', '}', '-'], '', $encoded_uuid);
        $components = [substr($parsed_uuid, 0, 8), substr($parsed_uuid, 8, 4), substr($parsed_uuid, 12, 4), substr($parsed_uuid, 16, 4), substr($parsed_uuid, 20)];
        if (!Uuid::is_valid(implode('-', $components))) {
            throw new Invalid_Uuid_String_Exception('Invalid UUID string: ' . $encoded_uuid);
        }
        return (string) hex2bin($parsed_uuid);
    }
}