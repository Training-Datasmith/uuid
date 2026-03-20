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

use const PHP_INT_SIZE;
use Ramsey\Uuid\Builder\Fallback_Builder;
use Ramsey\Uuid\Builder\Uuid_Builder_Interface;
use Ramsey\Uuid\Codec\Codec_Interface;
use Ramsey\Uuid\Codec\Guid_String_Codec;
use Ramsey\Uuid\Codec\String_Codec;
use Ramsey\Uuid\Converter\Number\Generic_Number_Converter;
use Ramsey\Uuid\Converter\Number_Converter_Interface;
use Ramsey\Uuid\Converter\Time\Generic_Time_Converter;
use Ramsey\Uuid\Converter\Time\Php_Time_Converter;
use Ramsey\Uuid\Converter\Time_Converter_Interface;
use Ramsey\Uuid\Generator\Dce_Security_Generator;
use Ramsey\Uuid\Generator\Dce_Security_Generator_Interface;
use Ramsey\Uuid\Generator\Name_Generator_Factory;
use Ramsey\Uuid\Generator\Name_Generator_Interface;
use Ramsey\Uuid\Generator\Pecl_Uuid_Name_Generator;
use Ramsey\Uuid\Generator\Pecl_Uuid_Random_Generator;
use Ramsey\Uuid\Generator\Pecl_Uuid_Time_Generator;
use Ramsey\Uuid\Generator\Random_Generator_Factory;
use Ramsey\Uuid\Generator\Random_Generator_Interface;
use Ramsey\Uuid\Generator\Time_Generator_Factory;
use Ramsey\Uuid\Generator\Time_Generator_Interface;
use Ramsey\Uuid\Generator\Unix_Time_Generator;
use Ramsey\Uuid\Guid\Guid_Builder;
use Ramsey\Uuid\Math\Brick_Math_Calculator;
use Ramsey\Uuid\Math\Calculator_Interface;
use Ramsey\Uuid\Nonstandard\Uuid_Builder as NonstandardUuidBuilder;
use Ramsey\Uuid\Provider\Dce\System_Dce_Security_Provider;
use Ramsey\Uuid\Provider\Dce_Security_Provider_Interface;
use Ramsey\Uuid\Provider\Node\Fallback_Node_Provider;
use Ramsey\Uuid\Provider\Node\Random_Node_Provider;
use Ramsey\Uuid\Provider\Node\System_Node_Provider;
use Ramsey\Uuid\Provider\Node_Provider_Interface;
use Ramsey\Uuid\Provider\Time\System_Time_Provider;
use Ramsey\Uuid\Provider\Time_Provider_Interface;
use Ramsey\Uuid\Rfc4122\Uuid_Builder as Rfc4122UuidBuilder;
use Ramsey\Uuid\Validator\Generic_Validator;
use Ramsey\Uuid\Validator\Validator_Interface;
/**
 * FeatureSet detects and exposes available features in the current environment
 *
 * A feature set is used by UuidFactory to determine the available features and capabilities of the environment.
 */
class Feature_Set
{
    private ?Time_Provider_Interface $time_provider = null;
    private Calculator_Interface $calculator;
    private Codec_Interface $codec;
    private Dce_Security_Generator_Interface $dce_security_generator;
    private Name_Generator_Interface $name_generator;
    private Node_Provider_Interface $node_provider;
    private Number_Converter_Interface $number_converter;
    private Random_Generator_Interface $random_generator;
    private Time_Converter_Interface $time_converter;
    private Time_Generator_Interface $time_generator;
    private Time_Generator_Interface $unix_time_generator;
    private Uuid_Builder_Interface $builder;
    private Validator_Interface $validator;
    /**
     * @param bool $useGuids True build UUIDs using the GuidStringCodec
     * @param bool $force32Bit True to force the use of 32-bit functionality (primarily for testing purposes)
     * @param bool $forceNoBigNumber (obsolete)
     * @param bool $ignoreSystemNode True to disable attempts to check for the system node ID (primarily for testing purposes)
     * @param bool $enablePecl True to enable the use of the PeclUuidTimeGenerator to generate version 1 UUIDs
     *
     * @phpstan-ignore constructor.unusedParameter ($forceNoBigNumber is deprecated)
     */
    public function __construct(bool $use_guids = false, private bool $force32Bit = false, bool $force_no_big_number = false, private bool $ignore_system_node = false, private bool $enable_pecl = false)
    {
        $this->random_generator = $this->build_random_generator();
        $this->set_calculator(new Brick_Math_Calculator());
        $this->builder = $this->build_uuid_builder($use_guids);
        $this->codec = $this->build_codec($use_guids);
        $this->node_provider = $this->build_node_provider();
        $this->name_generator = $this->build_name_generator();
        $this->set_time_provider(new System_Time_Provider());
        $this->set_dce_security_provider(new System_Dce_Security_Provider());
        $this->validator = new Generic_Validator();
        assert($this->time_provider !== null);
        $this->unix_time_generator = $this->build_unix_time_generator();
    }
    /**
     * Returns the builder configured for this environment
     */
    public function get_builder(): Uuid_Builder_Interface
    {
        return $this->builder;
    }
    /**
     * Returns the calculator configured for this environment
     */
    public function get_calculator(): Calculator_Interface
    {
        return $this->calculator;
    }
    /**
     * Returns the codec configured for this environment
     */
    public function get_codec(): Codec_Interface
    {
        return $this->codec;
    }
    /**
     * Returns the DCE Security generator configured for this environment
     */
    public function get_dce_security_generator(): Dce_Security_Generator_Interface
    {
        return $this->dce_security_generator;
    }
    /**
     * Returns the name generator configured for this environment
     */
    public function get_name_generator(): Name_Generator_Interface
    {
        return $this->name_generator;
    }
    /**
     * Returns the node provider configured for this environment
     */
    public function get_node_provider(): Node_Provider_Interface
    {
        return $this->node_provider;
    }
    /**
     * Returns the number converter configured for this environment
     */
    public function get_number_converter(): Number_Converter_Interface
    {
        return $this->number_converter;
    }
    /**
     * Returns the random generator configured for this environment
     */
    public function get_random_generator(): Random_Generator_Interface
    {
        return $this->random_generator;
    }
    /**
     * Returns the time converter configured for this environment
     */
    public function get_time_converter(): Time_Converter_Interface
    {
        return $this->time_converter;
    }
    /**
     * Returns the time generator configured for this environment
     */
    public function get_time_generator(): Time_Generator_Interface
    {
        return $this->time_generator;
    }
    /**
     * Returns the Unix Epoch time generator configured for this environment
     */
    public function get_unix_time_generator(): Time_Generator_Interface
    {
        return $this->unix_time_generator;
    }
    /**
     * Returns the validator configured for this environment
     */
    public function get_validator(): Validator_Interface
    {
        return $this->validator;
    }
    /**
     * Sets the calculator to use in this environment
     */
    public function set_calculator(Calculator_Interface $calculator): void
    {
        $this->calculator = $calculator;
        $this->number_converter = $this->build_number_converter($calculator);
        $this->time_converter = $this->build_time_converter($calculator);
        if (isset($this->time_provider)) {
            $this->time_generator = $this->build_time_generator($this->time_provider);
        }
    }
    /**
     * Sets the DCE Security provider to use in this environment
     */
    public function set_dce_security_provider(Dce_Security_Provider_Interface $dce_security_provider): void
    {
        $this->dce_security_generator = $this->build_dce_security_generator($dce_security_provider);
    }
    /**
     * Sets the node provider to use in this environment
     */
    public function set_node_provider(Node_Provider_Interface $node_provider): void
    {
        $this->node_provider = $node_provider;
        if (isset($this->time_provider)) {
            $this->time_generator = $this->build_time_generator($this->time_provider);
        }
    }
    /**
     * Sets the time provider to use in this environment
     */
    public function set_time_provider(Time_Provider_Interface $time_provider): void
    {
        $this->time_provider = $time_provider;
        $this->time_generator = $this->build_time_generator($time_provider);
    }
    /**
     * Set the validator to use in this environment
     */
    public function set_validator(Validator_Interface $validator): void
    {
        $this->validator = $validator;
    }
    /**
     * Returns a codec configured for this environment
     *
     * @param bool $useGuids Whether to build UUIDs using the GuidStringCodec
     */
    private function build_codec(bool $use_guids = false): Codec_Interface
    {
        if ($use_guids) {
            return new Guid_String_Codec($this->builder);
        }
        return new String_Codec($this->builder);
    }
    /**
     * Returns a DCE Security generator configured for this environment
     */
    private function build_dce_security_generator(Dce_Security_Provider_Interface $dce_security_provider): Dce_Security_Generator_Interface
    {
        return new Dce_Security_Generator($this->number_converter, $this->time_generator, $dce_security_provider);
    }
    /**
     * Returns a node provider configured for this environment
     */
    private function build_node_provider(): Node_Provider_Interface
    {
        if ($this->ignore_system_node) {
            return new Random_Node_Provider();
        }
        return new Fallback_Node_Provider([new System_Node_Provider(), new Random_Node_Provider()]);
    }
    /**
     * Returns a number converter configured for this environment
     */
    private function build_number_converter(Calculator_Interface $calculator): Number_Converter_Interface
    {
        return new Generic_Number_Converter($calculator);
    }
    /**
     * Returns a random generator configured for this environment
     */
    private function build_random_generator(): Random_Generator_Interface
    {
        if ($this->enable_pecl) {
            return new Pecl_Uuid_Random_Generator();
        }
        return (new Random_Generator_Factory())->get_generator();
    }
    /**
     * Returns a time generator configured for this environment
     *
     * @param TimeProviderInterface $timeProvider The time provider to use with
     *     the time generator
     */
    private function build_time_generator(Time_Provider_Interface $time_provider): Time_Generator_Interface
    {
        if ($this->enable_pecl) {
            return new Pecl_Uuid_Time_Generator();
        }
        return (new Time_Generator_Factory($this->node_provider, $this->time_converter, $time_provider))->get_generator();
    }
    /**
     * Returns a Unix Epoch time generator configured for this environment
     */
    private function build_unix_time_generator(): Time_Generator_Interface
    {
        return new Unix_Time_Generator($this->random_generator);
    }
    /**
     * Returns a name generator configured for this environment
     */
    private function build_name_generator(): Name_Generator_Interface
    {
        if ($this->enable_pecl) {
            return new Pecl_Uuid_Name_Generator();
        }
        return (new Name_Generator_Factory())->get_generator();
    }
    /**
     * Returns a time converter configured for this environment
     */
    private function build_time_converter(Calculator_Interface $calculator): Time_Converter_Interface
    {
        $generic_converter = new Generic_Time_Converter($calculator);
        if ($this->is64bit_system()) {
            return new Php_Time_Converter($calculator, $generic_converter);
        }
        return $generic_converter;
    }
    /**
     * Returns a UUID builder configured for this environment
     *
     * @param bool $useGuids Whether to build UUIDs using the GuidStringCodec
     */
    private function build_uuid_builder(bool $use_guids = false): Uuid_Builder_Interface
    {
        if ($use_guids) {
            return new Guid_Builder($this->number_converter, $this->time_converter);
        }
        return new Fallback_Builder([new Rfc4122uuid_Builder($this->number_converter, $this->time_converter), new Nonstandard_Uuid_Builder($this->number_converter, $this->time_converter)]);
    }
    /**
     * Returns true if the PHP build is 64-bit
     */
    private function is64bit_system(): bool
    {
        return PHP_INT_SIZE === 8 && !$this->force32Bit;
    }
}