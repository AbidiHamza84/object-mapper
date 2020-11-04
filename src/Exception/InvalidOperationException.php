<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Exception;

use Throwable;

/**
 * The invalid operation exception.
 *
 * @package Opportus\ObjectMapper\Exception
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class InvalidOperationException extends Exception
{
    /**
     * @var string
     */
    private $function;

    /**
     * Constructs the invalid operation exception.
     *
     * @param string $function
     * @param string $message
     * @param int $code
     * @param null|Throwable $previous
     */
    public function __construct(
        string $function,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->function = $function;

        $message = \sprintf(
            'Operation %s is invalid. %s',
            $this->function,
            $message
        );

        parent::__construct($message, $code, $previous);
    }

    /**
     * Gets the function.
     *
     * @return string
     */
    public function getFunction(): string
    {
        return $this->function;
    }
}
