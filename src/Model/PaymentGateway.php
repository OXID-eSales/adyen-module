<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\AdjustAuthorisation;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidSolutionCatalysts\Adyen\Service\PaymentCancel;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\RequestGetter;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidEsales\Eshop\Application\Model\Order as eShopOrder;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Model\PaymentGateway
 */
class PaymentGateway extends PaymentGateway_parent
{
    use ServiceContainer;
    use RequestGetter;

    /**
     * @inheritDoc
     *
     * @param double $amount Goods amount
     * @param object $order  User ordering object
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function executePayment($amount, &$order): bool
    {
        $session = $this->getServiceFromContainer(SessionSettings::class);
        $paymentId = $session->getPaymentId();

        if (Module::isAdyenPayment($paymentId)) {
            if (!Module::showInPaymentCtrl($paymentId)) {
                $this->doCollectAdyenRequestData();
            }
            /** @var eShopOrder $order */
            $this->doFinishAdyenPayment($amount, $order);
        }

        return parent::executePayment($amount, $order);
    }

    protected function doCollectAdyenRequestData(): void
    {
        $session = $this->getServiceFromContainer(SessionSettings::class);
        // put RequestData from OrderCtrl in the session as well as from PaymentCtrl
        $pspReference = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_PSPREFERENCE_NAME);
        $resultCode = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_RESULTCODE_NAME);
        $amountCurrency = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_AMOUNTCURRENCY_NAME);
        $session->setPspReference($pspReference);
        $session->setResultCode($resultCode);
        $session->setAmountCurrency($amountCurrency);
    }

    /**
     * @param double $amount Goods amount
     * @param eShopOrder $order User ordering object
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function doFinishAdyenPayment($amount, $order): bool
    {
        $success = false;

        $session = $this->getServiceFromContainer(SessionSettings::class);
        $paymentId = $session->getPaymentId();

        $pspReference = $session->getPspReference();
        $resultCode = $session->getResultCode();
        $amountCurrency = $session->getAmountCurrency();
        $orderReference = $session->getOrderReference();

        // everything is fine, we can save the references
        if ($pspReference && $resultCode && $orderReference) {
            // not necessary anymore, so cleanup
            $session->deletePaymentSession();

            /** @var Order $order */
            $order->setAdyenOrderReference($orderReference);
            $order->setAdyenPSPReference($pspReference);
            $order->setAdyenHistoryEntry(
                $pspReference,
                $pspReference,
                $order->getId(),
                $amount,
                $amountCurrency,
                $resultCode,
                Module::ADYEN_ACTION_AUTHORIZE
            );
            $order->save();

            // trigger Capture for all PaymentCtrl-Payments with Capture-Delay "immediate"
            if (Module::showInPaymentCtrl($paymentId)) {
                $order->captureAdyenOrder($amount);
            }

            $success = true;
        }
        return $success;
    }
}
