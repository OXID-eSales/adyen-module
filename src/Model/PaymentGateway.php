<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidSolutionCatalysts\Adyen\Core\AdyenSession;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidEsales\Eshop\Application\Model\Order as eShopOrder;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Model\PaymentGateway
 */
class PaymentGateway extends PaymentGateway_parent
{
    use ServiceContainer;

    /**
     * @inheritDoc
     *
     * @param double $amount Goods amount
     * @param object $order  User ordering object
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function executePayment($amount, &$order): bool
    {
        $paymentId = AdyenSession::getSessionPaymentId();

        if (!Module::isAdyenPayment($paymentId)) {
            return parent::executePayment($amount, $order);
        }
        /** @var eShopOrder $order */
        return $this->doExecuteAdyenPayment($amount, $order);
    }

    /**
     * @param double $amount Goods amount
     * @param eShopOrder $order User ordering object
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function doExecuteAdyenPayment($amount, $order): bool
    {
        $paymentService = $this->getServiceFromContainer(Payment::class);
        $context = $this->getServiceFromContainer(Context::class);
        $success = $paymentService->doAdyenAuthorization($amount, $order);

        if ($success) {
            $paymentResult = $paymentService->getPaymentResult();

            // everything is fine, we can save the references
            if (isset($paymentResult['pspReference'])) {
                $pspReference = $paymentResult['pspReference'];

                /** @var Order $order */
                $order->setAdyenPSPReference($pspReference);
                $order->save();

                $adyenHistory = oxNew(AdyenHistory::class);
                $adyenHistory->setPSPReference($pspReference);
                $adyenHistory->setParentPSPReference($pspReference);
                $adyenHistory->setOrderId($order->getId());
                $adyenHistory->setPrice((float)$order->getTotalOrderSum());
                $adyenHistory->setCurrency($context->getActiveCurrencyName());
                if (isset($paymentResult['resultCode'])) {
                    $adyenHistory->setAdyenStatus($paymentResult['resultCode']);
                }
                $adyenHistory->setAdyenAction(Module::ADYEN_ACTION_AUTHORIZE);
                $adyenHistory->save();
            }
            if (isset($paymentResult['action'])) {
                $action = $paymentResult['action'];
                if (
                    isset($action['type']) &&
                    $action['type'] === 'redirect' &&
                    isset($action['url'])
                ) {
                    AdyenSession::setRedirctLink($action['url']);
                    /** @var Order $order */
                    $this->_iLastErrorNo = (string)$order::ORDER_STATE_ADYENPAYMENTNEEDSREDICRET;
                }
            }
        }

        $this->_sLastError = $paymentService->getPaymentExecutionError();

        return $success;
    }
}
