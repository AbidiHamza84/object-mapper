<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\PathFinder\PathFinderInterface;
use Opportus\ObjectMapper\Route\Route;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Route\RouteCollection;

/**
 * The map builder interface.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface MapBuilderInterface
{
    /**
     * Gets the route builder.
     *
     * @return RouteBuilderInterface
     */
    public function getRouteBuilder(): RouteBuilderInterface;

    /**
     * Adds a route.
     *
     * @param Route $route
     * @return MapBuilderInterface
     */
    public function addRoute(Route $route): MapBuilderInterface;

    /**
     * Adds the routes.
     *
     * @param RouteCollection $routes
     * @return MapBuilderInterface
     */
    public function addRoutes(RouteCollection $routes): MapBuilderInterface;

    /**
     * Sets the pathfinder.
     *
     * @param null|PathFinderInterface $pathFinder
     * @return MapBuilderInterface
     */
    public function setPathFinder(
        ?PathFinderInterface $pathFinder = null
    ): MapBuilderInterface;

    /**
     * Gets the map.
     *
     * @return Map
     */
    public function getMap(): Map;
}
