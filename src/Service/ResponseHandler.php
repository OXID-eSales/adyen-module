<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidSolutionCatalysts\Adyen\Core\Response;

class ResponseHandler
{
    protected Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function response(): Response
    {
        return $this->response;
    }
}
