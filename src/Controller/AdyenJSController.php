<?php

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidSolutionCatalysts\Adyen\Service\ResponseHandler;
use OxidSolutionCatalysts\Adyen\Traits\AdyenAPI;

class AdyenJSController extends FrontendController
{
    use AdyenAPI;

    /**
     * @throws \Adyen\AdyenException
     * @throws \JsonException
     */
    public function payments(): void
    {
        $response = $this->getServiceFromContainer(ResponseHandler::class)->response();

        $response->setData(
            ['WIP']
        )->sendJson();
    }
}
