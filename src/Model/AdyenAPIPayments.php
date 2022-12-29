<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

class AdyenAPIPayments
{
    protected string $reference;

    protected array $paymentMethod;

    protected string $merchantAccount;

    protected string $returnUrl;

    protected string $currencyName;

    protected string $currencyAmount;

    protected string $applicationName;

    protected string $applicationVersion;
    protected array $browserInfo;

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function setPaymentMethod(array $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function setBrowserInfo(array $browserInfo): void
    {
        $this->browserInfo = $browserInfo;
    }

    public function setMerchantAccount(string $merchantAccount): void
    {
        $this->merchantAccount = $merchantAccount;
    }

    public function setReturnUrl(string $returnUrl): void
    {
        $this->returnUrl = $returnUrl;
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

    public function getAdyenPaymentsParams(): array
    {
        return [
            'paymentMethod' => $this->paymentMethod,
            'browserInfo' => $this->browserInfo,
            'amount' => [
                'currency' => $this->currencyName,
                'value' => $this->currencyAmount,
            ],
            'reference' => $this->reference,
            'returnUrl' => $this->returnUrl,
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
