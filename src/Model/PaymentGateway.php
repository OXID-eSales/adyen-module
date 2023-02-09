<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\RequestGetter;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidEsales\Eshop\Application\Model\Order as eShopOrder;
use OxidSolutionCatalysts\Adyen\Service\PaymentGateway as PaymentGatewayService;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Model\PaymentGateway
 */
class PaymentGateway extends PaymentGateway_parent
{
    use ServiceContainer;
    use RequestGetter;

    private SessionSettings $sessionSettings;
    private PaymentGatewayService $payGatewayService;

    public function __construct(SessionSettings $sessionSettings, PaymentGatewayService $payGatewayService)
    {
        $this->sessionSettings = $sessionSettings;
        $this->payGatewayService = $payGatewayService;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * Todo: make static method none static
     */
    public function executePayment($amount, &$order): bool
    {
        $paymentId = $this->sessionSettings->getPaymentId();

        if (Module::isAdyenPayment($paymentId)) {
            if (!Module::showInPaymentCtrl($paymentId)) {
                $this->payGatewayService->doCollectAdyenRequestData();
            }
            /** @var eShopOrder $order */
            $this->payGatewayService->doFinishAdyenPayment($amount, $order);
        }

        return parent::executePayment($amount, $order);
    }
}
