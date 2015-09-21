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

namespace ApiRest\Api\Transformer\Interfaces;

/**
 * Interface ApiTransformerInterface
 */
interface ApiTransformerInterface
{
    /**
     * Transform an entity to a API-valid element
     *
     * @param object $entity      Entity
     * @param string $entityAlias Entity alias
     *
     * @return array Data transformed
     */
    public function transform(
        $entity,
        $entityAlias
    );

    /**
     * Transform an entity to a API-valid element
     *
     * @param array  $apiData     Api data
     * @param string $entityAlias Entity alias
     *
     * @return array Entity instance
     */
    public function reverseTransform(
        $apiData,
        $entityAlias
    );
}
