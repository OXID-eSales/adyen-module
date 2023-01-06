<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
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
     */
    public function executePayment($amount, &$order): bool
    {
        $session = $this->getServiceFromContainer(SessionSettings::class);
        $paymentId = $session->getPaymentId();

        if (!Module::isAdyenPayment($paymentId)) {
            return parent::executePayment($amount, $order);
        }

        // put RequestData from OrderCtrl in the session as well as from PaymentCtrl
        if (!Module::showInPaymentCtrl($paymentId)) {
            $pspReference = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_PSPREFERENCE_NAME);
            $resultCode = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_RESULTCODE_NAME);
            $amountCurrency = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_AMOUNTCURRENCY_NAME);
            $session->setPspReference($pspReference);
            $session->setResultCode($resultCode);
            $session->setAmountCurrency($amountCurrency);
        }

        return $this->doFinishAdyenPayment($amount, $order);

//        if (!Module::showInPaymentCtrl($paymentId)) {
//            /** @var eShopOrder $order */
//            return $this->doFinishAdyenPayment($amount, $order);
//        }
//        /** @var eShopOrder $order */
//        return $this->doExecuteAdyenPayment($amount, $order);
    }

    /**
     * @param double $amount Goods amount
     * @param eShopOrder $order User ordering object
     */
//    protected function doExecuteAdyenPayment(float $amount, eShopOrder $order): bool
//    {
//        $session = $this->getServiceFromContainer(SessionSettings::class);
//        $paymentService = $this->getServiceFromContainer(Payment::class);
//        $context = $this->getServiceFromContainer(Context::class);
//
//        /** @var Order $order */
//        $orderReference = $order->getAdyenOrderReference();
//        $success = $paymentService->doAdyenAuthorization($amount, $orderReference);
//
//        if ($success) {
//            $paymentResult = $paymentService->getPaymentResult();
//
//            // everything is fine, we can save the references
//            if (isset($paymentResult['pspReference'])) {
//                $pspReference = $paymentResult['pspReference'];
//
//                $order->setAdyenPSPReference($pspReference);
//                $order->save();
//                $order->setAdyenHistoryEntry(
//                    $pspReference,
//                    $pspReference,
//                    $order->getId(),
//                    $amount,
//                    $context->getActiveCurrencyName(),
//                    $paymentResult['resultCode'] ?? '',
//                    Module::ADYEN_ACTION_AUTHORIZE
//                );
//            }
//            if (isset($paymentResult['action'])) {
//                $action = $paymentResult['action'];
//                if (
//                    isset($action['type']) &&
//                    $action['type'] === 'redirect' &&
//                    isset($action['url'])
//                ) {
//                    $session->setRedirctLink((string)$action['url']);
//                    /** @var Order $order */
//                    $this->_iLastErrorNo = (string)$order::ORDER_STATE_ADYENPAYMENTNEEDSREDICRET;
//                }
//            }
//        }
//
//        $this->_sLastError = $paymentService->getPaymentExecutionError();
//
//        return $success;
//    }

    /**
     * @param double $amount Goods amount
     * @param eShopOrder $order User ordering object
     */
    protected function doFinishAdyenPayment($amount, $order): bool
    {
        $success = false;

        $session = $this->getServiceFromContainer(SessionSettings::class);

        $pspReference = $session->getPspReference();
        $resultCode = $session->getResultCode();
        $amountCurrency = $session->getAmountCurrency();
        $orderReference = $session->getOrderReference();

        // everything is fine, we can save the references
        if ($pspReference && $resultCode && $orderReference) {
            // not necessary anymore, so cleanup
            $session->deletePspReference();
            $session->deleteResultCode();
            $session->deleteAmountCurrency();
            $session->deleteOrderReference();

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

            $success = true;
        }
        return $success;
    }
}
