<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\PathFinder;

use Opportus\ObjectMapper\PathFinder\StaticSourceToDynamicTargetPathFinder;
use Opportus\ObjectMapper\PathFinder\PathFinder;
use Opportus\ObjectMapper\PathFinder\PathFinderInterface;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\Tests\TestObjectA;
use Opportus\ObjectMapper\Tests\TestObjectB;
use PHPUnit\Framework\TestCase;

/**
 * The static source to dynamic target path finder test.
 *
 * @package Opportus\ObjectMapper\Tests\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class StaticSourceToDynamicTargetPathFinderTest extends TestCase
{
    public function testConstruct(): void
    {
        $pathFinder = $this->buildPathFinder();

        static::assertInstanceOf(PathFinderInterface::class, $pathFinder);
        static::assertInstanceOf(PathFinder::class, $pathFinder);
    }

    public function testGetRoutes(): void
    {
        $pathFinder = $this->buildPathFinder();

        $source = new Source(new TestObjectA());
        $target = new Target(TestObjectB::class);

        $routes = $pathFinder->getRoutes($source, $target);

        $expectedRoutes = $this->getExpectedRoutes();

        static::assertEquals($expectedRoutes, $routes);
    }

    private function buildPathFinder(): StaticSourceToDynamicTargetPathFinder
    {
        return new StaticSourceToDynamicTargetPathFinder(
            new RouteBuilder(new PointFactory())
        );
    }

    private function getExpectedRoutes(): RouteCollection
    {
        $routeBuilder = new RouteBuilder(new PointFactory());

        $routes = [];

        $routes[0] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '#%s::getJ()',
                TestObjectA::class
            ))
            ->setTargetPoint(\sprintf(
                '~%s::$j',
                TestObjectB::class
            ))
            ->getRoute();

        $routes[1] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '#%s::getM()',
                TestObjectA::class
            ))
            ->setTargetPoint(\sprintf(
                '~%s::$m',
                TestObjectB::class
            ))
            ->getRoute();

        $routes[2] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '#%s::$n',
                TestObjectA::class
            ))
            ->setTargetPoint(\sprintf(
                '~%s::$n',
                TestObjectB::class
            ))
            ->getRoute();

        return new RouteCollection($routes);
    }
}
