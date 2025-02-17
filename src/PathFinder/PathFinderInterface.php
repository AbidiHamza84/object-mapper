<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\PathFinder;

use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;

/**
 * The path finder interface.
 *
 * @package Opportus\ObjectMapper\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface PathFinderInterface
{
    /**
     * Gets the routes connecting the source points with the target points.
     *
     * @param  SourceInterface           $source A source to map data from
     * @param  TargetInterface           $target A target to map data to
     * @return RouteCollection           A route collection connecting the
     *                                   source points with the target points
     * @throws InvalidOperationException If the operation fails for any reason
     */
    public function getRoutes(SourceInterface $source, TargetInterface $target): RouteCollection;
}
