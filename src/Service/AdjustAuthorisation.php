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

    private string $adjustError = self::ADJUST_AUTHORISATION_ERROR_NONE;

    private array $adjustResult = [];

    private SessionSettings $session;

    private Context $context;

    private ModuleSettings $moduleSettings;

    private AdyenAPIResponseAdjustAuthorisation $APIAdjust;

    public function __construct(
        SessionSettings $session,
        Context $context,
        ModuleSettings $moduleSettings,
        AdyenAPIResponseAdjustAuthorisation $APIAdjust
    ) {
        $this->session = $session;
        $this->context = $context;
        $this->moduleSettings = $moduleSettings;
        $this->APIAdjust = $APIAdjust;
    }

    public function setAdjustAuthorisationResult(array $adjustResult): void
    {
        $this->adjustResult = $adjustResult;
    }

    public function getAdjustAuthorisationResult(): array
    {
        return $this->adjustResult;
    }

    public function setAdjustAuthorisationError(string $text): void
    {
        $this->adjustError = $text;
    }

    public function getAdjustAuthorisationError(): string
    {
        return $this->adjustError;
    }

    /**
     * @param double $amount Goods amount
     */
    public function doAdyenAdjustAuthorisation(float $amount): bool
    {
        $pspReference = $this->session->getPspReference();
        $reference = $this->session->getOrderReference();
        $adjustAuthorisation = $this->session->getAdjustAuthorisation();

        return $this->collectAdjustAuthorisation($amount, $reference, $pspReference, $adjustAuthorisation);
    }

    /**
     * @param double $amount Goods amount
     * @param string $reference Unique Order-Reference
     * @param string $pspReference pspReference
     * @param string $adjustAuthorisation The previous adjustAuthorisationData blob.
     */
    public function collectAdjustAuthorisation(
        float $amount,
        string $reference,
        string $pspReference,
        string $adjustAuthorisation
    ): bool {
        $result = false;

        $adjust = oxNew(AdyenAPIAdjustAuthorisation::class);
        $adjust->setCurrencyName($this->context->getActiveCurrencyName());
        $adjust->setReference($reference);
        $adjust->setPspReference($pspReference);
        $adjust->setAdjustAuthorisationData($adjustAuthorisation);
        $adjust->setCurrencyAmount($this->getAdyenAmount(
            $amount,
            $this->context->getActiveCurrencyDecimals()
        ));
        $adjust->setMerchantAccount($this->moduleSettings->getMerchantAccount());

        try {
            $resultAdjust = $this->APIAdjust->getAdjustAuthorisation($adjust);
            if (is_array($resultAdjust)) {
                $this->setAdjustAuthorisationResult($resultAdjust);
                $result = true;
            }
        } catch (Exception $exception) {
            Registry::getLogger()->error("Error on getAdjustAuthorisation call.", [$exception]);
            $this->setAdjustAuthorisationError(self::ADJUST_AUTHORISATION_ERROR_GENERIC);
        }
        return $result;
    }
}
