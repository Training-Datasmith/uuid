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
namespace Ramsey\Uuid\Lazy;

use function assert;
use function bin2hex;
use DateTimeInterface;
use function hex2bin;
use Ramsey\Uuid\Converter\Number_Converter_Interface;
use Ramsey\Uuid\Exception\Unsupported_Operation_Exception;
use Ramsey\Uuid\Fields\Fields_Interface;
use Ramsey\Uuid\Rfc4122\Uuid_V1;
use Ramsey\Uuid\Rfc4122\Uuid_V6;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Uuid_Factory;
use Ramsey\Uuid\Uuid_Interface;
use function sprintf;
use function str_replace;
use function substr;
use Value_Error;
/**
 * Lazy version of a UUID: its format has not been determined yet, so it is mostly only usable for string/bytes
 * conversion. This object optimizes instantiation, serialization and string conversion time, at the cost of increased
 * overhead for more advanced UUID operations.
 *
 * > [!NOTE]
 * > The {@see FieldsInterface} does not declare methods that deprecated API relies upon: the API has been ported from
 * > the {@see \Ramsey\Uuid\Uuid} definition, and is deprecated anyway.
 *
 * > [!NOTE]
 * > The deprecated API from {@see \Ramsey\Uuid\Uuid} is in use here (on purpose): it will be removed once the
 * > deprecated API is gone from this class too.
 *
 * @internal this type is used internally for performance reasons and is not supposed to be directly referenced in consumer libraries.
 */
final class Lazy_Uuid_From_String implements Uuid_Interface
{
    public const VALID_REGEX = '/\A[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\z/ms';
    private ?Uuid_Interface $unwrapped = null;
    /**
     * @param non-empty-string $uuid
     */
    public function __construct(private string $uuid)
    {
    }
    public static function from_bytes(string $bytes): self
    {
        $base16Uuid = bin2hex($bytes);
        return new self(substr($base16Uuid, 0, 8) . '-' . substr($base16Uuid, 8, 4) . '-' . substr($base16Uuid, 12, 4) . '-' . substr($base16Uuid, 16, 4) . '-' . substr($base16Uuid, 20, 12));
    }
    public function serialize(): string
    {
        return $this->uuid;
    }
    /**
     * @return array{string: non-empty-string}
     */
    public function __serialize(): array
    {
        return ['string' => $this->uuid];
    }
    /**
     * {@inheritDoc}
     *
     * @param non-empty-string $data
     */
    public function unserialize(string $data): void
    {
        $this->uuid = $data;
    }
    /**
     * @param array{string?: non-empty-string} $data
     */
    public function __unserialize(array $data): void
    {
        // @codeCoverageIgnoreStart
        if (!isset($data['string'])) {
            throw new Value_Error(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }
        // @codeCoverageIgnoreEnd
        $this->unserialize($data['string']);
    }
    public function get_number_converter(): Number_Converter_Interface
    {
        return ($this->unwrapped ?? $this->unwrap())->get_number_converter();
    }
    /**
     * @inheritDoc
     */
    public function get_fields_hex(): array
    {
        return ($this->unwrapped ?? $this->unwrap())->get_fields_hex();
    }
    public function get_clock_seq_hi_and_reserved_hex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->get_clock_seq_hi_and_reserved_hex();
    }
    public function get_clock_seq_low_hex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->get_clock_seq_low_hex();
    }
    public function get_clock_sequence_hex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->get_clock_sequence_hex();
    }
    public function get_date_time(): DateTimeInterface
    {
        return ($this->unwrapped ?? $this->unwrap())->get_date_time();
    }
    public function get_least_significant_bits_hex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->get_least_significant_bits_hex();
    }
    public function get_most_significant_bits_hex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->get_most_significant_bits_hex();
    }
    public function get_node_hex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->get_node_hex();
    }
    public function get_time_hi_and_version_hex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->get_time_hi_and_version_hex();
    }
    public function get_time_low_hex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->get_time_low_hex();
    }
    public function get_time_mid_hex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->get_time_mid_hex();
    }
    public function get_timestamp_hex(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->get_timestamp_hex();
    }
    public function get_urn(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->get_urn();
    }
    public function get_variant(): ?int
    {
        return ($this->unwrapped ?? $this->unwrap())->get_variant();
    }
    public function get_version(): ?int
    {
        return ($this->unwrapped ?? $this->unwrap())->get_version();
    }
    public function compare_to(Uuid_Interface $other): int
    {
        return ($this->unwrapped ?? $this->unwrap())->compare_to($other);
    }
    public function equals(?object $other): bool
    {
        if (!$other instanceof Uuid_Interface) {
            return false;
        }
        return $this->uuid === $other->to_string();
    }
    public function get_bytes(): string
    {
        /**
         * @phpstan-ignore possiblyImpure.functionCall, possiblyImpure.functionCall
         */
        return (string) hex2bin(str_replace('-', '', $this->uuid));
    }
    public function get_fields(): Fields_Interface
    {
        return ($this->unwrapped ?? $this->unwrap())->get_fields();
    }
    public function get_hex(): Hexadecimal
    {
        return ($this->unwrapped ?? $this->unwrap())->get_hex();
    }
    public function get_integer(): Integer_Object
    {
        return ($this->unwrapped ?? $this->unwrap())->get_integer();
    }
    public function to_string(): string
    {
        return $this->uuid;
    }
    public function __toString(): string
    {
        return $this->uuid;
    }
    public function jsonSerialize(): string
    {
        return $this->uuid;
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getClockSeqHiAndReserved()}
     *     and use the arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function get_clock_seq_hi_and_reserved(): string
    {
        $instance = $this->unwrapped ?? $this->unwrap();
        $fields = $instance->get_fields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\Fields_Interface);
        return $instance->get_number_converter()->from_hex($fields->get_clock_seq_hi_and_reserved()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getClockSeqLow()} and use
     *     the arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function get_clock_seq_low(): string
    {
        $instance = $this->unwrapped ?? $this->unwrap();
        $fields = $instance->get_fields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\Fields_Interface);
        return $instance->get_number_converter()->from_hex($fields->get_clock_seq_low()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getClockSeq()} and use the
     *     arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function get_clock_sequence(): string
    {
        $instance = $this->unwrapped ?? $this->unwrap();
        $fields = $instance->get_fields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\Fields_Interface);
        return $instance->get_number_converter()->from_hex($fields->get_clock_seq()->to_string());
    }
    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function get_least_significant_bits(): string
    {
        $instance = $this->unwrapped ?? $this->unwrap();
        return $instance->get_number_converter()->from_hex(substr($instance->get_hex()->to_string(), 16));
    }
    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function get_most_significant_bits(): string
    {
        $instance = $this->unwrapped ?? $this->unwrap();
        return $instance->get_number_converter()->from_hex(substr($instance->get_hex()->to_string(), 0, 16));
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getNode()} and use the
     *     arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function get_node(): string
    {
        $instance = $this->unwrapped ?? $this->unwrap();
        $fields = $instance->get_fields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\Fields_Interface);
        return $instance->get_number_converter()->from_hex($fields->get_node()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getTimeHiAndVersion()} and
     *     use the arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function get_time_hi_and_version(): string
    {
        $instance = $this->unwrapped ?? $this->unwrap();
        $fields = $instance->get_fields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\Fields_Interface);
        return $instance->get_number_converter()->from_hex($fields->get_time_hi_and_version()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getTimeLow()} and use the
     *     arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function get_time_low(): string
    {
        $instance = $this->unwrapped ?? $this->unwrap();
        $fields = $instance->get_fields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\Fields_Interface);
        return $instance->get_number_converter()->from_hex($fields->get_time_low()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getTimeMid()} and use the
     *     arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function get_time_mid(): string
    {
        $instance = $this->unwrapped ?? $this->unwrap();
        $fields = $instance->get_fields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\Fields_Interface);
        return $instance->get_number_converter()->from_hex($fields->get_time_mid()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see Rfc4122FieldsInterface} instance, you may call {@see Rfc4122FieldsInterface::getTimestamp()} and use
     *     the arbitrary-precision math library of your choice to convert it to a string integer.
     */
    public function get_timestamp(): string
    {
        $instance = $this->unwrapped ?? $this->unwrap();
        $fields = $instance->get_fields();
        assert($fields instanceof \Ramsey\Uuid\Rfc4122\Fields_Interface);
        if ($fields->get_version() !== 1) {
            throw new Unsupported_Operation_Exception('Not a time-based UUID');
        }
        return $instance->get_number_converter()->from_hex($fields->get_timestamp()->to_string());
    }
    public function to_uuid_v1(): Uuid_V1
    {
        $instance = $this->unwrapped ?? $this->unwrap();
        if ($instance instanceof Uuid_V1) {
            return $instance;
        }
        assert($instance instanceof Uuid_V6);
        return $instance->to_uuid_v1();
    }
    public function to_uuid_v6(): Uuid_V6
    {
        $instance = $this->unwrapped ?? $this->unwrap();
        assert($instance instanceof Uuid_V6);
        return $instance;
    }
    private function unwrap(): Uuid_Interface
    {
        return $this->unwrapped = (new Uuid_Factory())->from_string($this->uuid);
    }
}