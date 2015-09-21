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

use ApiRest\Api\Configuration;
use ApiRest\Api\Router\RoutesBuilder;
use ApiRest\Api\Transformer\Interfaces\ApiTransformerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Elcodi\Component\Core\Services\ManagerProvider;

/**
 * Class EntityTransformer
 */
class EntityTransformer implements ApiTransformerInterface
{
    /**
     * @var ManagerProvider
     *
     * Manager provider
     */
    protected $managerProvider;

    /**
     * @var Configuration
     *
     * configuration
     */
    protected $configuration;

    /**
     * @var RoutesBuilder
     *
     * Router Builder
     */
    private $routesBuilder;

    /**
     * @var MappingTransformerChain
     *
     * Mapping Transformer chain
     */
    private $mappingTransformerChain;

    /**
     * Construct
     *
     * @param ManagerProvider         $managerProvider         Manager provider
     * @param Configuration           $configuration           Configuration
     * @param RoutesBuilder           $RoutesBuilder           Router Builder
     * @param MappingTransformerChain $mappingTransformerChain Mapping Transformer chain
     */
    function __construct(
        ManagerProvider $managerProvider,
        Configuration $configuration,
        RoutesBuilder $RoutesBuilder,
        MappingTransformerChain $mappingTransformerChain
    )
    {
        $this->managerProvider = $managerProvider;
        $this->configuration = $configuration;
        $this->routesBuilder = $RoutesBuilder;
        $this->mappingTransformerChain = $mappingTransformerChain;
    }

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
    )
    {
        $entityNamespace = get_class($entity);
        $objectManager = $this
            ->managerProvider
            ->getManagerByEntityNamespace($entityNamespace);

        /**
         * @var ClassMetadata $classMetadata
         */
        $classMetadata = $objectManager->getClassMetadata($entityNamespace);
        $idField = $classMetadata->getIdentifier()[0];

        $structure = [
            'type'          => $entityAlias,
            'id'            => $classMetadata->getFieldValue($entity, $idField),
            'attributes'    => [],
            'relationships' => [],
            'links'         => $this->getEntityLinks($entity, $entityAlias),
        ];


        /**
         * Field transformation
         *
         * Each field is transformed by using the mapping transformer chain, so
         * every different field can be treated specifically
         */
        $fields = array_diff(
            $classMetadata->getFieldNames(),
            $classMetadata->getIdentifierFieldNames()
        );

        foreach ($fields as $field) {
            $fieldMapping = $classMetadata->getFieldMapping($field);
            $structure['attributes'][$field] = $this
                ->mappingTransformerChain
                ->transform(
                    $fieldMapping['type'],
                    $classMetadata->getFieldValue(
                        $entity,
                        $field
                    )
                );
        }

        /**
         * Each association is resolved
         */
        foreach ($classMetadata->getAssociationNames() as $association) {
            $structure['relationships'][$association] = $this->getAssociationStructure(
                $classMetadata,
                $entity,
                $association
            );
        }

        return $structure;
    }

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
    )
    {
        // TODO: Implement reverseTransform() method.
    }

    /**
     * Get an association element.
     *
     * This method resolves all relations, acting differently between *_TO_ONE
     * and *_TO_MANY
     *
     * @param ClassMetadata $classMetadata Class metadata
     * @param object        $entity        Entity
     * @param string        $field         Field
     *
     * @return array Association built
     */
    private function getAssociationStructure(
        ClassMetadata $classMetadata,
        $entity,
        $field
    )
    {
        $associationMapping = $classMetadata->getAssociationMapping($field);
        $associationNamespace = $associationMapping['targetEntity'];
        $entityNamespace = get_class($entity);
        $objectManager = $this
            ->managerProvider
            ->getManagerByEntityNamespace($associationNamespace);

        /**
         * @var ClassMetadata $associationClassMetadata
         */
        $associationClassMetadata = $objectManager->getClassMetadata($entityNamespace);
        $associationConfiguration = $this
            ->configuration
            ->getEntityConfigurationByNamespace($associationNamespace);
        $associationIsApiCovered = is_array($associationConfiguration);
        $associationIdField = $associationClassMetadata->getIdentifier()[0];

        if ($associationMapping['type'] & ClassMetadataInfo::TO_ONE) {

            $singleAssociationEntity = $classMetadata->getFieldValue($entity, $field);
            $structure = [
                'links' => $associationIsApiCovered
                    ? $this->getEntityLinks($entity, $associationConfiguration['alias'])
                    : [],
                'data'  => [
                    'type' => $associationIsApiCovered
                        ? 'unknown'
                        : $associationConfiguration['alias'],
                    'id'   => $singleAssociationEntity
                        ? $associationClassMetadata->getFieldValue($entity, $associationIdField)
                        : null
                ]
            ];
        } else {

            $multipleAssociationEntities = $classMetadata->getFieldValue($entity, $field);
            $structure = [];
            foreach ($multipleAssociationEntities as $multipleAssociationEntity) {

                $structure[] = [
                    'links' => $associationIsApiCovered
                        ? $this->getEntityLinks($multipleAssociationEntity, $associationConfiguration['alias'])
                        : [],
                    'data'  => [
                        'type' => $associationIsApiCovered
                            ? 'unknown'
                            : $associationConfiguration['alias'],
                        'id'   => $multipleAssociationEntity->getId()
                    ]
                ];
            }
        }

        return $structure;
    }

    /**
     * Get entity links
     *
     * @param object $entity Entity
     *
     * @return array Entity links
     */
    private function getEntityLinks($entity, $entityAlias)
    {
        return [
            'self' => $this
                ->routesBuilder
                ->getRoutePathByEntityAliasAndVerb(
                    $entityAlias,
                    'get',
                    ['id' => $entity->getId()]
                ),
            'bulk' => $this
                ->routesBuilder
                ->getRoutePathByEntityAliasAndVerb(
                    $entityAlias,
                    'get'
                ),
        ];
    }
}
