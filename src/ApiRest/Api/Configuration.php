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

namespace ApiRest\Api;
 
/**
 * Class Configuration
 */
class Configuration
{
    /**
     * @var array
     *
     * Entities configuration
     */
    private $entitiesConfiguration;

    /**
     * @var string
     *
     * Route format
     */
    private $routeFormat;

    /**
     * Constructor
     *
     * @param array $entitiesConfiguration Entities configuration
     * @param string $routeFormat Route format
     */
    function __construct(
        array $entitiesConfiguration,
        $routeFormat
    )
    {
        $this->entitiesConfiguration = $entitiesConfiguration;
        $this->routeFormat = $routeFormat;
    }

    /**
     * Get EntitiesConfiguration
     *
     * @return mixed EntitiesConfiguration
     */
    public function getEntitiesConfiguration()
    {
        return $this->entitiesConfiguration;
    }

    /**
     * Get RouteFormat
     *
     * @return string RouteFormat
     */
    public function getRouteFormat()
    {
        return $this->routeFormat;
    }

    /**
     * Get entity configuration given its alias
     *
     * @param string $entityAlias Entity alias
     *
     * @return array Entity configuration
     */
    public function getEntityConfigurationByAlias($entityAlias)
    {
        return $this->entitiesConfiguration[$entityAlias];
    }

    /**
     * Get entity configuration given its alias
     *
     * If is not found, return null
     *
     * @param string $entityNamespace Entity namespace
     *
     * @return array|false Entity configuration
     */
    public function getEntityConfigurationByNamespace($entityNamespace)
    {
        foreach ($this->entitiesConfiguration as $entityConfiguration) {

            if ($entityConfiguration['namespace'] === $entityNamespace) {

                return $entityConfiguration;
            }
        }

        return false;
    }
}
