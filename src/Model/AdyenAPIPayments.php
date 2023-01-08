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

    protected string $origin;

    protected string $shopperEmail;

    protected string $shopperIP;

    protected bool $preAuth = false;

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

    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    public function setShopperEmail(string $shopperEmail): void
    {
        $this->shopperEmail = $shopperEmail;
    }

    public function setShopperIP(string $shopperIP): void
    {
        $this->shopperIP = $shopperIP;
    }

    public function setIsPreAuth(): void
    {
        $this->preAuth = true;
    }

    public function getAdyenPaymentsParams(): array
    {
        $result = [
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
            ],
            'shopperEmail' => $this->shopperEmail,
            'shopperIP' => $this->shopperIP,
            'authenticationData' => [
                'threeDSRequestData' => [
                    'nativeThreeDS' => 'preferred'
                ]
            ],
            'channel' => 'Web',
            'origin' => $this->origin
        ];

        if ($this->preAuth) {
            $result['additionalData']['authorisationType'] = "PreAuth";
        }

        return $result;
    }
}
