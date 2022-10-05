<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

class AdyenAPISession
{
    protected string $currencyName;

    protected string $currencyFilterAmount;

    protected string $countryCode;

    protected string $merchantAccount;

    protected string $reference;

    protected string $returnUrl;

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

    public function setMerchantAccount(string $merchantAccount): void
    {
        $this->merchantAccount = $merchantAccount;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function setReturnUrl(string $returnUrl): void
    {
        $this->returnUrl = $returnUrl;
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
            'reference' => $this->reference,
            'returnUrl' => $this->returnUrl
        ];
    }
}
