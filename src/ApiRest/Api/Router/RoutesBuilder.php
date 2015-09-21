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

use ApiRest\Api\Configuration;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class RoutesBuilder
 */
class RoutesBuilder
{
    /**
     * @var UrlGeneratorInterface
     *
     * Url generator
     */
    private $urlGenerator;

    /**
     * @var Configuration
     *
     * Configuration
     */
    private $configuration;

    /**
     * Construct
     *
     * @param UrlGeneratorInterface $urlGenerator  Url generator
     * @param Configuration         $configuration Configuration
     */
    function __construct(
        UrlGeneratorInterface $urlGenerator,
        Configuration $configuration
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->configuration = $configuration;
    }


    /**
     * Build route name given the entity alias and the verb
     *
     * @param string $entityAlias Entity alias
     * @param string $verb        Verb
     *
     * @return string Route name
     */
    public function getRouteNameByEntityAliasAndVerb(
        $entityAlias,
        $verb
    )
    {
        return str_replace(
            [
                '{entity}',
                '{verb}'
            ],
            [
                $entityAlias,
                $verb,
            ],
            $this
                ->configuration
                ->getRouteFormat()
        );
    }

    /**
     * Build route path given the entity alias and the verb
     *
     * @param string $entityAlias Entity alias
     * @param string $verb        Verb
     * @param array  $parameters  Parameters
     *
     * @return string Route name
     */
    public function getRoutePathByEntityAliasAndVerb(
        $entityAlias,
        $verb,
        array $parameters = []
    )
    {
        $routeName = $this->getRouteNameByEntityAliasAndVerb(
            $entityAlias,
            $verb
        );

        return $this
            ->urlGenerator
            ->generate(
                $routeName,
                $parameters,
                UrlGeneratorInterface::ABSOLUTE_URL
            );
    }
}
