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

namespace ApiRest\Api\Controller\Abstracts;

use ApiRest\Api\ApiRoutes;
use ApiRest\Api\Configuration;
use ApiRest\Api\Transformer\Interfaces\ApiTransformerInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Elcodi\Component\Core\Services\ManagerProvider;
use Elcodi\Component\Core\Services\RepositoryProvider;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class AbstractApiController
 */
abstract class AbstractApiController
{
    /**
     * @var ObjectRepository
     *
     * Object Repository
     */
    protected $objectRepository;

    /**
     * @var EntityManager
     *
     * Object manager
     */
    protected $entityManager;

    /**
     * @var Request
     *
     * Request
     */
    private $request;

    /**
     * @var ApiTransformerInterface
     *
     * Apì transformer
     */
    private $apiTransformer;

    /**
     * @param array
     *
     * Entity configuration
     */
    protected $entityConfiguration;

    /**
     * @param string
     *
     * Route format
     */
    protected $routeFormat;

    /**
     * @param string
     *
     * Verb
     */
    private $verb;

    /**
     * @param string
     *
     * Entity alias
     */
    protected $entityAlias;

    /**
     * @param string
     *
     * Entity namespace
     */
    protected $entityNamespace;

    /**
     * Constructor
     *
     * @param RepositoryProvider      $repositoryProvider Repository provider
     * @param ManagerProvider         $managerProvider    Manager provider
     * @param RequestStack            $requestStack       Request stack
     * @param ApiTransformerInterface $apiTransformer     Api transformer
     * @param Configuration           $configuration      Configuration
     */
    public function __construct(
        RepositoryProvider $repositoryProvider,
        ManagerProvider $managerProvider,
        RequestStack $requestStack,
        ApiTransformerInterface $apiTransformer,
        Configuration $configuration
    )
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->routeFormat = $configuration->getRouteFormat();
        $this->apiTransformer = $apiTransformer;
        $this->loadRequestEnvironment();

        $this->entityConfiguration = $configuration->getEntityConfigurationByAlias($this->entityAlias);
        $this->entityNamespace = $this->entityConfiguration['namespace'];
        $this->objectRepository = $repositoryProvider->getRepositoryByEntityNamespace($this->entityNamespace);
        $this->entityManager = $managerProvider->getManagerByEntityNamespace($this->entityNamespace);


    }

    /**
     * Do action
     *
     * This method works as a simple entry point
     *
     * @param mixed $id Entity id
     *
     * @return JsonResponse Response
     */
    public function doAction($id = null)
    {
        $checkedResponse = $this->checkRequest($this->request);

        if ($checkedResponse instanceof JsonResponse) {
            return $checkedResponse;
        }

        if ($id === null) {

            $response = $this->doBulkAction($this->request);
        } else {

            $entity = $this
                ->objectRepository
                ->find($id);

            if (!empty($entity)) {

                $response = $this
                    ->doOneAction(
                        $this->request,
                        $entity
                    );
            } else {
                $response = new JsonResponse([
                    'Not found'
                ], 404);
            }
        }

        $this->completeResponse($response);

        return $response;
    }

    /**
     * Do action
     *
     * @param Request $request Request
     * @param object  $entity  Valid entity instance
     *
     * @return array Response data
     */
    abstract protected function doOneAction(
        Request $request,
        $entity
    );

    /**
     * Do bulk action
     *
     * @param Request $request Request
     *
     * @return array Response data
     */
    abstract protected function doBulkAction(Request $request);

    /**
     * Load all request environment
     *     * Entity alias
     *     * Verb
     *
     * and set all this data locally
     *
     * @return $this Self object
     */
    private function loadRequestEnvironment()
    {
        $route = $this->getRoute();
        $pattern = str_replace(
            [
                '{entity}',
                '{verb}'
            ],
            [
                '(?P<entity>\w+)',
                '(?P<verb>\w+)'
            ],
            $this->routeFormat
        );

        preg_match(
            '~' . $pattern . '~',
            $route,
            $matches
        );

        $this->entityAlias = isset($matches['entity'])
            ? $matches['entity']
            : null;

        $this->verb = isset($matches['verb'])
            ? $matches['verb']
            : null;

        return $this;
    }

    /**
     * Get uri
     *
     * @return string Current uri
     */
    protected function getUri()
    {
        return $this
            ->request
            ->getUri();
    }

    /**
     * Get route
     *
     * @return string Current route
     */
    protected function getRoute()
    {
        return $this
            ->request
            ->get('_route');
    }

    /**
     * Transform an entity to a API-valid element
     *
     * @param object $entity      Entity
     * @param string $entityAlias Entity alias
     *
     * @return array Data transformed
     */
    public function transformToApi($entity, $entityAlias)
    {
        return $this
            ->apiTransformer
            ->transform($entity, $entityAlias);
    }

    /**
     * Get first letter of namespace
     *
     * @return string First letter
     */
    protected function getFirstLetterNamespace()
    {
        return substr(0, 1, $this->entityNamespace);
    }

    /**
     * Complete response
     *
     * @param JsonResponse $response Response
     */
    private function completeResponse(JsonResponse $response)
    {
        $response
            ->headers
            ->set(
                'Content-Type',
                'application/vnd.api+json'
            );

        $responseData = $response->getContent();
        $content['jsonapi'] = [
            'version' => '1.0'
        ];
        $response->setContent($content);
    }

    /**
     * Check request
     *
     * @param Request $request Request
     *
     * @return JsonResponse|null Error Response if needed
     */
    private function checkRequest(Request $request)
    {
        /**
         * Servers MUST respond with a 415 Unsupported Media Type status code if
         * a request specifies the header Content-Type: application/vnd.api+json
         * with any media type parameters.
         *
         * @link http://jsonapi.org/format/#content-negotiation-servers
         */
        $requestContentType = $request
            ->headers
            ->get('Content-Type');

        if ($requestContentType !== 'application/vnd.api+json') {
            //return new JsonResponse('', 415);
        }

        /**
         * Servers MUST respond with a 406 Not Acceptable status code if a
         * request's Accept header contains the JSON API media type and all
         * instances of that media type are modified with media type parameters.
         */
        $requestAccept = $request
            ->headers
            ->get('Accept');

        /**
         * @todo
         */

        /**
         * Checking route validity, so the call can be safely done and processed
         */
        if (!$this->getRouteValidity()) {
            return new JsonResponse('', 403);
        }
    }

    /**
     * Check call
     *
     * @return boolean this route is valid
     */
    private function getRouteValidity()
    {
        $verbCode = ApiRoutes::toCode($this->verb);
        $level = $this->entityConfiguration['level'];

        return $level & $verbCode;
    }
}
