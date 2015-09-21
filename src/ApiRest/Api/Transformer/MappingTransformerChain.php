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
 
namespace ApiRest\Api\Transformer;

use ApiRest\Api\Transformer\Interfaces\MappingTransformerInterface;

/**
 * Class MappingTransformerChain
 */
class MappingTransformerChain
{
    /**
     * @var MappingTransformerInterface[]
     *
     * Mapping transformers
     */
    private $mappingTransformers = [];

    /**
     * Add mapping transformer
     *
     * @param MappingTransformerInterface $mappingTransformer Mapping transformer
     *
     * @return $this Self object
     */
    public function addMappingTransformer(MappingTransformerInterface $mappingTransformer)
    {
        $this->mappingTransformers[] = $mappingTransformer;

        return $this;
    }

    /**
     * Transform a mapped field into a valid API data
     *
     * @param string $type Type
     * @param mixed $value Original value
     *
     * @return mixed Value transformed
     */
    public function transform($type, $value)
    {
        foreach ($this->mappingTransformers as $mappingTransformer) {
            if (in_array($type, $mappingTransformer->getType())) {
                return $mappingTransformer->transform($value);
            }
        }

        return $value;
    }

    /**
     * Transform an entity to a API-valid element
     *
     * @param string $type Type
     * @param mixed $value Transformed value
     *
     * @return mixed Original value
     */
    public function reverseTransform($type, $value)
    {
        foreach ($this->mappingTransformers as $mappingTransformer) {
            if (in_array($type, $mappingTransformer->getType())) {
                return $mappingTransformer->reverseTransform($value);
            }
        }

        return $value;
    }
}
