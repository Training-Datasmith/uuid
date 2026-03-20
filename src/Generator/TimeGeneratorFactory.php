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

use Ramsey\Uuid\Converter\Time_Converter_Interface;
use Ramsey\Uuid\Provider\Node_Provider_Interface;
use Ramsey\Uuid\Provider\Time_Provider_Interface;
/**
 * TimeGeneratorFactory retrieves a default time generator, based on the environment
 */
class Time_Generator_Factory
{
    public function __construct(private Node_Provider_Interface $node_provider, private Time_Converter_Interface $time_converter, private Time_Provider_Interface $time_provider)
    {
    }
    /**
     * Returns a default time generator, based on the current environment
     */
    public function get_generator(): Time_Generator_Interface
    {
        return new Default_Time_Generator($this->node_provider, $this->time_converter, $this->time_provider);
    }
}