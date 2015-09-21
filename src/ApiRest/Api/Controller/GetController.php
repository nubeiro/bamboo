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

namespace ApiRest\Api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

use ApiRest\Api\Controller\Abstracts\AbstractApiController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GetController
 */
class GetController extends AbstractApiController
{
    /**
     * Do action
     *
     * @param Request $request Request
     * @param object  $entity  Valid entity instance
     *
     * @return array Response data
     */
    protected function doOneAction(
        Request $request,
        $entity
    )
    {
        $entityStructure = $this->transformToApi($entity, $this->entityAlias);

        return new JsonResponse([
            'data' => $entityStructure
        ], 200);
    }

    /**
     * Do bulk action
     *
     * @param Request $request Request
     *
     * @return array Response data
     */
    protected function doBulkAction(Request $request)
    {
        $sort = $request
            ->query
            ->get('sort', null);

        $sortElementsComputed = [];

        if ($sort) {
            $sortElements = explode(',', $sort);
            foreach ($sortElements as $sortElement) {
                $trimmedSortElement = ltrim($sortElement, '-+');
                $sortElementsComputed[$trimmedSortElement] = strpos($sortElement, '-') === 0
                    ? 'DESC'
                    : 'ASC';
            }
        }


        $limit = $request
            ->query
            ->get('limit', 10);
        $limit = max($limit, 1);

        $page = $request
            ->query
            ->get('page', 1);
        $page = max($page, 1);
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this
            ->entityManager
            ->createQueryBuilder()
            ->select('x')
            ->from($this->entityNamespace, 'x')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        foreach ($sortElementsComputed as $sortElement => $order) {
            $queryBuilder->addOrderBy('x.' . $sortElement, $order);
        }

        $entities = $queryBuilder
            ->getQuery()
            ->getResult();

        $entitiesStructure = [];

        foreach ($entities as $entity) {

            $entitiesStructure[] = $this->transformToApi($this->entityAlias, $entity);
        }

        return new JsonResponse([
            'data' => $entitiesStructure
        ], 200);
    }
}
