<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPICancels;
use Adyen\AdyenException;

/**
 * @extendable-class
 */
class PaymentCancel extends PaymentBase
{
    private array $cancelResult = [];

    /** @var ModuleSettings */
    private ModuleSettings $moduleSettings;

    /** @var AdyenAPIResponseCancels */
    private AdyenAPIResponseCancels $APICancels;
    private OxNewService $oxNewService;

    public function __construct(
        ModuleSettings $moduleSettings,
        AdyenAPIResponseCancels $APICancels,
        OxNewService $oxNewService
    ) {
        $this->moduleSettings = $moduleSettings;
        $this->APICancels = $APICancels;
        $this->oxNewService = $oxNewService;
    }

    public function setCancelResult(array $cancelResult): void
    {
        $this->cancelResult = $cancelResult;
    }

    public function getCancelResult(): array
    {
        return $this->cancelResult;
    }

    /**
     * @param string $orderNr as unique reference
     */
    public function doAdyenCancel(string $pspReference, string $orderNr): bool
    {
        $result = false;

        $cancels = $this->oxNewService->oxNew(AdyenAPICancels::class);
        $cancels->setReference($orderNr);
        $cancels->setPspReference($pspReference);
        $cancels->setMerchantAccount($this->moduleSettings->getMerchantAccount());

        try {
            $resultCancel = $this->APICancels->setCancel($cancels);
            if (is_array($resultCancel)) {
                $this->setCancelResult($resultCancel);
                $result = true;
            }
        } catch (AdyenException $exception) {
            Registry::getLogger()->error("Error on setCancel call.", [$exception]);
            $this->setPaymentExecutionError(self::PAYMENT_ERROR_GENERIC);
        }
        return $result;
    }
}
