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

namespace ApiRest\Api\Router;

use ApiRest\Api\ApiRoutes;
use ApiRest\Api\Configuration;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteGenerator
 *
 * This class depends on a structure like this
 *
 * [
 *      "order" => [
 *          "api" => true,
 *          "namespace" => "My\Namespace\Order",
 *          "methods" => [
 *              "GET", "POST", "DELETE",
 *          ],
 *      ],
 * ]
 */
class RoutesLoader implements LoaderInterface
{
    /**
     * @var boolean
     *
     * Route is loaded
     */
    private $loaded = false;

    /**
     * @var Configuration
     *
     * Configuration
     */
    private $configuration;

    /**
     * @var RoutesBuilder
     *
     * Router Builder
     */
    private $RoutesBuilder;

    /**
     * Construct
     *
     * @param Configuration $configuration Configuration
     * @param RoutesBuilder $RoutesBuilder Router Builder
     */
    public function __construct(
        Configuration $configuration,
        RoutesBuilder $RoutesBuilder
    )
    {
        $this->configuration = $configuration;
        $this->RoutesBuilder = $RoutesBuilder;
    }

    /**
     * Loads a resource.
     *
     * @param mixed       $resource The resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return RouteCollection
     *
     * @throws \Exception If something went wrong
     */
    public function load($resource, $type = null)
    {
        if ($this->loaded) {

            throw new \RuntimeException('Do not add this loader twice');
        }

        $routes = new RouteCollection();
        $entitiesConfiguration = $this
            ->configuration
            ->getEntitiesConfiguration();

        foreach ($entitiesConfiguration as $entityAlias => $entityConfiguration) {

            if ($entityConfiguration['api']) {
                $routes->addCollection(
                    $this->loadEntityRoutes(
                        $entityAlias,
                        $entityConfiguration
                    )
                );
            }
        }

        $this->loaded = true;

        return $routes;
    }

    /**
     * [
     *     "api" => true,
     *     "namespace" => "My\Namespace\Order",
     *     "methods" => [
     *         "GET", "POST", "DELETE",
     *     ]
     * ]
     *
     * @param string $entityAlias         Entity alias
     * @param array  $entityConfiguration Entity configuration
     *
     * @return RouteCollection
     */
    private function loadEntityRoutes(
        $entityAlias,
        array $entityConfiguration
    )
    {
        $routes = new RouteCollection();
        $entityLevel = $entityConfiguration['level'];
        $activeVerbs = ApiRoutes::valid($entityLevel);

        foreach ($activeVerbs as $activeVerb) {

            $verb = ApiRoutes::toVerb($activeVerb);
            $method = 'load' . ucfirst($verb) . 'EntityRoute';
            $routes
                ->add(
                    $this
                        ->RoutesBuilder
                        ->getRouteNameByEntityAliasAndVerb(
                            $entityAlias,
                            $verb
                        ),
                    $this->$method($entityAlias)
                );
        }

        return $routes;
    }

    /**
     * Generate get route
     *
     * @param string $entityAlias Entity alias
     *
     * @return Route Route
     */
    private function loadGetEntityRoute($entityAlias)
    {
        return new Route(
            $entityAlias . '/{id}', [
            '_controller' => 'api_rest.controller.get:doAction',
            'id'          => null,
        ], [], [], '', [], [
                'GET'
            ]
        );
    }

    /**
     * Generate post route
     *
     * @param string $entityAlias Entity alias
     *
     * @return Route Route
     */
    private function loadPostEntityRoute($entityAlias)
    {
        return new Route(
            $entityAlias . '/{id}', [
            '_controller' => 'api_rest.controller.post:doAction',
            'id'          => null,
        ], [], [], '', [], [
                'POST'
            ]
        );
    }

    /**
     * Generate put route
     *
     * @param string $entityAlias Entity alias
     *
     * @return Route Route
     */
    private function loadPutEntityRoute($entityAlias)
    {
        return new Route(
            $entityAlias . '/{id}', [
            '_controller' => 'api_rest.controller.put:doAction',
            'id'          => null,
        ], [], [], '', [], [
                'PUT'
            ]
        );
    }

    /**
     * Generate delete route
     *
     * @param string $entityAlias Entity alias
     *
     * @return Route Route
     */
    private function loadDeleteEntityRoute($entityAlias)
    {
        return new Route(
            $entityAlias . '/{id}', [
            '_controller' => 'api_rest.controller.delete:doAction',
            'id'          => null,
        ], [], [], '', [], [
                'DELETE'
            ]
        );
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed       $resource A resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return 'api' === $type;
    }

    /**
     * Gets the loader resolver.
     *
     * @return LoaderResolverInterface A LoaderResolverInterface instance
     */
    public function getResolver()
    {
    }

    /**
     * Sets the loader resolver.
     *
     * @param LoaderResolverInterface $resolver A LoaderResolverInterface instance
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
