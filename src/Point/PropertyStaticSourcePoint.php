<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use ReflectionException;
use ReflectionProperty;

/**
 * The property static source point.
 *
 * @package Opportus\ObjectMapper\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PropertyStaticSourcePoint extends SourcePoint implements StaticSourcePointInterface
{
    private const FQN_REGEX_PATTERN = '/^#?([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/';

    /**
     * Constructs the property static source point.
     *
     * @param string $fqn
     * @throws InvalidArgumentException
     */
    public function __construct(string $fqn)
    {
        if (!\preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                '%s is not a property static source point as FQN of such is expected to have the following syntax: %s.',
                $fqn,
                self::FQN_REGEX_PATTERN
            );

            throw new InvalidArgumentException(1, $message);
        }

        [$matchedFqn, $matchedSourceFqn, $matchedName] = $matches;

        try {
            new ReflectionProperty(
                $matchedSourceFqn,
                $matchedName
            );
        } catch (ReflectionException $exception) {
            $message = \sprintf(
                '%s is not a property static source point. %s.',
                $fqn,
                $exception->getMessage()
            );

            throw new InvalidArgumentException(1, $message);
        }

        $this->fqn = \sprintf('#%s', \ltrim($matchedFqn, '#'));
        $this->sourceFqn = $matchedSourceFqn;
        $this->name = $matchedName;
    }

    /**
     * {@inheritdoc}
     */
    public static function getFqnRegexPattern(): string
    {
        return self::FQN_REGEX_PATTERN;
    }
}
