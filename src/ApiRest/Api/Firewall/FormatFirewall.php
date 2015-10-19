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
 
namespace ApiRest\Api\Firewall;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class FormatFirewall
 */
final class FormatFirewall
{
    /**
     */
    private function filter(GetResponseEvent $event)
    {
        $request = $event->getRequest();

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
            return new JsonResponse('', 415);
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
    }
}
