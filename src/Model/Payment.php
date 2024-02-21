<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidSolutionCatalysts\Adyen\Service\PaymentConfigService;
use OxidSolutionCatalysts\Adyen\Traits\DataGetter;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Service\Module as ModuleService;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Model\Payment
 */
class Payment extends Payment_parent
{
    use ServiceContainer;
    use DataGetter;

    /**
     * Checks if the payment method is an Adyen payment method
     */
    public function isAdyenPayment(): bool
    {
        return $this->getAdyenPaymentConfigService()->isAdyenPayment($this->getId());
    }

    /**
     * Checks if the payment method is show on Payment Controller
     */
    public function showInPaymentCtrl(): bool
    {
        return ($this->isAdyenPayment() &&
            $this->getServiceFromContainer(ModuleService::class)->showInPaymentCtrl($this->getId()) &&
            $this->getAdyenBoolData('oxactive') === true
        );
    }

    /**
     * Checks if the payment method is show on Order Controller
     */
    public function showInOrderCtrl(): bool
    {
        return ($this->isAdyenPayment() &&
            !$this->getServiceFromContainer(ModuleService::class)->showInPaymentCtrl($this->getId()) &&
            $this->getAdyenBoolData('oxactive') === true
        );
    }

    /**
     * Checks if the payment method is show on Payment Controller
     */
    public function handleAdyenAssets(): bool
    {
        return ($this->isAdyenPayment() &&
            $this->getServiceFromContainer(ModuleService::class)->handleAssets($this->getId()) &&
            $this->getAdyenBoolData('oxactive') === true
        );
    }

    /**
     * Checks if the payment allow manual Capture
     */
    public function isAdyenManualCapture(): bool
    {
        return $this->getAdyenPaymentConfigService()->isAdyenManualCapture($this->getId());
    }

    /**
     * Checks if the payment allow immediate Capture
     */
    public function isAdyenImmediateCapture(): bool
    {
        return $this->getAdyenPaymentConfigService()->isAdyenImmediateCapture($this->getId());
    }

    /**
     * get the PaymentConfigService.
     * Normally this could be in the constructor. This model is used when the module is activated
     * and the services are not yet available at that moment. That's why it's outsourced here.
     */
    protected function getAdyenPaymentConfigService(): PaymentConfigService
    {
        return $this->getServiceFromContainer(PaymentConfigService::class);
    }
}
