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

use DateTimeInterface;
use Ramsey\Uuid\Converter\Number_Converter_Interface;
/**
 * This interface encapsulates deprecated methods for ramsey/uuid
 *
 * @immutable
 */
interface Deprecated_Uuid_Interface
{
    /**
     * @deprecated This method will be removed in 5.0.0. There is no alternative recommendation, so plan accordingly.
     */
    public function get_number_converter(): Number_Converter_Interface;
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance.
     *
     * @return string[]
     */
    public function get_fields_hex(): array;
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeqHiAndReserved()}.
     */
    public function get_clock_seq_hi_and_reserved_hex(): string;
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeqLow()}.
     */
    public function get_clock_seq_low_hex(): string;
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeq()}.
     */
    public function get_clock_sequence_hex(): string;
    /**
     * @deprecated In ramsey/uuid version 5.0.0, this will be removed from the interface. It is available at
     *     {@see UuidV1::getDateTime()}.
     */
    public function get_date_time(): DateTimeInterface;
    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function get_least_significant_bits_hex(): string;
    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct alternative, but the same information may be
     *     obtained by splitting in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function get_most_significant_bits_hex(): string;
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getNode()}.
     */
    public function get_node_hex(): string;
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeHiAndVersion()}.
     */
    public function get_time_hi_and_version_hex(): string;
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeLow()}.
     */
    public function get_time_low_hex(): string;
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeMid()}.
     */
    public function get_time_mid_hex(): string;
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimestamp()}.
     */
    public function get_timestamp_hex(): string;
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getVariant()}.
     */
    public function get_variant(): ?int;
    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getVersion()}.
     */
    public function get_version(): ?int;
}