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

use ApiRest\Api\Controller\Abstracts\AbstractApiController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PutController
 */
class PutController extends AbstractApiController
{
    /**
     * Do action
     *
     * @param Request $request  Request
     * @param string  $entityId Entity identifier
     */
    protected function doOneAction(
        Request $request,
        $entityId
    )
    {
        // TODO: Implement doOneAction() method.
    }

    /**
     * Do bulk action
     *
     * @param Request $request Request
     */
    protected function doBulkAction(Request $request)
    {
        // TODO: Implement doBulkAction() method.
    }
}
