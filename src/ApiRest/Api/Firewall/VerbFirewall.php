<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author Aldo Chiecchia <zimage@tiscali.it>
 * @author Elcodi Team <tech@elcodi.com>
 */

namespace ApiRest\Api\Firewall;

use ApiRest\Api\ApiRoutes;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use ApiRest\Api\Configuration;

/**
 * Class VerbFirewall
 */
final class VerbFirewall
{
    private $configuration;

    /**
     * Constructor
     *
     * @param Configuration $configuration Configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     *
     */
    public function filter(GetResponseEvent $event)
    {
        $routeFormat = $this->configuration->getRouteFormat();
        $route = $event
            ->getRequest()
            ->get('_route');

        $pattern = str_replace(
            [
                '{entity}',
                '{verb}'
            ],
            [
                '(?P<entity>\w+)',
                '(?P<verb>\w+)'
            ],
            $routeFormat
        );

        preg_match(
            '~' . $pattern . '~',
            $route,
            $matches
        );

        $entityAlias = isset($matches['entity'])
            ? $matches['entity']
            : null;

        $verb = isset($matches['verb'])
            ? $matches['verb']
            : null;

        $entityConfiguration = $this
            ->configuration
            ->getEntityConfigurationByAlias($entityAlias);

        if ($this->getRouteValidity($verb, $entityConfiguration['level'])) {

            return new JsonResponse('', 403);
        }

        $event
            ->getRequest()
            ->attributes
            ->set('_entity_namespace', $entityConfiguration['namespace']);
    }

    /**
     * Check call
     *
     * @return boolean this route is valid
     */
    private function getRouteValidity(
        $verb,
        $level
    )
    {
        $verbCode = ApiRoutes::toCode($verb);

        return $level & $verbCode;
    }
}
