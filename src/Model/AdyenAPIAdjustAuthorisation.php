<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

class AdyenAPIAdjustAuthorisation
{
    protected string $reference;

    protected string $pspReference;

    protected string $merchantAccount;

    protected string $currencyName;

    protected string $currencyAmount;

    protected string $adjustAuthorisationData;

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

    public function setAdjustAuthorisationData(string $adjustAuthorisationData): void
    {
        $this->adjustAuthorisationData = $adjustAuthorisationData;
    }

    public function getAdyenAdjustAuthorisationParams(): array
    {
        return [
            'reference' => $this->reference,
            'merchantAccount' => $this->merchantAccount,
            'originalReference' => $this->pspReference,
            'modificationAmount' => [
                'currency' => $this->currencyName,
                'value' => $this->currencyAmount,
            ],
            'additionalData' => $this->adjustAuthorisationData
        ];
    }
}
