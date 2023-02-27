<?php

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\ViewConfig;
use OxidSolutionCatalysts\Adyen\Model\User;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidSolutionCatalysts\Adyen\Service\PaymentCancel;
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

        /** @var Basket $basket */
        $basket = Registry::getSession()->getBasket();
        $amount = $basket->getPrice()->getBruttoPrice();
        $orderReference = $session->getOrderReference();
        $pspReference = $session->getPspReference();

        $postData = $this->jsonToArray($this->getJsonPostData());

        if (!$amount || !isset($postData['paymentMethod'])) {
            $response->setNotFound()->sendJson();
        }

        // check if a AdyenAuthorisation exists and a cancel is necessary
        if ($pspReference && $session->getAmountValue() < $amount) {
            $paymentCancel = $this->getServiceFromContainer(PaymentCancel::class);
            $paymentCancel->doAdyenCancel(
                $pspReference,
                $orderReference
            );
        }

        // no orderReference? create!
        // and save the amount to the session for AdyenAuthorisation-check
        if (!$orderReference) {
            $orderReference = $session->createOrderReference();
            $session->setAmountValue($amount);
        }

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
            $response->setNotFound()->sendJson();
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
