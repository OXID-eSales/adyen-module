<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Traits;

/**
 * Convenience trait to fetch Adyen API Payment Services.
 */
trait AdyenPayment
{
    protected function getAdyenAmount(float $amount, int $currencyDecimals): string
    {
        $decimalFactor = (int)('1' . str_repeat('0', $currencyDecimals));
        $currencyAmountInt = $amount * $decimalFactor;
        return (string)$currencyAmountInt;
    }
}
