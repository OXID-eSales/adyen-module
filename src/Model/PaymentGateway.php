<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidSolutionCatalysts\Adyen\Service\Module as ModuleService;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\RequestGetter;
use OxidEsales\Eshop\Application\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\PaymentGateway as PaymentGatewayService;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Model\PaymentGateway
 */
class PaymentGateway extends PaymentGateway_parent
{
    use RequestGetter;

    public function executePayment($amount, &$order): bool
    {
        $sessionSettings = $this->getServiceFromContainer(SessionSettings::class);
        $moduleService = $this->getServiceFromContainer(ModuleService::class);
        $payGatewayService = $this->getServiceFromContainer(PaymentGatewayService::class);
        $paymentId = $sessionSettings->getPaymentId();

        if ($moduleService->isAdyenPayment($paymentId)) {
            if (!$moduleService->showInPaymentCtrl($paymentId)) {
                $payGatewayService->doCollectAdyenRequestData();
            }
            /** @var Order $order */
            $payGatewayService->doFinishAdyenPayment($amount, $order);
        }

        return parent::executePayment($amount, $order);
    }
}
