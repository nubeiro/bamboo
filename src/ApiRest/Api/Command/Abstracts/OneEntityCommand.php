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
 * Class OneEntityCommand
 */
class OneEntityCommand extends EntityCommand
{
    /**
     * @var string
     *
     * Entity id
     */
    private $entityId;

    /**
     * Construct
     *
     * @param string $entityNamespace Entity namespace
     * @param string $entityId Entity id
     */
    public function __construct($entityNamespace, $entityId)
    {
        parent::__construct($entityNamespace);

        $this->entityId = $entityId;
    }

    /**
     * Get entity id
     *
     * @return string Entity id
     */
    public function getEntityId()
    {
        return $this->entityId;
    }
}
