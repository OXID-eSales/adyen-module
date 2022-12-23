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
use OxidSolutionCatalysts\Adyen\Model\AdyenAPICaptures;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;

/**
 * @extendable-class
 */
class PaymentCapture extends PaymentBase
{
    use AdyenPayment;

    private array $captureResult = [];

    /** @var Context */
    private Context $context;

    /** @var ModuleSettings */
    private ModuleSettings $moduleSettings;

    /** @var AdyenAPIResponseCaptures */
    private AdyenAPIResponseCaptures $APICaptures;

    public function __construct(
        Context $context,
        ModuleSettings $moduleSettings,
        AdyenAPIResponseCaptures $APICaptures
    ) {
        $this->context = $context;
        $this->moduleSettings = $moduleSettings;
        $this->APICaptures = $APICaptures;
    }

    public function setCaptureResult(array $captureResult): void
    {
        $this->captureResult = $captureResult;
    }

    public function getCaptureResult(): array
    {
        return $this->captureResult;
    }

    /**
     * @param double $amount Goods amount
     * @param string $pspReference User ordering object
     * @param string $orderNr as unique reference
     */
    public function doAdyenCapture(float $amount, string $pspReference, string $orderNr): bool
    {
        $result = false;

        $captures = oxNew(AdyenAPICaptures::class);
        $captures->setCurrencyName($this->context->getActiveCurrencyName());
        $captures->setReference($orderNr);
        $captures->setPspReference($pspReference);
        $captures->setCurrencyAmount($this->getAdyenAmount(
            $amount,
            $this->context->getActiveCurrencyDecimals()
        ));
        $captures->setMerchantAccount($this->moduleSettings->getMerchantAccount());
        $captures->setMerchantApplicationName(Module::MODULE_NAME_EN);
        $captures->setMerchantApplicationVersion(Module::MODULE_VERSION_FULL);

        try {
            $resultCapture = $this->APICaptures->setCapture($captures);
            if (is_array($resultCapture)) {
                $this->setCaptureResult($resultCapture);
                $result = true;
            }
        } catch (Exception $exception) {
            Registry::getLogger()->error("Error on setCapture call.", [$exception]);
            $this->setPaymentExecutionError(self::PAYMENT_ERROR_GENERIC);
        }
        return $result;
    }
}
