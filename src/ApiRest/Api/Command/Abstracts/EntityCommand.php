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
 
namespace ApiRest\Api\Command\Abstracts;

/**
 * Class EntityCommand
 */
class EntityCommand
{
    /**
     * @var string
     *
     * Entity namespace
     */
    private $entityNamespace;

    /**
     * Construct
     *
     * @param string $entityNamespace Entity namespace
     */
    public function __construct($entityNamespace)
    {
        $this->entityNamespace = $entityNamespace;
    }

    /**
     * Get entity namespace
     *
     * @return string Entity namespace
     */
    public function getEntityNamespace()
    {
        return $this->entityNamespace;
    }
}
