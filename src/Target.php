<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Point\ObjectPoint;
use Opportus\ObjectMapper\Point\OverloadedMethodParameterObjectPoint;
use Opportus\ObjectMapper\Point\OverloadedPropertyObjectPoint;
use Opportus\ObjectMapper\Point\MethodParameterObjectPoint;
use Opportus\ObjectMapper\Point\PropertyObjectPoint;
use ReflectionClass;
use ReflectionException;

/**
 * The target.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class Target
{
    /**
     * @var ReflectionClass $reflection
     */
    private $reflection;

    /**
     * @var null|object $instance
     */
    private $instance;

    /**
     * @var array $pointValues
     */
    private $pointValues;

    /**
     * Constructs the target.
     *
     * @param object|string $target
     * @throws InvalidArgumentException
     */
    public function __construct($target)
    {
        if (false === \is_object($target) && false === \is_string($target)) {
            $message = \sprintf(
                'The argument must be of type object or string, got an argument of type %s.',
                \gettype($target)
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        try {
            $this->reflection = new ReflectionClass($target);
        } catch (ReflectionException $exception) {
            $message = \sprintf(
                '%s is not a target. %s',
                $target,
                $exception->getMessage()
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        $this->instance = \is_object($target) ? $target : null;
        $this->pointValues = [
            'properties' => [],
            'parameters' => [],
            'overloaded_properties' => [],
            'overloaded_parameters' => [],
        ];
    }

    /**
     * Gets the target reflection.
     *
     * @return ReflectionClass
     */
    public function getReflection(): ReflectionClass
    {
        return new ReflectionClass($this->reflection->getName());
    }

    /**
     * Gets the target instance.
     *
     * @return null|object
     * @throws InvalidOperationException
     */
    public function getInstance(): ?object
    {
        if ($this->hasPointValues()) {
            try {
                return $this->operateInstance(
                    $this->instance,
                    $this->pointValues
                );
            } catch (ReflectionException $exception) {
                throw new InvalidOperationException(
                    __METHOD__,
                    $exception->getMessage()
                );
            }
        }

        return $this->instance;
    }

    /**
     * Checks whether the target is instantiated.
     *
     * @return bool
     */
    public function isInstantiated(): bool
    {
        return (bool)$this->instance;
    }

    /**
     * Checks whether the source has the passed point type.
     *
     * @param ObjectPoint $point
     * @return bool
     */
    public static function hasPointType(ObjectPoint $point): bool
    {
        return
            $point instanceof PropertyObjectPoint ||
            $point instanceof MethodParameterObjectPoint ||
            $point instanceof OverloadedPropertyObjectPoint ||
            $point instanceof OverloadedMethodParameterObjectPoint;
    }

    /**
     * Checks whether the target has the passed point.
     *
     * @param ObjectPoint $point
     * @return bool
     */
    public function hasPoint(ObjectPoint $point): bool
    {
        return
            self::hasPointType($point) &&
            $this->reflection->getName() === $point->getClassFqn();
    }

    /**
     * Sets the value of the passed target point.
     *
     * @param ObjectPoint $point
     * @param mixed $pointValue
     * @throws InvalidArgumentException
     */
    public function setPointValue(ObjectPoint $point, $pointValue)
    {
        if (false === $this->hasPoint($point) &&
            false === self::hasPointType($point)
        ) {
            $message = \sprintf(
                '%s is not a property of %s.',
                $point->getFqn(),
                $this->reflection->getName()
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        if ($point instanceof PropertyObjectPoint) {
            $this->pointValues['properties'][$point->getName()] = $pointValue;
        } elseif ($point instanceof MethodParameterObjectPoint) {
            $this->pointValues['parameters'][$point->getMethodName()]
                [$this->getParameterPointPosition($point)] = $pointValue;
        } elseif ($point instanceof OverloadedPropertyObjectPoint) {
            $this->pointValues['overloaded_properties']
                [$point->getName()] = $pointValue;
        } elseif ($point instanceof OverloadedMethodParameterObjectPoint) {
            $this->pointValues['overloaded_parameters']
                [$point->getMethodName()][] = $pointValue;
        }
    }

    /**
     * Gets the parameter point position.
     *
     * @param MethodParameterObjectPoint $point
     * @return int
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    private function getParameterPointPosition(MethodParameterObjectPoint $point): int
    {
        foreach (
            $this->reflection->getMethod($point->getMethodName())
                ->getParameters() as
            $parameter
        ) {
            if ($parameter->getName() === $point->getName()) {
                return $parameter->getPosition();
            }
        }
    }

    /**
     * Creates/updates the target instance.
     *
     * @param null|object $instance
     * @param array $pointValues
     * @return object
     * @throws ReflectionException
     */
    private function operateInstance(
        ?object $instance,
        array $pointValues
    ): object {
        if (null === $instance) {
            if (isset($pointValues['parameters']['__construct'])) {
                $instance = $this->reflection->newInstanceArgs(
                    $pointValues['parameters']['__construct']
                );
            } else {
                $instance = $this->reflection->newInstance();
            }
        }

        foreach (
            $pointValues['parameters'] as
            $methodName =>
            $methodArguments
        ) {
            if ('__construct' === $methodName) {
                continue;
            }

            $this->reflection->getMethod($methodName)->invokeArgs(
                $instance,
                $methodArguments
            );
        }

        foreach (
            $pointValues['overloaded_parameters'] as
            $methodName =>
            $methodArguments
        ) {
            if ('__construct' === $methodName) {
                continue;
            }

            $instance->{$methodName}(...$methodArguments);
        }

        foreach (
            $pointValues['properties'] as
            $propertyName =>
            $propertyValue
        ) {
            $this->reflection->getProperty($propertyName)->setValue(
                $instance,
                $propertyValue
            );
        }

        foreach (
            $pointValues['overloaded_properties'] as
            $propertyName =>
            $propertyValue
        ) {
            $instance->{$propertyName} = $propertyValue;
        }

        return $instance;
    }

    /**
     * Checks whether target point values have been set.
     *
     * @return bool
     */
    private function hasPointValues(): bool
    {
        return
            $this->pointValues['properties'] ||
            $this->pointValues['parameters'] ||
            $this->pointValues['overloaded_properties'] ||
            $this->pointValues['overloaded_parameters'];
    }
}
