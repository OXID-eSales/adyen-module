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
    public function getAdyenJsonSession(): void
    {
        $response = $this->getServiceFromContainer(ResponseHandler::class)->response();

        $response->setData([
            'id' => $this->getAdyenSessionId(),
            'data' => $this->getAdyenSessionData()
        ])->sendJson();
    }

    /**
     * @throws \Adyen\AdyenException
     * @throws \JsonException
     */
    public function getAdyenJsonPaymentMethods(): void
    {
        $response = $this->getServiceFromContainer(ResponseHandler::class)->response();

        $response->setData([
            'paymentMethods' => $this->getAdyenPaymentMethodsData()->getAdyenPaymentMethods()
        ])->sendJson();
    }
}
