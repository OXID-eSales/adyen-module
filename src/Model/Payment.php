<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidSolutionCatalysts\Adyen\Core\Module;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Model\Payment
 */
class Payment extends Payment_parent
{
    /**
     * Checks if the payment method is an Adyen payment method
     *
     * @return bool
     */
    public function isAdyenPayment(): bool
    {
        return Module::isAdyenPayment($this->getId());
    }
}
