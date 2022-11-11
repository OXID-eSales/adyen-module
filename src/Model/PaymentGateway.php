<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidEsales\Eshop\Application\Model\Order;

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
     *
     * @return bool
     */
    public function executePayment($amount, &$order): bool
    {
        $paymentService = $this->getServiceFromContainer(Payment::class);
        $paymentId = $paymentService->getSessionPaymentId();

        if (!Module::isAdyenPayment($paymentId)) {
            return parent::executePayment($amount, $order);
        }
        /** @var Order $order */
        return $this->doExecuteAdyenPayment($order);
    }

    protected function doExecuteAdyenPayment(Order $order): bool
    {
        $paymentService = $this->getServiceFromContainer(Payment::class);
        $paymentId = $paymentService->getSessionPaymentId();
        $success = false;

        return $success;
    }
}
