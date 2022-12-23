<?php

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidSolutionCatalysts\Adyen\Service\PaymentDetails;
use OxidSolutionCatalysts\Adyen\Service\ResponseHandler;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\Json;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class AdyenJSController extends FrontendController
{
    use ServiceContainer;
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
        $postData = $this->jsonToArray($this->getJsonPostData());

        if (!$amount || !isset($postData['paymentMethod'])) {
            $response->setNotFound()->sendJson();
        }

        /** @var Payment $paymentService */
        $paymentService = $this->getServiceFromContainer(Payment::class);
        $paymentService->collectPayments($amount, $reference, $postData['paymentMethod']['paymentMethod']);
        $payments = $paymentService->getPaymentResult();

        $response->setData(
            $payments
        )->sendJson();
    }

    public function details(): void
    {
        $response = $this->getServiceFromContainer(ResponseHandler::class)->response();

        $postData = $this->jsonToArray($this->getJsonPostData());

        //if (!isset($postData['paymentMethod'])) {
        //    $response->setNotFound()->sendJson();
        //}

        /** @var PaymentDetails $paymentService */
        $paymentService = $this->getServiceFromContainer(Payment::class);
        $paymentService->collectPaymentDetails($postData);
        $paymentDetails = $paymentService->getPaymentDetailsResult();

        $response->setData(
            $paymentDetails
        )->sendJson();
    }
}
