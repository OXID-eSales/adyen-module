<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Application\Controller\PaymentController;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Service\TranslationMapper;
use OxidSolutionCatalysts\Adyen\Service\OrderReturnService;
use OxidSolutionCatalysts\Adyen\Service\JSAPITemplateCheckoutCreate;
use OxidSolutionCatalysts\Adyen\Service\PaymentCancel;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Core\Module;

class OrderController extends OrderController_parent
{
    use ServiceContainer;

    /**
     * shopper came back from adyen, because of authorization, cancellation, error or refused
     */
    public function return(): ?string
    {
        $orderReturnService = $this->getServiceFromContainer(OrderReturnService::class);

        if (!$orderReturnService->isRedirectedFromAdyen()) {
            Registry::getUtils()->redirect(Registry::getConfig()->getShopHomeUrl() . 'cl=start');
        }

        if ($orderReturnService->isRedirectedFromAdyen()) {
            $paymentDetail = $orderReturnService->getPaymentDetails();
            if (
                $paymentDetail['resultCode'] === Module::ADYEN_RETURN_RESULT_CODE_AUTHORISED
                || $paymentDetail['resultCode'] === Module::ADYEN_RETURN_RESULT_CODE_RECEIVED
            ) {
                // Safety net for bug 0007976: if the shopper changed the basket in a
                // parallel tab/session after the redirect was started, Adyen may have
                // authorized less than the current cart total. Finalizing would create an
                // under-authorized order (an over-capture waiting to happen, especially
                // with delayed/two-step capture). Cancel the dangling authorization and
                // keep the shopper on the order page to pay again for the correct amount.
                if (
                    !$orderReturnService->isAuthorizedAmountSufficient(
                        $paymentDetail,
                        $this->getAdyenBasketBruttoPrice()
                    )
                ) {
                    $this->cancelPendingAdyenAuthorization();
                    $this->addTplParam(
                        'paymentReturnReason',
                        TranslationMapper::OSC_ADYEN_RETURN_REASON_AMOUNT_MISMATCH
                    );
                    $this->addTplParam('paymentResultCode', $paymentDetail['resultCode']);
                    return null;
                }

                $this->reinstateAgreementsAfterRedirect();
                return $this->execute();
            }

            $translationMapper = $this->getServiceFromContainer(TranslationMapper::class);
            $this->addTplParam(
                'paymentReturnReason',
                $translationMapper->mapReturnResultCode($paymentDetail['resultCode'])
            );
            $this->addTplParam('paymentResultCode', $paymentDetail['resultCode']);
        }

        return null;
    }

    /*
     * before the adyen credit card payment can be finalized,
     * it needs to be validated because this step is skipped for this payment type
     */
    public function execute()
    {
        /** @var Payment $payment */
        $payment = $this->getPayment();
        if ($payment->isAdyenCreditCardPayment()) {
            $oxNewService = $this->getServiceFromContainer(OxNewService::class);
            $paymentController = $oxNewService->oxNew(PaymentController::class);

            if ($paymentController->validatePayment() !== "order") {
                Registry::getUtils()->redirect(Registry::getConfig()->getShopHomeUrl() . 'cl=payment');
            }
        }

        return parent::execute();
    }

    /**
     * For redirect-based Adyen methods (Twint, Klarna) the shopper leaves the
     * order page completely; on return only `redirectResult` is in the URL,
     * so the AGB hidden fields submitted with the original order form are
     * gone. The JS guard in onSubmit blocks the payment unless the shopper
     * has accepted the agreements first, so reaching this point implies they
     * were accepted. Re-inject them into the request so OXID's standard
     * execute() does not reject the order.
     */
    private function reinstateAgreementsAfterRedirect(): void
    {
        $payment = $this->getPayment();
        if (!($payment instanceof Payment)) {
            return;
        }
        $checkoutCreateService = $this->getServiceFromContainer(JSAPITemplateCheckoutCreate::class);
        if (!$checkoutCreateService->isRedirectPayment($payment->getId())) {
            return;
        }

        foreach (['ord_agb', 'oxdownloadableproductsagreement', 'oxserviceproductsagreement'] as $param) {
            $_POST[$param] = '1';
        }
    }

    /**
     * Current basket gross total, i.e. the amount the order would actually be charged for.
     * Returns 0.0 when no basket is available, which the amount check treats as "cannot
     * compare" (does not block the order).
     */
    private function getAdyenBasketBruttoPrice(): float
    {
        $basket = $this->getBasket();
        $price = $basket ? $basket->getPrice() : null;

        return $price ? $price->getBruttoPrice() : 0.0;
    }

    /**
     * Cancel a still-pending Adyen authorization using the references kept in the session
     * (no order has been finalized at this point). Mirrors
     * Model\Order::removeAdyenPaymentFromSession() so no orphaned authorization remains.
     */
    private function cancelPendingAdyenAuthorization(): void
    {
        $session = $this->getServiceFromContainer(SessionSettings::class);
        $pspReference = $session->getPspReference();
        $orderReference = $session->getOrderReference();

        if ($pspReference && $orderReference) {
            $paymentCancel = $this->getServiceFromContainer(PaymentCancel::class);
            $paymentCancel->doAdyenCancel($pspReference, $orderReference);
            $session->deletePaymentSession();
        }
    }
}
