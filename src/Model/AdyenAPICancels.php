<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

class AdyenAPICancels
{
    protected string $reference;

    protected string $pspReference;

    protected string $merchantAccount;

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

    public function getAdyenCancelParams(): array
    {
        return [
            'reference' => $this->reference,
            'merchantAccount' => $this->merchantAccount,
            'paymentPspReference' => $this->pspReference
        ];
    }
}
