<?php

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\Eshop\Application\Model\User;
use OxidSolutionCatalysts\Adyen\Service\Controller\PaymentJSControllerService;
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
        $sessionSettings = $this->getServiceFromContainer(SessionSettings::class);
        $paymentJSControllerService = $this->getServiceFromContainer(PaymentJSControllerService::class);

        $basket = $sessionSettings->getBasket();
        $amount = $basket->getPrice()->getBruttoPrice();
        $orderReference = $sessionSettings->getOrderReference();
        $pspReference = $sessionSettings->getPspReference();

        $postData = $this->jsonToArray($this->getJsonPostData());

        if (!$amount || !isset($postData['paymentMethod'])) {
            $response->setNotFound()->sendJson(); // core code exits here
            return; // for unit tests
        }

        $paymentJSControllerService->cancelPaymentIfNecessary($pspReference, $amount, $orderReference);

        $orderReference = $paymentJSControllerService->createOrderReference($orderReference, $amount);

        /** @var Payment $paymentService */
        $paymentService = $this->getServiceFromContainer(Payment::class);
        /** @var User $user */
        $user = $this->getUser();
        /** @var ViewConfig $viewConfig */
        $viewConfig = $this->getViewConfig();
        $paymentService->collectPayments($amount, $orderReference, $postData, $user, $viewConfig);
        $payments = $paymentService->getPaymentResult();

        $response->setData(
            $payments
        )->sendJson();
    }

    public function details(): void
    {
        $response = $this->getServiceFromContainer(ResponseHandler::class)->response();

        $postData = $this->jsonToArray($this->getJsonPostData());

        if (!isset($postData['details'])) {
            $response->setNotFound()->sendJson(); // core code exits here
            return; // for unit tests
        }

        /** @var PaymentDetails $paymentService */
        $paymentService = $this->getServiceFromContainer(PaymentDetails::class);
        $paymentService->collectPaymentDetails($postData);
        $paymentDetails = $paymentService->getPaymentDetailsResult();

        $response->setData(
            $paymentDetails
        )->sendJson();
    }
}
