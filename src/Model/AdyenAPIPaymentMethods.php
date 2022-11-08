<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

class AdyenAPIPaymentMethods
{
    protected string $currencyName;

    protected string $currencyFilterAmount;

    protected string $countryCode;

    protected string $shopperLocale;

    protected string $merchantAccount;

    public function setCurrencyName(string $currencyName): void
    {
        $this->currencyName = $currencyName;
    }

    public function setCurrencyFilterAmount(string $currencyFilterAmount): void
    {
        $this->currencyFilterAmount = $currencyFilterAmount;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function setShopperLocale(string $shopperLocale): void
    {
        $this->shopperLocale = $shopperLocale;
    }

    public function setMerchantAccount(string $merchantAccount): void
    {
        $this->merchantAccount = $merchantAccount;
    }

    public function getAdyenSessionParams(): array
    {
        return [
            'amount' => [
                'currency' => $this->currencyName,
                'value' => $this->currencyFilterAmount,
            ],
            'countryCode' => $this->countryCode,
            'merchantAccount' => $this->merchantAccount,
            'shopperLocale' => $this->shopperLocale
        ];
    }
}
