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
 
namespace ApiRest\Api\Transformer\Mapping;

use ApiRest\Api\Transformer\Interfaces\MappingTransformerInterface;

/**
 * Class JsonArrayTransformer
 */
class JsonArrayTransformer implements MappingTransformerInterface
{
    /**
     * Transform a mapped field into a valid API data
     *
     * @param mixed $value Original value
     *
     * @return mixed Value transformed
     */
    public function transform($value)
    {
        return json_encode($value);
    }

    /**
     * Transform an entity to a API-valid element
     *
     * @param mixed $value Transformed value
     *
     * @return mixed Original value
     */
    public function reverseTransform($value)
    {
        return json_decode($value, true);
    }

    /**
     * Get type
     *
     * @return string Mapping type
     */
    public function getType()
    {
        return ['json_array'];
    }
}
