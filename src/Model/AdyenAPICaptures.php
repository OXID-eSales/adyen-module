<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

class AdyenAPICaptures
{
    protected string $reference;

    protected string $pspReference;

    protected string $merchantAccount;

    protected string $currencyName;

    protected string $currencyAmount;

    protected string $applicationName;

    protected string $applicationVersion;

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function setPspReference(string $pspReference): void
    {
        $this->pspReference = $pspReference;
    }

    public function setMerchantAccount(string $merchantAccount): void
    {
        $this->merchantAccount = $merchantAccount;
    }

    public function setCurrencyName(string $currencyName): void
    {
        $this->currencyName = $currencyName;
    }

    public function setCurrencyAmount(string $currencyAmount): void
    {
        $this->currencyAmount = $currencyAmount;
    }

    public function setMerchantApplicationName(string $applicationName): void
    {
        $this->applicationName = $applicationName;
    }

    public function setMerchantApplicationVersion(string $applicationVersion): void
    {
        $this->applicationVersion = $applicationVersion;
    }

    public function getAdyenCapturesParams(): array
    {
        return [
            'amount' => [
                'currency' => $this->currencyName,
                'value' => $this->currencyAmount,
            ],
            'reference' => $this->reference,
            'paymentPspReference' => $this->pspReference,
            'merchantAccount' => $this->merchantAccount,
            'applicationInfo' => [
                'merchantApplication' => [
                    'name' => $this->applicationName,
                    'version' => $this->applicationVersion
                ]
            ]
        ];
    }
}
