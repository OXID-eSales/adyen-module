<?php

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidSolutionCatalysts\Adyen\Service\ResponseHandler;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\AdyenAPI;
use OxidSolutionCatalysts\Adyen\Traits\Json;

class AdyenJSController extends FrontendController
{
    use AdyenAPI;
    use Json;

    /**
     * @throws \JsonException
     */
    public function payments(): void
    {
        $response = $this->getServiceFromContainer(ResponseHandler::class)->response();
        $session = $this->getServiceFromContainer(SessionSettings::class);
        $reference = $session->createOrderReference();

        /** @var Basket $basket */
        $basket = Registry::getSession()->getBasket();
        $amount = $basket->getPrice()->getBruttoPrice();
        $paymentMethod = $this->jsonToArray($this->getJsonPostData());

        if (!$amount || !isset($paymentMethod['paymentMethod'])) {
            $response->setNotFound()->sendJson();
        }

        /** @var Payment $paymentService */
        $paymentService = $this->getServiceFromContainer(Payment::class);
        $paymentService->collectPayments($amount, $reference, $paymentMethod['paymentMethod']['paymentMethod']);
        $payments = $paymentService->getPaymentResult();

        $response->setData(
            $payments
        )->sendJson();
    }
}
