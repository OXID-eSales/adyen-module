<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Traits;

use OxidEsales\Eshop\Core\Registry;

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

    /**
     * Resolve the number of decimals configured for a currency name, so Adyen minor units
     * can be converted currency-aware (JPY = 0, EUR = 2, KWD = 3, ...). Falls back to 2
     * decimals when the currency is unknown/misconfigured.
     */
    protected function getCurrencyDecimalsByName(string $currencyName): int
    {
        $currencyObj = Registry::getConfig()->getCurrencyObject($currencyName);
        if (is_object($currencyObj) && isset($currencyObj->decimal) && is_numeric($currencyObj->decimal)) {
            return (int)$currencyObj->decimal;
        }

        return 2;
    }
}
