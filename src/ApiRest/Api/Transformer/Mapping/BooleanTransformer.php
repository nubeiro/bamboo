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
 * Class BooleanTransformer
 */
class BooleanTransformer implements MappingTransformerInterface
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
        return $value
            ? 1
            : 0;
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
        return $value === 1;
    }

    /**
     * Get type
     *
     * @return array Mapping types
     */
    public function getType()
    {
        return ['boolean'];
    }
}
