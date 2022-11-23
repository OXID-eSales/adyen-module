<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPICancels;

/**
 * @extendable-class
 */
class PaymentCancel
{
    private ?array $cancelResult = null;

    /** @var ModuleSettings */
    private ModuleSettings $moduleSettings;

    /** @var AdyenAPIResponseCancels */
    private AdyenAPIResponseCancels $APICancels;

    public function __construct(
        ModuleSettings $moduleSettings,
        AdyenAPIResponseCancels $APICancels
    ) {
        $this->moduleSettings = $moduleSettings;
        $this->APICancels = $APICancels;
    }

    public function setCancelResult(array $cancelResult): void
    {
        $this->cancelResult = $cancelResult;
    }

    /** @return mixed */
    public function getCancelResult()
    {
        return $this->cancelResult;
    }

    /**
     * @param string $orderNr as unique reference
     */
    public function doAdyenCancel(string $pspReference, string $orderNr): bool
    {
        $result = false;

        $cancels = oxNew(AdyenAPICancels::class);
        $cancels->setReference($orderNr);
        $cancels->setPspReference($pspReference);
        $cancels->setMerchantAccount($this->moduleSettings->getMerchantAccount());


        try {
            $result = $this->APICancels->setCancel($cancels);
            $this->setCancelResult($result);
            $result = true;
        } catch (\Adyen\AdyenException $exception) {
            Registry::getLogger()->error("Error on setCancel call.", [$exception]);
        }
        return $result;
    }
}
