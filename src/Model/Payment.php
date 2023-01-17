<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\DataGetter;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

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
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return bool
     */
    public function isAdyenPayment(): bool
    {
        return Module::isAdyenPayment($this->getId());
    }

    /**
     * Checks if the payment method is show on Payment Controller
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return bool
     */
    public function showInPaymentCtrl(): bool
    {
        return ($this->isAdyenPayment() &&
            Module::showInPaymentCtrl($this->getId()) &&
            $this->getAdyenBoolData('oxactive') === true
        );
    }

    /**
     * Checks if the payment method is show on Order Controller
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return bool
     */
    public function showInOrderCtrl(): bool
    {
        return ($this->isAdyenPayment() &&
            !Module::showInPaymentCtrl($this->getId()) &&
            $this->getAdyenBoolData('oxactive') === true
        );
    }

    /**
     * Checks if the payment allow manual Capture
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return bool
     */
    public function isAdyenManualCapture(): bool
    {
        return (Module::isCaptureDelay($this->getId()) &&
            $this->getServiceFromContainer(ModuleSettings::class)
                ->isManualCapture($this->getId())
        );
    }

    /**
     * Checks if the payment allow immediate Capture
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return bool
     */
    public function isAdyenImmediateCapture(): bool
    {
        return (Module::isCaptureDelay($this->getId()) &&
            $this->getServiceFromContainer(ModuleSettings::class)
                ->isImmediateCapture($this->getId())
        );
    }
}
