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

use ApiRest\Api\Response\ApiResponse;
use SimpleBus\Message\Bus\MessageBus;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class AbstractApiController
 */
abstract class AbstractApiController
{
    /**
     * @var MessageBus
     *
     * Message bus
     */
    private $messageBus;

    /**
     * @var ApiResponse
     *
     * Api response
     */
    private $apiResponse;

    /**
     * Constructor
     *
     * @param MessageBus  $messageBus  Message bus
     * @param ApiResponse $apiResponse Api response
     */
    public function __construct(
        MessageBus $messageBus,
        ApiResponse $apiResponse
    )
    {
        $this->messageBus = $messageBus;
        $this->apiResponse = $apiResponse;
    }

    /**
     * Get message bus
     *
     * @return MessageBus Get message bus
     */
    public function getMessageBus()
    {
        return $this->messageBus;
    }

    /**
     * Get ApiResponse
     *
     * @return JsonResponse Response with $apiResponse in a json format
     */
    public function createResponseFromApiResponse()
    {
        return new JsonResponse(
            $this
                ->apiResponse
                ->serialize()
        );
    }

    /**
     * Do action
     *
     * This method works as a simple entry point
     *
     * @param Request $request Request
     * @param mixed   $id      Entity id
     *
     * @return JsonResponse Response
     */
    public function doAction(
        Request $request,
        $id = null
    )
    {
        $id === null
            ? $this->doBulkAction($request)
            : $this->doOneAction($request, $id);

        return $this->createResponseFromApiResponse();
    }

    /**
     * Do action
     *
     * @param Request $request  Request
     * @param string  $entityId Entity identifier
     */
    abstract protected function doOneAction(
        Request $request,
        $entityId
    );

    /**
     * Do bulk action
     *
     * @param Request $request Request
     */
    abstract protected function doBulkAction(Request $request);
}
