<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Exception;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIAdjustAuthorisation;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPayments;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;

/**
 * @extendable-class
 */
class AdjustAuthorisation
{
    use AdyenPayment;

    public const ADJUST_AUTHORISATION_ERROR_NONE = 'ADYEN_ADJUST_AUTHORISATION_ERROR_NONE';
    public const ADJUST_AUTHORISATION_ERROR_GENERIC = 'ADYEN_ADJUST_AUTHORISATION_ERROR_GENERIC';

    private string $adjustAuthorisationError = self::ADJUST_AUTHORISATION_ERROR_NONE;

    private array $adjustAuthorisationResult = [];

    private SessionSettings $session;

    private Context $context;

    private ModuleSettings $moduleSettings;

    private AdyenAPIResponseAdjustAuthorisation $APIAdjustAuthorisation;

    public function __construct(
        SessionSettings $session,
        Context $context,
        ModuleSettings $moduleSettings,
        AdyenAPIResponseAdjustAuthorisation $APIAdjustAuthorisation
    ) {
        $this->session = $session;
        $this->context = $context;
        $this->moduleSettings = $moduleSettings;
        $this->APIAdjustAuthorisation = $APIAdjustAuthorisation;
    }

    public function setAdjustAuthorisationResult(array $adjustAuthorisationResult): void
    {
        $this->adjustAuthorisationResult = $adjustAuthorisationResult;
    }

    public function getAdjustAuthorisationResult(): array
    {
        return $this->adjustAuthorisationResult;
    }

    public function setAdjustAuthorisationError(string $text): void
    {
        $this->adjustAuthorisationError = $text;
    }

    public function getAdjustAuthorisationError(): string
    {
        return $this->adjustAuthorisationError;
    }

    /**
     * @param double $amount Goods amount
     */
    public function doAdyenAdjustAuthorisation(float $amount): bool
    {
        $pspReference = $this->session->getPspReference();
        $reference = $this->session->getOrderReference();
        $adjustAuthorisationData = $this->session->getAdjustAuthorisation();

        return $this->collectAdjustAuthorisation($amount, $reference, $pspReference, $adjustAuthorisationData);
    }

    /**
     * @param double $amount Goods amount
     * @param string $reference Unique Order-Reference
     * @param string $pspReference pspReference
     * @param string $adjustAuthorisationData The previous adjustAuthorisationData blob.
     */
    public function collectAdjustAuthorisation(float $amount, string $reference, string $pspReference, string $adjustAuthorisationData): bool
    {
        $result = false;

        $adjustAuthorisation = oxNew(AdyenAPIAdjustAuthorisation::class);
        $adjustAuthorisation->setCurrencyName($this->context->getActiveCurrencyName());
        $adjustAuthorisation->setReference($reference);
        $adjustAuthorisation->setPspReference($pspReference);
        $adjustAuthorisation->setAdjustAuthorisationData($adjustAuthorisationData);
        $adjustAuthorisation->setCurrencyAmount($this->getAdyenAmount(
            $amount,
            $this->context->getActiveCurrencyDecimals()
        ));
        $adjustAuthorisation->setMerchantAccount($this->moduleSettings->getMerchantAccount());

        try {
            $resultAdjustAuthorisation = $this->APIAdjustAuthorisation->getAdjustAuthorisation($adjustAuthorisation);
            if (is_array($resultAdjustAuthorisation)) {
                $this->setAdjustAuthorisationResult($resultAdjustAuthorisation);
                $result = true;
            }
        } catch (Exception $exception) {
            Registry::getLogger()->error("Error on getAdjustAuthorisation call.", [$exception]);
            $this->setPaymentExecutionError(self::ADJUST_AUTHORISATION_ERROR_GENERIC);
        }
        return $result;
    }
}
