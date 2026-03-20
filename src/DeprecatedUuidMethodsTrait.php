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
namespace Ramsey\Uuid;

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Converter\Number_Converter_Interface;
use Ramsey\Uuid\Exception\Date_Time_Exception;
use Ramsey\Uuid\Exception\Unsupported_Operation_Exception;
use function str_pad;
use const STR_PAD_LEFT;
use function substr;
use Throwable;
/**
 * This trait encapsulates deprecated methods for ramsey/uuid; this trait and its methods will be removed in ramsey/uuid 5.0.0.
 *
 * @deprecated This trait and its methods will be removed in ramsey/uuid 5.0.0.
 *
 * @immutable
 */
trait Deprecated_Uuid_Methods_Trait
{
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeqHiAndReserved()} and use the arbitrary-precision math
     *     library of your choice to convert it to a string integer.
     */
    public function get_clock_seq_hi_and_reserved(): string
    {
        return $this->number_converter->from_hex($this->fields->get_clock_seq_hi_and_reserved()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeqHiAndReserved()}.
     */
    public function get_clock_seq_hi_and_reserved_hex(): string
    {
        return $this->fields->get_clock_seq_hi_and_reserved()->to_string();
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeqLow()} and use the arbitrary-precision math library of
     *     your choice to convert it to a string integer.
     */
    public function get_clock_seq_low(): string
    {
        return $this->number_converter->from_hex($this->fields->get_clock_seq_low()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeqLow()}.
     */
    public function get_clock_seq_low_hex(): string
    {
        return $this->fields->get_clock_seq_low()->to_string();
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeq()} and use the arbitrary-precision math library of
     *     your choice to convert it to a string integer.
     */
    public function get_clock_sequence(): string
    {
        return $this->number_converter->from_hex($this->fields->get_clock_seq()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeq()}.
     */
    public function get_clock_sequence_hex(): string
    {
        return $this->fields->get_clock_seq()->to_string();
    }
    /**
     * @deprecated This method will be removed in 5.0.0. There is no alternative recommendation, so plan accordingly.
     */
    public function get_number_converter(): Number_Converter_Interface
    {
        return $this->number_converter;
    }
    /**
     * @deprecated In ramsey/uuid version 5.0.0, this will be removed. It is available at {@see UuidV1::getDateTime()}.
     *
     * @return DateTimeImmutable An immutable instance of DateTimeInterface
     *
     * @throws UnsupportedOperationException if UUID is not time-based
     * @throws DateTimeException if DateTime throws an exception/error
     */
    public function get_date_time(): DateTimeInterface
    {
        if ($this->fields->get_version() !== 1) {
            throw new Unsupported_Operation_Exception('Not a time-based UUID');
        }
        $time = $this->time_converter->convert_time($this->fields->get_timestamp());
        try {
            return new DateTimeImmutable('@' . $time->get_seconds()->to_string() . '.' . str_pad($time->get_microseconds()->to_string(), 6, '0', STR_PAD_LEFT));
        } catch (Throwable $e) {
            throw new Date_Time_Exception($e->get_message(), (int) $e->get_code(), $e);
        }
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *
     * @return string[]
     */
    public function get_fields_hex(): array
    {
        return ['time_low' => $this->fields->get_time_low()->to_string(), 'time_mid' => $this->fields->get_time_mid()->to_string(), 'time_hi_and_version' => $this->fields->get_time_hi_and_version()->to_string(), 'clock_seq_hi_and_reserved' => $this->fields->get_clock_seq_hi_and_reserved()->to_string(), 'clock_seq_low' => $this->fields->get_clock_seq_low()->to_string(), 'node' => $this->fields->get_node()->to_string()];
    }
    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function get_least_significant_bits(): string
    {
        $least_significant_hex = substr($this->get_hex()->to_string(), 16);
        return $this->number_converter->from_hex($least_significant_hex);
    }
    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function get_least_significant_bits_hex(): string
    {
        return substr($this->get_hex()->to_string(), 16);
    }
    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function get_most_significant_bits(): string
    {
        $most_significant_hex = substr($this->get_hex()->to_string(), 0, 16);
        return $this->number_converter->from_hex($most_significant_hex);
    }
    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function get_most_significant_bits_hex(): string
    {
        return substr($this->get_hex()->to_string(), 0, 16);
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getNode()} and use the arbitrary-precision math library of your
     *     choice to convert it to a string integer.
     */
    public function get_node(): string
    {
        return $this->number_converter->from_hex($this->fields->get_node()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getNode()}.
     */
    public function get_node_hex(): string
    {
        return $this->fields->get_node()->to_string();
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeHiAndVersion()} and use the arbitrary-precision math
     *     library of your choice to convert it to a string integer.
     */
    public function get_time_hi_and_version(): string
    {
        return $this->number_converter->from_hex($this->fields->get_time_hi_and_version()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeHiAndVersion()}.
     */
    public function get_time_hi_and_version_hex(): string
    {
        return $this->fields->get_time_hi_and_version()->to_string();
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeLow()} and use the arbitrary-precision math library of
     *     your choice to convert it to a string integer.
     */
    public function get_time_low(): string
    {
        return $this->number_converter->from_hex($this->fields->get_time_low()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeLow()}.
     */
    public function get_time_low_hex(): string
    {
        return $this->fields->get_time_low()->to_string();
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeMid()} and use the arbitrary-precision math library of
     *     your choice to convert it to a string integer.
     */
    public function get_time_mid(): string
    {
        return $this->number_converter->from_hex($this->fields->get_time_mid()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeMid()}.
     */
    public function get_time_mid_hex(): string
    {
        return $this->fields->get_time_mid()->to_string();
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimestamp()} and use the arbitrary-precision math library of
     *     your choice to convert it to a string integer.
     */
    public function get_timestamp(): string
    {
        if ($this->fields->get_version() !== 1) {
            throw new Unsupported_Operation_Exception('Not a time-based UUID');
        }
        return $this->number_converter->from_hex($this->fields->get_timestamp()->to_string());
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimestamp()}.
     */
    public function get_timestamp_hex(): string
    {
        if ($this->fields->get_version() !== 1) {
            throw new Unsupported_Operation_Exception('Not a time-based UUID');
        }
        return $this->fields->get_timestamp()->to_string();
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getVariant()}.
     */
    public function get_variant(): ?int
    {
        return $this->fields->get_variant();
    }
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see \Ramsey\Uuid\Fields\FieldsInterface} instance.
     *     If it is a {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getVersion()}.
     */
    public function get_version(): ?int
    {
        return $this->fields->get_version();
    }
}