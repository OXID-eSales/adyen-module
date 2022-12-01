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
        $currencyAmountInt = $amount * $this->getDecimalFactor($currencyDecimals);
        return (string)$currencyAmountInt;
    }

    protected function getOxidAmount(float $amount, int $currencyDecimals): float
    {
        return $amount / $this->getDecimalFactor($currencyDecimals);
    }

    protected function getDecimalFactor(int $currencyDecimals): int
    {
        return (int)('1' . str_repeat('0', $currencyDecimals));
    }
}
