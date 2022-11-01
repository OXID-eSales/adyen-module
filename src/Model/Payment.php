<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Model\Payment
 */
class Payment extends Payment_parent
{
    use ServiceContainer;

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
     * Checks if the payment method is an Adyen payment method and active
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return bool
     */
    public function isActiveAdyenPayment(): bool
    {
        return $this->isAdyenPayment() && $this->getFieldData('oxactive') === '1';
    }

    /**
     * Checks if the payment allow seperate Capture
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return bool
     */
    public function isAdyenSeperateCapture(): bool
    {
        return (Module::isSeperateCapture($this->getId()) &&
            $this->getServiceFromContainer(ModuleSettings::class)
                ->isSeperateCapture($this->getId())
        );
    }
}
